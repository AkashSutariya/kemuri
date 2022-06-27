<?php

use PHPUnit\Framework\TestCase;

use StockAnalyzer\CsvParser;

class CsvParserTest extends TestCase
{
    public function testCsvParser()
    {
        $csvparser = new CsvParser("tests/testcsv/1.csv");
        $this->assertCount(5, $csvparser->getRawData());
        $this->assertCount(0, $csvparser->getHeaders());

        $csvparser = new CsvParser("tests/testcsv/1.csv", true);
        $this->assertCount(4, $csvparser->getRawData());
        $this->assertCount(4, $csvparser->getHeaders());

        $csvparser = new CsvParser("tests/testcsv/1.csv", true, true);
        
        $headers = $csvparser->getHeaders();
        $data = $csvparser->getRawData();

        foreach ($headers as $header) {
            foreach ($data as $d) {
                $this->assertArrayHasKey($header, $d);
            }
        }
    }
}
