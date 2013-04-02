<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
?>

<div class="control-group">
	<label class="control-label"><?=$label?></label>
	<div class="controls">
		<?
		print $form->textarea($this->field('content'), array(
			'class' => $class,
			'style' => 'width: 580px; height: 380px'
		));
		?>
	</div>
</div>