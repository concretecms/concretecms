<? defined('C5_EXECUTE') or die("Access Denied.");?>

<?php if ($this->controller->getTask() == 'export_database_schema') { ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Database Schema'), false, 'span12 offset2', false)?>

<div class="ccm-pane-body ccm-pane-body-footer">
<p>
<a href="<?php echo $this->url('/dashboard/system/backup_restore/database')?>" class="btn"><?php echo t('Return to Export Database XML')?></a>
</p>
<textarea style="width: 100%; height: 600px"><?php echo htmlentities($schema, ENT_COMPAT, APP_CHARSET)?></textarea>

</div>
<?php }else{?>

<?php  if (ENABLE_DEVELOPER_OPTIONS) { ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Database Tables and Content'), false, 'span12 offset2')?>
	
	<form method="post" class="form-stacked" id="export-db-form" action="<?php echo $this->url('/dashboard/system/backup_restore/database', 'export_database_schema')?>">

	<h3><?php echo t('Export Database Schema')?></h3>
	<p><?php echo t('Click below to view your database schema in a format that can imported into concrete5 later.')?></p>
    
    <div class="actions">
        <?
        print $interface->submit(t('Export Database Tables'), 'export-db-form', 'left');
        ?>
    </div>

	
	</form>
	
	
	<form method="post" class="form-stacked" id="refresh-schema-form" action="<?php echo $this->url('/dashboard/system/backup_restore/database', 'refresh_database_schema')?>" class="form-stacked">

	<h3><?=t('Database Refresh')?></h3>
		<?php echo $this->controller->token->output('refresh_database_schema')?>
		<?php  
		$extra = array();
		if (!file_exists('config/' . FILENAME_LOCAL_DB)) {
			$extra = array('disabled' => 'true');
		}
		?>
        
              <ul class="inputs-list">
                <li>
                  <label>
                    <?php echo $form->checkbox('refresh_global_schema', 1, false)?>
                    <span><?php echo t('Refresh core database tables and blocks.')?></span>
                  </label>
                </li>
               </ul>
              <span class="help-block">
                <?php echo t('Refreshes %s files contained in %s and all block directories.', FILENAME_BLOCK_DB, 'concrete/config/')?>
              </span>
              
              <div class="clearfix"></div>
              
              <ul class="inputs-list">
                <li>
                  <label>
                    <?php echo $form->checkbox('refresh_local_schema', 1, false, $extra)?>
                    <span><?php echo t('Reload custom tables.')?></span>
                  </label>
                </li>
               </ul>
              <span class="help-block">
                <?php echo t('Reloads database tables contained in %s.', 'config/' . FILENAME_LOCAL_DB)?>
              </span>
    
    <div class="actions">
            <?
        print $interface->submit(t('Refresh Databases'), 'refresh-schema-form', 'left');
        ?>
    
	</div>
            
	</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>		
	


<?php  }else{ ?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Database XML'), false, 'span12 offset2', false)?>
<div class="ccm-pane-body ccm-pane-body-footer">
<div class="alert-message block-message error">       
        <p><?php echo t('Developer options have been disabled in the config file.');?></p>
</div>
</div>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>    
<?php }?>
<?php }?>