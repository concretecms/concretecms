<?php
namespace Concrete\Core\Multilingual\Service;

use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Package\Package;
use Gettext\Extractors\Mo as MoExtractor;
use Gettext\Extractors\Po as PoExtractor;
use Gettext\Translations;
use Gettext\Generators\Po as PoGenerator;
use Gettext\Generators\Mo as MoGenerator;
use Concrete\Core\Package\PackageList;
use Config;

defined('C5_EXECUTE') or die("Access Denied.");

class Extractor
{
    /**
     * return \GetText\Translations $translations;
     */
    public function extractTranslatableSiteStrings()
    {
        $translations = new Translations();
        $translations->insert('SiteName', Config::get('concrete.site'));
        $phpParser = new \C5TL\Parser\Php();
        $blockTemplatesParser = new \C5TL\Parser\BlockTemplates();
        $themesPresetsParser = new \C5TL\Parser\ThemePresets();

        $processApplication = array(
            DIRNAME_BLOCKS => array($phpParser, $blockTemplatesParser),
            DIRNAME_ELEMENTS => array($phpParser),
            DIRNAME_CONTROLLERS => array($phpParser),
            DIRNAME_MAIL_TEMPLATES => array($phpParser),
            DIRNAME_PAGE_TYPES => array($phpParser),
            DIRNAME_PAGES => array($phpParser),
            DIRNAME_THEMES => array($phpParser, $themesPresetsParser, $blockTemplatesParser),
            DIRNAME_VIEWS => array($phpParser),
        );
        foreach ($processApplication as $dirname => $parsers) {
            if (is_dir(DIR_APPLICATION.'/'.$dirname)) {
                foreach ($parsers as $parser) {
                    /* @var $parser \C5TL\Parser */
                    $fullDirname = DIR_APPLICATION.'/'.$dirname;
                    if (is_dir($fullDirname)) {
                        $parser->parseDirectory(
                            $fullDirname,
                            DIRNAME_APPLICATION.'/'.$dirname,
                            $translations
                        );
                    }
                }
            }
        }

        if (is_dir(DIR_PACKAGES)) {
            $packages = Package::getInstalledList();
            foreach($packages as $package) {
                $fullDirname = DIR_PACKAGES.'/'.$package->getPackageHandle();
                $phpParser->parseDirectory($fullDirname,
                    DIRNAME_PACKAGES.'/'.$dirname,
                    $translations
                );
            }
        }

        // Now, we grab dynamic content that's part of our site that we translate dynamically
        $dynamicTranslations = $this->getDynamicTranslations();
        $translations->mergeWith($dynamicTranslations);

        return $translations;
    }

    public function getDynamicTranslations()
    {
        $parser = new \C5TL\Parser\Dynamic();

        return $parser->parseRunningConcrete5();
    }

    public function clearTranslationsFromDatabase()
    {
        $db = \Database::get();
        $db->Execute('truncate table MultilingualTranslations');
    }

    public function deleteSectionTranslationFile(Section $section)
    {
        $po = DIR_LANGUAGES_SITE_INTERFACE . '/' . $section->getLocale() . '.po';
        $mo = DIR_LANGUAGES_SITE_INTERFACE . '/' . $section->getLocale() . '.mo';
        if (is_file($po)) {
            unlink($po);
        }
        if (is_file($mo)) {
            unlink($mo);
        }
    }

    public function mergeTranslationsWithSectionFile(Section $section, Translations $translations)
    {
        $file = DIR_LANGUAGES_SITE_INTERFACE . '/' . $section->getLocale() . '.po';
        if (is_file($file)) {
            $sectionTranslations = PoExtractor::fromFile($file);
            $translations->mergeWith($sectionTranslations);
        }
    }

    public function mergeTranslationsWithCore(Section $section, Translations $translations)
    {
        // Now we're going to load the core translations.
        $poFile = DIR_LANGUAGES . '/' . $section->getLocale() . '/LC_MESSAGES/messages.po';
        $moFile = DIR_LANGUAGES . '/' . $section->getLocale() . '/LC_MESSAGES/messages.mo';
        $coreTranslations = null;
        if (is_file($poFile)) {
            $coreTranslations = PoExtractor::fromFile($poFile);
        } elseif (is_file($moFile)) {
            $coreTranslations = MoExtractor::fromFile($moFile);
        }

        if (isset($coreTranslations)) {
            // Now that we have the core translations, we loop through all the translations from above, and check
            // to see if the core has a translation for this string. If the core does not, we include it in the translations
            // object to return.

            // This is actually much faster than unsetting the matching translation from the existing translations object

            foreach ($translations as $translation) {
                /* @var $translation \Gettext\Translation */
                if (!$translation->hasTranslation()) {
                    $coreTranslation = $coreTranslations->find($translation);
                    if ($coreTranslation && $coreTranslation->hasTranslation()) {
                        $translation->mergeWith($coreTranslation);
                    }
                }
            }
        }
    }

