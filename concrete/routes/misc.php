<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var \Concrete\Core\Routing\Router
 */
$router->all('/ccm/system/captcha/picture', '\Concrete\Core\Captcha\CaptchaWithPictureInterface::displayCaptchaPicture');
$router->all('/ccm/system/dialogs/language/update/details', '\Concrete\Controller\Dialog\Language\Update\Details::view');
$router->all('/dashboard/blocks/stacks/list', '\Concrete\Controller\SinglePage\Dashboard\Blocks\Stacks::list_page');
$router->all('/ccm/system/notification/alert/archive/', '\Concrete\Controller\Backend\Notification\Alert::archive');
$router->all('/ccm/system/accept_privacy_policy/', '\Concrete\Controller\Backend\PrivacyPolicy::acceptPrivacyPolicy');
$router->all('/ccm/system/account/remove_inbox_new_message_status', '\Concrete\Controller\Backend\Account::removeInboxNewMessageStatus');

$router->all('/ccm/system/css/layout/{arLayoutID}', '\Concrete\Controller\Frontend\Stylesheet::layout');
$router->all('/ccm/system/css/page/{cID}/{stylesheet}/{cvID}', '\Concrete\Controller\Frontend\Stylesheet::page_version');
$router->all('/ccm/system/css/page/{cID}/{stylesheet}', '\Concrete\Controller\Frontend\Stylesheet::page');
$router->all('/ccm/system/backend/editor_data/', '\Concrete\Controller\Backend\EditorData::view');
$router->all('/ccm/system/backend/get_remote_help/', '\Concrete\Controller\Backend\GetRemoteHelp::view');
$router->all('/ccm/system/backend/intelligent_search/', '\Concrete\Controller\Backend\IntelligentSearch::view');
$router->all('/ccm/system/jobs', '\Concrete\Controller\Frontend\Jobs::view');
$router->all('/ccm/system/jobs/run_single', '\Concrete\Controller\Frontend\Jobs::run_single');
$router->all('/ccm/system/jobs/check_queue', '\Concrete\Controller\Frontend\Jobs::check_queue');

$router->all('/ccm/system/summary_template/render/{categoryHandle}/{memberIdentifier}/{templateID}', '\Concrete\Controller\Backend\SummaryTemplate::render');

// @TODO remove the line below
$router->all('/tools/required/jobs', '\Concrete\Controller\Frontend\Jobs::view');
$router->all('/tools/required/jobs/check_queue', '\Concrete\Controller\Frontend\Jobs::check_queue');
$router->all('/tools/required/jobs/run_single', '\Concrete\Controller\Frontend\Jobs::run_single');
// end removing lines
$router->all('/ccm/system/upgrade/', '\Concrete\Controller\Upgrade::view');
$router->all('/ccm/system/upgrade/submit', '\Concrete\Controller\Upgrade::submit');
$router->all('/ccm/system/country-stateprovince-link/get_stateprovinces', '\Concrete\Controller\Frontend\CountryDataLink::getStateprovinces');
$router->all('/ccm/system/country-data-link/all', '\Concrete\Controller\Frontend\CountryDataLink::getAll');

$router->all('/ccm/system/batch/monitor/{handle}/{token}', '\Concrete\Controller\Backend\Batch::monitor');
$router->all('/ccm/system/dialogs/editor/settings/preview', 'Concrete\Controller\Dialog\Editor\Settings\Preview::view');
$router->all('/ccm/system/dashboard/attribute/set/update_order', 'Concrete\Controller\Backend\Attribute\Set\UpdateOrder::view');
$router->all('/ccm/system/heartbeat', '\Concrete\Controller\Frontend\Heartbeat::view');
