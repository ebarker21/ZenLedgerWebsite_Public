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
    $tax = $profit *0.45; //random amount
    $profit = $profit - $tax;
    // keep going
    $query = "SELECT total_balance FROM chart_of_accounts WHERE account_subcategory = 'Stock' AND account_category = 'Equity' AND customer_name = '$selected_customer'";
    $result = pg_query($dbconn, $query);
    $stock = 0;
    while ($row = pg_fetch_row($result)) {
        $stock += $row[0];
    }

    if ($stock == 0) {
        echo "NA";
        $box_class6 = "";
    } else {
            $rce = $profit / ($stock + 0.00001);
        $ratio_percentage = $rce;
        $rce_display = (number_format($rce, 2)."%");
    
        if ($ratio_percentage <= 5) {
            $box_class6 = "red"; 
        } else if ($ratio_percentage > 5 && $ratio_percentage <=15){
        $box_class6 = "yellow";}
        else
        
            $box_class6 = "green";
        }
    }
?>
