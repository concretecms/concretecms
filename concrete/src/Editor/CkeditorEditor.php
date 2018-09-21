<?php

namespace Concrete\Core\Editor;

use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Page\Theme\Theme as PageTheme;
use Concrete\Core\Site\Config\Liaison as Repository;
use Concrete\Core\Utility\Service\Identifier;
use Page;
use Permissions;
use stdClass;
use URL;
use User;

class CkeditorEditor implements EditorInterface
{
    /**
     * The configuration repository.
     *
     * @var Repository
     */
    protected $config;

    /**
     * The plugin manager instance.
     *
     * @var PluginManager
     */
    protected $pluginManager;

    /**
     * @var ResponseAssetGroup
     */
    protected $assets;

    /**
     * The custom editor identifier.
     *
     * @var string|null
     */
    protected $identifier;

    /**
     * The CSRF token.
     *
     * @var string|null
     */
    protected $token;

    /**
     * Can the editor offer the "browse files" feature?
     *
     * @var bool
     */
    protected $allowFileManager = false;

    /**
     * Can the editor offer the "browse sitemap" feature?
     *
     * @var bool
     */
    protected $allowSitemap = false;

    /**
     * @var array
     */
    protected $styles;

    /**
     * Initialize the instance.
     *
     * @param Repository $config
     * @param PluginManager $pluginManager
     * @param array $styles
     */
    public function __construct(Repository $config, PluginManager $pluginManager, $styles)
    {
        $this->config = $config;
        $this->pluginManager = $pluginManager;
        $this->assets = ResponseAssetGroup::get();
        $this->styles = $styles;
    }

    /**
     * Generate the Javascript code that initialize the plugin.
     *
     * @param array $dynamicOptions a list of custom options that override the default ones
     *
     * @return string
     */
    public function getEditorInitJSFunction($dynamicOptions = [])
    {
        $pluginManager = $this->getPluginManager();

        if ($this->allowFileManager()) {
            $pluginManager->select(['concrete5filemanager', 'concrete5uploadimage']);
        } else {
            $pluginManager->deselect(['concrete5filemanager', 'concrete5uploadimage']);
        }

        $this->requireEditorAssets();
        $plugins = $pluginManager->getSelectedPlugins();
        $snippetsAndClasses = $this->getEditorSnippetsAndClasses();

        if (!is_array($dynamicOptions)) {
            $dynamicOptions = [];
        }

        $defaultOptions = [
            'plugins' => implode(',', $plugins),
            'stylesSet' => 'concrete5styles',
            'filebrowserBrowseUrl' => 'a',
            'uploadUrl' => (string) URL::to('/ccm/system/file/upload'),
            'language' => $this->getLanguageOption(),
            'customConfig' => '',
            'allowedContent' => true,
            'baseFloatZIndex' => 1990, /* Must come below modal variable in variables.less */
            'image2_captionedClass' => 'content-editor-image-captioned',
            'image2_alignClasses' => [
                'content-editor-image-left',
                'content-editor-image-center',
                'content-editor-image-right',
            ],
            'toolbarGroups' => $this->config->get('editor.ckeditor4.toolbar_groups'),
            'snippets' => $snippetsAndClasses->snippets,
            'classes' => $snippetsAndClasses->classes,
        ];

        $customOptions = $this->config->get('editor.ckeditor4.custom_config_options');
        if (!is_array($customOptions)) {
            $customOptions = [];
        }

        $options = json_encode($dynamicOptions + $customOptions + $defaultOptions);
        $removeEmptyIcon = '$removeEmpty[\'i\']';

        $jsfunc = <<<EOL
        function(identifier) {
            window.CCM_EDITOR_SECURITY_TOKEN = "{$this->token}";
            CKEDITOR.dtd.{$removeEmptyIcon} = false;
            if (CKEDITOR.stylesSet.get('concrete5styles') === null) {
                CKEDITOR.stylesSet.add('concrete5styles', {$this->getStylesJson()});
            }
            var ckeditor = $(identifier).ckeditor({$options}).editor;
            ckeditor.on('blur',function(){
                return false;
            });
            ckeditor.on('remove', function(){
                $(this).destroy();
            });
            if (CKEDITOR.env.ie) {
                ckeditor.on('ariaWidget', function (e) {
                    setTimeout(function() {
                        var \$contents = $(e.editor.ui.contentsElement.$),
                            \$textarea = \$contents.find('>textarea.cke_source');
                        if (\$textarea.length === 1) {
                            \$textarea.css({
                                width: \$contents.innerWidth() + 'px',
                                height: \$contents.innerHeight() + 'px'
                            });
                        }
                    }, 50);
                });
            }
            {$this->config->get('editor.ckeditor4.editor_function_options')}
        }
EOL;

        return $jsfunc;
    }

