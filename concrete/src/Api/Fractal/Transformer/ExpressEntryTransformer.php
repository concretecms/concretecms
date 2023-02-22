<?php
namespace Concrete\Core\Api\Fractal\Transformer;

use Carbon\Carbon;
use Concrete\Core\Api\Resources;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class ExpressEntryTransformer extends TransformerAbstract
{


    protected $availableIncludes = [
        'author',
    ];

    /**
     * @var Entity
     */
    protected $object;

    /**
     * ExpressEntryTransformer constructor.
     * @param string[] $availableIncludes
     */
    public function __construct(Entity $object)
    {
        $this->object = $object;
        foreach ($object->getAttributes() as $attribute) {
            $this->availableIncludes[] = $attribute->getAttributeKeyHandle();
        }
        foreach ($object->getAssociations() as $association) {
            $this->availableIncludes[] = $association->getTargetPropertyName();
        }
    }

    /**
     * @param Entry $entry
     * @return array
     */
    public function transform(Entry $entry)
    {
        return [
            'id' => $entry->getPublicIdentifier(),
            'date_added' => Carbon::make($entry->getDateCreated())->toAtomString(),
            'date_last_updated' => $entry->getDateModified() ?
                Carbon::make($entry->getDateModified())->toAtomString() : null,
            'url' => $entry->getURL(),
            'label' => $entry->getLabel(),
        ];
    }

    public function includeCustomAttributes(Entry $entry)
    {
        $values = $entry->getObjectAttributeCategory()->getAttributeValues($entry);
        return new Collection($values, new AttributeValueTransformer(), Resources::RESOURCE_CUSTOM_ATTRIBUTES);
    }

    public function includeAuthor(Entry $entry)
    {
        $user = $entry->getAuthor();
        if ($user) {
            return new Item($user->getUserInfoObject(), new UserTransformer(), Resources::RESOURCE_USERS);
        }
    }

    public function __call($method, $arguments)
    {
        $attributeOrObjectHandle = snake_case(substr($method, 7));
        $entry = $arguments[0];
        /**
         * @var $entry Entry
         */
        foreach ($entry->getEntity()->getAttributes() as $attribute) {
            if ($attribute->getAttributeKeyHandle() == $attributeOrObjectHandle) {
                $value = $entry->getAttributeValue($attribute);
                if ($value) {
                    return new Item($value, new AttributeValueTransformer(), Resources::RESOURCE_CUSTOM_ATTRIBUTES);
                }
            }
        }
        $associations = $entry->getAssociations();
        foreach ($associations as $association) {
            if ($association->getAssociation()->getTargetPropertyName() == $attributeOrObjectHandle) {
                if ($association instanceof Entry\OneAssociation) {
                    $associatedEntry = $association->getSelectedEntry();
                    return new Item(
                        $associatedEntry,
                        new ExpressEntryTransformer($associatedEntry->getEntity()),
                        $associatedEntry->getEntity()->getPluralHandle()
                    );
                } else if ($association instanceof Entry\ManyAssociation) {
                    $associatedEntries = $association->getSelectedEntries();
                    return new Collection(
                        $associatedEntries,
                        new ExpressEntryTransformer($association->getAssociation()->getTargetEntity()),
                        $association->getAssociation()->getTargetEntity()->getHandle(),
                    );
                }
            }
        }
    }
}
