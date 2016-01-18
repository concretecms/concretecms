<?php
namespace Concrete\Core\Editor;


use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Utility\Service\Identifier;
use URL;

class CkeditorEditor implements EditorInterface
{


    protected $assets;
    /**
     * @var Repository
     */
    protected $config;
    protected $identifier;
    protected $token;
    protected $allowFileManager;
    protected $allowSitemap;
    /**
     * @var PluginManager
     */
    protected $pluginManager;
    protected $styles;

    public function __construct(Repository $config, $pluginManager, $styles)
    {
        $this->assets = ResponseAssetGroup::get();
        $this->pluginManager = $pluginManager;
        $this->config = $config;
        $this->styles = $styles;
    }

    protected function getEditorScript($identifier, $options = array())
    {
        $pluginManager = $this->pluginManager;

        if ($this->allowFileManager()) {
            $pluginManager->select('concrete5filemanager');
            $pluginManager->select('concrete5uploadimage');
        } else {
            $pluginManager->deselect('concrete5filemanager');
            $pluginManager->deselect('concrete5uploadimage');
        }

        $this->requireEditorAssets();
        $plugins = $pluginManager->getSelectedPlugins();

        $options = array_merge(
            $options,
            array(
                'plugins' => implode(',', $plugins),
                'stylesSet' => 'concrete5styles',
                'filebrowserBrowseUrl' => 'a',
                'uploadUrl' => (string)URL::to('/ccm/system/file/upload'),
                'language' => $this->getLanguageOption(),
                'customConfig' => '',
                'uiColor' => '#FAFAFA',
                'allowedContent' => true,
                'image2_captionedClass' => 'content-editor-image-captioned',
                'image2_alignClasses' => array(
                    'content-editor-image-left',
                    'content-editor-image-center',
                    'content-editor-image-right'
                )
            )
        );
        $options = json_encode($options);
        $html = <<<EOL
        <script type="text/javascript">
        var CCM_EDITOR_SECURITY_TOKEN = "{$this->token}";
        $(function() {
            if (CKEDITOR.stylesSet.get('concrete5styles') === null) {
                CKEDITOR.stylesSet.add('concrete5styles', {$this->getStylesJson()});
            }
            var ckeditor = $('#{$identifier}').ckeditor({$options}).editor;
            ckeditor.on('blur',function(){
                return false;
            });
            ckeditor.on('remove', function(){
                $(this).destroy();
            });
        });
        </script>
EOL;
        return $html;
    }

    public function outputPageInlineEditor($key, $content = null)
    {
        if ($this->getPluginManager()->isSelected('autogrow')) {
            $this->getPluginManager()->deselect('autogrow');
        }

        $this->getPluginManager()->select('concrete5inline');
        $identifier = $this->getIdentifier();
        $html = sprintf(
            '<textarea id="%s_content" style="display:none;" name="%s"></textarea>
            <div contenteditable="true" id="%s">%s</div>',
            $identifier,
            $key,
            $identifier,
            $content
        );
        $html .= $this->getEditorScript(
            $identifier,
            array(
                'startupFocus' => true,
                'disableAutoInline' => true
            )
        );
        return $html;
    }

    public function outputStandardEditor($key, $content = null)
    {
        $options = array(
            'startupFocus' => true,
            'disableAutoInline' => true,
        );
        if ($this->getPluginManager()->isSelected('sourcearea')) {
            $this->getPluginManager()->deselect('sourcedialog');
        }

        $identifier = $this->getIdentifier();
        $html = sprintf(
            '<textarea id="%s" style="display:none;" name="%s">%s</textarea>',
            $identifier,
            $key,
            $content
        );
        $html .= $this->getEditorScript(
            $identifier,
            $options
        );
        return $html;
    }

    public function saveOptionsForm(Request $request)
    {
        $this->config->save('concrete.editor.concrete.enable_filemanager', $request->request->get('enable_filemanager'));
        $this->config->save('concrete.editor.concrete.enable_sitemap', $request->request->get('enable_sitemap'));

        $plugins = array();
        $post = $request->request->get('plugin');
        if (is_array($post)) {
            foreach ($post as $plugin) {
                if ($this->pluginManager->isAvailable($plugin)) {
                    $plugins[] = $plugin;
                }
            }
        }

        $this->config->save('concrete.editor.ckeditor4.plugins.selected', $plugins);
    }

