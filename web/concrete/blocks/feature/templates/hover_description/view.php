<?php  defined('C5_EXECUTE') or die("Access Denied.");
$ag = \Concrete\Core\Http\ResponseAssetGroup::get();
$ag->requireAsset('javascript', 'bootstrap/tooltip');
$ag->requireAsset('css', 'bootstrap/tooltip');
?>
<div class="ccm-block-feature-item-hover-wrapper" data-toggle="tooltip" data-placement="bottom" title="<?=$paragraph?>">
    <div class="ccm-block-feature-item-hover">
        <div class="ccm-block-feature-item-hover-icon"><i class="fa fa-<?=$icon?>"></i></div>
    </div>
    <div class="ccm-block-feature-item-hover-title"><?=$title?></div>
</div>