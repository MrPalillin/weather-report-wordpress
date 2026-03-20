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
    const element = document.getElementById("results").value;

    if (element == "") {
        alert("Please fill the input before searching.");
        return;
    }

    const city = JSON.parse(element);

    try {
        const res = await fetch(`wp-content/plugins/weather-report-wordpress/includes/forecast.php?lat=${city.lat}&lon=${city.lon}`);

        const data = await res.json();

        renderForecast(data);
    } catch (err) {
        console.log(err);
    }
}

function renderForecast(data) {

    let html = "";

    html += "<div class='table_component'><table class='table table-striped-columns table-hover caption-top'><caption>Forecast in " + document.getElementById('city').value + "</caption><tbody><tr scope='col'><th>Day</th><th scope='col'>Max temp</th><th scope='col'>Min temp</th><th scope='col'>Sunrise</th><th scope='col'>Sunset</th><th scope='col'>Precipitations</th><th scope='col'>Wind</th></tr>";

    for (let i = 0; i < data.daily.time.length; i++) {
        html += "<tr><td>" + data.daily.time[i] + "</td>";
        html += "<td>" + data.daily.temperature_2m_max[i] + " " + data.daily_units.temperature_2m_max + "</td>";
        html += "<td>" + data.daily.temperature_2m_min[i] + " " + data.daily_units.temperature_2m_min + "</td>";
        html += "<td>" + data.daily.sunrise[i].split("T")[1] + "</td>";
        html += "<td>" + data.daily.sunset[i].split("T")[1] + "</td>";
        html += "<td>" + data.daily.precipitation_sum[i] + " " + data.daily_units.precipitation_sum + "</td>";
        html += "<td>" + data.daily.wind_speed_10m_max[i] + " " + data.daily_units.wind_speed_10m_max + " - " + get_wind_direction(data.daily.wind_direction_10m_dominant[i]) + "</td></tr>";
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

        const data = await fetch("wp-content/plugins/weather-report-wordpress/includes/city-finder.php?city=" + encodeURIComponent(city_text));

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