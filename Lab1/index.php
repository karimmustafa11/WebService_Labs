<?php
require_once 'classes/WeatherService.php';

$cities_json = file_get_contents('city.list.json');
$cities_array = json_decode($cities_json, true);

$egypt_cities = array_filter($cities_array, fn($city) => $city['country'] === 'EG');

$weatherData = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['city_id'])) {
    $cityId = $_POST['city_id'];
    $apiKey = 'c3a59c3ffcab6b80c9635ab8b9e206f8'; 

    $weatherService = new WeatherService($apiKey);
    $weatherData = $weatherService->getWeatherByCityId($cityId);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Egypt Weather</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            padding: 40px;
            color: #333;
        }

        form {
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 350px;
            margin-bottom: 30px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
        }

        select, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        h2 {
            margin-top: 0;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        ul li {
            background: #fff;
            padding: 10px 15px;
            border: 1px solid #e1e1e1;
            margin-bottom: 10px;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <form method="POST">
        <label for="city">Choose Egyptian City:</label>
        <select name="city_id" id="city">
            <?php foreach ($egypt_cities as $city): ?>
                <option value="<?= $city['id'] ?>"><?= $city['name'] ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Get Weather</button>
    </form>

    <?php if ($weatherData): ?>
        <h2>Weather for <?= $weatherData['name'] ?>:</h2>
        <ul>
            <li>Temperature Min: <?= $weatherData['main']['temp_min'] ?> °C</li>
            <li>Temperature Max: <?= $weatherData['main']['temp_max'] ?> °C</li>
            <li>Humidity: <?= $weatherData['main']['humidity'] ?>%</li>
        </ul>
    <?php endif; ?>
</body>
</html>
