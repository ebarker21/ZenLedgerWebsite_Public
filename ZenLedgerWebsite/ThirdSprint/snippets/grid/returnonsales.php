<?php

$query = "SELECT total_balance FROM chart_of_accounts WHERE account_category = 'Revenue' AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
$rev = 0;
while ($row = pg_fetch_row($result)) {
    $rev += $row[0];
}

if ($rev == 0) {
    echo "NA";
    $box_class2 = "";
} else {
    $query = "SELECT total_balance FROM chart_of_accounts WHERE account_category = 'Expense' AND customer_name = '$selected_customer'";
    $result = pg_query($dbconn, $query);
    $expense = 0;
    while ($row = pg_fetch_row($result)) {
        $expense += $row[0];
    }

    $profit = $rev - $expense;

    $query = "SELECT total_balance FROM chart_of_accounts WHERE account_subcategory = 'Sales' AND customer_name = '$selected_customer'";
    $result = pg_query($dbconn, $query);
    $sales = 0;
    while ($row = pg_fetch_row($result)) {
        $sales += $row[0];
    }

    if ($sales == 0) {
        echo "NA";
        $box_class2 = "";
    } else {
        $ros = $profit / $sales;
        $ratio_percentage = $ros * 100;
        $ros_display = number_format($ratio_percentage, 2) . "%";

        if ($ratio_percentage <= 10) {
            $box_class2 = "yellow"; 
        } else {
            $box_class2 = "green";
        }
    }
}
?>