    public function mergeTranslationsWithPackages(Section $section, Translations $translations)
    {
        foreach (PackageList::get()->getPackages() as $package) {
            /* @var $package \Concrete\Core\Package\Package */
            $baseDir = $package->getPackagePath() . '/' . DIRNAME_LANGUAGES . '/' . $section->getLocale() . '/LC_MESSAGES';
            $poFile = $baseDir . '/messages.po';
            $moFile = $baseDir . '/messages.mo';
            $packageTranslations = null;
            if (is_file($poFile)) {
                $packageTranslations = PoExtractor::fromFile($poFile);
            } elseif (is_file($moFile)) {
                $packageTranslations = MoExtractor::fromFile($moFile);
            }
            if (isset($packageTranslations)) {
                foreach ($translations as $translation) {
                    /* @var $translation \Gettext\Translation */
                    if (!$translation->hasTranslation()) {
                        $packageTranslation = $packageTranslations->find($translation);
                        if ($packageTranslation && $packageTranslation->hasTranslation()) {
                            $translation->mergeWith($packageTranslation);
                        }
                    }
                }
            }
        }
    }

    public function saveSectionTranslationsToFile(Section $section, Translations $translations)
    {
        $po = DIR_LANGUAGES_SITE_INTERFACE . '/' . $section->getLocale() . '.po';
        $mo = DIR_LANGUAGES_SITE_INTERFACE . '/' . $section->getLocale() . '.mo';

        PoGenerator::toFile($translations, $po);

        /* Do not generate mo for empty catalog, it crashes Zend\I18n gettext loader */
        $empty = true;
        foreach ($translations as $entry) {
            if ($entry->hasTranslation()) {
                $empty = false;
                break;
            }
        }

        if (!$empty) {
            MoGenerator::$includeEmptyTranslations = true;
            MoGenerator::toFile($translations, $mo);
        } else {
            if (is_file($mo)) {
                unlink($mo);
            }
        }
    }

    public function saveSectionTranslationsToDatabase(Section $section, Translations $translations)
    {
        $db = \Database::get();
        $db->delete('MultilingualTranslations', array('mtSectionID' => $section->getCollectionID()));
        foreach ($translations as $translation) {
            /* @var $translation \Gettext\Translation */
            $data = array(
                'mtSectionID' => $section->getCollectionID(),
                'msgid' => $translation->getOriginal(),
                'msgidPlural' => $translation->getPlural(),
                'msgstr' => $translation->getTranslation(),
                'context' => $translation->getContext(),
            );
            $plurals = $translation->getPluralTranslation();
            if (!empty($plurals)) {
                $data['msgstrPlurals'] = implode("\x00", $plurals);
            }
            $comments = $translation->getExtractedComments();
            if (!empty($comments)) {
                $data['comments'] = implode("\n", $comments);
            }
            $references = $translation->getReferences();
            if (!empty($references)) {
                $data['reference'] = '';
                foreach ($translation->getReferences() as $reference) {
                    $data['reference'] .= (isset($reference[1]) ? implode(':', $reference) : $reference[0]) . "\n";
                }
            }
            $flags = $translation->getFlags();
            if (!empty($flags)) {
                $data['flags'] =  implode("\n", $flags);
            }
            $db->insert('MultilingualTranslations', $data);
        }
    }

    public function getSectionSiteInterfaceCompletionData(Section $section)
    {
        $db = \Database::get();
        $data = array();
        $data['messageCount'] = $db->GetOne('select count(mtID) from MultilingualTranslations where mtSectionID = ?',
            array($section->getCollectionID())
        );
        $data['translatedCount'] = $db->GetOne(
            'select count(mtID) from MultilingualTranslations where mtSectionID = ? and msgstr != ""',
            array($section->getCollectionID())
        );
        $data['completionPercentage'] = 0;
        if ($data['messageCount'] > 0) {
            $data['completionPercentage'] = round(($data['translatedCount'] / $data['messageCount']) * 100);
        }

        return $data;
    }
}
