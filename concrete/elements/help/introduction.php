<?php

defined('C5_EXECUTE') or die('Access Denied.');

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <h2><?= t('Learn the basics.') ?></h2>
            <div class="spacer-row-2"></div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="ccm-panel-help-item">
                        <h4><?= t('Use the toolbar') ?></h4>
                        <ol class="breadcrumb">
                            <li><a data-lightbox="iframe" href="https://www.youtube.com/watch?v=VB-R71zk06U"><?= t('Watch Video') ?></a></li>
                            <li class="visible-sm-inline visible-md-inline visible-lg-inline"><a href="#" data-launch-guide="toolbar"><?= t('Run Guide') ?></a></li>
                        </ol>
                    </div>
                    <div class="ccm-panel-help-item">
                        <h4><?= t('Add & Change Content') ?></h4>
                        <ol class="breadcrumb">
                            <li><a href="https://www.youtube.com/watch?v=Y1VmBVffLM0" data-lightbox="iframe"><?= t('Watch Video') ?></a></li>
                            <li class="visible-sm-inline visible-md-inline visible-lg-inline"><a href="#" data-launch-guide="change-content"><?= t(' Change Content') ?></a></li>
                            <li class="visible-sm-inline visible-md-inline visible-lg-inline"><a href="#" data-launch-guide="add-content"><?= t('Guide: Add Content') ?></a></li>
                        </ol>
                    </div>
                    <div class="ccm-panel-help-item">
                        <h4><?= t('Add a page') ?></h4>
                        <ol class="breadcrumb">
                            <li><a href="https://www.youtube.com/watch?v=mWTNga4_O_Q" data-lightbox="iframe"><?= t('Watch Video') ?></a></li>
                            <li class="visible-sm-inline visible-md-inline visible-lg-inline"><a href="#" data-launch-guide="add-page"><?= t('Run Guide') ?></a></li>
                        </ol>
                    </div>
                    <div class="ccm-panel-help-item">
                        <h4><?= t('Personalize your site') ?></h4>
                        <ol class="breadcrumb">
                            <li><a href="https://www.youtube.com/watch?v=xI8dUNAc6fU" data-lightbox="iframe"><?= t('Watch Video') ?></a></li>
                            <li class="visible-sm-inline visible-md-inline visible-lg-inline"><a href="#" data-launch-guide="personalize"><?= t('Run Guide') ?></a></li>
                        </ol>
                    </div>
                    <div class="ccm-panel-help-item">
                        <h4><?= t('Cleanup and organize your site') ?></h4>
                        <ol class="breadcrumb">
                            <li><a href="https://www.youtube.com/watch?v=_NhlWLU_L6E" data-lightbox="iframe"><?= t('Watch Video') ?></a></li>
                            <li class="visible-sm-inline visible-md-inline visible-lg-inline"><a href="#" data-launch-guide="dashboard"><?= t('Run Guide') ?></a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#ccm-panel-help a[data-lightbox=iframe]').magnificPopup({
            disableOn: 700,
            type: 'iframe',
            mainClass: 'mfp-fade',
            removalDelay: 160,
            preloader: false,
            fixedContentPos: false
        });
        $('#ccm-panel-help a[data-launch-guide]').on('click', function(e) {
            e.preventDefault();
            var guide = $(this).data('launch-guide'),
                tour = ConcreteHelpGuideManager.getGuide(guide);
            if (tour === undefined) {
                console.error('Guide "' + guide + '" is not defined');
                return;
            }
            tour.start();
        });
    });
</script>
