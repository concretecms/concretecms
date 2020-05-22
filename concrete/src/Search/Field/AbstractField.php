<?php
namespace Concrete\Core\Search\Field;

use Concrete\Core\Http\ResponseAssetGroup;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

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
     * Determines whether the data is loaded into the field. If this is true, loadFromRequest will not
     * repopulate from request.
     *
     * @var bool
     */
    protected $isLoaded = false;

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

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        $this->data = $data['data'];
    }

    public function setData($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * {@inheritdoc}
     *
     * @see FieldInterface::loadDataFromRequest()
     */
    public function loadDataFromRequest(array $request)
    {
        if (!$this->isLoaded) {
            foreach ($request as $key => $value) {
                if (in_array($key, $this->requestVariables)) {
                    $this->data[$key] = $value;
                }
            }
            $this->isLoaded = true;
        }
    }
}
