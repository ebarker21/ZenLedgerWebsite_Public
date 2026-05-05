<?php
session_start();
$dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require") or die('Could not connect: ' . pg_last_error());
$selected_customer =  pg_escape_string($_SESSION['selected_customer']);
$query = "delete from journal_entries WHERE is_approved = 'false' AND ( is_rejected is null or is_rejected is false ) AND customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);

header("Location: journal-approve.php");
?>
