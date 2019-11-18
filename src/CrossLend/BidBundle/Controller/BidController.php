<?php

namespace CrossLend\BidBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BidController extends Controller
{

    /**
     * Homepage
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction() : Response
    {
        return $this->render('@CrossLendBid/Default/index.html.twig');
    }

    /**
     * Initialize
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function initiateAction(): Response
    {
        $bidManager = $this->get('cross_lend.bid.manager');

        return $this->render('@CrossLendBid/Default/initiate.html.twig', [
            'params' => $bidManager->initiateParams()
        ]);
    }

    /**
     * Request System values and calculation
     *
     * @param $odd
     * @param $min
     * @param $max
     * @param $requestId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSystemValuesAction(int $odd, int $min, int $max, string $requestId): Response
    {
        $bidManager = $this->get('cross_lend.bid.manager');
        $params = [
            'odd'       => $odd,
            'min'       => $min,
            'max'       => $max,
            'requestId' => $requestId
        ];

        $systemValues = $bidManager->getSystemValuesAndSum($params['odd'], $params['min'], $params['max'], $params['requestId']);

        return $this->render('@CrossLendBid/Default/system_values.html.twig', [
            'systemValues' => $systemValues,
            'params'       => $params
        ]);
    }

    /** Post Solution and show result
     *
     * @param int    $odd
     * @param int    $min
     * @param int    $max
     * @param string $requestId
     * @param int    $solution
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postSolutionAction(int $odd,int $min,int $max, string $requestId, int $solution): Response
    {
        $bidManager = $this->get('cross_lend.bid.manager');
        $result = $bidManager->postSolution($odd, $min, $max, $requestId, $solution);
        $flashbag = $this->get('session')->getFlashBag();

        if($result == 200){
            $flashbag->add('success', 'Solution posted successfully');
        }else{
            $flashbag->add('error', 'A problem occured, try again later');
        }

        return $this->render('@CrossLendBid/Default/index.html.twig');
    }
}
