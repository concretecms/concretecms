<?php
Loader::model('system/image_editor/control_set');
Loader::model('system/image_editor/filter');

$position = SystemImageEditorControlSet::getByHandle('position');
if ($position->getImageEditorControlSetHandle() != 'position')  {
	SystemImageEditorControlSet::add('position','Position');
}
$size = SystemImageEditorControlSet::getByHandle('size');
if ($size->getImageEditorControlSetHandle() != 'size')  {
	SystemImageEditorControlSet::add('size','Size');
}
$crop = SystemImageEditorControlSet::getByHandle('crop');
if ($crop->getImageEditorControlSetHandle() != 'crop')  {
	SystemImageEditorControlSet::add('crop','Crop');
}
$filters = SystemImageEditorControlSet::getByHandle('filter');
if ($filters->getImageEditorControlSetHandle() != 'filter')  {
	SystemImageEditorControlSet::add('filter','Filters');
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