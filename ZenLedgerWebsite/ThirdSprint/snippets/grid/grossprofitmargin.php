<?php
$query = "SELECT total_balance FROM chart_of_accounts WHERE account_subcategory = 'Sales' AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
$sales = 0;
while ($row = pg_fetch_row($result)) {
    $sales += $row[0];
}

$query = "SELECT total_balance FROM chart_of_accounts WHERE account_subcategory = 'Goods' AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
$gs = 0;
while ($row = pg_fetch_row($result)) {
    $gs += $row[0];
}

if ($sales != 0 && $gs != 0) {
    $gpm = (($sales - $gs) / $sales); 
    $gpm_percentage = $gpm * 100;
    $gpm_display = number_format($gpm_percentage, 0) . "%";

    if ($gpm_percentage < 30) {
        $box_class1 = "red"; 
    } elseif ($gpm_percentage <= 40 && $gpm_percentage >= 30) {
        $box_class1 = "yellow";
    } else {
        $box_class1 = "green";
    }
} else {
    $gpm_display = "NA";
    $box_class1 = "";
}
?>
