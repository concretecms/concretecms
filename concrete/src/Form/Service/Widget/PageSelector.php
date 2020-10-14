<?php
namespace Concrete\Core\Form\Service\Widget;

use Core;
use Page;
use Permissions;
use HtmlObject\Element;
use Concrete\Core\Http\Request;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Entity\Site\SiteTree;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\Application\Application;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\Utility\Service\Validation\Numbers;

class PageSelector
{
    /**
     * The application container instance.
     *
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Application\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Creates form fields and JavaScript page chooser for choosing a page. For use with inclusion in blocks.
     * <code>
     *     $dh->selectPage('pageID', '1'); // prints out the home page and makes it selectable.
     * </code>.
     *
     * @param $fieldName
     * @param bool|int $cID
     *
     * @return string
     */
    public function selectPage($fieldName, $cID = false)
    {
        $selectedCID = 0;
        if (isset($_REQUEST[$fieldName])) {
            $selectedCID = intval($_REQUEST[$fieldName]);
        } else {
            if ($cID > 0) {
                $selectedCID = $cID;
            }
        }

        $pageIdProp = 0;
        if ($selectedCID && \Concrete\Core\Page\Page::getByID($selectedCID)->getError() !== COLLECTION_NOT_FOUND) {
            $pageIdProp = $selectedCID;
        }

        $chooseText = t('Choose Page');
        $uniqid = uniqid();
        $html = <<<EOL
<div data-concrete-page-input="{$uniqid}">
    <concrete-page-input :page-id="{$pageIdProp}" choose-text="{$chooseText}" input-name="{$fieldName}"></concrete-page-input>
</div>
<script type="text/javascript">
$(function() {
    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: 'div[data-concrete-page-input="{$uniqid}"]',
            components: config.components
        })
    })
});
</script>
EOL;
        return $html;
    }

    public function quickSelect($key, $cID = false, $miscFields = [])
    {
        $selectedCID = null;

        /** @var Request $request */
        $request = $this->app->make(Request::class);
        /** @var Token $valt */
        $valt = $this->app->make(Token::class);
        /** @var Identifier $idHelper */
        $idHelper = $this->app->make(Identifier::class);
        /** @var Form $form */
        $form = $this->app->make(Form::class);

        if ($request->request->has($key)) {
            $selectedCID = $request->request->get($key);
        } elseif ($request->query->has($key)) {
            $selectedCID = $request->query->get($key);
        } else {
            $selectedCID = $cID;
        }

        $token = $valt->generate('quick_page_select_' . $key);
        $identifier = $idHelper->getString(32);

        $pageList = [];

        if ($selectedCID && $this->app->make(Numbers::class)->integer($selectedCID, 1)) {
            $page = $this->app->make(Page::class)->getByID((int)$selectedCID);
            $cp = new Permissions($page);
            if ($cp->canViewPage()) {
                $pageList[(int) $selectedCID] = $page->getCollectionName();
            }
        } else {
            $page = null;
        }

        $selectedCID = (is_object($page) && !$page->isError()) ? $page->getCollectionID() : null;

        return sprintf(
            "%s\n" .
            "<script>\n" .
            "$(function() {\n" .
            " $('#ccm-quick-page-selector-{$identifier} select').selectpicker({liveSearch: true}).ajaxSelectPicker(%s);\n" .
            "});\n" .
            "</script>\n",
            (string) new Element(
                "span",
                $form->select($key, $pageList, $selectedCID, $miscFields),
                [
                    "class" => "ccm-quick-page-selector",
                    "id" => "ccm-quick-page-selector-" . $identifier,
                ]
            ),
            json_encode([
                "ajax" => [
                    "url" => REL_DIR_FILES_TOOLS_REQUIRED . '/pages/autocomplete',
                    "data" => [
                        "term" => "{{{q}}}",
                        "key" => $key,
                        "token" => $token,
                    ],
                ],
                "locale" => [
                    "currentlySelected" => t("Currently Selected"),
                    "emptyTitle" => t("Select and begin typing"),
                    "errorText" => t("Unable to retrieve results"),
                    "searchPlaceholder" => t("Search..."),
                    "statusInitialized" => t("Start typing a search query"),
                    "statusNoResults" => t("No Results"),
                    "statusSearching" => t("Searching..."),
                    "statusTooShort" => t("Please enter more characters"),
                ],
                "preserveSelected" => false,
                "minLength" => 2,
            ])
        );
    }

    public function selectMultipleFromSitemap($field, $pages = array(), $startingPoint = 'HOME_CID', $filters = array())
    {
        $identifier = new \Concrete\Core\Utility\Service\Identifier();
        $identifier = $identifier->getString(32);

        $args = new \stdClass();
        $selected = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST[$field]) && is_array($_POST[$field])) {
                foreach ($_POST[$field] as $value) {
                    $selected[] = intval($value);
                }
            }
        } else {
            foreach ($pages as $cID) {
                $selected[] = is_object($cID) ? $cID->getCollectionID() : $cID;
            }
        }

        $args->identifier = $identifier;
        $args->selected = $selected;
        $args->mode = 'multiple';
        $args->token = Core::make('token')->generate('select_sitemap');
        $args->inputName = $field;
        $args->startingPoint = $startingPoint === 'HOME_CID' ? Page::getHomePageID() : $startingPoint;
        if (count($filters)) {
            $args->filters = $filters;
        }
        $args = json_encode($args);

        $html = <<<EOL
        <div data-page-sitemap-selector="{$identifier}"></div>
        <script type="text/javascript">
        $(function() {
            $('[data-page-sitemap-selector={$identifier}]').concretePageSitemapSelector({$args});
        });
        </script>
EOL;

        return $html;
    }

    public function selectFromSitemap($field, $page = null, $startingPoint = 'HOME_CID', SiteTree $siteTree = null, $filters = array())
    {
        $identifier = new \Concrete\Core\Utility\Service\Identifier();
        $identifier = $identifier->getString(32);

        $args = new \stdClass();
        $selected = 0;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST[$field])) {
                $selected = intval($_POST[$field]);
            }
        } elseif ($page) {
            $selected = is_object($page) ? $page->getCollectionID() : $page;
        }
        $args->identifier = $identifier;
        $args->selected = $selected;
        $args->inputName = $field;
        $args->startingPoint = $startingPoint === 'HOME_CID' ? Page::getHomePageID() : $startingPoint;
        if ($siteTree) {
            $args->siteTreeID = $siteTree->getSiteTreeID();
        }
        $args->token = Core::make('token')->generate('select_sitemap');
        if (count($filters)) {
            $args->filters = $filters;
        }
        $args = json_encode($args);

        $html = <<<EOL
        <div data-page-sitemap-selector="{$identifier}"></div>
        <script type="text/javascript">
        $(function() {
            $('[data-page-sitemap-selector={$identifier}]').concretePageSitemapSelector({$args});
        });
        </script>
EOL;

        return $html;
    }
}
