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
 * @return array
 */
function getTransaction(array $files): array
{
    $transactions = [];
    $rowNum = 1;
    foreach ($files as $file) {
        if (($fileStream = fopen($file, "r")) != false) {
            while (($row = fgetcsv($fileStream, filesize($file), ",")) != false) {
                if ($rowNum > 1) {
                    $transactions[] = $row;
                }
                $rowNum++;
            }
        }
    }
    return $transactions;
}

/**
 * Summary of extractAmount
 * @param array $transaction
 * @return float 
 */
function extractAmount(array $transaction): float
{
    [$date, $check, $desc, $amount] = $transaction;
    $formatedAmount = (float) str_replace(['$', ','], '', $amount);
    return $formatedAmount;
}

/**
 * Summary of totalCalc
 * @param array $transaction
 * @return array 
 */
function totalCalc(array $transactions): array
{
    $totalCalced = [
        'income' => 0,
        'expense' => 0,
        'netTotal' => 0
    ];
    foreach ($transactions as $transaction) {
        if (extractAmount($transaction) > 0) {
            $totalCalced['income'] += extractAmount($transaction);
        } else {
            $totalCalced['expense'] += extractAmount($transaction);
        }
    }
    $totalCalced['netTotal'] = $totalCalced['income'] - abs($totalCalced['expense']);
    return $totalCalced;
}
