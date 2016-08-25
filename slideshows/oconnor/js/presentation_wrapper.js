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
        var checkboxes = new Object();
        $('#ls_frmpresentation :input').each(function () {
            var component = $(this);
            if (component.attr('name').indexOf('lsi_') === 0) {
                if (component.attr('type') === 'radio') {
                    if (component.prop('checked')) {
                        ret.push(this);
                        $("input[data-radio-id=" + component.attr('data-radio-id') + "]").prop('checked', true);
                    }
                }
                else if (component.attr('type') === 'checkbox') {
                    if (component.prop('checked')) {
                        var hidden;
                        if (component.attr('data-checkbox-id') in checkboxes) {
                            hidden = checkboxes[component.attr('data-checkbox-id')];
                        }
                        else {
                            hidden = document.createElement("input");
                            ret.push(hidden);
                            checkboxes[component.attr('data-checkbox-id')] = $(hidden);
                            hidden = $(hidden);
                            hidden.prop('name', component.attr('data-checkbox-id'));
                        }
                        $("input[name=" + component.attr('name') + "]").prop('checked', true);
                        if (hidden.val()) {
                            hidden.val(hidden.val() + "," + $(this).val());
                        }
                        else {
                            hidden.val($(this).val());
                        }
                    }
                }
                else {
                    console.log("NON RADIO:" + $(this).val());
                    ret.push(this);
                    $("input[name=" + component.attr('name') + "]").val($(this).val());
                }
            }
        });
        $('#ls_frmpresentation textarea').each(function () {
            if ($(this).attr('name').indexOf('lsi_') == 0) {
                ret.push(this);
                $("textarea[name=" + $(this).attr('name') + "]").html($(this).val());
            }
        });
        $('#ls_frmpresentation select').each(function () {
            if ($(this).attr('name').indexOf('lsi_') == 0) {
                ret.push(this);
                $("select[name=" + $(this).attr('name') + "]").val($(this).val());
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

				        // Fix because jQuery cannot clone selectedIndex
				        var toClone = $(this).find("div").first();
				        var cloned = toClone.clone(true);
				        toClone.find("select").each(function () {
				            var selectedValue = $(this).val();
				            cloned.find("select[name=" + this.name + "] option[value=" + selectedValue + "]").attr("selected", "selected");
				        });

				        element
                            .attr("method", "post")
                            .prop('id', 'ls_frmpresentation')
                            .append(
                                $("<div></div>")
                                    .append($("#ls_slides").find("div").eq(0).clone(true))
                            )
                            .append(
                                $("<div></div>")
                                    .append(cloned)
                            );
				        return element;
				    }
				});
		    });
    }
} (jQuery));