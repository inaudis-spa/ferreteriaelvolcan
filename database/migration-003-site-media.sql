CREATE TABLE IF NOT EXISTS site_media_slots (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slot_key VARCHAR(80) NOT NULL UNIQUE,
  label VARCHAR(140) NOT NULL,
  media_type ENUM('image','video') NOT NULL,
  path VARCHAR(255) NOT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO site_media_slots(slot_key,label,media_type,path) VALUES
('intro_video','Video de introducción','video','ferreteriaelvolcan/ferreteria-volcan-intro-web.mp4'),
('hero_video','Video principal del header','video','ferreteriaelvolcan/ferreteria-volcan-un-pequeno-gran-proyecto.mp4'),
('terrace_video','Video proyecto terraza','video','ferreteriaelvolcan/proyecto-terraza.mp4'),
('offers_cover','Portada de ofertas','image','ferreteriaelvolcan/luces3.jpg')
ON DUPLICATE KEY UPDATE label=VALUES(label);
