<?php declare(strict_types=1);

namespace Solo;

use Exception;
use Solo\Http\Client\ClientFactory;

abstract class BaseApiClient
{
    /**
     * @var ClientFactory
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $baseUri;

    public function __construct(ClientFactory $clientFactory, array $headers, string $baseUri)
    {
        $this->httpClient = $clientFactory->withHeaders($headers);
        $this->baseUri = $baseUri;
    }

    /**
     * @param string $endpoint
     * @return mixed
     */
    public function get(string $endpoint)
    {
        return $this->request('GET', $endpoint);
    }

    /**
     * @param string $endpoint
     * @param array $data
     * @return mixed
     */
    public function post(string $endpoint, array $data = [])
    {
        return $this->request('POST', $endpoint, $data);
    }

    /**
     * @param string $endpoint
     * @param array $data
     * @return mixed
     */
    public function put(string $endpoint, array $data = [])
    {
        return $this->request('PUT', $endpoint, $data);
    }

    /**
     * @param string $endpoint
     * @return mixed
     */
    public function delete(string $endpoint)
    {
        return $this->request('DELETE', $endpoint);
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    protected function request(string $method, string $endpoint, array $data = [])
    {
        try {
            $endpoint = $this->baseUri . $endpoint;
            $response = $this->httpClient->{$method}($endpoint, $data);
            $body = (string)$response->getBody();
            $body = $this->decodeBody($body);
            return json_decode($body);
        } catch (Exception $e) {
            throw new Exception("Error in {$method} request to {$endpoint}: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $body
     * @return string
     */
    abstract protected function decodeBody(string $body): string;
}