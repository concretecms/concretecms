<?php
namespace Concrete\Core\Notification\View;

/**
 * @since 8.0.0
 */
interface ListViewInterface
{

    function renderIcon();
    function renderDetails();
    function renderMenu();
    function getNotificationObject();
    function getFormAction();

}
