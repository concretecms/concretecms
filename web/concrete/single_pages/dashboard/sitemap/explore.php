<?php defined('C5_EXECUTE') or die("Access Denied.");
$view = View::getInstance();
?>

    <style type="text/css">
        div.ccm-sitemap-explore ul li.ccm-sitemap-explore-paging {
            display: none;
        }
    </style>

    <script type="text/javascript">
        (function () {
            var my_url = '<?= $view->action(''); ?>';
            $(function () {
                $('div#ccm-flat-sitemap-container').concreteSitemap({
                    displayNodePagination: true,
                    cParentID: '<?=$nodeID?>',
                    displaySingleLevel: true,
                    onDisplaySingleLevel: function (node) {
                        if (window && window.history && window.history.pushState) {
                            window.history.pushState({
                                key: node.data.key
                            }, 'title', my_url + '/-/' + node.data.key);
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

<?= Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(
    t('Sitemap'),
    t(
        'Sitemap flat view lets you page through particular long lists of pages.'),
    'span10 offset1',
    false); ?>
    <div class="ccm-pane-body">

        <?php if ($dh->canRead()) {
    ?>

            <div id="ccm-flat-sitemap-container" data-sitemap="container"></div>

        <?php 
} else {
    ?>
            <p><?= t('You do not have access to the dashboard sitemap.') ?></p>
        <?php 
} ?>

    </div>
    <div class="ccm-pane-footer" id="ccm-explore-paging-footer">

    </div>

    <script type="text/javascript">
        $(function () {
            $('#ccm-explore-paging-footer').html($('li.ccm-sitemap-explore-paging').html());
        });
    </script>
<?= Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);
