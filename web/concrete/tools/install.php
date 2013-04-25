<?php
Loader::model('system/image_editor/control_set');
Loader::model('system/image_editor/component');
Loader::model('system/image_editor/filter');

// Control Sets
$position = SystemImageEditorControlSet::getByHandle('position');
if ($position->getImageEditorControlSetHandle() != 'position') {
	echo "Add Position Control Set\n";
	SystemImageEditorControlSet::add('position','Position');
}
$size = SystemImageEditorControlSet::getByHandle('size');
if ($size->getImageEditorControlSetHandle() != 'size') {
	echo "Add Size Control Set\n";
	SystemImageEditorControlSet::add('size','Size');
}
$crop = SystemImageEditorControlSet::getByHandle('crop');
if ($crop->getImageEditorControlSetHandle() != 'crop') {
	echo "Add Crop Control Set\n";
	SystemImageEditorControlSet::add('crop','Crop');
}
$filters = SystemImageEditorControlSet::getByHandle('filter');
if ($filters->getImageEditorControlSetHandle() != 'filter') {
	echo "Add Filter Control Set\n";
	SystemImageEditorControlSet::add('filter','Filters');
}
$rotate = SystemImageEditorControlSet::getByHandle('rotate');
if ($rotate->getImageEditorControlSetHandle() != 'rotate') {
	echo "Add Text Control Set\n";
	SystemImageEditorControlSet::add('rotate','Rotate');
}

$text = SystemImageEditorControlSet::getByHandle('text');
if ($text->getImageEditorControlSetHandle() != 'text') {
	echo "Add Text Control Set\n";
	SystemImageEditorControlSet::add('text','Text');
}

// Components
$text = SystemImageEditorComponent::getByHandle('text');
if ($text->getImageEditorComponentHandle() != 'text') {
	echo "Add Text Component\n";
	SystemImageEditorComponent::add('text','Text');
}
$image = SystemImageEditorComponent::getByHandle('image');
if ($image->getImageEditorComponentHandle() != 'image') {
	echo "Add Image Component\n";
	SystemImageEditorComponent::add('image','Image');
}
$shape = SystemImageEditorComponent::getByHandle('shape');
if ($shape->getImageEditorComponentHandle() != 'shape') {
	echo "Add shape Component\n";
	SystemImageEditorComponent::add('shape','Shape');
}

// Filters
$grayscale = SystemImageEditorFilter::getByHandle('grayscale');
if ($grayscale->getImageEditorFilterHandle() != 'grayscale') {
	echo "Add Grayscale Filter\n";
	SystemImageEditorFilter::add('grayscale','Gray Scale');
}
$sepia = SystemImageEditorFilter::getByHandle('sepia');
if ($sepia->getImageEditorFilterHandle() != 'sepia') {
	echo "Add Sepia Filter\n";
	SystemImageEditorFilter::add('sepia','Sepia');
}
$blur = SystemImageEditorFilter::getByHandle('gaussian_blur');
if ($blur->getImageEditorFilterHandle() != 'gaussian_blur') {
	echo "Add Blur Filter\n";
	SystemImageEditorFilter::add('gaussian_blur','Gaussian Blur');
}
$vignette = SystemImageEditorFilter::getByHandle('vignette');
if ($vignette->getImageEditorFilterHandle() != 'vignette') {
	echo "Add Vignette Filter\n";
	SystemImageEditorFilter::add('vignette','Vignette');
}