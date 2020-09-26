<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<ul class="ccm-inline-toolbar ccm-ui" data-inline-toolbar="board">
    <li><label><a target="_blank" href="<?php echo URL::to('/dashboard/boards/instances/details', $instance->getBoardInstanceID())?>">
        <?php echo $instance->getBoardInstanceName()?>
    </a></li>
    <li class="ccm-inline-toolbar-icon-cell">
        <a href="javascript:void(0);"
           title="<?php echo t('Update or regenerate this board instance.') ?>">
            <i class="fas fa-sync-alt"></i>
        </a>

        <div class="ccm-dropdown-menu">
            <div class="row">
                <div class="col-sm-12">
                    <?php /* @TODO - make the Add Content command work more reliably and then expose this functionality
                    <h4><?=t('Update')?></h4>
                    <p><?=t('Adds new items to the board and refreshes content.')?></p>
                    <button class="btn btn-secondary" type="button" data-board-button="update"><?=t('Update')?></button>
                    <hr> */ ?>
                    <h4><?=t('Regenerate')?></h4>
                    <p><?=t('Updates the display of this board.')?></p>
                    <button class="btn btn-secondary" type="button" data-board-button="regenerate"><?=t('Regenerate')?></button>
                </div>
            </div>
        </div>
    </li>
    <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-save">
        <button type="button" data-toolbar-button-action="exit-board"><?= t("Done") ?></button>
    </li>
</ul>

<?php
$renderer->render($instance);
?>

<script type="text/javascript">
    $(function () {
        $('button[data-toolbar-button-action=exit-board]').on('click', function () {
            ConcreteEvent.fire('EditModeExitInline');
        });

        var $toolbar = $('ul[data-inline-toolbar=board]');
        $toolbar.find('.ccm-inline-toolbar-icon-cell > a').on('click', function () {
            const $dropdown = $(this).parent().find('.ccm-dropdown-menu')
            const isActive = $dropdown.hasClass('active')
            $toolbar.find('.ccm-inline-toolbar-icon-selected').removeClass('ccm-inline-toolbar-icon-selected')

            $('.ccm-dropdown-menu').removeClass('active')

            if (!isActive) {
                $dropdown.addClass('active')
                $(this).parent().addClass('ccm-inline-toolbar-icon-selected')
            }
        })

        $toolbar.on('click', 'button[data-board-button=update]', function() {
            $.concreteAjax({
                url: '<?=$view->action('update')?>',
                data: {
                    ccm_token: '<?= $token->generate('update') ?>'
                },
                success: function(r) {
                    window.location.reload()
                }
            })
            return false
        })

        $toolbar.on('click', 'button[data-board-button=regenerate]', function() {
            $.concreteAjax({
                url: '<?=$view->action('regenerate')?>',
                data: {
                    ccm_token: '<?= $token->generate('regenerate') ?>'
                },
                success: function(r) {
                    window.location.reload()
                }
            })
            return false
        })



        Concrete.Vue.activateContext('cms', function (Vue, config) {
            new Vue({
                el: 'div[data-vue=board]',
                components: config.components
            })
        })

    });
</script>