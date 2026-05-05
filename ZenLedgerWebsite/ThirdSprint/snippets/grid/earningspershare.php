<?php

$query = "SELECT total_balance FROM chart_of_accounts WHERE account_category = 'Revenue' AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
$rev = 0;
while ($row = pg_fetch_row($result)) {
    $rev += $row[0];
}
if ($rev == 0) {
    echo "NA";
    // stop here
}
else {
    $query = "SELECT total_balance FROM chart_of_accounts WHERE account_category = 'Expense' AND customer_name = '$selected_customer'";
    $result = pg_query($dbconn, $query);
    $expense = 0;
    while ($row = pg_fetch_row($result)) {
        $expense += $row[0];
    }
    $profit = $rev - $expense;

    $profit = $profit - $tax;
    // keep going
    $query = "SELECT total_balance FROM chart_of_accounts WHERE account_subcategory = 'Stock' AND account_category = 'Equity' AND customer_name = '$selected_customer'";
    $result = pg_query($dbconn, $query);

    $stock = pg_num_rows($result);

    if ($stock == 0) {
        echo "NA";
        $box_class7 = "";
    } else {
            $eps = $profit / ($stock);
        $ratio_percentage = $eps;
        $eps_display = (number_format($eps, 2)."%");
    
        if ($ratio_percentage <= 15) {
            $box_class5 = "red"; 
        } else if ($ratio_percentage > 15 && $ratio_percentage <=25){
        $box_class7 = "yellow";}
        else
        
            $box_class7 = "green";
        }
    }
?>
