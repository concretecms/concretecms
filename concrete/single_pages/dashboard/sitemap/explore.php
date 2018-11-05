<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Controller\SinglePage\Dashboard\Sitemap\Explore $controller */
/* @var Concrete\Core\Application\Service\Dashboard $dashboard */
/* @var Concrete\Core\Application\Service\Dashboard\Sitemap $dh */
/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\Html\Service\Html $html */
/* @var Concrete\Core\Application\Service\UserInterface $interface */
/* @var Concrete\Core\Page\View\PageView $this */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Page\View\PageView $view */

/* @var bool $includeSystemPages */
/* @var int $nodeID */

if ($dh->canRead()) {
    ?>
    <script>
    (function () {
        var my_url = <?= json_encode($view->action('')) ?>;
        $(function () {
            $('div#ccm-flat-sitemap-container').concreteSitemap({
                displayNodePagination: true,
                cParentID: '<?=$nodeID?>',
                displaySingleLevel: true,
                includeSystemPages: <?= $includeSystemPages ? 'true' : 'false' ?>,
                persist: false,
                onDisplaySingleLevel: function (node) {
                    if (window && window.history && window.history.pushState) {
                        window.history.pushState(
                            {
                                key: node.data.cID
                            },
                            'title',
                            my_url + '/-/' + node.data.cID
                        );
                    }
                }
            });
        });
        $(window).on('popstate', function (event) {
            var redirect;
            if (event.originalEvent.state && event.originalEvent.state.key) {
                redirect = my_url + '/-/' + event.originalEvent.state.key;
            } else {
                return true;
            }
            window.location = redirect;
            $.fn.dialog.showLoader();
            return false;
        });
    }());
    </script>
    <?php
}
?>
<?= $dashboard->getDashboardPaneHeaderWrapper(t('Sitemap'), t('Sitemap flat view lets you page through particular long lists of pages.'), 'span10 offset1', false) ?>
<div class="ccm-pane-body">
    <?php
    if ($dh->canRead()) {
        ?><div id="ccm-flat-sitemap-container" data-sitemap="container"></div><?php 
    } else {
        ?><p><?= t('You do not have access to the dashboard sitemap.') ?></p><?php 
    }
    ?>
</div>
<div class="ccm-pane-footer" id="ccm-explore-paging-footer">
</div>
<?= $dashboard->getDashboardPaneFooterWrapper(false) ?>

