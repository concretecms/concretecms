<?php
namespace Concrete\Core\Search\Field;

use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Utility\Service\Xml;
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
    #[\ReturnTypeWillChange]
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

    public function export(\SimpleXMLElement $element)
    {
        $xml = new Xml();
        $fieldNode = $element->addChild('field');
        $fieldNode->addAttribute('key', $this->getKey());
        $xml->createCDataNode($fieldNode, 'data', json_encode($this->data));
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
     * @param string $key
     * @return mixed|null
     */
    public function getData(string $key)
    {
        return $this->data[$key] ?? null;
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

        return $this;
    }

    public function loadDataFromImport(\SimpleXMLElement $element)
    {
        if (!$this->isLoaded) {
            $this->data = json_decode($element->data);
        }
    }
}
