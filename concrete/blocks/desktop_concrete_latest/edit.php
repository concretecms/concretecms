<?php
defined('C5_EXECUTE') or die('Access Denied.');
/** @var string|null $slot */
/** @var \Concrete\Core\Form\Service\Form $form */
?>
<h2><?=t('Slot')?></h2>
<div>
<?=$form->select('slot', ['A' => 'A', 'B' => 'B', 'C' => 'C'], $slot ?? null)?>
</div>