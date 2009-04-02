<?php defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?php 
	foreach($page_list as $page){
		$user = UserInfo::getById($page->getCollectionUserId());
		Loader::element('blog_abstract', Array(
			'title'			=>$page->getCollectionName(),
			'by'			=>$user->uName,
			'description'	=>$page->getCollectionDescription(),
			'link_entry'	=>$navigation->getLinkToCollection($page),
			'date'			=>$page->getCollectionDatePublic($date_format),
			'link_comments'	=>$navigation->getLinkToCollection($page).'#ccm-blog-comments',
			'comment_count'	=>$page->guestbook_count,
			'guestbook_id'	=>$page->guestbook_id,
		)); 
		
	}
?>