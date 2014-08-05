<?php
namespace Concrete\Core\Sharing\ShareThisPage;
class ServiceList
{
    protected static function getServices()
    {
        return array(
            array('facebook', 'Facebook', 'facebook'),
            array('twitter', 'Twitter', 'twitter'),
            array('linkedin', 'LinkedIn', 'linkedin-square'),
            array('reddit', 'Reddit', 'reddit'),
            array('email', 'Email', 'envelope')
        );
    }

    public static function get()
    {
        $services = static::getServices();
        $return = array();
        foreach($services as $serviceArray)
        {
            $o = new Service($serviceArray[0], $serviceArray[1], $serviceArray[2]);
            $return[] = $o;
        }
        return $return;
    }


}