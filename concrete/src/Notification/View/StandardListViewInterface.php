<?php
namespace Concrete\Core\Notification\View;

interface StandardListViewInterface extends ListViewInterface
{

    function getIconClass();
    function getTitle();
    function getShortDescription();
    function getActionDescription();
    function getInitiatorUserObject();
    function getInitiatorComment();
    function renderInitiatorActionDescription();
    function renderInitiatorCommentDescription();
    function getMenu();

}
