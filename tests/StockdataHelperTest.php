<?php

use PHPUnit\Framework\TestCase;

use StockAnalyzer\StockdataHelper;

class StockdataHelperTest extends TestCase
{
    public function testGetPricesForMaximumProfit()
    {

        $input = [320];

        $output = StockdataHelper::getPricesForMaximumProfit($input);

        $this->assertEquals([], $output);

        $input = [320, 324, 319, 319, 323, 313, 320];

        $output = StockdataHelper::getPricesForMaximumProfit($input);

        $this->assertEquals([313, 320], $output);

        $input = [320, 310, 300, 299, 270, 250];

        $output = StockdataHelper::getPricesForMaximumProfit($input);

        $this->assertEquals([300, 299], $output);
    }

    public function testCalculateMean()
    {

        $input = [320];

        $output = StockdataHelper::calculateMean($input);

        $this->assertEquals(320, $output);

        $input = [320, 324, 319, 319, 323, 313, 320];

        $output = StockdataHelper::calculateMean($input);

        $this->assertEquals(319.71, $output);
    }

    public function testCalculateStandardDeviation()
    {

        $input = [320];

        $output = StockdataHelper::calculateStandardDeviation($input);

        $this->assertEquals(0, $output);

        $input = [320, 324, 319, 319, 323, 313, 320];

        $output = StockdataHelper::calculateStandardDeviation($input);

        $this->assertEquals(3.55, $output);
    }
}
