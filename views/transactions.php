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
            <?php if (isset($formatedTransactions)) : ?>
                <?php foreach ($formatedTransactions as $transaction) { ?>
                    <tr>
                        <td><?php echo date("M j,Y", strtotime($transaction['date'])) ?></td>
                        <td><?php echo $transaction['check'] ?></td>
                        <td><?php echo $transaction['desc'] ?></td>
                        <?php if ($transaction['amount'] > 0) : ?>
                            <td style="color:green"><?= formatFloatToText($transaction['amount']) ?></td>
                        <?php
                        elseif ($transaction['amount'] < 0) : ?>
                            <td style="color:red"><?= formatFloatToText($transaction['amount']) ?></td>
                        <?php endif; ?>
                    </tr>
                <?php } ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">Total Income:</th>
                <td><?php echo formatFloatToText($totals['income']) ?></td>
            </tr>
            <tr>
                <th colspan="3">Total Expense:</th>
                <td><?php echo formatFloatToText($totals['expense']) ?></td>
            </tr>
            <tr>
                <th colspan="3">Net Total:</th>
                <td><?php echo formatFloatToText($totals['netTotal']) ?></td>
            </tr>
        </tfoot>
    </table>
</body>

</html>