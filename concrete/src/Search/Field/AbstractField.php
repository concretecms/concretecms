<?php
namespace Concrete\Core\Search\Field;

use Concrete\Core\Http\ResponseAssetGroup;
use Doctrine\Common\Collections\ArrayCollection;

abstract class AbstractField implements FieldInterface
{

    protected $data = array();
    protected $requestVariables = array();

    public function renderSearchField()
    {
        return '';
    }

    public function jsonSerialize()
    {

        ob_start();
        print $this->renderSearchField();
        $field = ob_get_contents();
        ob_end_clean();

        $ag = ResponseAssetGroup::get();
        $assetsResponse = array();
        foreach ($ag->getAssetsToOutput() as $position => $assets) {
            foreach ($assets as $asset) {
                if (is_object($asset)) {
                    $assetsResponse[$asset->getAssetType()][] = $asset->getAssetURL();
                }
            }
        }

        return [
            'key' => $this->getKey(),
            'label' => $this->getDisplayName(),
            'element' => $field,
            'data' => $this->data,
            'assets' => $assetsResponse
        ];
    }

    public function loadDataFromRequest(array $request)
    {
        foreach($request as $key => $value) {
            if (in_array($key, $this->requestVariables)) {
                $this->data[$key] = $value;
            }
        }
    }
}
