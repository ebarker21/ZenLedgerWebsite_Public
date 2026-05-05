<?php
session_start();
$dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
or die('Could not connect: ' . pg_last_error());
$selected_customer = $_SESSION['selected_customer'];
$query = "UPDATE customer_info SET total_sales = total_sales + 1 WHERE customer_name = '$selected_customer' RETURNING total_sales";
$result = pg_query($dbconn, $query);
$result_row = pg_fetch_row($result,0,PGSQL_ASSOC);
$sales = $result_row['total_sales'];

echo (json_encode("$sales"));
?>