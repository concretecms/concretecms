	<h1><span><?=t('Allowed File Types')?></span></h1>
	<div class="ccm-dashboard-inner">
		<form method="post" id="file-access-extensions" action="<?=$this->url('/dashboard/files/access', 'file_access_extensions')?>">
			<?=$validation_token->output('file_access_extensions');?>
			<p>
			Only files with the following extensions will be allowed.
			Separate extensions with commas. Periods and spaces will be
			ignored. 
			</p>
			<?=$form->textarea('file-access-file-types',$file_access_file_types,array('rows'=>'2','cols'=>'40','style'=>'width:99%'));?>
			<?php		
				$b1 = $concrete_interface->submit(t('Save'), 'file-access-extensions');
				print $concrete_interface->buttons($b1);
			?>		
		</form>
	</div>