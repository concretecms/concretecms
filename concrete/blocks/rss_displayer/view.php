<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-block-rss-displayer-wrapper">
    <div class="ccm-block-rss-displayer">


<?php if (strlen($title) > 0) {
    ?>
    <div class="ccm-block-rss-displayer-header">
        <<?php echo $titleFormat; ?>><?=h($title)?></<?php echo $titleFormat; ?>>
    </div>
<?php 
} ?>

<?php
$rssObj = $controller;
$textHelper = Loader::helper("text");

if (isset($errorMsg) && strlen($errorMsg) > 0) {
    echo $errorMsg;
} else {
    foreach ($posts as $itemNumber => $item) {
        ?>
		
		<div class="ccm-block-rss-displayer-item">
			<div class="ccm-block-rss-displayer-item-title">
				<a href="<?= $item->getLink();
        ?>" <?php if ($rssObj->launchInNewWindow) {
    echo 'target="_blank"';
}
        ?> >
					<?= $item->getTitle();
        ?>
				</a>
			</div>
			<div class="ccm-block-rss-displayer-item-date"><?= h($this->controller->formatDateTime($item->getDateCreated()));
        ?></div>
			<div class="ccm-block-rss-displayer-item-summary">
				<?php
                if ($rssObj->showSummary) {
                    echo $textHelper->shortText(strip_tags($item->getDescription()));
                }
        ?>
			</div>
		</div>
	
<?php 
    }
}
?>
    </div>

</div>