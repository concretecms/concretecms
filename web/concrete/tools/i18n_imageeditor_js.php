<?php
header('Content-type: text/javascript; charset=' . APP_CHARSET);

$jh = Core::make('helper/json');
?>
var ccmi18n_imageeditor = {
	loadingControlSets: <?php echo $jh->encode(t('Loading Control Sets...'))?>,
	loadingComponents: <?php echo $jh->encode(t('Loading Components...'))?>,
	loadingFilters: <?php echo $jh->encode(t('Loading Filters...'))?>,
	loadingImage: <?php echo $jh->encode(t('Loading Image...'))?>,
	areYouSure: <?php echo $jh->encode(t('Are you sure?'))?>
};
