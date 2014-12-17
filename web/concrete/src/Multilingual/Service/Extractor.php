<?php

namespace Concrete\Core\Multilingual\Service;
use Concrete\Core\Multilingual\Page\Section;
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

        return $translations;
    }

    public function mergeTranslationsWithSectionFile(Section $section, Translations $translations)
    {
        $file = DIR_LANGUAGES_SITE_INTERFACE . '/' . $section->getLocale() . '.po';
        if (file_exists($file)) {
            $sectionTranslations = Po::fromFile($file);
            $translations->mergeWith($sectionTranslations, Translations::MERGE_HEADERS | Translations::MERGE_COMMENTS);
        }
        return $translations;
    }

    public function saveSectionTranslationsToFile(Section $section, Translations $translations)
    {
        $po = DIR_LANGUAGES_SITE_INTERFACE . '/' . $section->getLocale() . '.po';
        $mo = DIR_LANGUAGES_SITE_INTERFACE . '/' . $section->getLocale() . '.mo';

        PoGenerator::toFile($translations, $po);
        MoGenerator::toFile($translations, $mo);
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
        $data['messageCount'] = $db->GetOne('select count(mtID) from MultilingualTranslations where mtSectionID = ?', array($section->getCollectionID()));
        $data['translatedCount'] = $db->GetOne('select count(mtID) from MultilingualTranslations where mtSectionID = ? and msgstr != ""', array($section->getCollectionID()));
        $data['completionPercentage'] = 0;
        if ($data['messageCount'] > 0) {
            $data['completionPercentage'] = round(($data['translatedCount'] / $data['messageCount']) * 100);
        }
        return $data;
    }

}
