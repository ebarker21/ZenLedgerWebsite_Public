<?php

$query = "SELECT total_balance FROM chart_of_accounts WHERE (account_subcategory = 'Debt' OR account_subcategory = 'Long-Term Debt') AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
$debt = 0;
while ($row = pg_fetch_row($result)) {
    $debt += $row[0];
}
if ($debt == 0) {
    echo "NA";
    // stop here
}
else {
    // keep going
    $query = "SELECT total_balance FROM chart_of_accounts WHERE account_category = 'Assets' AND customer_name = '$selected_customer'";
    $result = pg_query($dbconn, $query);
    $assets = 0;
    while ($row = pg_fetch_row($result)) {
        $assets += $row[0];
    }
    if ($assets == 0) {
        echo "NA";
        $box_class11 = "";
    } else {
    $dta = $debt/$assets;
   
        $ratio_percentage = $dta;
        $dta_display = (number_format($dta, 2)."%");
        if ($ratio_percentage <= 30) {
            $box_class11 = "green"; 
        } 
        else
        
            $box_class11 = "yellow";
        }
    }
    

?>
