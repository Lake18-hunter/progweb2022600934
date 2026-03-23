import requests
from bs4 import BeautifulSoup
import json
from urllib.parse import urljoin
import time
import os
from pathlib import Path
import re

TIMEOUT = 15
DELAY = 1  # Esperar 1 segundo entre solicitudes

# Crear carpeta de imágenes si no existe
CARPETA_IMAGENES = 'imagenes'
if not os.path.exists(CARPETA_IMAGENES):
	os.makedirs(CARPETA_IMAGENES)

def obtener_categorias():
	"""
	Obtiene todas las categorías del sitio desde la página principal.
	"""
	try:
		url_base = 'https://www.woowguau.mx/'
		print(f"[1/4] Obteniendo categorías desde {url_base}...")
		resp = requests.get(url_base, timeout=TIMEOUT)
		soup = BeautifulSoup(resp.text, 'html.parser')
		
		categorias = []
		urls_vistas = set()
		
		for enlace in soup.select('a.navmenu-link'):
			nombre = enlace.get_text(strip=True)
			href = enlace.get('href', '')
			
			if not nombre or not href or 'marca' in href.lower():
				continue
			
			if '/collections/' in href:
				url_completa = urljoin(url_base, href)
				if url_completa in urls_vistas:
					continue
				urls_vistas.add(url_completa)
				categorias.append({
					'nombre': nombre,
					'href': href,
					'url': url_completa,
					'productos': []
				})
		
		print(f"[2/4] Encontradas {len(categorias)} categorías")
		return categorias
		
	except Exception as e:
		print(f"Error obteniendo categorías: {e}")
		return []

def extraer_sku_de_url(url):
	"""
	Extrae el SKU de la URL del producto.
	"""
	try:
		partes = url.split('/')
		if partes:
			# El SKU es generalmente la última parte de la URL
			return partes[-1].replace('?', '').split('&')[0]
	except:
		pass
	return None

def obtener_extension_imagen(url_imagen):
	"""
	Obtiene la extensión de la imagen desde la URL.
	"""
	try:
		# Obtener la extensión del archivo
		path = url_imagen.split('?')[0]  # Eliminar parámetros query
		if '.' in path:
			extension = path.split('.')[-1].lower()
			return extension if extension in ['jpg', 'jpeg', 'png', 'gif', 'webp'] else 'jpg'
	except:
		pass
	return 'jpg'

def normalizar_url_imagen(url_imagen):
	"""
	Normaliza URL de imagen para que siempre tenga esquema válido.
	"""
	if not url_imagen:
		return ""

	url_imagen = url_imagen.strip().replace('{size}', '512x512')

	# Shopify suele devolver URLs con formato //dominio/ruta
	if url_imagen.startswith('//'):
		url_imagen = 'https:' + url_imagen
	elif url_imagen.startswith('/'):
		url_imagen = urljoin('https://www.woowguau.mx/', url_imagen)
	elif not url_imagen.startswith('http://') and not url_imagen.startswith('https://'):
		url_imagen = urljoin('https://www.woowguau.mx/', url_imagen)

	return url_imagen

def sanitizar_nombre_archivo(nombre):
	"""
	Limpia caracteres inválidos para nombres de archivo en Windows.
	"""
	nombre_limpio = re.sub(r'[\\/:*?"<>|]+', '_', nombre)
	nombre_limpio = re.sub(r'\s+', '_', nombre_limpio).strip('._')
	return nombre_limpio or 'sin_nombre'

def descargar_imagen(url_imagen, nombre_archivo):
	"""
	Descarga una imagen desde la URL y la guarda localmente.
	"""
	try:
		ruta_completa = os.path.join(CARPETA_IMAGENES, nombre_archivo)
		
		# Verificar si la imagen ya existe
		if os.path.exists(ruta_completa):
			return True, ruta_completa
		
		# Descargar imagen
		resp = requests.get(url_imagen, timeout=TIMEOUT)
		
		if resp.status_code == 200:
			with open(ruta_completa, 'wb') as f:
				f.write(resp.content)
			return True, ruta_completa
		else:
			return False, None
			
	except Exception as e:
		print(f"Error descargando {url_imagen}: {e}")
		return False, None

