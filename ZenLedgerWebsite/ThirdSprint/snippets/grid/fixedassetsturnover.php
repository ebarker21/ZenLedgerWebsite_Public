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
    $query = "SELECT total_balance FROM chart_of_accounts WHERE account_subcategory = 'Fixed' AND customer_name = '$selected_customer'";
    $fixed = 0;
    $result = pg_query($dbconn, $query);
    while ($row = pg_fetch_row($result)) {
        $fixed += $row[0];
    }
    if ($fixed == 0) {
        echo "NA";
        $box_class17 = "";
    } else {
            $fat = $sales/$fixed;
        $ratio_percentage = $fat;
        $fat_display = (number_format($fat, 2));
        if ($ratio_percentage >=2) {
            $box_class17 = "green"; 
    
        } else {
            $box_class17 = "yellow";
        }
    }
}
?>
