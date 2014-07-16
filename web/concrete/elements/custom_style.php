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
$borderStyle = '';
$borderWidth = '';
$borderColor = '';
$borderRadius = '';
$alignment = '';
$rotate = '';
$boxShadowHorizontal = '';
$boxShadowVertical = '';
$boxShadowBlur = '';
$boxShadowSpread = '';
$boxShadowColor = '';
$set = $style->getStyleSet();
if (is_object($set)) {
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
    $borderStyle = $set->getBorderStyle();
    $borderWidth = $set->getBorderWidth();
    $borderColor = $set->getBorderColor();
    $borderRadius = $set->getBorderRadius();
    $alignment = $set->getAlignment();
    $rotate = $set->getRotate();
    $boxShadowHorizontal = $set->getBoxShadowHorizontal();
    $boxShadowVertical = $set->getBoxShadowVertical();
    $boxShadowBlur = $set->getBoxShadowBlur();
    $boxShadowSpread = $set->getBoxShadowSpread();
    $boxShadowColor = $set->getBoxShadowColor();
}

$repeatOptions = array(
    'no-repeat' => t('None'),
    'repeat-x' => t('Horizontal'),
    'repeat-y' => t('Vertical'),
    'repeat' => t('Tile')
);
$borderOptions = array(
    'none' => t('None'),
    'solid' => t('Solid'),
    'dotted' => t('Dotted'),
    'dashed' => t('Dashed'),
    'double' => t('Double'),
    'groove' => t('Groove'),
    'ridge' => t('Ridge'),
    'inset' => t('Inset'),
    'outset' => t('Outset')
);

$alignmentOptions = array(
    '' => t('None'),
    'right' => t('Right'),
    'left' => t('Left'),
);

if ($style instanceof \Concrete\Core\Block\CustomStyle) {
    $method = 'concreteBlockInlineStyleCustomizer';
} else {
    $method = 'concreteAreaInlineStyleCustomizer';
}

$al = new Concrete\Core\Application\Service\FileManager();
$form = Core::make('helper/form');
?>

<form method="post" action="<?=$saveAction?>" id="ccm-inline-design-form">
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
            <div>
                <?=t('Alignment')?>
                <?=$form->select('alignment', $alignmentOptions, $alignment);?>
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
            <h3><?=t('Border')?></h3>
            <div>
                <?=t('Color')?>
                <?=Loader::helper('form/color')->output('borderColor', $borderColor);?>
            </div>
            <div>
                <?=t('Style')?>
                <?=$form->select('borderStyle', $borderOptions, $borderStyle);?>
            </div>
            <div>
                <?=t('Width')?>
                <?=$form->text('borderWidth', $borderWidth);?>
            </div>
            <div>
                <?=t('Radius')?>
                <?=$form->text('borderRadius', $borderRadius);?>
            </div>
        </div>
    </li>
    <li class="ccm-inline-toolbar-icon-cell"><a href="#" data-toggle="dropdown"><i class="fa fa-arrows-h"></i></a>
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
    <li class="ccm-inline-toolbar-icon-cell"><a href="#" data-toggle="dropdown"><i class="fa fa-magic"></i></a>
        <div class="ccm-inline-design-dropdown-menu dropdown-menu">
            <h3><?=t('Shadow')?></h3>
            <div>
                <?=t('Color')?>
                <?=Loader::helper('form/color')->output('boxShadowColor', $boxShadowColor);?>
            </div>
            <div>
                <?=t('Horizontal Position')?>
                <?=$form->text('boxShadowHorizontal', $boxShadowHorizontal);?>
            </div>
            <div>
                <?=t('Vertical Position')?>
                <?=$form->text('boxShadowVertical', $boxShadowVertical);?>
            </div>
            <div>
                <?=t('Blur')?>
                <?=$form->text('boxShadowBlur', $boxShadowBlur);?>
            </div>
            <div>
                <?=t('Spread')?>
                <?=$form->text('boxShadowSpread', $boxShadowSpread);?>
            </div>
            <hr/>
            <h3><?=t('Rotate')?></h3>
            <div>
                <?=t('Rotation (in degrees)')?>
                <?=$form->text('rotate', $rotate);?>
            </div>

        </div>

    </li>
    <li class="ccm-inline-toolbar-icon-cell"><a href="#" data-toggle="dropdown"><i class="fa fa-cog"></i></a>
        <div class="ccm-inline-design-dropdown-menu dropdown-menu">
            <button data-reset-action="<?=$resetAction?>" data-action="reset-design" type="button" class="btn btn-danger"><?=t("Clear Styles")?></button>
            <? if ($style instanceof \Concrete\Core\Block\CustomStyle && $canEditCustomTemplate) { ?>
                <hr/>
                <div>
                    <?=t('Block Name')?>
                    <?=$form->text('bName', $bName);?>
                </div>
                <div>
                    <?=t('Custom Template')?>
                    <select id="bFilename" name="bFilename" class="form-control">
                        <option value="">(<?=t('None selected')?>)</option>
                        <?
                        foreach($templates as $tpl) {
                            ?><option value="<?=$tpl->getTemplateFileFilename()?>" <? if ($bFilename == $tpl->getTemplateFileFilename()) { ?> selected <? } ?>><?=$tpl->getTemplateFileDisplayName()?></option><?
                        }
                        ?>
                    </select>
                 </div>
            <? } ?>
        </div>
    </li>
    <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-cancel">
        <button data-action="cancel-design" type="button" class="btn btn-mini"><?=t("Cancel")?></button>
    </li>
    <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-save">
        <button data-action="save-design" class="btn btn-primary" type="button"><?=t('Save')?></button>
    </li>
</ul>
</form>

<script type="text/javascript">
    $('#ccm-inline-design-form').<?=$method?>();
</script>