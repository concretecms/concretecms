<?
namespace \Concrete\Core\Feature\Detail;
use Loader;
class ImageDetail extends Detail {

	protected $path;
	protected $width;
	protected $height;

	public function __construct($mixed) {
		if (is_object($mixed)) {
			$f = $mixed->getImageFeatureDetailFileObject();
			if (is_object($f)) {
				$this->path = $f->getRelativePath();
				$this->width = $f->getAttribute('width');
				$this->height = $f->getAttribute('height');
			}
		} else {
			$this->path = $mixed;
			//$r = @getimagesize($this->path);
			if ($r[0]) {
				$this->width = $r[0];
			}
			if ($r[1]) {
				$this->height = $r[1];
			}
		}
	}

	public function getPath() {return $this->path;}
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
