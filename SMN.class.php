<?php

	class SMN {

		private $ciudad = '';
		private $estacion = 0;
		private $limite_cache = 0;
		private $path_cache = '';
		private $pronostico = array();
		private $estaciones = array();

		public function __construct($ciudad = "buenos.aires", $limite_cache = 120, $path_cache = '') {
			$this->limite_cache = $limite_cache;
			$this->path_cache = $path_cache;
			$this->recargar_estaciones();
			$this->asignar_ciudad($ciudad);
		}

		private function normalizar_nombre_estacion($nombre) {
			$nombre = strtolower($nombre);
			$nombre = strtr($nombre, utf8_decode("áéíóúñ"), "aeioun");
			$nombre	= str_ireplace(".", "", $nombre);
			$nombre	= str_ireplace("<br>", " ", $nombre);
			$nombre	= str_ireplace("(", "", $nombre);
			$nombre	= str_ireplace(")", "", $nombre);
			$nombre	= str_ireplace(" ", ".", $nombre);
			return utf8_encode($nombre);
		}

		private function recargar_estaciones() {
			$this->estaciones = array();

			$html = '';
			$archivo_cache = $this->path_cache . 'estaciones.json';

			if (($this->limite_cache == 0) or ((!file_exists($archivo_cache)) or ((strtotime("now") - filemtime($archivo_cache)) >= ($this->limite_cache * 60)) and ($this->limite_cache > 0))) {
				if ($web = @fopen("http://www3.smn.gov.ar/?mod=dpd&id=21&e=total", "r")) {
					while (!feof ($web)) {
						$html .= fgets($web);
					}
					fclose($web);
				}

				preg_match_all('/<td height="20" bgcolor="#DDEEFF" align="center" class="font1"><a href=\?mod=dpd&id=21&e=(.*?)>(.*?)<\/a><td>/si', $html, $salida, PREG_SET_ORDER);
				foreach($salida as $estacion) {
					$this->estaciones[$this->normalizar_nombre_estacion($estacion[2])] = utf8_encode($estacion[1]);
				}

				if ($this->limite_cache > 0) {
					if ($gestor = fopen($archivo_cache, 'w')) {
						fwrite($gestor, json_encode($this->estaciones));
						fclose($gestor);
					}
				}
			}
			else {
				$gestor = fopen($archivo_cache, "r");
				$this->estaciones = json_decode(fread($gestor, filesize($archivo_cache)), true);
				fclose($gestor);
			}
		}

		public function asignar_ciudad($ciudad) {
			$this->ciudad = $ciudad;
			$this->estacion = $this->estaciones[$ciudad];
			if ($this->estacion == '') {
				$this->estacion = 'error';
			}
		}

		public function recargar_estado_actual() {
			$html = '';
			$archivo_cache = $this->path_cache . $this->estacion . '.json';

			if (($this->limite_cache == 0) or ((!file_exists($archivo_cache)) or ((strtotime("now") - filemtime($archivo_cache)) >= ($this->limite_cache * 60)) and ($this->limite_cache > 0))) {
				if ($web = @fopen("http://www3.smn.gov.ar/?mod=dpd&id=21&e=" . $this->estacion, "r")) {
					while (!feof ($web)) {
						$html .= fgets($web);
					}
					fclose($web);
				}

				preg_match('/<td class="tdtitulo">Condiciones Meteorol&oacute;gicas en (.*?)<\/td>/si', $html, $salida);
				if (trim($salida[1]) != '') {
					$this->pronostico['ciudad'] = utf8_encode($salida[1]);
					preg_match('/ESTADO DEL TIEMPO: <span style="color:#54664E; font-weight:bold;">(.*?)<\/span>/si', $html, $salida);
					$this->pronostico['estado'] = utf8_encode($salida[1]);
					preg_match('/VISIBILIDAD: <span style="color:#54664E;">(.*?)<\/span>/si', $html, $salida);
					$this->pronostico['visibilidad'] = utf8_encode($salida[1]);
					preg_match('/TEMPERATURA: <span style="color:#54664E;">(.*?)<\/span>/si', $html, $salida);
					$this->pronostico['temperatura'] = utf8_encode($salida[1]);
					preg_match('/HUMEDAD: <span style="color:#54664E;">(.*?)<\/span>/si', $html, $salida);
					$this->pronostico['humedad'] = utf8_encode($salida[1]);
					preg_match('/VIENTO: <span style="color:#54664E;">(.*?)<\/span>/si', $html, $salida);
					$this->pronostico['viento'] = utf8_encode($salida[1]);
					preg_match('/SENSACION TERMICA: <span style="color:#54664E;">(.*?)<\/span>/si', $html, $salida);
					if (stripos($salida[1], 'no se calcula') === false) {
						$this->pronostico['termica'] = utf8_encode($salida[1]);
					}
					else {
						$this->pronostico['termica'] = utf8_encode('No se calcula');
					}
					preg_match('/PRESION NIVEL LOCALIDAD: <span style="color:#54664E;">(.*?)<\/span>/si', $html, $salida);
					$this->pronostico['presion'] = utf8_encode($salida[1]);
				}
				else {
					$this->pronostico['error'] = 'El estado actual no está disponible para la ciudad solicitada [' . $this->ciudad . ']';
				}

				if ($this->limite_cache > 0) {
					if ($gestor = fopen($archivo_cache, 'w')) {
						fwrite($gestor, json_encode($this->pronostico));
						fclose($gestor);
					}
				}
			}
			else {
				$gestor = fopen($archivo_cache, "r");
				$this->pronostico = json_decode(fread($gestor, filesize($archivo_cache)), true);
				fclose($gestor);
			}
		}

		public function obtener_estado_actual() {
			$this->recargar_estado_actual();
			return $this->pronostico;
		}

		public function obtener_estaciones() {
			$this->recargar_estaciones();
			return $this->estaciones;
		}

		public function imprimir_estado_actual($header = false) {
			if ($header) {
				header('Content-Type: text/html; charset=utf-8');
				header('Content-Type: application/json');
			}
			$this->recargar_estado_actual();
			echo json_encode($this->pronostico);
		}

		public function imprimir_estaciones($header = false) {
			if ($header) {
				header('Content-Type: text/html; charset=utf-8');
				header('Content-Type: application/json');
			}
			$this->recargar_estaciones();
			echo json_encode($this->estaciones);
		}
	}

?>