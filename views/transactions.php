<!DOCTYPE html>
<html>

<head>
    <title>Transactions</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        table tr th,
        table tr td {
            padding: 5px;
            border: 1px #eee solid;
        }

        tfoot tr th,
        tfoot tr td {
            font-size: 20px;
        }

        tfoot tr th {
            text-align: right;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center;">
        Transactions Data
    </h2>
    <?php
    if (isset($transactionsData) && $transactionsData != null) :
    ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Check #</th>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($transactionsData as $key => $row) :
                ?>
                    <tr>
                        <td><?= $row['date'] ?></td>
                        <td><?= $row['check_num'] ?></td>
                        <td><?= $row['description'] ?></td>
                        <td style="color:
                        <?php
                        echo (substr($row['amount'], 0, 1) === '-') ? 'red' : 'green';
                        ?>;"><?= $row['amount'] ?></td>
                    </tr>
                <?php
                endforeach;
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan=" 3">Total Income:</th>
                    <td><?= $totalIncome ?></td>
                </tr>
                <tr>
                    <th colspan="3">Total Expense:</th>
                    <td><?= $totalExpense ?></td>

                </tr>
                <tr>
                    <th colspan="3">Net Total:</th>
                    <td><?= $netTotal ?></td>

                </tr>
            </tfoot>
        </table>
    <?php
    else :
    ?>
        <h3 style="text-align:center;color:red;">There is No Data Uploaded</h3>
        <h4 style="text-align:center;color:red;">you will be redirected After 5sec</h4>

    <?php
        header("refresh:5;url=/");
    endif;
    ?>
</body>

</html>