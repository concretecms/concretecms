<?php
$form = Loader::helper('form');
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Conversation Editor'), false, 'span8 offset2', false);
?>
<form action="<?=$view->action('save')?>" method='post'>
	<div class='ccm-pane-body'>
		<div class='clearfix'>
			<label>
				<input value=1 name='banned_list_enabled' <?=$bannedListEnabled?'checked ':''?>type='checkbox'>
				<?=t('Disallow posts that include banned words?')?>
			</label>
		</div>
		<script class='word_template' type="text/template" charset="utf-8">
			<tr class='editing'>
				<th class='id'></th>
				<td class='word'><span></span><input name='banned_word[]'></td>
				<td style='text-align:right'><a href='#' class='save_word btn'><?=t('Save')?></a></td>
			</tr>
		</script>
		<div class='banned_words_table' style='overflow:hidden'>
			<table class='banned_word_list table'>
				<thead>
					<tr>
						<th style='width:20px'>ID</th>
						<th>Word</th>
						<th style='width:50px;text-align:right'><a class='add_word' href='#'><i class='icon-plus'></i></a></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($bannedWords as $word) {
						if (!is_object($word)) continue;
						?>
						<tr>
							<th class='id'><?=$word->getID()?></th>
							<td class='word'><span><?=$word->getWord()?></span><input style='display:none' name='banned_word[]' value='<?=$word->getWord()?>'></td>
							<td style='text-align:right'><a href='#' class='delete_word'><i class='icon-trash'></i></a> <a href='#' class='edit_word'><i class='icon-edit'></i></a></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
	<div class='ccm-pane-footer'>
		<button class='btn btn-primary pull-right'><?=t('Save')?></button>
	</div>
</form>
<script>
var ctx = $('table.banned_word_list'), template = $('script.word_template'),
	getTemplate = function(){return $(template.text());},
	save = $("<a href='#' class='save_word btn'><?=t('Save')?></a>"),
	edit = $("<a href='#' class='delete_word'><i class='icon-trash'></i></a> <a href='#' class='edit_word'><i class='icon-edit'></i></a>"),
	totalheight = ctx.parent().height();

if (!$('input[name=banned_list_enabled]').get(0).checked) {
	ctx.hide();
	ctx.parent().height(0);
}
$('input[name=banned_list_enabled]').click(function(){
	if (this.checked) {
		ctx.fadeIn(200);
		ctx.parent().animate({height:totalheight},200,function(){
			$(this).height('auto');
		});
	} else {
		totalheight = ctx.parent().height();
		ctx.fadeOut(200);
		ctx.parent().animate({height:0},200);
	}
});
ctx.on('click','a.edit_word',function(e){
	var me = $(this);
	ctx.find('tr.editing').find('a.save_word').click();
	me.closest('tr').addClass('editing').find('td.word').children('span').hide().end()
		.children('input').show().end().end().end()
		.closest('td').empty().append(save.clone());

	e.preventDefault();
	e.stopPropagation();
	return false;
}).on('click','a.save_word',function(e){
	var me = $(this);
	var tr = me.closest('tr');
	tr.find('td.word').children('span').text(tr.find('td.word').children('input').val());
	tr.removeClass('editing').find('td.word').children('span').show().end()
		.children('input').hide().end().end().end()
		.closest('td').empty().append(edit.clone());

	e.preventDefault();
	e.stopPropagation();
	return false;
}).on('click','a.add_word',function(e){
	ctx.find('tr.editing').find('a.save_word').click();
	var newWord = getTemplate();
	newWord.find('th.id').text(' ');
	newWord.appendTo(ctx.find('tbody'));

	e.preventDefault();
	e.stopPropagation();
	return false;
}).on('click','a.delete_word',function(e){
	if (confirm("<?=t('Are you sure you want to delete this word?')?>"))
		$(this).closest('tr').remove();
});
</script>