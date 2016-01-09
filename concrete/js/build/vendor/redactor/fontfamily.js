if (!RedactorPlugins) var RedactorPlugins = {};

(function($)
{
	RedactorPlugins.fontfamily = function()
	{
		return {
			init: function ()
			{
				var fonts = [ 'Arial', 'Helvetica', 'Georgia', 'Times New Roman', 'Monospace' ];
				var that = this;
				var dropdown = {};

				$.each(fonts, function(i, s)
				{
					dropdown['s' + i] = { title: s, func: function() { that.fontfamily.set(s); }};
				});

				/* concrete5 */
				dropdown.remove = { title: this.lang.get('remove_font_family'), func: that.fontfamily.reset };
				var button = this.button.add('fontfamily', this.lang.get('change_font_family'));
				this.button.setAwesome('fontfamily', 'fa fa-font');
				/* end concrete5 */
				this.button.addDropdown(button, dropdown);

			},
			set: function (value)
			{
				this.inline.format('span', 'style', 'font-family:' + value + ';');
			},
			reset: function()
			{
				this.inline.removeStyleRule('font-family');
			}
		};
	};
})(jQuery);