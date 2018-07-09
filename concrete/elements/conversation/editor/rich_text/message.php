<?php
$cnvID = 0;

$obj = $editor->getConversationObject();
if (is_object($obj)) {
    $cnvID = $obj->getConversationID();
}


$ck = Core::make('editor');

echo $ck->outputStandardEditor($editor->getConversationEditorInputName(), $editor->getConversationEditorMessageBody());


