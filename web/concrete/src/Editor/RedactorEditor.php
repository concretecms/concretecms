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

    protected function getEditor($key, $content = null, $plugins = array())
    {
        $this->assets->requireAsset('core/file-manager');
        $this->assets->requireAsset('redactor');

        $html = sprintf('<textarea data-redactor-editor="%s" name="%s">%s</textarea>', $this->identifier, $key, $content);
        $html .= <<<EOL
        <script type="text/javascript">
        var CCM_EDITOR_SECURITY_TOKEN = "{$this->token}";
        $(function() {
            $('textarea[data-redactor-editor={$this->identifier}]').redactor({
                minHeight: '300',
                'concrete5': {
                    filemanager: {$this->allowFileManager()},
                    sitemap: {$this->allowSitemap()},
                    lightbox: true
                },
                'plugins': [
                    'concrete5inline', 'concrete5magic', 'undoredo', 'specialcharacters'
                ]
            });
        });
        </script>
EOL;
        return $html;
    }
    public function outputPageInlineEditor($key, $content = null)
    {
        return $this->getEditor($key, $content, array(
            'concrete5inline',
            'concrete5magic',
            'undoredo'
        ));
    }

    public function outputPageComposerEditor($key, $content)
    {
        return $this->getEditor($key, $content, array(
            'concrete5magic',
            'undoredo'
        ));
    }

}