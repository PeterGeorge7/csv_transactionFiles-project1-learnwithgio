<?php

declare(strict_types=1);

namespace App\Controllers;

use App\View;
use App\Model\TransactionsModel;

class TransactionsController
{
    /**
     * The file path of the uploaded file.
     *
     * @var string
     */
    private string $filePath;

    /**
     * The data of the uploaded file.
     *
     * @var array
     */
    private array $fileData;

    /**
     * The TransactionsModel instance.
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
     * @return View
     */
    public function index(): View
    {
        $transactionData = $this->transactionModel->selectAllTransactionsData(); // from database

        [$totalIncome, $totalExpense, $netTotal] = $this->calculateIncomeAndExpense(
            $this->transactionModel->selectAllTransactionsAmount()
        );

        $transactionData = $this->handleDataForView($transactionData);

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
        $this->storeFileToServer();

        $this->fileData = $this->parseFile();

        $this->saveToDatabase($this->fileData);

        header('Location: /transactions');
    }

    /**
     * Parse the uploaded file and return the data as an array.
     *
     * @return array
     */
    private function parseFile(): array
    {
        $file = fopen($this->filePath, 'r');
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
     * @param array $row
     * @return array
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
     * @param array $data
     */
    private function saveToDatabase(array $data): void
    {
        (new TransactionsModel())->saveTransactions($data);
    }

    /**
     * Store the uploaded file to the server.
     */
    private function storeFileToServer(): void
    {
        // check if file is not uploaded
        if (!isset($_FILES['csv_file'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No file uploaded']);
            return;
        }

        // get the file from $_FILES array
        $file = $_FILES['csv_file'];
        $this->filePath = STORAGE_PATH . '/' . uniqid() . '.csv';

        try {
            $this->validateFile($file['size'], $file['name']);
        } catch (\Throwable $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
            return;
        }

        move_uploaded_file($file['tmp_name'], $this->filePath);
    }

    /**
     * Validate the uploaded file.
     *
     * @param int $fileSize
     * @param string $fileName
     * @return bool
     * @throws \Exception
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
     * @param array $transactionsData
     * @return array
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
     * @param string $date
     * @return string
     */
    private function formatDate(string $date): string
    {
        return date('M j, Y', strtotime($date));
    }

    /**
     * Format the amount for display.
     *
     * @param float $amount
     * @return string
     */
    private function formatAmount(float $amount): string
    {
        $formattedAmount = '$' . number_format(abs($amount), 2);

        return ($amount < 0) ? '-' . $formattedAmount : $formattedAmount;
    }

    /**
     * Calculate the total income and expense from the given amount data.
     *
     * @param array $amountData
     * @return array
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

        return [$totalIncome, $totalExpense, $netTotal]; // returned Formated
    }
}
