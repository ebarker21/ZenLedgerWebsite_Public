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
    $tax = $profit *0.01; //random amount
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
        $box_class5 = "";
    } else {
           $rnw = $profit/$stock;
        $ratio_percentage = $rnw * 100;
        $rnw_display = (number_format($rnw, 2)."%");
    
        if ($rnw <= 5) {
            $box_class5 = "red";}
            elseif ($rnw >= 5 && $rnw < 10) {
                $box_class5 = "yellow";
            
        } else {
            $box_class5 = "green";
        }
        }    }
?>
