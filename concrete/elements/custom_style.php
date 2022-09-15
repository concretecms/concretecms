<?php /** @noinspection DuplicatedCode */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\Block\CustomStyle;
use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\Color;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Theme\GridFramework\GridFramework;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\View\View;
use Doctrine\ORM\EntityManagerInterface;

if (isset($displayBlockContainerSettings)) {
    $displayBlockContainerSettings = (bool)$displayBlockContainerSettings;
} else {
    $displayBlockContainerSettings = false;
}

$backgroundColor = '';
$image = false;
$baseFontSize = '';
$backgroundRepeat = 'no-repeat';
$backgroundSize = 'auto';
$backgroundPosition = 'left top';
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
$boxShadowInset = false;
$customClass = '';
$customID = '';
$customElementAttribute = '';
/** @var string $resetAction */
/** @var string $bFilename */
/** @var string $canEditCustomTemplate */
/** @var string $saveAction */
/** @var CustomStyle $style */
/** @var StyleSet $set */
$set = $style->getStyleSet();

if (is_object($set)) {
    $backgroundColor = $set->getBackgroundColor();
    $textColor = $set->getTextColor();
    $linkColor = $set->getLinkColor();
    $image = $set->getBackgroundImageFileObject();
    $backgroundRepeat = $set->getBackgroundRepeat();
    $backgroundSize = $set->getBackgroundSize();
    $backgroundPosition = $set->getBackgroundPosition();
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
    $boxShadowInset = $set->getBoxShadowInset();
    $customClass = $set->getCustomClass();
    $customID = $set->getCustomID();
    $customElementAttribute = $set->getCustomElementAttribute();
}

$enableBlockContainerOptions = [
    -1 => t('Default Setting'),
    0 => t('Disable Grid Container'),
    1 => t('Enable Grid Container')
];

if ($style instanceof CustomStyle && $canEditCustomTemplate) {
    $customTemplateOptions = [
        "" => t('Default')
    ];

    foreach ($templates as $tpl) {
        $customTemplateOptions[$tpl->getTemplateFileFilename()] = $tpl->getTemplateFileDisplayName();
    }
}

$deviceHideClasses = [];
/* @var $gf GridFramework */
if (is_object($gf)) {
    $deviceHideClasses = $gf->getPageThemeGridFrameworkDeviceHideClasses();
}

$al = new FileManager();
$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Color $color */
$color = $app->make(Color::class);
/** @var Identifier $identifier */
$identifier = $app->make(Identifier::class);
$blockID =0;
if ($style instanceof CustomStyle) {
    $areaID = $style->block->getBlockAreaObject()->getAreaID();
    $blockID = $style->block->getBlockID();
} else {
    $areaID = $style->area->getAreaID();
}

?>

