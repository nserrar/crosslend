<?php

namespace CrossLend\BidBundle\Managers;

use CrossLend\BidBundle\Gateway\CrossLendBidGateway;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Monolog\Logger;

/**
 * Class BidManager
 * @package CrossLend\BidBundle\Managers
 */
class BidManager
{

    /**
     * @var CrossLendBidGateway
     */
    private $crossLendBidGateway;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(CrossLendBidGateway $crossLendBidGateway, Logger $logger)
    {
        $this->crossLendBidGateway = $crossLendBidGateway;
        $this->logger = $logger;
    }

    /**
     * Get Params boundries and parity from CrossLend Gateway
     *
     * @return array
     * @throws GuzzleException
     */
    public function initiateParams(): array
    {
        return $this->crossLendBidGateway->initiateCalculation();
    }

    /**
     * Request system values odd or even between given boundries from Gateway  and calcul SUM
     *
     * @param int    $odd
     * @param int    $min
     * @param int    $max
     * @param string $requestId
     * @return array
     * @throws GuzzleException
     */
    public function getSystemValuesAndSum(int $odd, int $min, int $max, string $requestId): array
    {
        $systemValue = 0;
        $systemValues[$requestId] = [];
        $systemValues['SUM'] = 0;
        $i = 0;

        do {
            try {
                //the call to get system value by number gives random error that should be fixed in the API
                //i catch that and log it but still get the value since we keep trying until we get it
                $systemValue = $this->crossLendBidGateway->getSystemValue($i);

                if ($odd === 0) {
                    if ($systemValue % 2 === 0 && $systemValue >= $min && $systemValue <= $max) {
                        $systemValues[$requestId][] = $systemValue;
                        $systemValues['SUM'] += $systemValue;
                    }
                } else {
                    if ($systemValue % 2 !== 0 && $systemValue >= $min && $systemValue <= $max) {
                        $systemValues[$requestId][] = $systemValue;
                        $systemValues['SUM'] += $systemValue;
                    }
                }

                $i++;

            } catch (RequestException $ex) {
                $this->logger->addError($ex->getMessage());
            }
        } while ($systemValue <= $max);

        return $systemValues;
    }

    /**
     * Post solution to CrossLend Gateway
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
        return $this->crossLendBidGateway->postSolution($odd, $min, $max, $requestId, $solution);
    }
}
