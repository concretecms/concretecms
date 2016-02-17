<?php
namespace Concrete\Core\Editor;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Legacy\FilePermissions;
use Concrete\Core\Legacy\TaskPermission;
use Concrete\Core\Utility\Service\Identifier;
use Core;

class RedactorEditor implements EditorInterface
{
    protected $assets;
    protected $identifier;
    protected $token;
    protected $allowFileManager;
    protected $allowSitemap;
    protected $pluginManager;

    public function __construct()
    {
        $fp = FilePermissions::getGlobal();
        $tp = new TaskPermission();

        $this->assets = ResponseAssetGroup::get();
        $this->token = Core::make("token")->generate('editor');
        $this->allowFileManager = \Config::get('concrete.editor.concrete.enable_filemanager') && $fp->canAccessFileManager();
        $this->allowSitemap = \Config::get('concrete.editor.concrete.enable_sitemap') && $tp->canAccessSitemap();
        $this->pluginManager = new PluginManager();

        $this->pluginManager->register('undoredo', t('Undo/Redo'));
        $this->pluginManager->register('underline', t('Underline'));
        $this->pluginManager->register('concrete5lightbox', t('Lightbox'));
        $this->pluginManager->register('specialcharacters', t('Special Characters Palette'));
        $this->pluginManager->register('table', t('Table'));
        $this->pluginManager->register('fontfamily', t('Font Family'));
        $this->pluginManager->register('fontsize', t('Font Size'));
        $this->pluginManager->register('fontcolor', t('Font Color'));

        $this->pluginManager->selectMultiple(\Config::get('concrete.editor.plugins.selected'));

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

    protected function getEditor($key, $content = null, $options = array())
    {
        $this->requireEditorAssets();
        $concrete5 = array(
            'filemanager' => $this->allowFileManager(),
            'sitemap' => $this->allowSitemap()
        );
        if (isset($options['concrete5'])) {
            $options['concrete5'] = array_merge($options['concrete5'], $concrete5);
        } else {
            $options['concrete5'] = $concrete5;
        }
        $options = json_encode($options);
        $identifier = id(new Identifier())->getString(32);
        $html = sprintf('<textarea data-redactor-editor="%s" name="%s">%s</textarea>', $identifier, $key, htmlspecialchars($content, ENT_QUOTES, APP_CHARSET));
        $html .= <<<EOL
        <script type="text/javascript">
        var CCM_EDITOR_SECURITY_TOKEN = "{$this->token}";
        $(function() {
            $('textarea[data-redactor-editor={$identifier}]').redactor({$options}).on('remove', function(){
                $(this).redactor('core.destroy');
            })
        });
        </script>
EOL;
        return $html;
    }

    public function outputPageInlineEditor($key, $content = null)
    {
        $plugins = $this->pluginManager->getSelectedPlugins();
        $plugins[] = 'concrete5magic';
        $plugins[] = 'concrete5inline';
        return $this->getEditor($key, $content, array('plugins' => $plugins, 'minHeight' => 300));
    }

    public function outputBlockEditModeEditor($key, $content)
    {
        $plugins = $this->pluginManager->getSelectedPlugins();
        $plugins[] = 'concrete5magic';
        return $this->getEditor($key, $content, array('plugins' => $plugins, 'minHeight' => 300));
    }

    public function outputPageComposerEditor($key, $content)
    {
        return $this->outputBlockEditModeEditor($key, $content);
    }

    public function outputStandardEditor($key, $content = null)
    {
        $plugins = $this->pluginManager->getSelectedPlugins();
        return $this->getEditor($key, $content, array('plugins' => $plugins, 'minHeight' => 300));
    }

    public function getPluginManager()
    {
        return $this->pluginManager;
    }

    public function setPluginManager(PluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }

    public function saveOptionsForm(Request $request)
    {
        \Config::save('concrete.editor.concrete.enable_filemanager', $request->request->get('enable_filemanager'));
        \Config::save('concrete.editor.concrete.enable_sitemap', $request->request->get('enable_sitemap'));

        $plugins = array();
        $post = $request->request->get('plugin');
        if (is_array($post)) {
            foreach($post as $plugin) {
                if ($this->pluginManager->isAvailable($plugin)) {
                    $plugins[] = $plugin;
                }
            }
        }

        \Config::save('concrete.editor.plugins.selected', $plugins);
    }

    public function requireEditorAssets()
    {
        $this->assets->requireAsset('core/file-manager');
        $this->assets->requireAsset('redactor');
        $plugins = $this->pluginManager->getSelectedPluginObjects();
        foreach ($plugins as $plugin) {
            $group = $plugin->getRequiredAssets();
            $this->assets->requireAsset($group);
        }


    }
}