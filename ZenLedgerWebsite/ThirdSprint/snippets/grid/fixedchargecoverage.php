<?php
// Get Revenue
$query = "SELECT total_balance FROM chart_of_accounts WHERE account_category = 'Revenue' AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
if (!$result || pg_num_rows($result) == 0) {
    echo "NA";
    return;
}

$revenue = 0;
while ($row = pg_fetch_row($result)) {
    $revenue += $row[0];
}

// Get Expenses (excluding interest)
$query = "SELECT total_balance FROM chart_of_accounts WHERE account_category = 'Expense' AND account_subcategory != 'Interest-Bearing' AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
if (!$result || pg_num_rows($result) == 0) {
    echo "NA";
    return;
}

$expenses = 0;
while ($row = pg_fetch_row($result)) {
    $expenses += $row[0];
}

// Calculate Profits Before Interest and Taxes (PBIT)
$pbit = $revenue - $expenses;

// Get Lease Obligations
$query = "SELECT total_balance FROM chart_of_accounts WHERE account_subcategory = 'Lease' AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
if (!$result) {
    echo "NA";
    return;
}

$lease_obligations = 0;
while ($row = pg_fetch_row($result)) {
    $lease_obligations += $row[0];
}

// Get Interest Charges
$query = "SELECT total_balance FROM chart_of_accounts WHERE account_subcategory = 'Interest-Bearing' AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
if (!$result || pg_num_rows($result) == 0) {
    echo "NA";
    return;
}

$interest = 0;
while ($row = pg_fetch_row($result)) {
    $interest += $row[0];
}

// Calculate Fixed-Charge Coverage Ratio
$denominator = $interest + $lease_obligations;
if ($denominator == 0) {
    echo "NA";
    $box_class15 = "";
} else {
    $fcc = ($pbit + $lease_obligations) / $denominator;
    $ratio_percentage = $fcc;
    $fcc_display = (number_format($fcc, 2));
    if ($ratio_percentage >=1.2) {
        $box_class15 = "green"; 
} elseif ($ratio_percentage < 1.2 && $ratio_percentage >= .5) {
        $box_class15 = "yellow";
    } else {
        $box_class15 = "red";
    }
}
?>
