<?php
namespace Concrete\Core\Url;

interface UrlInterface extends \League\Url\UrlInterface
{

    /**
     * @param integer $port
     */
    public function setPortIfNecessary($port);

}
