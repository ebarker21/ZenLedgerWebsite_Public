<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

//this part gets the customer and the accountant
$selectedCustomer = $_SESSION['selected_customer'] ?? null;

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if (empty($_SESSION['selected_customer'])) {
    header("Location: index.php");
    exit();
}
include("snippets/cosmic-message.php");

//year drop down
$selectedYear = $_GET['year'] ?? date("Y");

//populates the table from chart of accounts and gets the year from journal_entries
$dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
or die('Could not connect: ' . pg_last_error());

$query = "
    SELECT chart_of_accounts.account_name, chart_of_accounts.account_category, SUM(subentry.amount * CASE WHEN subentry.is_crediting THEN -1 ELSE 1 END) AS total_balance
    FROM chart_of_accounts chart_of_accounts
    JOIN journal_entries journal_entries ON journal_entries.customer_name = chart_of_accounts.customer_name
    JOIN LATERAL unnest(journal_entries.subentries) AS subentry ON subentry.account_id = chart_of_accounts.account_id
    WHERE chart_of_accounts.account_category IN ('Revenue', 'Expense')
      AND EXTRACT(YEAR FROM journal_entries.date) = $1
      AND chart_of_accounts.customer_name = $2
    GROUP BY chart_of_accounts.account_name, chart_of_accounts.account_category
";

$result = pg_query_params($dbconn, $query, [$selectedYear, $selectedCustomer]);

if(!$result){
    die("Failure: " .pg_last_error());
}

$accounts = [];
$netIncome = 0;


while ($row = pg_fetch_assoc($result)) {
    $accounts[] = $row;
    if ($row['account_category'] === 'Revenue'){
        $netIncome += $row['total_balance'];
    } elseif ($row['account_category'] === 'Expense') {
        $netIncome += $row['total_balance'];
    }
}

//this tax rate is completely made up
$taxRate = 0.045;
$taxes = $netIncome * $taxRate;
$afterTax = $netIncome - $taxes;

//this gets the retained earnings from the previous year

$previousYear = $selectedYear - 1;
$priorQuery = "
    SELECT end_retained_earnings
    FROM retained_earnings
    WHERE customer_name = $1 AND year = $2
";

$result = pg_query_params($dbconn, $priorQuery, [$selectedCustomer, $previousYear]);
$earnings = 0;

if ($result && pg_num_rows($result) > 0){
    $priorRow = pg_fetch_assoc($result);
    $earnings = $priorRow['end_retained_earnings'];
}
$endRetainedEarnings = $earnings + $afterTax; 
//this part puts the calculated information into the retained_earnings table

pg_query_params($dbconn, "


INSERT INTO retained_earnings (customer_name, year, net_income, end_retained_earnings) 
VALUES ($1, $2, $3, $4)
ON CONFLICT (customer_name, year) DO UPDATE
SET net_income = EXCLUDED.net_income,
end_retained_earnings = EXCLUDED.end_retained_earnings
", [$selectedCustomer, $selectedYear, $netIncome, $endRetainedEarnings]);

?>


<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <link href="style/nonregisterstyle.css" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ZenLedger - Retained Earnings</title>
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
    <h1>Retained Earnings</h1>
    <p class="cosmic-message"><?php echo $cosmic_message; ?></p>
    </div>
    <hr>

    <div class="helper">
                  <img src="images/zenledger logo.png" class="background-logo" />
    </div>
<form method="GET" action="">
    <div style="text-align: center; margin: auto 0;">
    <label for="year">Select year: </label>
    <select name="year" id="year">
        <?php $currentYear = date("Y");
        for ($y = $currentYear; $y >= 2015; $y--){
            $selected = (isset($_GET['year']) && $_GET['year'] == $y) ? 'selected' : '';
            echo "<option value=\"$y\" $selected>$y</option>";
        }
?>
</select>
<input type="submit" value="Filter">
    </div>
    </form>

<?php

$retainedEarnings = $endRetainedEarnings;

echo "<div class='print-background'>";
echo "<div style='text-align: center; margin-bottom: 25px;'>";
echo "<p>Statement of Retained Earnings</p>";
echo "for the year ending $selectedYear";
echo "</div>";
echo "<table>";


$grouped = ['Revenue' => [], 'Expense' => []];
foreach ($accounts as $account) {
    $grouped[$account['account_category']][] = $account; }

    foreach (['Revenue', 'Expense'] as $category) {
        echo "<tr class='account-title'><td>$category</td><td></td></tr>";
        echo "<tr><td colspan='2' style='height: 5px; border: none;'></td></tr>";


        foreach ($grouped[$category] as $account){
            $accountName = htmlspecialchars($account['account_name']);
            $amount = number_format($account['total_balance'], 2);
            echo "<tr ><td style='border-bottom: 1px solid #ddd;'>$accountName</td><td style='border-bottom: 1px solid #ddd;'>\$$amount</td></tr>";
            echo "<tr><td colspan='2' style='height: 10px; border: none;'></td></tr>";
        }
    }
    echo "<tr><td colspan='2' style='height: 15px; border: none;'></td></tr>";
    echo "<tr class='account-title'><td style='padding-left: 1em;'>Previous Year's Retained Earnings:</td><td>\$" .number_format($earnings ?? 0, 2). "*</td></tr>";
    echo "<tr><td colspan='2' style='height: 15px; border: none;'></td></tr>";
    
    echo "<tr class='account-title'><td>Net Income:</td><td>\$" .number_format($netIncome, 2) . "</td></tr>";
    echo "<tr><td colspan='2' style='height: 15px; border: none;'></td></tr>";
    echo "<tr class='account-title'><td>Less Taxes (4.5%):</td><td>\$" .number_format($taxes, 2) . "</td></tr>";
    echo "<tr><td colspan='2' style='height: 15px; border: none;'></td></tr>";
    echo "<tr class='account-title'><td>Retained Earnings:</td><td>\$" .number_format($endRetainedEarnings, 2) . "</td></tr>";



 echo "</table>";
 echo "</div>";








include('snippets/print.php'); ?>

</p>*Because ZenLedger version 2.0 supports adding journal entries in any year, each year's retained earnings page must be manually updated to display the current retained earnings. Please navigate to the earliest year with data and request retained earnings statements for each year up to the current year for updated results.</p>
</body>
</html>