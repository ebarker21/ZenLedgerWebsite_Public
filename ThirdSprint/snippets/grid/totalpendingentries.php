<?php
session_start();

$query = "SELECT post_reference FROM journal_entries WHERE is_approved = 'false' AND ( is_rejected is null or is_rejected is false ) AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
$pending_entries = 0;
while ($row = pg_fetch_row($result)) {
    $pending_entries+= 1;
}
echo $pending_entries;
?>
