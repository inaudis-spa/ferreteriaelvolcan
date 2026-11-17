-- Incorpora las fotografías iniciales de Ofertas al mantenedor central.
INSERT INTO gallery_media(title,description,media_type,path,sort_order,active)
SELECT seed.title,seed.description,'image',seed.path,seed.sort_order,1
FROM (
  SELECT 'Materiales para construcción' title,'Construcción' description,'ferreteriaelvolcan/cemento.jpg' path,100 sort_order
  UNION ALL SELECT 'Grifería para tu proyecto','Gasfitería','ferreteriaelvolcan/llaves-griferia.jpg',110
  UNION ALL SELECT 'Tableros para construir','Maderas','ferreteriaelvolcan/osb.jpg',120
  UNION ALL SELECT 'Iluminación eficiente','Electricidad','ferreteriaelvolcan/luces.jpg',130
  UNION ALL SELECT 'Soluciones en PVC','Gasfitería','ferreteriaelvolcan/tubos-pvc.jpg',140
  UNION ALL SELECT 'Renueva tus espacios','Hogar','ferreteriaelvolcan/llaves-griferia-3.jpg',150
) seed
WHERE NOT EXISTS (SELECT 1 FROM gallery_media existing WHERE existing.path=seed.path);
