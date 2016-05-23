<?php defined('C5_EXECUTE') or die("Access Denied.");

$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */

?>
<form class="form-inline" action="<?php echo $view->action('view')?>" method="get">
    <div class="ccm-dashboard-header-buttons">
	    <a href="<?=View::url('/dashboard/users/points/assign')?>" class="btn btn-primary"><?=t('Add Points')?></a>
	</div>
	
    <div class="ccm-pane-options">
        <div class="ccm-pane-options-permanent-search">
            <?=$form->label('uName', t('User'))?>
            <?php echo $form_user_selector->quickSelect('uName', $_GET['uName'], array('form-control'));?>
            <input type="submit" value="<?=t('Search')?>" class="btn" />


        </div>
    </div>
</form>
<br />
<?php
if (count($entries) > 0) {
    ?>	
	<table border="0" cellspacing="0" cellpadding="0" id="ccm-product-list" class="table table-striped">
	<tr>
		<th class="<?=$upEntryList->getSearchResultsClass('uName')?>"><a href="<?=$upEntryList->getSortByURL('uName', 'asc')?>"><?=t('User')?></a></th>
		<th class="<?=$upEntryList->getSearchResultsClass('upaName')?>"><a href="<?=$upEntryList->getSortByURL('upaName', 'asc')?>"><?=t('Action')?></a></th>
		<th class="<?=$upEntryList->getSearchResultsClass('upPoints')?>"><a href="<?=$upEntryList->getSortByURL('upPoints', 'asc')?>"><?=t('Points')?></a></th>
		<th class="<?=$upEntryList->getSearchResultsClass('timestamp')?>"><a href="<?=$upEntryList->getSortByURL('timestamp', 'asc')?>"><?=t('Date Assigned')?></a></th>
		<th><?=t("Details")?></th>
		<th></th>
	</tr>
    <?php 
    foreach ($entries as $up) {
        ?>
    	<tr>
    		<?php
                $ui = $up->getUserPointEntryUserObject();
        $action = $up->getUserPointEntryActionObject();
        ?>
    		<td><?php if (is_object($ui)) {
    ?><?php echo h($ui->getUserName())?><?php 
}
        ?></td>
    		<td><?php if (is_object($action)) {
    ?><?=h($action->getUserPointActionName())?><?php 
}
        ?></td>
    		<td><?php echo number_format($up->getUserPointEntryValue())?></td>
    		<td><?php echo $dh->formatDateTime($up->getUserPointEntryTimestamp());
        ?></td>
    		<td><?=h($up->getUserPointEntryDescription())?></td>
    		<td style="Text-align: right">
                <?php
                $delete = \Concrete\Core\Url\Url::createFromUrl($view->action('deleteEntry', $up->getUserPointEntryID()));

        $delete->setQuery(array(
                    'ccm_token' => \Core::make('helper/validation/token')->generate('delete_community_points'),
                ));
        ?>
    		    <a href="<?=$delete?>" class="btn btn-sm btn-danger"><?=t('Delete')?></a>
    		</td>
    	</tr>
    <?php 
    }
    ?>
</table>
<?php 
} else {
    ?>
	<div id="ccm-list-none"><?=t('No entries found.')?></div>
<?php 
}
$upEntryList->displayPaging(); ?>