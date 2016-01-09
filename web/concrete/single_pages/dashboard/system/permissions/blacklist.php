<?php defined('C5_EXECUTE') or die("Access Denied.");
/* @var $h ConcreteDashboardHelper */
$h = Loader::helper('concrete/dashboard');?>
<form method="post" id="ipblacklist-form" action="<?php echo $view->action('update_ipblacklist')?>">
	<?php echo $this->controller->token->output('update_ipblacklist')?>
	<div class="ccm-pane-body">
			
			<legend><?php echo t('Smart IP Banning')?></legend>
			<div class="form-group form-inline">
				<?php echo $form->checkbox('ip_ban_lock_ip_enable', 1, $ip_ban_enable_lock_ip_after)?> <?php echo t('Lock IP after')?>
				
				<?php echo $form->text('ip_ban_lock_ip_attempts', $ip_ban_lock_ip_after_attempts, array('style'=>'width:60px'))?>
				<?php echo t('failed login attempts');?>		
				<?php echo t('in');?>		
				<?php echo $form->text('ip_ban_lock_ip_time', $ip_ban_lock_ip_after_time, array('style'=>'width:60px'))?>
				<?php echo t('seconds');?>				
			</div>	
			<div class="form-inline form-group">
				<?php echo $form->radio('ip_ban_lock_ip_how_long_type', $ip_ban_lock_ip_how_long_type_timed, $ip_ban_lock_ip_how_long_type)?> <?php echo t('Ban IP For')?>	
				<?php echo $form->text('ip_ban_lock_ip_how_long_min', $ip_ban_lock_ip_how_long_min, array('style'=>'width:60px'))?>
				<?php echo t('minutes');?>
				<?php echo $form->radio('ip_ban_lock_ip_how_long_type', $ip_ban_lock_ip_how_long_type_forever, $ip_ban_lock_ip_how_long_type)?> <?php echo t('Forever')?>					
			</div>
			<h4><?php echo t('Automatically Banned IP Addresses')?></h4>
			<table id="ip-blacklist" class="ccm-results-list table table-condensed table-striped" width="100%" cellspacing="1" cellpadding="0" border="0">
				<thead>
				<tr>
					<th><?php echo $form->checkbox('ip_ban_select_all',1,false)?> <?php echo t('IP')?></th>
					<th><?php echo t('Reason For Ban')?></th>
					<th><?php echo t('Expires In')?></th>
					<th> 
						<select name="ip_ban_change_to" id="ip_ban_change_to" class="form-control" style="display: inline-block; width: 50%;">
							<option value="<?php echo $ip_ban_change_makeperm?>"><?php echo t('Make Ban Permanent')?></option>
							<option value="<?php echo $ip_ban_change_remove?>"><?php echo t('Remove Ban')?></option>
						</select>
						<input type="button" value="<?php echo t('Go')?>" name="submit-ipblacklist" id="submit-ipblacklist" class="btn btn-default" />
					</th>
				</tr>
				</thead>
				<tbody>
				<?php  if (count($user_banned_limited_ips) == 0) {?>
				<tr>
					<td colspan="4"><?php  echo t('None')?></td>
				</tr>
				<?php  } else { ?>
					<?php  foreach ($user_banned_limited_ips as $user_banned_ip) { ?>
						<tr>
							<td><label><?php echo $form->checkbox('ip_ban_changes[]',$user_banned_ip->getUniqueID(),false)?> <?php echo $user_banned_ip->getIPRangeForDisplay()?></label></td>
							<td><?php echo $user_banned_ip->getReason()?></td>
							<td><?php echo ($this->controller->formatTimestampAsMinutesSeconds($user_banned_ip->expires))?></td>			
							<td>&nbsp;</td>
						</tr>		
					<?php  } ?>
				<?php  } ?>
				</tbody>
			</table>	
			<legend><?php echo t('Permanent IP Ban')?></legend>
			<p class="notes">
			<?php echo t('Enter IP addresses, one per line, in the form below to manually ban an IP address. To indicate a range, use a wildcard character (e.g. 192.168.15.* will block 192.168.15.1, 192.168.15.2, etc...)')?>			
			</p>					
			<textarea id="ip_ban_manual" name="ip_ban_manual" rows="10" style="width: 350px; margin-bottom: 10px;"><?php echo $user_banned_manual_ips?></textarea>
	</div>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">

		<?php 	
		print $interface->button_js(t('Save'), 'saveIpBlacklist()', 'right', 'btn-primary');
		?>
	    </div>
	</div>
</form>

<script type="text/javascript">

var saveIpBlacklist = function(){
	$("form#ipblacklist-form").get(0).submit();	
}

//jQuery block for non-submit form logic
$(document).ready(function(){
	var sParentSelector;
	sParentSelector = 'form#ipblacklist-form';	
	//delegate for any clicks to this form
	$(sParentSelector).bind('click', function(e){
		//clicks the parent IP checkbox
		if ( $(e.target).is('input#ip_ban_select_all') ) {
			allIPs(e.target);
		}
		else if( $(e.target).is('input#submit-ipblacklist') ) {
			saveIpBlacklist();
		}
	});	
	
	$(sParentSelector).bind('change', function(e){
		if ($(e.target).is('select')) {			
			//$('input[name=submit-ipblacklist]').attr('value',$(':selected',e.target).text());
		}
	});
	
	function allIPs(t){
		if(t.checked){
			$('form#ipblacklist-form table input').attr('checked',true);
		}
		else{
			$('form#ipblacklist-form table input').attr('checked',false);
		}	
	}
});
</script>