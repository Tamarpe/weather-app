<?php declare(strict_types=1);

namespace WeatherApp;

use Dotenv;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Psr\Http\Message\ResponseInterface;

class OpenWeatherMapService
{
    /**
     * The base URL API of OpenWeatherMap.
     */
    const API_BASE_URL = 'api.openweathermap.org/data/2.5';

    /**
     * The OpenWeatherMap API key.
     */
    protected ?string $apiKey;

    /**
     * The query to fetch the weather information for.
     */
    protected string $query;

    /**
     * OpenWeatherMapService constructor.
     *
     * @throws \InvalidArgumentException if the API key is missing.
     */
    public function __construct()
    {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();
        if (!$this->isNotEmpty($_ENV['OPEN_WEATHER_MAP_KEY'])) {
            throw new \InvalidArgumentException('an API key is missing.');
        }
        $this->apiKey = $_ENV['OPEN_WEATHER_MAP_KEY'];
    }

    /**
     * Builds the query for the API.
     *
     * @param array $argv The given arguments.
     */
    public function buildQuery($argv): void
    {
        unset($argv[0]);
        $query = implode(' ', $argv);
        $this->query = $query;
    }

    /**
     * Executes an API request and returns the weather information
     * or an error message on failure.
     */
    public function getWeather(): string
    {
        $query = [
            'q' => $this->query,
            'appid' => $this->apiKey,
            'units' => 'metric',
        ];
        $client = new Client();
        $errorMsg = '';

        try {
            $response = $client->get(self::API_BASE_URL . '/weather', ['query' => $query]);
            return $this->parseResponse($response);
        } catch (ConnectException $e) {
            return 'An connection error occurred.';
        } catch (ClientException  $e) {
            if ($e->hasResponse()) {
                if ($data = json_decode((string)$e->getResponse()->getBody())) {
                    $errorMsg = isset($data->cod) ? 'Status code: ' . $data->cod . ' ' : '';
                    $errorMsg .= isset($data->message) ? 'Message: ' . $data->message : '';
                }
            }
        }
        $errorMsg ??= 'An error occurred, try again.';

        return $errorMsg;
    }

    /**
     * Parses the API response and returns the current weather.
     *
     * @param ResponseInterface $response OpenWeather API response JSON.
     */
    public function parseResponse($response): string
    {
        $result = [];
        if ($response->getBody()) {
            $data = json_decode((string)$response->getBody());
            if ($this->isNotEmpty($data->weather[0]->description)) {
                $result[] = ucfirst($data->weather[0]->description);
            }
            if ($this->isNotEmpty($data->main->temp)) {
                $result[] = round($data->main->temp) . ' degrees celcius';
            }

            return implode(', ', $result);
        }
    }

    /**
     * Check if a given variable is defined and not empty.
     *
     * @param mixed $variable the given variable.
     */
    public function isNotEmpty(&$variable): bool
    {
        return isset($variable) && $variable !== '';
    }
}
