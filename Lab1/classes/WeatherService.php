<?php

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;

class WeatherService
{
    private $client;
    private $apiKey;

    public function __construct($apiKey)
    {
        $this->client = new Client(['base_uri' => 'https://api.openweathermap.org/data/2.5/']);
        $this->apiKey = $apiKey;
    }

    public function getWeatherByCityId($cityId)
    {
        $response = $this->client->get('weather', [
            'query' => [
                'id' => $cityId,
                'appid' => $this->apiKey,
                'units' => 'metric'
            ]
        ]);

        return json_decode($response->getBody(), true);
    }
}
