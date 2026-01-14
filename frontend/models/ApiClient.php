<?php

class ApiClient
{
    private $baseUrl;
    private $timeout = 10;

    public function __construct($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Make a GET request to the API
     */
    public function get($endpoint, $token = null)
    {
        return $this->request('GET', $endpoint, null, $token);
    }

    /**
     * Make a POST request to the API
     */
    public function post($endpoint, $data = [], $token = null)
    {
        return $this->request('POST', $endpoint, $data, $token);
    }

    /**
     * Make a PUT request to the API
     */
    public function put($endpoint, $data = [], $token = null)
    {
        return $this->request('PUT', $endpoint, $data, $token);
    }

    /**
     * Make a DELETE request to the API
     */
    public function delete($endpoint, $token = null)
    {
        return $this->request('DELETE', $endpoint, null, $token);
    }

    /**
     * Make HTTP request to API
     */
    private function request($method, $endpoint, $data = null, $token = null)
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');

        $options = [
            'http' => [
                'method' => $method,
                'timeout' => $this->timeout,
                'header' => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                ]
            ]
        ];

        // Add authorization header if token provided
        if ($token) {
            $options['http']['header'][] = 'Authorization: Bearer ' . $token;
        }

        // Add request body for POST/PUT requests
        if ($data && in_array($method, ['POST', 'PUT'])) {
            $options['http']['content'] = json_encode($data);
        }

        // Suppress warnings and make request
        $context = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            return [
                'success' => false,
                'error' => 'API request failed',
                'data' => null
            ];
        }

        $decoded = json_decode($response, true);

        return [
            'success' => true,
            'data' => $decoded,
            'error' => null
        ];
    }
}
