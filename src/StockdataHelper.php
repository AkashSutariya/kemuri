<?php

namespace StockAnalyzer;

class StockdataHelper {

    /**
     * Find buy and sell price from stock prices
     * 
     * @param array $prices array with prices
     * 
     * @return array
     */
    public static function getPricesForMaximumProfit($prices) {
    
        $totalData = count($prices);

        if ($totalData < 2) {
            return [];
        }

        $currentProfit  = 0;
        $currentBuy = $prices[0];
        $globalSell = $prices[1];
        $globalProfit = $globalSell - $currentBuy;
    
        for ($i = 1; $i < $totalData; $i++) {

            $currentProfit = $prices[$i] - $currentBuy;
    
            if ($currentProfit > $globalProfit) {
                $globalProfit = $currentProfit;
                $globalSell = $prices[$i];
            }
    
            if ($prices[$i] < $currentBuy) {
                $currentBuy = $prices[$i];
            }
        }
    
        return [$globalSell - $globalProfit, $globalSell];
    }

    /**
     * Calculate Mean for given array of numbers
     * 
     * @param array $prices array of prices
     * 
     * @return float $mean
     */
    public static function calculateMean($prices): float {
        return round(
            array_sum($prices) / count($prices), 2
        );
    }

    /**
     * Calculate Standard Deviation for given array of numbers
     * 
     * @param array $prices array of prices
     * 
     * @return float $sd
     */
    public static function calculateStandardDeviation($prices): float {

        $totalPrices = count($prices);

        if ($totalPrices < 2) {
            return 0;
        }

        $mean  = self::calculateMean($prices);

        $deviations = [];

        foreach ($prices as $price) {
            array_push($deviations, pow($price - $mean, 2));
        }

        $variance = array_sum($deviations) / ($totalPrices - 1);

        return round(sqrt($variance), 2);
    }
}