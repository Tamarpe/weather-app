<?php

require_once __DIR__ . '/vendor/autoload.php';

use WeatherApp\OpenWeatherMapService;

$openWeatherMap = new OpenWeatherMapService();
$openWeatherMap->buildQuery($argv);
print $openWeatherMap->getWeather();
