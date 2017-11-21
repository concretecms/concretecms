<?php
namespace Concrete\Core\Form\Service\Widget;

use Concrete\Core\Entity\Site\Locale;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Multilingual\Service\UserInterface\Flag;

class SiteLocaleSelector
{
    /**
     * Creates form fields and JavaScript page chooser for choosing a locale.
     */
    public function selectLocale($fieldName, Site $site, Locale $selectedLocale = null)
    {
        $v = \View::getInstance();
        $v->requireAsset('core/app');

        if (!$selectedLocale) {
            $selectedLocale = $site->getDefaultLocale();
        }

        $localeID = $selectedLocale->getLocaleID();

        $identifier = new \Concrete\Core\Utility\Service\Identifier();
        $identifier = $identifier->getString(32);
        $flag = Flag::getLocaleFlagIcon($selectedLocale);
        $label = $selectedLocale->getLanguageText();

        $locales = $site->getLocales();
        $localeHTML = '';
        foreach ($locales as $locale) {
            $locales = $site->getLocales();
            $localeHTML .= '<li><a href="#" ';
            if ($selectedLocale->getLocaleID() == $locale->getLocaleID()) {
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
