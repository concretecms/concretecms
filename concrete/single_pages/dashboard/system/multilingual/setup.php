<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-dashboard-header-buttons">
    <button class="btn btn-primary" data-dialog="add-locale"><?= t('Add Locale') ?></button>
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
                <?php } else { ?><span class="text-warning"><?=t('None Created.')?></span>
            <?php } ?></td>
            <td><?php echo $locale->getLanguageText() ?></td>
            <td><?php echo $locale->getLocale() ?></td>
            <td><a href="<?php echo $this->action('remove_locale_section', $locale->getSiteLocaleID(),
                    Loader::helper('validation/token')->generate()) ?>" class="icon-link"><i
                        class="fa fa-trash"></i></a></td>
        </tr>
    <?php } ?>
</table>

<div style="display: none">
    <div data-dialog-wrapper="add-locale">
        <form data-dialog-form="add-locale" action="<?= $view->action('add_content_section') ?>">
            <div class="form-group">
                <?php echo $form->label('msLanguage', t('Choose Language')) ?>
                <?php echo $form->select('msLanguage', $languages); ?>
            </div>
            <div class="form-group">
                <?php echo $form->label('msCountry', t('Choose Country')) ?>
                <?php echo $form->select('msCountry',
                    array_merge(array('' => t('*** Undetermined country')), $countries)); ?>
            </div>
            <div class="form-group">
                <label class="control-label"><?php echo t('Language Icon') ?></label>
                <div id="ccm-multilingual-language-icon"><?php echo t('None') ?></div>
            </div>
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
            $country.empty().append($('<option value="" />').text(<?php echo json_encode(t('*** Undetermined country')); ?>));
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