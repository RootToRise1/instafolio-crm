var Page = {
	applying: false,
	imagesChanged: false,
	width: 0,
	resizingDelay: 50,
	cellMinWidth: 124,
	leftCellMinWidth: 230,


	changeWidgetItemCss: function (widgetStyles, selector, rules) {
		selector = selector.split(',');

		for (var i = 0; i < selector.length; i++) {
			var style = Css.getRule('.' + widgetStyles.class + ' ' + selector[i].trim(), true).style;

			for (var prop in rules) {
				if (rules.hasOwnProperty(prop)) {
					style[prop] = rules[prop];
				}
			}
		}
	},

	// left-column-t has fixed width 100%
	// x1 has fixed width leftCellMinWidth
	// all columns float

	setResponsiveCss: function (widgetStyles) {
		var page = this;

		page.changeWidgetItemCss(widgetStyles, '.x1', {
			'width': page.cellMinWidth.toString() + 'px',
			'text-align': 'left'
		});

		page.changeWidgetItemCss(widgetStyles, '.x2', {
			'width': (page.cellMinWidth * 2).toString() + 'px',
			'text-align': 'left'
		});

		page.changeWidgetItemCss(widgetStyles, '.x1 .table-header-cell, .x2 .table-header-cell', {
			'border-bottom': 'none',
			'padding-left': '0',
			'padding-right': '15px'
		});

		page.changeWidgetItemCss(widgetStyles, '.x2 .inner-row, .x1 .inner-row, .x2 .row-m, .x1 .row-m, .x2 .row-l, .x1 .row-l', {
			'padding-left': '0',
			'padding-right': '15px'
		});

		page.changeWidgetItemCss(widgetStyles, '.responsive-hidden', {
			'display': 'block'
		});

		page.changeWidgetItemCss(widgetStyles, '.left-column-t', {
			'width': '100%'
		});
	},

	// One row includes 5 x1 and left-column-t

	setFullCss: function (widgetStyles, columnWidth, leftColumnWidth) {
		var page = this;

		page.changeWidgetItemCss(widgetStyles, '.x1', {
			'width': columnWidth.toString() + 'px',
			'text-align': 'right'
		});

		page.changeWidgetItemCss(widgetStyles, '.x2', {
			'width': (columnWidth * 2).toString() + 'px',
			'text-align': 'right'
		});

		page.changeWidgetItemCss(widgetStyles, '.left-column-t', {
			'width': leftColumnWidth.toString() + 'px'
		});

		page.changeWidgetItemCss(widgetStyles, '.x1 .table-header-cell, .x2 .table-header-cell', {
			'border-bottom': '1px solid #e0e0e0',
			'padding-left': '15px',
			'padding-right': '0'
		});

		page.changeWidgetItemCss(widgetStyles, '.x2 .inner-row, .x1 .inner-row, .x2 .row-m, .x1 .row-m, .x2 .row-l, .x1 .row-l', {
			'padding-left': '15px',
			'padding-right': '0'
		});

		page.changeWidgetItemCss(widgetStyles, '.responsive-hidden', {
			'display': 'none'
		});

	},

	setCss: function (widget, widgetStyles) {
		var page = this;

		widget.addClass(widgetStyles.class);

		if (widgetStyles && widgetStyles.fullWidth) {
			if (((widgetStyles.minColumn * widgetStyles.parts) > widgetStyles.fullWidth) || page.width - widgetStyles.paddingWidth < (widgetStyles.minColumn * widgetStyles.parts)) {
				page.setResponsiveCss(widgetStyles);
			} else {
				if (page.width - widgetStyles.paddingWidth < widgetStyles.fullWidth) {
					var columnFlexWidth = (page.width - widgetStyles.paddingWidth) / widgetStyles.parts;
					page.setFullCss(widgetStyles, columnFlexWidth, widgetStyles.leftMinColumn);
				} else {
					page.setFullCss(widgetStyles, widgetStyles.columnWidth, widgetStyles.leftColumnWidth);
				}
			}
		}
	},

	changeImages: function () {
		var page = this;

		if (page.width < 700 && !page.imagesChanged) {
			$('.responsive-img').each(function () {
				$(this).attr('src', $(this).attr('src').split('.')[0] + "-s.png");
				$('.responsive-hr').css('display', 'block');
			});

			page.imagesChanged = true;
		} else if (page.width >= 700 && page.imagesChanged) {
			$('.responsive-img').each(function () {
				$(this).attr('src', $(this).attr('src').split('-s.')[0] + ".png");
				$('.responsive-hr').css('display', 'none');
			});

			page.imagesChanged = false;
		}
	},

	calculateStyles: function (item) {
		var page = this;

		var widgetStyles = {};

		if (item) {
			widgetStyles.fullRowWidth = 850;
			widgetStyles.fullWidth = 620;
			widgetStyles.paddingWidth = 310;
			widgetStyles.minColumn = page.cellMinWidth;
			widgetStyles.leftMinColumn = page.leftCellMinWidth;
			widgetStyles.x1 = $('.x1', item).length;
			widgetStyles.x2 = $('.x2', item).length;
			widgetStyles.parts = widgetStyles.x1 + widgetStyles.x2 * 2;

			//  k - multiplication factor for columns
			widgetStyles.k = widgetStyles.fullRowWidth / (widgetStyles.leftMinColumn + widgetStyles.minColumn * widgetStyles.parts);
			widgetStyles.columnWidth = Math.floor(widgetStyles.minColumn * widgetStyles.k);
			widgetStyles.leftColumnWidth = Math.floor(widgetStyles.leftMinColumn * widgetStyles.k);
			widgetStyles.context = item;
			widgetStyles.maxHeightRow = 0;
		}

		return widgetStyles;
	},

	resizeWidgets: function (widgets) {
		var start = (new Date()).getTime();
		var page = this;

		widgets.each(function (index) {
			var row = $(this).find('.group-columns')[0];
			var widgetStyles = page.calculateStyles(row);
			widgetStyles.class = 'widget_' + index;
			page.setCss($(this), widgetStyles);
		});

		page.changeImages();

		page.applying = false;
		console.log((new Date()).getTime() - start);
	}
};

