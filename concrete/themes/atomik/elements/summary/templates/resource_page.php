<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-summary-template-resource-page">
    <div class="row">
        <div class="col-md-4">
            <a href="<?=$link?>"><img class="img-fluid" src="<?=$thumbnail->getThumbnailURL('resource_list_entry')?>"></a>
        </div>
        <div class="col-md-8">
            <h5><a href="<?=$link?>"><?=$title?></a></h5>
            <p><?=$description?></p>
        </div>
    </div>
</div>
