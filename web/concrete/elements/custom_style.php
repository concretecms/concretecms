<?
defined('C5_EXECUTE') or die("Access Denied.");

$backgroundColor = '';
$image = false;
if (is_object($set)) {
    $backgroundColor = $set->getBackgroundColor();
    $image = $set->getBackgroundImageFileObject();
}

$al = new Concrete\Core\Application\Service\FileManager();

?>

<form method="post" action="<?=$action?>" id="ccm-inline-design-form">
<ul class="ccm-inline-toolbar ccm-ui">
    <li class="ccm-inline-toolbar-icon-cell"><a href="#"><i class="fa fa-font"></i></a></li>
    <li class="ccm-inline-toolbar-icon-cell"><a href="#" data-toggle="dropdown"><i class="fa fa-image"></i></a>

        <div class="ccm-inline-design-dropdown-menu dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
            <h3><?=t('Background')?></h3>
            <div>
                <?=t('Color')?>
                <?=Loader::helper('form/color')->output('backgroundColor', $backgroundColor);?>
            </div>
            <hr />
            <div>
                <?=t('Image')?>
                <?=$al->image('backgroundImageFileID', 'backgroundImageFileID', t('Choose Image'), $image);?>
            </div>
        </div>

    </li>
    <li class="ccm-inline-toolbar-icon-cell"><a href="#"><i class="fa fa-square-o"></i></a></li>
    <li class="ccm-inline-toolbar-icon-cell"><a href="#"><i class="fa fa-arrows-h"></i></a></li>
    <li class="ccm-inline-toolbar-icon-cell"><a href="#"><i class="fa fa-cog"></i></a></li>
    <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-cancel">
        <button data-action="cancel-design" type="button" class="btn btn-mini"><?=t("Cancel")?></button>
    </li>
    <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-save">
        <button data-action="save-design" class="btn btn-primary" type="button"><?=t('Save')?></button>
    </li>
</ul>
</form>

<script type="text/javascript">
    $('#ccm-inline-design-form').concreteInlineStyleCustomizer();
</script>