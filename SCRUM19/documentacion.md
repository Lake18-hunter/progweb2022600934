# SCRUM-19: Estructuración de Datos para Base de Datos

## Objetivo
Consolidar todos los datos extraídos en las tareas anteriores (SCRUM-20, SCRUM-21, SCRUM-18, SCRUM-22) y estructurarlos en formato normalizado listo para inserción en base de datos, usando SKU como llave primaria y estableciendo relaciones entre tablas.

## Descripción Técnica

### Flujo del Script
1. **Cargar Datos**: Lee los resultados de todas las tareas previas
   - SCRUM-20: Jerarquía y categorías
   - SCRUM-21: Productos con precios
   - SCRUM-18: Filtros por categoría
   - SCRUM-22: Imágenes de productos

2. **Consolidar Productos**: Elimina duplicados usando SKU como identificador único
   - Calcula precio_lista y precio_oferta
   - Asocia imágenes descargadas
   - Identifica productos con descuento

3. **Consolidar Categorías**: Genera lista única con IDs secuenciales

4. **Crear Relaciones**: Tabla muchos-a-muchos entre productos y categorías

5. **Consolidar Filtros**: Asocia filtros disponibles con categorías

### Estructura de Directorios
```
SCRUM-19/
├── estructuracion_datos.py      (script principal)
├── documentacion.md              (este archivo)
├── resultados.json               (resumen general)
├── productos.json                (tabla de productos)
├── categorias.json               (tabla de categorías)
├── productos_categorias.json    (tabla de relación)
└── filtros_categorias.json      (filtros por categoría)
```

### Dependencias de Tareas
```
SCRUM-20 (jerarquía) ──┐
SCRUM-21 (productos)  ──┼──► SCRUM-19 (consolidación)
SCRUM-18 (filtros)    ──┤
SCRUM-22 (imágenes)   ──┘
```

## Estructura de Salida

### 1. productos.json
Tabla principal con productos únicos (SKU como PK).

```json
[
  {
    "sku": "poncho-artesanal-mexicano-2430-azul",
    "nombre": "PONCHO PARA PERRO",
    "precio_lista": 300.00,
    "precio_oferta": 240.00,
    "tiene_descuento": true,
    "url_producto": "https://www.woowguau.mx/...",
    "url_imagen": "https://cdn.shopify.com/...",
    "archivo_imagen": "poncho-artesanal-mexicano-2430-azul.jpg",
    "estado_imagen": "descargado"
  }
]
```

**Campos**:
- `sku` (string, PK): Identificador único del producto
- `nombre` (string): Nombre del producto
- `precio_lista` (decimal): Precio normal sin descuento
- `precio_oferta` (decimal|null): Precio con descuento (si aplica)
- `tiene_descuento` (boolean): Indica si hay descuento activo
- `url_producto` (string): URL del producto en el sitio
- `url_imagen` (string): URL de la imagen en CDN
- `archivo_imagen` (string): Nombre del archivo descargado localmente
- `estado_imagen` (string): Estado de descarga ('descargado', 'error', 'sin_imagen')

### 2. categorias.json
Catálogo de categorías con IDs secuenciales.

```json
[
  {
    "categoria_id": 1,
    "nombre": "Accesorios",
    "url": "https://www.woowguau.mx/collections/accesorios"
  },
  {
    "categoria_id": 2,
    "nombre": "Alimento",
    "url": "https://www.woowguau.mx/collections/alimento"
  }
]
```

**Campos**:
- `categoria_id` (int, PK): Identificador único de categoría
- `nombre` (string): Nombre de la categoría
- `url` (string): URL de la categoría en el sitio

### 3. productos_categorias.json
Tabla de relación muchos-a-muchos (un producto puede estar en varias categorías).

```json
[
  {
    "sku": "poncho-artesanal-mexicano-2430-azul",
    "categoria_id": 15
  },
  {
    "sku": "poncho-artesanal-mexicano-2430-azul",
    "categoria_id": 38
  }
]
```

