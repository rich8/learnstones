(function ($) {
    ls.getSelectedIndex = function () {
        return $presentation.get_current_slide();
    }

    ls.setSelectedIndex = function (index) {
        $presentation.set_current_slide(index);
    }

    ls.next = function () {
        $presentation.next_slide();
    }

    ls.save = function () {

        var ret = [];
        $('#ls_frmpresentation input').each(function () {
            if ($(this).attr('name').indexOf('lsi_') == 0) {
                ret.push(this);
                $("input[name=" + $(this).attr('name') + "]").val($(this).val());
            }
        });
        return ret;

    }

    ls.populate = function () {
        //Need to copy learnstone menu div and content div into the slide portion
        $("div.ls_slideshow")
		    .each(function () {
		        $(this)
			    .ls_presentation(
				{
				    ele: function () {
				        var element = $("<form></form>");
				        element
                            .attr("method", "post")
                            .prop('id', 'ls_frmpresentation')
                            .append(
                                $("<div></div>")
                                    .append($("#ls_slides").find("div").eq(0).clone(true))
                            )
                            .append(
                                $("<div></div>")
                                    .append($(this).find("div").first().clone(true))
                            );
				        return element;
				    }
				});
		    });
    }
} (jQuery));