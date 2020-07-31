<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Url;

/**
 * @var Concrete\Core\Form\Service\Widget\DateTime $form_date_time
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Form\Service\Widget\UserSelector $form_user_selector
 * @var int $upUser
 * @var int $upaID
 * @var array $userPointActions
 * @var string $upPoints
 * @var string $upComments
 * @var string $timestamp
 */
?>
<form method="post" action="<?= $view->action('save') ?>" id="ccm-community-point-entry">
	<?php
        $token->output('add_community_points');
        if (isset($upID) && $upID > 0) {
            echo $form->hidden('upID', $upID);
        }
    ?>
	<div class="form-group">
	    <?= $form->label('upUser', t('User')) ?>
        <?= $form_user_selector->quickSelect('upUser', $upUser, ['autofocus' => 'autofocus']) ?>
	</div>

	<div class="form-group">
	    <?= $form->label('upaID', t('Action'))?>
        <?= $form->select('upaID', $userPointActions, $upaID, ['json-src' => $view->action('getJsonDefaultPointAction')]) ?>
	</div>

	<div class="form-group">
	    <?= $form->label('upPoints', t('Points')) ?>
        <?= $form->number('upPoints', $upPoints) ?>
	</div>

	<div class="form-group">
	    <?= $form->label('upComments', t('Comments')) ?>
		<?= $form->textarea('upComments', $upComments) ?>
	</div>

	<div class="form-group">
	    <?= $form->label('dtoverride', t('Override Timestamp'))?>
		<?= $form_date_time->datetime('dtoverride', $timestamp, true); ?>
	</div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?= Url::to('/dashboard/users/points') ?>" class="btn btn-secondary float-left"><?=t('Back to List')?></a>
            <button type="submit" class="btn btn-primary float-right"><?=t('Assign')?></button>
        </div>
    </div>
</form>

<script type="text/javascript">
$(function() {
	$('#upaID').change(function() {
		var src = $('#upaID').attr('json-src')+'/-/'+$('#upaID').val();
		$.getJSON(src,function(j) {
			$('#upPoints').val(j);
		});
	});
});
</script>
