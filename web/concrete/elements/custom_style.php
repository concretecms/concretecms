<?
defined('C5_EXECUTE') or die("Access Denied.");

$backgroundColor = '';
$image = false;
$baseFontSize = '';
$backgroundRepeat = 'no-repeat';
$textColor = '';
$linkColor = '';
$marginTop = '';
$marginLeft = '';
$marginRight = '';
$marginBottom = '';
$paddingTop = '';
$paddingLeft = '';
$paddingRight = '';
$paddingBottom = '';

if (is_object($style)) {
    $set = $style->getStyleSet();
    $backgroundColor = $set->getBackgroundColor();
    $textColor = $set->getTextColor();
    $linkColor = $set->getLinkColor();
    $image = $set->getBackgroundImageFileObject();
    $backgroundRepeat = $set->getBackgroundRepeat();
    $baseFontSize = $set->getBaseFontSize();
    $marginTop = $set->getMarginTop();
    $marginLeft = $set->getMarginLeft();
    $marginRight = $set->getMarginRight();
    $marginBottom = $set->getMarginBottom();
    $paddingTop = $set->getPaddingTop();
    $paddingLeft = $set->getPaddingLeft();
    $paddingRight = $set->getPaddingRight();
    $paddingBottom = $set->getPaddingBottom();
}

$repeatOptions = array(
    'no-repeat' => t('None'),
    'repeat-x' => t('Horizontal'),
    'repeat-y' => t('Vertical'),
    'repeat' => t('Tile')
);
$al = new Concrete\Core\Application\Service\FileManager();
$form = Core::make('helper/form');
?>

<form method="post" action="<?=$action?>" id="ccm-inline-design-form">
<ul class="ccm-inline-toolbar ccm-ui">
    <li class="ccm-inline-toolbar-icon-cell"><a href="#" data-toggle="dropdown"><i class="fa fa-font"></i></a>

        <div class="ccm-inline-design-dropdown-menu dropdown-menu">
            <div>
                <?=t('Text Color')?>
                <?=Loader::helper('form/color')->output('textColor', $textColor);?>
            </div>
            <hr />
            <div>
                <?=t('Link Color')?>
                <?=Loader::helper('form/color')->output('linkColor', $linkColor);?>
            </div>
            <div>
                <?=t('Base Font Size')?>
                <?=$form->text('baseFontSize', $baseFontSize);?>
            </div>
        </div>

    </li>
    <li class="ccm-inline-toolbar-icon-cell"><a href="#" data-toggle="dropdown"><i class="fa fa-image"></i></a>

        <div class="ccm-inline-design-dropdown-menu dropdown-menu">
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
            <div>
                <?=t('Tile')?>
                <?=$form->select('backgroundRepeat', $repeatOptions, $backgroundRepeat);?>
            </div>
        </div>

    </li>
    <li class="ccm-inline-toolbar-icon-cell"><a href="#" data-toggle="dropdown"><i class="fa fa-square-o"></i></a>
        <div class="ccm-inline-design-dropdown-menu dropdown-menu">
            <h3><?=t('Padding')?></h3>
            <div>
                <?=t('Top')?>
                <?=$form->text('paddingTop', $paddingTop);?>
            </div>
            <div>
                <?=t('Right')?>
                <?=$form->text('paddingRight', $paddingRight);?>
            </div>
            <div>
                <?=t('Bottom')?>
                <?=$form->text('paddingBottom', $paddingBottom);?>
            </div>
            <div>
                <?=t('Left')?>
                <?=$form->text('paddingLeft', $paddingLeft);?>
            </div>

            <? if ($style instanceof \Concrete\Core\Block\CustomStyle) { ?>
                <hr />
                <h3><?=t('Margin')?></h3>
                <div>
                    <?=t('Top')?>
                    <?=$form->text('marginTop', $marginTop);?>
                </div>
                <div>
                    <?=t('Right')?>
                    <?=$form->text('marginRight', $marginRight);?>
                </div>
                <div>
                    <?=t('Bottom')?>
                    <?=$form->text('marginBottom', $marginBottom);?>
                </div>
                <div>
                    <?=t('Left')?>
                    <?=$form->text('marginLeft', $marginLeft);?>
                </div>

            <? } ?>
        </div>
    </li>
    <li class="ccm-inline-toolbar-icon-cell"><a href="#"><i class="fa fa-arrows-h"></i></a></li>
    <li class="ccm-inline-toolbar-icon-cell"><a href="#"><i class="fa fa-html5"></i></a></li>
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