<form method="post" action="<?php echo h($saveAction) ?>" id="ccm-inline-design-form"
      data-target-element="<?php echo ($style instanceof CustomStyle) ? "block" : "area"; ?>">
    <ul class="ccm-inline-toolbar ccm-ui">
        <?php if ($style instanceof CustomStyle && $canEditCustomTemplate) { ?>
            <li class="ccm-inline-toolbar-select">
                <div class="form-group d-flex text-nowrap">
                    <?php echo $form->label("bFilename", t('Block Template')); ?>
                    <?php echo $form->select("bFilename", $customTemplateOptions, $bFilename ?? null, ['class' => 'form-select form-select-sm', 'v-model'=>'bFilename', '@change'=>'refreshTemplate']); ?>
                </div>
            </li>
        <?php } ?>

        <toolbar-section-widget title="<?=h(t('Text Size and Color'))?>" @dropdown="toggleDropDown" :active-toolbar="activeDropDown" toolbar-name="text-size-color" icon="fas fa-font">

                <div class="row">
                    <div class="col-sm-12">
                        <fieldset>
                            <div class="form-group d-flex flex-row justify-content-between">
                                <?php echo $form->label("textColor", t('Text Color')) ?>
                                <input type="hidden" value="<?=$textColor?>" name="textColor" v-model="textColor">
                                <color-page-customizer-widget @update="handleColorUpdate" :style-value="{style:{variable:'textColor'}, value:'<?=$textColor?>'}"></color-page-customizer-widget>

                            </div>

                            <hr/>

                            <div class="form-group d-flex flex-row justify-content-between">
                                <?php echo $form->label("linkColor", t('Link Color')) ?>
                                <input type="hidden" value="<?=$linkColor?>" name="linkColor" v-model="linkColor">
                                <color-page-customizer-widget @update="handleColorUpdate" :style-value="{style:{variable:'linkColor'}, value:'<?=$linkColor?>'}"></color-page-customizer-widget>

                            </div>

                            <hr/>

                            <toolbar-slider-widget title="<?=h(t("Base Font Size"))?>" :max="200" :min="0" v-model.number="baseFontSize" input-id="baseFontSize" units="px" @input="calculatePreview" >
                            </toolbar-slider-widget>

                            <hr/>

                            <div class="form-group">
                                <?php echo $form->label("alignment", t('Alignment')) ?>
                                <?php echo $form->select('alignment', [
                                    '' => t('None'),
                                    'left' => t('Left'),
                                    'center' => t('Center'),
                                    'right' => t('Right'),
                                ], $alignment, ['v-model'=>'alignment', '@change'=>'calculatePreview']); ?>
                            </div>
                        </fieldset>
                    </div>
                </div>
        </toolbar-section-widget>
        <toolbar-section-widget title="<?=h(t('Background Color and Image'))?>" @dropdown="toggleDropDown" :active-toolbar="activeDropDown" toolbar-name="background" icon="fas fa-image">
            <div class="row">
                    <div class="col-sm-12">
                        <fieldset>
                            <legend>
                                <?php echo t('Background') ?>
                            </legend>

                            <div class="form-group d-flex flex-row justify-content-between">
                                <?php echo $form->label("backgroundColor", t('Color')) ?>
                                <color-page-customizer-widget @update="handleColorUpdate" :style-value="{style:{variable:'backgroundColor'}, value:'<?=$backgroundColor?>'}"></color-page-customizer-widget>
                                <input type="hidden" value="<?=$backgroundColor?>" name="backgroundColor" v-model="background.color">
                            </div>

                            <hr/>

                            <div class="form-group">
                                <?php echo $form->label("backgroundImageFileID", t('Image')) ?>
                                <?php

                                $app = Application::getFacadeApplication();

                                $view = View::getInstance();
                                $request = $app->make(Request::class);
                                $vh = $app->make('helper/validation/numbers');

                                $fID = 0;

                                if ($vh->integer($request->request->get('backgroundImageFileID'))) {
                                    $file = $app->make(EntityManagerInterface::class)->find(FileEntity::class, $request->request->get('backgroundImageFileID'));
                                    if ($file !== null) {
                                        $fID = $file->getFileID();
                                    }
                                } elseif ($vh->integer($image)) {
                                    $fID = (int) $image;
                                } elseif (is_object($image)) {
                                    $fID = (int) $image->getFileID();
                                }
                                ?>
                                <div data-concrete-file-input="<?=uniqid()?>>">
                                    <concrete-file-input :filters='<?=json_encode([['field' => 'type', 'type' => \Concrete\Core\File\Type\Type::T_IMAGE,]])?>' :file-id="<?=$fID?>" choose-text="<?=h(t('Choose Image'))?>" input-name="backgroundImageFileID" @selectedfile="handleFileSelect"></concrete-file-input>
                                </div>
                                <?php // echo $al->image('backgroundImageFileID', 'backgroundImageFileID', t('Choose Image'), $image); ?>
                            </div>

                            <hr/>

                            <div class="form-group">
                                <?php echo $form->label("backgroundRepeat", t('Repeats')) ?>
                                <?php echo $form->select('backgroundRepeat', [
                                    'no-repeat' => t('No Repeat'),
                                    'repeat-x' => t('Horizontally'),
                                    'repeat-y' => t('Vertically'),
                                    'repeat' => t('Horizontally & Vertically'),
                                ], $backgroundRepeat, ['v-model'=>'background.repeat', '@change'=>'calculatePreview']); ?>
                            </div>

                            <hr/>

                            <div class="form-group">
                                <?php echo $form->label("backgroundSize", t('Size')) ?>
                                <?php echo $form->select('backgroundSize', [
                                    'auto' => t('Auto'),
                                    'contain' => t('Contain'),
                                    'cover' => t('Cover'),
                                    '10%' => t('10%'),
                                    '25%' => t('25%'),
                                    '50%' => t('50%'),
                                    '75%' => t('75%'),
                                    '100%' => t('100%'),
                                ], $backgroundSize, ['v-model'=>'background.size', '@change'=>'calculatePreview']); ?>
                            </div>

                            <hr/>

                            <div class="form-group">
                                <?php echo $form->label("backgroundPosition", t('Position')) ?>
                                <?php echo $form->select('backgroundPosition', [
                                    'left top' => t('Left Top'),
                                    'left center' => t('Left Center'),
                                    'left bottom' => t('Left Bottom'),
                                    'center top' => t('Center Top'),
                                    'center center' => t('Center Center'),
                                    'center bottom' => t('Center Bottom'),
                                    'right top' => t('Right Top'),
                                    'right center' => t('Right Center'),
                                    'right bottom' => t('Right Bottom'),
                                ], $backgroundPosition, ['v-model'=>'background.position', '@change'=>'calculatePreview']); ?>
                            </div>
                        </fieldset>
                    </div>
                </div>
        </toolbar-section-widget>
        <toolbar-section-widget title="<?=h(t('Borders'))?>" @dropdown="toggleDropDown" :active-toolbar="activeDropDown" toolbar-name="borders" icon="fas fa-border-style">
            <div class="row">
                    <div class="col-sm-12">
                        <fieldset>
                            <legend>
                                <?php echo t('Border') ?>
                            </legend>

                            <div class="form-group d-flex flex-row justify-content-between">
                                <?php echo $form->label("borderColor", t('Color')) ?>
                                <color-page-customizer-widget @update="handleColorUpdate" :style-value="{style:{variable:'borderColor'}, value:'<?=$borderColor?>'}"></color-page-customizer-widget>
                                <input type="hidden" value="<?=$borderColor?>" name="borderColor" v-model="border.color">
                            </div>

                            <hr/>

                            <div class="form-group">
                                <?php echo $form->label("borderStyle", t('Style')) ?>
                                <?php echo $form->select('borderStyle', [
                                    '' => t('None'),
                                    'solid' => t('Solid'),
                                    'dotted' => t('Dotted'),
                                    'dashed' => t('Dashed'),
                                    'double' => t('Double'),
                                    'groove' => t('Groove'),
                                    'ridge' => t('Ridge'),
                                    'inset' => t('Inset'),
                                    'outset' => t('Outset'),
                                ], $borderStyle, ['v-model'=>'border.style','@change'=>'calculatePreview']); ?>
                            </div>

                            <hr/>
                            <toolbar-slider-widget title="<?=h(t("Width"))?>" :max="100" :min="0" v-model.number="border.width" @input="calculatePreview" input-id="borderWidth" units="px">
                            </toolbar-slider-widget>
                            <hr/>
                            <toolbar-slider-widget title="<?=h(t("Radius"))?>" :max="999" :min="0" v-model.number="border.radius" @input="calculatePreview"  input-id="borderRadius" units="px">
                            </toolbar-slider-widget>
                        </fieldset>
                    </div>
                </div>
        </toolbar-section-widget>

        <toolbar-section-widget :is-double="true" title="<?=h(t('Margin and Padding'))?>" @dropdown="toggleDropDown" :active-toolbar="activeDropDown" toolbar-name="margin-padding" icon="fas fa-arrows-alt-h">


                <div class="row">
                    <div class="<?php echo $style instanceof CustomStyle ? "col-sm-6" : "col-sm-12"; ?>">
                        <fieldset>
                            <legend>
                                <?php echo t('Padding') ?>
                            </legend>

                            <toolbar-slider-widget title="<?=h(t("Top"))?>" :max="200" :min="0" v-model.number="padding.top" @input="calculatePreview" input-id="paddingTop" units="px"></toolbar-slider-widget>
                            <toolbar-slider-widget title="<?=h(t("Right"))?>" :max="200" :min="0" v-model.number="padding.right" @input="calculatePreview" input-id="paddingRight" units="px"></toolbar-slider-widget>
                            <toolbar-slider-widget title="<?=h(t("Bottom"))?>" :max="200" :min="0" v-model.number="padding.bottom" @input="calculatePreview" input-id="paddingBottom" units="px"></toolbar-slider-widget>
                            <toolbar-slider-widget title="<?=h(t("Left"))?>" :max="200" :min="0" v-model.number="padding.left" @input="calculatePreview" input-id="paddingLeft" units="px"></toolbar-slider-widget>
                        </fieldset>
                    </div>

                    <?php if ($style instanceof CustomStyle) { ?>
                    <div class="col-sm-6">
                        <fieldset>
                            <legend>
                                <?php echo t('Margin') ?>
                            </legend>
                            <toolbar-slider-widget title="<?=h(t("Top"))?>" :max="200" :min="-100" v-model.number="margin.top" @input="calculatePreview" input-id="marginTop" units="px"></toolbar-slider-widget>
                            <toolbar-slider-widget title="<?=h(t("Right"))?>" :max="200" :min="-100" v-model.number="margin.right" @input="calculatePreview" input-id="marginRight" units="px"></toolbar-slider-widget>
                            <toolbar-slider-widget title="<?=h(t("Bottom"))?>" :max="200" :min="-100" v-model.number="margin.bottom" @input="calculatePreview" input-id="marginBottom" units="px"></toolbar-slider-widget>
                            <toolbar-slider-widget title="<?=h(t("Left"))?>" :max="200" :min="-100" v-model.number="margin.left" @input="calculatePreview" input-id="marginLeft" units="px"></toolbar-slider-widget>
                        </fieldset>
                        <?php } ?>
                    </div>
                </div>
        </toolbar-section-widget>

        <toolbar-section-widget :is-double="true" title="<?=h(t('Shadow and Rotation (CSS3)'))?>" @dropdown="toggleDropDown" :active-toolbar="activeDropDown" toolbar-name="shadow-rotation" icon="fas fa-magic">

                <div class="row">
                    <div class="col-sm-12">
                        <fieldset>
                            <legend>
                                <?php echo t('Shadow') ?>
                            </legend>

                            <div class="row">
                                <div class="col-sm-6">
                                    <toolbar-slider-widget title="<?=h(t("Horizontal Position"))?>" :max="200" :min="-100" v-model.number="boxShadow.horizontal" @input="calculatePreview" input-id="boxShadowHorizontal" units="px"></toolbar-slider-widget>

                                </div>

                                <div class="col-sm-6">
                                    <toolbar-slider-widget title="<?=h(t("Vertical Position"))?>" :max="200" :min="-100" v-model.number="boxShadow.vertical" @input="calculatePreview" input-id="boxShadowVertical" units="px"></toolbar-slider-widget>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <toolbar-slider-widget title="<?=h(t("Blur"))?>" :max="100" :min="0" v-model.number="boxShadow.blur" @input="calculatePreview" input-id="boxShadowBlur" units="px"></toolbar-slider-widget>

                                </div>

                                <div class="col-sm-6">
                                    <toolbar-slider-widget title="<?=h(t("Spread"))?>" :max="100" :min="-50" v-model.number="boxShadow.spread" @input="calculatePreview" input-id="boxShadowSpread" units="px"></toolbar-slider-widget>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group d-flex flex-row justify-content-between items-baseline mt-3">
                                        <?php echo $form->label("boxShadowColor", t('Color')) ?>
                                        <color-page-customizer-widget @update="handleColorUpdate" :style-value="{style:{variable:'boxShadowColor'}, value:'<?=$boxShadowColor?>'}" ></color-page-customizer-widget>
                                        <input type="hidden" value="<?=$boxShadowColor?>" name="boxShadowColor" v-model="boxShadow.color">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group d-flex flex-row justify-content-between items-baseline mt-3">
                                        <?php echo $form->label("boxShadowInset", t('Inset')) ?>
                                        <div class="form-check form-switch">

                                        <?php echo $form->checkbox('boxShadowInset', 1, $boxShadowInset, ['v-model'=>'boxShadow.inset', '@change'=>'calculatePreview'])?>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <hr/>

                <div class="row">
                    <div class="col-sm-6">
                        <fieldset>
                            <legend>
                                <?php echo t('Rotate') ?>
                            </legend>

                            <div>

                                <toolbar-slider-widget :attach-units="false" title="<?=h(t("Rotation (in degrees)"))?>" :max="180" :min="-180" v-model.number="rotation" @input="calculatePreview" input-id="rotate" units="°" />
                            </div>
                        </fieldset>
                    </div>

                    <?php if (count($deviceHideClasses)) { ?>
                        <div class="col-sm-6">
                            <fieldset class="position-relative">
                                <legend>
                                    <?php echo t('Device Visibility') ?>

                                    <label class="form-label"><?=t('Visible on Device(s)')?></label>
                                </legend>
                                <div class="btn-group">
                                    <?php foreach ($deviceHideClasses as $class) {
                                        $hidden = false;


                                        if (is_object($set)) {
                                            $hidden = $set->isHiddenOnDevice($class);
                                        }
                                        $jsonClasses[$class] = ['classes'=>$gf->getDeviceHideClasses($class), 'hidden'=>$hidden ? 1 : 0];
                                        echo $form->hidden('hideOnDevice['.$class.']',$hidden ? 1 : 0,['v-model.number'=>'deviceClasses['.$class.'].hidden'])
                                    ?>
                                    <button type="button"
                                            data-hide-on-device="<?php echo h($class) ?>"
                                            class="btn btn-sm" :class="{'btn-danger active':isHidden('<?=h($class)?>'), 'btn-outline-success':!isHidden('<?=h($class)?>')}" @click="hideDevice('<?=h($class)?>')">
                                        <i class="<?php echo $gf->getDeviceHideClassIconClass($class) ?>"></i>
                                    </button>
                                <?php } ?>
                                </div>
                            </fieldset>
                        </div>
                    <?php } ?>
                </div>
        </toolbar-section-widget>

        <toolbar-section-widget title="<?=h(t('Custom CSS Classes, Block Name, Block Templates and Reset Styles'))?>" @dropdown="toggleDropDown" :active-toolbar="activeDropDown" toolbar-name="custom-css">
            <div class="row">
                <div class="col-sm-12">
                    <fieldset>
                        <legend>
                            <?php echo t('Advanced') ?>
                        </legend>

                        <div class="form-group">
                            <?php echo $form->label("customClass", t('Custom Class')) ?>
                            <?php
                            $customClassesSelect = [];
                            $customClassesSelected = [];

                            if (is_string($customClass) && $customClass != '') {
                                $customClassesSelected = explode(' ', $customClass);
                            }

                            if (isset($customClasses) && is_array($customClasses)) {
                                foreach ($customClasses as $class) {
                                    $customClassesSelect[$class] = $class;
                                }

                                if ($customClassesSelected) {
                                    foreach ($customClassesSelected as $class) {
                                        $customClassesSelect[$class] = $class;
                                    }
                                }
                            }

                            echo $form->selectMultiple('customClass', $customClassesSelect, $customClassesSelected, ['class' => 'form-control', 'v-model'=>'customClass']);
                            ?>
                        </div>

                        <hr/>

                        <div class="form-group">
                            <?php echo $form->label("customID", t('Custom ID')); ?>
                            <?php echo $form->text('customID', $customID); ?>
                        </div>

                        <hr/>

                        <div class="form-group">
                            <?php echo $form->label("customElementAttribute", t('Custom Element Attribute')); ?>
                            <?php echo $form->textarea('customElementAttribute', $customElementAttribute); ?>
                        </div>

                        <hr>

                        <?php if ($displayBlockContainerSettings) { ?>
                            <div class="form-group">
                                <?php echo $form->label("enableBlockContainer", t('Block Container Class')) ?>
                                <?php echo $form->select("enableBlockContainer", $enableBlockContainerOptions, $enableBlockContainer ?? null); ?>
                            </div>

                            <hr/>
                        <?php } ?>

                        <div class="d-grid">
                            <?php echo $form->button("clearStyles", t('Clear Styles'), ["@click" => 'resetStyles', "data-action" => "reset-design"], "btn-danger"); ?>
                        </div>
                    </fieldset>
                </div>
            </div>
        </toolbar-section-widget>

        <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-cancel">
            <button type="button" class="btn ccm-input-button btn-secondary" @click="cancelDesign"><?=t('Cancel'); ?></button>
        </li>

        <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-save">
            <?php echo $form->button("saveDesign", t('Save'), ['@click'=>'saveDesign'], "btn-primary"); ?>
        </li>
    </ul>
