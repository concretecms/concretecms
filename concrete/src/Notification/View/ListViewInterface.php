<?php
namespace Concrete\Core\Notification\View;

interface ListViewInterface
{

    function renderIcon();
    function renderDetails();
    function renderMenu();
    function getNotificationObject();
    function getFormAction();

}
