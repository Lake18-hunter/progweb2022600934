import requests
from bs4 import BeautifulSoup
import json
from urllib.parse import urljoin
import time

TIMEOUT = 15
DELAY = 1  # Esperar 1 segundo entre solicitudes

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
		
		for enlace in soup.select('a.navmenu-link'):
			nombre = enlace.get_text(strip=True)
			href = enlace.get('href', '')
			
			if not nombre or not href or 'marca' in href.lower():
				continue
			
			if '/collections/' in href:
				url_completa = urljoin(url_base, href)
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
	Intenta extraer el SKU de la URL del producto.
	"""
	try:
		# El SKU generalmente está en la URL o data attribute
		partes = url.split('/')
		if partes:
			return partes[-1]
	except:
		pass
	return None

def extraer_productos_de_categoria(url_categoria, nombre_categoria):
	"""
	Extrae todos los productos de una categoría específica.
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
				
				# Extraer precio
				precio_elem = item.select_one('span.money')
				precio = precio_elem.get_text(strip=True) if precio_elem else "N/A"
				
				# Extraer enlace del producto (para SKU)
				enlace_elem = item.select_one('a.productitem--image-link')
				enlace = enlace_elem.get('href', '') if enlace_elem else ""
				url_producto = urljoin('https://www.woowguau.mx/', enlace)
				
				# Extraer SKU
				sku = extraer_sku_de_url(url_producto)
				
				# Extraer descuento (si existe)
				descuento_elem = item.select_one('div.price--compare-at')
				descuento = "No" if not descuento_elem else "Sí"
				
				if nombre and nombre != "N/A":
					productos.append({
						'nombre': nombre,
						'precio': precio,
						'descuento': descuento,
						'sku': sku,
						'url': url_producto
					})
			
			except Exception as e:
				print(f"Error extrayendo producto: {e}")
				continue
		
		print(f"✓ {len(productos)} productos")
		return productos
	
	except Exception as e:
		print(f"✗ Error en categoría: {e}")
		return []

def main():
	print("=" * 70)
	print("EXTRACCIÓN DE DETALLES DE PRODUCTOS")
	print("=" * 70)
	
	# Paso 1: Obtener categorías
	categorias = obtener_categorias()
	
	if not categorias:
		print("Error: No se pudieron obtener categorías")
		return
	
	# Paso 2: Extraer productos de cada categoría
	print(f"\n[3/4] Navegando categorías y extrayendo productos...\n")
	
	total_productos = 0
	for i, categoria in enumerate(categorias, 1):
		productos = extraer_productos_de_categoria(categoria['url'], categoria['nombre'])
		categoria['productos'] = productos
		total_productos += len(productos)
		print(f"      [{i}/{len(categorias)}]", end='\r', flush=True)
	
	# Paso 3: Guardar resultados
	print(f"\n\n[4/4] Guardando resultados...", end=' ', flush=True)
	
	resultado_final = {
		'sitio': 'WoowGuau',
		'url_base': 'https://www.woowguau.mx/',
		'total_categorias': len(categorias),
		'total_productos': total_productos,
		'categorias': categorias
	}
	
	with open('resultados.json', 'w', encoding='utf-8') as f:
		json.dump(resultado_final, f, ensure_ascii=False, indent=2)
	
	print("✓\n")
	
	# Resumen final
	print("=" * 70)
	print("✓ EXTRACCIÓN COMPLETADA EXITOSAMENTE")
	print("=" * 70)
	print(f"✓ Categorías procesadas: {len(categorias)}")
	print(f"✓ Total de productos extraídos: {total_productos}")
	print(f"✓ Archivo guardado: resultados.json")
	print("=" * 70)

if __name__ == '__main__':
	main()
