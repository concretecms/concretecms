<?php

namespace Concrete\Core\Sharing\ShareThisPage;

class ServiceList
{
    protected static function getServices()
    {
        return array(
            array('facebook', 'Facebook', 'fab fa-facebook'),
            array('twitter', 'Twitter', 'fab fa-twitter'),
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
