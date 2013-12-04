(function($) {
	ls.getSelectedIndex = function( ) {
		return Presentation.get_current_slide();
	}

	ls.setSelectedIndex = function( index ) {
		Presentation.set_current_slide( index );
	}

	ls.next = function( ) {
		Presentation.next_slide();
	}

$(document)
.ready( function()
	{

		//Need to copy learnstone menu div and content div into the slide portion

		$("#slides div[data-menu^=lsmenu]").each( function() {
			$("#slides span[data-menu=" + $(this).attr("data-menu") + "item]").html($(this).find("h1").html());
		});

		var $gallery = Presentation;
		$("a#openGallery")
		.click( function(e) {
					e.preventDefault();
					$gallery.start();
				});
		$gallery.start();
});
}(jQuery));