<?php
defined('C5_EXECUTE') or die("Access Denied.");
$view->inc('elements/header.php');

$image = date('Ymd') . '.jpg';

if (Config::get('concrete.white_label.background_image') !== 'none' && !Config::get('concrete.white_label.background_url')) {
    $imagePath = Config::get('concrete.urls.background_feed') . '/' . $image;
    $imageData = Core::getApplicationURL() . '/' . DISPATCHER_FILENAME . '/tools/required/dashboard/get_image_data';

} else if (Config::get('concrete.white_label.background_url')) {
    $imagePath = Config::get('concrete.white_label.background_url');
}
?>

<div class="ccm-dashboard-desktop">
    <div class="ccm-dashboard-content-full">

        <div class="ccm-dashboard-welcome">
            <h1><div class="ccm-dashboard-welcome-inner"><?=t('Welcome Back')?>
                <?php if (isset($imageData)) { ?>
                    <a href="#" class="launch-tooltip" title="<?=t('View Original Image')?>"><i class="fa fa-image"></i></a>
                <?php } ?>
                </div>
            </h1>
        </div>

        <nav class="ccm-dashboard-desktop-navbar navbar navbar-default">
            <div class="container-fluid">

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li><p class="navbar-text"><?=Core::make('date')->formatCustom('l, M dS g:ia')?></p></li>
                    </ul>
                    <form method="post" data-form="check-in" action="<?=$approveAction?>">

                        <input type="hidden" name="cID" value="<?=$c->getCollectionID()?>">
                        <input type="hidden" name="action" value="publish">
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <?php if ($c->isEditMode()) {
                                ?>
                                <a href="#" id="ccm-dashboard-welcome-check-in"><?=t('Save Changes')?></a>
                                <?php
                            }
                            ?>
                            <?php if (!$c->isEditMode()) {
                                ?><a href="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$c->getCollectionID()?>&ctask=check-out&<?=$token->getParameter()?>" id="ccm-nav-check-out"><?=t('Edit Page')?></a><?php
                            }
                            ?>
                        </li>
                    </ul>
                    </form>
                </div>
            </div>
        </nav>


    </div>

    <?php
    $a = new Area('Main');
    $a->setAreaGridMaximumColumns(12);
    $a->display($c); ?>

</div>

<?php if (isset($imagePath)) { ?>
    <style type="text/css">
        div.ccm-dashboard-welcome {
            background-image: url(<?=$imagePath?>);
        }
    </style>
<?php } ?>

<?php if (isset($imageData)) { ?>
    <script type="text/javascript">
        $(function() {
            $.getJSON('<?=$imageData?>', { image: '<?= $image ?>' }, function (data) {
               $('.ccm-dashboard-welcome-inner a').attr('href', data.link);
            });

            $('#ccm-dashboard-welcome-check-in').on('click', function(e) {
                e.preventDefault();
                $(this).closest('form').submit();
            });

            $('form[data-form=check-in]').concreteAjaxForm();

            ConcreteEvent.on('AddBlockListAddBlock', function(event, data) {
                var editor = Concrete.getEditMode();
                var area = editor.getNextBlockArea();
                blockType = new Concrete.BlockType(data.$launcher, editor);
                blockType.addToDragArea(_.last(area.getDragAreas()));
            });

            ConcreteEvent.on('EditModeAfterInit', function(event, data) {
                var areas = data.editMode.getAreas();
                _.each(areas, function(area) {
                    area.bindEvent("EditModeAddBlocksToArea.area",
                        function(e, myData) {
                            if (myData.area === area) {
                                var arHandle = myData.area.getHandle();
                                $.fn.dialog.open({
                                    width: 550,
                                    height: 380,
                                    title: '<?=t('Add Block')?>',
                                    href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/add_block_list?cID=<?=$c->getCollectionID()?>&arHandle=' + encodeURIComponent(arHandle)});
                            }
                        }
                    )
                });
            });
        });
    </script>
<?php } ?>




<?php $view->inc('elements/footer.php'); ?>