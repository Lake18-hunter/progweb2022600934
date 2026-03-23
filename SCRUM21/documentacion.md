# SCRUM-21: Extracción de Detalles de Productos

## Objetivo
Extraer detalles específicos de cada producto del sitio WoowGuau, incluyendo:
- Nombre completo del producto
- Precio vigente
- Descuentos aplicables
- ID único (SKU)

## Descripción Técnica

### Funcionalidad del Script

El script `extraccion_productos.py` realiza las siguientes operaciones:

1. **Obtención de Categorías**: Navega por el árbol de categorías del sitio (utilizando la estructura identificada en SCRUM-20)

2. **Extracción de Productos**: Para cada categoría, el script:
   - Accede a la página de la categoría
   - Identifica todos los productos listados
   - Extrae los siguientes datos de cada producto:
     - **Nombre**: Título completo del producto
     - **Precio**: Precio vigente en formato moneda
     - **Descuento**: Indicador de si existe descuento aplicado
     - **SKU**: Identificador único extraído de la URL o HTML

3. **Almacenamiento**: Los datos se guardan en formato JSON estructurado por categoría

### Selectores CSS Utilizados

```
- Contenedores de producto: li.productgrid--item, div.productitem
- Nombre: h2.productitem--title a
- Precio: span.money
- Enlace: a.productitem--image-link
- Descuento: div.price--compare-at
```

### Estructura de Salida (resultados.json)

```json
{
  "sitio": "WoowGuau",
  "url_base": "https://www.woowguau.mx/",
  "total_categorias": X,
  "total_productos": Y,
  "categorias": [
    {
      "nombre": "Nombre Categoría",
      "url": "https://...",
      "productos": [
        {
          "nombre": "Nombre del Producto",
          "precio": "$XXX.XX",
          "descuento": "Sí/No",
          "sku": "identificador",
          "url": "https://..."
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
cd SCRUM-21
python extraccion_productos.py
```

### Tiempo Estimado
- **10-20 minutos** (depende del número de categorías y productos)

### Output Esperado
```
======================================================================
EXTRACCIÓN DE DETALLES DE PRODUCTOS
======================================================================
[1/4] Obteniendo categorías desde https://www.woowguau.mx/...
[2/4] Encontradas X categorías
[3/4] Navegando categorías y extrayendo productos...
[4/4] Guardando resultados...

======================================================================
✓ EXTRACCIÓN COMPLETADA EXITOSAMENTE
======================================================================
✓ Categorías procesadas: X
✓ Total de productos extraídos: Y
✓ Archivo guardado: resultados.json
======================================================================
```

## Notas Técnicas

- El script respeta un delay de 1 segundo entre solicitudes para no sobrecargar el servidor
- Incluye manejo de errores robusto para continuar incluso si algunos productos fallan
- Los selectores CSS se basan en la estructura actual del sitio WoowGuau (puede requerir ajustes si el sitio cambia)
- El SKU se extrae de la URL del producto (última porción de la ruta)

## Próximas Mejoras
- Extraer atributos adicionales (stock, descripción breve)
- Implementar extracción de imágenes de productos
- Exportar a formatos adicionales (CSV, Excel)
