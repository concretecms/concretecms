<?php defined('C5_EXECUTE') or die('Access denied.') ?>

<div style='margin-left:20px;background:#3B5998'>
	<h3 style='color:white;padding:0 10px 0'>
		facebook
		<button style='margin-top:5px' class='btn btn-primary pull-right authFacebookLogin'>
			<?=t('Login with Facebook')?>
		</button>
	</h3>
</div>
<script type="text/javascript">
	$('button.authFacebookLogin').click(function(){
		var login = window.open('<?=$loginUrl?>','Login with Facebook',"width=500,height=300");
		(login.focus && login.focus());

		function loginStatus() {
			if (login.closed) {
				window.location.href = '<?=$statusURI?>';
				return;
			}
			setTimeout(loginStatus, 500);
		}
		loginStatus();
		console.log(login);
		return false;
	});
</script>