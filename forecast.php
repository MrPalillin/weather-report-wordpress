<?php
if(isset($_GET["lat"]) && isset($_GET["lon"])) {

  $lat = floatval($_GET["lat"]);
  $lon = floatval($_GET["lon"]);

  $api_url = "https://api.open-meteo.com/v1/forecast?latitude=$lat&longitude=$lon&daily=temperature_2m_max,temperature_2m_min,sunrise,sunset,precipitation_sum,wind_speed_10m_max,wind_direction_10m_dominant";

  $response = file_get_contents($api_url);

  if ($response === FALSE) {
    die('Error al conectar con la API.');
  }

  header('Content-Type: application/json');
  echo $response;
  wp_die();
}