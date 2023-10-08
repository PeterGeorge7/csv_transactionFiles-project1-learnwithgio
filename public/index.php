<?php

declare(strict_types=1);

$root = dirname(__DIR__) . DIRECTORY_SEPARATOR;

define('APP_PATH', $root . 'app' . DIRECTORY_SEPARATOR);
define('FILES_PATH', $root . 'transaction_files' . DIRECTORY_SEPARATOR);
define('VIEWS_PATH', $root . 'views' . DIRECTORY_SEPARATOR);

/* YOUR CODE (Instructions in README.md) */

require(APP_PATH . "App.php");

$AllfilesArray = getAllFilesNames(FILES_PATH);


$transactions = getTransaction($AllfilesArray,'extractKeyValueTransactions');

$formatedTransactions = [];
$formatedTransactions = array_merge($formatedTransactions, $transactions);

$totals = totalCalc($formatedTransactions);
require(APP_PATH."helper.php");
require(VIEWS_PATH . "transactions.php");
