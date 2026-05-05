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
    <title>ZenLedger - Trial Balance</title>
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
    <h1>Trial Balance</h1>
    <p class="cosmic-message"><?php echo $cosmic_message; ?></p>
</div>
<hr>

<div class="helper">
                  <img src="images/zenledger logo.png" class="background-logo" />
              </div>

<?php
    $dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
        or die('Could not connect: ' . pg_last_error());

    $selected_account = $_GET['account_name'] ?? '';

    $account_names_query = "SELECT DISTINCT account_name FROM chart_of_accounts ORDER BY account_name";
    $account_names_result = pg_query($dbconn, $account_names_query);





$selected_customer = $_SESSION['selected_customer'];

    $params = [$selected_customer];
    $param_index = 2;
    $query = "SELECT account_name, 
    total_debit, 
    total_credit 
    FROM chart_of_accounts
    WHERE 
    customer_name = $1";

    if (!empty($selected_account)) {
        $query .= " AND account_name = $" . $param_index;
        $params[] = $selected_account;
        $param_index++;
    }

    $query .= " ORDER BY account_name";

    $result = pg_query_params($dbconn, $query, $params);


    if (!$result) {
        echo "<p>Error executing query: " . pg_last_error($dbconn) . "</p>";
        exit;
    }

    echo "<div class='print-background'>";

    echo "<div style='text-align: center; margin: 20px 0;'>";
    echo "<p>Trial Balance</p>";
    echo "<p>" . date("F j, Y") . "</p>";
    echo "</div>";

    echo "<table>
    <tr class='bold-me'>
    <td>Account</td>
    <td>Debit</td>
    <td>Credit</td>
</tr>";

$total_debit = 0;
$total_credit = 0;

    while ($row = pg_fetch_assoc($result)) {
        $account_name = $row['account_name'];
        $debit = floatval($row['total_debit']);
        $credit = floatval($row['total_credit']);

        echo "<tr>
        <td>{$account_name}</td>
        <td>" . number_format($debit, 2) . "</td>
        <td>" . number_format($credit, 2) . "</td>
    </tr>";




        $total_debit += $debit;
        $total_credit += $credit;
    }


            echo "<tr class='bold-me'>
            <td>Totals</td>
            <td>" . number_format($total_debit, 2) . "</td>
            <td>" . number_format($total_credit, 2) . "</td>
            </tr>";

           echo "</table>
            </div>";



            ?>  
<?php include('snippets/print.php'); ?>
</main>
</body>
</html>
