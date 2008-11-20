ccmDiscussion = {

	canSubmit: true,
	totalAttachments: 0,
	
	submit: function(form) {
		if (!ccmDiscussion.canSubmit) {
			return false;
		}
		ccmDiscussion.showLoading();
		$("div#facebox div.ccm-error").html("");
	},
	
	response: function(data) {
		resp = eval("(" + data + ")");
		if (resp.errors) {
			ccmDiscussion.hideLoading();
			for(i = 0; i < resp.errors.length; i++) {
				$("div#facebox div.ccm-error").append(resp.errors[i] + '<br>');
			}
		} else if (resp.redirect) {
			ccmDiscussion.redirect(resp.redirect);
		}
	},
	
	redirect: function(redir) {
		window.location.href = redir;
	},
	
	reply: function(cID) {
		if (cID > 0) {
			$("input[@name=cDiscussionPostParentID]").val(cID);		
		}
		jQuery.facebox($('#discussion-post-reply-form').html());
		$("div#facebox input[@name=subject]").get(0).focus();
	},
	
	
	showLoading: function() {
		ccmDiscussion.canSubmit = false;
		$("div#facebox input[@name=post]").get(0).disabled = true;
		$("div#facebox div.discussion-post-loader").show();
	},
	
	addAttachment: function() {
		ccmDiscussion.totalAttachments++;
		$("div#facebox .discussion-add-attachment").html("Attach another file");
		var html = $("div#facebox div.discussion-attachments-selector").html();
		$("div#facebox div.discussion-attachments-wrapper").append(html);
	},
	
	removeAttachment: function(link) {
		ccmDiscussion.totalAttachments--;
		var p = $(link).parent();
		p.remove();	
		
		if (ccmDiscussion.totalAttachments == 0) {
			$("div#facebox .discussion-add-attachment").html("Attach a file");
		}
	},
	
	hideLoading: function() {
		ccmDiscussion.canSubmit = true;
		$("div#facebox input[@name=post]").get(0).disabled = false;
		$("div#facebox div.discussion-post-loader").hide();
	}


	
}
