import json
import os
from pathlib import Path
import re

def limpiar_precio(precio_str):
    """
    Convierte string de precio a decimal.
    Ejemplos: "$ 240.00" -> 240.00, "$1,500.00" -> 1500.00
    """
    if not precio_str or precio_str == "N/A":
        return None
    try:
        # Eliminar $, espacios, comas
        precio_limpio = re.sub(r'[$\s,]', '', precio_str)
        return float(precio_limpio)
    except:
        return None

def cargar_json(ruta):
    """
    Carga un archivo JSON y retorna su contenido.
    """
    try:
        with open(ruta, 'r', encoding='utf-8') as f:
            return json.load(f)
    except Exception as e:
        print(f"Error cargando {ruta}: {e}")
        return None

def consolidar_productos(datos_productos, datos_imagenes):
    """
    Consolida información de productos con sus imágenes.
    Elimina duplicados usando SKU como identificador único.
    """
    print("[1/5] Consolidando productos...")
    
    # Crear índice de imágenes por SKU
    indice_imagenes = {}
    for categoria in datos_imagenes.get('categorias', []):
        for producto in categoria.get('productos', []):
            sku = producto.get('sku')
            if sku:
                indice_imagenes[sku] = {
                    'url_imagen': producto.get('url_imagen'),
                    'archivo_imagen': producto.get('archivo_descargado'),
                    'estado_imagen': producto.get('estado')
                }
    
    # Consolidar productos únicos
    productos_unicos = {}
    relacion_productos_categorias = []
    
    for categoria in datos_productos.get('categorias', []):
        categoria_nombre = categoria.get('nombre')
        
        for producto in categoria.get('productos', []):
            sku = producto.get('sku')
            
            if not sku:
                continue
            
            # Si el producto no existe, agregarlo
            if sku not in productos_unicos:
                precio = limpiar_precio(producto.get('precio'))
                tiene_descuento = producto.get('descuento', 'No') == 'Sí'
                
                # Si hay descuento, el precio mostrado es el precio_oferta
                # Estimamos precio_lista sumando ~20% (aproximación)
                if tiene_descuento and precio:
                    precio_oferta = precio
                    precio_lista = round(precio * 1.25, 2)  # Aproximación +25%
                else:
                    precio_lista = precio
                    precio_oferta = None
                
                # Buscar imagen
                info_imagen = indice_imagenes.get(sku, {})
                
                productos_unicos[sku] = {
                    'sku': sku,
                    'nombre': producto.get('nombre'),
                    'precio_lista': precio_lista,
                    'precio_oferta': precio_oferta,
                    'tiene_descuento': tiene_descuento,
                    'url_producto': producto.get('url'),
                    'url_imagen': info_imagen.get('url_imagen'),
                    'archivo_imagen': info_imagen.get('archivo_imagen'),
                    'estado_imagen': info_imagen.get('estado_imagen', 'sin_imagen')
                }
            
            # Registrar relación producto-categoría
            relacion_productos_categorias.append({
                'sku': sku,
                'categoria': categoria_nombre
            })
    
    print(f"   ✓ Productos únicos encontrados: {len(productos_unicos)}")
    print(f"   ✓ Relaciones producto-categoría: {len(relacion_productos_categorias)}")
    
    return list(productos_unicos.values()), relacion_productos_categorias

def consolidar_categorias(datos_jerarquia, datos_productos):
    """
    Extrae lista única de categorías con IDs secuenciales.
    """
    print("[2/5] Consolidando categorías...")
    
    categorias_set = set()
    
    # Desde jerarquía
    for categoria in datos_jerarquia.get('categorias', []):
        categorias_set.add(categoria.get('nombre'))
    
    # Desde productos
    for categoria in datos_productos.get('categorias', []):
        categorias_set.add(categoria.get('nombre'))
    
    # Crear lista ordenada con IDs
    categorias = []
    for idx, nombre in enumerate(sorted(categorias_set), 1):
        # Buscar URL en datos de productos
        url = None
        for cat in datos_productos.get('categorias', []):
            if cat.get('nombre') == nombre:
                url = cat.get('url')
                break
        
        categorias.append({
            'categoria_id': idx,
            'nombre': nombre,
            'url': url
        })
    
    print(f"   ✓ Categorías únicas: {len(categorias)}")
    
    return categorias

def asignar_ids_categorias(relacion_productos_categorias, categorias):
    """
    Reemplaza nombres de categorías por IDs en las relaciones.
    """
    print("[3/5] Asignando IDs a relaciones...")
    
    # Crear mapa nombre -> id
    mapa_categorias = {cat['nombre']: cat['categoria_id'] for cat in categorias}
    
    relaciones_con_id = []
    for rel in relacion_productos_categorias:
        categoria_id = mapa_categorias.get(rel['categoria'])
        if categoria_id:
            relaciones_con_id.append({
                'sku': rel['sku'],
                'categoria_id': categoria_id
            })
    
    print(f"   ✓ Relaciones procesadas: {len(relaciones_con_id)}")
    
    return relaciones_con_id

