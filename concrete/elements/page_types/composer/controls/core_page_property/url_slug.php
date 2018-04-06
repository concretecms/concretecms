<?php
defined('C5_EXECUTE') or die("Access Denied.");
(is_object($draft) && !$draft->isPageDraft() || Config::get('concrete.seo.auto_update_url_slug')) ? $showInEditMode = true : $showInEditMode = false;
$draft = $control->getPageObject();
?>

<div class="form-group ccm-composer-url-slug" data-composer-field="url_slug" style="position: relative" data-auto-update="<?=Config::get('concrete.seo.auto_update_url_slug')?>">
	<label class="control-label"><?=$label?></label>
    <?php if ($control->isPageTypeComposerControlRequiredByDefault() || $control->isPageTypeComposerFormControlRequiredOnThisRequest()) : ?>
        <span class="label label-info"><?= t('Required') ?></span>
    <?php endif; ?>
	<?php if ($description): ?>
	<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?=$description?>"></i>
	<?php endif; ?>

    <?php
    $element = $form->text($this->field('url_slug'), $control->getPageTypeComposerControlDraftValue(), array('class' => 'span4'));
    ?>
    <div>
        <i class="fa-refresh fa-spin fa ccm-composer-url-slug-loading"></i>
        <?php if ( !$showInEditMode ) {
    ?>
            <div><a href="#" class="icon-link" data-composer-field="edit_url_slug"><i class="fa fa-pencil"></i></a> <span><?=$control->getPageTypeComposerControlDraftValue()?></span></div>
        <?php 
} else {
    ?>
            <?=$element?>
        <?php 
} ?>
    </div>
</div>

<style type="text/css">
    div.ccm-composer-url-slug {
        position: relative;
    }

    div.ccm-composer-url-slug i.ccm-composer-url-slug-loading {
        position: absolute; top: 35px; right: 10px; display: none;
    }
</style>

<script type="text/javascript">
    var slugHTML = '<?=addslashes($element)?>';
    $(function() {
        $('a[data-composer-field=edit_url_slug]').on('click', function(e) {
            e.preventDefault();
            $(this).parent().replaceWith(slugHTML);
        });
        var $urlSlugField = $('div.ccm-composer-url-slug input');
        var $nameField = $('div[data-composer-field=name] input');
        var autoUpdate = $urlSlugField.data('auto-update');
        if ($urlSlugField.length && autoUpdate == true) {
            $nameField.on('input', function() {
                var input = $(this);
                var send = {
                    token: '<?=Loader::helper('validation/token')->generate('get_url_slug')?>',
                    name: input.val()
                };
                var parentID = input.closest('form').find('input[name=cParentID]').val();
                if (parentID) {
                    send.parentID = parentID;
                }
                clearTimeout(concreteComposerAddPageTimer);
                var concreteComposerAddPageTimer = setTimeout(function() {
                    $('.ccm-composer-url-slug-loading').show();
                    $.post(
                        '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/url_slug',
                        send,
                        function(r) {
                            $('.ccm-composer-url-slug-loading').hide();
                            $urlSlugField.val(r);
                        }
                    );
                }, 150);
            });
        }
    });
</script>
