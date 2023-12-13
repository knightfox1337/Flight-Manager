<?php
require_once "DataFormatter.php";
use RichieLennox\DataFormatter;
// setting timezone of server
date_default_timezone_set('Europe/London');
// loading json file
$json = file_get_contents("data/flightdata_01AUG-10AUG.json");
// Decode the JSON file
$json_data = json_decode($json, true);
// init new data formatter
$data_formatter = new DataFormatter();
// format json data
$formatted_data = $data_formatter->formatData($json_data);
// print out formatted data (json)
echo $formatted_data;