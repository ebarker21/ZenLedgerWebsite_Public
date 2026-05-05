  <?php
// written by Alexis McGruder, moved into a snippet by Francisco Torres
$query = "SELECT total_balance FROM chart_of_accounts WHERE account_name = 'Accounts Receivable' AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
$ar = 0;
while ($row = pg_fetch_row($result)) {
    $ar += $row[0];
}
if ($ar == 0) {
    echo "NA";
    // stop here
}
else {
    $query = "SELECT * FROM customer_info WHERE customer_name = '$selected_customer'";
    $result = pg_query($dbconn, $query);
    $result_row = pg_fetch_row($result,0,PGSQL_ASSOC);
    $sales = $result_row['total_sales'];
    $acp =number_format ($ar/($sales/365),0);
    echo($acp. " days");
}  
?>
