<? 
defined('C5_EXECUTE') or die("Access Denied.");
$valt = Loader::helper('validation/token');
$th = Loader::helper('text');
	?>

<h1><span><?=$title?></span></h1>
<div class="ccm-dashboard-inner">

	<form method="post" id="ccm-log-search"  action="<?=$pageBase?>">
	<?=t('Keywords')?>
	<?=$form->text('keywords', $keywords)?>
	&nbsp;&nbsp;
	<?=t('Type')?>
	<?=$form->select('logType', $logTypes)?>
	<?=$form->submit('search',t('Search') )?>
	</form>

	<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
	<tr>
		<td class="subheaderActive"><?=t('Date/Time')?></td>
		<td class="subheader"><?=t('Log Type')?></td>
		<td class="subheader"><?=t('Text')?></td>
	</tr>
	<? foreach($entries as $ent) { ?>
	<tr>
		<td valign="top" style="white-space: nowrap" class="active"><?=date(DATE_APP_GENERIC_TS, strtotime($ent->getTimestamp('user')))?><? if (date('m-d-y') != date('m-d-y', strtotime($ent->getTimestamp('user')))) { ?>
			<?=t(' at ')?><?=date(DATE_APP_GENERIC_MDY, strtotime($ent->getTimestamp('user')))?>
		<? } ?></td>
		<td valign="top"><strong><?=$ent->getType()?></strong></td>
		<td style="width: 100%"><?=$th->makenice($ent->getText())?></td>
	</tr>
	<? } ?>
	</table>	

	<? if (count($entries) > 0) { ?>
		<div style="text-align: center; padding-top: 10px">
		<input type="button" onclick="if (confirm('<?=t("Are you sure you want to clear this log?")?>')) { location.href='<?=$this->url('/dashboard/reports/logs', 'clear', $valt->generate())?>'}" value="<?=t('Clear Log')?>" />
		</div>
	<? } ?>
	
	<br/>
	
	<? if($paginator && strlen($paginator->getPages())>0){ ?>	 
		 <div  class="pagination">
			 <div class="pageLeft"><?=$paginator->getPrevious()?></div>
			 <div class="pageRight"><?=$paginator->getNext()?></div>
			 <?=$paginator->getPages()?>
		 </div>		
	<? } ?>		

</div>