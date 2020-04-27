<?php
namespace Concrete\Core\Express\Form\Control\Type;

use Concrete\Core\Entity\Express\Control\AuthorControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Control\PublicIdentifierControl;
use Concrete\Core\Entity\Express\Control\TextControl;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Express\Form\Control\SaveHandler\AuthorSaveHandler;
use Concrete\Core\Express\Form\Control\Template\Template;
use Concrete\Core\Express\Form\Control\Type\Item\AuthorEntityPropertyItem;
use Concrete\Core\Express\Form\Control\Type\Item\PublicIdentifierPropertyItem;
use Concrete\Core\Express\Form\Control\Type\Item\TextEntityPropertyItem;
use Concrete\Core\Import\Item\Express\Control\EntityPropertyControl;

class EntityPropertyType implements TypeInterface
{
    public function getPluralDisplayName()
    {
        return t('Core Properties');
    }

    public function getType()
    {
        return 'entity_property';
    }

    public function getValidator()
    {
        return false;
    }

    public function getDisplayName()
    {
        return t('Core Property');
    }

    public function getItems(Entity $entity)
    {
        return array(
            new TextEntityPropertyItem(),
            new AuthorEntityPropertyItem(),
            new PublicIdentifierPropertyItem()
        );
    }

    public function createControlByIdentifier($id)
    {
        switch ($id) {
            case 'text':
                return new TextControl();
            case 'author':
                return new AuthorControl();
            case 'public-identifier':
                return new PublicIdentifierControl();
        }
    }

    public function getSaveHandler(Control $control)
    {
        if ($control instanceof AuthorControl) {
            return new AuthorSaveHandler();
        }
        return null;
    }

    public function getImporter()
    {
        return new EntityPropertyControl();
    }
}
