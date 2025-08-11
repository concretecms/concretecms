<?php

use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var Concrete\Core\Page\Type\Composer\Control\Control $control
 * @var Concrete\Core\Form\Service\Form $form
 * @var string $label
 * @var string $description
 */

$draft = $control->getPageObject();
$element = $form->text($this->field('url_slug'), $control->getPageTypeComposerControlDraftValue());
$resolverManager = app(ResolverManagerInterface::class);
?>

<div class="form-group ccm-composer-url-slug" data-composer-field="url_slug" style="position: relative">
    <?= $form->label('', $label) ?>
    <?php if ($control->isPageTypeComposerControlRequiredByDefault() || $control->isPageTypeComposerFormControlRequiredOnThisRequest()) { ?>
        <span class="label label-info"><?= t('Required') ?></span>
    <?php } ?>

	<?php if ($description) { ?>
        <i class="fas fa-question-circle launch-tooltip" data-bs-toggle="tooltip" title="<?= h($description); ?>"></i>
	<?php } ?>

    <div>
        <i class="fas fa-sync fa-spin ccm-composer-url-slug-loading"></i>
        <?php
        if (is_object($draft) && !$draft->isPageDraft()) {
            ?>
            <div><a href="#" class="icon-link" data-composer-field="edit_url_slug"><i class="fas fa-pencil-alt"></i></a> <span><?= $control->getPageTypeComposerControlDraftValue() ?></span></div>
        <?php
        } else {
            echo $element;
        }
        ?>
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
    var slugHTML = '<?= addslashes($element) ?>';
    $(function() {
        $('a[data-composer-field=edit_url_slug]').on('click', function(e) {
            e.preventDefault();
            $(this).parent().replaceWith(slugHTML);
        });
        var $urlSlugField = $('div[data-composer-field=url_slug] input');
        if ($urlSlugField.length) {
            $('div[data-composer-field=name] input').on('input', function() {
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
                concreteComposerAddPageTimer = setTimeout(function() {
                    $('.ccm-composer-url-slug-loading').show();
                    $.post(
                        <?= json_encode((string) $resolverManager->resolve(['/ccm/system/page/url_slug'])) ?>,
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
