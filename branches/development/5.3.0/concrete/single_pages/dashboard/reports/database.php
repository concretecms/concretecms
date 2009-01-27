<? 
defined('C5_EXECUTE') or die(_("Access Denied."));
$valt = Loader::helper('validation/token');
?>
<h1><span><?=t('Database Logs')?></span></h1>
<div class="ccm-dashboard-inner">
<? if (count($entries) > 0) { ?>
	
	<form id="ccm-log-search">
	<input type="button" onclick="location.href='<?=$this->url('/dashboard/reports/database', 'clear', $valt->generate())?>'" value="<?=t('Clear Log')?>" />
	</form>
	
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
	<tr>
		<td class="subheaderActive"><?=t('Date/Time')?></td>
		<td class="subheader"><?=t('Query')?></td>
		<td class="subheader"><?=t('Params')?></td>
		<td class="subheader"><?=t('Page/Script')?></td>
	</tr>
	<? foreach($entries as $ent) { ?> 
	<tr>
		<td class="active">
			<?=date('g:i:s', strtotime($ent->getTimestamp()))?>
			<? if (date('m-d-y') != date('m-d-y', strtotime($ent->getTimestamp()))) { ?>
				<?=date('m/d/y', strtotime($ent->getTimestamp()))?>
			<? } ?></td>
		<td><?=$ent->getQuery()?></td>
		<td><?=$ent->getParameters()?></td>
		<td><?=$ent->getTrace()?></td>
	</tr>
	<? } ?>
	</table>	
	
	<br/>
	
	<? if($paginator && strlen($paginator->getPages())>0){ ?>	 
		 <div  class="pagination">
			 <div class="pageLeft"><?=$paginator->getPrevious()?></div>
			 <div class="pageRight"><?=$paginator->getNext()?></div>
			 <?=$paginator->getPages()?>
		 </div>		
	<? } ?>		

<?	

} else { ?>
	<p>
	<?=t('You must first enable the logging of database queries from your <a href="%s">developer settings section</a> before you can use this page.', $this->url('/dashboard/settings', 'set_developer'))?>
	</p>
<? } ?>