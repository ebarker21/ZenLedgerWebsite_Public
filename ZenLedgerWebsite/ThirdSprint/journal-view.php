<?php
session_start();

if (isset($_SESSION["username"])) {
    include("snippets/project-utils.php");

    if (empty($_SESSION['selected_customer'])) {
        header("Location: index.php");
        exit();
    }
    
} 
include("snippets/cosmic-message.php");

?>

<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <link href="style/nonregisterstyle.css" rel="stylesheet" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>ZenLedger - Journal View</title>
        <!-- NOTE(Art): In code, I go by art lol. Anyway, Susan, I said we should use flex
                        but I was incorrect. I think raw width may be easier to align!
                        I thought flex had this weight property, but like the name
                        implies, is more for being flexible to scenarios.
                        We are looking for something more firm like raw width!
        -->
        <!-- NOTE(Art): Let me document what's happening in these CSS boxes. In order of
                        their earliest appearance in the HTML below:
                        - our view-box class is using flex to center the table in the page, that is all.
                            - also note align items and justify content are needed for centering
                            - white background for the entire thing to give it a page like feel
                        - our table inside viewbox will be 95% wide for our screens
                            - collapse borders to have no spacing between elements, gives a spreadsheet feel
                        - the classes with a '_th' suffix (like date_col_th) are used to layout sizes via width
                            - For safety, I also made sure the corresponding class like (date_col) also shares
                              identical widths to ensure the table keeps the correct shape throughout.
                        - the rest is just border stuff, text alignments, and padding to emulate the 
                          look that was requested for this table.
        -->
        <style>
            .view-box {
                display: flex;
                background-color: white;
                align-items: center;
                justify-content: center;
            }
            .view-box > table {
                width:95%;
                border-collapse: collapse;
            }
            .title_row {
                border: 2px solid #4274a1;
            }
            .title_col {
                padding-top: 0.5em;
                text-align:left;
                padding-left: 1.5rem;
                text-transform:uppercase;
            }
            .view-box th {
                background-color: #9ebeda;
            }
            .date_col_th, .pr_col_th, .acc_col_th, .pad_col_th, .deb_col_th, .cred_col_th{ 
                padding-top: 0.5em;
                border: 2px solid #4274a1;
            }
            .date_col, .pr_col, .acc_col, .pad_col, .deb_col, .cred_col { 
                border: 2px solid lightgrey;
                padding-bottom: 0px;
            }

            .acc_col {
                padding-left: 0.5em;
            }
            .date_col {
                text-align: center;
            }

            .cred {
                padding-left: 4.0em;
            }

            .date_col, .date_col_th { width:10%; }
            .pr_col, .pr_col_th  { width:  4%; text-align:center;}
            .acc_col, .acc_col_th  { width:48%; }
            .pad_col, .pad_col_th  { width:2%; }
            .deb_col, .cred_col { text-align:right; padding-right: 0.4em;}
            .deb_col, .deb_col_th  { width:14%; }
            .cred_col, .cred_col_th { width:14%; }
            .view-box .standard_row {
                line-height:1.5em;
            }
        </style>
    </head>
    <body>
        <main>
        <?php include('snippets/logged-in-top-bar.php'); ?>
        <div class="cosmic-container">
    <h1>Journal</h1>
    <p class="cosmic-message"><?php echo $cosmic_message; ?></p>
