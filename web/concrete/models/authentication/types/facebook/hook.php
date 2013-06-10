<?php defined('C5_EXECUTE') or die('Access denied.')?>
<fieldset>
<?php
	try {
		$controller->getFacebookUserByUser($u->getUserID());
		$uinf = $controller->getFacebookUserInfo();
		$now = new DateTime();
		$bd = new DateTime($uinf['birthday']);
		$age = $now->diff($bd)->format('%y');
		?>
		<div class='facebookProfileDiv'>
			<div style='background:#3B5998'>
				<h3 style='color:white;padding:0 10px 0'>facebook</h3>
				<button class='btn detachFacebook pull-right'>Detach</button>
			</div>
			<div class='facebookUserInfo'>
				<a href='<?=$uinf['link']?>'><img style='float:left;margin-right:10px' src='<?=$controller->getUserImagePath($u)?>'></a>
				<h4><?=$uinf['name']?></h4>
				<?php
				if ($uinf['email']) {
					?>
					<p><strong>Email:</strong> <?=$uinf['email']?></p>
					<?php
				}
				if ($uinf['gender']) {
					?>
					<p><strong>Gender:</strong> <?=$uinf['gender']?></p>
					<?php
				}
				if ($age) {
					?>
					<p><strong>Age:</strong> <?=$age?></p>
					<?php
				} ?>
			</div>
		</div>
		<script type="text/javascript">
			(function($){
				"use Strict";
				$('button.detachFacebook').click(function(){
					$.post("<?=View::url('/account/profile/edit','callback','facebook','detachUser')?>",function(msg) {
						$('div.facebookProfileDiv > div.facebookUserInfo').empty().append($('<p/>').text('Successfully detached account.'));
						window.location.reload();
					});
					return false;
				})
			})(jQuery);
		</script>
		<?php
	} catch (Exception $e) {
		?>
		<div class='facebookProfileDiv'>
			<div style='background:#3B5998'>
				<h3 style='color:white;padding:0 10px 0'>facebook</h3>
			</div>
			<div class='facebookUserInfo'>
				<center>
					<button class='btn btn-primary authFacebookLogin'>
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
			</div>
		</div>
		<?php
	}
	?>
</fieldset>