<?php
defined('C5_EXECUTE') or die('Access Denied.');
use Concrete\Core\User\Group\GroupList;
$form = Core::make('helper/form');
?>

<form class="ccm-dashboard-content-form" method="post" action="<?= $view->action('process') ?>">
    <?= $token->output('send_message') ?>
    <div class="form-group">
        <?= $form->label('subject', t('Subject')) ?>
        <?= $form->text('subject', $subject, ['class' => 'form-control']) ?>
    </div>
    <div class="form-group">
        <?php
        echo $form->label('group', t('Select a Group'));
        echo $form->select('group', $groups, $group, ['class' => 'form-control']);
        ?>
    </div>
    <div class="form-group">
        <?= $form->label('message', t('Message')) ?>
        <?= $form->textArea('message', $message, ['class' => 'form-control']) ?>
    </div>
    <div class="form-group">
        <button class="pull-right btn btn-primary" type="submit"><i class="fa fa-send"></i> <?= t('Send Message') ?></button>
    </div>
</form>
