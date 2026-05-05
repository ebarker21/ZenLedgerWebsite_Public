<?php
// Get Annual Credit Sales (assuming all Sales are on credit)
$query = "SELECT total_balance FROM chart_of_accounts WHERE account_category = 'Revenue' AND account_subcategory = 'Sales' AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
if (!$result || pg_num_rows($result) == 0) {
    echo "NA";
    return;
}

$credit_sales = 0;
while ($row = pg_fetch_row($result)) {
    $credit_sales += $row[0];
}

// Get Accounts Receivable (using account_subcategory = 'Credit' as a proxy)
$query = "SELECT total_balance FROM chart_of_accounts WHERE account_name = 'Accounts Receivable' AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
if (!$result || pg_num_rows($result) == 0) {
    echo "NA";
    return;
}

$accounts_receivable = 0;
while ($row = pg_fetch_row($result)) {
    $accounts_receivable += $row[0];
}

// Calculate Accounts Receivable Turnover Ratio
if ($accounts_receivable == 0) {
    echo "NA";
    $box_class19 = "";
} else {
    $art = $credit_sales / $accounts_receivable;
    $ratio_percentage = $art;
    $art_display = (number_format($art, 2));
    if ($ratio_percentage >=5) {
        $box_class19 = "green"; 

    } else {
        $box_class19 = "yellow";
    }
}
?>