<?php

use PHPUnit\Framework\TestCase;

use StockAnalyzer\CsvParser;
use StockAnalyzer\StockAnalyzer;

class StockAnalyzerTest extends TestCase
{
    public function testStockAnalyzer()
    {
        try {
        //$cf = new CURLFile("tests/testcsv/2.csv");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "/stock_analyzer.php");
        curl_setopt($ch, CURLOPT_POST, true);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, ["upload" => $cf]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        var_dump(curl_getinfo($ch));
        curl_close($ch);
        
        } catch(Exception $e) {
            var_dump($e);
        }
        
    }
}
