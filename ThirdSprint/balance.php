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
        <title>ZenLedger - Balance Sheet</title>
        <style>
            table {
                width: 75%;
                border-collapse: collapse;
                margin: 0 auto;
            }

            th, td {
                padding: 10px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }

        </style>
    </head>
    <body>
        <main>
        <?php include('snippets/logged-in-top-bar.php'); ?>
        <div class="cosmic-container">
    <h1>Balance Sheet</h1>
    <p class="cosmic-message"><?php echo $cosmic_message; ?></p>
</div>
        <hr>
        <div class="helper">
                  <img src="images/zenledger logo.png" class="background-logo" />
              </div>

<?php
    $conn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require");

    if (!$conn) {
        die("<p>Connection failed: " . pg_last_error() . "</p>");
    }

    $selected_account = $_GET['account_name'] ?? '';
    $selected_customer = $_SESSION['selected_customer'];


$params = [];
$query = "SELECT account_name, 
account_category, 
total_balance 
FROM chart_of_accounts
WHERE customer_name = $1";

if (!empty($selected_account)) {
    $query .= " AND account_name = $2";
    $params[] = $selected_customer;
    $params[] = $selected_account;
}
else
$params[] = $selected_customer;

$query .= " ORDER BY account_category";


$result = pg_query_params($conn, $query, $params);

if (!$result) {
    die("<p>Query failed: " . pg_last_error() . "</p>");
}

$grouped_accounts = [];

while ($row = pg_fetch_assoc($result)) {
    $category = strtolower($row['account_category']);
    $amount = $row['total_balance'];


    if (!isset($grouped_accounts[$category])) {
        $grouped_accounts[$category] = [];
    }

    $grouped_accounts[$category][$row['account_name']] = $amount;
}
    
function print_section($title, $accounts) {
    $total = 0;
    echo "<tr><td class='account-title'><strong>$title</strong></td><td><strong>Amount</strong></td></tr>";
    foreach ($accounts as $account => $amount) {
        echo "<tr>
                <td>" . htmlspecialchars($account) . "</td>
                <td>" . number_format($amount, 2) . "</td>
              </tr>";
        $total += $amount;
    }
    echo "<tr>    
    <tr><td colspan='2' style='height: 3px; border: none;'></td></tr>
    <th>Total $title</th><th>" . number_format($total, 2) . "</th></tr>
    <tr><td colspan='2' style='height: 10px; border: none;'></td></tr>";
}





echo "<div class='print-background'>";

echo "<div style='text-align: center; margin-bottom: 15px;'>";
echo "<p>Balance Sheet</p>";
echo "<p>" . date("F j, Y") . "</p>";
echo "</div>";

echo "<table>";

foreach ($grouped_accounts as $category => $accounts){
    print_section(ucwords($category), $accounts);
}


echo "</table>";
echo "</div>";
?>
        <?php include('snippets/print.php'); ?>
        </main>
    </body>
</html>
