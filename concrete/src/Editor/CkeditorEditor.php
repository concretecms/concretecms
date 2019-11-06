<?php

namespace Concrete\Core\Editor;

use Concrete\Core\Application\Application;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Site\Config\Liaison as Repository;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Site\Service;
use Concrete\Core\User\User;
use Concrete\Core\Utility\Service\Identifier;

/**
 * Interface that all rich-text editors must implement.
 */
class CKEditorEditor implements EditorInterface
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
        $this->allowFileManager = (bool)$allow;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Editor\EditorInterface::setAllowSitemap()
     */
    public function setAllowSitemap($allow)
    {
        $this->allowSitemap = (bool)$allow;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Editor\EditorInterface::saveOptionsForm()
     */
    public function saveOptionsForm(Request $request)
    {
        $this->config->save('editor.concrete.enable_filemanager', (bool)$request->request->get('enable_filemanager'));
        $this->config->save('editor.concrete.enable_sitemap', (bool)$request->request->get('enable_sitemap'));

        // Load in selected_hidden plugins from the default site
        $defaultConfig = $this->site->getDefault()->getConfigRepository();
        $selected = (array)$defaultConfig->get('editor.ckeditor4.plugins.selected_hidden', []);

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
     * Build an object containing the CKEditor preconfigured snippets and classes.
     *
     * @return \stdClass
     */
    private function getEditorSnippetsAndClasses()
    {
        $obj = new \stdClass();
        $obj->snippets = [];
        $u = new User();
        if ($u->isRegistered()) {
            $snippets = \Concrete\Core\Editor\Snippet::getActiveList();
            foreach ($snippets as $sns) {
                $menu = new \stdClass();
                $menu->scsHandle = $sns->getSystemContentEditorSnippetHandle();
                $menu->scsName = $sns->getSystemContentEditorSnippetName();
                $obj->snippets[] = $menu;
            }
        }
        $c = Page::getCurrentPage();
        $obj->classes = [];
        if (is_object($c) && !$c->isError()) {
            $cp = new Checker($c);
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
            $siteTheme = Theme::getSiteTheme();
            if (is_object($siteTheme)) {
                $obj->classes = $siteTheme->getThemeEditorClasses();
            }
        }

        return $obj;
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
    
    protected function filterIncompatiblePlugins()
    {
        $pluginManager = clone $this->pluginManager;
        if ($pluginManager->isSelected('sourcearea') && $pluginManager->isSelected('sourcedialog')) {
            $pluginManager->deselect('sourcearea');
        }
        return $pluginManager;
    }

    protected function getDefaultEditorOptions()
    {
        $pluginManager = $this->filterIncompatiblePlugins();
        $plugins = $pluginManager->getSelectedPlugins();
        $snippetsAndClasses = $this->getEditorSnippetsAndClasses();
        $defaultOptions = [
            'plugins' => implode(',', $plugins),
            'stylesSet' => 'concrete5styles',
            'filebrowserBrowseUrl' => 'a',
            'uploadUrl' => (string) \URL::to('/ccm/system/file/upload'),
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
            'sitemap' => $this->allowSitemap()
        ];
        return $defaultOptions;
    }
    
    protected function getSiteConfigurationOptions()
    {
        $customOptions = $this->config->get('editor.ckeditor4.custom_config_options');
        if (!is_array($customOptions)) {
            $customOptions = [];
        }
        return $customOptions;
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



    public function outputPageInlineEditor($key, $content = null)
    {
        $this->requireEditorAssets();
        if ($this->pluginManager->isSelected('autogrow')) {
            $this->pluginManager->deselect('autogrow');
        }
        $this->pluginManager->select('concrete5inline');

        $identifier = $this->getIdentifier();
        $html = sprintf(
            '<textarea id="%s_content" style="display:none;" name="%s"></textarea>' .
            '<div contenteditable="true" id="%s">%s</div>',
            $identifier,
            $key,
            $identifier,
            $content
        );
        
        $customOptions = [
            'startupFocus' => true,
        ];

        $options = json_encode(
            $customOptions + $this->getSiteConfigurationOptions()  + $this->getDefaultEditorOptions()
        );

        $js = <<<EOL
        CKEDITOR.disableAutoInline = true;
        $('#{$identifier}').ckeditor({$options});

EOL;

        $js = '<script type="text/javascript">' . $js . '</script>';
        $html .= $js;

        return $html;
    }
    
    public function outputStandardEditor($key, $content = null)
    {
        $this->requireEditorAssets();
        $identifier = $this->getIdentifier();
        $html = sprintf(
            '<textarea id="%s" name="%s">%s</textarea>',
            $identifier,
            $key,
            $content
        );

        $options = json_encode(
            $this->getSiteConfigurationOptions()  + $this->getDefaultEditorOptions()
        );

        $js = <<<EOL

        $('#{$identifier}').ckeditor({$options});

EOL;

        $js = '<script type="text/javascript">' . $js . '</script>';
        $html .= $js;

        return $html;
    }

    public function outputPageComposerEditor($key, $content)
    {
        return $this->outputStandardEditor($key, $content);
    }

    public function outputBlockEditModeEditor($key, $content)
    {
        return $this->outputStandardEditor($key, $content);
    }

}