**Campos**:
- `sku` (string, FK): Referencia a productos.sku
- `categoria_id` (int, FK): Referencia a categorias.categoria_id

### 4. filtros_categorias.json
Filtros disponibles por categoría (marca, edad, tipo, etc.).

```json
[
  {
    "categoria_id": 2,
    "filtro_nombre": "Edad",
    "filtro_opciones": ["Adulto", "Cachorro", "Senior"]
  },
  {
    "categoria_id": 2,
    "filtro_nombre": "Tipo",
    "filtro_opciones": ["Seco", "Húmedo"]
  }
]
```

**Campos**:
- `categoria_id` (int, FK): Referencia a categorias.categoria_id
- `filtro_nombre` (string): Nombre del filtro
- `filtro_opciones` (array): Opciones disponibles para ese filtro

### 5. resultados.json
Resumen estadístico de la consolidación.

```json
{
  "sitio": "WoowGuau",
  "url_base": "https://www.woowguau.mx/",
  "estadisticas": {
    "total_productos": 1250,
    "total_categorias": 73,
    "total_relaciones": 2640,
    "total_filtros": 245,
    "productos_con_descuento": 580,
    "productos_con_imagen": 1180
  },
  "archivos_generados": [
    "productos.json",
    "categorias.json",
    "productos_categorias.json",
    "filtros_categorias.json"
  ]
}
```

## Lógica de Negocio

### Cálculo de Precios
- Si `tiene_descuento = true`:
  - `precio_oferta` = precio mostrado en el sitio
  - `precio_lista` = precio_oferta × 1.25 (aproximación +25%)
- Si `tiene_descuento = false`:
  - `precio_lista` = precio mostrado
  - `precio_oferta` = null

### Eliminación de Duplicados
- Se usa **SKU** como identificador único
- Si un producto aparece en múltiples categorías:
  - Se guarda **una sola vez** en productos.json
  - Se crean **múltiples relaciones** en productos_categorias.json

### Asociación de Imágenes
- Se busca la imagen por SKU en los resultados de SCRUM-22
- Si existe: se incluyen `url_imagen`, `archivo_imagen`, `estado_imagen`
- Si no existe: `estado_imagen = 'sin_imagen'`

## Ejecución

### Requisitos
- Python 3.14+
- Librerías estándar (json, os, pathlib, re)
- Archivos generados de SCRUM-20, SCRUM-21, SCRUM-18, SCRUM-22

### Comando
```bash
python estructuracion_datos.py
```

### Salida Esperada
```
======================================================================
ESTRUCTURACIÓN DE DATOS PARA BASE DE DATOS
======================================================================

Cargando datos de tareas anteriores...
   • SCRUM-20: ..\SCRUM-20\resultados.json
   • SCRUM-21: ..\SCRUM-21\resultados.json
   • SCRUM-18: ..\SCRUM-18\resultados.json
   • SCRUM-22: ..\SCRUM-22\resultados.json

[1/5] Consolidando productos...
   ✓ Productos únicos encontrados: 1250
   ✓ Relaciones producto-categoría: 2640
[2/5] Consolidando categorías...
   ✓ Categorías únicas: 73
[3/5] Asignando IDs a relaciones...
   ✓ Relaciones procesadas: 2640
[4/5] Consolidando filtros...
   ✓ Filtros consolidados: 245
[5/5] Guardando resultados...
   ✓ productos.json → 1250 registros
   ✓ categorias.json → 73 registros
   ✓ productos_categorias.json → 2640 registros
   ✓ filtros_categorias.json → 245 registros
   ✓ resultados.json (resumen)

======================================================================
✓ ESTRUCTURACIÓN COMPLETADA EXITOSAMENTE
======================================================================
✓ Productos únicos: 1250
✓ Categorías: 73
✓ Relaciones producto-categoría: 2640
✓ Filtros por categoría: 245

📁 Archivos generados:
   • productos.json
   • categorias.json
   • productos_categorias.json
   • filtros_categorias.json
   • resultados.json (resumen)
======================================================================
```

