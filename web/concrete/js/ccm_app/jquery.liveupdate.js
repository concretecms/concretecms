/** 
 * Much thanks to http://static.railstips.org/orderedlist
 */
 
(function($) {  
	var self = null;
 	var lutype = 'blocktypes';
 	var searchValue = null;
 	
	$.fn.liveUpdate = function(list, type) {	
		return this.each(function() {
			new $.liveUpdate(this, list, type);
		});
	};
	
	$.liveUpdate = function (e, list, type) {
		this.field = $(e);
		$(e).data('liveUpdate', this);
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
			if (this.field.val() != searchValue) {
				if ($.trim(this.field.val()) == '') { 
					if (this.lutype == 'blocktypes') {
						this.list.children('li').addClass('ccm-block-type-available'); 
						this.list.children('li').removeClass('ccm-block-type-selected'); 
					} else if (this.lutype == 'attributes') {
						this.list.children('li').addClass('ccm-attribute-available'); 
						this.list.children('li').removeClass('ccm-attribute-selected'); 
					} else if (this.lutype == 'stacks') {
						this.list.children('li').addClass('ccm-stack-available'); 
						this.list.children('li').removeClass('ccm-stack-selected'); 
					} else if (this.lutype == 'intelligent-search') {
						if (this.list.is(':visible')) {
							this.list.hide();
						}
					} else {
						this.list.children('li').show();
					}
					return; 
				}
				if (this.lutype != 'intelligent-search' || this.field.val().length > 2) {
					this.displayResults(this.getScores(this.field.val().toLowerCase()));
				} else if (this.lutype  == 'intelligent-search') {
					if (this.list.is(':visible')) {
						this.list.hide();
					}
				}
			}
			searchValue = this.field.val();
			if (searchValue == '' && this.lutype  == 'intelligent-search') {
				if (this.list.is(':visible')) {
					this.list.hide();
				}
			}

		},
		
		setupCache: function() {
			var self = this;
			this.cache = [];
			this.rows = [];
			var lutype = this.lutype;
			this.list.find('li').each(function() {
				if (lutype == 'blocktypes') {
					self.cache.push($(this).find('a.ccm-block-type-inner').html().toLowerCase());
				} else if (lutype == 'attributes') {
					var val = $(this).find('a,span').html().toLowerCase();
					self.cache.push(val);
				} else if (lutype == 'stacks') {
					var val = $(this).find('a,span').html().toLowerCase();
					self.cache.push(val);
				} else if (lutype == 'fileset') {
					self.cache.push($(this).find('label').html().toLowerCase());
				} else if (lutype == 'intelligent-search') {
					var s = $(this).find('span').html();
					if (s) {
						self.cache.push(s.toLowerCase());
					}
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
			} else if (this.lutype == 'attributes') {
				this.list.children('li').removeClass('ccm-attribute-available');
				this.list.children('li').removeClass('ccm-attribute-selected');
				this.list.children('li').removeClass('ccm-item-selected');
				$.each(scores, function(i, score) { self.rows[score[1]].addClass('ccm-attribute-available'); });
				this.list.children('li.item-select-list-header').removeClass("ccm-attribute-available");
				$(this.list.find('li.ccm-attribute-available')[0]).addClass('ccm-item-selected');

			} else if (this.lutype == 'stacks') {
				this.list.children('li').removeClass('ccm-stack-available');
				this.list.children('li').removeClass('ccm-stack-selected');
				this.list.children('li').removeClass('ccm-item-selected');
				$.each(scores, function(i, score) { self.rows[score[1]].addClass('ccm-stack-available'); });
				this.list.children('li.item-select-list-header').removeClass("ccm-stack-available");
				$(this.list.find('li.ccm-stack-available')[0]).addClass('ccm-item-selected');
			} else if (this.lutype == 'intelligent-search') {
				if (!this.list.is(':visible')) {
					this.list.fadeIn(160, 'easeOutExpo');
				}
				this.list.find('.ccm-intelligent-search-results-module-onsite').hide();
				this.list.find('li').hide();
				var shown = 0;
				$.each(scores, function(i, score) { 
					$li = self.rows[score[1]];
					if (score[0] > 0.75) {
						shown++;
						if (!$li.parent().parent().is(':visible')) {
							$li.parent().parent().show();
						}
						$li.show();
					}
				});
				this.list.find('li a').removeClass('ccm-intelligent-search-result-selected');
				this.list.find('li:visible a:first').addClass('ccm-intelligent-search-result-selected');
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