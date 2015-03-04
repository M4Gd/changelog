(function ( $ ) {
	"use strict";

	$(function () {

		// init date picker for changelog release date
		$('#release_date').datepicker({
            dateFormat: "dd.mm.yy",
            showButtonPanel: true
        });

		$('#release_version').mask("9.9.99");
		$('#compatibility_version').mask("9.9.9");
	});

}(jQuery));
