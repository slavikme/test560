<?php

require_once __DIR__ . "/Database.php";

/**
 * 1b. Load CSV file into the DB.
 * @param string $filename The CSV filename location to load.
 * @return bool
 * @throws Exception
 */
function loadCSVTransactions($filename)
{

    $db = new Database();

    if (($fhandler = fopen($filename, "r")) === false) {
        throw new Exception("Unable to open the CSV file");
    }

    while (($row = fgetcsv($fhandler)) !== false) {
        $price = trim($row[1]);
        if (is_numeric($price) && $price >= 0) {
            $category_name = trim(strtolower($row[0]));
            $datetime = trim(strtolower($row[2]));
            $db->addTransaction($category_name, $price, $datetime);
        }
    }

    return true;
}

/**
 * 2. Remove existing lines in file2 from file1.
 * @param string $file1
 * @param string $file2
 * @return bool
 * @throws Exception
 */
function removeExistingLinesInFile2FromFile1($file1, $file2)
{
    // Open files
    if ( !($fh1 = fopen($file1, "r")) ) {
        throw new Exception("Unable to open the file '$file1'");
    }
    if ( !($fh1tmp = fopen("{$file1}__tmp__", "w")) ) {
        throw new Exception("Unable to open the file '{$file1}__tmp__'");
    }
    if ( !($fh2 = fopen($file2, "r")) ) {
        throw new Exception("Unable to open the file '$file2'");
    }

    $bf1 = fgets($fh1);
    $bf2 = fgets($fh2);

    while ($bf1 !== false && $bf2 !== false) {
        if ($bf1 <= $bf2) {
            if ($bf1 != $bf2) {
                fwrite($fh1tmp, $bf1);
            }
            $bf1 = fgets($fh1);
        } else {
            $bf2 = fgets($fh2);
        }
    }

    fclose($fh1);
    fclose($fh1tmp);
    fclose($fh2);

    unlink($file1);
    rename("{$file1}__tmp__", $file1);

    return true;
}

/**
 * 3. Determines whether any combination of the number inside the array sums up to the number given.
 * @param array $numbersArray
 * @param integer $number
 * @return bool
 */
function isSumsCombinationExists(array $numbersArray, $number)
{
    global $counter;
    $counter++;
    $arr_length = count($numbersArray);

    if ( !$arr_length ) {
        return false;
    }

    if ( $arr_length == 1 ) {
        return array_shift($numbersArray) == $number;
    }

    if ( array_sum($numbersArray) == $number ) {
        return true;
    }

    foreach ( $numbersArray as $key => $value )
    {
        unset($numbersArray[$key]);
        if ( isSumsCombinationExists($numbersArray, $number) ) {
            return true;
        }
        $numbersArray[$key] = $value;
    }
    return false;
}