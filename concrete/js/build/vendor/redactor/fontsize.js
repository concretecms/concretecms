if (!RedactorPlugins) var RedactorPlugins = {};

(function($)
{
	RedactorPlugins.fontsize = function()
	{
		return {
			init: function()
			{
				var fonts = [10, 11, 12, 14, 16, 18, 20, 24, 28, 30];
				var that = this;
				var dropdown = {};

				$.each(fonts, function(i, s)
				{
					dropdown['s' + i] = { title: s + 'px', func: function() { that.fontsize.set(s); } };
				});
				/* concrete5 */
				dropdown.remove = { title: this.lang.get('remove_font_size'), func: that.fontsize.reset };
				var button = this.button.add('fontsize', this.lang.get('change_font_size'));
				this.button.setAwesome('fontsize', 'fa fa-text-height');
				/* end concrete5 */
				this.button.addDropdown(button, dropdown);
			},
			set: function(size)
			{
				this.inline.format('span', 'style', 'font-size: ' + size + 'px;');
			},
			reset: function()
			{
				this.inline.removeStyleRule('font-size');
			}
		};
	};
})(jQuery);