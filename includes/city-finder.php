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
	ob_start();
	?>
		<div class='container text-center'>
			<div class='row'>
				<div class='col-6'>
					<input  class='form-control' type='search' placeholder='Write city name here' id='city' required oninput='startSearch()' list='cities'>
					<datalist id='cities'></datalist>
				</div>
				<div class='col-6'>
						<button class='btn btn-primary' onclick='setForecast()'>Show forecast</button>
				</div>
			</div>
			<div class='row'>
				<input id='results' type='hidden' readonly>
				<div id='forecast-container' style='margin-top: 2em;'>
				</div>
			</div>
		</div>
		<script>
			document.addEventListener('DOMContentLoaded', function () {
			document.getElementById('city').addEventListener('input', function () {
					const value = this.value;
					const options = document.querySelectorAll('#cities option');
					const match = Array.from(options).find(opt => opt.value === value);
					if (match) {
						const selected = {
							name: match.value,
							id: match.dataset.id,
							lat: match.dataset.latitude,
							lon: match.dataset.longitude
						};
						document.getElementById('results').value = JSON.stringify(selected);
					}else{
						document.getElementById('results').value = '';
					}
				});
			});
		</script>
	<?php
	return ob_get_clean();
}
?>