<?php
namespace Concrete\Core\Editor;

use Concrete\Core\Site\Config\Liaison as Repository;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Utility\Service\Identifier;
use URL;

class CkeditorEditor implements EditorInterface
{
    /** @var Repository */
    protected $config;

    /** @var PluginManager */
    protected $pluginManager;

    /** @var ResponseAssetGroup */
    protected $assets;

    protected $identifier;
    protected $token;
    protected $allowFileManager;
    protected $allowSitemap;
    protected $styles;

    public function __construct(Repository $config, PluginManager $pluginManager, $styles)
    {
        $this->assets = ResponseAssetGroup::get();
        $this->pluginManager = $pluginManager;
        $this->config = $config;
        $this->styles = $styles;
    }

    /**
     * @param string $identifier
     * @param array $options
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
     * @param array $options
     *
     * @return string
     */
    public function getEditorInitJSFunction($options = [])
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
            [
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
                'toolbarGroups' => [
                    ['name' => 'mode', 'groups' => ['mode']],
                    ['name' => 'document', 'groups' => ['document']],
                    ['name' => 'doctools', 'groups' => ['doctools']],
                    ['name' => 'clipboard', 'groups' => ['clipboard']],
                    ['name' => 'undo', 'groups' => ['undo']],
                    ['name' => 'find', 'groups' => ['find']],
                    ['name' => 'selection', 'groups' => ['selection']],
                    ['name' => 'spellchecker', 'groups' => ['spellchecker']],
                    ['name' => 'editing', 'groups' => ['editing']],
                    ['name' => 'basicstyles', 'groups' => ['basicstyles']],
                    ['name' => 'cleanup', 'groups' => ['cleanup']],
                    ['name' => 'list', 'groups' => ['list']],
                    ['name' => 'indent', 'groups' => ['indent']],
                    ['name' => 'blocks', 'groups' => ['blocks']],
                    ['name' => 'align', 'groups' => ['align']],
                    ['name' => 'bidi', 'groups' => ['bidi']],
                    ['name' => 'paragraph', 'groups' => ['paragraph']],
                    ['name' => 'links', 'groups' => ['links']],
                    ['name' => 'insert', 'groups' => ['insert']],
                    ['name' => 'forms', 'groups' => ['forms']],
                    ['name' => 'styles', 'groups' => ['styles']],
                    ['name' => 'colors', 'groups' => ['colors']],
                    ['name' => 'tools', 'groups' => ['tools']],
                    ['name' => 'others', 'groups' => ['others']],
                    ['name' => 'about', 'groups' => ['about']],
                ],
            ]
        );

        $options = json_encode($options);
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
        }
EOL;

        return $jsfunc;
    }

    /**
     * @return string
     */
    public function outputInlineEditorInitJSFunction()
    {
        if ($this->getPluginManager()->isSelected('autogrow')) {
            $this->getPluginManager()->deselect('autogrow');
        }

        return $this->getEditorInitJSFunction();
    }

    /**
     * @param string $key
     * @param string|null $content
     *
     * @return string
     */
    public function outputPageInlineEditor($key, $content = null)
    {
        if ($this->getPluginManager()->isSelected('autogrow')) {
            $this->getPluginManager()->deselect('autogrow');
        }

        $this->getPluginManager()->select('concrete5inline');
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
     * @param string $key
     * @param string|null $content
     *
     * @return string
     */
    public function outputStandardEditor($key, $content = null)
    {
        $options = [
            'disableAutoInline' => true,
        ];

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

    /**
     * @return string
     */
    public function outputStandardEditorInitJSFunction()
    {
        $options = [
            'disableAutoInline' => true,
        ];

        if ($this->getPluginManager()->isSelected('sourcearea')) {
            $this->getPluginManager()->deselect('sourcedialog');
        }

        return $this->getEditorInitJSFunction($options);
    }

    /**
     * @param Request $request
     */
    public function saveOptionsForm(Request $request)
    {
        $this->config->save('editor.concrete.enable_filemanager', $request->request->get('enable_filemanager'));
        $this->config->save('editor.concrete.enable_sitemap', $request->request->get('enable_sitemap'));

        $plugins = [];
        $post = $request->request->get('plugin');
        $selectedHidden = $this->config->get('editor.ckeditor4.plugins.selected_hidden');
        if (is_array($post)) {
            $post = array_merge($selectedHidden, $post);
            foreach ($post as $plugin) {
                if ($this->pluginManager->isAvailable($plugin)) {
                    $plugins[] = $plugin;
                }
            }
        } else {
            foreach ($selectedHidden as $plugin) {
                if ($this->pluginManager->isAvailable($plugin)) {
                    $plugins[] = $plugin;
                }
            }
        }

        $this->config->save('editor.ckeditor4.plugins.selected', $plugins);
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
     * Returns the CKEditor language configuration
     *
     * @return string
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
        }

        return $useLanguage;
    }

    /**
     * Returns a JSON Encoded string of styles
     *
     * @return string
     */
    public function getStylesJson()
    {
        return json_encode($this->styles);
    }

    /**
     * @param string $key
     * @param string $content
     *
     * @return string
     */
    public function outputPageComposerEditor($key, $content)
    {
        return $this->outputStandardEditor($key, $content);
    }

    /**
     * @param string $key
     * @param string $content
     *
     * @return string
     */
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

    /**
     * @param bool $allow
     */
    public function setAllowFileManager($allow)
    {
        $this->allowFileManager = $allow;
    }

    /**
     * @param bool $allow
     */
    public function setAllowSitemap($allow)
    {
        $this->allowSitemap = $allow;
    }

    /**
     * @return PluginManager
     */
    public function getPluginManager()
    {
        return $this->pluginManager;
    }

    /**
     * @param string $token
     */
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
}
