<?php

namespace CrossLend\BidBundle\Gateway;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Monolog\Logger;

/**
 * Class CrossLendBidGateway
 * @package CrossLend\BidBundle\Gateway
 */
class CrossLendBidGateway
{

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var string
     */
    private $authToken;

    /**
     * @var string
     */
    private $applicantName;

    /**
     * @var Logger
     */
    private $logger;



    public function __construct(string $authToken, string $applicantName, Logger $logger)
    {
        $this->authToken = $authToken;
        $this->applicantName = $applicantName;
        $this->logger = $logger;

        $this->httpClient = new Client([
            'base_uri' => 'http://apply.crosslend.io',
            'header'   => [
                'Accept'       => 'application/json'
            ]
        ]);
    }

    /**
     * Initialize Params to get Boundries and parity
     *
     * @return array
     * @throws GuzzleException
     */
    public function initiateCalculation(): array
    {

        $result = $this->httpClient->request('GET', '/bid', [
            'headers' => [
                'authorization' => 'Bearer ' . $this->authToken
            ]
        ]);

        return json_decode($result->getBody()->getContents(), true);
    }

    /**
     * Return system value by number
     *
     * @param int $number
     * @return int
     * @throws GuzzleException
     */
    public function getSystemValue(int $number): int
    {
        $result = $this->httpClient->request('GET', "/bid/$number", [
            'headers' => [
                'authorization' => 'Bearer ' . $this->authToken
            ]
        ]);

        return json_decode($result->getBody()->getContents(), true);
    }

    /**
     * Post solution to CrossLend
     *
     * @param int    $odd
     * @param int    $min
     * @param int    $max
     * @param string $requestId
     * @param int    $solution
     * @return int
     * @throws GuzzleException
     */
    public function postSolution(int $odd, int $min, int $max, string $requestId, int $solution) : int
    {
        try{
            $result = $this->httpClient->request('POST', '/bid', [
                'headers' => [
                    'authorization' => 'Bearer '. $this->authToken,
                    'content-type'  => 'multipart/form-data;'
                ],
                'form_params'   => [
                    'min'               => $min,
                    'max'               => $max,
                    'odd'               => $odd,
                    'solution'          => $solution,
                    'applicantName'     => $this->applicantName,
                    'originalRequestId' => $requestId
                ]
            ]);

            return $result->getStatusCode();

        }catch (RequestException $ex){
            $this->logger->addError($ex->getMessage());

            return 500;
        }
    }
}
