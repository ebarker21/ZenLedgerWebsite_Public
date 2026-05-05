<?php
// Get Revenue
$query = "SELECT total_balance FROM chart_of_accounts WHERE account_category = 'Revenue' AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
if (!$result) {
    echo "NA (Revenue query failed)";
    return;
}

// Check if Revenue accounts exist
if (pg_num_rows($result) == 0) {
    echo "NA (No Revenue accounts)";
    return;
}

$revenue = 0;
while ($row = pg_fetch_row($result)) {
    $revenue += $row[0];
}

// Get Expenses (excluding interest)
$query = "SELECT total_balance FROM chart_of_accounts WHERE account_category = 'Expense' AND account_subcategory != 'Interest-Bearing' AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
if (!$result) {
    echo "NA (Expense query failed)";
    return;
}

// Check if Expense accounts exist (excluding interest)
if (pg_num_rows($result) == 0) {
    echo "NA (No non-interest Expense accounts)";
    return;
}

$expenses = 0;
while ($row = pg_fetch_row($result)) {
    $expenses += $row[0];
}

// Calculate Profits Before Interest and Taxes
$pbit = $revenue - $expenses;

// Get Total Interest Charges
$query = "SELECT total_balance FROM chart_of_accounts WHERE account_subcategory = 'Interest-Bearing' AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
if (!$result) {
    echo "NA (Interest-Bearing query failed)";
    return;
}

// Check if Interest-Bearing accounts exist
if (pg_num_rows($result) == 0) {
    echo "NA (No Interest-Bearing accounts)";
    return;
}

// Process the interest
$interest = 0;
while ($row = pg_fetch_row($result)) {
    $interest += $row[0];
}

// Calculate Times-Interest-Earned Ratio
if ($interest == 0) {
    echo "NA";
    $box_class14 = "";
} else {
    $tie = $pbit / $interest;
    $ratio_percentage = $tie;
    $tie_display = (number_format($tie, 2));
    if ($ratio_percentage >=2.5) {
        $box_class14 = "green"; 
} elseif ($ratio_percentage < 2.5 && $ratio_percentage >= 2.0) {
        $box_class14 = "yellow";
    } else {
        $box_class14 = "yellow";
    }
}
?>
