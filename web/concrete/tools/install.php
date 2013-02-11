<?php
Loader::model('system/image_editor/control_set');

$position = Concrete5_Model_System_ImageEditor_ControlSet::getByHandle('position');
if ($position->getImageEditorControlSetHandle() != 'position')  {
	Concrete5_Model_System_ImageEditor_ControlSet::add('position','Position');
}
$size = Concrete5_Model_System_ImageEditor_ControlSet::getByHandle('size');
if ($size->getImageEditorControlSetHandle() != 'size')  {
	Concrete5_Model_System_ImageEditor_ControlSet::add('size','Size');
}
$filters = Concrete5_Model_System_ImageEditor_ControlSet::getByHandle('filter');
if ($size->getImageEditorControlSetHandle() != 'filter')  {
	Concrete5_Model_System_ImageEditor_ControlSet::add('filter','Filters');
}