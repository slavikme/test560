<?php

$dir = __DIR__;

require_once "$dir/../functions.php";
require_once "$dir/TestUtils.php";

TestUtils::checkFuncTrue("loadCSVTransactions", ["$dir/data/transaction_demo.csv"]);
TestUtils::checkFuncTrue("removeExistingLinesInFile2FromFile1", ["$dir/data/file1", "$dir/data/file2"]);
TestUtils::checkFuncTrue("isSumsCombinationExists", [[1,2,3,4,5], 9]);
TestUtils::checkFuncFalse("isSumsCombinationExists", [[1,14,7,9,34,26], 71]);