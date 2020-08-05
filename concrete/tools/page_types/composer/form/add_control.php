<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php
use Concrete\Core\Page\Type\Composer\Control\Type\Type as PageTypeComposerControlType;
use Concrete\Core\Page\Type\Composer\FormLayoutSet as PageTypeComposerFormLayoutSet;

$c = Page::getByPath('/dashboard/pages/types/form');
$cp = new Permissions($c);

$set = PageTypeComposerFormLayoutSet::getByID($_REQUEST['ptComposerFormLayoutSetID']);
if (!is_object($set)) {
    die(t('Invalid set'));
}
if (!$cp->canViewPage()) {
    return;
}

if (isset($_POST['ptComposerControlTypeID']) && $_POST['ptComposerControlTypeID'] && $_POST['ptComposerControlIdentifier']) {
    $type = PageTypeComposerControlType::getByID($_POST['ptComposerControlTypeID']);
    $control = $type->getPageTypeComposerControlByIdentifier($_POST['ptComposerControlIdentifier']);
    $layoutSetControl = $control->addToPageTypeComposerFormLayoutSet($set);
    View::element('page_types/composer/form/layout_set/control', ['control' => $layoutSetControl]);
    exit;
}
?>
<div class="ccm-ui">
    <?php
    $tabs = [];
    $types = PageTypeComposerControlType::getList();
    $typesCount = count($types);
    for ($i = 0; $i < $typesCount; $i++) {
        $type = $types[$i];
        $tabs[] = [$type->getPageTypeComposerControlTypeHandle(), $type->getPageTypeComposerControlTypeDisplayName(), $i == 0];
    }
    echo app('helper/concrete/ui')->tabs($tabs);
    ?>
    
    <div class="tab-content ccm-tab-content" >
    <?php $i = 0; ?>
    <?php foreach ($types as $t) { ?>
        <div class="tab-pane fade <?= $i == 0 ? 'show active' : ''; ?> " id="<?=$t->getPageTypeComposerControlTypeHandle(); ?>">
            <input class="form-control ccm-input-text" type="text" name="attribute-search" placeholder="<?php echo t('Search attributes'); ?>" />
            <ul data-list="page-type-composer-control-type" class="item-select-list">
                <?php $controls = $t->getPageTypeComposerControlObjects(); ?>
                <?php foreach ($controls as $cnt) { ?>
                    <li>
                        <a href="#" data-control-type-id="<?=$t->getPageTypeComposerControlTypeID(); ?>" data-control-identifier="<?=$cnt->getPageTypeComposerControlIdentifier(); ?>">
                            <?=$cnt->getPageTypeComposerControlIcon(); ?>
                            <?=$cnt->getPageTypeComposerControlDisplayName(); ?>
                        </a>
                    </li>
                <?php $i++; } ?>
            </ul>
        </div>
    <?php } ?>
    </div>
</div>

<style type="text/css">
    ul.item-select-list li a {
        background-size: 16px 16px;
    }
</style>

<script type="text/javascript">
    $(function() {

        $('input[name="attribute-search"]').on('keyup', function() {
            var $search = $(this),
                $list = $search.siblings('[data-list="page-type-composer-control-type"]');

            $list.find('li').each(function() {
                if( this.innerText.toLowerCase().indexOf($search.val().toLowerCase()) === -1 ) {
                    $(this).hide();
                }
                else {
                    $(this).show();
                }
            });
        });

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
                'value': '<?=$set->getPageTypeComposerFormLayoutSetID(); ?>'
            }];
            jQuery.fn.dialog.showLoader();
            $.ajax({
                type: 'post',
                data: formData,
                url: '<?=REL_DIR_FILES_TOOLS_REQUIRED; ?>/page_types/composer/form/add_control',
                success: function(html) {
                    jQuery.fn.dialog.hideLoader();
                    jQuery.fn.dialog.closeTop();
                    $('div[data-page-type-composer-form-layout-control-set-id=<?=$set->getPageTypeComposerFormLayoutSetID(); ?>] tbody.ccm-item-set-inner').append(html);
                    $('a[data-command=edit-form-set-control]').dialog();
                }
            });

        });
    });
</script>
