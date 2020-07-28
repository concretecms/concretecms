<?php
namespace Concrete\Core\Attribute\Key\Component\KeySelector;

use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Context\AttributePanelContext;
use Concrete\Core\Attribute\View as AttributeTypeView;
use Concrete\Core\Entity\Attribute\Key\Key as AttributeKey;
use Concrete\Core\Http\ResponseAssetGroup;

/**
 * Responsible for serializing an attribute key for use in the key selector component.
 */
class KeySerializer implements \JsonSerializable
{
    /**
     * @var AttributeKey
     */
    protected $key;

    /**
     * @var AttributeValueInterface|null
     */
    protected $value;

    public function __construct(AttributeKey $key, AttributeValueInterface $value = null)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function getAssets()
    {
        $ag = ResponseAssetGroup::get();
        $return = [];
        foreach ($ag->getAssetsToOutput() as $position => $assets) {
            foreach ($assets as $asset) {
                if (is_object($asset)) {
                    // have to do a check here because we might be included a dumb javascript call like i18n_js
                    $return[$asset->getAssetType()][] = $asset->getAssetURL();
                }
            }
        }
        return $return;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        ob_start();
        if ($this->value) {
            $this->key->render(new AttributePanelContext(), $this->value);
        } else {
            $av = new AttributeTypeView($this->key);
            echo $av->render(new AttributePanelContext());
        }
        $html = ob_get_contents();
        ob_end_clean();

        $data = [
            'controlID' => $this->key->getController()->getControlID(),
            'akID' => $this->key->getAttributeKeyID(),
            'label' => $this->key->getAttributeKeyDisplayName(),
            'content' => $html,
            'assets' => $this->getAssets(),
        ];

        return $data;
    }
}
