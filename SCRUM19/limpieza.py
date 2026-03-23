import json
import re
import os

def limpiar_precio(precio_str):
    """Convierte string de precio a float: '$ 240.00' -> 240.0"""
    if not precio_str or precio_str == "N/A":
        return 0.0
    if isinstance(precio_str, (int, float)):
        return float(precio_str)
    try:
        precio_limpio = re.sub(r'[$\s,]', '', str(precio_str))
        return float(precio_limpio)
    except:
        return 0.0

def procesar_carpeta(carpeta):
    """Procesa los archivos JSON en una carpeta específica."""
    ruta_productos = os.path.join(carpeta, 'productos_categorias.json')
    ruta_imagenes = os.path.join(carpeta, 'datos_imagenes.json')

    try:
        with open(ruta_productos, 'r', encoding='utf-8') as f:
            data = json.load(f)
    except FileNotFoundError:
        print(f"Advertencia: No se encontró {ruta_productos}. Saltando carpeta {carpeta}.")
        return

    # Cargar imágenes (si existen)
    try:
        with open(ruta_imagenes, 'r', encoding='utf-8') as f:
            datos_imagenes = json.load(f)
    except FileNotFoundError:
        print(f"Advertencia: No se encontró {ruta_imagenes}. Procesando sin fotos.")
        datos_imagenes = {"categorias": []}

    # Índice de imágenes
    indice_imagenes = {}
    for cat in datos_imagenes.get('categorias', []):
        for prod_img in cat.get('productos', []):
            sku_img = str(prod_img.get('sku', '')).strip().lower()
            if sku_img:
                indice_imagenes[sku_img] = prod_img

    # Limpiar y consolidar productos
    productos_unicos = {}
    for item in data:
        sku = str(item.get('sku', '')).strip()
        sku_key = sku.lower()
        if not sku_key:
            continue

        nombre_sucio = item.get('nombre', '')
        nombre_limpio = re.sub(r'\s+', ' ', str(nombre_sucio).replace('	', ' ')).strip().upper()

        info_img = indice_imagenes.get(sku_key, {})

        if sku_key not in productos_unicos:
            precio_l = limpiar_precio(item.get('precio_lista'))
            precio_o = limpiar_precio(item.get('precio_oferta'))

            productos_unicos[sku_key] = {
                "sku": sku,
                "nombre": nombre_limpio,
                "precio_lista": precio_l,
                "precio_oferta": precio_o,
                "tiene_descuento": (precio_o < precio_l and precio_o > 0),
                "url_producto": item.get('url_producto') or item.get('url'),
                "url_imagen": info_img.get('url_imagen') or item.get('url_imagen'),
                "archivo_imagen": info_img.get('archivo_imagen', 'sin_imagen.jpg'),
                "estado_imagen": info_img.get('estado_imagen', 'pendiente')
            }

    # Filtrar productos válidos
    data_final = [item for item in productos_unicos.values() if item['nombre'] and item['url_producto']]

    # Guardar resultados
    ruta_salida = os.path.join(carpeta, 'productos_limpio.json')
    with open(ruta_salida, 'w', encoding='utf-8') as f:
        json.dump(data_final, f, indent=2, ensure_ascii=False)

    print(f"Carpeta {carpeta}:")
    print(f"  Registros iniciales: {len(data)}")
    print(f"  Registros únicos detectados: {len(productos_unicos)}")
    print(f"  Registros finales (tras filtro de calidad): {len(data_final)}")
    print(f"  Registros descartados: {len(data) - len(data_final)}")

# Procesar todas las carpetas
carpetas = ["SCRUM19", "SCRUM20", "SCRUM21", "SCRUM22"]
for carpeta in carpetas:
    procesar_carpeta(carpeta)