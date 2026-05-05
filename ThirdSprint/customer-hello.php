<?php
session_start();


if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}


if (empty($_SESSION['selected_customer'])) {
    header("Location: index.php");
    exit();
}


$selected_customer = $_SESSION['selected_customer'];

$bold_name = "<strong>" . htmlspecialchars($selected_customer, ENT_QUOTES, 'UTF-8') . "</strong>";
$affirmations = [
    "You are now in energetic alignment with {$bold_name}’s financial aura.",
    "Tuning into the fiscal frequency of {$bold_name}.",
    "Channeling the abundance field surrounding {$bold_name}’s wealth journey.",
    "You have entered the sacred accounting space of {$bold_name}.",
    "Now witnessing the manifested abundance of {$bold_name}.",
    "Your third eye now gazes upon the prosperity path of {$bold_name}.",
    "Floating gently through the monetary meridians of {$bold_name}.",
    "Harmonizing with the soul of {$bold_name}’s balance sheet."
];


$cosmic_message = $affirmations[array_rand($affirmations)];

$dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
or die('Could not connect: ' . pg_last_error());
$query = "SELECT * FROM customer_info WHERE customer_name = '$selected_customer'";
$result = pg_query($dbconn, $query);
$result_row = pg_fetch_row($result,0,PGSQL_ASSOC);
$sales = $result_row['total_sales'];
?>

<!DOCTYPE html>
<html>
<head>
    <style>
 .box {
    position: relative;
  background-color: white;
  color: black;
  border-radius: 5px;
  padding: 20px;
  font-size: smaller;
  height: 139px;
  width: 139px;
}

.flex_wrap {
    display: flex;
    justify-content: center;
    align-items: center;
}
.wrapper {
  display: grid;
  grid-gap: 10px;
  grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
  grid-template-rows: 1fr 1fr 1fr 1fr 1fr;
  grid-auto-flow: row;
}  

.sales{
    cursor: pointer;
    background-color: #deffc7;
}

    </style>
    <meta charset="utf-8">
    <title>ZenLedger</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="style/nonregisterstyle.css" rel="stylesheet">
</head>
<body>

<main>
<?php include('snippets/logged-in-top-bar.php'); ?>

<div class="cosmic-container">
<h1> Main Dashboard </h1>



<p class="cosmic-message"><?php echo $cosmic_message; ?></p>
</div>

<hr>
<div class="helper">
                  <img src="images/zenledger logo.png" class="background-logo" />
              </div>

<div class="flex_wrap">
<div class="wrapper">

<?php include("snippets/grid/grossprofitmargin.php");?>
<div class="box <?php echo $box_class1; ?>">Gross Profit Margin: <?php echo $gpm_display; ?> 
</div>

<?php include("snippets/grid/returnonsales.php");?>
<div class="box <?php echo $box_class2; ?>">Return on Sales: <?php echo $ros_display; ?> 
</div>

<?php include("snippets/grid/netreturnonsales.php");?>
<div class="box <?php echo $box_class3; ?>">Net Return on Sales: <?php echo $nros_display; ?> 
</div>

<?php include("snippets/grid/returnontotalassets.php");?>
<div class="box <?php echo $box_class4; ?>">Return on Total Assets: <?php echo $totalass_display; ?>
</div>

<?php include("snippets/grid/returnonetworth.php");?>
<div class="box <?php echo $box_class5; ?>">Return on Net Worth: <?php echo $rnw_display; ?>
</div>

<?php include("snippets/grid/returnoncommonequity.php");?>
  <div class="box <?php echo $box_class6; ?>">Return on Common Equity: <?php echo $rce_display; ?>
</div>

<?php include("snippets/grid/earningspershare.php");?>
  <div class="box <?php echo $box_class7; ?>">Earnings per Share: <?php echo $eps_display; ?>
</div>

<?php include("snippets/grid/currentratio.php");?>
  <div class="box <?php echo $box_class8; ?>">Current Ratio: <?php echo $curr_display; ?>
</div>

<?php include("snippets/grid/quickratio.php");?>
  <div class="box <?php echo $box_class9; ?>">Quick Ratio: <?php echo $qu_display; ?>
</div>


<?php include("snippets/grid/inventorytonetworkingcapital.php");?>
  <div class="box <?php echo $box_class10; ?>">Inventory to Net Working Capital: <?php echo $nwc_display; ?>
</div>


<?php include("snippets/grid/debttoassets.php");?>
  <div class="box <?php echo $box_class11; ?>">Debt to Assets: <?php echo $dta_display; ?>
</div>


<?php include("snippets/grid/debttoequity.php");?>
  <div class="box <?php echo $box_class12; ?>">Debt to Equity: <?php echo $dte_display; ?>
</div>

<?php include("snippets/grid/longtermdebttoequity.php");?>
  <div class="box <?php echo $box_class13; ?>"">Long Term Debt to Equity: <?php echo $ltdte_display; ?> 
</div>

<?php include("snippets/grid/timesinterestearned.php");?>
  <div class="box <?php echo $box_class14; ?>">Times Interest Earned: <?php echo $tie_display; ?>
</div>


<?php include("snippets/grid/fixedchargecoverage.php");?>
  <div class="box <?php echo $box_class15; ?>">Fixed-Charge Coverage: <?php echo $fcc_display; ?>
</div>


<?php include("snippets/grid/inventoryturnover.php");?>
  <div class="box <?php echo $box_class16; ?>">Inventory Turnover: <?php echo $it_display; ?>
  </div>


  <?php include("snippets/grid/fixedassetsturnover.php");?>
  <div class="box <?php echo $box_class17; ?>">Fixed Assets Turnover: <?php echo $fat_display; ?>
</div>


<?php include("snippets/grid/totalassetsturnover.php");?>
  <div class="box <?php echo $box_class18; ?>">Total Assets Turnover:  <?php echo $tat_display; ?>
</div>

<?php include("snippets/grid/accountsreceivableturnover.php");?>
<div class="box <?php echo $box_class19; ?>">Accounts Receivable Turnover: <?php echo $art_display; ?></div>

  <div class="box">Average Collection Period: <?php include("snippets/grid/averagecollectionperiod.php");?></div>

  <?php if(isset($_SESSION['manager'])) { ?> <a href="journal-approve.php"> <?php } ?>
  <div class="box">Total Pending Entries: <?php include("snippets/grid/totalpendingentries.php");?></div>
  <?php if(isset($_SESSION['manager'])) { ?> </a> <?php } ?>
  <div class="box sales" id="sale" title="Push Me!">Total Sales: <?php echo $sales; ?>
    <script>
    var sale = document.getElementById('sale');
    sale.addEventListener('click', function() {
        async function send() {
        try {
            const response = await fetch("action-tic-sale.php", {
                                method: "POST",
                                redirect: 'follow',
                                headers: {'Content-Type': 'application/json'}
                                });
            if (!response.ok) {
                        throw new Error(`Response status: ${response.status}`);
            }
            const json_response = await response.json();
            sale.innerHTML = "Total Sales: " + json_response;
        }
        catch(error) { console.error(error); }
    }
    send();
    });
    </script>
    </div>
</div>
</div>
<div class="cosmic-options">
</div>

</main>
</body>
</html>
