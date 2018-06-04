<?php

namespace Concrete\Core\Editor;

use AssetList;
use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Legacy\FilePermissions;
use Concrete\Core\Legacy\TaskPermission;
use Concrete\Core\Localization\Localization;

class EditorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(
            EditorInterface::class,
            function (Application $app) {
                $config = $app->make('site')->getSite()->getConfigRepository();
                $styles = $config->get('editor.ckeditor4.styles', []);
                $pluginManager = new PluginManager();
                $selectedPlugins = $config->get('editor.ckeditor4.plugins.selected');
                if (!is_array($selectedPlugins)) {
                    $selectedPlugins = array_merge($config->get('editor.ckeditor4.plugins.selected_default'), $config->get('editor.ckeditor4.plugins.selected_hidden'));
                }
                $pluginManager->select($selectedPlugins);
                $this->registerCkeditorPlugins($pluginManager);
                $this->registerCorePlugins($pluginManager);
                $editor = new CkeditorEditor($config, $pluginManager, $styles);
                $editor->setToken($app->make('token')->generate('editor'));

                $filePermission = FilePermissions::getGlobal();
                $taskPermission = new TaskPermission();

                $editor->setAllowFileManager(
                    $filePermission->canAccessFileManager()
                    && $config->get('editor.concrete.enable_filemanager')
                );
                $editor->setAllowSitemap(
                    $taskPermission->canAccessSitemap()
                    && $config->get('editor.concrete.enable_sitemap')
                );

                return $editor;
            }
        );
        $this->app->alias(EditorInterface::class, 'editor');
    }

    protected function registerCkeditorPlugins(PluginManager $pluginManager)
    {
        $loc = Localization::getInstance();
        $loc->pushActiveContext(Localization::CONTEXT_UI);
        try {
            $pluginList = [
                'about' => t('About'),
                'autogrow' => t('Auto Grow'),
                'a11yhelp' => t('Accessibility Help'),
                'basicstyles' => t('Basic Styles'),
                'bidi' => t('BiDi (Text Direction)'),
                'blockquote' => t('Blockquote'),
                'clipboard' => t('Clipboard'),
                'colorbutton' => t('Color Button'),
                'colordialog' => t('Color Dialog'),
                'contextmenu' => t('Context Menu'),
                'dialogadvtab' => t('Advanced Tab for Dialogs'),
                'div' => t('Div Container Manager'),
                'divarea' => t('Div Editing Area'),
                'elementspath' => t('Elements Path'),
                'enterkey' => t('Enter Key'),
                'entities' => t('Escape HTML Entities'),
                'find' => t('Find / Replace'),
                'flash' => t('Flash Dialog'),
                'floatingspace' => t('Floating Space'),
                'font' => t('Font Size and Family'),
                'format' => t('Format'),
                'horizontalrule' => t('Horizontal Rule'),
                'htmlwriter' => t('HTML Output Writer'),
                'image' => t('Image'),
                'image2' => t('Enhanced Image'),
                'indentblock' => t('Indent Block'),
                'indentlist' => t('Indent List'),
                'justify' => t('Justify'),
                'language' => t('Language'),
                'list' => t('List'),
                'liststyle' => t('List Style'),
                'magicline' => t('Magic Line'),
                'maximize' => t('Maximize'),
                'newpage' => t('New Page'),
                'pagebreak' => t('Page Break'),
                'pastetext' => t('Paste As Plain Text'),
                'pastefromword' => t('Paste from Word'),
                'preview' => t('Preview'),
                'removeformat' => t('Remove Format'),
                'resize' => t('Editor Resize'),
                'scayt' => t('SpellCheckAsYouType (SCAYT)'),
                'selectall' => t('Select All'),
                'showblocks' => t('Show Blocks'),
                'showborders' => t('Show Table Borders'),
                'smiley' => t('Insert Smiley'),
                'sourcearea' => t('Source Editing Area'),
                'sourcedialog' => t('Source Dialog'),
                'specialchar' => t('Special Characters'),
                'stylescombo' => t('Styles Combo'),
                'tab' => t('Tab Key Handling'),
                'table' => t('Table'),
                'tableresize' => t('Table Resize'),
                'tableselection' => t('Table Selection'),
                'tabletools' => t('Table Tools'),
                'toolbar' => t('Editor Toolbar'),
                'undo' => t('Undo'),
                'wsc' => t('WebSpellChecker'),
                'wysiwygarea' => t('IFrame Editing Area'),
            ];
        } finally {
            $loc->popActiveContext();
        }
        foreach ($pluginList as $key => $name) {
            $editorPlugin = new Plugin();
            $editorPlugin->setKey($key);
            $editorPlugin->setName($name);
            $pluginManager->register($editorPlugin);
        }
    }

    private function registerCorePlugins(PluginManager $pluginManager)
    {
        $coreAssetDir = 'js/ckeditor4/core/';
        $vendorAssetDir = 'js/ckeditor4/vendor/';

        $assetList = AssetList::getInstance();
        $assetList->register(
            'javascript',
            'editor/ckeditor4',
            $vendorAssetDir . 'ckeditor.js',
            ['combine' => false, 'minify' => false]
        );
        $assetList->register(
            'css',
            'editor/ckeditor4',
            $coreAssetDir . 'ckeditor.css'
        );
        $assetList->register(
            'javascript',
            'editor/ckeditor4/jquery_adapter',
            $vendorAssetDir . 'adapters/jquery.js'
        );

        $assetList->registerGroup(
            'editor/ckeditor4',
            [
                ['javascript', 'editor/ckeditor4'],
                ['css', 'editor/ckeditor4'],
                ['javascript', 'editor/ckeditor4/jquery_adapter'],
            ]
        );

        $assetList->register(
            'javascript',
            'editor/ckeditor4/concrete5inline',
            $coreAssetDir . 'concrete5inline/register.js'
        );
        $assetList->register(
            'css',
            'editor/ckeditor4/concrete5inline',
            $coreAssetDir . 'concrete5inline/styles.css'
        );

        $assetList->registerGroup(
            'editor/ckeditor4/concrete5inline',
            [
                ['javascript', 'editor/ckeditor4/concrete5inline'],
                ['css', 'editor/ckeditor4/concrete5inline'],
            ]
        );

        $assetList->register(
            'javascript',
            'editor/ckeditor4/concrete5filemanager',
            $coreAssetDir . 'concrete5filemanager/register.js'
        );
        $assetList->register(
            'css',
            'editor/ckeditor4/concrete5filemanager',
            $coreAssetDir . 'concrete5filemanager/styles.css'
        );
        $assetList->registerGroup(
            'editor/ckeditor4/concrete5filemanager',
            [
                ['javascript', 'editor/ckeditor4/concrete5filemanager'],
                ['css', 'editor/ckeditor4/concrete5filemanager'],
            ]
        );

        $assetList->register(
            'javascript',
            'editor/ckeditor4/concrete5uploadimage',
            $coreAssetDir . 'concrete5uploadimage/register.js'
        );
        $assetList->registerGroup(
            'editor/ckeditor4/concrete5uploadimage',
            [
                ['javascript', 'editor/ckeditor4/concrete5uploadimage'],
            ]
        );

        $assetList->register(
            'javascript',
            'editor/ckeditor4/concrete5link',
            $coreAssetDir . 'concrete5link/register.js'
        );
        $assetList->registerGroup(
            'editor/ckeditor4/concrete5link',
            [
                ['javascript', 'editor/ckeditor4/concrete5link'],
            ]
        );

        $assetList->register(
            'javascript',
            'editor/ckeditor4/normalizeonchange',
            $coreAssetDir . 'normalizeonchange/register.js'
        );
        $assetList->registerGroup(
            'editor/ckeditor4/normalizeonchange',
            [
                ['javascript', 'editor/ckeditor4/normalizeonchange'],
            ]
        );

        $assetList->register(
            'javascript',
            'editor/ckeditor4/concrete5styles',
            $coreAssetDir . 'concrete5styles/register.js'
        );
        $assetList->register(
            'css',
            'editor/ckeditor4/concrete5styles',
            $coreAssetDir . 'concrete5styles/styles.css'
        );
        $assetList->registerGroup(
            'editor/ckeditor4/concrete5styles',
            [
                ['javascript', 'editor/ckeditor4/concrete5styles'],
                ['css', 'editor/ckeditor4/concrete5styles'],
            ]
        );

        $plugin = new Plugin();
        $plugin->setKey('concrete5inline');
        $plugin->setName(t('concrete5 Inline'));
        $plugin->requireAsset('editor/ckeditor4/concrete5inline');
        $pluginManager->register($plugin);

        $plugin = new Plugin();
        $plugin->setKey('concrete5filemanager');
        $plugin->setName(t('concrete5 File Browser'));
        $plugin->requireAsset('editor/ckeditor4/concrete5filemanager');
        $pluginManager->register($plugin);

        $plugin = new Plugin();
        $plugin->setKey('concrete5uploadimage');
        $plugin->setName(t('concrete5 Upload Image'));
        $plugin->requireAsset('editor/ckeditor4/concrete5uploadimage');
        $pluginManager->register($plugin);

        $plugin = new Plugin();
        $plugin->setKey('concrete5link');
        $plugin->setName(t('concrete5 Link'));
        $plugin->requireAsset('editor/ckeditor4/concrete5link');
        $pluginManager->register($plugin);

        $plugin = new Plugin();
        $plugin->setKey('normalizeonchange');
        $plugin->setName(t('Normalize On Change'));
        $plugin->requireAsset('editor/ckeditor4/normalizeonchange');
        $pluginManager->register($plugin);

        $plugin = new Plugin();
        $plugin->setKey('concrete5styles');
        $plugin->setName(t('concrete5 Styles'));
        $plugin->requireAsset('editor/ckeditor4/concrete5styles');
        $pluginManager->register($plugin);
    }
}
