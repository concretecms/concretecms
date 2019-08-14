<?php
namespace Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry;

/**
 * @since 8.2.0
 */
interface EntryInterface
{

    function getSiteTreeID();
    function getOptionElement();
    function getLabel();
    function getID();
    /**
     * @since 8.4.0
     */
    function getIcon();
    function getGroupClass();
    function isSelected();


}
