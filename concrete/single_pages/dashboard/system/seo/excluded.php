<?php defined('C5_EXECUTE') or die('Access Denied');
$form = $app->make('helper/form');
?>

<form method="post" id="url-form" action="<?php echo $view->action('save'); ?>">
    <?php echo $app->make('token')->output('excluded_words_save'); ?>

	<div class="form-group">
		<textarea class="form-control" style='width:100%; height:100px' name='SEO_EXCLUDE_WORDS'><?php echo $SEO_EXCLUDE_WORDS; ?></textarea>
	</div>
	<div class="alert alert-info"><?php echo t('Separate reserved words with a comma. These words will be automatically removed from URL slugs. To remove no words from URLs, delete all the words above.'); ?></div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
        <?php if (count($SEO_EXCLUDE_WORDS_ORIGINAL_ARRAY) != count($SEO_EXCLUDE_WORDS_ARRAY) || !$SEO_EXCLUDE_WORDS) { ?>
            <a href="<?php echo $view->action('reset'); ?>" class="btn btn-default pull-left"><?php echo t('Reset To Default'); ?></a>
        <?php } ?>
        <?php echo $interface->submit(t('Save'), null, 'right', 'btn-primary');?>
        </div>
    </div>
</form>
