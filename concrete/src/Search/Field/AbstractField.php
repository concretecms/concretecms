<?php
namespace Concrete\Core\Search\Field;

use Concrete\Core\Http\ResponseAssetGroup;

abstract class AbstractField implements FieldInterface
{
    /**
     * The current search data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * The list of all the request variables.
     *
     * @var array
     */
    protected $requestVariables = [];

    /**
     * {@inheritdoc}
     *
     * @see FieldInterface::renderSearchField()
     */
    public function renderSearchField()
    {
        return '';
    }

    /**
     * Initialize the instance.
     *
     * @param array|mixed $data the current search data
     */
    public function __construct($data = null)
    {
        if (is_array($data)) {
            $this->data = $data;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \JsonSerializable::jsonSerialize()
     */
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

    /**
     * {@inheritdoc}
     *
     * @see FieldInterface::loadDataFromRequest()
     */
    public function loadDataFromRequest(array $request)
    {
        foreach ($request as $key => $value) {
            if (in_array($key, $this->requestVariables)) {
                $this->data[$key] = $value;
            }
        }
    }
}
