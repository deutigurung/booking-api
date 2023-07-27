<?php

namespace Tests\Unit;

use App\Services\PricingService;
use PHPUnit\Framework\TestCase;

class ApartmentPricingTest extends TestCase
{
    private PricingService $pricingService;
   
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function setUp(): void{
        parent::setUp();
        $this->pricingService = new PricingService();
    }

    public function test_pricing_for_single_price() : void{
        $prices = collect([
            ['start_date' => '2023-05-01', 'end_date' => '2030-05-01', 'price' => 100]
        ]);
 
        $priceForOneDay = $this->pricingService->calculateApartmentPriceForDates(
            $prices,
            '2023-05-11',
            '2023-05-11'
        );
        $this->assertEquals(100, $priceForOneDay);

        $priceForTwoDay = $this->pricingService->calculateApartmentPriceForDates(
            $prices,
            '2023-05-11',
            '2023-05-12'
        );
        $this->assertEquals(2*100, $priceForTwoDay);
    }

    public function test_pricing_for_multiple_price_ranges() : void{
        $prices = collect([
            ['start_date' => '2023-05-01', 'end_date' => '2023-05-30', 'price' => 100],
            ['start_date' => '2023-06-01', 'end_date' => '2030-07-01', 'price' => 90],
        ]);
 
        $caseFirst = $this->pricingService->calculateApartmentPriceForDates(
            $prices,
            '2023-05-05',
            '2023-05-05'
        );
        $this->assertEquals(100, $caseFirst);

        $caseTwo = $this->pricingService->calculateApartmentPriceForDates(
            $prices,
            '2023-06-01',
            '2023-06-02'
        );
        $this->assertEquals(2*90, $caseTwo);

        $caseThree = $this->pricingService->calculateApartmentPriceForDates(
            $prices,
            '2023-05-30',
            '2023-06-01'
        );
        $this->assertEquals(1*100 + 1*90, $caseThree);
    }
}