</form>
<script type="text/javascript">
    $(function() {
        Concrete.Vue.activateContext('cms', function (Vue, config) {
           new Vue({
                el: 'ul.ccm-inline-toolbar',
                components: config.components,
                data: {
                    activeDropDown: null,
                    baseFontSize: <?=(int) str_replace('px', '', $baseFontSize)?>,
                    border: {
                        color: '<?=$borderColor?>',
                        width: <?=(int) str_replace('px', '', $borderWidth)?>,
                        radius: <?=(int) str_replace('px', '', $borderRadius)?>,
                        style: '<?=$borderStyle?>'
                    },
                    margin: {
                        left: <?=(int) str_replace('px', '', $marginLeft)?>,
                        top: <?=(int) str_replace('px', '', $marginTop)?>,
                        right: <?=(int) str_replace('px', '', $marginRight)?>,
                        bottom: <?=(int) str_replace('px', '', $marginBottom)?>,
                    },
                    padding: {
                        left: <?=(int) str_replace('px', '', $paddingLeft)?>,
                        top: <?=(int) str_replace('px', '', $paddingTop)?>,
                        right: <?=(int) str_replace('px', '', $paddingRight)?>,
                        bottom: <?=(int) str_replace('px', '', $paddingBottom)?>,
                    },
                    boxShadow: {
                        vertical: <?=(int) str_replace('px', '', $boxShadowVertical)?>,
                        horizontal: <?=(int) str_replace('px', '', $boxShadowHorizontal)?>,
                        blur: <?=(int) str_replace('px', '', $boxShadowBlur)?>,
                        spread: <?=(int) str_replace('px', '', $boxShadowSpread)?>,
                        color: '<?=$boxShadowColor?>',
                        inset: <?=$boxShadowInset ? 'true': 'false'?>,
                    },
                    background: {
                        position: '<?=$backgroundPosition?>',
                        size: '<?=$backgroundSize?>',
                        repeat: '<?=$backgroundRepeat?>',
                        color: '<?=$backgroundColor?>',
                    },
                    deviceClasses: <?=json_encode($jsonClasses ?? [])?>,
                    alignment: '<?=$alignment?>',
                    textColor: '<?=$textColor?>',
                    linkColor: '<?=$linkColor?>',
                    rotation: <?=(int) $rotate?>,
                    bFilename: '<?=$bFilename ?? null?>',
                    styleContainer: null,
                    customClass: <?=json_encode($customClassesSelected ?? [])?>,
                    aStyleRule: null,
                    isIgnoring: false,
                    pageId: <?=(int) $page->getCollectionID()?>,
                    blockId: <?=(int) $blockID?>,
                    areaId: <?=(int) $areaID?>,
                    isBlock: <?=$blockID ? 'true': 'false'?>,
                    cantFind: false,
                },
                methods: {
                    hideDevice(device) {
                        if (this.deviceClasses[device]) {
                            this.deviceClasses[device].hidden = this.deviceClasses[device].hidden === 0 ? 1 : 0;
                            if (this.deviceClasses[device].classes.length > 0) {
                                const classes = this.deviceClasses[device].classes.split(/\s+/);
                                for (let i = 0; i < classes.length; i++) {

                                    if (classes[i].length === 0) continue;

                                    if (this.deviceClasses[device].hidden) {
                                        this.styleContainer.classList.add(classes[i])
                                    } else {
                                        this.styleContainer.classList.remove(classes[i])
                                    }
                                }

                            }

                        }
                    },
                    isHidden(device) {
                        return this.deviceClasses[device] ? this.deviceClasses[device].hidden : false;
                    },
                    handleResponse(resp, callback = null) {
                        var editor = new Concrete.getEditMode()
                        var area = editor.getAreaByID(resp.aID)
                        var block = area.getBlockByID(parseInt(resp.originalBlockID))
                        var arEnableGridContainer = area.getEnableGridContainer() ? 1 : 0
                        var action = CCM_DISPATCHER_FILENAME + '/ccm/system/block/render';
                        const request = {
                            arHandle: area.getHandle(),
                            cID: resp.cID,
                            bID: resp.bID,
                            arEnableGridContainer: arEnableGridContainer
                        };
                        if (resp.tempFilename !== null) {
                            request.tempFilename = resp.tempFilename
                        }
                        $.get(action, request, (r)=> {
                            ConcreteToolbar.disableDirectExit()
                            var newBlock = block.replace(r)
                            ConcreteAlert.notify({
                                message: resp.message
                            })
                            if (callback !== null) {
                                return callback(newBlock);
                            }

                            this.refreshStyles(resp)
                            ConcreteEvent.fire('EditModeExitInline', {
                                action: 'save_inline',
                                block: newBlock
                            })
                            ConcreteEvent.fire('EditModeExitInlineComplete', {
                                block: newBlock
                            })
                            $.fn.dialog.hideLoader()
                            editor.destroyInlineEditModeToolbars()
                            editor.scanBlocks()
                        })
                    },
                    handleAreaResponse(resp) {
                        let editor = new Concrete.getEditMode()
                        let area = editor.getAreaByID(resp.aID)

                        this.refreshStyles(resp)
                        let keepClass = '';
                        if (area.getElem().hasClass('ccm-area')) {
                            keepClass += 'ccm-area';
                        }
                        if (area.getElem().hasClass('ccm-global-area')) {
                            if (keepClass.length > 0) {
                                keepClass += ' ';
                            }
                            keepClass += 'ccm-global-area';
                        }
                        area.getElem().removeClass();
                        if (keepClass.length > 0) {
                            area.getElem().addClass(keepClass);
                        }
                        if (resp.containerClass) {
                            area.getElem().addClass(resp.containerClass)
                        }
                        editor.destroyInlineEditModeToolbars()
                    },
                    handleFileSelect(file) {

                      if (!file) {
                          this.background.image = '';
                          return;
                      }

                      this.background.image = 'url(' + file.url + ')';
                      this.calculatePreview();

                    },
                    handleColorUpdate(style) {
                        let newValue ='';
                        if (style.value) {
                            newValue = 'rgba('+style.value.r+','+style.value.g+','+style.value.b+','+style.value.a+')';
                        }


                        if (style.variable === 'textColor') {
                            this.textColor = newValue;
                        } else if (style.variable === 'boxShadowColor') {
                            this.boxShadow.color = newValue;
                        } else if (style.variable === 'backgroundColor') {
                            this.background.color = newValue;
                        } else if (style.variable === 'borderColor') {
                            this.border.color = newValue;
                        } else {
                            this.linkColor = newValue;
                        }

                        this.calculatePreview();
                    },
                    calculatePreview()
                    {
                        if (!this.styleContainer && !this.cantFind) {
                            this.getStyleContainer();
                        }
                        if (this.cantFind) {
                            return;
                        }
                        const transform = 'rotate('+this.rotation+'deg)';
                        this.styleContainer.setAttribute('style', '{transform: '+transform+';-webkit-transform: '+transform+'; -o-transform: '+transform+'; -ms-transform: '+transform+'}');
                        this.styleContainer.style.transform= transform;
                        if (this.baseFontSize === 0) {
                            this.styleContainer.style.fontSize = '';
                        } else {
                            this.styleContainer.style.fontSize = this.baseFontSize + 'px';
                        }

                        this.styleContainer.style.color = this.textColor;
                        if (this.aStyleRule && !this.isIgnoring) {
                            this.aStyleRule.selectorText += '-ignore-me';
                            this.isIgnoring = true;
                        }

                        if (!this.inlineStyle) {
                            this.inlineStyle = document.createElement('style');
                            this.inlineStyle.setAttribute('data-block-id', this.blockId);
                            this.inlineStyle.setAttribute('data-area-id', this.areaId);
                            this.inlineStyle.setAttribute('data-custom-tag', this.blockId + '-'+this.areaId);
                            document.querySelector('body').appendChild(this.inlineStyle);
                        }
                        if (this.linkColor) {
                            this.inlineStyle.innerText = this.identifier + ' a {color: '+this.linkColor+' !important;}';
                        } else {
                            this.inlineStyle.innerText = '';
                        }
                        this.styleContainer.style.borderWidth = this.border.width + 'px';
                        this.styleContainer.style.borderColor = this.border.color;
                        this.styleContainer.style.borderRadius = this.border.radius + 'px';
                        this.styleContainer.style.borderStyle = this.border.style;

                        this.styleContainer.style.backgroundColor = this.background.color;
                        this.styleContainer.style.backgroundRepeat = this.background.repeat;
                        this.styleContainer.style.backgroundPosition = this.background.position;
                        this.styleContainer.style.backgroundImage = this.background.image;
                        this.styleContainer.style.backgroundSize = this.background.size;

                        this.styleContainer.style.paddingRight = this.padding.right + 'px';
                        this.styleContainer.style.paddingLeft = this.padding.left + 'px';
                        this.styleContainer.style.paddingTop = this.padding.top + 'px';
                        this.styleContainer.style.paddingBottom = this.padding.bottom + 'px';

                        this.styleContainer.style.marginRight = this.margin.right + 'px';
                        this.styleContainer.style.marginLeft = this.margin.left + 'px';
                        this.styleContainer.style.marginTop = this.margin.top + 'px';
                        this.styleContainer.style.marginBottom = this.margin.bottom + 'px';

                        this.styleContainer.style.boxShadow = this.calculateShadow();
                        this.styleContainer.style.textAlign = this.alignment;


                    },
                    calculateShadow() {
                        if (this.boxShadow.color === '') {
                            return '';
                        }


                        return (this.boxShadow.inset? 'inset ' : '') + this.boxShadow.horizontal + 'px ' + this.boxShadow.vertical + 'px ' +this.boxShadow.blur + 'px ' + this.boxShadow.spread + 'px ' +this.boxShadow.color;

                    },
                    getStyleContainer()
                    {
                        this.identifier = 'div[data-area-id="'+this.areaId+'"]'
                        if (this.isBlock) {
                            this.identifier += ' div[data-container="block"] div[data-block-id="'+this.blockId+'"]'
                        }
                        const container = document.querySelector(this.identifier);
                        if (!container) {
                            this.cantFind = true;
                            return;
                        }
                        if (!this.isBlock) {
                            this.styleContainer = container;
                            this.styleContainer.style.transition.property = 'all';
                            this.styleContainer.style.transition.duration = '200ms';
                            this.styleContainer.style.transition.timingFunction = 'ease-in-out';

                            return;
                        }
                        const innerContainer = document.querySelector(this.identifier + ' div.ccm-custom-style-container')
                        if (innerContainer) {
                            this.styleContainer = innerContainer;
                            let styleRule;
                            const regex = '/\^.ccm-custom-style-container.ccm-custom-style-[\w\d]+-'+this.blockId+' a$/i'
                            for (let i = 0; i < document.styleSheets.length; i++) {
                             if (document.styleSheets[i].href) {
                                 continue;
                             }
                                for (let j = 0; j < document.styleSheets[i].cssRules; j++) {
                                    if (document.styleSheets[i].cssRules[j].selectorText.match(regex)) {
                                        this.aStyleRule = document.styleSheets[i].cssRules[j];
                                    }
                                }
                            }
                            return;
                        }

                        const newDiv = document.createElement('div');
                        newDiv.classList.add('ccm-custom-style-container');
                        newDiv.innerHTML = container.innerHTML;
                        container.innerHTML = '';
                        container.appendChild(newDiv);
                        this.styleContainer = newDiv;
                        this.styleContainer.style.transition.property = 'all';
                        this.styleContainer.style.transition.duration = '200ms';
                        this.styleContainer.style.transition.timingFunction = 'ease-in-out';


                    },
                    toggleDropDown(name)
                    {
                        if (this.activeDropDown === name)  {
                            this.activeDropDown = null;
                        } else {
                            this.activeDropDown = name;
                        }

                    },
                    cancelDesign() {
                        if (this.aStyleRule) {
                            this.aStyleRule.selectorText = this.aStyleRule.selectorText.replace('-ignore-me', '');
                        }
                        if (this.inlineStyle) {
                            document.querySelector('body').removeChild(this.inlineStyle);
                            this.inlineStyle = null;
                        }
                        if(this.styleContainer) {
                            this.styleContainer.removeAttribute('style');
                            this.styleContainer = null;
                        }
                        $('#ccm-inline-toolbar-container').hide();
                        ConcreteEvent.fire('EditModeExitInline');
                    },
                    saveDesign() {
                        const form = $('#ccm-inline-design-form');
                        form.concreteAjaxForm({
                            success:(resp) => {
                                if (this.isBlock) {
                                    this.handleResponse(resp, null)
                                } else {
                                    this.handleAreaResponse(resp)
                                }

                            },
                            error:(r) => {
                                $(this.$el).prependTo('#ccm-inline-toolbar-container').show()
                            }
                        })
                        $(this.$el).hide().prependTo(form);
                        if (this.inlineStyle) {
                            document.querySelector('body').removeChild(this.inlineStyle);
                            this.inlineStyle = null;
                        }
                        form.submit();
                        ConcreteEvent.unsubscribe('EditModeExitInlineComplete');
                    },
                    resetStyles() {
                        this.bFilename = null,
                        $.concreteAjax({
                            url: '<?=$resetAction?>',
                            success: (resp)=>{
                                if (this.isBlock) {
                                    this.handleResponse(resp, null)
                                } else {
                                    this.handleAreaResponse(resp)
                                    this.styleContainer.removeAttribute('style');
                                    this.styleContainer = null;
                                }
                                if (this.inlineStyle) {
                                    document.querySelector('body').removeChild(this.inlineStyle);
                                    this.inlineStyle = null;
                                }
                            }
                        })

                    },
                    refreshStyles: function (resp) {
                        if (resp.oldIssID) {
                            $('head').find('style[data-style-set=' + resp.oldIssID + ']').remove()
                        }
                        if (resp.issID && resp.css) {
                            $('head').append(resp.css)
                        }
                    },
                    refreshTemplate()
                    {
                        const form = $('#ccm-inline-design-form').detach();
                        this.handleResponse({
                            cID: this.pageId,
                            bID: this.blockId,
                            aID: this.areaId,
                            originalBlockID: this.blockId,
                            tempFilename: this.bFilename ?? '',
                            message: '<?=t('Template Loaded')?>'
                        }, (block)=>{
                            block.setActive(true);
                            block.getElem().addClass('ccm-block-edit-inline-active');
                            block.getElem().find('.ccm-block-edit').addClass('ccm-block-edit-inline-active')
                            form.appendTo(block.getElem().parent());
                            this.styleContainer = null;
                            this.getStyleContainer();
                            this.calculatePreview();
                            if (this.customClass.length > 0) {
                                for (let i = 0; i < this.customClass.length; i++) {
                                    this.styleContainer.classList.add(this.customClass[i].trim());
                                }
                            }

                            $.fn.dialog.hideLoader();
                        })
                    },
                },
               mounted(){
                   $('#customClass').selectpicker({
                       liveSearch: true,
                       allowAdd: true
                   })
               },
               watch: {
                   customClass :{
                       immediate: true,
                       handler: function (newValue, oldValue) {

                           if (!this.styleContainer && !this.cantFind) {
                               this.getStyleContainer();
                           }
                           if (oldValue) {
                               if(Array.isArray(oldValue) && oldValue.length > 0) {
                                   this.styleContainer.classList.remove(...oldValue);
                               } else if (oldValue.length > 0) {
                                   this.styleContainer.classList.remove(oldValue.trim());
                               }
                           }
                           if (newValue) {
                               if(Array.isArray(newValue) && newValue.length > 0) {
                                   newValue = newValue.map(val=>val.trim())
                                   this.styleContainer.classList.add(...newValue);
                               } else if (newValue.length > 0) {
                                   this.styleContainer.classList.add(newValue.trim());
                               }
                           }
                       }
                   }
               }
            })
        })
    });
</script>
