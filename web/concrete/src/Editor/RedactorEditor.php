<?php
namespace Concrete\Core\Editor;
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

    public function __construct()
    {
        $fp = FilePermissions::getGlobal();
        $tp = new TaskPermission();

        $this->assets = ResponseAssetGroup::get();
        $this->identifier = id(new Identifier())->getString(32);
        $this->token = Core::make("token")->generate('editor');
        $this->allowFileManager = $fp->canAccessFileManager();
        $this->allowSitemap = $tp->canAccessSitemap();
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
        $this->assets->requireAsset('core/file-manager');
        $this->assets->requireAsset('redactor');
        $concrete5 = array(
            'filemanager' => $this->allowFileManager(),
            'sitemap' => $this->allowSitemap(),
            'lightbox' => true
        );
        if (isset($options['concrete5'])) {
            $options['concrete5'] = array_merge($options['concrete5'], $concrete5);
        } else {
            $options['concrete5'] = $concrete5;
        }
        $options = json_encode($options);
        $html = sprintf('<textarea data-redactor-editor="%s" name="%s">%s</textarea>', $this->identifier, $key, $content);
        $html .= <<<EOL
        <script type="text/javascript">
        var CCM_EDITOR_SECURITY_TOKEN = "{$this->token}";
        $(function() {
            $('textarea[data-redactor-editor={$this->identifier}]').redactor({$options});
        });
        </script>
EOL;
        return $html;
    }
    public function outputPageInlineEditor($key, $content = null)
    {
        return $this->getEditor($key, $content, array('plugins' => array(
            'concrete5magic',
            'undoredo',
            'specialcharacters',
            'table',
            'fontfamily',
            'fontsize',
            'fontcolor',
            'underline',
            'concrete5inline'
        ), 'minHeight' => 300));
    }

    public function outputPageComposerEditor($key, $content)
    {
        return $this->getEditor($key, $content, array('plugins' => array(
            'concrete5magic',
            'undoredo',
            'specialcharacters',
            'table',
            'fontfamily',
            'fontsize',
            'fontcolor',
            'underline'
        ), 'minHeight' => 300));
    }

    public function outputStandardEditor($key, $content = null)
    {
        return $this->getEditor($key, $content, array('plugins' => array(
            'undoredo',
            'specialcharacters',
            'table',
            'fontfamily',
            'fontsize',
            'fontcolor',
            'underline'
        ), 'minHeight' => 300));
    }

}