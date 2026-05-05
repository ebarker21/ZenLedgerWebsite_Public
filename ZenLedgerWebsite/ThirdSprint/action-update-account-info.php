<?php session_start();

include("snippets/project-utils.php");

$dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
or die('Could not connect: ' . pg_last_error());

$number = $_POST["number"];
$query_old = "select * from chart_of_accounts where account_id='$number'";
$result_old = pg_query($dbconn, $query_old) or die('Query failed: ' . pg_last_error());

$arr_old = pg_fetch_array($result_old, 0, PGSQL_ASSOC);
$name_old = pg_escape_string($arr_old['account_name']);
$description_old = pg_escape_string($arr_old['account_description']);
$account_category_old = pg_escape_string($arr_old['account_category']);
$account_subcategory_old = pg_escape_string($arr_old['account_subcategory']);
$order_old = pg_escape_string($arr_old['order']);
$statement_old = pg_escape_string($arr_old['statement']);
$comments_old = pg_escape_string($arr_old['comments']);

// haven't really added this to update info yet
$initial_balance = pg_escape_string($arr_old['initial_balance']);
$status = pg_escape_string($arr_old['is_activated']);

// collecting all the info from the form
$name = pg_escape_string($_POST["name"]);
$description = pg_escape_string($_POST["description"]);
$account_category = pg_escape_string($_POST["account-category"]);
$account_subcategory = pg_escape_string($_POST["account-subcategory"]);
$order = pg_escape_string($_POST["order"]);
$statement = pg_escape_string($_POST["statement"]);
$comments = pg_escape_string($_POST["comments"]);

$customer = pg_escape_string($_SESSION['selected_customer']);
$username = pg_escape_string(($_SESSION['username']));
$time = date("Y-m-d H:i:s");

$query_update = "update chart_of_accounts set account_name='$name', "
            ."account_description='$description', "
            ."account_subcategory='$account_subcategory', "
            ."account_category='$account_category', "
            ."\"order\"='$order', "
            ."statement='$statement', "
            ."comments='$comments' "
            ."where account_id ='$number';";


$result_update = pg_query($dbconn, $query_update) or die('Query failed: ' . pg_last_error());

$changelog_query = "INSERT INTO chart_of_accounts_changelog "
        ."(account_name_before, account_description_before, account_category_before, initial_balance_before, order_before, statement_before,"
        ."comments_before, is_activated_before, account_subcategory_before,"
        ."account_name_after, account_description_after, account_category_after, initial_balance_after, order_after, statement_after,"
        ."comments_after, is_activated_after, account_subcategory_after,"
        ." account_id, customer_name, employee_username, changelog_time) VALUES "
        ."('$name_old', '$description_old', '$account_category_old', $initial_balance, '$order_old', '$statement_old',"
        ."'$comments_old', '$status', '$account_subcategory_old',"
        ."'$name', '$description', '$account_category', $initial_balance, '$order', '$statement',"
        ."'$comments', '$status', '$account_subcategory',"
        ."$number, '$customer', '$username', '$time');";
$result_changelog = pg_query($dbconn, $changelog_query) or die('Query failed: ' . pg_last_error());

header("Location: accounts-details.php?number=$number");
die();
?>
