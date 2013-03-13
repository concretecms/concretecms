<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
$form = Loader::helper('form');
print $form->textarea($editor->getConversationEditorInputName());