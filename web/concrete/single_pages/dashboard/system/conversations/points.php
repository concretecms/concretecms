<?php defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
?>
<h4><?=t('Installed Rating Types')?></h4>
<? if (count($ratingTypes) > 0) { ?>
    <form action="<?=$view->action('save')?>" method="post">
        <table class="table">
            <thead>
                <tr>
                    <th><?=t('Name')?></th>
                    <th><?=t('Point Value')?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ratingTypes as $ratingType) { ?>
                    <tr>
                        <td><?=$ratingType->getConversationRatingTypeDisplayName();?></td>
                        <td><?=$form->number('rtPoints_' . $ratingType->getConversationRatingTypeID(), $ratingType->cnvRatingTypeCommunityPoints, array('style' => 'width: 100px'))?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="3"><?=$form->submit('save', t('Save'), array(), 'btn-primary pull-right')?>
            </tbody>
        </table>
    </form>
<? } else { ?>
    <p><?=t('There are no Community Points Rating Types installed.')?></p>
<? } ?>
