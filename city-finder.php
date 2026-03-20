<?php
if (isset($_GET["city"])) {
	$api_url = "https://geocoding-api.open-meteo.com/v1/search?name=".urlencode($_GET["city"])."&count=20&language=en&format=json";
	$api_response = file_get_contents($api_url);

	if ($api_response === FALSE) {
		die('Error al conectar con la API de ciudades.');
	}

	$response = json_decode($api_response, false);

	$list = [];

	if (isset($response -> results)) {

		foreach($response -> results as $result){

			$elem = array(
				"name" => $result -> name,
				"latitude" => $result -> latitude,
				"longitude" => $result -> longitude,
				"country" => $result -> country,
				"id" => $result -> id,
			);

			$list[] = $elem;
		}

	}
	header('Content-Type: application/json; charset=utf-8');
	header("Access-Control-Allow-Origin: *");
	echo json_encode($list);
	wp_die();

}

function getInput() {
	$text = "<div class='container text-center'>";
	$text.= "<div class='row'>";
	$text.= "<div class='col-6'>";
	$text.= "<input  class='form-control' type='search' placeholder='Write city name here' id='city' required oninput='startSearch()' list='cities'></input>";
	$text.= "<datalist id='cities'></datalist>";
	$text.= "</div><div class='col-6'>";
	$text.= "<button class='btn btn-primary' onclick='setForecast()'>Show forecast</button></div>";
	$text.= "</div><div class='row'>";
	$text.= "<input id='results' type='hidden' readonly></input>";
	$text.= "<div id='forecast-container' style='margin-top: 2em;'></div></div></div>";
	$text.= "<script>document.addEventListener('DOMContentLoaded', function () {document.getElementById('city').addEventListener('input', function () {const value = this.value;const options = document.querySelectorAll('#cities option');const match = Array.from(options).find(opt => opt.value === value);if (match) {const selected = {name: match.value,id: match.dataset.id,lat: match.dataset.latitude,lon: match.dataset.longitude};document.getElementById('results').value = JSON.stringify(selected);}else{document.getElementById('results').value = '';}});});</script>";
	return $text;
}

?>
	<script>
		const startSearch = debounce(updateList, 500);

		function debounce(callback, delay) {
			let timer;
		return function (...args) {
			clearTimeout(timer)
			timer = setTimeout(() => {
			callback.apply(this, args);
			}, delay)
		  }
	}

		async function setForecast() {
			console.log("CLICK detectado");
		const element = document.getElementById("results").value;

		if (element == "") {
			alert("Please fill the input before searching.");
		return;
  }

		const city = JSON.parse(element);

		try {
    const res = await fetch(
		`wp-content/plugins/weather-widget/forecast.php?lat=${city.lat}&lon=${city.lon}`
		);

		const data = await res.json();

		console.log(data);

		renderForecast(data);
  } catch (err) {
			console.log(err);
  }
}

		function renderForecast(data) {
			
			let html = "";

			/*let html = "<style>";
			html += ".table_component {overflow: auto;width: 100%;}";
			html += ".table_component table {border: 1px solid #dededf;height: 100%;width: 100%;table-layout: fixed;border-collapse: collapse;border-spacing: 1px;text-align: left;}";
			html += ".table_component caption {caption - side: top;text-align: middle; font-weight: bold;}";
			html += ".table_component th {border: 1px solid #dededf;background-color: #eceff1;color: #000000;padding: 5px;}";
			html += ".table_component td {border: 1px solid #dededf;background-color: #ffffff;color: #000000;padding: 5px;}";
			html += "</style>";*/

		html += "<div class='table_component'><table class='table table-striped-columns table-hover caption-top'><caption>Forecast in "+document.getElementById('city').value+"</caption><tbody><tr scope='col'><th>Day</th><th scope='col'>Max temp</th><th scope='col'>Min temp</th><th scope='col'>Sunrise</th><th scope='col'>Sunset</th><th scope='col'>Precipitations</th><th scope='col'>Wind</th></tr>";
			
			for (let i = 0; i < data.daily.time.length; i++){
				html += "<tr><td>" + data.daily.time[i] + "</td>";
			html += "<td>"+data.daily.temperature_2m_max[i]+" "+data.daily_units.temperature_2m_max+"</td>";
			html += "<td>"+data.daily.temperature_2m_min[i]+" "+data.daily_units.temperature_2m_min+"</td>";
			html += "<td>"+data.daily.sunrise[i].split("T")[1]+"</td>";
			html += "<td>"+data.daily.sunset[i].split("T")[1]+"</td>";
			html += "<td>"+data.daily.precipitation_sum[i]+" "+data.daily_units.precipitation_sum+"</td>";
			html += "<td>"+data.daily.wind_speed_10m_max[i]+" "+data.daily_units.wind_speed_10m_max+" - "+get_wind_direction(data.daily.wind_direction_10m_dominant[i])+"</td></tr>";
		}

			html += "</tbody></table></div>";

document.getElementById("forecast-container").innerHTML = html;
		  
}

function get_wind_direction(deg) {

	const directions = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];

	return directions[Math.round(deg / 45) % 8];

}

async function updateList() {

	const city_text = document.getElementById("city").value;

	if (city_text.length < 3) return;

	try {

		const data = await fetch("wp-content/plugins/weather-widget/city-finder.php?city=" + encodeURIComponent(city_text));

		if (!data.ok) {
			throw new Error('Response status: ' + data.status);
		}

		const cities = await data.json();

		const datalist = document.getElementById("cities");

		datalist.innerHTML = "";

		cities.forEach(city => {

			const option = document.createElement("option");
			option.value = city.name + ", " + city.country;
			option.dataset.id = city.id;
			option.dataset.latitude = city.latitude;
			option.dataset.longitude = city.longitude;

			datalist.appendChild(option);

		});

	} catch (err_city) {
		console.log("Error al intentar ejecutar el fetch. " + err_city);
	}

}

</script>