
import requests
from bs4 import BeautifulSoup
import json
from urllib.parse import urljoin
import time

TIMEOUT = 15
DELAY = 1  # Esperar 1 segundo entre solicitudes

def extraer_datos_principal():
	"""
	Extrae categorías y marcas desde la página principal.
	"""
	try:
		url_base = 'https://www.woowguau.mx/'
		print(f"[1/3] Conectando a {url_base}...")
		resp = requests.get(url_base, timeout=TIMEOUT)
		soup = BeautifulSoup(resp.text, 'html.parser')
		
		# Extraer todos los enlaces del menú
		categorias = []
		marcas = []
		
		for enlace in soup.select('a.navmenu-link'):
			nombre = enlace.get_text(strip=True)
			href = enlace.get('href', '')
			
			if not nombre or not href:
				continue
			
			url_completa = urljoin(url_base, href)
			
			if 'marca' in href.lower():
				marcas.append({
					'nombre': nombre,
					'href': href,
					'url': url_completa
				})
			elif '/collections/' in href:
				categorias.append({
					'nombre': nombre,
					'href': href,
					'url': url_completa,
					'marcas': []
				})
		
		print(f"[2/3] Encontradas {len(categorias)} categorías y {len(marcas)} marcas principales")
		return url_base, categorias, marcas
		
	except Exception as e:
		print(f"Error extrayendo página principal: {e}")
		return None, [], []

def extraer_marcas_de_categoria(url_categoria, nombre_categoria):
	"""
	Extrae marcas específicas de una categoría.
	"""
	try:
		time.sleep(DELAY)  # Esperar entre solicitudes
		print(f"   Extrayendo de: {nombre_categoria}...", end=' ', flush=True)
		resp = requests.get(url_categoria, timeout=TIMEOUT)
		soup = BeautifulSoup(resp.text, 'html.parser')
		
		marcas_categoria = []
		
		# Buscar enlaces que contengan marcas
		for enlace in soup.select('a'):
			texto = enlace.get_text(strip=True)
			href = enlace.get('href', '')
			
			if texto and 'marca' in href.lower() and '/products/' in href:
				if texto not in [m['nombre'] for m in marcas_categoria]:
					marcas_categoria.append({
						'nombre': texto,
						'href': href
					})
		
		print(f"✓ {len(marcas_categoria)} marcas")
		return marcas_categoria
		
	except Exception as e:
		print(f"✗ Error: {e}")
		return []

def main():
	print("=" * 60)
	print("EXTRACCIÓN DE JERARQUÍA, CATEGORÍAS Y MARCAS")
	print("=" * 60)
	
	url_base, categorias, marcas_principales = extraer_datos_principal()
	
	if not url_base:
		print("Error: No se pudieron obtener los datos")
		return
	
	print(f"\n[3/3] Navegando categorías...")
	
	# Extraer marcas de cada categoría
	for i, categoria in enumerate(categorias, 1):
		marcas = extraer_marcas_de_categoria(categoria['url'], categoria['nombre'])
		categoria['marcas'] = marcas
		print(f"    [{i}/{len(categorias)}]", end='\r', flush=True)
	
	# Estructura final
	jerarquia = {
		'sitio': 'WoowGuau',
		'url': url_base,
		'categorias': categorias,
		'marcas_principales': marcas_principales,
		'total_categorias': len(categorias),
		'total_marcas': len(marcas_principales)
	}
	
	# Guardar resultados
	with open('resultados.json', 'w', encoding='utf-8') as f:
		json.dump(jerarquia, f, ensure_ascii=False, indent=2)
	
	print("\n" + "=" * 60)
	print("✓ EXTRACCIÓN COMPLETADA")
	print("=" * 60)
	print(f"✓ Categorías extraídas: {len(categorias)}")
	print(f"✓ Marcas encontradas: {len(marcas_principales)}")
	print(f"✓ Archivo guardado: resultados.json")
	print("=" * 60)

if __name__ == '__main__':
	main()