## Casos de Uso

### 1. Importación a Base de Datos Relacional
Los archivos JSON pueden importarse directamente a tablas SQL:
```sql
CREATE TABLE productos (
    sku VARCHAR(255) PRIMARY KEY,
    nombre VARCHAR(500),
    precio_lista DECIMAL(10,2),
    precio_oferta DECIMAL(10,2),
    tiene_descuento BOOLEAN,
    url_producto TEXT,
    url_imagen TEXT,
    archivo_imagen VARCHAR(255),
    estado_imagen VARCHAR(50)
);

CREATE TABLE categorias (
    categoria_id INT PRIMARY KEY,
    nombre VARCHAR(255),
    url TEXT
);

CREATE TABLE productos_categorias (
    sku VARCHAR(255),
    categoria_id INT,
    FOREIGN KEY (sku) REFERENCES productos(sku),
    FOREIGN KEY (categoria_id) REFERENCES categorias(categoria_id),
    PRIMARY KEY (sku, categoria_id)
);
```

### 2. Análisis de Datos
- Productos con mayor descuento
- Categorías más pobladas
- Productos sin imagen para seguimiento

### 3. Sincronización con Azure
Base para script de carga a Azure SQL Database:
- productos.json → tabla Products
- categorias.json → tabla Categories
- productos_categorias.json → tabla ProductCategories

## Características

### Normalización
- ✓ Elimina redundancia de datos
- ✓ SKU como llave primaria única
- ✓ Relaciones muchos-a-muchos correctamente modeladas
- ✓ Integridad referencial mantenida

### Completitud
- ✓ Integra datos de 4 tareas previas
- ✓ Asocia productos con imágenes descargadas
- ✓ Mantiene relaciones categoría-filtro
- ✓ Preserva información de precios y descuentos

### Trazabilidad
- ✓ Resumen estadístico completo
- ✓ Archivos separados por entidad
- ✓ Fácil auditoría de relaciones
- ✓ Identificación clara de productos sin imagen

## Validaciones

### Integridad de Datos
- SKUs duplicados se consolidan en un único registro
- Relaciones producto-categoría mantienen todas las asociaciones
- Precios se validan y convierten a decimal
- Categorías sin productos no se descartan

### Manejo de Casos Especiales
- Productos sin SKU: se excluyen (no pueden ser PK)
- Productos sin imagen: se marcan con `estado_imagen = 'sin_imagen'`
- Precios inválidos: se establecen a `null`
- Categorías duplicadas: se unifican por nombre

## Notas Importantes

- ⚠️ El script requiere que se hayan ejecutado previamente SCRUM-20, 21, 18 y 22
- ⚠️ Los archivos JSON generados pueden ser grandes (varios MB)
- ⚠️ La aproximación de precio_lista (+25%) es estimativa, validar con datos reales
- ⚠️ SKU es la única llave primaria confiable identificada en el sitio

## Solución de Problemas

### "Error cargando archivo"
- Verificar que existan los directorios SCRUM-20, 21, 18, 22
- Verificar que cada tarea tenga su resultados.json
- Ejecutar tareas previas si faltan archivos

### "Productos únicos: 0"
- Revisar que SCRUM-21 tenga productos con SKU válidos
- Verificar formato del archivo resultados.json de SCRUM-21

### Archivos JSON muy grandes
- Normal para catálogos extensos (>1000 productos)
- Considerar compresión gzip para almacenamiento
- Para bases de datos, importar directamente sin cargar en memoria

## Archivos Generados

1. **estructuracion_datos.py**: Script principal de consolidación
2. **documentacion.md**: Este archivo
3. **productos.json**: Tabla de productos (SKU como PK)
4. **categorias.json**: Tabla de categorías
5. **productos_categorias.json**: Tabla de relación
6. **filtros_categorias.json**: Filtros por categoría
7. **resultados.json**: Resumen estadístico
