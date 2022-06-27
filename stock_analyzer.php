<?php

include("vendor/autoload.php");

use StockAnalyzer\StockAnalyzer;

echo json_encode(StockAnalyzer::run());
return;