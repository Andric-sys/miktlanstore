const fs = require('fs');
const path = require('path');

// Carpetas de producto a procesar
const productFolders = ['Tazas','Cuadros','Nuevos','Otros','Paquetes','Personalizados','Playeras','Promos'];

function isImageFile(name) {
  const ext = path.extname(name).toLowerCase();
  return ['.jpg','.jpeg','.png'].includes(ext);
}

function scanImages(folderPath, baseRelative = 'imagenes') {
  const abs = path.join(folderPath, baseRelative);
  const results = [];
  if (!fs.existsSync(abs)) return results;

  function walk(dir) {
    const items = fs.readdirSync(dir, { withFileTypes: true });
    for (const it of items) {
      const p = path.join(dir, it.name);
      if (it.isDirectory()) {
        walk(p);
      } else if (it.isFile() && isImageFile(it.name)) {
        // ruta relativa dentro de la carpeta del producto, usando slash
        const rel = path.relative(folderPath, p).split(path.sep).join('/');
        results.push(rel);
      }
    }
  }

  walk(abs);
  results.sort((a,b) => a.localeCompare(b, undefined, { numeric: true, sensitivity: 'base' }));
  return results;
}

for (const folder of productFolders) {
  const folderPath = path.join(__dirname, '..', folder);
  if (!fs.existsSync(folderPath)) {
    console.log('No existe:', folderPath);
    continue;
  }

  const images = scanImages(folderPath);
  const out = { imagenes: images };
  const outPath = path.join(folderPath, 'imagenes.json');
  fs.writeFileSync(outPath, JSON.stringify(out, null, 2), 'utf8');
  console.log('Escrito', outPath, images.length, 'entradas');
}

console.log('Generación completada.');
