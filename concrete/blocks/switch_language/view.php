<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\Block\View\BlockView $view */

/* @var string $label The text label as configured by the user */

/* @var array $languages Array keys are the multilingual section IDs, array values are the language names (in their own language) */
/* @var Concrete\Core\Multilingual\Page\Section\Section[] $languageSections The list of multilanguage language sections */
/* @var int|null $activeLanguage The ID of the currently active multilingual section (if available) */
/* @var Concrete\Core\Multilingual\Page\Section\Section $defaultLocale The default multilingual section */
/* @var string $locale The current language code (without Country code) */
/* @var int $cID The ID of the current page */
?>

<div class="ccm-block-switch-language">

    <form method="post" class="form-inline">
        <?= $label ?>
        <?= $form->select(
            'language',
            $languages,
            $activeLanguage,
            [
                'data-select' => 'multilingual-switch-language',
                'data-action' => $view->action('switch_language', $cID, '--language--'),
            ]
        ) ?>
    </form>

</div>