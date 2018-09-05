<?php

namespace Concrete\Core\Form\Service\Widget;

use Concrete\Core\Entity\Site\Locale;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Multilingual\Service\UserInterface\Flag;
use Concrete\Core\Utility\Service\Identifier;
use View;

class SiteLocaleSelector
{
    /**
     * Creates form fields and JavaScript page chooser for choosing a locale.
     *
     * @param string $fieldName
     * @param \Concrete\Core\Entity\Site\Site $site
     * @param \Concrete\Core\Entity\Site\Locale|null $selectedLocale
     * @param array $options Supported options are:
     *     bool $allowNull Set to a non falsy value to allow users to choose "no" locale [default: false]
     *     string $noLocaleText The string to represent "no locale" [default: t('No Locale')]
     *
     * @return string
     */
    public function selectLocale($fieldName, Site $site, Locale $selectedLocale = null, array $options = [])
    {
        $v = View::getInstance();
        $v->requireAsset('core/app');

        $allowNull = !empty($options['allowNull']);
        $nullText = isset($options['noLocaleText']) ? $options['noLocaleText'] : t('No Locale');

        if ($selectedLocale === null && !$allowNull) {
            $selectedLocale = $site->getDefaultLocale();
        }

        $localeID = $selectedLocale ? $selectedLocale->getLocaleID() : '';

        $identifier = (new Identifier())->getString(32);

        $flag = $selectedLocale ? Flag::getLocaleFlagIcon($selectedLocale) : '';

        $label = $selectedLocale ? $selectedLocale->getLanguageText() : $nullText;

        $localeHTML = '';
        if ($allowNull) {
            $localeHTML .= '<li><a href="#"' . ($selectedLocale === null ? ' data-locale="default"' : '') . ' data-select-locale="">' . $nullText . '</li>';
        }
        foreach ($site->getLocales() as $locale) {
            $localeHTML .= '<li><a href="#" ';
            if ($selectedLocale && $selectedLocale->getLocaleID() == $locale->getLocaleID()) {
                $localeHTML .= 'data-locale="default"';
            }
            $localeHTML .= 'data-select-locale="' . $locale->getLocaleID() . '">';
            $localeHTML .= Flag::getLocaleFlagIcon($locale) . ' ' . $locale->getLanguageText() . '</a></li>';
        }

        $html = <<<EOL
        <input type="hidden" name="{$fieldName}" value="{$localeID}">
        <div class="btn-group" data-locale-selector="{$identifier}">
            <button type="button" class="btn btn-default" data-toggle="dropdown">
                {$flag} {$label}
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                {$localeHTML}
            </ul>
        </div>
        <script type="text/javascript">
            $(function() {
                $('[data-toggle=dropdown]').dropdown();
                $('div[data-locale-selector={$identifier}]').on('click', 'a[data-select-locale]', function(e) {
                    e.preventDefault();
                    var localeID = $(this).attr('data-select-locale'),
                        html = $(this).html() + ' <span class="caret"></span>',
                        form = $(this).closest('form');

                    form.find('input[name={$fieldName}]').val(localeID);
                    form.find('div[data-locale-selector={$identifier}] > button').html(html);
                });
            });
        </script>

EOL;

        return $html;
    }
}
