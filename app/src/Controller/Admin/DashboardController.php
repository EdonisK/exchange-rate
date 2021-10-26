<?php


namespace App\Controller\Admin;


use App\Entity\Rate;
use App\Form\GetRateType;
use App\Form\RateType;
use App\Service\ExchangeRateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends AbstractController
{
    private $exchangeRateService;

    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->exchangeRateService = $exchangeRateService;
    }

    /**
     * @Route("/")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $rate = new Rate();

        $form = $this->createForm(RateType::class, $rate);

        $form->handleRequest($request);

        // Check user already used
        if ($form->isSubmitted()) {
            $this->exchangeRateService->prepareExchangeRate($form['fromCurrency']->getData(), $form['toCurrency']->getData());

            $rate->setRate($this->exchangeRateService->prepareExchangeRate($form['fromCurrency']->getData(), $form['toCurrency']->getData()));
            return $this->getRate($rate);
        }

        return $this->render('dashboard.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/getRate/{id}", methods={"GET", "POST"})
     * @param Rate $rate
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getRate(Rate $rate)
    {
        $form = $this->createForm(GetRateType::class, $rate);

        return $this->render('dashboard.html.twig', [
            'form' => $form->createView(),
        ]);

    }
}