    /**
     * Generate the Javascript code that initialize the plugin when it will be used inline.
     *
     * @return string
     */
    public function outputInlineEditorInitJSFunction()
    {
        $pluginManager = $this->getPluginManager();
        if ($pluginManager->isSelected('autogrow')) {
            $pluginManager->deselect('autogrow');
        }

        return $this->getEditorInitJSFunction();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Editor\EditorInterface::outputPageInlineEditor()
     */
    public function outputPageInlineEditor($key, $content = null)
    {
        $pluginManager = $this->getPluginManager();
        if ($pluginManager->isSelected('autogrow')) {
            $pluginManager->deselect('autogrow');
        }

        $pluginManager->select('concrete5inline');
        $identifier = $this->getIdentifier();

        $html = sprintf(
            '<textarea id="%s_content" style="display:none;" name="%s"></textarea>' .
            '<div contenteditable="true" id="%s">%s</div>',
            $identifier,
            $key,
            $identifier,
            $content
        );

        $html .= $this->getEditorScript(
            $identifier,
            [
                'startupFocus' => true,
                'disableAutoInline' => true,
            ]
        );

        return $html;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Editor\EditorInterface::outputStandardEditor()
     */
    public function outputStandardEditor($key, $content = null)
    {
        $options = [
            'disableAutoInline' => true,
        ];

        $pluginManager = $this->getPluginManager();
        if ($pluginManager->isSelected('sourcearea')) {
            $pluginManager->deselect('sourcedialog');
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

    /**
     * Generate the standard Javascript code that initialize the plugin.
     *
     * @return string
     */
    public function outputStandardEditorInitJSFunction()
    {
        $options = [
            'disableAutoInline' => true,
        ];

        $pluginManager = $this->getPluginManager();
        if ($pluginManager->isSelected('sourcearea')) {
            $pluginManager->deselect('sourcedialog');
        }

        return $this->getEditorInitJSFunction($options);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Editor\EditorInterface::saveOptionsForm()
     */
    public function saveOptionsForm(Request $request)
    {
        $this->config->save('editor.concrete.enable_filemanager', (bool) $request->request->get('enable_filemanager'));
        $this->config->save('editor.concrete.enable_sitemap', (bool) $request->request->get('enable_sitemap'));

        $selected = $this->config->get('editor.ckeditor4.plugins.selected_hidden');
        $post = $request->request->get('plugin');
        if (is_array($post)) {
            $selected = array_merge($selected, $post);
        }
        $plugins = [];
        foreach ($selected as $plugin) {
            if ($this->pluginManager->isAvailable($plugin)) {
                $plugins[] = $plugin;
            }
        }

        $this->config->save('editor.ckeditor4.plugins.selected', $plugins);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Editor\EditorInterface::requireEditorAssets()
     */
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
     * Returns a JSON Encoded string of styles.
     *
     * @return string
     */
    public function getStylesJson()
    {
        return json_encode($this->styles);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Editor\EditorInterface::outputPageComposerEditor()
     */
    public function outputPageComposerEditor($key, $content)
    {
        return $this->outputStandardEditor($key, $content);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Editor\EditorInterface::outputBlockEditModeEditor()
     */
    public function outputBlockEditModeEditor($key, $content)
    {
        return $this->outputStandardEditor($key, $content);
    }

    /**
     * Can the editor offer the "browse files" feature?
     *
     * @return bool
     */
    public function allowFileManager()
    {
        return $this->allowFileManager;
    }

    /**
     * Can the editor offer the "browse sitemap" feature?
     *
     * @return bool
     */
    public function allowSitemap()
    {
        return $this->allowSitemap;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Editor\EditorInterface::setAllowFileManager()
     */
    public function setAllowFileManager($allow)
    {
        $this->allowFileManager = (bool) $allow;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Editor\EditorInterface::setAllowSitemap()
     */
    public function setAllowSitemap($allow)
    {
        $this->allowSitemap = (bool) $allow;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Editor\EditorInterface::getPluginManager()
     */
    public function getPluginManager()
    {
        return $this->pluginManager;
    }

    /**
     * Set the CSRF token.
     *
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Set the custom editor identifier.
     *
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Get the editor identifier.
     *
     * @param bool $autogenerate When true, will generate a new identifier, when false will use the object's set identifier
     *
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
     * Get the HTML code to be used to initialize the editor.
     *
     * @param string $identifier the editor identifier
     * @param array $options a list of custom options that override the default one
     *
     * @return string
     */
    protected function getEditorScript($identifier, $options = [])
    {
        $jsFunc = $this->getEditorInitJSFunction($options);

        $html = <<<EOL
        <script type="text/javascript">
        $(function() {
            var initEditor = {$jsFunc};
            initEditor('#{$identifier}');
         });
        </script>
EOL;

        return $html;
    }

    /**
     * Get the CKEditor language configuration.
     *
     * @return string|null
     */
    protected function getLanguageOption()
    {
        $langPath = DIR_BASE_CORE . '/js/ckeditor4/vendor/lang/';
        $useLanguage = 'en';

        $language = strtolower(str_replace('_', '-', Localization::activeLocale()));
        if (file_exists($langPath . $language . '.js')) {
            $useLanguage = $language;
        } elseif (file_exists($langPath . strtolower(Localization::activeLanguage()) . '.js')) {
            $useLanguage = strtolower(Localization::activeLanguage());
        } else {
            $useLanguage = null;
        }

        return $useLanguage;
    }

    /**
     * Build an object containing the CKEditor preconfigured snippets and classes.
     *
     * @return \stdClass
     */
    private function getEditorSnippetsAndClasses()
    {
        $obj = new stdClass();
        $obj->snippets = [];
        $u = new User();
        if ($u->isRegistered()) {
            $snippets = \Concrete\Core\Editor\Snippet::getActiveList();
            foreach ($snippets as $sns) {
                $menu = new stdClass();
                $menu->scsHandle = $sns->getSystemContentEditorSnippetHandle();
                $menu->scsName = $sns->getSystemContentEditorSnippetName();
                $obj->snippets[] = $menu;
            }
        }
        $c = Page::getCurrentPage();
        $obj->classes = [];
        if (is_object($c) && !$c->isError()) {
            $cp = new Permissions($c);
            if ($cp->canViewPage()) {
                $pt = $c->getCollectionThemeObject();
                if (is_object($pt)) {
                    if ($pt->getThemeHandle()) {
                        $obj->classes = $pt->getThemeEditorClasses();
                    } else {
                        $siteTheme = $pt::getSiteTheme();
                        if (is_object($siteTheme)) {
                            $obj->classes = $siteTheme->getThemeEditorClasses();
                        }
                    }
                }
            }
        } else {
            $siteTheme = PageTheme::getSiteTheme();
            if (is_object($siteTheme)) {
                $obj->classes = $siteTheme->getThemeEditorClasses();
            }
        }

        return $obj;
    }
}
