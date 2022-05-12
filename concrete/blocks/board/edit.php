<?php defined('C5_EXECUTE') or die('Access Denied.');
use Concrete\Core\Support\Facade\Url;

/** @var \Concrete\Core\Block\View\BlockView $view */
/** @var \Concrete\Block\Board\Controller $controller */
/** @var Concrete\Core\Validation\CSRF\Token $validation_token */
/** @var \Concrete\Core\Entity\Board\Instance|null $instance */
/** @var \Concrete\Core\Board\Instance\Renderer|null $renderer */
$renderer = $renderer ?? null;
$instance = $instance ?? null;
$checker = null;
$boardInstanceId = 0;
if (is_object($instance)) {
    $instanceName = $instance->getBoardInstanceName();
    $checker = new \Concrete\Core\Permission\Checker($instance->getBoard());
    $boardInstanceId = $instance->getBoardInstanceID();
}
$instanceName = $instanceName ?? t('(No Name)');

?>
<ul class="ccm-inline-toolbar ccm-ui" data-inline-toolbar="board">
    <li><a target="_blank" href="<?php echo Url::to('/dashboard/boards/instances/details', $boardInstanceId)?>">
        <?=t('Instance: %s', $instanceName)?>
    </a></li>
    <?php
    /** @phpstan-ignore-next-line */
    if (is_object($checker) && $checker->canEditBoardContents()) { ?>
        <li class="ccm-inline-toolbar-icon-cell">
            <a href="javascript:void(0);"
               data-board-button="schedule"
               title="<?php echo t('Schedule content in this board.') ?>">
                <i class="fas fa-calendar"></i>
            </a>
        </li>
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
    <?php } ?>
    <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-save">
        <button type="button" data-toolbar-button-action="exit-board" class="btn btn-primary"><?= t('Done') ?></button>
    </li>
</ul>

<?php
if (is_object($renderer)) {
    $renderer->render($instance);
}

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
                    ccm_token: '<?= $validation_token->generate('update') ?>'
                },
                success: function(r) {
                    window.location.reload()
                }
            })
            return false
        })

        $toolbar.on('click', 'a[data-board-button=schedule]', function() {
            $.fn.dialog.open({
                href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/boards/schedule/' + <?=$boardInstanceId?>,
                width: '820',
                height: '600',
                title: '<?=t('Scheduled Content')?>'
            })
            return false
        })

        $toolbar.on('click', 'button[data-board-button=regenerate]', function() {
            $.concreteAjax({
                url: '<?=$view->action('regenerate')?>',
                data: {
                    ccm_token: '<?= $validation_token->generate('regenerate') ?>'
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