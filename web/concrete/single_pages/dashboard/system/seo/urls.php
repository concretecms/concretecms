<?php
defined('C5_EXECUTE') or die("Access Denied.");
$dh = Loader::helper('concrete/dashboard');

echo $dh->getDashboardPaneHeaderWrapper(t('Pretty URLs'), t("Automatically translates your path-based concrete5 URLs so that they don't include %s.", DISPATCHER_FILENAME), 'span8 offset2', false);
?>

<form method="post" action="<?php echo $this->action('update_rewriting'); ?>">
	<div class="ccm-pane-body">	
		<?php echo $this->controller->token->output('update_rewriting'); ?>
		<div class="clearfix inputs-list">
		<label for="URL_REWRITING">
			<?php echo $fh->checkbox('URL_REWRITING', 1, $intRewriting) ?>
		
			<span><?php echo t('Enable Pretty URLs'); ?></span>
		</label>
		
		</div>
		
		<?php 
		// Show the placeholder textarea with the mod_rewrite rules if pretty urls enabled
		// NOTE: The contents of the textarea are not saved
		if(URL_REWRITING){
			echo '
		<div class="clearfix"><h5>' . t('Code for your .htaccess file') . '</h5>
		<textarea style="width:98%; max-width:98%; min-width:98%; height:150px; min-height:150px; max-height:300px;" onclick="this.select()">' . $strRules . '</textarea></div>';
		}
		?>		
	</div>
	<div class="ccm-pane-footer">	
		<?php echo $interface->submit(t('Save'), 'url-form', 'right', 'primary'); ?>
	</div>
</form>

<?php echo $dh->getDashboardPaneFooterWrapper(false); ?>