<?php defined('C5_EXECUTE') or die('Access Denied.');
/** @var \Concrete\Core\Form\Service\Form $form */
/** @var \Concrete\Core\Page\View\PageView $view */
/** @var \Concrete\Core\Validation\CSRF\Token $validation_token */

$topicTreeName = $topicTreeName ?? null;
?>

<form method="post" action="<?php echo $view->action('submit'); ?>" class="form-horizontal">
<?php if (\Concrete\Core\Permission\Key\Key::getByHandle('add_topic_tree')->validate()) { ?>
	<?php echo $validation_token->output('submit'); ?>
	<div class="form-group">
		<?php echo $form->label('topicTreeName', t('Tree Name')); ?>
		<?php echo $form->text('topicTreeName', $topicTreeName); ?>
	</div>
<?php } else { ?>
	<p><?php echo t('You may not add topic trees.'); ?></p>
<?php } ?>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions ">
            <a href="<?php echo $view->action('/dashboard/system/attributes/topics'); ?>" class="btn btn-secondary float-start"><?php echo t('Cancel'); ?></a>
            <button type="submit" class="btn btn-primary float-end"><?php echo t('Add Topic Tree'); ?></button>
        </div>
    </div>
</form>
