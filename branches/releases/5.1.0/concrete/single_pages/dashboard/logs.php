<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
if ($this->controller->getTask() == 'database') { ?>

	<h1><span><?php echo t('Database Logs')?></span></h1>
	<div class="ccm-dashboard-inner">
	<?php  if (count($entries) > 0) { ?>
		
		<form id="ccm-log-search">
		<input type="button" onclick="location.href='<?php echo $this->url('/dashboard/logs', 'clear_database_log')?>'" value="<?php echo t('Clear Log')?>" />
		</form>
		
		<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
		<tr>
			<td class="subheaderActive"><?php echo t('Date/Time')?></td>
			<td class="subheader"><?php echo t('Query')?></td>
			<td class="subheader"><?php echo t('Params')?></td>
			<td class="subheader"><?php echo t('Page/Script')?></td>
		</tr>
		<?php  foreach($entries as $ent) { ?>
		<tr>
			<td class="active"><?php echo date('g:i:s', strtotime($ent->getTimestamp()))?><?php  if (date('m-d-y') != date('m-d-y', strtotime($ent->getTimestamp()))) { ?>
				<?php echo date('m/d/y', strtotime($ent->getTimestamp()))?>
			<?php  } ?></td>
			<td><?php echo $ent->getQuery()?></td>
			<td><?php echo $ent->getParameters()?></td>
			<td><?php echo $ent->getTrace()?></td>
		</tr>
		<?php  } ?>
		</table>	
		
		<br/>
		
		<?php  if($paginator && strlen($paginator->getPages())>0){ ?>	 
			 <div  class="pagination">
				 <div class="pageLeft"><?php echo $paginator->getPrevious()?></div>
				 <div class="pageRight"><?php echo $paginator->getNext()?></div>
				 <?php echo $paginator->getPages()?>
			 </div>		
		<?php  } ?>		

	<?php 	
	
	} else { ?>
		<p>
		<?php echo t('You must first enable the logging of database queries from your <a href="%s">developer settings section</a> before you can use this page.', $this->url('/dashboard/settings', 'set_developer'))?>
		</p>
	<?php  } ?>

<?php  } else { 
	$th = Loader::helper('text');
	?>

	<h1><span><?php echo $title?></span></h1>
	<div class="ccm-dashboard-inner">
	
		<form method="post" id="ccm-log-search"  action="<?php echo $pageBase?>">
		<?php echo $form->text('keywords', $keywords)?>
		<?php echo $form->submit('search',t('Search') )?>
		<input type="button" onclick="if (confirm('<?php echo t("Are you sure you want to clear this log?")?>')) { location.href='<?php echo $this->url('/dashboard/logs', 'clear_log', $this->controller->getTask())?>'}" value="<?php echo t('Clear Log')?>" />
		</form>

		<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
		<tr>
			<td class="subheaderActive"><?php echo t('Date/Time')?></td>
			<td class="subheader"><?php echo t('Log Type')?></td>
			<td class="subheader"><?php echo t('Text')?></td>
		</tr>
		<?php  foreach($entries as $ent) { ?>
		<tr>
			<td valign="top" style="white-space: nowrap" class="active"><?php echo date('g:i:s', strtotime($ent->getTimestamp()))?><?php  if (date('m-d-y') != date('m-d-y', strtotime($ent->getTimestamp()))) { ?>
				<?php echo date('m/d/y', strtotime($ent->getTimestamp()))?>
			<?php  } ?></td>
			<td valign="top"><strong><?php echo $ent->getType()?></strong></td>
			<td style="width: 100%"><?php echo $th->makenice($ent->getText())?></td>
		</tr>
		<?php  } ?>
		</table>	
		
		<br/>
		
		<?php  if($paginator && strlen($paginator->getPages())>0){ ?>	 
			 <div  class="pagination">
				 <div class="pageLeft"><?php echo $paginator->getPrevious()?></div>
				 <div class="pageRight"><?php echo $paginator->getNext()?></div>
				 <?php echo $paginator->getPages()?>
			 </div>		
		<?php  } ?>		

	</div>
	
<?php  } ?>