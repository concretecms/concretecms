<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Url;

/**
 * @var Concrete\Core\Localization\Service\Date $dh
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Form\Service\Widget\UserSelector $form_user_selector
 * @var Concrete\Core\User\Point\EntryList $upEntryList
 * @var Concrete\Core\User\Point\Entry[] $entries
 * @var Pagerfanta\Pagerfanta $pagination
 */

?>
<div class="ccm-dashboard-header-buttons">
    <a href="<?= Url::to('/dashboard/users/points/assign') ?>" class="btn btn-primary"><?= t('Add Points') ?></a>
</div>

<form action="<?= $view->action('view')?>" method="get">
    <div class="ccm-pane-options">
        <div class="ccm-pane-options-permanent-search">
            <?= $form->label('uID', t('User')) ?>
            <?= $form_user_selector->quickSelect('uID') ?>
        </div>
    </div>

    <div class="clearfix" style="margin-top: 30px;">
        <input type="submit" value="<?= t('Search') ?>" class="btn btn-primary float-right"/>
    </div>
</form>
<?php
if (count($entries) > 0) {
    ?>
<br>
<div class="table-responsive">
	<table id="ccm-product-list" class="ccm-search-results-table compact-results">
    	<thead>
    		<tr>
                <th class="<?=$upEntryList->getSortClassName('u.uName')?>"><a href="<?=$upEntryList->getSortURL('u.uName')?>"><?= t('User') ?></a></th>
                <th class="<?=$upEntryList->getSortClassName('upa.upaName')?>"><a href="<?=$upEntryList->getSortURL('upa.upaName')?>"><?=t('Action')?></a></th>
                <th class="<?=$upEntryList->getSortClassName('uph.upPoints')?>"><a href="<?=$upEntryList->getSortURL('uph.upPoints')?>"><?=t('Points')?></a></th>
                <th class="<?=$upEntryList->getSortClassName('uph.timestamp')?>"><a href="<?=$upEntryList->getSortURL('uph.timestamp')?>"><?=t('Date Assigned')?></a></th>
                <th><span><?=t('Details')?></span></th>
                <th></th>
            </tr>
    	</thead>
        <tbody>
        <?php
            foreach ($entries as $up) {
                $ui = $up->getUserPointEntryUserObject();
                $action = $up->getUserPointEntryActionObject();
                ?>
                <tr>
                    <td><?= is_object($ui) ? h($ui->getUserName()) : '' ?></td>
                    <td><?= is_object($action) ? h($action->getUserPointActionName()) : '' ?></td>
                    <td><?= number_format($up->getUserPointEntryValue()) ?></td>
                    <td><?= $dh->formatDateTime($up->getUserPointEntryTimestamp()) ?></td>
                    <td><?= $up->getUserPointEntryDescription() ?></td>
                    <td class="text-right">
                        <?php
                            $deleteUrl = \Concrete\Core\Url\Url::createFromUrl($view->action('deleteEntry', $up->getUserPointEntryID()));
                            $deleteUrl->setQuery([
                                'ccm_token' => $token->generate('delete_community_points'),
                            ]);
                        ?>
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
	<div id="ccm-list-none"><?=t('No entries found.')?></div>
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
