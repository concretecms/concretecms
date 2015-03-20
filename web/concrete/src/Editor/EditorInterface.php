<?php
namespace Concrete\Core\Editor;

interface EditorInterface
{

	public function outputPageInlineEditor($key, $content = null);
	public function outputPageComposerEditor($key, $content);


	public function setAllowSitemap($allow);
	public function setAllowFileManager($allow);

}
