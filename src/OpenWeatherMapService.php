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
     * The API key.
     */
    protected string $apiKey;

    /**
     * The query to fetch the weather information for.
     */
    protected string $query;

    /**
     * OpenWeatherMapService constructor.
     */
    public function __construct()
    {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();
        if (empty($_ENV['OPEN_WEATHER_MAP_KEY'])) {
            throw new \InvalidArgumentException("an API key is missing.");
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
     * Executes an API request and returns the weather information.
     * Returns FALSE on failure.
     *
     */
    public function getWeather(): string
    {
        $query = [
            'q' => $this->query,
            'appid' => $this->apiKey,
            'units' => 'metric',
        ];
        $client = new Client();
        $errorMsg = 'An error occurred, try again.';

        try {
            $response = $client->get(self::API_BASE_URL . '/weather', ['query' => $query]);

            return $this->parseResponse($response, $response->getStatusCode());
        } catch (ConnectException $e) {
            $errorMsg = 'An connection error occurred.';
        } catch (ClientException  $e) {
            if ($e->hasResponse()) {
                if ($data = json_decode((string)$e->getResponse()->getBody())) {
                    $errorMsg = isset($data->cod) ? 'Status code: ' . $data->cod . ' ' : '';
                    $errorMsg .= isset($data->message) ? 'Message: ' . $data->message : '';
                }
            }
        }

        return $errorMsg;
    }

    /**
     * Parses and returns an OpenWeather current weather API response.
     *
     * @param ResponseInterface $response OpenWeather API response JSON.
     */
    public function parseResponse($response): string
    {
        $result = [];
        if ($response->getBody()) {
            $data = json_decode((string)$response->getBody());
            if (!empty($data->weather[0]->description)) {
                $result[] = ucfirst($data->weather[0]->description);
            }
            if (!empty($data->main->temp)) {
                $result[] = round($data->main->temp) . ' degrees celcius';
            }

            return implode(', ', $result);
        }
    }
}
