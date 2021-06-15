<?php

namespace Concrete\Core\Form\Service\Widget;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Site\SiteTree;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Facade;
use URL;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\Validation\CSRF\Token;
use Core;
use HtmlObject\Element;
use Page;
use Permissions;

class PageSelector
{

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
    public function selectPage($fieldName, $cID = false, array $options = [])
    {
        $selectedCID = 0;
        if (isset($_REQUEST[$fieldName])) {
            $selectedCID = (int)($_REQUEST[$fieldName]);
        } else {
            if ($cID > 0) {
                $selectedCID = $cID;
            }
        }
        $pageIdProp = 0;
        if ($selectedCID !== 0) {
            $page = Page::getByID($selectedCID);
            if ($page && $page->getError() !== COLLECTION_NOT_FOUND) {
                $pageIdProp = $selectedCID;
            }
        }
        $options += [
            'includeSystemPages' => false,
            'askIncludeSystemPages' => false,
            'chooseText' => t('Choose Page'),
        ];
        $options['includeSystemPages'] = $options['includeSystemPages'] ? 'true' : 'false';
        $options['askIncludeSystemPages'] = $options['askIncludeSystemPages'] ? 'true' : 'false';

        $chooseText = t('Choose Page');
        $uniqid = uniqid();
        $html = <<<EOL
<div data-concrete-page-input="{$uniqid}">
    <concrete-page-input :page-id="{$pageIdProp}" input-name="{$fieldName}" choose-text="{$options['chooseText']}" :include-system-pages="{$options['includeSystemPages']}" :ask-include-system-pages="{$options['askIncludeSystemPages']}"></concrete-page-input>
</div>
<script>
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

        $app = Facade::getFacadeApplication();

        /** @var Request $request */
        $request = $app->make(Request::class);
        /** @var Token $valt */
        $valt = $app->make(Token::class);
        /** @var Identifier $idHelper */
        $idHelper = $app->make(Identifier::class);
        /** @var Form $form */
        $form = $app->make(Form::class);

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

        if ($selectedCID && $app->make(Numbers::class)->integer($selectedCID, 1)) {
            $page = $app->make(Page::class)->getByID((int)$selectedCID);
            $cp = new Permissions($page);
            if ($cp->canViewPage()) {
                $pageList[(int)$selectedCID] = $page->getCollectionName();
            }
        } else {
            $page = null;
        }

        $selectedCID = (is_object($page) && !$page->isError()) ? $page->getCollectionID() : null;
        $element = (string)new Element(
            'span',
            $form->select($key, $pageList, $selectedCID, $miscFields),
            [
                'class' => 'ccm-quick-page-selector',
                'id' => 'ccm-quick-page-selector-' . $identifier,
            ]
        );

        $args = [
            'ajax' => [
                'url' => (string) URL::to('/ccm/system/page/autocomplete'),
                'data' => [
                    'term' => '{{{q}}}',
                    'key' => $key,
                    'token' => $token,
                ],
            ],
            'locale' => [
                'currentlySelected' => t('Currently Selected'),
                'emptyTitle' => t('Select and begin typing'),
                'errorText' => t('Unable to retrieve results'),
                'searchPlaceholder' => t('Search...'),
                'statusInitialized' => t('Start typing a search query'),
                'statusNoResults' => t('No Results'),
                'statusSearching' => t('Searching...'),
                'statusTooShort' => t('Please enter more characters'),
            ],
            'preserveSelected' => false,
            'minLength' => 2,
        ];

        $args = json_encode($args);

        $html = <<<EOL
        $element
        <script type="text/javascript">
        $(function() {
            $('#ccm-quick-page-selector-{$identifier} select').selectpicker({liveSearch: true}).ajaxSelectPicker({$args});
        });
        </script>
EOL;

        return $html;
    }

    public function selectMultipleFromSitemap($field, $pages = [], $startingPoint = 'HOME_CID', $filters = [])
    {
        $identifier = new \Concrete\Core\Utility\Service\Identifier();
        $identifier = $identifier->getString(32);

        $args = new \stdClass();
        $selected = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST[$field]) && is_array($_POST[$field])) {
                foreach ($_POST[$field] as $value) {
                    $selected[] = (int)$value;
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

    public function selectFromSitemap(
        $field,
        $page = null,
        $startingPoint = 'HOME_CID',
        ?SiteTree $siteTree = null,
        $filters = []
    ) {
        $identifier = new \Concrete\Core\Utility\Service\Identifier();
        $identifier = $identifier->getString(32);

        $args = new \stdClass();
        $selected = 0;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST[$field])) {
                $selected = (int)($_POST[$field]);
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
