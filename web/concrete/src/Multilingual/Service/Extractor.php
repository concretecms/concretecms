<?php

namespace Concrete\Core\Multilingual\Service;
use Concrete\Attribute\Select\Option as SelectAttributeOption;
use Concrete\Core\Attribute\Key\Key as AttributeKey;
use Concrete\Core\Attribute\Set as AttributeSet;
use Concrete\Core\Attribute\Type as AttributeType;
use Concrete\Core\Job\Set as JobSet;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Permission\Access\Entity\Type as PermissionAccessEntityType;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupSet;
use Gettext\Extractors\Mo;
use Gettext\Extractors\Po;
use Gettext\Translations;
use Gettext\Extractors\PhpCode;
use Gettext\Generators\Po as PoGenerator;
use Gettext\Generators\Mo as MoGenerator;

defined('C5_EXECUTE') or die("Access Denied.");

class Extractor
{
    public function __construct()
    {
        // register concrete5 translator functions
        PhpCode::$functions['t'] = '__';
        PhpCode::$functions['t2'] = 'n__';
        PhpCode::$functions['tc'] = 'p__';
    }

    /**
     * return \GetText\Translations $translations;
     */
    public function extractTranslatableSiteStrings()
    {
        // first, we figure out which files we're going to operate on.
        $directories = array(
            DIR_APPLICATION . '/' . DIRNAME_BLOCKS,
            DIR_APPLICATION . '/' . DIRNAME_ELEMENTS,
            DIR_APPLICATION . '/' . DIRNAME_CONTROLLERS,
            DIR_APPLICATION . '/' . DIRNAME_MAIL_TEMPLATES,
            DIR_APPLICATION . '/' . DIRNAME_PAGE_TYPES,
            DIR_APPLICATION . '/' . DIRNAME_PAGES,
            DIR_APPLICATION . '/' . DIRNAME_THEMES,
            DIR_APPLICATION . '/' . DIRNAME_VIEWS
        );

        $files = array();
        foreach($directories as $directory) {
            if (!is_dir($directory)) {
                continue;
            }
            $directoryIterator = new \RecursiveDirectoryIterator($directory);
            $iterator = new \RecursiveIteratorIterator($directoryIterator);
            $results = new \RegexIterator($iterator, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);
            foreach($results as $result) {
                $files[] = $result[0];
            }
        }

        $translations = new Translations();
        foreach($files as $file) {
            $fileTranslations = Translations::fromPhpCodeFile($file);
            $translations->mergeWith($fileTranslations);
        }

        // Now, we grab dynamic content that's part of our site that we translate dynamically
        $dynamicTranslations = $this->getDynamicTranslations();
        $translations->mergeWith($dynamicTranslations);

        return $translations;
    }

    public function getDynamicTranslations()
    {
        $translations = new Translations();
        $translations->mergeWith(AttributeSet::exportTranslations());
        $translations->mergeWith(AttributeKey::exportTranslations());
        $translations->mergeWith(AttributeType::exportTranslations());
        $translations->mergeWith(PermissionKey::exportTranslations());
        $translations->mergeWith(PermissionAccessEntityType::exportTranslations());
        $translations->mergeWith(JobSet::exportTranslations());
        $translations->mergeWith(Group::exportTranslations());
        $translations->mergeWith(GroupSet::exportTranslations());
        $translations->mergeWith(SelectAttributeOption::exportTranslations());
        return $translations;
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
        if (file_exists($po)) {
            unlink($po);
        }
        if (file_exists($mo)) {
            unlink($mo);
        }
    }

    public function mergeTranslationsWithSectionFile(Section $section, Translations $translations)
    {
        $file = DIR_LANGUAGES_SITE_INTERFACE . '/' . $section->getLocale() . '.po';
        if (file_exists($file)) {
            $sectionTranslations = Po::fromFile($file);
            $translations->mergeWith($sectionTranslations, Translations::MERGE_ADD);
        }

        return $translations;
    }

    public function mergeTranslationsWithCore(Section $section, Translations $translations)
    {
        // Now we're going to load the core translations.
        $poFile = DIR_LANGUAGES . '/' . $section->getLocale() . '/LC_MESSAGES/messages.po';
        $moFile = DIR_LANGUAGES . '/' . $section->getLocale() . '/LC_MESSAGES/messages.mo';
        if (file_exists($poFile)) {
            $coreTranslations = Po::fromFile($poFile);
        } else if (file_exists($moFile)) {
            $coreTranslations = Mo::fromFile($moFile);
        }

        if (isset($coreTranslations)) {
            $returnTranslations = new Translations();
            // Now that we have the core translations, we loop through all the translations from above, and check
            // to see if the core has a translation for this string. If the core does not, we include it in the translations
            // object to return.

            // This is actually much faster than unsetting the matching translation from the existing translations object

            foreach($translations as $key => $translation) {
                if (!$translation->getTranslation()) {
                    if (!$coreTranslations->find($translation->getContext(), $translation->getOriginal())) {
                        $returnTranslations[] = $translation;
                    }
                } else {
                    $returnTranslations[] = $translation;
                }
            }
            return $returnTranslations;
        }

        return $translations;
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
            MoGenerator::toFile($translations, $mo);
        } else {
            if (file_exists($mo)) {
                unlink($mo);
            }
        }
    }

    public function saveSectionTranslationsToDatabase(Section $section, Translations $translations)
    {
        $db = \Database::get();
        $db->delete('MultilingualTranslations', array('mtSectionID' => $section->getCollectionID()));
        foreach($translations as $translation) {
            $comments = implode(':', $translation->getComments());
            $references = $translation->getReferences();
            $refs = '';
            foreach($references as $reference) {
                $refs = implode(':', $reference) . "\n";
            }
            $db->insert('MultilingualTranslations', array(
               'mtSectionID' => $section->getCollectionID(),
                'msgid' => $translation->getOriginal(),
                'msgstr' => $translation->getTranslation(),
                'context' => $translation->getContext(),
                'comments' => $comments,
                'reference' => $refs
            ));
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
