
<?php  if ($this->controller->getTask() == 'database') { ?>

	<h1><span>Database Logs</span></h1>
	<div class="ccm-dashboard-inner">
	<?php  if (count($entries) > 0) { ?>
		
		<form id="ccm-log-search">
		<input type="button" onclick="location.href='<?php echo $this->url('/dashboard/logs', 'clear_database_log')?>'" value="Clear Log" />
		</form>
		
		<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
		<tr>
			<td class="subheaderActive">Date/Time</td>
			<td class="subheader">Query</td>
			<td class="subheader">Params</td>
			<td class="subheader">Page/Script</td>
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
		<p>You must first enable the logging of database queries from your <a href="<?php echo $this->url('/dashboard/settings', 'set_developer')?>">developer settings section</a> before you can use this page.
		</p>
	<?php  } ?>

<?php  } else { 
	$th = Loader::helper('text');
	?>

	<h1><span><?php echo $title?></span></h1>
	<div class="ccm-dashboard-inner">
	
		<form method="post" id="ccm-log-search"  action="<?php echo $pageBase?>">
		<?php echo $form->text('keywords', $keywords)?>
		<?php echo $form->submit('search','Search')?>
		</form>

		<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
		<tr>
			<td class="subheaderActive">Date/Time</td>
			<td class="subheader">Log Type</td>
			<td class="subheader">Text</td>
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