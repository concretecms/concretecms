<?php
Loader::model('system/image_editor/control_set');
Loader::model('system/image_editor/filter');

$position = Concrete5_Model_System_ImageEditor_ControlSet::getByHandle('position');
if ($position->getImageEditorControlSetHandle() != 'position')  {
	Concrete5_Model_System_ImageEditor_ControlSet::add('position','Position');
}
$size = Concrete5_Model_System_ImageEditor_ControlSet::getByHandle('size');
if ($size->getImageEditorControlSetHandle() != 'size')  {
	Concrete5_Model_System_ImageEditor_ControlSet::add('size','Size');
}
$filters = Concrete5_Model_System_ImageEditor_ControlSet::getByHandle('filter');
if ($filters->getImageEditorControlSetHandle() != 'filter')  {
	Concrete5_Model_System_ImageEditor_ControlSet::add('filter','Filters');
}
$grayscale = SystemImageEditorFilter::getByHandle('grayscale');
if ($grayscale->getImageEditorFilterHandle() != 'grayscale')  {
	SystemImageEditorFilter::add('grayscale','Gray Scale');
}
$sepia = SystemImageEditorFilter::getByHandle('sepia');
if ($sepia->getImageEditorFilterHandle() != 'sepia')  {
	SystemImageEditorFilter::add('sepia','Sepia');
}
$blur = SystemImageEditorFilter::getByHandle('blur');
if ($blur->getImageEditorFilterHandle() != 'blur')  {
	SystemImageEditorFilter::add('gaussian_blur','Gaussian Blur');
}