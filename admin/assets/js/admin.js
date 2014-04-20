(function ( $ ) {
	"use strict";

	$(function () {

		// init date picker for changelog release date
		$('#release_date').datepicker({
            dateFormat: "dd.mm.yy",
            showButtonPanel: true
        });

		$('.cl_there_dig_version').mask("9.9.9");
	});

}(jQuery));