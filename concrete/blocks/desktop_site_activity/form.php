<?php defined('C5_EXECUTE') or die('Access Denied.');
/** @var \Concrete\Core\Form\Service\Form $form */
/** @var string[]|null $types */
$types = $types ?? [];
?>
<div class="form-group">
    <?php echo $form->label('types', t('Types to Display')); ?>
    <div class="form-check"><?=$form->checkbox('types[]', 'form_submissions', in_array('form_submissions', $types))?> <?=$form->label('types_form_submissions', t('Form Submissions'), ['class' => 'form-check-label'])?></div>
    <div class="form-check"><?=$form->checkbox('types[]', 'survey_results', in_array('survey_results', $types))?> <?=$form->label('types_survey_results', t('Survey Results'), ['class' => 'form-check-label'])?></div>
    <div class="form-check"><?=$form->checkbox('types[]', 'signups', in_array('signups', $types))?> <?=$form->label('types_signups', t('New Users'), ['class' => 'form-check-label'])?></div>
    <div class="form-check"><?=$form->checkbox('types[]', 'conversation_messages', in_array('conversation_messages', $types))?> <?=$form->label('types_conversation_messages', t('Conversation Messages'), ['class' => 'form-check-label'])?></div>
    <div class="form-check"><?=$form->checkbox('types[]', 'workflow', in_array('workflow', $types))?> <?=$form->label('types_workflow', t('Workflow Approvals'), ['class' => 'form-check-label'])?></div>
</div>
