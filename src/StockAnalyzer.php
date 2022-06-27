<?php

namespace StockAnalyzer;

use StockAnalyzer\RequestValidator;
use StockAnalyzer\CsvParser;
use StockAnalyzer\StockdataHelper;

class StockAnalyzer {

    private static $csvDateFormat = "d-m-Y";

    public static function run() {
        // Validate HTTP Request
        $validator = RequestValidator::validate();

        // check failure for and send response with validtion messages
        if (!$validator["check"]) {
            return [
                "success" => false,
                "errors" => $validator["messages"]
            ];
        }

        // parse csv file
        $csvParser = new CsvParser($_FILES["input_csv"]["tmp_name"], true, true);

        // check for all required header present in csv
        if ([
                "id_no",
                "date",
                "stock_name",
                "price"
            ] !== $csvParser->getHeaders()
        ) {
            return [
                "success" => false,
                "errors" => ["Invalid Headers in CSV"]
            ];
        }

        $csvData = $csvParser->getRawData();

        // check for valid data in csv and sanitize appropritae csv data
        $invalidCsv = false;

        foreach ($csvData as $key => $data) {

            $csvData[$key]["stock_name"] = strtoupper(trim($data["stock_name"]));

            // associate date object
            $dateObject = date_create_from_format(self::$csvDateFormat, trim($data["date"]));
            // check date is valid date
            if (!$dateObject) {
                $invalidCsv = true;
            
                return [
                    "success" => false,
                    "errors" => ["Invalid Values in CSV"]
                ];
                break;
            }

            $csvData[$key]["dateobject"] = $dateObject;

            // associate price
            $price = trim($data["price"]);
            // check price is valid numeric
            if (!preg_match ("/^[0-9]*$/", $price) || intval($price) < 0) {
                $invalidCsv = true;
                return [
                    "success" => false,
                    "errors" => ["Invalid Values in CSV"]
                ];
                break;
            }

            $csvData[$key]["price"] = intval($price);
        }

        if ($invalidCsv) {
            return;
        }

        // ------ filter data start -----
        // Filter Data from CSV data with applied input filter
        $filteredData = [];

        $stockName = strtoupper(trim($_POST["input_stock"]));
        $startDate = date_create(trim($_POST["input_startdate"]));
        $endDate = date_create(trim($_POST["input_enddate"]));

        foreach ($csvData as $data) {

            if ($data['stock_name'] !== $stockName) {
                continue;
            }

            if ($data["dateobject"] < $startDate) {
                continue;
            }

            if ($data["dateobject"] > $endDate) {
                continue;
            }

            array_push($filteredData, $data);
        }
        // ------ filter data end -----

        if (count($filteredData) < 2) {
            return [
                "success" => false,
                "errors" => ["No data available for given inputs"]
            ];
            return;
        }

        // ------ sort data start -----
        // Sort data date wise
        $sortedData = [];
        $totaldays = intval(date_diff($startDate, $endDate)->format('%a'));

        for ($i = 0; $i <= $totaldays; $i++) {
            $sortedData[$i] = null;
        }

        foreach($filteredData as $data) {
            $dayIndex = intval(date_diff($startDate, $data["dateobject"])->format("%a"));
            $sortedData[$dayIndex] = $data;
        }

        $sortedData = array_values(array_filter($sortedData));
        // ------ sort data end -----

        $allPrices = [];

        foreach ($sortedData as $data) {
            array_push($allPrices, $data["price"]);
        }


        // ------ create final output ------
        $buySellPrices = StockdataHelper::getPricesForMaximumProfit($allPrices);
        $mean = StockdataHelper::calculateMean($allPrices);
        $standardDeviation = StockdataHelper::calculateStandardDeviation($allPrices);

        $buyPrice = $buySellPrices[0];
        $buyDate = null;
        $sellPrice = $buySellPrices[1];
        $sellDate = null;

        foreach ($sortedData as $data) {
            if (!$buyDate && $buyPrice === $data["price"]) {
                $buyDate = $data["date"];
            }

            if ($buyDate && $sellPrice === $data["price"]) {
                $sellDate = $data["date"];
                break;
            }
        }


        return [
            "success" => true,
            "data" => [
                "buydate" => $buyDate,
                "selldate" => $sellDate,
                "profit" => ($sellPrice - $buyPrice) * 200,
                "mean" => $mean,
                "standardDeviation" => $standardDeviation
            ]
        ];
    }
}
