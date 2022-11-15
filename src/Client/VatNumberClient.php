<?php

namespace Vilkas\VatNumber\Client;

use Exception;
use Logger;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Vilkas\VatNumber\Utility\VgLogger;

class VatNumberClient
{
    /**
     * @var string
     */
    private $baseurl;

    /** 
     * @var HttpClient 
     */
    private $client;

    /**
     * @var NullLogger|Logger
     */
    private $logger;

    public function __construct()
    {
        $this->baseurl = 'https://ec.europa.eu/taxation_customs/vies/rest-api/ms/';

        $this->logger = VgLogger::createLogger('/var/logs/vatnumber.log', 'vat_number_client');

        $this->client = HttpClient::create();
    }

    /**
     * Do a request and check that the response is somewhat valid.
     *
     * @param string $method    one of GET POST PUT etc
     * @param string $endpoint  path of the url to call
     * @param array  $options   parameters for HttpClient
     *
     * @return array json_decoded response
     *
     * @throws Exception|ExceptionInterface
     */
    public function doRequest(string $method, string $endpoint, array $options): array
    {
        $url = $this->_buildUrl($endpoint);

        try {
            // $old_precision = ini_get('serialize_precision');
            // ini_set('serialize_precision', '-1');
            $response = $this->client->request($method, $url, $options);
            // ini_set('serialize_precision', $old_precision);
            $status = $response->getStatusCode();
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Unsupported option passed to HttpClient', ['exception' => $e]);
            throw $e;
        }

        try {
            // this will throw for all 300-599 and other errors
            $content = $response->getContent();
        } catch (HttpExceptionInterface $e) {
            // for 400 error we can try to dig up a bit better response from the api
            // and throw it as a new exception for controllers to show
            if (Response::HTTP_BAD_REQUEST === $status) {
                $content = $response->getContent(false);

                $results = json_decode($content, true);
                if (null === $results) {
                    // TODO: probably needs more information
                    $this->logger->error('Could not decode JSON response');
                    throw new Exception('Could not decode JSON response');
                }
            }

            // try to return the content as is, it probably contains some valid debug data
            $content = $e->getResponse()->getContent(false);
            $this->logger->error(
                'API request response other than 200',
                ['status' => $status, 'content' => $content, 'exception' => $e]
            );
            throw new Exception($content);
        } catch (TransportExceptionInterface $e) {
            // TODO: make this better
            $this->logger->error('Network error occurred', ['exception' => $e]);
            throw $e;
        } catch (Exception $e) {
            // TODO: make this better
            // something bad happened
            $this->logger->error('Something bad happened', ['exception' => $e]);
            throw $e;
        }
        // decode the json response
        $results = json_decode($content, true);

        if (null === $results) {
            // TODO: probably needs more information
            $this->logger->error('Could not decode JSON response');
            throw new Exception('Could not decode JSON response');
        }

        return $results;
    }

    /**
     * Get service points by address.
     *
     * https://guides.atdeveloper.postnord.com/#747cfedf-fa97-4145-8a3e-5031c38416f9
     *
     * @throws Exception|ExceptionInterface
     */
    public function getVatInfo(string $memberState, string $vatNumber): array
    {
        $endpoint = implode('/', [$memberState, 'vat', $vatNumber]);
        try {
            $response = $this->doRequest('GET', $endpoint, []);
        } catch (Exception $e) {
            $this->logger->error('Error getting Vat Information', ['exception' => $e]);

            return [
                'error' => $e->getMessage(),
            ];
        }

        $this->logger->debug('Received response', [
            'response' => $response
        ]);
        return $response;
    }

    /**
     * Build full url from base + endpoint
     */
    public function _buildUrl(string $endpoint)
    {
        return $this->baseurl . $endpoint;
    }
}
