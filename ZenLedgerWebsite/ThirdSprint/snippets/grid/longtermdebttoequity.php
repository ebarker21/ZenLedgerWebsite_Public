<?php

$query = "SELECT total_balance FROM chart_of_accounts WHERE account_subcategory = 'Long-Term Debt'AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
$ltdebt = 0;
while ($row = pg_fetch_row($result)) {
    $ltdebt += $row[0];
}

$query = "SELECT total_balance FROM chart_of_accounts WHERE account_category = 'Equity' AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
$she = 0;
while ($row = pg_fetch_row($result)) {
    $she += $row[0];
}

if ($she != 0 && $debt != 0) {
    $debttoequity = ($ltdebt/$she);
        $ratio_percentage = $debttoequity;
        $ltdte_display = (number_format($debttoequity, 2)."%");
        if ($ratio_percentage <= 1.50) {
            $box_class13 = "green"; 
        } 
        else
        
            $box_class13 = "yellow";
        
    
    } else {
        echo "NA";
        $box_class13 = "";
    }

?>