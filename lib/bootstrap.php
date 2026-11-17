<?php
declare(strict_types=1);

const PROJECT_ROOT = __DIR__ . '/..';
const UPLOAD_ROOT = PROJECT_ROOT . '/uploads';

function config(): array
{
    static $config;
    if ($config !== null) return $config;
    $local = PROJECT_ROOT . '/config.local.php';
    $file = is_file($local) ? require $local : [];
    $config = array_merge([
        'db_host' => getenv('VOLCAN_DB_HOST') ?: '127.0.0.1',
        'db_port' => getenv('VOLCAN_DB_PORT') ?: '3306',
        'db_name' => getenv('VOLCAN_DB_NAME') ?: 'ferreteria_volcan',
        'db_user' => getenv('VOLCAN_DB_USER') ?: 'root',
        'db_pass' => getenv('VOLCAN_DB_PASS') ?: '',
        'app_url' => getenv('VOLCAN_APP_URL') ?: '',
        'whatsapp' => getenv('VOLCAN_WHATSAPP') ?: '56986771343',
        'max_image_mb' => 8,
        'max_video_mb' => 40,
    ], $file);
    return $config;
}

function db(bool $withoutDatabase = false): PDO
{
    static $pdo;
    if (!$withoutDatabase && $pdo instanceof PDO) return $pdo;
    $c = config();
    $dbPart = $withoutDatabase ? '' : ';dbname=' . $c['db_name'];
    $connection = new PDO(
        "mysql:host={$c['db_host']};port={$c['db_port']}{$dbPart};charset=utf8mb4",
        $c['db_user'],
        $c['db_pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]
    );
    if (!$withoutDatabase) $pdo = $connection;
    return $connection;
}

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) return;
    session_name('volcan_admin');
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS']), 'path' => '/']);
    session_start();
}

function csrf_token(): string
{
    start_secure_session();
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}

function verify_csrf(): void
{
    start_secure_session();
    if (!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['csrf'] ?? ''))) {
        http_response_code(419);
        exit('La sesión del formulario expiró. Regresa e inténtalo nuevamente.');
    }
}

function admin_user(): ?array
{
    start_secure_session();
    if (empty($_SESSION['admin_id'])) return null;
    $stmt = db()->prepare('SELECT id, name, email FROM admin_users WHERE id = ? AND active = 1');
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch() ?: null;
}

function require_admin(): array
{
    $user = admin_user();
    if (!$user) {
        header('Location: login.php');
        exit;
    }
    return $user;
}

function slugify(string $value): string
{
    $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', trim($value)) ?: $value;
    $value = strtolower((string)preg_replace('/[^a-zA-Z0-9]+/', '-', $value));
    return trim($value, '-') ?: 'item-' . bin2hex(random_bytes(3));
}

function upload_file(array $file, string $kind): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) throw new RuntimeException('No se recibió un archivo válido.');
    $isImage = $kind === 'image';
    $max = (int)config()[$isImage ? 'max_image_mb' : 'max_video_mb'] * 1024 * 1024;
    if ((int)$file['size'] > $max) throw new RuntimeException('El archivo supera el tamaño máximo permitido.');
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $allowed = $isImage
        ? ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp']
        : ['video/mp4' => 'mp4', 'video/webm' => 'webm'];
    if (!isset($allowed[$mime])) throw new RuntimeException('Formato de archivo no permitido.');
    $folder = $isImage ? 'images' : 'videos';
    $directory = UPLOAD_ROOT . '/' . $folder;
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) throw new RuntimeException('No se pudo crear el directorio de archivos.');
    if ($isImage) {
        $base = bin2hex(random_bytes(16));
        $source = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($file['tmp_name']),
            'image/png' => imagecreatefrompng($file['tmp_name']),
            'image/webp' => imagecreatefromwebp($file['tmp_name']),
            default => false,
        };
        if (!$source) throw new RuntimeException('No se pudo procesar la fotografía.');
        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($file['tmp_name']);
            $orientation = (int)($exif['Orientation'] ?? 1);
            if ($orientation === 3) $source = imagerotate($source, 180, 0);
            if ($orientation === 6) $source = imagerotate($source, -90, 0);
            if ($orientation === 8) $source = imagerotate($source, 90, 0);
        }
        $variants = ['' => 1600, '-md' => 960, '-sm' => 480];
        foreach ($variants as $suffix => $maxWidth) {
            $width = imagesx($source); $height = imagesy($source);
            $ratio = min(1, $maxWidth / max(1, $width));
            $targetWidth = max(1, (int)round($width * $ratio));
            $targetHeight = max(1, (int)round($height * $ratio));
            $target = imagecreatetruecolor($targetWidth, $targetHeight);
            imagealphablending($target, false); imagesavealpha($target, true);
            $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
            imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $transparent);
            imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
            if (!imagewebp($target, $directory . '/' . $base . $suffix . '.webp', 82)) {
                imagedestroy($target); imagedestroy($source);
                throw new RuntimeException('No se pudo convertir la fotografía a WebP.');
            }
            imagedestroy($target);
        }
        imagedestroy($source);
        return ['path' => 'uploads/images/' . $base . '.webp', 'mime' => 'image/webp', 'type' => 'image'];
    }
    $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $filename)) throw new RuntimeException('No se pudo guardar el archivo.');
    return ['path' => 'uploads/' . $folder . '/' . $filename, 'mime' => $mime, 'type' => 'video'];
}

function flash(string $type, string $message): void
{
    start_secure_session();
    $_SESSION['flash'][] = compact('type', 'message');
}

function take_flashes(): array
{
    start_secure_session();
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function redirect_admin(string $section = 'products'): never
{
    header('Location: index.php?section=' . rawurlencode($section));
    exit;
}
