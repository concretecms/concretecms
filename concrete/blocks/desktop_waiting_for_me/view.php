<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-block-desktop-waiting-for-me">

    <h3><?=t('Waiting on Me')?></h3>

    <div class="ccm-block-desktop-waiting-for-me-inner">

        <?php if (count($items)) {

            foreach($items as $wp) {
                $wf = $wp->getWorkflowObject();
                $request = $wp->getWorkflowRequestObject();
                $description = $request->getWorkflowRequestDescriptionObject();
                $author = $request->getRequesterUserObject();
                $actions = $wp->getWorkflowProgressActions();

        ?>

            <div class="ccm-block-desktop-waiting-for-me-item" data-workflow-item-id="<?=$wp->getWorkflowProgressID()?>">
            <form action="<?=$wp->getWorkflowProgressFormAction()?>" method="post">

                    <div class="ccm-block-desktop-waiting-for-me-icon">
                        <?php print $request->getRequestIconElement() ?>
                    </div>

                    <div class="ccm-block-desktop-waiting-for-me-details">
                        <div class="ccm-block-desktop-waiting-for-me-description">
                            <?php print $description->getDescription() ?>
                        </div>

                        <?php if (is_object($author)) { ?>
                        <div class="ccm-block-desktop-waiting-for-me-about">
                        <?php $comment = $request->getRequesterComment();
                        if ($comment) { ?>
                            <?=t('Author Comment,')?>

                            <span class="ccm-block-desktop-waiting-for-me-author">
                                <a target="_blank" href="<?=URL::to('/dashboard/users/search', 'view', $author->getUserID())?>"><?=$author->getUserDisplayName()?></a>:
                            </span>

                            <div class="ccm-block-desktop-waiting-for-me-author-comment">
                                <?=$comment?>
                            </div>

                        <?php } else { ?>

                            <?=t('Requested By')?>

                            <span class="ccm-block-desktop-waiting-for-me-author">
                                <a href=""><?=$author->getUserDisplayName()?></a>.
                            </span>

                            <?php } ?>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="ccm-block-desktop-waiting-for-me-menu">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-chevron-down"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <?php
                                foreach($actions as $act) {

                                    $attribs = '';
                                    $_attribs = $act->getWorkflowProgressActionExtraButtonParameters();
                                    foreach ($_attribs as $key => $value) {
                                        $attribs .= $key . '="' . $value . '" ';
                                    }
                                    $br = '';
                                    $bl = '';
                                    if ($act->getWorkflowProgressActionStyleInnerButtonLeftHTML()) {
                                        $bl = $act->getWorkflowProgressActionStyleInnerButtonLeftHTML() . '&nbsp;&nbsp;';
                                    }
                                    if ($act->getWorkflowProgressActionStyleInnerButtonRightHTML()) {
                                        $br = '&nbsp;&nbsp;' . $act->getWorkflowProgressActionStyleInnerButtonRightHTML();
                                    }
                                    if ($act->getWorkflowProgressActionURL() != '') {
                                        echo '<li><a href="' . $act->getWorkflowProgressActionURL() . '&source=dashboard" ' . $attribs . ' class="' . $act->getWorkflowProgressActionStyleClass() . '">' . $act->getWorkflowProgressActionLabel() . '</a></li>';
                                    } else {
                                        echo '<li><a data-workflow-task="' . $act->getWorkflowProgressActionTask() . '" href="#" ' . $attribs . '>' . $act->getWorkflowProgressActionLabel() . '</a></li>';
                                    }
                                } ?>
                            </ul>
                        </div>
                    </div>

                </form>
                </div>

            <?php } ?>

        <?php } else { ?>

            <p><?=t('There are no items that currently need your attention.')?></p>

        <?php } ?>

    </div>

</div>

<script type="text/javascript">
    $(function() {
        $('a[data-workflow-task]').on('click', function(e) {
            var action = $(this).attr('data-workflow-task'),
                $form = $(this).closest('form');
            e.preventDefault();
            $form.append('<input type="hidden" name="action_' + action + '" value="' + action + '">');
            $form.submit();
        });

        $('.ccm-block-desktop-waiting-for-me form').ajaxForm({
            dataType: 'json',
            beforeSubmit: function() {
                jQuery.fn.dialog.showLoader();
            },
            success: function(r) {
                var wpID = r.wpID;
                $('div[data-workflow-item-id=' + wpID + ']').addClass('animated fadeOut');
                jQuery.fn.dialog.hideLoader();
                setTimeout(function() {
                    $('div[data-workflow-item-id=' + wpID + ']').remove();
                    if (!$('div[data-workflow-item-id]').length) {
                        $(".ccm-block-desktop-waiting-for-me-inner").html('<p><?=t('There are no items that currently need your attention.')?></p>');
                    }
                }, 500);
            }
        });

        $('div.ccm-block-desktop-waiting-for-me-menu').on('show.bs.dropdown', function () {
            $(this).closest('.ccm-block-desktop-waiting-for-me-item').addClass('ccm-block-desktop-waiting-for-me-menu-active');
            $(this).closest('.ccm-block-desktop-waiting-for-me-item')
                .find('i.fa-chevron-down')
                .removeClass('fa-chevron-down')
                .addClass('fa-chevron-up');
        })

        $('div.ccm-block-desktop-waiting-for-me-menu').on('hide.bs.dropdown', function () {
            $(this).closest('.ccm-block-desktop-waiting-for-me-item').removeClass('ccm-block-desktop-waiting-for-me-menu-active');
            $(this).closest('.ccm-block-desktop-waiting-for-me-item')
                .find('i.fa-chevron-up')
                .removeClass('fa-chevron-up')
                .addClass('fa-chevron-down');
        })



    });
</script>
