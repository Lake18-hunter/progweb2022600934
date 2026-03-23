# SCRUM-22: Extracción y Descarga de Imágenes de Productos

## Objetivo
Extraer URLs de imágenes de todos los productos en cada categoría del sitio WoowGuau, descargarlas localmente y renombrarlas según el SKU del producto para facilitar la carga masiva en Azure.

## Descripción Técnica

### Flujo del Script
1. **Obtener Categorías**: Accede a la página principal y extrae todos los enlaces de categorías
2. **Navegar Categorías**: Para cada categoría, accede a la página y extrae productos
3. **Extraer Imágenes**: De cada producto, obtiene:
   - Nombre del producto
   - SKU (desde la URL del producto)
   - URL de la imagen
4. **Descargar Imágenes**: Descarga cada imagen y la renombra usando el SKU
5. **Crear Índice**: Genera JSON con mapeo de SKU → archivo descargado

### Estructura de Directorios
```
SCRUM-22/
├── extraccion_imagenes.py      (script principal)
├── documentacion.md             (este archivo)
├── resultados.json              (índice de imágenes descargadas)
└── imagenes/                    (carpeta con imágenes descargadas)
    ├── prod-12345.jpg
    ├── prod-12346.png
    └── ...
```

### Selectors CSS Utilizados
- **Categorías**: `a.navmenu-link` con href `/collections/`
- **Productos**: `li.productgrid--item` o `div.productitem`
- **Imagen**: `img.productitem--image-primary` o `img[data-rimg-template]`
- **Nombre**: `h2.productitem--title a`
- **URL Producto**: `a.productitem--image-link`

### Configuración de Red
- **TIMEOUT**: 15 segundos por solicitud
- **DELAY**: 1 segundo entre solicitudes
- **Reintentos**: Continúa con siguientes productos si hay error

### Convención de Nombres
Las imágenes se guardan con el siguiente formato:
```
[SKU].[extension]
Ejemplo: prod-12345.jpg
```

Si no hay SKU disponible, se utiliza el nombre del producto (espacios reemplazados con guiones bajos).

### Estructura de Salida JSON

```json
{
  "sitio": "WoowGuau",
  "url_base": "https://www.woowguau.mx/",
  "carpeta_imagenes": "imagenes",
  "total_categorias": 15,
  "total_imagenes_encontradas": 1250,
  "imagenes_descargadas": 1245,
  "imagenes_error": 5,
  "categorias": [
    {
      "nombre": "Alimentos Secos",
      "href": "/collections/alimentos-secos",
      "url": "https://www.woowguau.mx/collections/alimentos-secos",
      "productos": [
        {
          "nombre": "Alimento Premium Perros",
          "sku": "prod-12345",
          "url_imagen": "https://...",
          "archivo_descargado": "prod-12345.jpg",
          "estado": "descargado",
          "ruta_local": "imagenes/prod-12345.jpg"
        }
      ]
    }
  ]
}
```

### Campos del Índice de Imágenes

| Campo | Descripción |
|-------|-------------|
| `sitio` | Nombre del sitio web |
| `url_base` | URL base del sitio |
| `carpeta_imagenes` | Carpeta local donde se guardan imágenes |
| `total_categorias` | Cantidad de categorías procesadas |
| `total_imagenes_encontradas` | Total de imágenes encontradas en el sitio |
| `imagenes_descargadas` | Cantidad de imágenes descargadas exitosamente |
| `imagenes_error` | Cantidad de imágenes con error en descarga |

## Ejecución

### Requisitos
- Python 3.14+
- Librerías: requests, beautifulsoup4
- Conexión a internet

### Comando
```bash
python extraccion_imagenes.py
```

### Salida Esperada
```
======================================================================
EXTRACCIÓN Y DESCARGA DE IMÁGENES DE PRODUCTOS
======================================================================
[1/4] Obteniendo categorías desde https://www.woowguau.mx/...
[2/4] Encontradas 15 categorías
[3/4] Navegando categorías y descargando imágenes...
   [Alimentos Secos]... ✓ 82 imágenes descargadas
   [Alimentos Húmedos]... ✓ 65 imágenes descargadas
   ...
[4/4] Guardando índice de imágenes... ✓

======================================================================
✓ DESCARGA COMPLETADA EXITOSAMENTE
======================================================================
✓ Categorías procesadas: 15
✓ Total de imágenes encontradas: 1245
✓ Imágenes descargadas exitosamente: 1240
✓ Imágenes con error: 5
✓ Carpeta de imágenes: imagenes/
✓ Índice guardado: resultados.json
======================================================================
```

## Características

### Resiliencia
- ✓ Continúa procesando si una imagen falla
- ✓ Evita descargar imágenes duplicadas (verifica si existen)
- ✓ Manejo de excepciones en cada paso
- ✓ Validación de extensiones de archivo

### Eficiencia
- ✓ Rate limiting (1 segundo entre solicitudes)
- ✓ Descarga en streaming para grandes archivos
- ✓ Ruta directa a archivos (sin procesamiento innecesario)
- ✓ Caché local para evitar descargas duplicadas

### Trazabilidad
- ✓ Índice JSON completo de todos los productos y imágenes
- ✓ Mapeo SKU → archivo descargado
- ✓ Estado de cada descarga (éxito/error)
- ✓ Ruta local de cada imagen

## Casos de Uso

### 1. Carga a Azure Storage
El índice `resultados.json` y las imágenes en `imagenes/` pueden utilizarse directamente para carga masiva a Azure Storage, usando el SKU como identificador.

### 2. Auditoría
El archivo `resultados.json` contiene el registro completo de:
- Qué imágenes se descargaron
- Cuáles fallaron
- Dónde se almacenan localmente

### 3. Actualizaciones Incrementales
Si se ejecuta el script nuevamente:
- Solo descargará imágenes nuevas (verifica existencia local)
- Actualizará el índice con cambios
- Mantiene integridad de imágenes previas

## Notas Importantes

- ⚠️ La ejecución puede tomar 15-30 minutos dependiendo del número de productos
- ⚠️ Requiere espacio en disco para todas las imágenes (~500MB-2GB estimado)
- ⚠️ Algunos productos pueden no tener imagen disponible
- ⚠️ Las imágenes se descargan en su resolución completa

## Solución de Problemas

### "No se pudieron obtener categorías"
- Verificar conexión a internet
- Verificar que `https://www.woowguau.mx/` está disponible
- Aumentar TIMEOUT si hay lentitud

### "Error descargando imagen"
- Normal para algunos productos sin imagen
- El script continúa con los siguientes
- Ver `resultados.json` para imágenes con error

### Carpeta de imágenes vacía
- Verificar que el script completó exitosamente
- Revisar permisos de escritura en la carpeta
- Aumentar TIMEOUT si hay timeout

## Archivos Generados

1. **extraccion_imagenes.py**: Script principal de descarga
2. **documentacion.md**: Este archivo
3. **resultados.json**: Índice completo con metadata
4. **imagenes/**: Carpeta con todas las imágenes descargadas