def extraer_productos_de_categoria(url_categoria, nombre_categoria):
	"""
	Extrae productos con sus imágenes de una categoría específica.
	"""
	productos = []
	
	try:
		time.sleep(DELAY)
		print(f"   [{nombre_categoria}]...", end=' ', flush=True)
		
		resp = requests.get(url_categoria, timeout=TIMEOUT)
		soup = BeautifulSoup(resp.text, 'html.parser')
		
		# Buscar contenedores de productos
		items_producto = soup.select('li.productgrid--item, div.productitem')
		
		for item in items_producto:
			try:
				# Extraer nombre
				nombre_elem = item.select_one('h2.productitem--title a')
				nombre = nombre_elem.get_text(strip=True) if nombre_elem else "N/A"
				
				# Extraer URL del producto (para SKU)
				enlace_elem = item.select_one('a.productitem--image-link')
				enlace = enlace_elem.get('href', '') if enlace_elem else ""
				url_producto = urljoin('https://www.woowguau.mx/', enlace)
				
				# Extraer SKU
				sku = extraer_sku_de_url(url_producto)
				
				# Extraer URL de imagen
				imagen_elem = item.select_one('img.productitem--image-primary')
				url_imagen = imagen_elem.get('src', '') if imagen_elem else ""
				
				if not url_imagen:
					# Intentar obtener de atributo data
					imagen_elem = item.select_one('img[data-rimg-template]')
					url_imagen = imagen_elem.get('data-rimg-template', '') if imagen_elem else ""
				
				# Si hay URL de imagen, procesarla
				if url_imagen and nombre != "N/A":
					# Normalizar URL de imagen
					url_imagen = normalizar_url_imagen(url_imagen)
					if not url_imagen:
						continue
					
					# Obtener extensión
					extension = obtener_extension_imagen(url_imagen)
					
					# Crear nombre de archivo basado en SKU
					if sku:
						nombre_base = sanitizar_nombre_archivo(sku)
					else:
						nombre_base = sanitizar_nombre_archivo(nombre)
					nombre_archivo = f"{nombre_base}.{extension}"
					
					# Descargar imagen
					exito, ruta_descargada = descargar_imagen(url_imagen, nombre_archivo)
					
					producto = {
						'nombre': nombre,
						'sku': sku,
						'url_imagen': url_imagen,
						'archivo_descargado': nombre_archivo,
						'estado': 'descargado' if exito else 'error',
						'ruta_local': ruta_descargada if exito else None
					}
					
					productos.append(producto)
			
			except Exception as e:
				print(f"Error extrayendo producto: {e}")
				continue
		
		print(f"✓ {len(productos)} imágenes descargadas")
		return productos
	
	except Exception as e:
		print(f"✗ Error en categoría: {e}")
		return []

def main():
	print("=" * 70)
	print("EXTRACCIÓN Y DESCARGA DE IMÁGENES DE PRODUCTOS")
	print("=" * 70)
	
	# Paso 1: Obtener categorías
	categorias = obtener_categorias()
	
	if not categorias:
		print("Error: No se pudieron obtener categorías")
		return
	
	# Paso 2: Extraer y descargar imágenes de cada categoría
	print(f"\n[3/4] Navegando categorías y descargando imágenes...\n")
	
	total_imagenes = 0
	imagenes_descargadas = 0
	imagenes_error = 0
	
	for i, categoria in enumerate(categorias, 1):
		productos = extraer_productos_de_categoria(categoria['url'], categoria['nombre'])
		categoria['productos'] = productos
		total_imagenes += len(productos)
		
		# Contar éxitos y errores
		descargados = len([p for p in productos if p['estado'] == 'descargado'])
		errores = len([p for p in productos if p['estado'] == 'error'])
		
		imagenes_descargadas += descargados
		imagenes_error += errores
		
		print(f"      [{i}/{len(categorias)}]", end='\r', flush=True)
	
	# Paso 3: Guardar resultados
	print(f"\n\n[4/4] Guardando índice de imágenes...", end=' ', flush=True)
	
	resultado_final = {
		'sitio': 'WoowGuau',
		'url_base': 'https://www.woowguau.mx/',
		'carpeta_imagenes': CARPETA_IMAGENES,
		'total_categorias': len(categorias),
		'total_imagenes_encontradas': total_imagenes,
		'imagenes_descargadas': imagenes_descargadas,
		'imagenes_error': imagenes_error,
		'categorias': categorias
	}
	
	with open('resultados.json', 'w', encoding='utf-8') as f:
		json.dump(resultado_final, f, ensure_ascii=False, indent=2)
	
	print("✓\n")
	
	# Resumen final
	print("=" * 70)
	print("✓ DESCARGA COMPLETADA EXITOSAMENTE")
	print("=" * 70)
	print(f"✓ Categorías procesadas: {len(categorias)}")
	print(f"✓ Total de imágenes encontradas: {total_imagenes}")
	print(f"✓ Imágenes descargadas exitosamente: {imagenes_descargadas}")
	print(f"✓ Imágenes con error: {imagenes_error}")
	print(f"✓ Carpeta de imágenes: {CARPETA_IMAGENES}/")
	print(f"✓ Índice guardado: resultados.json")
	print("=" * 70)

if __name__ == '__main__':
	main()
