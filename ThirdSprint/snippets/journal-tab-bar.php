<div class="journal-tab-bar">

<?php if(isset($_SESSION['manager'])) {?>
<a id="approve_tab" href="journal-approve.php" class="journal-button">Approve</a>
<?php } ?>
<a id="view_tab" href="journal-view.php"  class="journal-button">View</a>
<a id="entry_tab" href="journal-entry.php"  class="journal-button">Entry</a>

<a id="changelog_tab" href="journal-changelog.php"  class="journal-button">Changelog</a>
</div>