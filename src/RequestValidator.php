<?php

namespace StockAnalyzer;

class RequestValidator {

    /**
     * Validate all request inputs
     * 
     * @return array
     */
    public static function validate(): array {

        $check = true;
        $messages = [];

        $allvaidation = [
            self::validateInputCsv(),
            self::validateInputStock(),
            self::validateInputStartDate(),
            self::validateInputEndDate(),
        ];

        foreach ($allvaidation as $valid) {
            if (!$valid["check"]) {
                $check = false;
                array_push($messages, $valid["message"]);
            }
        }

        return [
            "check" => $check,
            "messages" => $messages
        ];
    }

    /**
     * Validation function for uploaded CSV file
     * 
     * @return array
     */
    private static function validateInputCsv(): array {
        
        // Check for input of file is present
        if (!isset($_FILES["input_csv"])) {
            return [
                "check" => false,
                "message" => "csv file is required"
            ];
        }

        $fileUploadErrors = [
            "There is no error, the file uploaded with success",
            "The uploaded file exceeds the upload_max_filesize directive in php.ini",
            "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
            "The uploaded file was only partially uploaded",
            "No file was uploaded",
            "Missing a temporary folder",
            "Failed to write file to disk",
            "A PHP extension stopped the file upload",
        ];

        // check for file uploading errors
        if ($_FILES["input_csv"]["error"] > 0) {
            return [
                "check" => false,
                "message" => $fileUploadErrors[$_FILES["input_csv"]["error"]]
            ];
        }

        // check for file type
        if ($_FILES["input_csv"]["type"] !== "text/csv") {
            return [
                "check" => false,
                "message" => "only csv file is allowed"
            ];
        }

        return ["check" => true];
    }

    /**
     * Validation function for stock name
     * 
     * @return array
     */
    private static function validateInputStock(): array {

        // Check for stock name is present
        if (!isset($_POST["input_stock"])) {
            return [
                "check" => false,
                "message" => "stock name is required"
            ];
        }

        // Check for given stock name is empty
        if (!trim($_POST["input_stock"])) {
            return [
                "check" => false,
                "message" => "stock name shoud not be empty"
            ];
        }

        // Check for given stock name is valid stirng
        if (!preg_match ("/^[a-zA-z0-9]*$/", trim($_POST["input_stock"]))) {
            return [
                "check" => false,
                "message" => "stock name is must be valid string"
            ];
        }

        return ["check" => true];
    }

    /**
     * Validation function for start date
     * 
     * @return array
     */
    private static function validateInputStartDate(): array {

        // Check for start date is present
        if (!isset($_POST["input_startdate"])) {
            return [
                "check" => false,
                "message" => "start date is required"
            ];
        }

        // Check for given start date is empty
        if (!trim($_POST["input_startdate"])) {
            return [
                "check" => false,
                "message" => "start date shoud not be empty"
            ];
        }

        // Check for given start date is valid
        if (!date_create($_POST["input_startdate"])) {
            return [
                "check" => false,
                "message" => "start date is invalid"
            ];
        }

        return ["check" => true];
    }

    private static function validateInputEndDate(): array {

        // Check for end date is present
        if (!isset($_POST["input_enddate"])) {
            return [
                "check" => false,
                "message" => "end date is required"
            ];
        }

        // Check for given end date is empty
        if (!trim($_POST["input_enddate"])) {
            return [
                "check" => false,
                "message" => "end date shoud not be empty"
            ];
        }
        
        $enddate = date_create($_POST["input_enddate"]);

        // Check for given end date is valid
        if (!$enddate) {
            return [
                "check" => false,
                "message" => "end date is invalid"
            ];
        }

        $startdate = null;

        if (isset($_POST["input_startdate"])) {
            $startdate = date_create($_POST["input_startdate"]);
        }

        if ($startdate && $enddate) {

            // check for end date is greater than start date
            if ($enddate <= $startdate) {
                return [
                    "check" => false,
                    "message" => "end date must be greater than start date"
                ];
            }
        }

        return ["check" => true];
    }
}