<?php

use Concrete\Core\Conversation\Rating\Type as ConversationRatingType;
use Concrete\Core\Http\Request;
use Concrete\Core\Permission\IPService;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;

defined('C5_EXECUTE') or die('Access Denied.');

$app = Application::getFacadeApplication();
$request = $app->make(Request::class);
$post = $request->request;

$cnvMessageID = $post->get('cnvMessageID');
if ($app->make('helper/validation/numbers')->integer($cnvMessageID, 1)) {
    $msg = ConversationMessage::getByID($cnvMessageID);
    if ($msg !== null) {
        $msp = new Permissions($msg);
        if ($msp->canRateConversationMessage()) {
            $cnvRatingTypeHandle = $post->get('cnvRatingTypeHandle');
            $ratingType = is_string($cnvRatingTypeHandle) ? ConversationRatingType::getByHandle($cnvRatingTypeHandle) : null;
            if ($ratingType !== null) {
                $commentRatingUserID = 0;
                if (User::isLoggedIn()) {
                    $user = new User();
                    if ($user->isRegistered()) {
                        $commentRatingUserID = (int) $user->getUserID();
                    }
                }
                $commentRatingIP = (string) $app->make(IPService::class)->getRequestIPAddress();
                $msg->rateMessage($ratingType, $commentRatingIP, $commentRatingUserID);
            }
        }
    }
}
