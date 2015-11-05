<?php

require_once "Database.php";

/**
 * 1b
 * @param $filename
 * @throws Exception
 */
function loadCSVTransactions($filename) {

    $db = new Database();

    if ( ($fhandler = fopen($filename, "r")) === false ) {
        throw new Exception("Unable to open the CSV file");
    }

    while ( ($row = fgetcsv($fhandler)) !== false ) {
        $price = trim(strtolower($row[1]));
        if ( is_numeric($price) ) {
            $category_name = trim(strtolower($row[0]));
            $datetime = trim(strtolower($row[2]));
            $db->addTransaction($category_name, $price, $datetime);
        }
    }
}