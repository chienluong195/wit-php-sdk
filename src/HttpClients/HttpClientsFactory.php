<?php
namespace Wit\HttpClients;

use GuzzleHttp\Client;
use InvalidArgumentException;

class HttpClientsFactory
{
    private function __construct()
    {
        // a factory constructor should never be invoked
    }

    /**
     * HTTP client generation.
     *
     * @param WitHttpClientInterface|Client|string|null $handler
     *
     * @throws Exception                If the cURL extension or the Guzzle client aren't available (if required).
     * @throws InvalidArgumentException If the http client handler isn't "curl", "stream", "guzzle", or an instance of Wit\HttpClients\WitHttpClientInterface.
     *
     * @return WitHttpClientInterface
     */
    public static function createHttpClient($handler)
    {
        if (!$handler) {
            return self::detectDefaultClient();
        }

        if ($handler instanceof WitHttpClientInterface) {
            return $handler;
        }

        if ('curl' === $handler) {
            if (!extension_loaded('curl')) {
                throw new Exception('The cURL extension must be loaded in order to use the "curl" handler.');
            }

            return new WitCurlHttpClient();
        }

        if ('guzzle' === $handler && !class_exists('GuzzleHttp\Client')) {
            throw new Exception('The Guzzle HTTP client must be included in order to use the "guzzle" handler.');
        }

        if ($handler instanceof Client) {
            return new WitGuzzleHttpClient($handler);
        }
        if ('guzzle' === $handler) {
            return new WitGuzzleHttpClient();
        }

        throw new InvalidArgumentException('The http client handler must be set to "curl", "stream", "guzzle", be an instance of GuzzleHttp\Client or an instance of Wit\HttpClients\WitHttpClientInterface');
    }

    /**
     * Detect default HTTP client.
     *
     * @return WitHttpClientInterface
     */
    private static function detectDefaultClient()
    {
        if (extension_loaded('curl')) {
            return new WitCurlHttpClient();
        }

        if (class_exists('GuzzleHttp\Client')) {
            return new WitGuzzleHttpClient();
        }

        return new WitStreamHttpClient();
    }
}
