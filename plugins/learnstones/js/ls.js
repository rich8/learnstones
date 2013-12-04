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
			$.ls.next();
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
}(jQuery));