ccmDiscussion = {

	validate: function() {
		return true;
	},
	
	reply: function(cID) {
		if (cID > 0) {
			$("input[@name=cDiscussionPostParentID]").val(cID);		
		}
		jQuery.facebox($('#discussion-post-reply-form').html());
	}
	
}
