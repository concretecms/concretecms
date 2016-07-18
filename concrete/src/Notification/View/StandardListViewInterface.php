<?php
namespace Concrete\Core\Notification\View;

interface StandardListViewInterface extends ListViewInterface
{

    function getIconClass();
    function getTitle();
    function getShortDescription();
    function getDetailedDescription();
    function getRequesterUserObject();
    function getRequesterComment();
    function getActions();

}
