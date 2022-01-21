<?php
    defined('C5_EXECUTE') or die('Access Denied.');
    /** @var int|null $ptComposerOutputControlID */
    /** @var \Concrete\Core\Block\Block $b */
    use Concrete\Core\Page\Type\Composer\FormLayoutSetControl as PageTypeComposerFormLayoutSetControl;
    use Concrete\Core\Page\Type\Composer\OutputControl as PageTypeComposerOutputControl;

    $control = PageTypeComposerOutputControl::getByID($ptComposerOutputControlID);
    if (is_object($control)) {
        $fls = PageTypeComposerFormLayoutSetControl::getByID($control->getPageTypeComposerFormLayoutSetControlID());
        $cc = $fls->getPageTypeComposerControlObject();
        if (is_object($cc)) {
            ?>
	<div class="ccm-ui">
		<div class="alert alert-info">
			<?php if ($fls->getPageTypeComposerFormLayoutSetControlCustomLabel()) {
    $displayname = $fls->getPageTypeComposerFormLayoutSetControlCustomLabel();
} else {
    $displayname = $cc->getPageTypeComposerControlDisplayName();
}
            echo t('The %s page type composer form element will output its contents here (Block ID %s)', $displayname, $b->getBlockID());
            ?>
		</div>
	</div>
	<?php
        }
        ?>
<?php
    } ?>