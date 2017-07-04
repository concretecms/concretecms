<?php
namespace Concrete\Core\Search\Field;

use Concrete\Core\Http\ResponseAssetGroup;

abstract class AbstractField implements FieldInterface
{
    protected $data = [];
    protected $requestVariables = [];

    public function renderSearchField()
    {
        return '';
    }

    public function __construct($data = null)
    {
        if (is_array($data)) {
            $this->data = $data;
        }
    }

    public function jsonSerialize()
    {
        ob_start();
        echo $this->renderSearchField();
        $field = ob_get_contents();
        ob_end_clean();

        $ag = ResponseAssetGroup::get();
        $assetsResponse = [];
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
            'assets' => $assetsResponse,
        ];
    }

    public function loadDataFromRequest(array $request)
    {
        foreach ($request as $key => $value) {
            if (in_array($key, $this->requestVariables)) {
                $this->data[$key] = $value;
            }
        }
    }
}
