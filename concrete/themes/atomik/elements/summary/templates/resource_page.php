<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-summary-template-resource-page">
    <div class="row">
        <div class="col-md-4">
            <a href="<?=$link?>"><img class="img-fluid mb-md-0 mb-3" src="<?=$thumbnail->getThumbnailURL('resource_list_entry')?>"></a>
        </div>
        <div class="col-md-8">
            <div>
                <h5><a href="<?=$link?>"><?=$title?></a></h5>
                <?php if (!empty($description)) { ?>
                    <p><?=$description?></p>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