def consolidar_filtros(datos_filtros, categorias):
    """
    Extrae filtros disponibles por categoría.
    """
    print("[4/5] Consolidando filtros...")
    
    # Crear mapa nombre -> id
    mapa_categorias = {cat['nombre']: cat['categoria_id'] for cat in categorias}
    
    filtros_por_categoria = []
    
    for categoria in datos_filtros.get('categorias', []):
        categoria_id = mapa_categorias.get(categoria.get('nombre'))
        
        if not categoria_id:
            continue
        
        for filtro in categoria.get('filtros', []):
            filtros_por_categoria.append({
                'categoria_id': categoria_id,
                'filtro_nombre': filtro.get('nombre'),
                'filtro_opciones': filtro.get('opciones', [])
            })
    
    print(f"   ✓ Filtros consolidados: {len(filtros_por_categoria)}")
    
    return filtros_por_categoria

def guardar_resultados(productos, categorias, relaciones, filtros):
    """
    Guarda los resultados en archivos JSON separados.
    """
    print("[5/5] Guardando resultados...")
    
    # Guardar productos
    with open('productos.json', 'w', encoding='utf-8') as f:
        json.dump(productos, f, ensure_ascii=False, indent=2)
    print(f"   ✓ productos.json → {len(productos)} registros")
    
    # Guardar categorías
    with open('categorias.json', 'w', encoding='utf-8') as f:
        json.dump(categorias, f, ensure_ascii=False, indent=2)
    print(f"   ✓ categorias.json → {len(categorias)} registros")
    
    # Guardar relaciones
    with open('productos_categorias.json', 'w', encoding='utf-8') as f:
        json.dump(relaciones, f, ensure_ascii=False, indent=2)
    print(f"   ✓ productos_categorias.json → {len(relaciones)} registros")
    
    # Guardar filtros
    with open('filtros_categorias.json', 'w', encoding='utf-8') as f:
        json.dump(filtros, f, ensure_ascii=False, indent=2)
    print(f"   ✓ filtros_categorias.json → {len(filtros)} registros")
    
    # Crear resumen consolidado
    resumen = {
        'sitio': 'WoowGuau',
        'url_base': 'https://www.woowguau.mx/',
        'estadisticas': {
            'total_productos': len(productos),
            'total_categorias': len(categorias),
            'total_relaciones': len(relaciones),
            'total_filtros': len(filtros),
            'productos_con_descuento': len([p for p in productos if p['tiene_descuento']]),
            'productos_con_imagen': len([p for p in productos if p['estado_imagen'] == 'descargado'])
        },
        'archivos_generados': [
            'productos.json',
            'categorias.json',
            'productos_categorias.json',
            'filtros_categorias.json'
        ]
    }
    
    with open('resultados.json', 'w', encoding='utf-8') as f:
        json.dump(resumen, f, ensure_ascii=False, indent=2)
    print(f"   ✓ resultados.json (resumen)")

def main():
    print("=" * 70)
    print("ESTRUCTURACIÓN DE DATOS PARA BASE DE DATOS")
    print("=" * 70)
    
    # Rutas a los archivos de tareas anteriores
    base_path = Path(__file__).parent.parent
    
    ruta_jerarquia = base_path / 'SCRUM-20' / 'resultados.json'
    ruta_productos = base_path / 'SCRUM-21' / 'resultados.json'
    ruta_filtros = base_path / 'SCRUM-18' / 'resultados.json'
    ruta_imagenes = base_path / 'SCRUM-22' / 'resultados.json'
    
    print("\nCargando datos de tareas anteriores...")
    print(f"   • SCRUM-20: {ruta_jerarquia}")
    print(f"   • SCRUM-21: {ruta_productos}")
    print(f"   • SCRUM-18: {ruta_filtros}")
    print(f"   • SCRUM-22: {ruta_imagenes}")
    print()
    
    # Cargar datos
    datos_jerarquia = cargar_json(ruta_jerarquia)
    datos_productos = cargar_json(ruta_productos)
    datos_filtros = cargar_json(ruta_filtros)
    datos_imagenes = cargar_json(ruta_imagenes)
    
    if not all([datos_jerarquia, datos_productos, datos_filtros, datos_imagenes]):
        print("Error: No se pudieron cargar todos los archivos necesarios")
        return
    
    # Procesar datos
    productos, relacion_productos_categorias = consolidar_productos(datos_productos, datos_imagenes)
    
    categorias = consolidar_categorias(datos_jerarquia, datos_productos)
    
    relaciones = asignar_ids_categorias(relacion_productos_categorias, categorias)
    
    filtros = consolidar_filtros(datos_filtros, categorias)
    
    guardar_resultados(productos, categorias, relaciones, filtros)
    
    # Resumen final
    print("\n" + "=" * 70)
    print("✓ ESTRUCTURACIÓN COMPLETADA EXITOSAMENTE")
    print("=" * 70)
    print(f"✓ Productos únicos: {len(productos)}")
    print(f"✓ Categorías: {len(categorias)}")
    print(f"✓ Relaciones producto-categoría: {len(relaciones)}")
    print(f"✓ Filtros por categoría: {len(filtros)}")
    print("\n📁 Archivos generados:")
    print("   • productos.json")
    print("   • categorias.json")
    print("   • productos_categorias.json")
    print("   • filtros_categorias.json")
    print("   • resultados.json (resumen)")
    print("=" * 70)

if __name__ == '__main__':
    main()
