/*!
 * remark (http://getbootstrapadmin.com/remark)
 * Copyright 2016 amazingsurge
 * Licensed under the Themeforest Standard Licenses
 */
$.components.register("switchery", {
	mode: "init",
	defaults: {
		color: '#b5b5ac'
	},
	init: function(context) {
		if (typeof Switchery === "undefined") return;

		var defaults = $.components.getDefaults("switchery");

		$('[data-plugin="switchery"]', context).each(function() {
			var options = $.extend({}, defaults, $(this).data());

			new Switchery(this, options);
		});
	}
});