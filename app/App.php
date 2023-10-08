<?php

declare(strict_types=1);

/**
 * Summary of getAllFiles
 * @param string $dirPath
 * @return array
 */
function getAllFilesNames(string $dirPath): array
{
    $files = [];
    foreach (scandir($dirPath) as $fileName) {
        if (is_file($dirPath . $fileName)) {
            $files[] = $dirPath . $fileName;
        }
    }
    return $files;
}


/**
 * Summary of getTransaction
 * @param array $files
 * @param mixed $transactionHandler
 * @return array
 */
function getTransaction(array $files, ?callable $transactionHandler = null): array
{
    $transactions = [];
    $rowNum = 1; // to ignore first row in csv
    foreach ($files as $file) {
        if (($fileStream = fopen($file, "r")) != false) {
            while (($transactionRow = fgetcsv($fileStream)) != false) {
                if($rowNum > 1){
                    if ($transactionHandler !== null) {
                        $transactionRow = $transactionHandler($transactionRow);
                    }
                    $transactions[] = $transactionRow;
                }
                $rowNum++;
            }
        }
    }
    return $transactions;
}

/**
 * Summary of extractKeyValueTransaction
 * @param array $transactionRow
 * @return array 
 */
function extractKeyValueTransactions(array $transactionRow): array
{
    // ! what if i have more files with diffrent formats then will makesomething Diffrent in code debend on this case
    [$date, $check, $desc, $amount] = $transactionRow;
    $formatedAmount = (float) str_replace(['$', ','], '', $amount);
    // return formated Amount with key=> value;
    return [
        'date' => $date,
        'check' => $check,
        'desc' => $desc,
        'amount' => $formatedAmount
    ];
}

/**
 * Summary of totalCalc
 * @param array $formatedTransactions
 * @return array 
 */

// * in first update i have sent the pre-formated array then reformat here
// * but in tutorial in the public we record all formated and send the formated to make less dependence in the func.
function totalCalc(array $formatedTransactions): array
{
    $totalCalced = [
        'income' => 0,
        'expense' => 0,
        'netTotal' => 0
    ];
    foreach ($formatedTransactions as $transaction) {
        $totalCalced['netTotal'] += $transaction['amount'];
        if ($transaction['amount'] > 0) {
            $totalCalced['income'] += $transaction['amount'];
        } else {
            $totalCalced['expense'] += $transaction['amount'];
        }
    }
    // its a way but i found another one in the first line in foreach
    //  $totalCalced['netTotal'] = $totalCalced['income'] - abs($totalCalced['expense']);
    // return array of final records
    return $totalCalced;
}


// ! with the mentor we prefer to make the formating for text and this things in the view or in layer between them so we have craeted helper.php to use in view to fomat our text,floats & date 