<!DOCTYPE html>
<html>
<head>
<title>SMN</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<style>
	body{background-color:#222; font-family:"Courier New", Courier, monospace; letter-spacing:0px; color:#fff; font-size:16px; line-height:24px;}
	a{color:#fff; text-decoration:none;}
	a:hover{color:#81f499;}
	.campo{color:#81f499;}
	.volver:hover{color:#afece7;}
	h3{color:#99c5b5;}
</style>
</head>
<body>
<?php

	require_once("SMN.class.php");

	$demo = new SMN("buenos.aires", 15, "cache/");

	if (isset($_GET["ciudad"]) and (trim($_GET["ciudad"]) != '')) {
		echo '<h3>Estado del tiempo SMN</h3>';
		$demo->asignar_ciudad(trim($_GET["ciudad"]));
		$ciudad = $demo->obtener_estado_actual();
		foreach ($ciudad as $id_campo => $campo) {
			echo '<span class="campo">' . strtoupper($id_campo) . ':</span> ' . $campo . '<br>';
		}
		echo '<br><a href="index.php" class="volver">Volver</a>';
	}
	else {
		$estaciones = $demo->obtener_estaciones();
		ksort($estaciones);
		echo '<h3>Estaciones meteorológicas disponibles en SMN (' . count($estaciones) . ')</h3>';
		foreach ($estaciones as $id_estacion => $estacion) {
			echo '<a href="?ciudad=' . $id_estacion . '">' . strtoupper(str_ireplace(".", " ", $id_estacion)) . '</a><br>';
		}
	}

?>
</body>
</html>