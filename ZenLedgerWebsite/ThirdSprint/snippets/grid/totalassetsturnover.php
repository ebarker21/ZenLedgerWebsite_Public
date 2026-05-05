<?php

$query = "SELECT total_balance FROM chart_of_accounts WHERE account_subcategory = 'Sales' AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
$sales = 0;
while ($row = pg_fetch_row($result)) {
    $sales += $row[0];
}
if ($sales == 0) {
    echo "NA";
    // stop here
}
else {
    // keep going
    $query = "SELECT total_balance FROM chart_of_accounts WHERE account_category = 'Assets' AND customer_name = '$selected_customer'";
    $assets = 0;
    $result = pg_query($dbconn, $query);
    while ($row = pg_fetch_row($result)) {
        $assets += $row[0];
    }
    if ($assets == 0) {
        echo "NA";
        $box_class18 = "";
    } else {
            $tat = $sales/$assets;
        $ratio_percentage = $tat;
        $tat_display = (number_format($tat, 2));
        if ($ratio_percentage >=.25) {
            $box_class18 = "green"; 
    
        } else {
            $box_class18 = "yellow";
        }
    }
}
?>
