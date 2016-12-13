<?php
namespace Concrete\Core\Editor;


use Illuminate\Config\Repository;
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

    public function __construct($config, $pluginManager, $styles)
    {
        $this->assets = ResponseAssetGroup::get();
        $this->pluginManager = $pluginManager;
        $this->config = $config;
        $this->styles = $styles;
    }

    protected function getEditorScript($identifier, $options = array())
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

    public function getEditorInitJSFunction($options = array()) {
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
                'allowedContent' => true,
                'baseFloatZIndex' => 1990, /* Must come below modal variable in variables.less */
                'image2_captionedClass' => 'content-editor-image-captioned',
                'image2_alignClasses' => array(
                    'content-editor-image-left',
                    'content-editor-image-center',
                    'content-editor-image-right'
                ),
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
                ]
            )
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
        }
EOL;
        return $jsfunc;
    }

    public function outputInlineEditorInitJSFunction() {

        if ($this->getPluginManager()->isSelected('autogrow')) {
            $this->getPluginManager()->deselect('autogrow');
        }

        return $this->getEditorInitJSFunction();
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

    public function outputStandardEditorInitJSFunction() {
        $options = array(
            'disableAutoInline' => true,
        );
        if ($this->getPluginManager()->isSelected('sourcearea')) {
            $this->getPluginManager()->deselect('sourcedialog');
        }

        return $this->getEditorInitJSFunction($options);
    }

    public function outputStandardEditor($key, $content = null)
    {
        $options = array(
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
        $this->config->save('editor.concrete.enable_filemanager', $request->request->get('enable_filemanager'));
        $this->config->save('editor.concrete.enable_sitemap', $request->request->get('enable_sitemap'));

        $plugins = array();
        $post = $request->request->get('plugin');
        $selected_hidden = $this->config->get('editor.ckeditor4.plugins.selected_hidden');
        if (is_array($post)) {
            $post = array_merge($selected_hidden, $post);
            foreach ($post as $plugin) {
                if ($this->pluginManager->isAvailable($plugin)) {
                    $plugins[] = $plugin;
                }
            }
        } else {
            foreach ($selected_hidden as $plugin) {
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
     * @return string Returns the CKEditor language configuration
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
}
