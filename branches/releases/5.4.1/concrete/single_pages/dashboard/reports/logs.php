<?php  
defined('C5_EXECUTE') or die("Access Denied.");
$valt = Loader::helper('validation/token');
$th = Loader::helper('text');
	?>

<h1><span><?php echo $title?></span></h1>
<div class="ccm-dashboard-inner">

	<form method="post" id="ccm-log-search"  action="<?php echo $pageBase?>">
	<?php echo t('Keywords')?>
	<?php echo $form->text('keywords', $keywords)?>
	&nbsp;&nbsp;
	<?php echo t('Type')?>
	<?php echo $form->select('logType', $logTypes)?>
	<?php echo $form->submit('search',t('Search') )?>
	</form>

	<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
	<tr>
		<td class="subheaderActive"><?php echo t('Date/Time')?></td>
		<td class="subheader"><?php echo t('Log Type')?></td>
		<td class="subheader"><?php echo t('Text')?></td>
	</tr>
	<?php  foreach($entries as $ent) { ?>
	<tr>
		<td valign="top" style="white-space: nowrap" class="active"><?php echo date(DATE_APP_GENERIC_TS, strtotime($ent->getTimestamp('user')))?><?php  if (date('m-d-y') != date('m-d-y', strtotime($ent->getTimestamp('user')))) { ?>
			<?php echo t(' at ')?><?php echo date(DATE_APP_GENERIC_MDY, strtotime($ent->getTimestamp('user')))?>
		<?php  } ?></td>
		<td valign="top"><strong><?php echo $ent->getType()?></strong></td>
		<td style="width: 100%"><?php echo $th->makenice($ent->getText())?></td>
	</tr>
	<?php  } ?>
	</table>	

	<?php  if (count($entries) > 0) { ?>
		<div style="text-align: center; padding-top: 10px">
		<input type="button" onclick="if (confirm('<?php echo t("Are you sure you want to clear this log?")?>')) { location.href='<?php echo $this->url('/dashboard/reports/logs', 'clear', $valt->generate())?>'}" value="<?php echo t('Clear Log')?>" />
		</div>
	<?php  } ?>
	
	<br/>
	
	<?php  if($paginator && strlen($paginator->getPages())>0){ ?>	 
		 <div  class="pagination">
			 <div class="pageLeft"><?php echo $paginator->getPrevious()?></div>
			 <div class="pageRight"><?php echo $paginator->getNext()?></div>
			 <?php echo $paginator->getPages()?>
		 </div>		
	<?php  } ?>		

</div>