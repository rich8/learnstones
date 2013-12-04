(function($) {
	ls.getSelectedIndex = function( ) {
		return $.colorbox.getSelectedIndex();
	}

	ls.setSelectedIndex = function( index ) {
		$.colorbox.setSelectedIndex( index );
	}

	ls.next = function( ) {
		$.colorbox.next();
	}

$(document)
.ready( function()
	{
		$("div.slideshow")
		.each(function() { 
			$(this)
			.colorbox(
				{	html: function() { return "<div class='lsslidemenu'>" + $("#slides").find("div").eq(0).html() + "</div><div class='lsslidebody'>" + $(this).find("div").html() + $("#slides").find("div").eq(1).html() + "</div>"; },
					rel: "group_1", 
					transition:"none",
					width:"100%",
					height:"95%",
					className: "entry-content",
					scrolling: false,
					loop: false,
					title: $(this).find("div").find("h1").html()
				});
		});

		$("#slides div[data-menu^=lsmenu]").each( function() {
			$("#slides span[data-menu=" + $(this).attr("data-menu") + "item]").html($(this).find("h1").html());
		});

		var $gallery = $("div[rel=gallery]").colorbox();
		$("a#openGallery")
		.click( function(e) {
					e.preventDefault();
					$gallery.eq(0).click();
				});
		$gallery.eq(0).click();
});
}(jQuery));