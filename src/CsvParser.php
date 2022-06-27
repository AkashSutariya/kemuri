<?php

namespace StockAnalyzer;

class CsvParser {

    /**
     * @property array $rawData
     */
    private $rawData = [];

    /**
     * @property array $headers
     */
    private $headers = [];

    /**
     * Constructor for csv file parser
     * 
     * @param string $filepath
     * @param bool $withHeader parse file with header 
     * @param bool $parseAsOrm create data with associtive propery
     * 
     * @return new object
     */
    public function __construct($filePath, $withHeader = false, $parseAsOrm = false)
    {
        $file = fopen($filePath,"r");

        if ($withHeader) {
            $this->headers = fgetcsv($file);
        }

        while(!feof($file))
        {
            $row = fgetcsv($file);
            
            if (!$row) {
                continue;
            }

            if ($withHeader && $parseAsOrm) {

                $orm = [];

                foreach ($this->headers as $key => $header) {
                    $orm[$header] = $row[$key];
                }

                array_push($this->rawData, $orm);

            } else {
                array_push($this->rawData, $row);
            }
        }

        fclose($file);
    }

    /**
     * Return raw data of csv file
     * 
     * @return array
     */
    public function getRawData() {
        return $this->rawData;
    }

    /**
     * Return all headers of csv
     * 
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }
}