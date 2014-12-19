<?

namespace Concrete\Controller\SinglePage\Dashboard\System\Multilingual;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Multilingual\Page\Section\Section;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Multilingual\Page\Section\Translation;
use Core;
use Database;
use Config;

defined('C5_EXECUTE') or die("Access Denied.");

class TranslateInterface extends DashboardPageController
{

    public $helpers = array('form');

    public function view()
    {
        $extractor = Core::make('multilingual/extractor');
        $this->set('extractor', $extractor);

    }
    public function reloaded()
    {
        $this->set('message', t('Languages refreshed.'));
        $this->view();
    }

    public function exported()
    {
        $this->set('message', t('Translations exported to PO File and Reloaded.'));
        $this->view();
    }

    public function reset_complete()
    {
        $this->set('message', t('Languages reset.'));
        $this->view();
    }

    public function reset_languages()
    {
        if (Core::make('token')->validate('reset_languages')) {
            $u = new \User();
            $extractor = Core::make('multilingual/extractor');
            if ($u->isSuperUser()) {
                $list = Section::getList();
                foreach($list as $section) {
                    // now we load the translations that currently exist for each section
                    $extractor->deleteSectionTranslationFile($section);
                }
                $extractor->clearTranslationsFromDatabase();
                $this->redirect('/dashboard/system/multilingual/translate_interface', 'reset_complete');
            }
        } else {
            $this->error->add(t('Only the admin user may reset all strings.'));
        }
    }

    public function submit()
    {
        $extractor = Core::make('multilingual/extractor');
        if ($this->post('action') == 'reload') {
            if (Core::make('token')->validate()) {
                // First, we look in all the site sources for PHP code with GetText
                $translations = $extractor->extractTranslatableSiteStrings();

                // $translations contains all of our site translations.
                $list = Section::getList();
                $defaultSourceLocale = Config::get('concrete.multilingual.default_source_locale');
                foreach($list as $section) {
                    if($section->getLocale() != $defaultSourceLocale) {
                        // now we load the translations that currently exist for each section
                        $translations = $extractor->mergeTranslationsWithSectionFile($section, $translations);
                        $translations = $extractor->mergeTranslationsWithCore($section, $translations);
                        $extractor->saveSectionTranslationsToFile($section, $translations);
    
                        // now that we've updated the translation file, we take all the translations and
                        // we insert them into the database so we can update our counts, and give the users
                        // a web interface
                        $extractor->saveSectionTranslationsToDatabase($section, $translations);
                    }
                }
                $this->redirect('/dashboard/system/multilingual/translate_interface', 'reloaded');
            } else {
                $this->error->add(Core::make('token')->getErrorMessage());
            }
        }
        if ($this->post('action') == 'export') {
            if (Core::make('token')->validate()) {
                $defaultSourceLocale = Config::get('concrete.multilingual.default_source_locale');
                $list = Section::getList();
                foreach($list as $section) {
                    if($section->getLocale() != $defaultSourceLocale) {
                        $translations = $section->getSectionInterfaceTranslations();
                        $translations = $extractor->mergeTranslationsWithSectionFile($section, $translations);
                        $extractor->saveSectionTranslationsToFile($section, $translations);
                    }
                }
                $this->redirect('/dashboard/system/multilingual/translate_interface', 'exported');
            } else {
                $this->error->add(Core::make('token')->getErrorMessage());
            }
        }
        $this->view();
    }

    public function save_translation()
    {
        $mtID = intval($this->post('mtID'));
        $translation = Translation::getByID($mtID);
        if (is_object($translation)) {
            $translation->updateTranslation($this->post('msgstr'));
        }
        $r = new EditResponse();
        $r->outputJSON();
    }

    public function translate_po($mtSectionID = false)
    {
        $mtSectionID = intval($mtSectionID);
        $section = Section::getByID(intval($mtSectionID));
        if ($section) {
            $translations = $section->getSectionInterfaceTranslations();
            $this->set('section', $section);
            $this->set('translations', $translations);
        } else {
            $this->redirect('/dashboard/system/multilingual/translate_interface');
        }


    }

}