var Css = {
	rules: null,
	customSheet: null,

	getRules: function () {
		var css = this;

		if (css.rules === null) {
			css.rules = {};
			var ds = document.styleSheets;
			var dsl = ds.length;

			for (var i = 0; i < dsl; i++) {
				var dsi = ds[i].cssRules;

				if (dsi) {
					var dsil = dsi.length;

					for (var j = 0; j < dsil; j++) css.rules[dsi[j].selectorText] = dsi[j];
				}
			}
		}

		return css.rules;
	},

	getRule: function (selector, createIfNotFound) {
		var css = this;

		css.getRules();

		if (css.rules.hasOwnProperty(selector)) {
			return css.rules[selector];
		} else {
			if (createIfNotFound) {
				if (css.customSheet === null) {
					css.customSheet = css.addSheet();
				}

				css.addRule(css.customSheet, selector, "", css.customSheet.cssRules.length);

				return css.rules[selector];
			} else {
				return null;
			}
		}
	},

	addRule: function (sheet, selector, rules, index) {
		var css = this;

		// FIXME: not affective method
		css.rules = null;

		if ("insertRule" in sheet) {
			sheet.insertRule(selector + "{" + rules + "}", index);
		} else if ("addRule" in sheet) {
			sheet.addRule(selector, rules, index);
		}

		css.getRules();
	},

	addSheet: function () {
		var style = document.createElement("style");

		// Add a media (and/or media query) here if you'd like!
		// style.setAttribute("media", "screen")
		// style.setAttribute("media", "only screen and (max-width : 1024px)")

		// WebKit hack :(
		style.appendChild(document.createTextNode(""));

		// Add the <style> element to the page
		document.head.appendChild(style);

		return style.sheet;
	}
};

$(function () {
	var widgets = $(".widget-content");

	Page.width = $(window).width();
	Page.resizeWidgets(widgets);

	$(window).on('resize', function () {
		var width = $(window).width();

		if (Page.width != width) {
			Page.width = width;
			Page.resizeWidgets(widgets);
		}
	});
});;
