<?php defined('C5_EXECUTE') or die("Access Denied.");?>
<?
use Concrete\Core\Multilingual\Page\Section\Section as MultilingualSection;
?>
<script type="text/javascript">
var ccmCountryForLanguageLister = (function() {
    var countryDictionary = <?php echo json_encode($countries); ?>;
    var sortedCountries = (function() {
        var list = [];
        $.each(countryDictionary, function(id) {
           list.push(id);
        });
        list.sort(function(aCode, bCode) {
           var aName = countryDictionary[aCode].toLowerCase(), bName = countryDictionary[bCode].toLowerCase();
           if(aName < bName) {
               return -1;
           }
           if(aName > bName) {
               return 1;
           }
           return 0;
        });
        return list;
    })();
    var cache = {'': []};
    function appendCountries($parent, countryCodes) {
        $.each(countryCodes, function(_, countryCode) {
           $parent.append($('<option />').val(countryCode).text(countryDictionary[countryCode]));
        });
    }
    function updateCountrySelect($country, preferredCountryCodes) {
        var preferredCountries = [];
        $.each(preferredCountryCodes, function(_, countryCode) {
            if(countryCode in countryDictionary) {
                preferredCountries.push(countryCode);
            }
        });
        var selectedCountry = $country.val();
        $country.empty().append($('<option value="" />').text(<?php echo json_encode(t('*** Undetermined country')); ?>));
        if(preferredCountries.length) {
            var otherCountries = [];
            $.each(sortedCountries, function(_, countryCode) {
                if($.inArray(countryCode, preferredCountries) < 0) {
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
        this.$language.change(function() {
            me.updateCountries();
        });
        this.currentLanguage = null;
        this.updateCountries();
    }
    CountryForLanguageLister.prototype = {
        updateCountries: function() {
            var me = this;
            var language = this.$language.val();
            if(language === '') {
                this.$country.attr('disabled', 'disabled');
            }
            else {
                this.$country.removeAttr('disabled');
            }
            if(language === this.currentLanguage) {
                return;
            }
            this.currentLanguage = language;
            if(language in cache) {
                updateCountrySelect(this.$country, cache[language]);
                return;
            }
            updateCountrySelect(this.$country, []);
            $.get(
                <?php echo json_encode($view->action('get_countries_for_language')); ?>,
                {language: language},
                function(data) {
                    cache[language] = data ? data : [];
                    if(me.currentLanguage === language) {
                        updateCountrySelect(me.$country, cache[language]);
                    }
                },
                'json'
            );
        }
    };
    return CountryForLanguageLister;
})();
</script>
<fieldset>
    <legend><?php echo t('Content Sections')?></legend>
    <?php
    $nav = Loader::helper('navigation');
    if (count($pages) > 0) { ?>
        <table class="table table-striped">
        <tr>
            <th>&nbsp;</th>
            <th style="width: 45%"><?php echo t("Name")?></th>
            <th style="width: auto"><?php echo t('Locale')?></th>
            <th style="width: 30%"><?php echo t('Path')?></th>
            <th>&nbsp;</th>
        </tr>
        <?php foreach($pages as $pc) {
            $pcl = MultilingualSection::getByID($pc->getCollectionID()); ?>
            <tr>
                <td><?php echo $ch->getSectionFlagIcon($pc)?></td>
                <td><a href="<?php echo $nav->getLinkToCollection($pc)?>"><?php echo $pc->getCollectionName()?></a></td>
                <td><?php echo $pcl->getLanguageText()?> (<?php echo $pcl->getLocale();?>)</td>
                <td><?php echo $pc->getCollectionPath()?></td>
                <td><a href="<?php echo $this->action('remove_locale_section', $pc->getCollectionID(), Loader::helper('validation/token')->generate())?>" class="icon-link"><i class="fa fa-trash"></i></a></td>
            </tr>
        <?php } ?>
        </table>

    <?php } else { ?>
        <p><?php echo t('You have not created any multilingual content sections yet.')?></p>
    <?php } ?>
    <form method="post" action="<?php echo $this->action('add_content_section')?>">
        <h4><?php echo t('Add a Locale')?></h4>
        <div class="form-group">
            <label class="control-label"><?php echo t('Choose a Parent Page')?></label>
            <?php echo Loader::helper('form/page_selector')->selectPage('pageID', '')?>
        </div>
        <div class="form-group">
            <?php echo $form->label('msLanguage', t('Choose Language'))?>
            <?php echo $form->select('msLanguage', $languages);?>
        </div>
        <div class="form-group">
            <?php echo $form->label('msCountry', t('Choose Country'))?>
            <?php echo $form->select('msCountry', array_merge(array('' => t('*** Undetermined country')), $countries));?>
        </div>

        <div class="form-group">
            <label class="control-label"><?php echo t('Language Icon')?></label>
            <div id="ccm-multilingual-language-icon"><?php echo t('None')?></div>
        </div>
        <div class="alert alert-info"><?=t('Your locale will be computed from your choice of language and country. Your language icon will be chosen based on the country.')?></div>

        <div class="form-group">
            <?php echo Loader::helper('validation/token')->output('add_content_section')?>
            <button class="btn btn-default pull-left" type="submit" name="add"><?=t('Add Content Section')?></button>
        </div>
    </form>
</fieldset>

<div class="spacer-row-6"></div>

<script type="text/javascript">
$(function() {
	new ccmCountryForLanguageLister($('#msLanguage'), $('#msCountry'));
	$("select[name=msCountry]").change(function() {
		ccm_multilingualPopulateIcons($(this).val());
	});
	ccm_multilingualPopulateIcons($("select[name=msCountry]").val());
});

ccm_multilingualPopulateIcons = function(country) {
    if (country && country != '') {
	    $("#ccm-multilingual-language-icon").load('<?php echo $view->action("load_icon")?>', {'msCountry': country});
	}
};

</script>


<hr/>
<?php if (count($pages) > 0) {
	$defaultLocales = array('' => t('** None Set'));
	foreach($pages as $pc) {
		$pcl = MultilingualSection::getByID($pc->getCollectionID());
		$defaultLocales[$pcl->getLocale()] = tc(/*i18n: %1$s is a page name, %2$s is a language name, %3$s is a locale identifier (eg en_US)*/'PageWithLocale', '%1$s (%2$s, %3$s)', $pc->getCollectionName(), $pcl->getLanguageText(), $pcl->getLocale());
	}
	$defaultLocalesSelect = $form->select('defaultLocale', $defaultLocales, $defaultLocale, array('required' => 'required'));
	?>
   <legend><?php echo t('Multilingual Settings')?></legend>
	<fieldset>
        <form method="post" action="<?php echo $this->action('set_default')?>">
            <div class="form-group">
                <label class="control-label"><?php echo t('Default Locale');?></label>
                <?php print $defaultLocalesSelect; ?>
            </div>

            <div class="form-group">
                <div class="checkbox">
                <label>
                    <?php echo $form->checkbox('useBrowserDetectedLocale', 1, $useBrowserDetectedLocale)?>
                    <span><?php echo t('Attempt to use visitor\'s locale based on their browser information.') ?></span>
                </label>
                </div>
                <div class="checkbox">
                <label>
                    <?php echo $form->checkbox('redirectHomeToDefaultLocale', 1, $redirectHomeToDefaultLocale)?>
                    <span><?php echo t('Redirect home page to default locale.') ?></span>
                </label>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label"><?php echo t('Registered Users Language Preferences'); ?></label>
                <p><?php echo t('Load Interface Language from:'); ?></p>
                <div class="radio">
                    <label>
                        <?php echo $form->radio('keepUsersLocale', 0, $keepUsersLocale); ?>
                        <span><?php echo t('Page (if it exists)'); ?></span>
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <?php echo $form->radio('keepUsersLocale', 1, $keepUsersLocale); ?>
                        <span><?php echo t("User Profile"); ?></span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label"><?php echo t('Site interface source locale');?></label>
                <div class="form-inline">
                <?php
                echo $form->select('defaultSourceLanguage', array_merge(array('' => t('*** Unknown or mixed language')), $languages), $defaultSourceLanguage);
                ?>

                    <?
                echo $form->select('defaultSourceCountry', array_merge(array('' => t('*** Undetermined country')), $countries), $defaultSourceCountry);
                ?>
                </div>
                <script>
                $(document).ready(function() {
                	new ccmCountryForLanguageLister($('#defaultSourceLanguage'), $('#defaultSourceCountry'));
                });
                </script>
            </div>

            <div class="form-group">
                <?php echo Loader::helper('validation/token')->output('set_default')?>
                <button class="btn btn-default pull-left" type="submit" name="save"><?=t('Save Settings')?></button>
            </div>
        </form>
    </fieldset>
    <?php
}

