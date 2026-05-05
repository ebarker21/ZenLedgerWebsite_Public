<?php

$query = "SELECT total_balance FROM chart_of_accounts WHERE (account_subcategory = 'Debt' OR account_subcategory = 'Long-Term Debt') AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
$debt = 0;
while ($row = pg_fetch_row($result)) {
    $debt += $row[0];
}

$query = "SELECT total_balance FROM chart_of_accounts WHERE account_category = 'Equity' AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
$she = 0;
while ($row = pg_fetch_row($result)) {
    $she += $row[0];
}

if ($she != 0 && $debt != 0) {
    $debttoequity = ($debt/$she);
        $ratio_percentage = $debttoequity;
        $dte_display = (number_format($debttoequity, 2)."%");
        if ($ratio_percentage <= 1.50) {
            $box_class12 = "green"; 
        } 
        else
        
            $box_class12 = "yellow";
        
    
    } else {
        echo "NA";
        $box_class12 = "";
    
    }

?>