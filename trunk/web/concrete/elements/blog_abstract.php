<div class="ccm-blog-abstract">
	<h2><a href="<?=$link_entry?>" rel="bookmark"><?=$title?></a></h2>
	<div>
		<h3>
			by <?=$by?>
		</h3>
		<?php if($description) { ?>
			<p>
				<?=$description?>
				<br />
				<a href="<?=$link_entry?>">
					Read the rest of this entry &raquo;</a>
			</p>
		<?php } ?>
	</div>
	<div>
		<div>
			<p>
				<?=$date?>
			</p>
			<?php if($guestbook_id){ ?>
				<p>
					<a href="<?=$link_comments?>">
						<?=$comment_count?> comments
					</a>
					<span>
					</span>
				</p>
			<?php } ?>
		</div>
		<div>
			<p>
				Posted in ______________
			</p>
			<p>
				Tagged with 
				<a rel="tag">
					_____________</a>
			</p>
		</div>
	</div>
</div>