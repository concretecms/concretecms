
<? if ($this->controller->getTask() == 'database') { ?>

	<h1><span>Database Logs</span></h1>
	<div class="ccm-dashboard-inner">
	<? if (count($entries) > 0) { ?>
		
		<form id="ccm-log-search">
		<input type="button" onclick="location.href='<?=$this->url('/dashboard/logs', 'clear_database_log')?>'" value="Clear Log" />
		</form>
		
		<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
		<tr>
			<td class="subheaderActive">Date/Time</td>
			<td class="subheader">Query</td>
			<td class="subheader">Params</td>
			<td class="subheader">Page/Script</td>
		</tr>
		<? foreach($entries as $ent) { ?>
		<tr>
			<td class="active"><?=date('g:i:s', strtotime($ent->getTimestamp()))?><? if (date('m-d-y') != date('m-d-y', strtotime($ent->getTimestamp()))) { ?>
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
		<p>You must first enable the logging of database queries from your <a href="<?=$this->url('/dashboard/settings', 'set_developer')?>">developer settings section</a> before you can use this page.
		</p>
	<? } ?>

<? } else { 
	$th = Loader::helper('text');
	?>

	<h1><span><?=$title?></span></h1>
	<div class="ccm-dashboard-inner">
	
		<form method="post" id="ccm-log-search"  action="<?=$pageBase?>">
		<?=$form->text('keywords', $keywords)?>
		<?=$form->submit('search','Search')?>
		</form>

		<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
		<tr>
			<td class="subheaderActive">Date/Time</td>
			<td class="subheader">Log Type</td>
			<td class="subheader">Text</td>
		</tr>
		<? foreach($entries as $ent) { ?>
		<tr>
			<td valign="top" style="white-space: nowrap" class="active"><?=date('g:i:s', strtotime($ent->getTimestamp()))?><? if (date('m-d-y') != date('m-d-y', strtotime($ent->getTimestamp()))) { ?>
				<?=date('m/d/y', strtotime($ent->getTimestamp()))?>
			<? } ?></td>
			<td valign="top"><strong><?=$ent->getType()?></strong></td>
			<td style="width: 100%"><?=$th->makenice($ent->getText())?></td>
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

	</div>
	
<? } ?>