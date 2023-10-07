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
            <?php if (isset($transactions)) : ?>
                <?php foreach ($transactions as $transaction) { ?>
                    <tr>
                        <td><?php echo date("M j,Y", strtotime($transaction[0])) ?></td>
                        <td><?php echo $transaction[1] ?></td>
                        <td><?php echo $transaction[2] ?></td>
                        <?php if (extractAmount($transaction) > 0) : ?>
                            <td style="color:green"><?= $transaction[3] ?></td>
                        <?php
                        elseif (extractAmount($transaction) < 0) : ?>
                            <td style="color:red"><?= $transaction[3] ?></td>
                        <?php endif; ?>
                    </tr>
                <?php } ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">Total Income:</th>
                <td><?php echo number_format(totalCalc($transactions)['income'], 2) ?></td>
            </tr>
            <tr>
                <th colspan="3">Total Expense:</th>
                <td><?php echo number_format(totalCalc($transactions)['expense'], 2) ?></td>
            </tr>
            <tr>
                <th colspan="3">Net Total:</th>
                <td><?php echo number_format(totalCalc($transactions)['netTotal'], 2) ?></td>
            </tr>
        </tfoot>
    </table>
</body>

</html>