<?php


namespace App\Service;


use App\Entity\Rate;
use App\Repository\RateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use DateTime;

class ExchangeRateService
{
    private $client;
    private $entityManager;
    private $rateRepository;

    public function __construct(
        HttpClientInterface $client,
        EntityManagerInterface $entityManager,
        RateRepository $rateRepository
    ) {
        $this->client = $client;
        $this->entityManager = $entityManager;
        $this->rateRepository = $rateRepository;
    }

    public function fetchData(string $from, string $to)
    {
        $response = $this->client->request(
            'GET',
            $_ENV['EXCHANGE_RATE_URL'] .'latest?apikey=963d5760-33da-11ec-8446-dd0125377026&base_currency=' . strtoupper($from)
        );

        $content = $response->toArray();

        return $content['data'][$to];
    }

    public function fetchTenLatestData(string $from, string $to)
    {
        $currentDate = new DateTime();
        $currentDate = $currentDate->format('Y-m-d');
        $pastDate = date("Y-m-d", strtotime("$currentDate - 9 days"));

        $response = $this->client->request(
            'GET',
            $_ENV['EXCHANGE_RATE_URL'] . 'historical?apikey=963d5760-33da-11ec-8446-dd0125377026&base_currency='. strtoupper($from) .'&date_from='. $pastDate .'&date_to=' . $currentDate
        );

        $content = $response->toArray();

        return $content['data'];
    }

    public function prepareExchangeRate(string $from, string $to)
    {
        $existingRate = $this->rateRepository->getRateFromLastHour($from, $to);

        if ($existingRate) {
            return $existingRate[0]['rate'];
        }

        $exchangeRates = $this->fetchTenLatestData($from, $to);

        $value = 0;
        foreach ($exchangeRates as $exchangeRate) {
            $value += $exchangeRate[$to];
        }

        $currentRate = $this->fetchData($from, $to);

        $result = $value / 10;

        $finalRate = $currentRate;

        switch ($result) {
            case $result > $currentRate; $finalRate = $currentRate . ' ++ '; break;
            case $result < $currentRate; $finalRate = $currentRate . ' -- '; break;
            case $result == $currentRate; $finalRate = $currentRate . ' - '; break;
        }

        $this->createRate($from, $to, $result);

        return $finalRate;
    }

    public function createRate(string $from, string $to, string $rateValue)
    {
        $rate = new Rate();

        $rate->setFromCurrency($from)
            ->setToCurrency($to)
            ->setRate($rateValue)
            ->setCreatedAt(new DateTime())
        ;

        $this->entityManager->persist($rate);
        $this->entityManager->flush();
    }
}