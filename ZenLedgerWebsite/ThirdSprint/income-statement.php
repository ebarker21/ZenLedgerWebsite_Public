<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if (empty($_SESSION['selected_customer'])) {
    header("Location: index.php");
    exit();
}
include("snippets/cosmic-message.php");
?>


<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <link href="style/nonregisterstyle.css" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ZenLedger - Income Statement</title>
    <style>
        table {
            border-collapse: collapse;
            width: 75%;
            margin: 0 auto;
        }
        th, td {


            text-align: left;
            /* border-bottom: 1px solid #ddd; */
        }
    </style>
</head>
<body>
    <main>
    <?php include('snippets/logged-in-top-bar.php'); ?>
    <div class="cosmic-container">
    <h1>Income Statement</h1>
    <p class="cosmic-message"><?php echo $cosmic_message; ?></p>
    </div>
    <hr>

    <div class="helper">
                  <img src="images/zenledger logo.png" class="background-logo" />
    </div>

<?php
    $conn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require");

    $selected_customer = $_SESSION['selected_customer'];


    $result = pg_query($conn, "
        SELECT account_name, account_category, total_balance
        FROM chart_of_accounts
        WHERE customer_name = '$selected_customer'
        ORDER BY account_category DESC, account_name ASC
    ");

    $revenues = [];
    $expenses = [];
    $total_revenue = 0;
    $total_expense = 0;

    while ($row = pg_fetch_assoc($result)) {
        $category = $row['account_category'];

        $balance = floatval($row['total_balance']);
        $account_name = $row['account_name'];
    


    if ($category === 'Revenue') {
        
        $revenues[] = ['name' => $account_name, 'amount' => $balance];
        $total_revenue += $balance;
    
    } elseif ($category === 'Expense') {

        $expenses[] = ['name' => $account_name, 'amount' => $balance];
        $total_expense += $balance;
    }
    }

    $net_income_before_taxes = $total_revenue - $total_expense;
    $less_taxes = 0;
    

    //this tax rate is completely made up
$taxRate = 0.045;
$less_taxes = $net_income_before_taxes * $taxRate;
$net_income = $net_income_before_taxes - $less_taxes;

    ?>

<div class="print-background">

<?php
$selected_year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$end_of_year = new DateTime("December 31, $selected_year");

echo "<p style='text-align: center; margin-bottom: 15px;'>Income Statement</p>";
echo "<p style='text-align: center; margin-bottom: 15px;'>For the year ending " . $end_of_year->format("F j, Y") . "</p>";
?>

<table>
    <tr><td colspan="2" class="bold-me" style="font-size: 1.25em;">Revenues</td></tr>
    <tr><td colspan="2" style="height: 10px; border: none;"></td></tr>
    <?php 
    foreach ($revenues as $rev) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($rev['name']) . "</td>";
        echo "<td>$" . number_format($rev['amount'], 2) . "</td>";
        echo "</tr>";
    }
    ?>


    <tr class="bold-me">
        <td>Total Revenues:</td>
        <td>$<?= number_format($total_revenue, 2) ?></td>
    </tr>

    <tr><td colspan="2" style="height: 20px; border: none;"></td></tr>
    <tr><td colspan="2" style="height: 20px; border: none;"></td></tr>
    <tr><td colspan="2" class="bold-me" style="font-size: 1.25em;">Expenses</td></tr>
    <tr><td colspan="2" style="height: 10px; border: none;"></td></tr>
    <?php 
    foreach ($expenses as $exp) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($exp['name']) . "</td>";
        echo "<td>$" . number_format($exp['amount'], 2) . "</td>";
        echo "</tr>";
    }
    ?>
    <tr class="bold-me">
        <td>Total Expenses:</td>
        <td>$<?= number_format($total_expense, 2) ?></td>
    </tr>
    <tr><td colspan="2" style="height: 20px; border: none;"></td></tr>


    <tr><td colspan="2" style="height: 20px; border: none;"></td></tr>
    <tr class="bold-me">
        <td>Net Income Before Taxes:</td>
        <td>$<?= number_format($net_income_before_taxes, 2) ?></td>
    </tr>

    <tr><td colspan="2" style="height: 20px; border: none;"></td></tr>
    <tr class="bold-me">
        <td>Less Taxes (4.5%):</td>
        <td>($<?= number_format($less_taxes, 2) ?>)</td>
    </tr>

    <tr><td colspan="2" style="height: 20px; border: none;"></td></tr>
    <tr class="bold-me" style="font-size: 1.25em;">
        <td>Net Income:</td>
        <td>$<?= number_format($net_income, 2) ?></td>
    </tr>
</table>
</div>

    <?php include('snippets/print.php'); ?>

</body>
</html>