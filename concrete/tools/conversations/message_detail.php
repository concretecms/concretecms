<?php defined('C5_EXECUTE') or die('Access Denied.');

use \Concrete\Core\Conversation\Message\Message as ConversationMessage;

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
$ax = $app->make('helper/ajax');
$vs = $app->make('helper/validation/strings');
$ve = $app->make('error');

if ($app->request->post('enablePosting')) {
    $enablePosting = true;
} else {
    $enablePosting = false;
}

if (in_array($app->request->post('displayMode'), array('flat'))) {
    $displayMode = $app->request->post('displayMode');
} else {
    $displayMode = 'threaded';
}

if ($app->make('helper/validation/numbers')->integer($app->request->post('cnvMessageID')) && $app->request->post('cnvMessageID') > 0) {
    $message = ConversationMessage::getByID($app->request->post('cnvMessageID'));
    if (is_object($message)) {
        if ($message->isConversationMessageApproved()) {
            View::element('conversation/message', array(
                'message' => $message,
                'displayMode' => $displayMode,
                'enablePosting' => $enablePosting,
                'enableCommentRating' => $app->request->post('enableCommentRating'),
                'displaySocialLinks' => $app->request->post('displaySocialLinks')
            ));
        } else {
            // it's a new message, but it's pending
            View::element('conversation/message/pending', array('message' => $message));
        }
    }
}
