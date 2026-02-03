<?php

namespace Concrete\Core\Form\Service\Widget;

use Concrete\Core\Entity\Site\SiteTree;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Component\PageSelectInstanceFactory;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Core;
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
            $selectedCID = (int) ($_REQUEST[$fieldName]);
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

    public function quickSelect($key, $cID = false)
    {
        $selectedCID = null;

        $app = Facade::getFacadeApplication();

        /** @var Request $request */
        $request = $app->make(Request::class);
        /** @var PageSelectInstanceFactory $pageSelectInstanceFactory */
        $pageSelectInstanceFactory = $app->make(PageSelectInstanceFactory::class);
        $pageSelectInstance = $pageSelectInstanceFactory->createInstance();

        if ($request->request->has($key)) {
            $selectedCID = $request->request->get($key);
        } elseif ($request->query->has($key)) {
            $selectedCID = $request->query->get($key);
        } else {
            $selectedCID = $cID;
        }

        if ($selectedCID && $app->make(Numbers::class)->integer($selectedCID, 1)) {
            $page = $app->make(Page::class)->getByID((int) $selectedCID);
            $cp = new Permissions($page);
            if (!$cp->canViewPage()) {
                unset($page);
            }
        } else {
            $page = null;
        }

        $selectedCID = (is_object($page) && !$page->isError()) ? $page->getCollectionID() : 'null';
        $identifier = $app->make(Identifier::class)->getString(32);

        return <<<EOL
<div data-concrete-select-page-input="{$identifier}">
    <concrete-page-select
    access-token="{$pageSelectInstance->getAccessToken()}"
    :page-id="{$selectedCID}"
    input-name="{$key}"
    ></concrete-page-select>
</div>
<script type="text/javascript">
$(function() {
    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: 'div[data-concrete-select-page-input="{$identifier}"]',
            components: config.components
        })
    })
});
</script>
EOL;
    }

    public function selectMultipleFromSitemap($field, $pages = [], $startingPoint = 'HOME_CID', $filters = [])
    {
        $identifier = new \Concrete\Core\Utility\Service\Identifier();
        $identifier = $identifier->getString(32);
        $startingPoint = $startingPoint === 'HOME_CID' ? Page::getHomePageID() : $startingPoint;

        $args = new \stdClass();
        $selected = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST[$field]) && is_array($_POST[$field])) {
                foreach ($_POST[$field] as $value) {
                    $selected[] = (int) $value;
                }
            }
        } else {
            foreach ($pages as $cID) {
                $selected[] = is_object($cID) ? $cID->getCollectionID() : $cID;
            }
        }

        $selectedPath = null;
        if ($selected) {
            foreach ($selected as $selectedItem) {
                $selectedPath[] = $this->getPathFromStartingPoint($startingPoint, $selectedItem);
            }
        }

        $args->identifier = $identifier;
        $args->selected = $selected;
        $args->selectedPath = $selectedPath;
        $args->mode = 'multiple';
        $args->token = Core::make('token')->generate('select_sitemap');
        $args->inputName = $field;
        $args->startingPoint = $startingPoint;
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
        $startingPoint = $startingPoint === 'HOME_CID' ? Page::getHomePageID() : $startingPoint;

        $args = new \stdClass();
        $selected = 0;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST[$field])) {
                $selected = (int) ($_POST[$field]);
            }
        } elseif ($page) {
            $selected = is_object($page) ? $page->getCollectionID() : $page;
        }

        $selectedPath = null;
        if ($selected) {
            $selectedPath = $this->getPathFromStartingPoint($startingPoint, $selected);
        }

        $args->identifier = $identifier;
        $args->selected = $selected;
        $args->selectedPath = $selectedPath;
        $args->inputName = $field;
        $args->startingPoint = $startingPoint;
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

    private function getPathFromStartingPoint($startingPoint, $selected): array
    {
        $pages = [];

        $currentPage = Page::getByID($selected);

        while ($currentPage && !$currentPage->isError()) {
            $pages[] = $currentPage;
            $parentID = $currentPage->getCollectionParentID();

            if ($currentPage->getCollectionID() === $startingPoint) {
                break;
            }

            $currentPage = Page::getByID($parentID);
        }

        $reversedPages = array_reverse($pages);

        $pageIds = [];
        foreach ($reversedPages as $reversedPage) {
            $pageIds[] = $reversedPage->getCollectionID();
        }

        array_pop($pageIds);

        return $pageIds;
    }
}
