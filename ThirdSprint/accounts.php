<?php
session_start();


if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}


if (empty($_SESSION['selected_customer'])) {
    header("Location: index.php");
    exit();
}
include("snippets/cosmic-message.php");
?>

<!DOCTYPE html>
<html lang="">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="style/nonregisterstyle.css" rel="stylesheet" />
    <title>ZenLedger - Accounts</title>
    <style>
        [data-tab-info] { display: none; }
        .active[data-tab-info] { display: block; }
        .tab-header { cursor:pointer; margin: 4em; }
    </style>
  </head>
  <body>
    <main>
        <?php include('snippets/logged-in-top-bar.php'); ?>
        <div class="cosmic-container">
    <h1>Accounts</h1>
    <p class="cosmic-message"><?php echo $cosmic_message; ?></p>
</div>
        <hr>

        <div class="helper">
                  <img src="images/zenledger logo.png" class="background-logo" />
              </div>

        <div class="butterdish">
        <?php
        if(isset($_SESSION["admin"]))
        {
            ?>
            <span class="info-icon">
                <a href="accounts-add.php" class="journal-button">Add</a>
                <span class="tooltip-text">Add a new account to the chart</span>
            </span>
            <span class="info-icon">
                <a href="accounts-deactivate.php" class="journal-button">Activate/Deactivate</a>
                <span class="tooltip-text">Activate/Deactivate all accounts</span>
            </span>
            <span class="info-icon">
                <a href="accounts-edit.php" class="journal-button">Edit</a>
                <span class="tooltip-text">Edit account information</span>
            </span>
            <span class="info-icon">
                <a href="accounts-changelog.php" class="journal-button">Changelog</a>
                <span class="tooltip-text">Show Changelog</span>
            </span>
        <?php
        }
        else {
        echo("<script>window.top.location='/accounts-view.php'</script>");
        } ?>
        <span class="info-icon">
            <a href="accounts-view.php" class="journal-button">View</a>
            <span class="tooltip-text">View all accounts</span>
        </span>
    </div>

    
        <div class="booties"><a href="help.php" class="help-button">Need help?</a> <a href="mail-page.php" class="help-button">Email</a>
    </div>
    <script src="snippets/calendar.js"></script> 
    </footer>
    </body>
</html>
<?php
