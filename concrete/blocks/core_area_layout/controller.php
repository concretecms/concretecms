<?php

namespace Concrete\Block\CoreAreaLayout;

use Concrete\Core\Area\Area;
use Concrete\Core\Area\Layout\CustomLayout as CustomAreaLayout;
use Concrete\Core\Area\Layout\Layout as AreaLayout;
use Concrete\Core\Area\Layout\Preset\Preset as AreaLayoutPreset;
use Concrete\Core\Area\Layout\Preset\PresetInterface as AreaLayoutPresetInterface;
use Concrete\Core\Area\Layout\Preset\Provider\ActiveThemeProvider;
use Concrete\Core\Area\Layout\Preset\Provider\Manager as AreaLayoutPresetProvider;
use Concrete\Core\Area\Layout\Preset\Provider\ThemeProvider;
use Concrete\Core\Area\Layout\Preset\Provider\ThemeProviderInterface;
use Concrete\Core\Area\Layout\PresetLayout;
use Concrete\Core\Area\Layout\ThemeGridLayout as ThemeGridAreaLayout;
use Concrete\Core\Area\SubArea;
use Concrete\Core\Asset\CssAsset;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\StyleCustomizer\Inline\StyleSet;
use Concrete\Core\Support\Facade\Url;

class Controller extends BlockController implements UsesFeatureInterface
{
    /**
     * @var \Concrete\Core\Area\Layout\CustomLayout|\Concrete\Core\Area\Layout\PresetLayout|\Concrete\Core\Area\Layout\ThemeGridLayout|null
     */
    public $arLayout;

    /**
     * @var bool
     */
    protected $btSupportsInlineAdd = true;

    /**
     * @var bool
     */
    protected $btSupportsInlineEdit = true;

    /**
     * @var string
     */
    protected $btTable = 'btCoreAreaLayout';

    /**
     * @var bool
     */
    protected $btIsInternal = true;

    /**
     * @var bool
     */
    protected $btCacheSettingsInitialized = false;

    /**
     * @var string[]
     */
    protected $requiredFeatures = [];

    /**
     * @var Area|null
     */
    protected $area;

    /**
     * @return bool
     */
    public function cacheBlockOutput()
    {
        $this->setupCacheSettings();

        return $this->btCacheBlockOutput;
    }

    /**
     * @return bool
     */
    public function cacheBlockOutputOnPost()
    {
        $this->setupCacheSettings();

        return $this->btCacheBlockOutputOnPost;
    }

    /**
     * @return int
     */
    public function getBlockTypeCacheOutputLifetime()
    {
        $this->setupCacheSettings();

        return $this->btCacheBlockOutputLifetime;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Proxy block for area layouts.');
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Area Layout');
    }

    /**
     * {@inheritdoc}
     *
     * @return string[]
     */
    public function getRequiredFeatures(): array
    {
        $this->setupCacheSettings();

        return $this->requiredFeatures;
    }

