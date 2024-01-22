<?php

namespace Concrete\Core\Sharing\ShareThisPage;

class ServiceList
{
    protected static function getServices()
    {
        return array(
            array('facebook', 'Facebook', 'fab fa-facebook'),
            array('twitter', 'X',  null, '<svg id="icon-twitter-x" width="16" height="16" viewBox="0 0 300 300" version="1.1" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M178.57 127.15 290.27 0h-26.46l-97.03 110.38L89.34 0H0l117.13 166.93L0 300.25h26.46l102.4-116.59 81.8 116.59h89.34M36.01 19.54H76.66l187.13 262.13h-40.66"/></svg>'),
            array('linkedin', 'LinkedIn', 'fab fa-linkedin'),
            array('reddit', 'Reddit', 'fab fa-reddit'),
            array('pinterest', 'Pinterest', 'fab fa-pinterest'),
            array('google_plus', 'Google Plus', 'fab fa-google-plus-square'),
            array('print', t('Print'), 'print', '<i class="fas fa-print" aria-hidden="true" title="' . h(t("Print")) . '"></i>'),
            array('email', 'Email', 'envelope', '<i class="fas fa-envelope" aria-hidden="true" title="' . h(t("Email")) . '"></i>'),
        );
    }

    public static function get()
    {
        $services = [];

        foreach (static::getServices() as $serviceArray) {
            $ssHandle = isset($serviceArray[0]) ? $serviceArray[0] : null;
            $ssName = isset($serviceArray[1]) ? $serviceArray[1] : null;
            $ssIcon = isset($serviceArray[2]) ? $serviceArray[2] : null;
            $customHTML = isset($serviceArray[3]) ? $serviceArray[3] : null;

            $service = new Service($ssHandle, $ssName, $ssIcon, $customHTML);

            $services[] = $service;
        }

        return $services;
    }
}
