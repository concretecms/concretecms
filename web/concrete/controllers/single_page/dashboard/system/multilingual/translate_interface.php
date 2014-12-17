<?

namespace Concrete\Controller\SinglePage\Dashboard\System\Multilingual;
use Concrete\Core\Multilingual\Page\Section;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Core;
use Database;

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

    public function reload()
    {
        if (Core::make('token')->validate('reload')) {
            // First, we look in all the site sources for PHP code with GetText
            $extractor = Core::make('multilingual/extractor');
            $translations = $extractor->extractTranslatableSiteStrings();

            // $translations contains all of our site translations.
            $list = Section::getList();
            foreach($list as $section) {
                // now we load the translations that currently exist for each section
                $translations = $extractor->mergeTranslationsWithSectionFile($section, $translations);
                $extractor->saveSectionTranslationsToFile($section, $translations);

                // now that we've updated the translation file, we take all the translations and
                // we insert them into the database so we can update our counts, and give the users
                // a web interface
                $extractor->saveSectionTranslationsToDatabase($section, $translations);
            }
            $this->redirect('/dashboard/system/multilingual/translate_interface', 'reloaded');

        } else {
            $this->error->add(Core::make('token')->getErrorMessage());
        }
    }

    /*

    public function translate_po($catalogID = false)
    {
        $this->set('catalogID', $catalogID);
        $db = Database::get();
        $r = $db->getCol('SELECT name FROM MultilingualSimplePoCatalogues WHERE id=? LIMIT 1', $catalogID);
        $lang = $r[0];
        $this->set('lang', $lang);
        $html = Core::make('helper/html');
        $this->addHeaderItem($html->css('simplepo/index.css', 'multilingual_plus'));
        $this->addHeaderItem($html->css('simplepo/edit.css', 'multilingual_plus'));
        $this->addHeaderItem($html->javascript('simplepo/jquery.tablesorter.js', 'multilingual_plus'));
        $this->addHeaderItem($html->javascript('simplepo/json2.js', 'multilingual_plus'));

    }

    public function refresh_status()
    {
        $defaultLanguage = \Config::get('concrete.multilingual.default_locale');

        $list = MultilingualSection::getList();
        foreach ($list as $ms) {
            if ($ms->getLanguage() == $defaultLanguage || (!is_file(DIR_LANGUAGES_SITE_INTERFACE . '/' . $ms->getLocale() . '.po'))) {
                continue;
            }
            $MsgStore = new DBPoMsgStore();
            $MsgStore->init($ms->getLocale());
            $POParser = new POParser($MsgStore);
            $POParser->parseEntriesFromStream(fopen(DIR_LANGUAGES_SITE_INTERFACE . '/' . $ms->getLocale() . '.po',
                    'r'));
        }
        $this->redirect($this->getCollectionObject()->getCollectionPath(), t('Completion status refreshed'));
    }
    */

}