    /**
     * @param string $outputContent
     *
     * @return void
     */
    public function registerViewAssets($outputContent = '')
    {
        if (is_object($this->block) && $this->block->getBlockFilename() == 'parallax') {
            $this->requireAsset('javascript', 'jquery');
            $this->requireAsset('javascript', 'core/frontend/parallax-image');
        }

        $arLayout = $this->getAreaLayoutObject();
        if (is_object($arLayout)) {
            if ($arLayout instanceof CustomAreaLayout) {
                $asset = new CssAsset();
                $asset->setAssetURL((string) Url::to('/ccm/system/css/layout', $arLayout->getAreaLayoutID()));
                $asset->setAssetSupportsMinification(false);
                $asset->setAssetSupportsCombination(false);
                $this->requireAsset($asset);
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param int $newBID
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException|\Doctrine\DBAL\Exception
     *
     * @return \Concrete\Core\Legacy\BlockRecord|null
     */
    public function duplicate($newBID)
    {
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        $record = parent::duplicate($newBID);
        if (isset($this->arLayoutID)) {
            $ar = AreaLayout::getByID($this->arLayoutID);
            $nr = $ar->duplicate();
            $db->executeStatement(
                'update btCoreAreaLayout set arLayoutID = ? where bID = ?',
                [$nr->getAreaLayoutID(), $newBID]
            );
        }

        return $record;
    }

    /**
     * @return \Concrete\Core\Area\Layout\CustomLayout|\Concrete\Core\Area\Layout\PresetLayout|\Concrete\Core\Area\Layout\ThemeGridLayout|null
     */
    public function getAreaLayoutObject()
    {
        if (isset($this->arLayoutID)) {
            $arLayout = AreaLayout::getByID($this->arLayoutID);
            $b = $this->getBlockObject();
            if (is_object($arLayout) && is_object($b)) {
                $arLayout->setBlockObject($b);
            }

            return $arLayout;
        }

        return null;
    }

    /**
     * @return void
     */
    public function delete()
    {
        $arLayout = $this->getAreaLayoutObject();
        if (is_object($arLayout)) {
            $arLayout->delete();
        }
        parent::delete();
    }

    /**
     * @param \SimpleXMLElement $blockNode
     *
     * @return void
     */
    public function export(\SimpleXMLElement $blockNode)
    {
        $layout = $this->getAreaLayoutObject();
        $layout->export($blockNode);
    }

    /**
     * @param array<string,mixed> $post
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Doctrine\DBAL\Exception
     *
     * @return void;
     */
    public function save($post)
    {
        if (isset($post['arLayoutID']) && !isset($post['arLayoutEdit'])) {
            // terribly lame, but in import we pass arLayoutID and we also pass it in the post of editing a layout
            // We need to somehow differentiate the two. If it's JUST arLayoutID we're using the migration tool
            // if it includes arLayoutEdit (which is included in the form) then run the standrd block save.
            // we are passing it in directly –likely from import
            $values = ['arLayoutID' => $post['arLayoutID']];
            parent::save($values);

            return;
        }
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        $arLayoutID = $db->fetchOne('select arLayoutID from btCoreAreaLayout where bID = ?', [$this->bID]);
        if (!$arLayoutID) {
            $arLayout = $this->addFromPost($post);
        } else {
            $arLayout = AreaLayout::getByID($arLayoutID);
            if ($arLayout instanceof PresetLayout) {
                return;
            }
            // save spacing
            if ($arLayout->isAreaLayoutUsingThemeGridFramework()) {
                /** @var \Concrete\Core\Area\Layout\ThemeGridColumn[] $columns */
                $columns = $arLayout->getAreaLayoutColumns();
                for ($i = 0; $i < count($columns); $i++) {
                    $col = $columns[$i];
                    $span = ($post['span'][$i]) ? $post['span'][$i] : 0;
                    $offset = ($post['offset'][$i]) ? $post['offset'][$i] : 0;
                    $col->setAreaLayoutColumnSpan($span);
                    $col->setAreaLayoutColumnOffset($offset);
                }
            } else {
                $arLayout->setAreaLayoutColumnSpacing($post['spacing']);
                if ($post['isautomated']) {
                    $arLayout->disableAreaLayoutCustomColumnWidths();
                } else {
                    $arLayout->enableAreaLayoutCustomColumnWidths();
                    /** @var \Concrete\Core\Area\Layout\CustomColumn[]|\Concrete\Core\Area\Layout\PresetColumn $columns */
                    $columns = $arLayout->getAreaLayoutColumns();
                    for ($i = 0; $i < count($columns); $i++) {
                        $col = $columns[$i];
                        $width = $post['width'][$i] ?: 0;
                        $col->setAreaLayoutColumnWidth($width);
                    }
                }
            }
        }

        $values = ['arLayoutID' => $arLayout->getAreaLayoutID()];
        parent::save($values);
    }

    /**
     * @param \SimpleXMLElement $blockNode
     * @param \Concrete\Core\Page\Page $page
     *
     * @return array<string,mixed>
     */
    public function getImportData($blockNode, $page)
    {
        if (!isset($blockNode->arealayout)) {
            return [];
        }
        $node = $blockNode->arealayout;
        $type = (string) $node['type'];
        switch ($type) {
            case 'theme-grid':
                $args = [
                    'gridType' => 'TG',
                    'arLayoutMaxColumns' => (string) $node['columns'],
                    'themeGridColumns' => count($node->columns->column),
                    'offset' => [],
                    'span' => [],
                ];
                foreach ($node->columns->column as $column) {
                    $args['span'][] = (int) $column['span'];
                    $args['offset'][] = (int) $column['offset'];
                }

                return $args;
            case 'preset':
                $preset = $this->resolveLayoutPreset(isset($node['preset-id']) ? (string) $node['preset-id'] : '', $page);
                if ($preset !== null) {
                    return [
                        'gridType' => $preset->getIdentifier(),
                        'arLayoutPresetID' => $preset->getIdentifier(),
                    ];
                }
                break;
        }
        // Custom type, or fallback when the preset could not be found
        $args = [
            'gridType' => 'FF',
            'isautomated' => (int) $node['custom-widths'] !== 1,
            'spacing' => (int) $node['spacing'],
            'columns' => count($node->columns->column),
            'width' => [],
        ];
        foreach ($node->columns->column as $column) {
            $args['width'][] = (int) $column['width'];
        }

        return $args;
    }

    /**
     * @param array<string,mixed> $post
     *
     * @return \Concrete\Core\Area\Layout\CustomLayout|\Concrete\Core\Area\Layout\PresetLayout|\Concrete\Core\Area\Layout\ThemeGridLayout|null
     */
    public function addFromPost($post)
    {
        // we are adding a new layout
        switch ($post['gridType']) {
            case 'TG':
                /** @var ThemeGridAreaLayout $arLayout */
                $arLayout = ThemeGridAreaLayout::add();
                $arLayout->setAreaLayoutMaxColumns($post['arLayoutMaxColumns']);
                for ($i = 0; $i < $post['themeGridColumns']; $i++) {
                    $span = ($post['span'][$i]) ? $post['span'][$i] : 0;
                    $offset = ($post['offset'][$i]) ? $post['offset'][$i] : 0;
                    /** @var \Concrete\Core\Area\Layout\ThemeGridColumn $column */
                    $column = $arLayout->addLayoutColumn();
                    $column->setAreaLayoutColumnSpan($span);
                    $column->setAreaLayoutColumnOffset($offset);
                }
                break;
            case 'FF':
                if ((!$post['isautomated']) && $post['columns'] > 1) {
                    $iscustom = true;
                } else {
                    $iscustom = false;
                }
                /** @var CustomAreaLayout $arLayout */
                $arLayout = CustomAreaLayout::add($post['spacing'], $iscustom);
                for ($i = 0; $i < $post['columns']; $i++) {
                    $width = ($post['width'][$i]) ? $post['width'][$i] : 0;
                    /** @var \Concrete\Core\Area\Layout\CustomColumn $column */
                    $column = $arLayout->addLayoutColumn();
                    $column->setAreaLayoutColumnWidth($width);
                }
                break;
            default: // a preset
                $arLayoutPreset = AreaLayoutPreset::getByID($post['arLayoutPresetID']);
                $arLayout = PresetLayout::add($arLayoutPreset);
                foreach ($arLayoutPreset->getColumns() as $column) {
                    $arLayout->addLayoutColumn();
                }
                break;
        }

        return $arLayout;
    }

    /**
     * @return void
     */
    public function view()
    {
        $b = $this->getBlockObject();
        $a = $b->getBlockAreaObject();
        $this->arLayout = $this->getAreaLayoutObject();
        if (is_object($this->arLayout)) {
            if ($a instanceof Area) {
                $this->arLayout->setAreaObject($a);
            }
            $this->set('columns', $this->arLayout->getAreaLayoutColumns());
            $c = Page::getCurrentPage();
            $this->set('c', $c);

            $gf = false;
            if ($this->arLayout->isAreaLayoutUsingThemeGridFramework()) {
                $pt = $c->getCollectionThemeObject();
                $gf = $pt->getThemeGridFrameworkObject();
            }

            $formatter = $this->arLayout->getFormatter();
            $this->set('formatter', $formatter);
        } else {
            $this->set('columns', []);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function edit()
    {
        $this->view();
        $gf = null;
        // since we set a render override in view() we have to explicitly declare edit
        if ($this->arLayout->isAreaLayoutUsingThemeGridFramework()) {
            $c = Page::getCurrentPage();
            $pt = $c->getCollectionThemeObject();
            $gf = $pt->getThemeGridFrameworkObject();
        }
        if ($this->arLayout instanceof ThemeGridAreaLayout) {
            $this->set('enableThemeGrid', true);
            $this->set('themeGridFramework', $gf);
            $this->set('themeGridMaxColumns', $this->arLayout->getAreaLayoutMaxColumns());
            $this->set('themeGridName', $gf->getPageThemeGridFrameworkName());
            $this->render('edit_grid');
        } elseif ($this->arLayout instanceof CustomAreaLayout) {
            $this->set('enableThemeGrid', false);
            $this->set('spacing', $this->arLayout->getAreaLayoutSpacing());
            $this->set('iscustom', $this->arLayout->hasAreaLayoutCustomColumnWidths());
            $this->set('maxColumns', 12);
            $this->render('edit');
        } else {
            $preset = $this->arLayout->getPresetObject();
            $this->set('selectedPreset', $preset);
            $this->render('edit_preset');
        }
        $this->set('columnsNum', count($this->arLayout->getAreaLayoutColumns()));
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function add()
    {
        $maxColumns = 12; // normally
        // now we check our active theme and see if it has other plans
        $c = Page::getCurrentPage();
        $pt = $c->getCollectionThemeObject();
        if (is_object($pt) && $pt->supportsGridFramework() && is_object(
            $this->area
        ) && $this->area->getAreaGridMaximumColumns()
        ) {
            $gf = $pt->getThemeGridFrameworkObject();
            $this->set('enableThemeGrid', true);
            $this->set('themeGridName', $gf->getPageThemeGridFrameworkName());
            $this->set('themeGridFramework', $gf);
            $this->set('themeGridMaxColumns', $this->area->getAreaGridMaximumColumns());
        } else {
            $this->set('enableThemeGrid', false);
        }
        $this->set('columnsNum', 1);
        $this->set('maxColumns', $maxColumns);
    }

    /**
     * @return void
     */
    protected function setupCacheSettings()
    {
        if ($this->btCacheSettingsInitialized || Page::getCurrentPage()->isEditMode()) {
            return;
        }

        $this->btCacheSettingsInitialized = true;

        $btCacheBlockOutput = true;
        $btCacheBlockOutputOnPost = true;
        $btCacheBlockOutputLifetime = 0;

        $c = $this->getCollectionObject();

        $blocks = [];
        if ($this->getAreaObject() instanceof Area) {
            $layout = $this->getAreaLayoutObject();
            if ($layout) {
                $layout->setAreaObject($this->getAreaObject());
                foreach ($layout->getAreaLayoutColumns() as $column) {
                    $area = $column->getSubAreaObject();
                    if ($area) {
                        foreach ($area->getAreaBlocksArray($c) as $block) {
                            $blocks[] = $block;
                        }
                    }
                }
            }
        }

        $arrAssetBlocks = [];

        /** @var \Concrete\Core\Block\Block $b */
        foreach ($blocks as $b) {
            if ($b->overrideAreaPermissions()) {
                $btCacheBlockOutput = false;
                $btCacheBlockOutputOnPost = false;
                $btCacheBlockOutputLifetime = 0;
                break;
            }

            $btCacheBlockOutputOnPost = $btCacheBlockOutputOnPost && $b->cacheBlockOutputOnPost();

            //As soon as we find something which cannot be cached, entire block cannot be cached, so stop checking.
            if (!$b->cacheBlockOutput()) {
                $this->btCacheBlockOutput = false;
                $this->btCacheBlockOutputOnPost = false;
                $this->btCacheBlockOutputLifetime = 0;

                return;
            }
            $expires = $b->getBlockOutputCacheLifetime();
            if ($expires && $btCacheBlockOutputLifetime < $expires) {
                $btCacheBlockOutputLifetime = $expires;
            }

            $objController = $b->getController();
            if (is_callable([$objController, 'registerViewAssets'])) {
                $arrAssetBlocks[] = $objController;
            }
        }

        $this->btCacheBlockOutput = $btCacheBlockOutput;
        $this->btCacheBlockOutputOnPost = $btCacheBlockOutputOnPost;
        $this->btCacheBlockOutputLifetime = $btCacheBlockOutputLifetime;

        foreach ($arrAssetBlocks as $objController) {
            $objController->on_start();
            $objController->outputAutoHeaderItems();
            $objController->registerViewAssets();
            if ($objController instanceof UsesFeatureInterface) {
                foreach ($objController->getRequiredFeatures() as $feature) {
                    if (!in_array($feature, $this->requiredFeatures)) {
                        $this->requiredFeatures[] = $feature;
                    }
                }
            }
        }
    }

    /**
     * @param \Concrete\Core\Block\Block $b
     * @param \SimpleXMLElement $blockNode
     *
     * @throws \Exception
     *
     * @return void
     */
    protected function importAdditionalData($b, $blockNode)
    {
        /** @var \Concrete\Block\CoreAreaLayout\Controller $controller */
        $controller = $b->getController();
        $arLayout = $controller->getAreaLayoutObject();

        $columns = $arLayout->getAreaLayoutColumns();
        $layoutArea = $b->getBlockAreaObject();
        $arLayout->setAreaObject($b->getBlockAreaObject());
        /** @var Page $page */
        $page = $b->getBlockCollectionObject();

        $i = 0;
        foreach ($blockNode->arealayout->columns->column as $columnNode) {
            $column = $columns[$i];
            $as = new SubArea((string) $column->getAreaLayoutColumnDisplayID(), $layoutArea->getAreaHandle(), $layoutArea->getAreaID());
            $as->load($page);
            $column->setAreaID($as->getAreaID());
            $area = $column->getAreaObject();
            if ($columnNode->style) {
                $set = StyleSet::import($columnNode->style);
                $page->setCustomStyleSet($area, $set);
            }
            foreach ($columnNode->block as $bx) {
                $bt = BlockType::getByHandle((string) $bx['type']);
                if (!is_object($bt)) {
                    throw new \Exception(t('Invalid block type handle: %s', (string) ($bx['type'])));
                }
                $btc = $bt->getController();
                $btc->import($page, $area->getAreaHandle(), $bx);
            }
            $i++;
        }
    }

    /**
     * @param \Concrete\Core\Page\Page|mixed $page
     */
    private function resolveLayoutPreset(string $presetIdentififer, $page): ?AreaLayoutPresetInterface
    {
        if ($presetIdentififer === '') {
            return null;
        }
        $presetProvider = $this->app->make(AreaLayoutPresetProvider::class);
        $preset = $presetProvider->getPresetByIdentifier($presetIdentififer);
        if ($preset !== null) {
            return $preset;
        }
        if (!($page instanceof Page)) {
            return null;
        }
        /*
         * By default, the preset provider only provides presets for the currently active theme
         * But we may be importing a page that doesn't use the site theme, so let's load
         * the page theme too
         */
        $theme = $page->getCollectionThemeObject();
        if (!($theme instanceof ThemeProviderInterface)) {
            // The page theme does not provide presets
            return null;
        }
        // Add to the preset provider the page theme
        foreach ($presetProvider->getProviders() as $provider) {
            if ($provider instanceof ActiveThemeProvider || $provider instanceof ThemeProvider) {
                if ($provider->getThemeHandle() === $theme->getThemeHandle()) {
                    // The page theme is already listed in the provider
                    return null;
                }
            }
        }
        $presetProvider->register(new ThemeProvider($theme));

        return $presetProvider->getPresetByIdentifier($presetIdentififer);
    }
}
