<?php defined('C5_EXECUTE') or die('Access denied.'); ?>

<div class='form-group'>
    <?=$form->label('apikey', t('App ID'))?>
    <?=$form->text('apikey', $apikey)?>
</div>
<div class='form-group'>
    <?=$form->label('apisecret', t('App Secret'))?>
    <div class="input-group">
      <?=$form->password('apisecret', $apisecret)?>
      <span class="input-group-btn">
        <button id="showsecret" class="btn btn-warning" type="button"><?php echo t('Show secret')?></button>
      </span>
    </div>
</div>

<div class="alert alert-info">
    <?php echo t('<a href="%s" target="_blank">Click here</a> to obtain your access keys.', 'https://developers.facebook.com/apps/'); ?>
</div>

<script type="text/javascript">
  var button = $('#showsecret');
  button.click(function() {
    var apisecret = $('#apisecret');
    if(apisecret.attr('type') == 'password') {
      apisecret.attr('type', 'text');
      button.html('<?php echo addslashes(t('Hide secret key'))?>');
    } else {
      apisecret.attr('type', 'password');
      button.html('<?php echo addslashes(t('Show secret key'))?>');
    }
  });
</script>
