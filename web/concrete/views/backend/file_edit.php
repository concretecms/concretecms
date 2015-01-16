<?php defined('C5_EXECUTE') or die('Access Denied.');

if ($to->getPackageHandle() != '') {
	Loader::packageElement('files/edit/' . $to->getEditor(), $to->getPackageHandle(), array('fv' => $fv));
} else {
    $view = new View;
    $view->setInnerContentFile(DIR_BASE . '/concrete/views/image-editor/editor.php');
    echo $view->renderViewContents(array('fv' => $fv));
}
