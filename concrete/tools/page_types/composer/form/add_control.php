<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
use \Concrete\Core\Page\Type\Composer\FormLayoutSet as PageTypeComposerFormLayoutSet;
use \Concrete\Core\Page\Type\Composer\Control\Type\Type as PageTypeComposerControlType;

$c = Page::getByPath('/dashboard/pages/types/form');
$cp = new Permissions($c);
$ih = Loader::helper('concrete/ui');
$set = PageTypeComposerFormLayoutSet::getByID($_REQUEST['ptComposerFormLayoutSetID']);
if (!is_object($set)) {
    die(t('Invalid set'));
}
if ($cp->canViewPage()) {
    if ($_POST['ptComposerControlTypeID'] && $_POST['ptComposerControlIdentifier']) {
        $type = PageTypeComposerControlType::getByID($_POST['ptComposerControlTypeID']);
        $control = $type->getPageTypeComposerControlByIdentifier($_POST['ptComposerControlIdentifier']);
        $layoutSetControl = $control->addToPageTypeComposerFormLayoutSet($set);
        Loader::element('page_types/composer/form/layout_set/control', array('control' => $layoutSetControl));
        exit;
    }

    ?>

	<div class="ccm-ui">
	<?php
    $tabs = array();
    $types = PageTypeComposerControlType::getList();
    for ($i = 0; $i < count($types); ++$i) {
        $type = $types[$i];
        $tabs[] = array($type->getPageTypeComposerControlTypeHandle(), $type->getPageTypeComposerControlTypeDisplayName(), $i == 0);
    }

    echo $ih->tabs($tabs);

    foreach ($types as $t) {
        ?>

	<div class="ccm-tab-content" id="ccm-tab-content-<?=$t->getPageTypeComposerControlTypeHandle()?>">
	<ul data-list="page-type-composer-control-type" class="item-select-list">
		<?php
        $controls = $t->getPageTypeComposerControlObjects();
        foreach ($controls as $cnt) {
            ?>
			<li><a href="#" data-control-type-id="<?=$t->getPageTypeComposerControlTypeID()?>" data-control-identifier="<?=$cnt->getPageTypeComposerControlIdentifier()?>">
                    <?=$cnt->getPageTypeComposerControlIcon()?>
                    <?=$cnt->getPageTypeComposerControlDisplayName()?></a></li>
		<?php
        }
        ?>
	</ul>
	</div>


	<?php
    }
    ?>

	</div>

<style type="text/css">
	ul.item-select-list li a {
		background-size: 16px 16px;
	}
</style>

<script type="text/javascript">
$(function() {
	$('ul[data-list=page-type-composer-control-type] a').on('click', function() {
		var ptComposerControlTypeID = $(this).attr('data-control-type-id');
		var ptComposerControlIdentifier = $(this).attr('data-control-identifier');
		var formData = [{
			'name': 'ptComposerControlTypeID',
			'value': ptComposerControlTypeID
		},{
			'name': 'ptComposerControlIdentifier',
			'value': ptComposerControlIdentifier
		},{
			'name': 'ptComposerFormLayoutSetID',
			'value': '<?=$set->getPageTypeComposerFormLayoutSetID()?>'
		}];
		jQuery.fn.dialog.showLoader();
		$.ajax({
			type: 'post',
			data: formData,
			url: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/page_types/composer/form/add_control',
			success: function(html) {
				jQuery.fn.dialog.hideLoader();
				jQuery.fn.dialog.closeTop();
				$('div[data-page-type-composer-form-layout-control-set-id=<?=$set->getPageTypeComposerFormLayoutSetID()?>] tbody.ccm-item-set-inner').append(html);
				$('a[data-command=edit-form-set-control]').dialog();
			}
		});

	});
});
</script>


<?php

}