</div>
        <hr>
        <!--
        <div class="helper">
                  <img src="images/zenledger logo.png" class="background-logo" />
              </div>
         -->

        <?php include("snippets/journal-tab-bar.php"); ?>

        <!-- Status filter, date range, and search form -->
        <form method="GET" style="margin-bottom: 20px;">
            <label>Show:</label>
            <select name="status">
                <option value="approved" <?php echo (!isset($_GET['status']) || $_GET['status'] === 'approved') ? 'selected' : ''; ?>>Approved</option>
                <option value="pending" <?php echo ($_GET['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
            </select>
            <label>Date Range:</label>
            <input type="date" name="start_date" value="<?php echo $_GET['start_date'] ?? ''; ?>">
            <input type="date" name="end_date" value="<?php echo $_GET['end_date'] ?? ''; ?>">
            <label>Search:</label>
            <input type="text" name="search" value="<?php echo $_GET['search'] ?? ''; ?>" placeholder="Post Reference, Amount, Date">
            <input type="submit" value="Apply">
        </form>

        <br><span style="font-size: 0.8em;">* All accounts viewable are approved. To manage accounts with other statuses, go to the 'Approve' tab. </span>


        <!-- NOTE(Art): Read comment in CSS in <head> before the following:
                        This is an explanation of the code below.
                        First, we are gonna just declare a box that will use flexbox to center.
                        Second, we then create our headings, which is made up of two rows just
                        like the styling of the example that we base our table upon.
        -->
        <div class="view-box">
                        <?php error_log("--- TABLE SEPERATOR ---"); ?>
            <table>
                <tr class="title_row">
                    <th class="title_col" colspan="6">General Journal</th>
                </tr>
                <tr>
                        <th class="date_col_th">Date</th>
                        <th class="pr_col_th">PR</th>
                        <th class="acc_col_th">Accounts</th>
                        <th class="pad_col_th"></th>
                        <th class="deb_col_th">Debits</th>
                        <th class="cred_col_th">Credits</th>
                </tr>

                <!-- NOTE(Art): Read comment in <div class="view-box"> before the following:
                                This is an explanation of the code below.
                                First, we connect to our database. 
                                Second, we do a very interesting SQL call.
                                    - Going from the inner parentheses and working out, we first select
                                      the necessary data for our table.
                                    - Additionally, in the call, we also grab our composite datatype array
                                      and unnest it. This creates a side effect in the sql call, where
                                      each subentry in an entry gets its own row
                                        - For each subentry row, all other journal details are identical.
                                          So if we have TWO subentries, we get a row for our journal entry,
                                          but the subentry will only contain a single subentry.
                                    - Then outside this inner parentheses, we select most of the data that was
                                      retrieved from inside. Except, our subentries, we grab the individual 
                                      pieces of the composite datatype because that's easier to handle in PHP.
                                    - Also An setup a bunch of filters using 'where' statements and some other
                                      SQL syntax that I'm less familiar with. Google that info I guess haha.
                                Third, we now get some rows where we loop through them and spit out some <tr>
                -->
                <?php 
                $dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
                    or die('Could not connect: ' . pg_last_error());

                // $query = "SELECT date, post_reference, unnest(subentries) FROM journal_entries";
                $query = "select date, post_reference, (sub).is_crediting, (sub).amount, (sub).account_id, description from (SELECT date, post_reference, is_approved, customer_name, description, unnest(subentries) as sub FROM journal_entries";
                $conditions = [];

                if (isset($_GET['status'])) {
                    if ($_GET['status'] === 'approved') {
                        $conditions[] = "is_approved = true";
                    } elseif ($_GET['status'] === 'pending') {
                        $conditions[] = "is_approved = false";
                        $conditions[] = "COALESCE (is_rejected, false) = false";
                    }
                } else {
                    $conditions[] = "is_approved = true"; 
                }

                if (!empty($_GET['start_date'])) {
                    $start_date = pg_escape_string($_GET['start_date']);
                    $conditions[] = "date >= '$start_date'";
                }
                if (!empty($_GET['end_date'])) {
                    $end_date = pg_escape_string($_GET['end_date']);
                    $conditions[] = "date <= '$end_date'";
                }

                if (!empty($_GET['search'])) {
                    $search = pg_escape_string($_GET['search']);
                    $conditions[] = "(post_reference ILIKE '%$search%' OR 
                        TO_CHAR(date, 'YYYY-MM-DD') ILIKE '%$search%' OR 
                        EXISTS (
                            SELECT 1 
                            FROM unnest(subentries) AS s 
                            WHERE CAST(s.amount AS text) ILIKE '%$search%'
                        )
                    )";
                }

                $customer = $_SESSION['selected_customer'];
                $query .= " WHERE customer_name='$customer'";
                if (!empty($conditions)) {
                    $query .= ' AND ' .implode(" AND ", $conditions);
                }

                // $query .= " ORDER BY post_reference COLLATE \"numeric\" desc;";
                $query .= " ORDER BY date desc, post_reference COLLATE \"numeric\" desc)";

                $result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());

                // NOTE(Art): Read comment above $db_conn =...  before the following:
                           // This is an explanation of the code below.
                           // DISCLAIMER: Comment variable is referring to 'description' column in journal_entry (my bad)
                           // So we declare some variables that will persist between while loops which are POST_REF and COMMENT
                               // - The post_ref will check when we have moved to rows that are referring to a new journal_entry,
                               //   we do this by checking the new post ref vs the old post ref. Obviously if they're different,
                               //   then the row, we are reading now is a new journal_entry!
                               //   This ambiguity in which journal_entry we're in is a sideffect of using 'unnest' in our sql call.
                               //
                               // - The comment will store the past row's description. When we detect we're in a new journal_entry,
                               //   we should see if our old journal_entry had a non-empty description. If it did, make a new row to
                               //   show just the description. If not, don't even include a new row at all and move on.
                               //
                           // Then we begin reading in each row. Our assumption is that there AT LEAST TWO rows dedicated to each
                           // journal entry, but their could be more. The loop will contain the finer details, but the pseudocode
                           // is as follows:
                           // - Get row. Obtain this row's post_ref. Determine if row has a credit or debit subentry. Obtain subentry accountname.
                           // - Check to see if we have an old post_ref. If not, this means we are on our first iteration of the while loop
                           // - When we're in either the first iteration of our while loop, or we have a loop that just entered a different journal_entry,
                           //   then we will put the journal entry's date and PR in the table.
                           // - Irrespective of that, then we place our account name, then credit or debit. 
                           // - Depending on whether credit or debit, we will apply some CSS for indentation and place in appropriate column on table

                $old_pr = "";
                $old_comment = "";
                $current_account_name = "";
                while ($row = pg_fetch_row($result, null, PGSQL_NUM)) {
                    $current_pr = $row[1];
                    // determine if credit or debit and store in a bool
                    $is_credit = false;
                    if (strcmp($row[2], 't') == 0) {
                        $is_credit = true;
                    }
                    // get account_name of subentry and store in name
                    $name_query = "select account_name from chart_of_accounts where account_id=$row[4]";
                    $name_result = pg_query($dbconn, $name_query) or die('Query failed: ' . pg_last_error());
                    $name = pg_fetch_row($name_result, null, PGSQL_NUM)[0];

                    // if true, this tells us we're on our first iteration of the loop. I.e,
                    // on a new journal_entry.
                    if(empty($old_pr)) {
                    ?>
                    <tr class="standard_row" id="<?php echo htmlspecialchars($row[1])?>" style="border-bottom: 1px solid #ddd;">
                        <td class="date_col"> <?php echo htmlspecialchars($row[0]) ?> </td>
                        <td class="pr_col">
                        <a href="journal-entry-edit.php?number=<?php echo htmlspecialchars($row[1])?>">
                        <?php echo htmlspecialchars($row[1]) ?>
                        </a>
                        </td>
                        <td class="acc_col <?php error_log($is_credit); if($is_credit) { echo "cred"; }?>"><?php echo $name ?></td>
                        <td class="pad_col"></td>
                        <?php
                        if(!$is_credit) { ?>
                            <td class="deb_col">$<?php echo htmlspecialchars($row[3])?></td>
                            <td class="cred_col"></td>
                        <?php } else { ?>
                            <td class="deb_col"></td>
                            <td class="cred_col">$<?php echo htmlspecialchars($row[3])?></td>
                        <?php } ?>
                    </tr>
                    <?php
                    }
                    else {
                        // if true, this tells us we're still on the same journal-entry
                        if($current_pr === $old_pr) {
                        ?>
                            <tr class="standard_row" style="border-bottom: 1px solid #ddd;">
                                <td class="date_col"></td>
                                <td class="pr_col"></td>
                                <td class="acc_col <?php error_log($is_credit); if($is_credit === true) { echo "cred"; }?>"><?php echo $name ?></td>
                                <td class="pad_col"></td>
                                <?php
                                if(!$is_credit) { ?>
                                    <td class="deb_col">$<?php echo htmlspecialchars($row[3])?></td>
                                    <td class="cred_col"></td>
                                <?php } else { ?>
                                    <td class="deb_col"></td>
                                    <td class="cred_col">$<?php echo htmlspecialchars($row[3])?></td>
                                <?php } ?>
                            </tr>
                        <?php
                        }
                        // if false, this tells us we're
                        // on a new journal_entry.
                        else {
                            // generate new stuff, but also check for comments
                            // if not empty, post comment on it's own row.
                            if(!empty($old_comment)) {?>
                                <tr class="standard_row" style="border-bottom: 1px solid #ddd;">
                                    <td class="date_col"></td>
                                    <td class="pr_col"></td>
                                    <td class="acc_col"><em><?php echo $old_comment?></em></td>
                                    <td class="pad_col"></td>
                                    <td class="deb_col"></td>
                                    <td class="cred_col"></td>
                                </tr>
                            <?php } ?>
                            <tr class="standard_row" id="<?php echo htmlspecialchars($row[0])?>" style="border-bottom: 1px solid #ddd;">
                                <td class="date_col"> <?php echo htmlspecialchars($row[0]) ?> </td>
                                <td class="pr_col">
                                    <a href="journal-entry-edit.php?number=<?php echo htmlspecialchars($row[1])?>">
                                    <?php echo htmlspecialchars($row[1]) ?>
                                    </a>
                                </td>
                                <td class="acc_col <?php if($is_credit === true) { echo "cred"; }?>"><?php echo $name ?></td>
                                <td class="pad_col"></td>
                                <?php
                                if(!$is_credit) { ?>
                                    <td class="deb_col">$<?php echo htmlspecialchars($row[3])?></td>
                                    <td class="cred_col"></td>
                                <?php } else { ?>
                                    <td class="deb_col"></td>
                                    <td class="cred_col">$<?php echo htmlspecialchars($row[3])?></td>
                                <?php } ?>
                            </tr>
                        <?php
                        }
                    }
                    $old_comment = $row[5];
                    $old_pr = $current_pr;
                }
                pg_close($dbconn);
                ?>
            </table>
        </div>
        </main>

        <div class="booties"><a href="help.php" class="help-button">Need help?</a></div>
    </body>
</html>

