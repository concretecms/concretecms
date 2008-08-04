<p>This is the add template for the basic test block. Anything you add in this view will automatically be wrapped in a form and, when submitted, sent to the block's controller.</p>

<?=$form->label('content', 'Name');?>
<?=$form->text('content', array('style' => 'width: 320px'));?>