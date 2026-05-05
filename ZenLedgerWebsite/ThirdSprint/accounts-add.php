<?php 
session_start();

if (isset($_SESSION["admin"])) {

    if (empty($_SESSION['selected_customer'])) {
        header("Location: index.php");
        exit();
    }
    include("snippets/cosmic-message.php");


    ?>


    <!DOCTYPE html>
    <html lang="">
    <head>
        <meta charset="utf-8">
        <link href="style/nonregisterstyle.css" rel="stylesheet" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>ZenLedger - Accounts Add</title>
        <!-- Note(Fran): This CSS is **necessary** for html tabbing effect to work. All other CSS goes in a separate stylesheet! -->
        <style>
            [data-tab-info] {
                display: none;
            }

            .active[data-tab-info] {
                display: block;
            }
            .tab-header {
                cursor:pointer;
                margin: 4em;
            }
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

        </main>
        <div class="butterdish">
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
                <span class="info-icon">
                    <a href="accounts-view.php" class="journal-button">View</a>
                    <span class="tooltip-text">View all accounts</span>
                </span>
        </div>

        <div class="tab" id="Add-Tab">
        <br>
            <form method="POST" action="action-create-account.php"> 
                <label for="account-name">Account Name</label>
                <input type="text" name="account-name" id="account-name" required /><br>

                <label for="account-description">Account Description</label>
                <input type="text" name="account-description" id="account-description"/><br>

                <label for="account-category">Account Category</label>
                <select name="account-category" id="account-category"  required>
                    <option value="Assets">Assets</option>
                    <option value="Liabilities">Liabilities</option>
                    <option value="Equity">Equity</option>
                    <option value="Revenue">Revenue</option>
                    <option value="Expense">Expense</option>
                </select><br>

                <label for="account-subcategory">Account Subcategory</label>
                <select name="account-subcategory" id="account-subcategory"  required>
                    <option value="None">None</option>
                    <option value="Sales">Sales</option>
                    <option value="Goods">Goods Purchase</option>
                    <option value="Stock">Stock</option>
                    <option value="Inventory">Inventory</option>
                    <option value="Debt">Debt</option>
                    <option value="Long">Long-Term Debt</option>
                    <option value="Interest-Bearing">Interest-Bearing</option>
                    <option value="Finished-Goods Inventory">Finished-Goods Inventory</option>
                    <option value="Fixed">Fixed</option>
                    <option value="Credit">Credit</option>
                    <option value="Dividend-Yielding">Dividend-Yielding</option>
                    <option value="Lease">Lease</option>
                    <option value="Current Asset">Current Asset</option>
                    <option value="Current Liability">Current Liability</option>
                </select><br>


                <td style="border:none">
                <label for="initial-balance">Initial Balance</label>
                $<input type="number" name="initial-balance" id="debit" min="0" step='0.01' value='0.00' placeholder='0.00' /><br>

                <label for="order">Order</label>
                <input type="number" value="1" min="1" step="1" name="order" id="order" required /><br>

                <label for="statement">Statement</label>
                <select name="statement" id="statement" required>
                    <option value="is">IS</option>
                    <option value="bs">BS</option>
                    <option value="re">RE</option>
                </select><br>

                <label for="comment">Comment</label>
                <input type="text" name="comment" id="comment"/><br>
                <input type="submit" value="Create Account" class="tiny-button-maker">
            </form>
        </div>

        <div class="booties"><a href="help.php" class="help-button">Need help?</a> <a href="mail-page.php" class="help-button">Email</a>
    </div>
    </footer>
    </body>
</html>

<?php 
} else {

    echo "Error: Unauthorized Page";
    exit();
}
?>



