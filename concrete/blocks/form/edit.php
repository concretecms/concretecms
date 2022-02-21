<?php defined('C5_EXECUTE') or die('Access Denied.');
use Concrete\Block\Form\MiniSurvey;

/** @var \Concrete\Core\Block\Block $b */
/** @var \Concrete\Core\Page\Page $c */
/** @var \Concrete\Core\Area\Area $a */
/** @var \Concrete\Core\Block\BlockType\BlockType $bt */
/** @var \Concrete\Core\Block\View\BlockView $this */

//$miniSurveyInfo['surveyName']= $bs->surveyName;
$miniSurvey = new MiniSurvey();
$miniSurveyInfo = $miniSurvey->getMiniSurveyBlockInfo($b->getBlockID());
MiniSurvey::questionCleanup((int) ($miniSurveyInfo['questionSetId'] ?? null), $b->getBlockID());
?>

<script>
<?php if (is_object($b->getProxyBlock())) {
    ?>
	var thisbID=parseInt(<?php echo $b->getProxyBlock()->getBlockID()?>); 
<?php
} else {
    ?>
	var thisbID=parseInt(<?php echo $b->getBlockID()?>); 
<?php
} ?>
var thisbtID=parseInt(<?php echo $b->getBlockTypeID()?>); 
</script>

<?php  $this->inc('form_setup_html.php', ['c' => $c, 'b' => $b, 'miniSurveyInfo' => $miniSurveyInfo, 'miniSurvey' => $miniSurvey, 'a' => $a, 'bt' => $bt]); ?>
