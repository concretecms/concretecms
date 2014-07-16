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
            <hr />
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Base Font Size')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="0" data-style-slider-max="200" data-style-slider-default-setting="0">
                    <input type="hidden" name="baseFontSize" id="baseFontSize" class="ccm-inline-style-slider-value" value="<?php echo $baseFontSize ? $baseFontSize : '' ?>" />
                </div>
                <span class="ccm-inline-style-slider-total-display"><span class="ccm-inline-style-slider-display-value"><?php echo $baseFontSize ? $baseFontSize.'' : '0' ?></span><span class="ccm-inline-style-slider-display-format">px</span></span>
            </div>
            <div class="ccm-inline-select-container">
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
            <div class="ccm-inline-select-container">
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
            <hr />
            <div class="ccm-inline-select-container">
                <?=t('Style')?>
                <?=$form->select('borderStyle', $borderOptions, $borderStyle);?>
            </div>
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Width')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="0" data-style-slider-max="200" data-style-slider-default-setting="0">
                    <input type="hidden" name="borderWidth" id="borderWidth" class="ccm-inline-style-slider-value" value="<?php echo $borderWidth ? $borderWidth : '' ?>" />
                </div>
                <span class="ccm-inline-style-slider-total-display"><span class="ccm-inline-style-slider-display-value"><?php echo $borderWidth ? $borderWidth.'' : '0' ?></span><span class="ccm-inline-style-slider-display-format">px</span></span>
            </div>
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Radius')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="0" data-style-slider-max="200" data-style-slider-default-setting="0">
                    <input type="hidden" name="borderRadius" id="borderRadius" class="ccm-inline-style-slider-value" value="<?php echo $borderRadius ? $borderRadius : '' ?>" />
                </div>
                <span class="ccm-inline-style-slider-total-display"><span class="ccm-inline-style-slider-display-value"><?php echo $borderRadius ? $borderRadius.'' : '0' ?></span><span class="ccm-inline-style-slider-display-format">px</span></span>
            </div>
        </div>
    </li>
    <li class="ccm-inline-toolbar-icon-cell"><a href="#" data-toggle="dropdown"><i class="fa fa-arrows-h"></i></a>
        <div class="ccm-inline-design-dropdown-menu dropdown-menu">
            <h3><?=t('Padding')?></h3>
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Top')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="0" data-style-slider-max="200" data-style-slider-default-setting="0">
                    <input type="hidden" name="paddingTop" id="paddingTop" class="ccm-inline-style-slider-value" value="<?php echo $paddingTop ? $paddingTop : '' ?>" />
                </div>
                <span class="ccm-inline-style-slider-total-display"><span class="ccm-inline-style-slider-display-value"><?php echo $paddingTop ? $paddingTop.'' : '0' ?></span><span class="ccm-inline-style-slider-display-format">px</span></span>
            </div>
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Right')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="0" data-style-slider-max="200" data-style-slider-default-setting="0">
                    <input type="hidden" name="paddingRight" id="paddingRight" class="ccm-inline-style-slider-value" value="<?php echo $paddingRight ? $paddingRight : '' ?>" />
                </div>
                <span class="ccm-inline-style-slider-total-display"><span class="ccm-inline-style-slider-display-value"><?php echo $paddingRight ? $paddingRight.'' : '0' ?></span><span class="ccm-inline-style-slider-display-format">px</span></span>
            </div>
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Bottom')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="0" data-style-slider-max="200" data-style-slider-default-setting="0">
                    <input type="hidden" name="paddingBottom" id="paddingBottom" class="ccm-inline-style-slider-value" value="<?php echo $paddingBottom ? $paddingBottom : '' ?>" />
                </div>
                <span class="ccm-inline-style-slider-total-display"><span class="ccm-inline-style-slider-display-value"><?php echo $paddingBottom ? $paddingBottom.'' : '0' ?></span><span class="ccm-inline-style-slider-display-format">px</span></span>
            </div>
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Left')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="0" data-style-slider-max="200" data-style-slider-default-setting="0">
                    <input type="hidden" name="paddingLeft" id="paddingLeft" class="ccm-inline-style-slider-value" value="<?php echo $paddingLeft ? $paddingLeft : '' ?>" />
                </div>
                <span class="ccm-inline-style-slider-total-display"><span class="ccm-inline-style-slider-display-value"><?php echo $paddingLeft ? $paddingLeft.'' : '0' ?></span><span class="ccm-inline-style-slider-display-format">px</span></span>
            </div>

            <? if ($style instanceof \Concrete\Core\Block\CustomStyle) { ?>
                <hr />
                <h3><?=t('Margin')?></h3>
                <div>
                    <span class="ccm-inline-style-slider-heading"><?=t('Top')?></span>
                    <div class="ccm-inline-style-sliders" data-style-slider-min="-50" data-style-slider-max="200" data-style-slider-default-setting="0">
                        <input type="hidden" name="marginTop" id="marginTop" class="ccm-inline-style-slider-value" value="<?php echo $marginTop ? $marginTop : '' ?>" />
                    </div>
                    <span class="ccm-inline-style-slider-total-display"><span class="ccm-inline-style-slider-display-value"><?php echo $marginTop ? $marginTop.'' : '0' ?></span><span class="ccm-inline-style-slider-display-format">px</span></span>
                </div>
                <div>
                    <span class="ccm-inline-style-slider-heading"><?=t('Right')?></span>
                    <div class="ccm-inline-style-sliders" data-style-slider-min="-50" data-style-slider-max="200" data-style-slider-default-setting="0">
                        <input type="hidden" name="marginRight" id="marginRight" class="ccm-inline-style-slider-value" value="<?php echo $marginRight ? $marginRight : '' ?>" />
                    </div>
                    <span class="ccm-inline-style-slider-total-display"><span class="ccm-inline-style-slider-display-value"><?php echo $marginRight ? $marginRight.'' : '0' ?></span><span class="ccm-inline-style-slider-display-format">px</span></span>
                </div>
                <div>
                    <span class="ccm-inline-style-slider-heading"><?=t('Bottom')?></span>
                    <div class="ccm-inline-style-sliders" data-style-slider-min="-50" data-style-slider-max="200" data-style-slider-default-setting="0">
                        <input type="hidden" name="marginBottom" id="marginBottom" class="ccm-inline-style-slider-value" value="<?php echo $marginBottom ? $marginBottom : '' ?>" />
                    </div>
                    <span class="ccm-inline-style-slider-total-display"><span class="ccm-inline-style-slider-display-value"><?php echo $marginBottom ? $marginBottom.'' : '0' ?></span><span class="ccm-inline-style-slider-display-format">px</span></span>
                </div>
                <div>
                    <span class="ccm-inline-style-slider-heading"><?=t('Left')?></span>
                    <div class="ccm-inline-style-sliders" data-style-slider-min="-50" data-style-slider-max="200" data-style-slider-default-setting="0">
                        <input type="hidden" name="marginLeft" id="marginLeft" class="ccm-inline-style-slider-value" value="<?php echo $marginLeft ? $marginLeft : '' ?>" />
                    </div>
                    <span class="ccm-inline-style-slider-total-display"><span class="ccm-inline-style-slider-display-value"><?php echo $marginLeft ? $marginLeft.'' : '0' ?></span><span class="ccm-inline-style-slider-display-format">px</span></span>
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
            <hr />
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Horizontal Position')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="0" data-style-slider-max="200" data-style-slider-default-setting="0">
                    <input type="hidden" name="boxShadowHorizontal" id="boxShadowHorizontal" class="ccm-inline-style-slider-value" value="<?php echo $boxShadowHorizontal ? $boxShadowHorizontal : '' ?>" />
                </div>
                <span class="ccm-inline-style-slider-total-display"><span class="ccm-inline-style-slider-display-value"><?php echo $boxShadowHorizontal ? $boxShadowHorizontal.'' : '0' ?></span><span class="ccm-inline-style-slider-display-format">px</span></span>
            </div>
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Vertical Position')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="0" data-style-slider-max="200" data-style-slider-default-setting="0">
                    <input type="hidden" name="boxShadowVertical" id="boxShadowVertical" class="ccm-inline-style-slider-value" value="<?php echo $boxShadowVertical ? $boxShadowVertical : '' ?>" />
                </div>
                <span class="ccm-inline-style-slider-total-display"><span class="ccm-inline-style-slider-display-value"><?php echo $boxShadowVertical ? $boxShadowVertical.'' : '0' ?></span><span class="ccm-inline-style-slider-display-format">px</span></span>
            </div>
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Blur')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="0" data-style-slider-max="200" data-style-slider-default-setting="0">
                    <input type="hidden" name="boxShadowBlur" id="boxShadowBlur" class="ccm-inline-style-slider-value" value="<?php echo $boxShadowBlur ? $boxShadowBlur : '' ?>" />
                </div>
                <span class="ccm-inline-style-slider-total-display"><span class="ccm-inline-style-slider-display-value"><?php echo $boxShadowBlur ? $boxShadowBlur.'' : '0' ?></span><span class="ccm-inline-style-slider-display-format">px</span></span>
            </div>
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Spread')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="-50" data-style-slider-max="200" data-style-slider-default-setting="0">
                    <input type="hidden" name="boxShadowSpread" id="boxShadowSpread" class="ccm-inline-style-slider-value" value="<?php echo $boxShadowSpread ? $boxShadowSpread : '' ?>" />
                </div>
                <span class="ccm-inline-style-slider-total-display"><span class="ccm-inline-style-slider-display-value"><?php echo $boxShadowSpread ? $boxShadowSpread.'' : '0' ?></span><span class="ccm-inline-style-slider-display-format">px</span></span>
            </div>
            <hr/>
            <h3><?=t('Rotate')?></h3>
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Rotation (in degrees)')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="0" data-style-slider-max="360" data-style-slider-default-setting="0">
                    <input type="hidden" name="rotate" id="rotate" class="ccm-inline-style-slider-value" value="<?php echo $rotate ? $rotate : '' ?>" />
                </div>
                <span class="ccm-inline-style-slider-total-display"><span class="ccm-inline-style-slider-display-value"><?php echo $rotate ? $rotate.'' : '0' ?></span><span class="ccm-inline-style-slider-display-format">&deg;</span></span>
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
                <div class="ccm-inline-select-container">
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