<?php
use \Concrete\Core\Localization\Localization;

header('Content-type: text/javascript; charset=UTF-8');

$locale = str_replace('_', '-', Localization::activeLocale());

if ($locale === 'en-US') {
    echo '// No needs to translate ' . $locale;
} else {
    $language = Localization::activeLanguage();
    $alternatives = array($locale);
    if (strcmp($locale, $language) !== 0) {
        $alternatives[] = $language;
    }
    $content = false;
    foreach ($alternatives as $alternative) {
        $path = DIR_BASE_CORE . '/' . DIRNAME_JAVASCRIPT . "/i18n/select2_locale_{$alternative}.js";
        if (is_file($path) && is_readable($path)) {
            $content = @file_get_contents($path);
            if (is_string($content)) {
                break;
            }
        }
    }
    if (is_string($content)) {
        echo $content;
    } else {
        echo '// No select2 translations for ' . implode(', ', $alternatives);
    }
}
