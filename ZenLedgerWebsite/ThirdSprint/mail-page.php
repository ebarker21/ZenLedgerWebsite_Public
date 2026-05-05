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
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="style/nonregisterstyle.css" rel="stylesheet" />
    <title>ZenLedger - Email System</title>
    <style>
        /*styling for this page specifically*/
        /* body {
            display: flex;
            flex-direction: column;
            align-items: center;

            margin: 0;
            padding: 0;
        }
        .cosmic-container {
            text-align: center;
            margin-top: 2em;
        } */
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 2em;
        }
        input[type="email"], input[type="text"], textarea {
            width: 300px;
            padding: 10px;
            margin: 10px 0;
            font-size: 1em;
        }
        input[type="submit"] {
            padding: 10px 20px;
            font-size: 1em;
            margin-top: 1em;
            cursor: pointer;
        }
        /* .booties {
            margin-top: 2em;
        } */
        /* .helper img {
            opacity: 0.1;
            position: fixed;
            top: 10%;
            left: 50%;
            transform: translateX(-50%);
            width: 400px;
            pointer-events: none;
        } */
    </style>
</head>
<body>
<main>
    <?php include('snippets/logged-in-top-bar.php'); ?>

    <div class="cosmic-container">
        <h1>Email System</h1>
        
        <p class="cosmic-message"><?php echo $cosmic_message; ?></p>
    </div>
    <hr>

    <form method="POST" action="mail.php">
        <label for="send_email">Recipient Email:</label>
        <input type="email" id="send_email" name="send_email" required>

        <label for="send_subject">Subject:</label>
        <input type="text" id="send_subject" name="send_subject" required>

        <label for="send_message">Message:</label>
        <textarea id="send_message" name="send_message" rows="10" cols="50" required></textarea>

        <input type="submit" value="Send Email">
    </form>



    <div class="helper">
        <img src="images/zenledger logo.png" class="background-logo" />
    </div>

    <script src="snippets/calendar.js"></script>
    </main>
    <footer>
    <div class="booties">
        <a href="help.php" class="help-button">Need help?</a>
    </div>
    </footer>
</body>
</html>
