<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<?php if(isset($forms) && count($forms) > 1){?>
<div class="btn-group">

<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <?php if(isset($currentForm)){
        echo $currentForm->getName();
    }?>
    <span class="caret"></span>
</button>

<ul class="dropdown-menu">
    <li class="dropdown-header"><?=t('Forms')?></li>
   <?php if(isset($forms)){
      foreach ($forms as $form){
          if($form->getId() <> $currentForm->getId()) {
            echo '<li><a href="' . $url . '/' . $form->getId() . '">' . $form->getName() . '</a></li>';
          }
      }
   }?>

</ul>

</div>
<?php }?>
