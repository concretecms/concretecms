<?php

namespace Concrete\Core\Editor;

use Concrete\Core\Application\Application;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Page\Theme\Theme as PageTheme;
use Concrete\Core\Site\Config\Liaison as Repository;
use Concrete\Core\Site\Service;
use Concrete\Core\Utility\Service\Identifier;
use Page;
use Permissions;
use stdClass;
use URL;
use Concrete\Core\User\User;

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
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * @var \Concrete\Core\Site\Service
     */
    private $site;

    /**
     * Initialize the instance.
     *
     * @param Repository $config
     * @param PluginManager $pluginManager
     * @param array $styles
     */
    public function __construct(Repository $config, Service $site, PluginManager $pluginManager, $styles, Application $app)
    {
        $this->config = $config;
        $this->pluginManager = $pluginManager;
        $this->assets = ResponseAssetGroup::get();
        $this->styles = $styles;
        $this->app = $app;
        $this->site = $site;
    }

    public function getOptions($dynamicOptions = []): array
    {

        $pluginManager = $this->getPluginManager();

        if ($this->allowFileManager()) {
            $pluginManager->select(['concretefilemanager', 'concreteuploadimage']);
        } else {
            $pluginManager->deselect(['concretefilemanager', 'concreteuploadimage']);
        }
        $plugins = $pluginManager->getSelectedPlugins();
        $snippetsAndClasses = $this->getEditorSnippetsAndClasses();

        if (!is_array($dynamicOptions)) {
            $dynamicOptions = [];
        }

        $defaultOptions = [
            'plugins' => implode(',', $plugins),
            'stylesSet' => 'concretestyles',
            'filebrowserBrowseUrl' => 'a',
            'uploadUrl' => (string) URL::to('/ccm/system/file/upload'),
            'language' => $this->getLanguageOption(),
            'customConfig' => '',
            'disableNativeSpellChecker' => false,
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
            'sitemap' => $this->allowSitemap()
        ];

        $customOptions = $this->config->get('editor.ckeditor4.custom_config_options');
        if (!is_array($customOptions)) {
            $customOptions = [];
        }

        $options = $dynamicOptions + $customOptions + $defaultOptions;
        return $options;
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

        $this->requireEditorAssets();

        $options = json_encode($this->getOptions($dynamicOptions));

        $removeEmptyIcon = '$removeEmpty[\'i\']';

        $jsfunc = <<<EOL
        function(identifier) {
            window.CCM_EDITOR_SECURITY_TOKEN = "{$this->token}";
            CKEDITOR.dtd.{$removeEmptyIcon} = false;
            if (CKEDITOR.stylesSet.get('concretestyles') === null) {
                CKEDITOR.stylesSet.add('concretestyles', {$this->getStylesJson()});
            }
            var element = $(identifier),
                form = element.closest('form'),
                ckeditor = element.ckeditor({$options}).editor;
            function resetMode() {
                if (ckeditor.mode === 'source' && ckeditor.setMode) {
                    ckeditor.setMode('wysiwyg');
                }
            }
            ckeditor.on('blur',function(){
                return false;
            });
            ckeditor.on('remove', function(){
                form.off('submit', resetMode);
                $(this).destroy();
            });
            form.on('submit', resetMode);
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

        $pluginManager->select('concreteinline');
        $identifier = $this->getIdentifier();

        $html = sprintf(
            '<textarea id="%s_content" style="display:none;" name="%s"></textarea>' .
            '<div id="%s">%s</div>',
            $identifier,
            $key,
            $identifier,
            $content
        );

        $html .= $this->getEditorScript(
            $identifier,
            [
                'startupFocus' => true,
            ]
        );

        return $html;
    }

    /**
     * Outputs a simple, sanitized editor.
     */
    public function outputSimpleEditor($key, $content = null)
    {
        $simplePlugins = ['basicstyles','dialogadvtab','divarea','image','tab','toolbar','undo','wysiwygarea','normalizeonchange'];
        if ($this->allowFileManager()) {
            $simplePlugins += ['concrete5filemanager', 'concrete5uploadimage'];
        }
        $this->pluginManager->select($simplePlugins);

        return $this->outputEditorWithOptions($key, [
            'plugins' => implode(',', $simplePlugins),
        ], $content);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Editor\EditorInterface::outputStandardEditor()
     */
    public function outputStandardEditor($key, $content = null)
    {
        return $this->outputEditorWithOptions($key, [], $content);
    }

    /**
     * Generate the HTML to be placed in a page to display the editor.
     *
     * @param string $key the name of the field to be used to POST the editor content
     * @param array $options custom options
     * @param string|null $content The initial value of the editor content
     *
     * @return string
     */
    public function outputEditorWithOptions($key, array $options = [], $content = null)
    {
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
        $options = [];

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

        // Load in selected_hidden plugins from the default site
        $defaultConfig = $this->site->getDefault()->getConfigRepository();
        $selected = (array) $defaultConfig->get('editor.ckeditor4.plugins.selected_hidden', []);

        // Merge in plugins selected in the dashboard form
        $post = $request->request->get('plugin');
        if (is_array($post)) {
            $selected = array_merge($selected, $post);
        }

        // Filter out plugins that aren't available
        $selected = array_filter($selected, [$this->pluginManager, 'isAvailable']);
        $this->config->save('editor.ckeditor4.plugins.selected', $selected);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Editor\EditorInterface::requireEditorAssets()
     */
    public function requireEditorAssets()
    {
        $this->assets->requireAsset('ckeditor');

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
        $pluginManager = $this->getPluginManager();
        if ($pluginManager->isSelected('sourcearea')) {
            // Sourcearea conflicts with composer
            // See https://github.com/concrete5/concrete5/issues/10232
            $pluginManager->deselect('sourcearea');
        }
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
            $('#{$identifier}').attr('contenteditable', 'true');
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
        $u = $this->app->make(User::class);
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
