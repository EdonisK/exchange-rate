<?php


namespace App\Controller\Api;


use App\Service\ExchangeRateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExchangeRateController extends AbstractController
{
    /**
     * @param Request $request
     * @param ExchangeRateService $exchangeRateService
     *
     * @return JsonResponse
     * @Route("/getExchangeRate")
     */
    public function getExchangeRate(
        Request $request,
        ExchangeRateService $exchangeRateService
    ) {
        $fromCurrency = $request->get('from');
        $toCurrency = $request->get('to');

        return new JsonResponse([
            'rate' => $exchangeRateService->prepareExchangeRate($fromCurrency, $toCurrency)
        ]);
    }
}