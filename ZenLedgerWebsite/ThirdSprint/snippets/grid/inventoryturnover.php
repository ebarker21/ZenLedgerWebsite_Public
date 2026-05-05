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
    $query = "SELECT total_balance FROM chart_of_accounts WHERE account_subcategory = 'Finished-Goods Inventory' AND customer_name = '$selected_customer'";
    $fgi = 0;
    $result = pg_query($dbconn, $query);
    while ($row = pg_fetch_row($result)) {
        $fgi += $row[0];
    }

    if ($fgi == 0) {
        echo "NA";
        $box_class16 = "";
    } else {
            $it = $sales/$fgi;
        $ratio_percentage = $it;
        $it_display = (number_format($it, 2));
        if ($ratio_percentage >=10) {
            $box_class16 = "green"; 
    } elseif ($ratio_percentage < 10 && $ratio_percentage >= 4) {
            $box_class16 = "yellow";
        } else {
            $box_class16 = "red";
        }
    }
}
?>
