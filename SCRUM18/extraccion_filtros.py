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
					'filtros': []
				})
		
		print(f"[2/4] Encontradas {len(categorias)} categorías")
		return categorias
		
	except Exception as e:
		print(f"Error obteniendo categorías: {e}")
		return []

def extraer_filtros_de_categoria(url_categoria, nombre_categoria):
	"""
	Extrae los filtros disponibles de una categoría específica.
	"""
	filtros = []
	
	try:
		time.sleep(DELAY)
		print(f"   [{nombre_categoria}]...", end=' ', flush=True)
		
		resp = requests.get(url_categoria, timeout=TIMEOUT)
		soup = BeautifulSoup(resp.text, 'html.parser')
		
		# Buscar contenedores de filtros (pueden variar según la estructura)
		# Opciones comunes: div.filter, div.boost-pfs-filter, div.sidebar-filter, etc.
		contenedores_filtro = soup.select(
			'div.boost-pfs-filter-group, '
			'div.filter-group, '
			'div.sidebar-filter, '
			'div.filter-container'
		)
		
		# Si no encuentra con esos selectores, intenta otro patrón
		if not contenedores_filtro:
			contenedores_filtro = soup.select('div[data-filter-group], fieldset')
		
		for contenedor in contenedores_filtro:
			try:
				# Extraer nombre del filtro
				nombre_filtro_elem = contenedor.select_one(
					'h3, label, .filter-title, .filter-name, legend'
				)
				
				if not nombre_filtro_elem:
					continue
				
				nombre_filtro = nombre_filtro_elem.get_text(strip=True)
				
				# Extraer opciones del filtro
				opciones = []
				
				# Buscar opciones en labels, checkboxes, opciones de select, etc.
				for opcion_elem in contenedor.select('label, option, a[data-filter]'):
					texto_opcion = opcion_elem.get_text(strip=True)
					
					# Limpiar el texto de opciones (quitar números de cantidad si existen)
					if '(' in texto_opcion:
						texto_opcion = texto_opcion.split('(')[0].strip()
					
					if texto_opcion and texto_opcion not in opciones:
						opciones.append(texto_opcion)
				
				# Si encontró opciones, agregar el filtro
				if opciones:
					filtros.append({
						'nombre': nombre_filtro,
						'opciones': opciones
					})
			
			except Exception as e:
				print(f"Error extrayendo filtro: {e}")
				continue
		
		print(f"✓ {len(filtros)} filtros")
		return filtros
	
	except Exception as e:
		print(f"✗ Error en categoría: {e}")
		return []

def main():
	print("=" * 70)
	print("EXTRACCIÓN DE FILTROS Y ATRIBUTOS DE CATEGORÍAS")
	print("=" * 70)
	
	# Paso 1: Obtener categorías
	categorias = obtener_categorias()
	
	if not categorias:
		print("Error: No se pudieron obtener categorías")
		return
	
	# Paso 2: Extraer filtros de cada categoría
	print(f"\n[3/4] Navegando categorías y extrayendo filtros...\n")
	
	total_filtros = 0
	for i, categoria in enumerate(categorias, 1):
		filtros = extraer_filtros_de_categoria(categoria['url'], categoria['nombre'])
		categoria['filtros'] = filtros
		total_filtros += len(filtros)
		print(f"      [{i}/{len(categorias)}]", end='\r', flush=True)
	
	# Paso 3: Guardar resultados
	print(f"\n\n[4/4] Guardando resultados...", end=' ', flush=True)
	
	resultado_final = {
		'sitio': 'WoowGuau',
		'url_base': 'https://www.woowguau.mx/',
		'total_categorias': len(categorias),
		'total_filtros_por_categorias': total_filtros,
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
	print(f"✓ Total de filtros extraídos: {total_filtros}")
	print(f"✓ Archivo guardado: resultados.json")
	print("=" * 70)

if __name__ == '__main__':
	main()