    public function registerEditorPlugins()
    {
        $coreAssetDir = $this->getEditorRelativePath() . 'core/';
        $vendorAssetDir = $this->getEditorRelativePath() . 'vendor/';

        $assetList = \AssetList::getInstance();
        $assetList->register(
            'javascript',
            'editor/ckeditor4',
            $vendorAssetDir . 'ckeditor.js'
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
        $plugin->setName(t('Concrete5 Inline'));
        $plugin->requireAsset('editor/ckeditor4/concrete5inline');
        $this->getPluginManager()->register($plugin);

        $plugin = new Plugin();
        $plugin->setKey('concrete5filemanager');
        $plugin->setName(t('Concrete5 File Browser'));
        $plugin->requireAsset('editor/ckeditor4/concrete5filemanager');
        $this->getPluginManager()->register($plugin);

        $plugin = new Plugin();
        $plugin->setKey('concrete5uploadimage');
        $plugin->setName(t('Concrete5 Upload Image'));
        $plugin->requireAsset('editor/ckeditor4/concrete5uploadimage');
        $this->getPluginManager()->register($plugin);

        $plugin = new Plugin();
        $plugin->setKey('concrete5link');
        $plugin->setName(t('Concrete5 Link'));
        $plugin->requireAsset('editor/ckeditor4/concrete5link');
        $this->getPluginManager()->register($plugin);

        $plugin = new Plugin();
        $plugin->setKey('concrete5styles');
        $plugin->setName(t('Concrete5 Styles'));
        $plugin->requireAsset('editor/ckeditor4/concrete5styles');
        $this->getPluginManager()->register($plugin);
    }


    public function registerInternalPlugins()
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
            array('key' => 'forms', 'name' => t('Form Elements')),
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
            array('key' => 'placeholder', 'name' => t('Placeholder')),
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
            array('key' => 'tabletools', 'name' => t('Table Tools')),
            array('key' => 'templates', 'name' => t('Content Templates')),
            array('key' => 'toolbar', 'name' => t('Editor Toolbar')),
            array('key' => 'undo', 'name' => t('Undo')),
            array('key' => 'wsc', 'name' => t('WebSpellChecker')),
            array('key' => 'wysiwygarea', 'name' => t('IFrame Editing Area')),
        );

        foreach ($pluginList as $plugin) {
            $editorPlugin = new Plugin();
            $editorPlugin->setKey($plugin['key']);
            $editorPlugin->setName($plugin['name']);
            $this->getPluginManager()->register($editorPlugin);
        }
    }

    public function requireEditorAssets()
    {
        $this->assets->requireAsset('core/file-manager');
        $this->assets->requireAsset('editor/ckeditor4');
        $plugins = $this->pluginManager->getSelectedPluginObjects();
        foreach ($plugins as $plugin) {
            /** @var Plugin $plugin */
            $group = $plugin->getRequiredAssets();
            $this->assets->requireAsset($group);
        }
    }

    /**
     * @return string Returns the CKEditor language configuration
     */
    protected function getLanguageOption()
    {
        $langPath = DIR_BASE_CORE . '/' . $this->getEditorRelativePath() . 'vendor/lang/';
        $useLanguage = 'en';
        $language = strtolower(str_replace('_', '-', Localization::activeLocale()));
        if (file_exists($langPath . $language . '.js')) {
            $useLanguage = $language;
        } elseif (file_exists($langPath . strtolower(Localization::activeLanguage()) . '.js')) {
            $useLanguage = strtolower(Localization::activeLanguage());
        }
        return $useLanguage;
    }

    /**
     * @return string A JSON Encoded string of styles
     */
    public function getStylesJson()
    {
        return json_encode($this->styles);
    }

    public function outputPageComposerEditor($key, $content)
    {
        return $this->outputStandardEditor($key, $content);
    }

    public function outputBlockEditModeEditor($key, $content)
    {
        return $this->outputStandardEditor($key, $content);
    }

    public function allowFileManager()
    {
        return $this->allowFileManager;
    }

    public function allowSitemap()
    {
        return $this->allowSitemap;
    }

    public function setAllowFileManager($allow)
    {
        $this->allowFileManager = $allow;
    }

    public function setAllowSitemap($allow)
    {
        $this->allowSitemap = $allow;
    }


    public function getPluginManager()
    {
        return $this->pluginManager;
    }


    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @param bool $autogenerate When true, will generate a new identifier, when false will use the object's set identifier
     * @return string
     */
    public function getIdentifier($autogenerate = true)
    {
        if ($autogenerate) {
            return 'cke-' . (new Identifier())->getString(32);
        }
        return $this->identifier;
    }

    /**
     * Returns the relative path to the editor from the core directory
     */
    protected function getEditorRelativePath()
    {
        return 'js/ckeditor4/';
    }
}