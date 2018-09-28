<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\Block\View\BlockView $view */

// The text label as configured by the user
/* @var string $label */

// Array keys are the multilingual section IDs, array values are the language names (in their own language)
/* @var array $languages */

// The list of multilanguage language sections
/* @var Concrete\Core\Multilingual\Page\Section\Section[] $languageSections */

// The ID of the currently active multilingual section (if available)
/* @var int|null $activeLanguage */

// The default multilingual section
/* @var Concrete\Core\Multilingual\Page\Section\Section $defaultLocale */

// The current language code (without Country code)
/* @var string $locale */

// The ID of the current page
/* @var int $cID */
$ih = Core::make('multilingual/interface/flag');
?>
<div class="ccm-block-switch-language-flags">
    <div class="ccm-block-switch-language-flags-label"><?= $label ?></div>
    <?php
    foreach ($languageSections as $ml) {
        ?>
        <a href="<?= $controller->resolve_language_url($cID, $ml->getCollectionID()) ?>" title="<?= $languages[$ml->getCollectionID()] ?>" class="<?php if ($activeLanguage == $ml->getCollectionID()) { ?>ccm-block-switch-language-active-flag<?php } ?>"><?= $ih->getSectionFlagIcon($ml) ?></a>
        <?php
    }
    ?>
</div>