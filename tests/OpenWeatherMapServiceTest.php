<?php declare(strict_types=1);

namespace WeatherApp;

use PHPUnit\Framework\TestCase;

final class OpenWeatherMapServiceTest extends TestCase
{
    public function testBadRequest()
    {
        $openWeatherMap = new OpenWeatherMapService();
        $openWeatherMap->buildQuery([]);
        $this->assertEquals('Status code: 400 Message: Nothing to geocode', $openWeatherMap->getWeather());
    }

    public function testGetWeather()
    {
        $openWeatherMap = new OpenWeatherMapService();
        $openWeatherMap->buildQuery([
            'weather',
            'Antwerp'
        ]);
        $this->assertStringContainsString('degrees celcius', $openWeatherMap->getWeather());
    }
}
