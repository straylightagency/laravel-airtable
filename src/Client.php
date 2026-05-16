<?php

namespace Straylightagency\LaravelAirtable;

use RuntimeException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\ConnectionException;

/**
 * Airtable API Client.
 *
 * @package Straylightagency\LaravelAirtable
 * @author Anthony Pauwels <anthony@straylightagency.be>
 */
class Client
{
    /**
     * Client constructor.
     *
     * @param string $baseUrl
     * @param string $apiKey
     */
    public function __construct(
        protected string $baseUrl,
        protected string $apiKey
    ) {
    }

    /**
     * Send a GET request to Airtable.
     *
     * @param string $query_string
     * @param array $data
     * @return array
     *
     * @throws RuntimeException
     * @throws ConnectionException
     */
    public function sendGet(string $query_string, array $data = []): array
    {
        if ( !empty( $data ) ) {
            $query_string .= '?';

            foreach ( $data as $key => $value ) {
                $query_string .= '&' . $key . '=' . urlencode( $value );
            }
        }

        return $this->handleResponse(
            $this->makeRequest()->get( $query_string )
        );
    }

    /**
     * Send a POST request.
     *
     * @param string $query_string
     * @param array $data
     * @return array
     *
     * @throws RuntimeException
     * @throws ConnectionException
     */
    public function sendPost(string $query_string, array $data = []): array
    {
        return $this->handleResponse(
            $this->makeRequest()->post( $query_string, $data )
        );
    }

    /**
     * Send a PATCH request.
     *
     * @param string $query_string
     * @param array $data
     * @return array
     *
     * @throws RuntimeException
     * @throws ConnectionException
     */
    public function sendPatch(string $query_string, array $data = []): array
    {
        return $this->handleResponse(
            $this->makeRequest()->patch( $query_string, $data )
        );
    }

    /**
     * Send a PUT request.
     *
     * @param string $query_string
     * @param array $data
     * @return array
     *
     * @throws RuntimeException
     * @throws ConnectionException
     */
    public function sendPut(string $query_string, array $data = []): array
    {
        return $this->handleResponse(
            $this->makeRequest()->put( $query_string, $data )
        );
    }

    /**
     * Send a DELETE request.
     *
     * @param string $query_string
     * @param array $data
     * @return array
     *
     * @throws RuntimeException
     * @throws ConnectionException
     */
    public function sendDelete(string $query_string, array $data = []): array
    {
        return $this->handleResponse(
            $this->makeRequest()->delete( $query_string, $data )
        );
    }

    /**
     * Make a new HTTP Client PendingRequest with the right headers, the bearer token and base URL.
     *
     * @return PendingRequest
     */
    protected function makeRequest(): PendingRequest
    {
        return Http::baseUrl( $this->baseUrl )
            ->withToken( $this->apiKey )
            ->acceptJson()
            ->asJson();
    }

    /**
     * Handle the response to convert JSON into readable array
     *
     * @param Response $response
     * @return array
     *
     * @throws RuntimeException
     */
    protected function handleResponse(Response $response):array
    {
        $content = $response->json();

        if ( ! is_array( $content ) ) {
            throw new RuntimeException( 'Content returned by Airtable Client is not an array.' );
        }

        $status = $response->status();

        if ( ! ( $status >= 200 && $status <= 299 ) ) {
            throw new RuntimeException( $content['message'] );
        }

        return $content;
    }
}
