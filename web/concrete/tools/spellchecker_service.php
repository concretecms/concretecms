<?
defined('C5_EXECUTE') or die("Access Denied.");

/*
require_once('_base_tools.php');
require_once(DIR_CLASSES.'/toolbox/validation_tools.php');
*/

require_once(DIR_LIBRARIES_CORE . '/loader.php');
$txtToCheck=$_POST['txt'];
$fieldId=$_POST['fieldId']; 

$spellChecker=Loader::helper('spellchecker');
$correctedHTML=addslashes($spellChecker->findMisspellings($txtToCheck));
//var_dump( $spellChecker->wordSuggestions );  
$suggestionPairs=$spellChecker->getSuggestionPairsJSON();

echo '{html:"<div class=\"correctedHTML\">'.$correctedHTML.'</div><div id=\"suggestPopup\">SuggestPopup</div>",';
echo 'suggestions:{'.join(', ',$suggestionPairs).'},';
echo 'fieldId:"'.$fieldId.'"}';

?>