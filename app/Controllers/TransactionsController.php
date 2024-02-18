<?php

declare(strict_types=1);

namespace App\Controllers;

use App\View;
use App\Model\TransactionsModel;

/**
 * TransactionsController Class
 *
 * This class is responsible for handling the transactions related functionality.
 */
class TransactionsController
{
    /**
     * TransactionsModel instance.
     *
     * @var TransactionsModel
     */
    private TransactionsModel $transactionModel;

    /**
     * Create a new TransactionsController instance.
     */
    public function __construct()
    {
        $this->transactionModel = new TransactionsModel();
    }

    /**
     * Display the transactions view.
     *
     * @return View The transactions view.
     */
    public function index(): View
    {
        // Retrieve all transaction data from the database
        $transactionData = $this->transactionModel->selectAllTransactionsData();

        // Calculate total income, total expense, and net total
        [$totalIncome, $totalExpense, $netTotal] = $this->calculateIncomeAndExpense(
            $this->transactionModel->selectAllTransactionsAmount()
        );

        // Prepare transaction data for view display
        $transactionData = $this->handleDataForView($transactionData);

        // Return the transactions view with the necessary data
        return View::make('transactions', [
            'transactionsData' => $transactionData,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netTotal' => $netTotal
        ]);
    }

    /**
     * Submit the file uploaded by the user.
     */
    public function submitFileFromUser()
    {
        // Check if file is not uploaded
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'][0] != 0) {
            http_response_code(400);
            echo json_encode(['error' => 'No file uploaded']);
            return;
        }

        $numberOfFiles = sizeof($_FILES['csv_file']['name']);

        foreach ($_FILES['csv_file']['name'] as $key => $value) {

            $fileName = $_FILES['csv_file']['name'][$key];
            $fileSize = (int) $_FILES['csv_file']['size'][$key];
            $fileTmp = $_FILES['csv_file']['tmp_name'][$key];

            $storedFilePath = $this->storeFileToServer($fileName, $fileSize, $fileTmp);

            $fileData = $this->parseFile($storedFilePath);

            $this->saveToDatabase($fileData);
        }
        header('Location: /transactions');
    }

    /**
     * Parse the uploaded file and return the data as an array.
     *
     * @param string $savedFilePath The path of the saved file.
     * @return array The parsed file data.
     */
    private function parseFile(string $savedFilePath): array
    {
        $file = fopen($savedFilePath, 'r');
        $data = [];
        while (($row = fgetcsv($file)) !== false) {
            // Skip the header row
            if ($row[0] === 'Date') {
                continue;
            }
            $row = $this->handleRowDataForDB($row);
            $data[] = $row;
        }
        fclose($file);

        return $data;
    }

    /**
     * Handle the row data for database storage.
     *
     * @param array $row The row data.
     * @return array The modified row data.
     */
    private function handleRowDataForDB(array $row): array
    {
        $row[3] = str_replace(['$', ','], '', $row[3]);
        $row[0] = date('Y-m-d', strtotime($row[0]));
        return $row;
    }

    /**
     * Save the data to the database.
     *
     * @param array $data The data to be saved.
     */
    private function saveToDatabase(array $data): void
    {
        (new TransactionsModel())->saveTransactions($data);
    }

    /**
     * Store the uploaded file to the server.
     *
     * @param string $fileName The name of the uploaded file.
     * @param int $fileSize The size of the uploaded file.
     * @param string $fileTmp The temporary path of the uploaded file.
     * @return string The stored file path.
     */
    private function storeFileToServer(string $fileName, int $fileSize, string $fileTmp): string
    {
        try {
            $this->validateFile($fileSize, $fileName);
        } catch (\Throwable $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }

        $filePath = STORAGE_PATH . '/' . uniqid() . '.csv';
        move_uploaded_file($fileTmp, $filePath);
        return $filePath;
    }

    /**
     * Validate the uploaded file.
     *
     * @param int $fileSize The size of the uploaded file.
     * @param string $fileName The name of the uploaded file.
     * @return bool True if the file is valid, false otherwise.
     * @throws \Exception If the file size limit is exceeded or the file extension is invalid.
     */
    private function validateFile(int $fileSize, string $fileName): bool
    {
        // Check file size
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        if ($fileSize > $maxFileSize) {
            throw new \Exception('File size limit exceeded. Maximum file size allowed is 5MB.');
        }

        // Check file extension
        $allowedExtensions = ['csv', 'xlsx'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new \Exception('Invalid file extension. Only CSV and XLSX files are allowed.');
        }
        return true;
    }

    /**
     * Handle the row data for view display.
     *
     * @param array $transactionsData The transaction data.
     * @return array The modified transaction data.
     */
    private function handleDataForView(array $transactionsData): array
    {
        foreach ($transactionsData as $key => $row) {
            $transactionsData[$key]['date'] = $this->formatDate($row['date']);
            $transactionsData[$key]['amount'] = $this->formatAmount($row['amount']);
        }

        return $transactionsData;
    }

    /**
     * Format the date for display.
     *
     * @param string $date The date to be formatted.
     * @return string The formatted date.
     */
    private function formatDate(string $date): string
    {
        return date('M j, Y', strtotime($date));
    }

    /**
     * Format the amount for display.
     *
     * @param float $amount The amount to be formatted.
     * @return string The formatted amount.
     */
    private function formatAmount(float $amount): string
    {
        $formattedAmount = '$' . number_format(abs($amount), 2);

        return ($amount < 0) ? '-' . $formattedAmount : $formattedAmount;
    }

    /**
     * Calculate the total income and expense from the given amount data.
     *
     * @param array $amountData The amount data.
     * @return array The calculated total income, total expense, and net total.
     */
    private function calculateIncomeAndExpense(array $amountData): array
    {
        $totalIncome = $totalExpense = 0;

        foreach ($amountData  as $key => $value) {
            if ($value['amount'] < 0) {
                $totalExpense += $value['amount'];
            } else {
                $totalIncome += $value['amount'];
            }
        }
        $netTotal = $this->formatAmount($totalIncome + $totalExpense);
        $totalIncome = $this->formatAmount($totalIncome);
        $totalExpense = $this->formatAmount($totalExpense);

        return [$totalIncome, $totalExpense, $netTotal];
    }

    /**
     * Display the transaction details view.
     *
     * @return View The transaction details view.
     */
    public function details(): View
    {
        $transactionId = (int) $_GET['id'];

        // Retrieve transaction data by ID
        $transactionDataFetched = $this->transactionModel->selectTransactionDataById($transactionId);

        if (!$transactionDataFetched) {
            http_response_code(404);
            echo 'Transaction not found';
            exit;
        }

        // Format transaction data for display
        $transactionData['date'] = $this->formatDate($transactionDataFetched['date']);
        $transactionData['amount'] = $this->formatAmount($transactionDataFetched['amount']);
        $transactionData['check_num'] = $transactionDataFetched['check_num'] ?: 'N/A';
        $transactionData['description'] = $transactionDataFetched['description'] ?: 'N/A';
        $transactionData['id'] = $transactionDataFetched['id'];

        // Return the transaction details view with the necessary data
        return View::make('transaction-details', [
            'transactionData' => $transactionData
        ]);
    }
}
