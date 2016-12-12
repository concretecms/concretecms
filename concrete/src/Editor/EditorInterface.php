<?php
namespace Concrete\Core\Editor;

use Concrete\Core\Http\Request;

interface EditorInterface
{
    public function outputPageInlineEditor($key, $content = null);
    public function outputPageComposerEditor($key, $content);
    public function outputBlockEditModeEditor($key, $content);
    public function outputStandardEditor($key, $content = null);

    public function setAllowSitemap($allow);
    public function setAllowFileManager($allow);

    public function getPluginManager();

    public function saveOptionsForm(Request $request);

    public function requireEditorAssets();
}
