<?php
defined('C5_EXECUTE') or die("Access Denied.");
$view->inc('elements/header.php');

$image = date('Ymd') . '.jpg';
$token = '&' . Core::make('token')->getParameter();

if (Config::get('concrete.white_label.background_image') !== 'none' && !Config::get('concrete.white_label.background_url')) {
    $imagePath = Config::get('concrete.urls.background_feed') . '/' . $image;
    $imageData = Core::getApplicationURL() . '/' . DISPATCHER_FILENAME . '/tools/required/dashboard/get_image_data';

} else if (Config::get('concrete.white_label.background_url')) {
    $imagePath = Config::get('concrete.white_label.background_url');
}
?>

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
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <?php if ($c->isCheckedOut()) {
                            ?>
                            <a href="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$c->getCollectionID()?>&approve=APPROVE&ctask=check-in&<?=$token?>" id="ccm-nav-exit-edit-direct" class="ccm-main-nav-edit-option"><?=t('Save Changes')?></a>
                            <?php
                        }
                        ?>
                        <?php if (!$c->isCheckedOut()) {
                            ?><a href="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$c->getCollectionID()?>&ctask=check-out<?=$token?>" id="ccm-nav-check-out"><?=t('Edit Page')?></a><?php
                        }
                        ?>

                    </li>
                </ul>
            </div>
        </div>
    </nav>


</div>

<?php $a = new Area('Main'); $a->display($c); ?>


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
        });
    </script>
<?php } ?>




<?php $view->inc('elements/footer.php'); ?>