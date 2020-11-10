<?php

use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Controller\Panel\Detail\Page\Composer $controller */
/* @var Concrete\Core\View\DialogView $view */
/* @var Concrete\Core\User\User $u */

/* @var Concrete\Core\Application\Service\Composer $composer */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Page\Type\Type $pagetype */
/* @var string $saveURL */
/* @var string $viewURL */
/* @var Concrete\Core\Page\Page $c */
/* @var int $cID */
/* @var int|null $idleTimeout */

?>

<section class="ccm-ui">
    <header><h3><?= t('Composer - %s', $pagetype->getPageTypeDisplayName()); ?></h3></header>
    <div class="row">
        <div class="col-sm-9">
            <form method="post" data-panel-detail-form="compose">
                <?php $composer->display($pagetype, $c); ?>
            </form>
        </div>
    </div>

    <div class="ccm-panel-detail-form-actions dialog-buttons">
        <?php $composer->displayButtons($pagetype, $c); ?>
    </div>
</section>

<script type="text/javascript">
var ConcretePageComposerDetail = {

    saving: false,
    saver: null,
    $form: $('form[data-panel-detail-form=compose]'),

    saveDraft: function(onComplete) {
        var my = this;
        my.$form.concreteAjaxForm({
            beforeSubmit: function() {
                my.saving = true;
            },
            url: <?= json_encode($controller->action('autosave')); ?>,
            success: function(r) {
                my.saving = false;
                $('#ccm-page-type-composer-form-save-status').html(r.message).show();
                if (onComplete) {
                    onComplete(r);
                }
            }
        }).submit();
    },

    enableAutosave: function() {
        if (this.saver) {
            this.saver.resetIdleTimer();
        }
    },

    disableAutosave: function() {
        if (this.saver) {
            this.saver.disableIdleTimer();
        }
    },

    updateWatchers: function() {
        var my = this,
            newElements = my.$form.find('button,input,keygen,output,select,textarea').not(my.watching);

        if (!my.watching.length) {
            newElements = newElements.add(my.$form);
        }

        my.watching = my.watching.add(newElements);

        if (this.saver) {
            newElements.bind('change', function() {
                my.saver.requestSave();
            });

            newElements.bind('keyup', function() {
                my.saver.requestSave(true);
            });
        }
    },

    start: function() {
        var my = this;
        my.watching = $();
        my.updateWatchers();

        <?php
        if ($idleTimeout) {
            ?>
            my.saver = my.$form
                .saveCoordinator(
                    function(coordinater, data, success) {
                        my.updateWatchers();
                        my.saveDraft(function() {
                            success();
                        });
                    },
                    {
                        idleTimeout: <?= $idleTimeout; ?>
                    }
                )
                .data('SaveCoordinator');
            <?php
        }
        ?>

        $('button[data-page-type-composer-form-btn=discard]').on('click', function() {
            if (confirm(<?= json_encode(t('This will remove this draft and it cannot be undone. Are you sure?')); ?>)) {
                my.disableAutosave();
                $.concreteAjax({
                    url: <?= json_encode($controller->action('discard')); ?>,
                    data: {cID: <?= $cID; ?>},
                    success: function(r) {
                        window.location.href = r.redirectURL;
                    }
                });
            }
        });

        $('button[data-page-type-composer-form-btn=preview]').on('click', function() {
            my.disableAutosave();
            function redirect() {
                window.location.href = <?= json_encode((string) app(ResolverManagerInterface::class)->resolve(["/ccm/system/page/checkout/{$cID}/-/" . $token->generate()])) ?>;
            }
            if (!my.saving) {
                my.saveDraft(redirect);
            } else {
                redirect();
            }
        });

        $('button[data-page-type-composer-form-btn=exit]').on('click', function() {
            my.disableAutosave();
            var submitSuccess = false;
            my.$form.concreteAjaxForm({
                url: <?= json_encode($controller->action('save_and_exit')); ?>,
                success: function(r) {
                    submitSuccess = true;
                    window.location.href = r.redirectURL;
                },
                complete: function() {
                    if (!submitSuccess) {
                        my.enableAutosave();
                    }
                    jQuery.fn.dialog.hideLoader();
                }
            }).submit();
        });

        $('button[data-page-type-composer-form-btn=publish]').on('click', function() {
            var data = my.$form.serializeArray();
            ConcreteEvent.fire('PanelComposerPublish', {data: data});
        });

        ConcreteEvent.subscribe('PanelCloseDetail',function(e, panelDetail) {
            if (panelDetail && panelDetail.identifier == 'page-composer') {
                my.disableAutosave();
            }
        });

        ConcreteEvent.subscribe('PanelComposerPublish',function(e, data) {
            // Disable the autosaver completely so that it is not posting a
            // request after the publish event has been called. This could
            // otherwise lead to an extra version being created for the page
            // after the publish action has been already called.
            my.saver.disable();
            var submitSuccess = false;
            $.concreteAjax({
                data: data.data,
                url: <?= json_encode($controller->action('publish')); ?>,
                success: function(r) {
                    submitSuccess = true;
                    window.location.href = r.redirectURL;
                },
                complete: function() {
                    if (!submitSuccess) {
                        my.enableAutosave();
                    }
                    jQuery.fn.dialog.hideLoader();
                }
            });
        });

        ConcreteEvent.subscribe('AjaxRequestError',function(r) {
            if (this.saver) {
                my.saver.disable();
            }
        });

        if (this.saver) {
            this.saver.enable();
        }
        my.enableAutosave();
    }

};

$(function() {
    ConcretePageComposerDetail.start();
});
</script>
