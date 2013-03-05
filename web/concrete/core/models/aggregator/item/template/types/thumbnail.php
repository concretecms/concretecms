<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ThumbnailAggregatorItemTemplate extends AggregatorItemTemplate {

	public function getAggregatorItemTemplateData(AggregatorItem $item) {
		$items = parent::getAggregatorItemTemplateData($item);
		$images = $item->getAggregatorItemExtendedFeatureDetailObjects('image');
		if (is_object($images[0])) {
			$f = $images[0]->getFileObject();
			if (is_object($f)) {
				$items['thumbnailPath'] = $f->getRelativePath();
			}
		}
		return $items;
	}
	
}