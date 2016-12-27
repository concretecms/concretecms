<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-dashboard-header-buttons">
    <button class="btn btn-primary" data-dialog-width="400" data-dialog-height="500"
            data-dialog="add-locale"><?= t('Add Locale') ?></button>
</div>

<h3><?= t('Locales') ?></h3>

<table class="table table-striped">
    <tr>
        <th style="width: 1%">&nbsp;</th>
        <th style="width: auto"><?php echo t('Home Page') ?></th>
        <th style="width: auto"><?php echo t('Language') ?></th>
        <th style="width: auto"><?php echo t('Locale') ?></th>
        <th style="width: 1%">&nbsp;</th>
    </tr>
    <?php
    /**
     * @var $locale \Concrete\Core\Entity\Site\Locale
     */
    $u = new User();
    foreach ($locales as $locale) {
        $home = null;
        if (is_object($locale->getSiteTree())) {
            $home = $locale->getSiteTree()->getSiteHomePageObject();
        }
        ?>
        <tr>
            <td><?php echo $flag->getLocaleFlagIcon($locale) ?></td>
            <td><?php if (is_object($home)) { ?><a
                    href="<?= $home->getCollectionLink() ?>"><?= $home->getCollectionName() ?></a>
                <?php } else { ?><span class="text-warning"><?= t('None Created.') ?></span>
                <?php } ?></td>
            <td><?php echo $locale->getLanguageText() ?></td>
            <td><?php echo $locale->getLocale() ?></td>
            <td><?php if (!$locale->getIsDefault()) { ?><a data-dialog-title="<?= t('Delete Locale') ?>"
                                                           data-dialog="delete-section-<?= $locale->getSiteLocaleID() ?>"
                                                           href="#" class="icon-link"><i
                            class="fa fa-trash"></i></a><?php } ?></td>
        </tr>
    <?php } ?>
</table>

