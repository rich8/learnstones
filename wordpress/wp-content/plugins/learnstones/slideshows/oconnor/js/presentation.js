function Presentation($)
{
	this.LS_PRESENTATION = "ls_presentation";
	this.components = [];
	this.current_slide = -1;
	this.num_slides = 0;
	this.id = 0;
	this.box = null;
	this.$ = $;

	this.is_first_slide = function () {
	    return this.components.length == 1;
	}

	this.add_slide = function (component, content) {
	    if (this.box == null) {
	        this.box = this.$('#ls_presentation_w');
	        this.box.hide();
	    }
	    this.components.push(this.$.extend({}, component, content));
	}

	this.get_current_slide = function() {
		return this.current_slide;
	}

    this.next_slide = function() {
        this.set_current_slide(this.get_current_slide() + 1);
    }

    this.previous_slide = function() {
        this.set_current_slide(this.get_current_slide() - 1);
    }

    this.set_current_slide = function (index) {
        if (this.current_slide != index) {
            if (index >= 0 && index < this.components.length) {
                this.current_slide = index;
            }
            if (this.box) {
                this.box.empty();
                this.box.append(this.components[this.current_slide].ele());
                this.box.css({ 'height': 'auto' });
                if (this.$(document).height() > this.box.height()) {
                    this.box.css({ 'height': this.$(document).height() });
                }
                this.$(document).scrollTop(0);
            }
        }
        this.box.show();
    }

    this.handle_keys = function (event) {
        // Skip events with modifier keys
        if (this.box.is(":hidden")) {
            return false;
        }
        else if (event.altKey || event.ctrlKey || event.metaKey || event.shiftKey) {
            return true;
        }

        var handled = true;
        switch (event.keyCode) {
            //case this.$.ui.keyCode.HOME:
            //    this.set_current_slide(0);
            //    break;
            //case this.$.ui.keyCode.END:  
            //    this.set_current_slide(this.components.length - 1);  
            //    break;  
            case this.$.ui.keyCode.ENTER:
                break;
            //case this.$.ui.keyCode.SPACE:  
            //case this.$.ui.keyCode.RIGHT:
            //    this.next_slide();
            //    break;
            //case this.$.ui.keyCode.LEFT:
            //    this.previous_slide();
            //    break;
            case this.$.ui.keyCode.ESCAPE:
                this.box.hide();
                break;
            default:
                handled = false;
                break;
        }

        if (handled) {
            // Squash propagation of key events so WebKit-based browsers
            // don't see two of everything.
            event.stopPropagation();
            event.preventDefault();
        }

        return !handled;
    }

}

(function($) {
 
	// Don't do anything if presentation already exists.
	if ($.presentation) {
		return;
	}

	$presentation = new Presentation($);

	presentation = $.fn[$presentation.LS_PRESENTATION] = $[$presentation.LS_PRESENTATION] = function(content) {

		if(content)
		{
			$presentation.add_slide(this, content);
		
            if($presentation.is_first_slide())
            {
                //$(document).on('click.' + $presentation.LS_PRESENTATION, '.' + $presentation.LS_PRESENTATION, function( event ) {
				//    if (!(event.which > 1 || event.shiftKey || event.altKey || event.metaKey || event.control)) {
				//	    $presentation.next_slide();
				//	    event.preventDefault();
				//    }	
			    //}
			    //);

                $(document).on('keydown.document','', function(event) { $presentation.handle_keys(event); });
           }

		}       
		return this;
	}
}(jQuery));
