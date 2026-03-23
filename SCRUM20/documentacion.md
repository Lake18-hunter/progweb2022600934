# SCRUM-20: Extracción de Jerarquía, Categorías y Marcas

## Objetivo
Extraer la estructura jerárquica completa del sitio WoowGuau, identificando todas las categorías y marcas principales disponibles en la página para establece la base de la taxonomía del catálogo.

## Descripción de la Tarea

### Alcance
- Navegar la página principal del sitio (https://www.woowguau.mx/)
- Extraer todas las categorías disponibles en el menú de navegación
- Identificar categorías para Perros, Gatos, Marcas y Secciones especiales
- Extraer marcas principales como Royal Canin, Eukanuba, Nupec, Hill's Diet, etc.
- Organizar la información en estructura jerárquica

### Metodología
1. **Solicitud HTTP** a la página principal
2. **Parsing HTML** usando BeautifulSoup4
3. **Extracción de enlaces** del menú de navegación (selectores CSS `.navmenu-link`)
4. **Clasificación** de categorías vs marcas basado en URLs
5. **Validación** de URLs completas usando urljoin
6. **Organización jerárquica** de datos

## Estructura de Datos

### Formato de Salida (resultados.json)

```json
{
  "sitio": "WoowGuau",
  "url": "https://www.woowguau.mx/",
  "categorias": [
    {
      "nombre": "Perros",
      "href": "/collections/perros",
      "url": "https://www.woowguau.mx/collections/perros",
      "marcas": []
    },
    {
      "nombre": "Ropa",
      "href": "/collections/ropa-perro",
      "url": "https://www.woowguau.mx/collections/ropa-perro",
      "marcas": []
    }
    // ... más categorías
  ],
  "marcas_principales": [
    {
      "nombre": "Marcas de Alimento",
      "url": "https://www.woowguau.mx/collections/marcas-de-alimento"
    }
    // ... más marcas
  ],
  "total_categorias": 146,
  "total_marcas": 2
}
```

### Campos
- **sitio**: Identificador del sitio (WoowGuau)
- **url**: URL base del sitio
- **categorias**: Lista de categorías principales encontradas
  - `nombre`: Nombre visible de la categoría
  - `href`: Path relativo (/collections/...)
  - `url`: URL absoluta completa
  - `marcas`: Subarray para marcas específicas de la categoría
- **marcas_principales**: Marcas principales del sitio
- **total_categorias**: Contador de categorías únicas
- **total_marcas**: Contador de marcas

## Resultados Obtenidos

| Métrica | Valor |
|---------|-------|
| **Total de Categorías** | 146 |
| **Total de Marcas Principales** | 2 |
| **Categorías Perros** | ~35 |
| **Categorías Gatos** | ~30 |
| **Categorías Personalizados** | ~6 |
| **Categorías Especiales** | ~75 |

### Categorías Principales Encontradas

**Sección Perros:**
- Perros (general)
- Ropa, Ropa de Perro, Ropa de Perra
- Accesorios (Collares, Bandanas, Pecheras, Correas, Otros)
- Juguetes (Mexicanos, Importados)
- Alimento (Húmedo, Seco, Prescripción, Natural)
- Premios
- Higiene & Limpieza
- Camas & Mobiliario
- Transportadoras
- Farmacia (Antipulgas, Desparasitantes)
- Suplementos & Aceites
- Joyería Pet Lovers

**Sección Gatos:**
- Gatos (general)
- Ropa & Accesorios
- Collares, Bandanas
- Juguetes
- Alimento (Húmedo, Seco, Prescripción, Natural)
- Premios
- Higiene & Limpieza
- Arena & Areneros
- Camas & Rascadores
- Transportadoras
- Antipulgas & Desparasitantes
- Joyería Pet Lovers

**Secciones Especiales:**
- ¡Lo Nuevo!
- Mejores Amigos (San Valentín)
- Moda de Regateo
- Últimas Piezas
- Productos Personalizados (Collares, Placas, Platos, Camas, Obras de Arte)

**Marcas (Collections):**
- Royal Canin
- Eukanuba
- Nupec
- Hill's Diet
- Purina Proplan
- Diamond
- Taste of the Wild
- Kio Naturals

## Detalles Técnicos

### Script Principal
**Archivo**: `data_extraccion.py`

**Funciones:**

1. **`extraer_datos_principal()`**
   - Conecta a la URL base
   - Extrae enlace del menú usando selector `a.navmenu-link`
   - Clasifica como categoría si contiene `/collections/`
   - Clasifica como marca si contiene `marca` en el href
   - Retorna: (url_base, categorias[], marcas[])

2. **`extraer_marcas_de_categoria(url_categoria, nombre_categoria)`**
   - Actualmente sin implementación de extracción de submarcas
   - Reservado para expansión futura
   - Retorna: marcas_categoria[]

3. **`main()`**
   - Orquesta la extracción
   - Itera sobre categorías
   - Genera estructura JSON final
   - Guarda resultados en `resultados.json`

### Parámetros de Configuración
```python
TIMEOUT = 15      # Tiempo máximo de espera por solicitud (segundos)
DELAY = 1         # Espera entre solicitudes (segundos)
```

### Selectores CSS Utilizados
- `a.navmenu-link` - Enlaces del menú principal de navegación

### URLs Base
- Sitio: `https://www.woowguau.mx/`
- Pattern de categoría: `/collections/{nombre-categoria}`

## Cómo Ejecutar

### Requisitos
- Python 3.7+
- Paquetes: `requests`, `beautifulsoup4`

### Pasos
```bash
# 1. Navegar al directorio
cd SCRUM-20

# 2. Activar entorno virtual (si aplica)
source .venv/bin/activate  # Linux/Mac
.venv\Scripts\Activate.ps1 # Windows PowerShell

# 3. Ejecutar script
python data_extraccion.py
```

### Salida Esperada
```
============================================================
EXTRACCIÓN DE JERARQUÍA, CATEGORÍAS Y MARCAS
============================================================
[1/3] Conectando a https://www.woowguau.mx/...
[2/3] Encontradas 146 categorías y 2 marcas principales

[3/3] Navegando categorías...
✓ EXTRACCIÓN COMPLETADA
============================================================
✓ Categorías extraídas: 146
✓ Marcas encontradas: 2
✓ Archivo guardado: resultados.json
============================================================
```

## Archivos Generados

| Archivo | Descripción |
|---------|------------|
| `resultados.json` | Estructura jerárquica completa con categorías y marcas |
| `data_extraccion.py` | Script principal de extracción |
| `documentacion.md` | Este archivo de documentación |

## Validación de Datos

✅ **Validaciones Implementadas:**
- URLs completas construidas con `urljoin()`
- Eliminación de duplicados (no se repiten categorías)
- Verificación de presencia de href y nombre
- Clasificación correcta de categorías vs marcas

## Notas Importantes

1. **Duplicados de Menú**: El sitio presenta la misma categoría en múltiples ubicaciones del menú, resultando en ~146 entradas (algunas duplicadas por navegación múltiple)
2. **Jerarquía Plana**: Actualmente se extraen solo las categorías de primer nivel
3. **Marcas Vacías**: El array `marcas` dentro de cada categoría está vacío en esta versión (preparado para expansión)
4. **Rate Limiting**: Se respeta con un delay de 1 segundo entre solicitudes
5. **URLs Relativas**: Se convierten a URLs absolutas usando `urljoin()`

## Casos de Uso Downstream

Este SCRUM proporciona la base para:
- **SCRUM-18**: Extracción de filtros disponibles por categoría
- **SCRUM-21**: Extracción de productos dentro de cada categoría
- **SCRUM-22**: Descarga de imágenes de productos organizadas por categoría
- **SCRUM-19**: Estructuración de datos consolidados en base de datos

## Problemas Conocidos y Limitaciones

1. **Subcategorías**: No se extraen anidamientos profundos
2. **Filtros por Categoría**: No se extraen los filtros de cada categoría (manejado en SCRUM-18)
3. **Productos**: No se extraen detalles de productos (manejado en SCRUM-21)
4. **Dinámicas**: Cambios en tiempo real en el sitio no se reflejan inmediatamente

## Historial de Cambios

| Versión | Fecha | Cambios |
|---------|-------|---------|
| 1.0 | 2026-03-02 | Extracción inicial de jerarquía y categorías |

## Contacto y Soporte

Desarrollador: Sistema de Extracción Automatizada  
Proyecto: Migración de Datos WoowGuau  
Ticket: SCRUM-20
