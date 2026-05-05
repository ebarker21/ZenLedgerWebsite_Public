<?php

// Get Current Assets
$query = "SELECT total_balance FROM chart_of_accounts WHERE account_subcategory = 'Current Asset' AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
$current_assets = 0;
while ($row = pg_fetch_row($result)) {
    $current_assets += $row[0];
}

// Get Current Liabilities
$query = "SELECT total_balance FROM chart_of_accounts WHERE account_subcategory = 'Current Liability' AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
$current_liabilities = 0;
while ($row = pg_fetch_row($result)) {
    $current_liabilities += $row[0];
}

// Avoid division by zero

if ($current_liabilities == 0) {
    echo "NA";
    $box_class8 = "";
} else {
    $curr = $current_assets / $current_liabilities;
    $ratio_percentage = $curr;
    $curr_display = (number_format($curr, 2)."%");

    if ($ratio_percentage <= 1) {
        $box_class8 = "red"; 
    } 
	else
	
        $box_class8 = "green";
    
}

?>
