<?php defined('C5_EXECUTE') or die('Access denied.') ?>

<center>
	<button class='btn primary authFacebookLogin'>
		<?=$loggedin?t('Attach your facebook account to this concrete5 account'):t('Login with Facebook')?>
	</button>
</center>
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