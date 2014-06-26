<?php
defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Page\Style;

$c = Page::getByID($_REQUEST['cID'], 'RECENT');
$a = Area::get($c, $_REQUEST['arHandle']);
if (!is_object($a)) {
    die('Invalid Area');
}
if (!$a->isGlobalArea()) {
    $b = Block::getByID($_REQUEST['bID'], $c, $a);
} else {
    $stack = Stack::getByName($_REQUEST['arHandle']);
    $sc = Page::getByID($stack->getCollectionID(), 'RECENT');
    $b = Block::getByID($_REQUEST['bID'], $sc, STACKS_AREA_NAME);
    $b->setBlockAreaObject($a); // set the original area object
    $isGlobalArea = true;
}

if (!is_object($b)) {
    echo '<div class="ccm-ui"><div class="alert alert-error">';
    echo(t("Unable to retrieve block object. If this block has been moved please reload the page."));
    echo '</div></div';
    exit;
}

$bp = new Permissions($b);
$ap = new Permissions($a);
if (!$bp->canViewEditInterface()) {
    die(t("Access Denied."));
}

if ($_REQUEST['btask'] != 'view' && $_REQUEST['btask'] != 'view_edit_mode') {
    include(DIR_FILES_ELEMENTS_CORE . '/dialog_header.php');
}

$bv = new BlockView($b);

if ($isGlobalArea && $_REQUEST['btask'] != 'view_edit_mode') {
    echo '<div class="ccm-ui"><div class="alert alert-warning">';
    echo t(
        'This block is contained within a global area. Changing its content will change it everywhere that global area is referenced.');
    echo('</div></div>');
}

if (($c->isMasterCollection()) && (!in_array($_REQUEST['btask'], array('child_pages', 'view_edit_mode')))) {
    echo '<div class="ccm-ui"><div class="alert alert-warning">';
    echo t('This is a global block.  Editing it here will change all instances of this block throughout the site.');
    //echo t('This is a global block.  Edit it from the <a href="%s">Global Scrapbook</a> in your dashboard.<br /><br /><br />', View::url('/dashboard/scrapbook/') );
    //echo '[<a class="ccm-dialog-close">'.t('Close Window').'</a>]';
    echo '</div></div>';
}

if ($b->isAliasOfMasterCollection() && $_REQUEST['btask'] != 'view_edit_mode') {
    echo '<div class="ccm-ui"><div class="alert alert-warning">';
    echo t(
        'This block is an alias of Page Defaults. Editing it here will "disconnect" it so changes to Page Defaults will no longer affect this block.');
    echo '</div></div>';
}

$request = Request::getInstance();
$request->setCurrentPage($c);

if (is_object($b)) {
    switch ($_REQUEST['btask']) {
        case 'block_css':
            if ($bp->canEditBlockDesign()) {
                $style = $b->getBlockCustomStyleRule();
                $action = $b->getBlockUpdateCssAction();
                if ($_REQUEST['subtask'] == 'delete_custom_style_preset') {
                    $styleToDelete = CustomStylePreset::getByID($_REQUEST['deleteCspID']);
                    $styleToDelete->delete();
                }
                $refreshAction = REL_DIR_FILES_TOOLS_REQUIRED . '/edit_block_popup?btask=block_css&cID=' . $c->getCollectionID() . '&arHandle=' . $a->getAreaHandle() . '&bID=' . $b->getBlockID() . '&refresh=1';
                Loader::element(
                      'custom_style',
                      array(
                          'b'             => $b,
                          'rcID'          => $rcID,
                          'c'             => $c,
                          'a'             => $a,
                          'style'         => $style,
                          'action'        => $action,
                          'refreshAction' => $refreshAction));
            }
            break;
        case 'template':
            if ($bp->canEditBlockCustomTemplate()) {
                Loader::element('block_custom_template', array('b' => $b, 'rcID' => $rcID));
            }
            break;
        case 'view':
            if ($bp->canViewBlock()) {
                $bv->addScopeItems(
                   array(
                       'c' => $c,
                       'a' => $a
                   ));
                $bv->render('view');
            }
            break;
        case 'view_edit_mode':
            if ($bp->canViewEditInterface()) {

                $btc = $b->getInstance();
                // now we inject any custom template CSS and JavaScript into the header
                if ('Controller' != get_class($btc)) {
                    $btc->outputAutoHeaderItems();
                }

                $v = View::getInstance();

                $csr = $b->getBlockCustomStyleRule();
                if (is_object($csr)) {
                    $styleHeader = '#' . $csr->getCustomStyleRuleCSSID(
                                             1) . ' {' . $csr->getCustomStyleRuleText() . "}";  ?>
                    <script type="text/javascript">
                        $('head').append('<style type="text/css"><?=addslashes($styleHeader)?></style>');
                    </script>
                <?
                }

                Loader::element(
                      'block_header',
                      array(
                          'a' => $a,
                          'b' => $b,
                          'p' => $bp
                      ));

                // we make sure that our active theme gets registered as well because we want to make sure that
                // assets provided by the theme aren't loaded by the block in this mode.
                $pt = $c->getCollectionThemeObject();
                $pt->registerAssets();
                $bv->render('view');
                Loader::element('block_footer');
            }
            break;
        case 'groups':
            if ($bp->canEditBlockPermissions()) {
                Loader::element('permission/lists/block', array('b' => $b, 'rcID' => $rcID));
            }
            break;
        case 'set_advanced_permissions':
            if ($bp->canEditBlockPermissions()) {
                Loader::element('permission/details/block', array('b' => $b, 'rcID' => $rcID));
            }
            break;
        case 'guest_timed_access':
            if ($bp->canScheduleGuestAccess() && $bp->canGuestsViewThisBlock()) {
                Loader::element('permission/details/block/timed_guest_access', array('b' => $b, 'rcID' => $rcID));
            }
            break;

        case 'child_pages':
            if ($bp->canAdminBlock()) {
                Loader::element('block_master_collection_alias', array('b' => $b));
            }
            break;
        case 'edit':
            if ($bp->canWrite()) {

                // Handle special posted area parameters here
                if (isset($_REQUEST['arGridColumnSpan'])) {
                    $a->setAreaGridColumnSpan(intval($_REQUEST['arGridColumnSpan']));
                }
                $bv->addScopeItems(
                   array(
                       'c'    => $c,
                       'a'    => $a,
                       'rcID' => $rcID
                   ));
                $bv->render('edit');
            }
            break;
    }
}

if ($_REQUEST['btask'] != 'view' && $_REQUEST['btask'] != 'view_edit_mode') {
    include(DIR_FILES_ELEMENTS_CORE . '/dialog_footer.php');
}
