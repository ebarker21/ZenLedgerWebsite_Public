<?php

// Get Quick Assets
$query = "SELECT total_balance FROM chart_of_accounts 
          WHERE account_subcategory IN ('Cash', 'Accounts Receivable', 'Marketable Securities') 
          AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
$quick_assets = 0;
while ($row = pg_fetch_row($result)) {
    $quick_assets += $row[0];
}

// Get Current Liabilities
$query = "SELECT total_balance FROM chart_of_accounts WHERE account_subcategory = 'Current Liability' AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
$current_liabilities = 0;
while ($row = pg_fetch_row($result)) {
    $current_liabilities += $row[0];
}

if ($current_liabilities == 0) {
    echo "NA";
    $box_class9 = "";
} else {
     $quick_ratio = $quick_assets / $current_liabilities;
    $ratio_percentage = $quick_ratio;
    $qu_display = (number_format($quick_ratio, 2));
    if ($ratio_percentage >= 1) {
        $box_class9 = "green"; 
    } 
	else
	
        $box_class9 = "red";
    }



?>
