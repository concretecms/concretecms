/** 
 * Much thanks to http://static.railstips.org/orderedlist
 */
 
(function($) {  
	var self = null;
 	var lutype = 'blocktypes';
 	
	$.fn.liveUpdate = function(list, type) {	
		return this.each(function() {
			new $.liveUpdate(this, list, type);
		});
	};
	
	$.liveUpdate = function (e, list, type) {
		this.field = $(e);
		this.list  = $('#' + list);
		this.lutype = 'blocktypes';
		if (typeof(type) != 'undefined') {
			this.lutype = type;
		}

		if (this.list.length > 0) {
			this.init();
		}
	};
	
	$.liveUpdate.prototype = {
		init: function() {
			var self = this;
			this.setupCache();
			this.field.parents('form').submit(function() { return false; });
			this.field.keyup(function() { self.filter(); });
			self.filter();
		},
		
		filter: function() {
			if ($.trim(this.field.val()) == '') { 
				if (this.lutype == 'blocktypes') {
					this.list.children('li').addClass('ccm-block-type-available'); 
					this.list.children('li').removeClass('ccm-block-type-selected'); 
				} else {
					this.list.children('li').show();
				}
				return; 
			}
			this.displayResults(this.getScores(this.field.val().toLowerCase()));
		},
		
		setupCache: function() {
			var self = this;
			this.cache = [];
			this.rows = [];
			var lutype = this.lutype;
			this.list.children('li').each(function() {
				if (lutype == 'blocktypes') {
					self.cache.push($(this).find('a.ccm-block-type-inner').html().toLowerCase());
				} else if (lutype == 'fileset') {
					self.cache.push($(this).find('label').html().toLowerCase());
				}
				self.rows.push($(this));
			});
			this.cache_length = this.cache.length;
		},
		
		displayResults: function(scores) {
			var self = this;
			if (this.lutype == 'blocktypes') {
				this.list.children('li').removeClass('ccm-block-type-available');
				this.list.children('li').removeClass('ccm-block-type-selected');
				$.each(scores, function(i, score) { self.rows[score[1]].addClass('ccm-block-type-available'); });
				$(this.list.find('li.ccm-block-type-available')[0]).addClass('ccm-block-type-selected');
			} else {
				this.list.children('li').hide();
				$.each(scores, function(i, score) { self.rows[score[1]].show(); });
			}
		},
		
		getScores: function(term) {
			var scores = [];
			for (var i=0; i < this.cache_length; i++) {
				var score = this.cache[i].score(term);
				if (score > 0) { scores.push([score, i]); }
			}
			return scores.sort(function(a, b) { return b[0] - a[0]; });
		}
	}
})(jQuery);