<?php
namespace Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry;

interface EntryInterface
{

    function getSiteTreeID();
    function getOptionElement();
    function getLabel();
    function getID();
    function getGroupClass();
    function isSelected();


}
