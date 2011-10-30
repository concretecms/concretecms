<?php
defined('C5_EXECUTE') or die("Access Denied.");
$dh = Loader::helper('concrete/dashboard');

echo $dh->getDashboardPaneHeaderWrapper(t('Pretty URLs'), false, 'span9 offset2', false);
?>

<form method="post" action="<?php echo $this->action('update_rewriting'); ?>">
	<div class="ccm-pane-body">	
		<?php echo $this->controller->token->output('update_rewriting'); ?>
		
		<label for="URL_REWRITING">
			<?php echo $fh->checkbox('URL_REWRITING', 1, $intRewriting) ?>
		
			<span><?php echo t('Enable Pretty URLs'); ?></span>
		</label>
		
		<span class="help-block tab-content clearfix">
			<?php echo t("Automatically translates your path-based Concrete5 URLs so that they don't include %s.", DISPATCHER_FILENAME); ?>
		</span>
		
		<?php 
		// Show the placeholder textarea with the mod_rewrite rules if pretty urls enabled
		// NOTE: The contents of the textarea are not saved
		if(URL_REWRITING){
			echo '
		<h5>' . t('Required Code') . '</h5>
		<textarea class="xlarge tab-content" style="max-width:100%; min-width:270px; height:150px; min-height:150px; max-height:300px;" onclick="this.select()">' . $strRules . '</textarea>';
		}
		?>		
	</div>

	<div class="ccm-pane-footer">	
		<?php echo $interface->submit(t('Save'), 'url-form', 'left', 'primary'); ?>
	</div>
</form>

<?php echo $dh->getDashboardPaneFooterWrapper(false); ?>