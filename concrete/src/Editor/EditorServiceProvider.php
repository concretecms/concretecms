<?php
namespace Concrete\Core\Editor;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Legacy\FilePermissions;
use Concrete\Core\Legacy\TaskPermission;

class EditorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(
            'editor',
            function ($app) {
                $config = $app->make('site')->getSite()->getConfigRepository();
                $styles = $config->get('editor.ckeditor4.styles', array());
                $pluginManager = new PluginManager();
                $pluginManager->selectMultiple(
                    $config->get('editor.ckeditor4.plugins.selected', array())
                );
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
    }

    protected function registerCkeditorPlugins(PluginManager $pluginManager)
    {
        $pluginList = array(
            array('key' => 'about', 'name' => t('About')),
            array('key' => 'autogrow', 'name' => t('Auto Grow')),
            array('key' => 'a11yhelp', 'name' => t('Accessibility Help')),
            array('key' => 'basicstyles', 'name' => t('Basic Styles')),
            array('key' => 'bidi', 'name' => t('BiDi (Text Direction)')),
            array('key' => 'blockquote', 'name' => t('Blockquote')),
            array('key' => 'clipboard', 'name' => t('Clipboard')),
            array('key' => 'colorbutton', 'name' => t('Color Button')),
            array('key' => 'colordialog', 'name' => t('Color Dialog')),
            array('key' => 'contextmenu', 'name' => t('Context Menu')),
            array('key' => 'dialogadvtab', 'name' => t('Advanced Tab for Dialogs')),
            array('key' => 'div', 'name' => t('Div Container Manager')),
            array('key' => 'divarea', 'name' => t('Div Editing Area')),
            array('key' => 'elementspath', 'name' => t('Elements Path')),
            array('key' => 'enterkey', 'name' => t('Enter Key')),
            array('key' => 'entities', 'name' => t('Escape HTML Entities')),
            array('key' => 'find', 'name' => t('Find / Replace')),
            array('key' => 'flash', 'name' => t('Flash Dialog')),
            array('key' => 'floatingspace', 'name' => t('Floating Space')),
            array('key' => 'font', 'name' => t('Font Size and Famiy')),
            array('key' => 'format', 'name' => t('Format')),
            array('key' => 'horizontalrule', 'name' => t('Horizontal Rule')),
            array('key' => 'htmlwriter', 'name' => t('HTML Output Writer')),
            array('key' => 'image', 'name' => t('Image')),
            array('key' => 'image2', 'name' => t('Enhanced Image')),
            array('key' => 'indentblock', 'name' => t('Indent Block')),
            array('key' => 'indentlist', 'name' => t('Indent List')),
            array('key' => 'justify', 'name' => t('Justify')),
            array('key' => 'language', 'name' => t('Language')),
            array('key' => 'list', 'name' => t('List')),
            array('key' => 'liststyle', 'name' => t('List Style')),
            array('key' => 'magicline', 'name' => t('Magic Line')),
            array('key' => 'maximize', 'name' => t('Maximize')),
            array('key' => 'newpage', 'name' => t('New Page')),
            array('key' => 'pagebreak', 'name' => t('Page Break')),
            array('key' => 'pastetext', 'name' => t('Paste As Plain Text')),
            array('key' => 'pastefromword', 'name' => t('Paste from Word')),
            array('key' => 'preview', 'name' => t('Preview')),
            array('key' => 'removeformat', 'name' => t('Remove Format')),
            array('key' => 'resize', 'name' => t('Editor Resize')),
            array('key' => 'scayt', 'name' => t('SpellCheckAsYouType (SCAYT)')),
            array('key' => 'selectall', 'name' => t('Select All')),
            array('key' => 'showblocks', 'name' => t('Show Blocks')),
            array('key' => 'showborders', 'name' => t('Show Table Borders')),
            array('key' => 'smiley', 'name' => t('Insert Smiley')),
            array('key' => 'sourcearea', 'name' => t('Source Editing Area')),
            array('key' => 'sourcedialog', 'name' => t('Source Dialog')),
            array('key' => 'specialchar', 'name' => t('Special Characters')),
            array('key' => 'stylescombo', 'name' => t('Styles Combo')),
            array('key' => 'tab', 'name' => t('Tab Key Handling')),
            array('key' => 'table', 'name' => t('Table')),
            array('key' => 'tableresize', 'name' => t('Table Resize')),
            array('key' => 'tableselection', 'name' => t('Table Selection')),
            array('key' => 'tabletools', 'name' => t('Table Tools')),
            array('key' => 'toolbar', 'name' => t('Editor Toolbar')),
            array('key' => 'undo', 'name' => t('Undo')),
            array('key' => 'wsc', 'name' => t('WebSpellChecker')),
            array('key' => 'wysiwygarea', 'name' => t('IFrame Editing Area')),
        );

        foreach ($pluginList as $plugin) {
            $editorPlugin = new Plugin();
            $editorPlugin->setKey($plugin['key']);
            $editorPlugin->setName($plugin['name']);
            $pluginManager->register($editorPlugin);
        }
    }

    private function registerCorePlugins(PluginManager $pluginManager)
    {

        $coreAssetDir = 'js/ckeditor4/core/';
        $vendorAssetDir = 'js/ckeditor4/vendor/';

        $assetList = \AssetList::getInstance();
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
            array(
                array('javascript', 'editor/ckeditor4'),
                array('css', 'editor/ckeditor4'),
                array('javascript', 'editor/ckeditor4/jquery_adapter')
            )
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
            array(
                array('javascript', 'editor/ckeditor4/concrete5inline'),
                array('css', 'editor/ckeditor4/concrete5inline')
            )
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
            array(
                array('javascript', 'editor/ckeditor4/concrete5filemanager'),
                array('css', 'editor/ckeditor4/concrete5filemanager'),
            )
        );

        $assetList->register(
            'javascript',
            'editor/ckeditor4/concrete5uploadimage',
            $coreAssetDir . 'concrete5uploadimage/register.js'
        );
        $assetList->registerGroup(
            'editor/ckeditor4/concrete5uploadimage',
            array(
                array('javascript', 'editor/ckeditor4/concrete5uploadimage'),
            )
        );

        $assetList->register(
            'javascript',
            'editor/ckeditor4/concrete5link',
            $coreAssetDir . 'concrete5link/register.js'
        );
        $assetList->registerGroup(
            'editor/ckeditor4/concrete5link',
            array(
                array('javascript', 'editor/ckeditor4/concrete5link'),
            )
        );

        $assetList->register(
            'javascript',
            'editor/ckeditor4/normalizeonchange',
            $coreAssetDir . 'normalizeonchange/register.js'
        );
        $assetList->registerGroup(
            'editor/ckeditor4/normalizeonchange',
            array(
                array('javascript', 'editor/ckeditor4/normalizeonchange'),
            )
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
            array(
                array('javascript', 'editor/ckeditor4/concrete5styles'),
                array('css', 'editor/ckeditor4/concrete5styles'),
            )
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
