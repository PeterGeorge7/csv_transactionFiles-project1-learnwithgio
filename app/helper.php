<?php



/**
 * Summary of formatFloatToText
 * @param float $transactionAmount
 * @return string
 */
function formatFloatToText(float $transactionAmount): string
{
    if ($transactionAmount > 0) {
        return '$' . number_format($transactionAmount, 2);
    } else {
        return '-$' . number_format(abs($transactionAmount), 2);
    }
    // how mentor typed it profissonally ->
    /*
    $isNegative = $transactionAmount < 0;
    return ($isNegative ? '-' : '') . '$' . number_format(abs($transactionAmount), 2);
    */
}

// ! I preferd to make the date format in view its not in need to scripted function