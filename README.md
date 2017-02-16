# smn-json
**smn-json** permite la consulta del estado actual del tiempo, de las ciudades disponibles, desde el sitio web del Servicio Meteorológico Nacional Argentino.

```

$demo = new SMN([id_ciudad], [ttl_cache], [ubicacion_cache]);

/* Para obtener las estaciones meteorológicas disponibles en formato JSON */
$demo->imprimir_estaciones(true); 

/* Para obtener las estaciones meteorológicas disponibles en un arreglo */
$estaciones = $demo->obtener_estaciones();

/* Para obtener el estado del tiempo en formato JSON */
$demo->asignar_ciudad("buenos.aires");
$demo->imprimir_estado_actual(true);

/* Para obtener el estado del tiempo en un arreglo */
$demo->asignar_ciudad("buenos.aires");
$ciudad = $demo->obtener_estado_actual();

```

**Nota:** El nombre de la ciudad (id_ciudad) para la consulta del estado del tiempo se debe obtener del listado de estaciones meteorológicas disponibles.

DEMO: http://www.devs.com.ar/smn/
