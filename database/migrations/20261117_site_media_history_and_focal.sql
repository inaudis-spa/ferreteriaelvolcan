-- Ejecutar una sola vez sobre instalaciones que ya tienen site_media_slots.
ALTER TABLE site_media_slots
  ADD COLUMN focal_x TINYINT UNSIGNED NOT NULL DEFAULT 50,
  ADD COLUMN focal_y TINYINT UNSIGNED NOT NULL DEFAULT 50;

CREATE TABLE IF NOT EXISTS site_media_history (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slot_id BIGINT UNSIGNED NOT NULL,
  path VARCHAR(255) NOT NULL,
  focal_x TINYINT UNSIGNED NOT NULL DEFAULT 50,
  focal_y TINYINT UNSIGNED NOT NULL DEFAULT 50,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_site_media_history_slot FOREIGN KEY (slot_id) REFERENCES site_media_slots(id) ON DELETE CASCADE,
  INDEX idx_site_media_history_slot (slot_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
