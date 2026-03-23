# SCRUM-18: Extracción de Filtros y Atributos

## Objetivo
Extraer y mapear todos los atributos de filtrado disponibles en las categorías del sitio WoowGuau, tales como:
- Edad (Adulto/Cachorro/Senior)
- Tipo (Alimento Húmedo/Seco)
- Raza
- Marca
- Otros atributos específicos por categoría

## Descripción Técnica

### Funcionalidad del Script

El script `extraccion_filtros.py` realiza las siguientes operaciones:

1. **Obtención de Categorías**: Navega por el árbol de categorías del sitio (reutilizando la estructura identificada en SCRUM-20)

2. **Extracción de Filtros**: Para cada categoría, el script:
   - Accede a la página de la categoría
   - Identifica la barra de filtros disponibles
   - Extrae todos los filtros y sus opciones
   - **Datos extraídos:**
     - **Nombre del filtro**: Etiqueta del filtro (ej: "Edad", "Tipo")
     - **Opciones**: Lista de valores disponibles para cada filtro

3. **Almacenamiento**: Los datos se guardan en formato JSON estructurado por categoría

### Selectores CSS Utilizados

```
- Contenedores de filtros: 
  - div.boost-pfs-filter-group
  - div.filter-group
  - div.sidebar-filter
  - div.filter-container
  - div[data-filter-group]
  
- Nombre del filtro: h3, label, .filter-title, .filter-name, legend

- Opciones de filtro: label, option, a[data-filter]
```

### Estructura de Salida (resultados.json)

```json
{
  "sitio": "WoowGuau",
  "url_base": "https://www.woowguau.mx/",
  "total_categorias": X,
  "total_filtros_por_categorias": Y,
  "categorias": [
    {
      "nombre": "Ropa Perro",
      "url": "https://...",
      "filtros": [
        {
          "nombre": "Edad",
          "opciones": ["Adulto", "Cachorro", "Senior"]
        },
        {
          "nombre": "Tipo",
          "opciones": ["Ropa", "Accesorios"]
        },
        {
          "nombre": "Marca",
          "opciones": ["Marca A", "Marca B", "Marca C"]
        }
      ]
    }
  ]
}
```

## Ejecución

### Requisitos
- Python 3.8+
- Bibliotecas: `requests`, `beautifulsoup4`

### Comando de Ejecución
```bash
cd SCRUM-18
python extraccion_filtros.py
```

### Tiempo Estimado
- **10-20 minutos** (depende del número de categorías)

### Output Esperado
```
======================================================================
EXTRACCIÓN DE FILTROS Y ATRIBUTOS DE CATEGORÍAS
======================================================================
[1/4] Obteniendo categorías desde https://www.woowguau.mx/...
[2/4] Encontradas X categorías
[3/4] Navegando categorías y extrayendo filtros...
[4/4] Guardando resultados...

======================================================================
✓ EXTRACCIÓN COMPLETADA EXITOSAMENTE
======================================================================
✓ Categorías procesadas: X
✓ Total de filtros extraídos: Y
✓ Archivo guardado: resultados.json
======================================================================
```

## Notas Técnicas

- El script respeta un delay de 1 segundo entre solicitudes para no sobrecargar el servidor
- Incluye manejo robusto de errores para continuar incluso si algunos filtros fallan
- Utiliza múltiples selectores CSS para capturar diferentes estructuras de filtros
- Limpia automáticamente el texto de opciones (elimina contadores de cantidad si existen)
- Elimina duplicados de opciones dentro de cada filtro

## Casos de Uso

Este script es fundamental para:
- **Enriquecimiento de búsqueda**: Proporciona la estructura de filtros para mejorar la experiencia de búsqueda en la nueva infraestructura
- **Mapeo de atributos**: Identifica todos los atributos disponibles para catalogación
- **Validación de datos**: Asegura que los productos están correctamente clasificados según los filtros disponibles
- **Analytics**: Analizar qué filtros son más comunes en cada categoría

## Próximas Mejoras
- Extraer valores de filtros numéricos (rango de precios, tamaños)
- Contar frecuencia de cada opción de filtro
- Exportar a formatos adicionales (CSV, Excel)
- Crear índice de filtros globales del sitio
