<?php

// Get Inventory
$query = "SELECT total_balance FROM chart_of_accounts 
          WHERE account_subcategory = 'Inventory' 
          AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
$inventory = 0;
while ($row = pg_fetch_row($result)) {
    $inventory += $row[0];
}

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

// Calculate Net Working Capital
$net_working_capital = $current_assets - $current_liabilities;

// Avoid division by zero or negative NWC
if ($net_working_capital <= 0) {
    echo "NA";
    $box_class10 = "";
} else {
    $inventory_to_nwc = $inventory / $net_working_capital;
    $ratio_percentage = $inventory_to_nwc;
    $nwc_display = (number_format($inventory_to_nwc, 2));
    if ($ratio_percentage >= 1) {
        $box_class10 = "green"; 
    } 
	else
	
        $box_class10 = "yellow";
    }



?>
