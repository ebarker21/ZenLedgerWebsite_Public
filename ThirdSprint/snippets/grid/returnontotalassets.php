<?php

$query = "SELECT total_balance FROM chart_of_accounts WHERE account_subcategory = 'Debt' AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
$total_debt = 0;
while ($row = pg_fetch_row($result)) {
    $total_debt += $row[0];
}


$query = "SELECT total_balance FROM chart_of_accounts WHERE account_category = 'Assets' AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
$total_assets = 0;
while ($row = pg_fetch_row($result)) {
    $total_assets += $row[0];
}


if ($total_assets == 0) {
    echo "NA";
    $box_class4 = "";
} else {
        $totalass = $total_debt/$total_assets;
    $ratio_percentage = $totalass * 100;
    $totalass_display = (number_format($totalass, 2)."%");

    if ($ratio_percentage <= 5) {
        $box_class4 = "yellow"; 
    } else {
        $box_class4 = "green";
    }
}


?>