<?php
foreach ($locales as $locale) {
    if (!$locale->getIsDefault() && $u->isSuperUser()) { ?>
        <div style="display: none">
            <div data-dialog-wrapper="delete-section-<?= $locale->getSiteLocaleID() ?>">
                <?php if ($u->isSuperUser()) { ?>
                    <form data-form="delete-locale-<?= $locale->getSiteLocaleID() ?>" method="post"
                          action="<?= $view->action('remove_locale_section') ?>">
                        <?= $token->output('remove_locale_section') ?>
                        <input type="hidden" name="siteLocaleID" value="<?= $locale->getSiteLocaleID() ?>">
                        <p><?= t('Delete this multilingual section? This will remove the entire site tree and its content from your website.') ?></p>
                        <div class="dialog-buttons">
                            <button class="btn btn-default" data-dialog-action="cancel"><?= t('Cancel') ?></button>
                            <button class="btn btn-danger pull-right"
                                    onclick="$('form[data-form=delete-locale-<?= $locale->getSiteLocaleID() ?>]').submit()"
                                    type="submit"><?= t('Delete') ?></button>
                        </div>
                    </form>
                <?php } else { ?>
                    <p><?= t('Only the super user may remove a multilingual section.') ?></p>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
<?php } ?>
<?php
$defaultLocales = array();
$defaultLocaleID = 0;
foreach ($locales as $locale) {
    $defaultLocales[$locale->getSiteLocaleID()] = sprintf('%s (%s)', $locale->getLanguageText(), $locale->getLocale());
    if ($locale->getIsDefault()) {
        $defaultLocaleID = $locale->getSiteLocaleID();
    }
}
?>
<h3><?php echo t('Settings') ?></h3>
<form method="post" action="<?php echo $this->action('set_default') ?>">
    <div class="form-group">
        <label class="control-label"><?php echo t('Default Locale'); ?></label>
        <?= $form->select('defaultLocale', $defaultLocales, $defaultLocaleID, array('required' => 'required')); ?>
    </div>

    <div class="form-group">
        <div class="checkbox">
            <label>
                <?php echo $form->checkbox('useBrowserDetectedLocale', 1, $useBrowserDetectedLocale) ?>
                <span><?php echo t('Attempt to use visitor\'s locale based on their browser information.') ?></span>
            </label>
        </div>
        <div class="checkbox">
            <label>
                <?php echo $form->checkbox('redirectHomeToDefaultLocale', 1, $redirectHomeToDefaultLocale) ?>
                <span><?php echo t('Redirect home page to default locale.') ?></span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label"><?php echo t('Site interface source locale');
            ?></label>
        <div class="form-inline">
            <?php
            echo $form->select('defaultSourceLanguage',
                array_merge(array('' => t('*** Unknown or mixed language')), $languages), $defaultSourceLanguage);
            ?>

            <?php
            echo $form->select('defaultSourceCountry',
                array_merge(array('' => t('*** Undetermined country')), $countries), $defaultSourceCountry);
            ?>
        </div>
        <script>
            $(document).ready(function () {
                new ccmCountryForLanguageLister($('#defaultSourceLanguage'), $('#defaultSourceCountry'));
            });
        </script>
    </div>

    <div class="form-group">
        <?php echo Loader::helper('validation/token')->output('set_default') ?>
        <button class="btn btn-default" type="submit" name="save"><?= t('Save Settings') ?></button>
    </div>
</form>


<div style="display: none">
    <div data-dialog-wrapper="add-locale">
        <form data-dialog-form="add-locale" action="<?= $view->action('add_content_section') ?>">
            <fieldset>
                <legend><?= t('Locale') ?></legend>
                <div class="form-group">
                    <?php echo $form->label('msLanguage', t('Choose Language')) ?>
                    <?php echo $form->select('msLanguage', $languages); ?>
                </div>
                <div class="form-group">
                    <?php echo $form->label('msCountry', t('Choose Country')) ?>
                    <?php echo $form->select('msCountry',
                        array_merge(array('' => t('** None Selected')), $countries)); ?>
                </div>
                <div class="form-group">
                    <label class="control-label"><?php echo t('Icon') ?></label>
                    <div id="ccm-multilingual-language-icon"><?php echo t('None') ?></div>
                </div>
            </fieldset>
            <fieldset>
                <legend><?= t('Home Page') ?></legend>
                <div class="form-group">
                    <?= $form->label('template', t('Template')) ?>
                    <?= $form->select('template', $templates) ?>
                </div>
                <div class="form-group">
                    <?= $form->label('homePageName', t('Page Name')) ?>
                    <?= $form->text('homePageName') ?>
                </div>
                <div class="form-group">
                    <?= $form->label('URL Slug', t('URL Slug')) ?>
                    <?= $form->text('urlSlug') ?>
                </div>
            </fieldset>
            <div class="dialog-buttons">
                <button class="btn btn-default pull-left" data-dialog-action="cancel"><?= t('Cancel') ?></button>
                <button class="btn btn-primary pull-right" data-dialog-action="submit"><?= t('Add Locale') ?></button>
            </div>
            <?php echo $token->output('add_content_section') ?>
        </form>
    </div>
</div>

<script type="text/javascript">
    var ccmCountryForLanguageLister = (function () {
        var countryDictionary = <?php echo json_encode($countries); ?>;
        var sortedCountries = (function () {
            var list = [];
            $.each(countryDictionary, function (id) {
                list.push(id);
            });
            list.sort(function (aCode, bCode) {
                var aName = countryDictionary[aCode].toLowerCase(), bName = countryDictionary[bCode].toLowerCase();
                if (aName < bName) {
                    return -1;
                }
                if (aName > bName) {
                    return 1;
                }
                return 0;
            });
            return list;
        })();
        var cache = {'': []};

        function appendCountries($parent, countryCodes) {
            $.each(countryCodes, function (_, countryCode) {
                $parent.append($('<option />').val(countryCode).text(countryDictionary[countryCode]));
            });
        }

        function updateCountrySelect($country, preferredCountryCodes) {
            var preferredCountries = [];
            $.each(preferredCountryCodes, function (_, countryCode) {
                if (countryCode in countryDictionary) {
                    preferredCountries.push(countryCode);
                }
            });
            var selectedCountry = $country.val();
            $country.empty().append($('<option value="" />').text(<?php echo json_encode(t('** None Selected')); ?>));
            if (preferredCountries.length) {
                var otherCountries = [];
                $.each(sortedCountries, function (_, countryCode) {
                    if ($.inArray(countryCode, preferredCountries) < 0) {
                        otherCountries.push(countryCode);
                    }
                });
                var $group;
                $country.append($group = $('<optgroup />').attr('label', <?php echo json_encode(t('Suggested countries')); ?>));
                appendCountries($group, preferredCountries);
                $country.append($group = $('<optgroup />').attr('label', <?php echo json_encode(t('Other countries')); ?>));
                appendCountries($group, otherCountries);
            }
            else {
                appendCountries($country, sortedCountries);
            }
            $country.val(selectedCountry);
        }

        function CountryForLanguageLister($language, $country) {
            var me = this;
            this.$language = $language;
            this.$country = $country;
            this.$language.change(function () {
                me.updateCountries();
            });
            this.currentLanguage = null;
            this.updateCountries();
        }

        CountryForLanguageLister.prototype = {
            updateCountries: function () {
                var me = this;
                var language = this.$language.val();
                if (language === '') {
                    this.$country.attr('disabled', 'disabled');
                }
                else {
                    this.$country.removeAttr('disabled');
                }
                if (language === this.currentLanguage) {
                    return;
                }
                this.currentLanguage = language;
                if (language in cache) {
                    updateCountrySelect(this.$country, cache[language]);
                    return;
                }
                updateCountrySelect(this.$country, []);
                $.get(
                    <?php echo json_encode($view->action('get_countries_for_language')); ?>,
                    {language: language},
                    function (data) {
                        cache[language] = data ? data : [];
                        if (me.currentLanguage === language) {
                            updateCountrySelect(me.$country, cache[language]);
                        }
                    },
                    'json'
                );
            }
        };
        return CountryForLanguageLister;
    })();

    $(function () {
        new ccmCountryForLanguageLister($('#msLanguage'), $('#msCountry'));
        $("select[name=msCountry]").change(function () {
            ccm_multilingualPopulateIcons($(this).val());
        });
        ccm_multilingualPopulateIcons($("select[name=msCountry]").val());

        ConcreteEvent.subscribe('AjaxFormSubmitSuccess.addLocale', function (e, data) {
            if (data.form == 'add-locale') {
                window.location.reload();
            }
        });
    });

    ccm_multilingualPopulateIcons = function (country) {
        if (country && country != '') {
            $("#ccm-multilingual-language-icon").load('<?php echo $view->action("load_icon")?>', {'msCountry': country});
        }
    };

</script>


</script>