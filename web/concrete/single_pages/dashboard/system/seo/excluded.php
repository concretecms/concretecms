<?php defined('C5_EXECUTE') or die('Access Denied');
$form = Loader::helper('form'); ?>
<form method="post" id="url-form" action="<?php echo $view->action('save')?>">
		<div class="control-group">
			<textarea style='width:100%;height:100px' name='SEO_EXCLUDE_WORDS'><?=$SEO_EXCLUDE_WORDS?></textarea>
		</div>
		<div class="alert alert-info"><?=t('Separate reserved words with a comma. These words will be automatically removed from URL slugs. To remove no words from URLs, delete all the words above.')?></div>
	    <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <?php if (count($SEO_EXCLUDE_WORDS_ORIGINAL_ARRAY) != count($SEO_EXCLUDE_WORDS_ARRAY) || !$SEO_EXCLUDE_WORDS) { ?>
                    <a href="<?=$view->action('reset')?>" class="btn btn-default pull-left"><?=t('Reset To Default')?></a>
                <?php } ?>
                    <?php echo $interface->submit(t('Save'), null, 'right', 'btn-primary');?>
            </div>
        </div>
</form>
