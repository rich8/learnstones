(function($) {
		LS_LEARNSTONE = "ls";
		LS_CLASSES = lsAjax.LS_STYLES.split(" ");
		ls = $.fn[LS_LEARNSTONE] = $[LS_LEARNSTONE] = function () {
			var $this = this;
			return $this;
		}
		  
		ls.mark = function( ls, response, post_id, nonce ) {
			$("span[data-menu=lsmenu" + ls + "]").each( function() { $(this).removeClass(lsAjax.LS_STYLES).addClass(LS_CLASSES[response]); });
			$.ajax({
				type : "post",
				dataType : "json",
				url : lsAjax.ajaxurl,
				data : {action: "ls_submission", type: "mark", post_id : post_id, learnstone: ls, response : response, nonce: nonce},
				success: function(response) {
					//alert(response.response);
         			}
			});
			$.colorbox.next();
		}

		ls.submission = function ( email, post_id, nonce ) {
			$.ajax({
				type : "post",
				dataType : "json",
				url : lsAjax.ajaxurl,
				data : {action: "ls_submission", type: "sub", email: email, post_id : post_id, nonce: nonce},
				success: function(response) {
					//alert(response.response);
         			}
			});
		}
		  
$(document)
.ready( function()
	{
		$("div.colorbox")
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