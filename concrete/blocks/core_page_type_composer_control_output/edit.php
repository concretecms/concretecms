<?php
defined('C5_EXECUTE') or die('Access Denied.');
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Template as PageTemplate;
use Concrete\Core\Page\Type\Composer\FormLayoutSetControl as PageTypeComposerFormLayoutSetControl;
use Concrete\Core\Page\Type\Composer\OutputControl as PageTypeComposerOutputControl;
use Concrete\Core\Page\Type\Type as PageType;

/** @var string|int|null $ptComposerOutputControlID */
/** @var \Concrete\Core\Form\Service\Form $form */

$ptComposerOutputControlID = $ptComposerOutputControlID ?? null;

$c = Page::getCurrentPage();
    // retrieve all block controls attached to this page template.
    $pt = PageTemplate::getByID($c->getPageTemplateID());
    $ptt = PageType::getByDefaultsPage($c);
    $controls = PageTypeComposerOutputControl::getList($ptt, $pt);
    $values = [];
    foreach ($controls as $control) {
        $fls = PageTypeComposerFormLayoutSetControl::getByID($control->getPageTypeComposerFormLayoutSetControlID());
        if ($fls->getPageTypeComposerFormLayoutSetControlCustomLabel()) {
            $displayname = $fls->getPageTypeComposerFormLayoutSetControlCustomLabel();
        } else {
            $cc = $fls->getPageTypeComposerControlObject();
            $displayname = $cc->getPageTypeComposerControlDisplayName();
        }
        $values[$control->getPageTypeComposerOutputControlID()] = $displayname;
    }
?>
<div class="form-group">
	<label for="ptComposerOutputControlID" class="control-label form-label"><?=t('Control')?></label>
	<?=$form->select('ptComposerOutputControlID', $values, $ptComposerOutputControlID ?? '')?>
</div>
