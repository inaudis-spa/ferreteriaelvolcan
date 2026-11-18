-- SEO/GEO fields for product and category landing pages.
-- Run once against an existing Ferreteria Volcan database.
ALTER TABLE products
  ADD COLUMN seo_title VARCHAR(190) NULL AFTER description,
  ADD COLUMN seo_description VARCHAR(320) NULL AFTER seo_title,
  ADD COLUMN seo_indexable TINYINT(1) NOT NULL DEFAULT 1 AFTER seo_description;

ALTER TABLE categories
  ADD COLUMN seo_title VARCHAR(190) NULL AFTER description,
  ADD COLUMN seo_description VARCHAR(320) NULL AFTER seo_title,
  ADD COLUMN seo_content MEDIUMTEXT NULL AFTER seo_description,
  ADD COLUMN seo_indexable TINYINT(1) NOT NULL DEFAULT 1 AFTER seo_content;
