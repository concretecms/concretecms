<?php
namespace Concrete\Core\Feature\Detail;
use Loader;
class ImageDetail extends Detail {

	protected $src;
	protected $width;
	protected $height;

	public function __construct($mixed) {
		if (is_object($mixed)) {
			$f = $mixed->getImageFeatureDetailFileObject();
			if (is_object($f)) {
				$this->src = $f->getRelativePath();
				$this->width = $f->getAttribute('width');
				$this->height = $f->getAttribute('height');
			}
		} else {
			$this->src = $mixed;
		}
	}

	public function getSrc() {return $this->src;}
	public function getWidth() {return $this->width;}
	public function getHeight() {return $this->height;}


	public function getGatheringItemSuggestedSlotHeight() {
		if ($this->getHeight() < 240) {
			return 1;
		} else if ($this->getHeight() < 360) {
			return 2;
		}

		// no suggestion
		return 0;
	}

	public function getGatheringItemSuggestedSlotWidth() {
		if ($this->getWidth() < 240) {
			return 1;
		} else if ($this->getWidth() < 360) {
			return 2;
		}

		// no suggestion
		return 0;

	}
}
