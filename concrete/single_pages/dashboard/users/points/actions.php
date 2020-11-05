<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Url;

/**
 * @var Concrete\Core\User\Point\Action\ActionList $actionList
 * @var Pagerfanta\Pagerfanta $pagination
 * @var bool $showForm
 * @var int $upaID
 * @var bool $upaHasCustomClass
 * @var string $upaHandle
 * @var string $upaName
 * @var string $upaDefaultPoints
 * @var array $badges
 * @var int $gBadgeID
 * @var array $actions
 * @var int $upaIsActive
 */

if ($showForm) {
    ?>
<form method="post" action="<?= $view->action('save') ?>" id="ccm-community-points-action">
    <?php $token->output('add_action') ?>
    <div class="row">
        <div class="col-md-12">
            <?= $form->hidden('upaID', $upaID) ?>

        	<div class="form-check">
                <?= $form->checkbox('upaIsActive', 1, ($upaIsActive == 1 || (!$upaID))) ?>
                <?= $form->label('upaIsActive', t('Enabled'), ['class' => 'form-check-label']) ?>
            </div>

        	<div class="form-group">
        	    <?= $form->label('upaHandle', t('Action Handle')) ?>
                <?= $form->text('upaHandle', $upaHandle, $upaHasCustomClass ? ['disabled' => 'disabled'] : []) ?>
        	</div>

        	<div class="form-group">
        	    <?= $form->label('upaName', t('Action Name')) ?>
        		<?= $form->text('upaName', $upaName) ?>
        	</div>

        	<div class="form-group">
                <?= $form->label('upaDefaultPoints', t('Default Points')) ?>
        		<?= $form->number('upaDefaultPoints', $upaDefaultPoints) ?>
        	</div>

        	<div class="form-group">
        	    <?= $form->label(
        'gBadgeID',
        t('Badge Associated') . ' <i class="fas fa-question-circle launch-tooltip" title="' . t('If a badge is assigned to this action, the first time this user performs this action they will be granted the badge.') . '"></i>'
    )
                ?>
        		<?= $form->select('gBadgeID', $badges, $gBadgeID) ?>
        	</div>

            <?php
                $label = t('Add Action');
                if ($upaID > 0) {
                    $label = t('Update Action');
                }
            ?>

            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <a href="<?= Url::to('/dashboard/users/points/actions') ?>" class="btn btn-secondary float-left"><?=t('Back to List')?></a>
                    <button class="btn btn-primary float-right" type="submit"><?= $label ?></button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php
} else {
    ?>
	<div class="ccm-dashboard-header-buttons">
	    <a href="<?=$view->action('add')?>" class="btn btn-primary"><?=t('Add Action')?></a>
	</div>

	<?php
    if (count($actions) > 0) {
        ?>
        <div class="table-responsive">
			<table class="ccm-search-results-table compact-results">
    			<thead>
    				<tr>
                        <th><span><?=t('Active')?></span></th>
                        <th class="<?=$actionList->getSortClassName('upa.upaName')?>"><a href="<?=$actionList->getSortURL('upa.upaName', 'desc')?>"><?=t('Action Name')?></a></th>
                        <th class="<?=$actionList->getSortClassName('upa.upaHandle')?>"><a href="<?=$actionList->getSortURL('upa.upaHandle')?>"><?=t('Action Handle')?></a></th>
                        <th class="<?=$actionList->getSortClassName('upa.upaDefaultPoints')?>"><a href="<?=$actionList->getSortURL('upa.upaDefaultPoints')?>"><?=t('Default Points')?></a></th>
                        <th class="<?=$actionList->getSortClassName('upa.gBadgeID')?>"><a href="<?=$actionList->getSortURL('upa.gBadgeID')?>"><?=t('Group')?></a></th>
                        <th></th>
                    </tr>
    			</thead>

                <tbody>
            		<?php
                    foreach ($actions as $upa) {
                        ?>
                        <tr class="">
                            <td style="text-align: center"><?php if ($upa['upaIsActive']) { ?><i class="fas fa-check"></i><?php } ?></td>
                            <td><?= h($upa['upaName']) ?></td>
                            <td><?= h($upa['upaHandle']) ?></td>
                            <td><?= number_format($upa['upaDefaultPoints']) ?></td>
                            <td><?= h($upa['gName']) ?></td>
                            <td class="text-right">
                                <?php
                                    $deleteUrl = \Concrete\Core\Url\Url::createFromUrl($view->action('delete', $upa['upaID']));
                                    $deleteUrl->setQuery([
                                        'ccm_token' => $token->generate('delete_action'),
                                    ]);
                                ?>
                                <a href="<?= $view->action($upa['upaID']) ?>" class="btn btn-sm btn-secondary"><?= t('Edit') ?></a>
                                <a href="<?= $deleteUrl ?>" class="btn btn-sm btn-danger"><?= t('Delete') ?></a>
                            </td>
                        </tr>
            		<?php
                    }
                    ?>
                </tbody>
    		</table>
        </div>
		<?php
    } else {
        ?>
			<p><?=t('No Actions found.')?></p>
		<?php
    }
    ?>

    <div class="ccm-search-results-pagination">
        <?php
            if ($pagination->haveToPaginate()) {
                echo $pagination->renderView('dashboard');
            }
        ?>
    </div>

<?php
} ?>
