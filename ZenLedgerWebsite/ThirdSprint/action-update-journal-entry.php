<?php session_start();
    $dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
    or die('Could not connect: ' . pg_last_error());
    $data = json_decode(file_get_contents('php://input'), true);

    $post_reference = pg_escape_string($data['entry_id']);
    $is_approved_data = pg_escape_string($data['is_approved']);
    $form_date = $data['date_of_transaction'];
    $date_of_transaction = date($form_date);
    $description = pg_escape_string($data['description']);
    $comments = pg_escape_string($data['comments']);
    $customer = pg_escape_string($_SESSION['selected_customer']);
    $time = date("Y-m-d H:i:s");


    $dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
    or die('Could not connect: ' . pg_last_error());


    $is_approved = "FALSE";
    $is_app_b = false;
    if($is_approved_data == 't'){
        $is_approved = "TRUE";
        $is_app_b = true;
    }

    // get old info
    $old_query = "select * from journal_entries where post_reference='$post_reference'";
    $old_result = pg_query($dbconn, $old_query) or die('Query failed: ' . pg_last_error());
    $old_result_arr = pg_fetch_array($old_result, 0, PGSQL_ASSOC);
    $old_description = pg_escape_string($old_result_arr['description']);
    $old_comments = pg_escape_string($old_result_arr['comments']);
    $old_subentries = pg_escape_string($old_result_arr['subentries']);
    $old_date = pg_escape_string($old_result_arr['date']);

    $query_changelog = "insert into journal_entries_changelog (post_reference, date_before, customer_name, time_stamp,"
                          ."description_before, comments_before, is_approved_before, journal_subentry_before,"
                          ."description_after, comments_after, is_approved_after, date_after)"
                      ."values ('$post_reference', '$old_date', '$customer', '$time',"
                          ."'$old_description', '$old_comments', '$is_approved', '$old_subentries',"
                          ."'$description', '$comments', '$is_approved', '$date_of_transaction') returning entry_id";
    $result_changelog = pg_query($dbconn, $query_changelog) or die('Query failed: ' . pg_last_error());
    $entry_id_row = pg_fetch_row($result_changelog,null,PGSQL_NUM);
    $entry_id = pg_escape_string($entry_id_row[0]);

    $query_update = "update journal_entries set description = '$description', comments = '$comments', date = '$date_of_transaction'"
                      ." where post_reference='$post_reference'";
    $result_update = pg_query($dbconn, $query_update) or die('Query failed: ' . pg_last_error());
    // do subentries

    $query_clear_transactions = "update journal_entries set subentries = null where post_reference='$post_reference'";
    $result_clear_transactions = pg_query($dbconn, $query_clear_transactions) or die('Query failed: ' . pg_last_error());

    $debit_accounts = $data['debit_accounts'];
    $debit_values = $data['debit_values'];
    $debit_loop_size = count($debit_accounts);
    for($i = 0; $i < $debit_loop_size; $i++)
    {
        $query_add_transactions = "update journal_entries set subentries = subentries || '{\"";
        $query_add_transactions .= "(0, ".$debit_values[$i].", ".$debit_accounts[$i];
        $query_add_transactions .= ",";
        $query_add_transactions .= ")";
        $query_add_transactions .= "\"}' where post_reference='".$post_reference."'";
        error_log($query_add_transactions);
        $result_add_transactions = pg_query($dbconn, $query_add_transactions) or die('Query failed: ' . pg_last_error());
    }

    $credit_accounts = $data['credit_accounts'];
    $credit_values = $data['credit_values'];
    $credit_loop_size = count($credit_accounts);
    for($i = 0; $i < $credit_loop_size; $i++)
    {
        $query_add_transactions = "update journal_entries set subentries = subentries || '{\"";
        $query_add_transactions .= "(1, ".$credit_values[$i].", ".$credit_accounts[$i];
        $query_add_transactions .= ",";
        $query_add_transactions .= ")";
        $query_add_transactions .= "\"}' where post_reference='".$post_reference."' returning subentries";
        $result_add_transactions = pg_query($dbconn, $query_add_transactions) or die('Query failed: ' . pg_last_error());
    }
    $subentries_arr = pg_fetch_row($result_add_transactions,null,PGSQL_NUM);
    $subentries = pg_escape_string($subentries_arr[0]);
    
    // now update changelog again
    $query_changelog_subs_update = "update journal_entries_changelog set journal_subentry_after='$subentries' where entry_id='$entry_id'";
    $result_changelog_subs_update = pg_query($dbconn, $query_changelog_subs_update) or die('Query failed: ' . pg_last_error());
    if($is_app_b) {
        $reverse_subentries_query =" select (before).is_crediting as is_crediting, (before).amount as amount, (before).account_id as account_id from (select unnest(journal_subentry_before) as before from journal_entries_changelog where entry_id='$entry_id') ";
        $reverse_subentries_result = pg_query($dbconn, $reverse_subentries_query) or die('Query failed: ' . pg_last_error());
        while ($sub = pg_fetch_row($reverse_subentries_result, null, PGSQL_ASSOC))
        {
            if($sub['is_crediting'] == 'f'){
                $query_update_account = "update chart_of_accounts set total_debit = total_debit - ".$sub['amount'].", total_balance = total_balance + ".$sub['amount'];
                $query_update_account .= " where account_id=".$sub['account_id']." returning total_balance";
                $result_update_account = pg_query($dbconn, $query_update_account) or die('Query failed: ' . pg_last_error());
            }
            else {
                $query_update_account = "update chart_of_accounts set total_credit = total_credit - ".$sub['amount'].", total_balance = total_balance - ".$sub['amount'];
                $query_update_account .= " where account_id=".$sub['account_id']." returning total_balance";
                $result_update_account = pg_query($dbconn, $query_update_account) or die('Query failed: ' . pg_last_error());
            }
        }
        $reverse_subentries_query =" select (after).is_crediting as is_crediting, (after).amount as amount, (after).account_id as account_id from (select unnest(journal_subentry_after) as after from journal_entries_changelog where entry_id='$entry_id') ";
        $reverse_subentries_result = pg_query($dbconn, $reverse_subentries_query) or die('Query failed: ' . pg_last_error());
        while ($sub = pg_fetch_row($reverse_subentries_result, null, PGSQL_ASSOC)) 
        {
            if($sub['is_crediting'] == 'f'){
                $query_update_account = "update chart_of_accounts set total_debit = total_debit + ".$sub['amount'].", total_balance = total_balance - ".$sub['amount'];
                $query_update_account .= " where account_id=".$sub['account_id']." returning total_balance";
                $result_update_account = pg_query($dbconn, $query_update_account) or die('Query failed: ' . pg_last_error());
            }
            else {
                $query_update_account = "update chart_of_accounts set total_credit = total_credit + ".$sub['amount'].", total_balance = total_balance + ".$sub['amount'];
                $query_update_account .= " where account_id=".$sub['account_id']." returning total_balance";
                $result_update_account = pg_query($dbconn, $query_update_account) or die('Query failed: ' . pg_last_error());
            }
        }
    
    }
    echo json_encode($entry_id);
?>
