<?php

declare(strict_types=1);

namespace App\Model;

class TransactionsModel extends \App\Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getTransactions()
    {
        $stmt = $this->db->query('SELECT * FROM transactions');
        return $stmt->fetchAll();
    }
    public function saveTransactions(array $data)
    {
        $stmt = $this->db->prepare('INSERT INTO transactions
                                    (`date`, `check_num`, `description`, `amount`)
                                    VALUES (?, ?, ?, ?)');
        foreach ($data as $row) {
            $stmt->execute($row);
        }
    }

    public function selectAllTransactionsData()
    {
        $stmt = $this->db->prepare('SELECT *
                                    FROM transactions');
        $stmt->execute();
        $transactionsData = $stmt->fetchAll();
        return $transactionsData;
    }
    public function clearData()
    {
        $stmt = $this->db->prepare('TRUNCATE TABLE transactions');
        $stmt->execute();
    }
    public function selectAllTransactionsAmount(): array
    {
        $stmt = $this->db->prepare('SELECT amount FROM transactions');
        $stmt->execute();
        $transactionsAmount = $stmt->fetchAll();
        return $transactionsAmount;
    }
}
