<?php
namespace Concrete\Core\Express\Form\Control\Type\Item;

/**
 * @since 8.0.0
 */
interface ItemInterface
{
    public function getDisplayName();
    public function getIcon();
    public function getItemIdentifier();
}
