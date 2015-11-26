(function ($) {

    /*
    * Entities that need to be escaped in HTML
    */
    var ENTITY_MAP = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": '&#39;',
        "/": '&#x2F;'
    };

    /*
    * Classes that provide the CSS for the responses
    */
    LS_CLASSES = lsAjax.LS_STYLES.split(" ");

    /**
    * Merges anonymous and user sessions
    */
    ls.merge = function () {

        // Get all the selected sessions
        var chks = $('#ls_dashboard :checked');
        var merges = [];

        // Check if we have enough selected
        if (chks.length <= 1) {
            alert('Select more than 1 to merge');
        } else {

            // Now check if at most one user is selected
            var user = false;
            for (var chk = 0; chk < chks.length; chk++) {
                if (chks[chk].id.indexOf('lsc_user') == 0) {
                    if (user) {
                        alert('Multiple users selected');
                        return;
                    }
                    user = true;
                }
                merges.push(chks[chk].id.substring(4));
            }

            // If user confirms they are happy to merge, send off ajax request
            if (window
					.confirm("Are you sure you want to merge selected session?")) {
                $
						.ajax({
						    type: "post",
						    dataType: "json",
						    url: lsAjax.ajaxurl,
						    data: {
						        action: "ls_submission",
						        type: "merge",
						        nonce: lsAjax.nonce,
						        post_id: lsAjax.post_id,
						        merges: merges
						    },
						    success: function (response) {

						        // If ajax response ok, then update 
						        if (response.response === 'ok') {
						            for (var update in response.session) {
						                var id = response.session[update][0];
						                var name = ls
												.escape_html(response.session[update][1]);
						                for (var chk = 0; chk < merges.length; chk++) {
						                    if (merges[chk] != update) {
						                        $('#' + merges[chk]).remove();

						                    } else {
						                        $('#lsc_' + update).prop(
														'checked', false);
						                    }
						                }
						                break;
						            }
						        }
						        ls.input_responses(response, false);
						    },
						    error: function (a, b, c) {
						        alert(a.responseText + "," + b + "," + c);
						    }
						});
            }
        }
    }

    /**
    * @param {Boolean}
    *            purge - true if action is purge, false to just remove
    */
    ls.remove = function (purge) {
        var action = "remove";
        if (purge) {
            action = "purge";
        }
        var chks = $('#ls_dashboard :checked');
        if (chks.length == 0) {
            alert('No sessions or users selected');

            // If user wants to do action, send ajax response
        } else if (window.confirm('Are you sure you want to ' + action + ' '
				+ chks.length + ' users/sessions?')) {
            var removes = [];
            for (var chk = 0; chk < chks.length; chk++) {
                removes.push(chks[chk].id.substring(4));
            }
            $.ajax({
                type: "post",
                dataType: "json",
                url: lsAjax.ajaxurl,
                data: {
                    action: "ls_submission",
                    type: action,
                    post_id: lsAjax.post_id,
                    from: $('#ls_dashboard_time').val(),
                    classid: $('#ls_filter option:selected').val(),
                    nonce: lsAjax.nonce,
                    removes: removes
                },
                success: function (response) {

                    // If ajax ok, then update dashboard
                    if (response.response == 'ok') {
                        ls.input_responses(response, false);
                        for (var chk = 0; chk < removes.length; chk++) {
                            var chkb = $('#lsc_' + removes[chk]);
                            if (chkb.length) {
                                chkb.prop('checked', false);
                            }
                        }
                        ls.db_filter(1);
                    } else {
                        alert(response.response);
                    }
                },
                error: function (a, b, c) {
                    alert("error:" + a.responseText + "," + b + "," + c);
                }
            });
        }
    }

    /**
    * Populates cell with a given time or length of time if earlier than
    * yesterday
    * 
    * @param {Object}
    *            cell - object or name of table cell to update
    * @param {String}
    *            time - time (or --) to display in cell
    */
    ls.set_time = function (cell, time) {
        var fmt = time;
        if (time == "--") {
            // No input time known as event occurred after last input
            return;
        } else if (time != "-") {
            var date1 = new Date(time.substr(0, 10));
            var date2 = new Date();
            if (date1.getDay() === date2.getDay()
					&& date1.getMonth() === date2.getMonth()
					&& date1.getYear() === date2.getYear()) {
                fmt = time.substr(11);
            } else {
                fmt = Math.floor((date2 - date1) / 1000 / 60 / 60 / 24)
						+ " days";
            }
        }
        var c = cell;
        if (typeof (cell) === 'string') {
            c = $('#' + cell + " .ls_db_time");
        }
        c.data('time', time).html(fmt);
    }

    /**
    * Mark a learnstone with a given response
    */
    ls.mark = function (learnstone, response) {
        var vals = $.ls.save();
        var obj = new Object();
        for (var i = 0; i < vals.length; i++) {
            if (response >= 0 || $(vals[i]).val().length > 0) {
                obj[$(vals[i]).attr('name')] = $(vals[i]).val();
            }
        }
        if (response >= 0) {
            $("input[data-menu=ls_menu" + learnstone + "]").each(
					function () {
					    $(this).removeClass(lsAjax.LS_STYLES).addClass(
								LS_CLASSES[response]);
					});
        }
        if (response >= 0 || vals.length > 0) {
            $
					.ajax({
					    type: "post",
					    dataType: "json",
					    url: lsAjax.ajaxurl,
					    data: {
					        action: "ls_submission",
					        type: "mark",
					        post_id: lsAjax.post_id,
					        learnstone: lsAjax.lss[learnstone],
					        response: response,
					        nonce: lsAjax.nonce,
					        inputs: obj
					    },
					    success: function (response) {
					        if (response.response != 'ok') {
					            alert("Your session has been invalidated.  You will be redirected to the home page");
					            location.reload();
					        }
					    },
					    error: function (a, b, c) {
					        alert("error:" + a.responseText + "," + b + "," + c);
					    }
					});
        }
        if (response >= 0) {
            ls.set_selected_index(-1);
        }
    }

    /**
    * 
    */
    ls.set_selected_index = function (index) {
        $('input[data-menu=ls_menu' + ls.getSelectedIndex() + ']').each(
				function () {
				    $(this).removeClass('ls_menu_active');
				});
        if (index == -1) {
            ls.next();
            index = ls.getSelectedIndex();
        } else {
            ls.setSelectedIndex(index);
        }
        $('input[data-menu=ls_menu' + index + ']').each(function () {
            $(this).addClass('ls_menu_active');
        });
        $('input[name=ls_stone]').val(lsAjax.lss[index]);
    }

    /**
    * 
    */
    ls.db_toggle_col = function (obj, tag, col) {
        var td2 = obj.find(tag).eq(col);
        var exit = false;
        while (!exit) {
            if (td2.hasClass('ls_db_input_hide')) {
                td2.removeClass("ls_db_input_hide")
						.addClass("ls_db_input_show");
            } else if (td2.hasClass('ls_db_input_show')) {
                td2.removeClass("ls_db_input_show")
						.addClass("ls_db_input_hide");
            } else {
                exit = true;
            }
            td2 = td2.next(tag);
        }
    }

    /**
    * 
    */
    ls.db_toggle_input = function (anchor) {
        var td = $(anchor.parent().parent());
        var col = td.parent().children().index(td) + 1;
        $("#ls_dashboard thead").each(function () {
            ls.db_toggle_col($(this), "th", col);
        });
        $("#ls_dashboard tr").each(function () {
            ls.db_toggle_col($(this), "td", col);
        });
    }

    /**
    * 
    */
    ls.escape_html = function (input) {
        return String(input).replace(/[&<>"'\/]/g, function (s) {
            return ENTITY_MAP[s];
        });
    }

    ls.special_escape_html = function (input) {
        var ele = $('<div />').append($(input));
        ele.children().each(function () {
            if (this.tagName !== "PRE") {
                $(this).html(ls.escape_html($(this).html()));
            }
        });
         return ele.html();
    }

    /**
    * 
    */
    ls.db_input_update = function (td, selected, updatelink) {
        var opt = selected.find('option:selected');
        var op = opt.data('format');
        var url = opt.data('url');
        var txt = td.data('input');
        if (txt && txt.length != 0) {
            op = op.replace("%input%", txt);
            if (url == 1) {
                var qm = op.indexOf('?');
                if (qm == -1) {
                    op = encodeURI(op);
                } else if (qm == 0) {
                    op = "?" + encodeURIComponent(op.substring(qm + 1));
                } else {
                    op = encodeURI(op.substring(0, qm - 1)) + "?"
							+ encodeURIComponent(op.substring(qm + 1));
                }
                txt = "<a href='" + op + "'>" + ls.escape_html(txt) + "</a>";
            } else {
                txt = ls.special_escape_html(op);
            }
        } else if (txt) {
            txt = ls.special_escape_html(txt);
        }
        td.html(txt);

        if (updatelink) {
            var full = 1;
            if (td.data('input').length == 0) {
                full = 0;
            } else {
                var tdtest = td.next("td");
                while ((tdtest.hasClass('ls_db_input_show') || tdtest
						.hasClass('ls_db_input_hide'))
						&& full == 1) {
                    if (tdtest.data('input').length == 0) {
                        full = 0;
                    } else {
                        tdtest = tdtest.next("td");
                    }
                }
                if (full == 1) {
                    var tdtest = td.prev("td");
                    while ((tdtest.hasClass('ls_db_input_show') || tdtest
							.hasClass('ls_db_input_hide'))
							&& full == 1) {
                        if (tdtest.data('input').length == 0) {
                            full = 0;
                        } else {
                            tdtest = tdtest.prev("td");
                        }
                    }
                }
            }

            var tdtest2 = td.prev("td");
            while (tdtest2.hasClass('ls_db_input_show')
					|| tdtest2.hasClass('ls_db_input_hide')) {
                tdtest2 = tdtest2.prev("td");
            }
            if (full == 1) {
                tdtest2.find("a").removeClass('ls_db_input_a_empty').addClass(
						'ls_db_input_a_full');
            } else {
                tdtest2.find("a").removeClass('ls_db_input_a_full').addClass(
						'ls_db_input_a_empty');
            }
        }
    }

    /**
    * 
    */
    ls.db_format = function (selected) {
        var th = $("#ls_dashboard th[data-input=" + selected.attr('name') + "]");
        var col = th.parent().children().index(th);
        $("#ls_dashboard tr").each(function () {
            ls.db_input_update($(this).find("td").eq(col), selected, false);
        });
        $("#ls_stream tr").each(function () {
            if ($(this).data('iname') == selected.attr('name')) {
                ls.db_input_update($(this).find("td").eq(3), selected, false);
            }
        });
        $.ajax({
            type: "post",
            dataType: "json",
            url: lsAjax.ajaxurl,
            data: {
                action: "ls_submission",
                type: "format",
                field: selected.attr('name'),
                format: selected.val(),
                post_id: lsAjax.post_id,
                nonce: lsAjax.nonce
            },
            success: function (response) {
                // alert(response.response);
            }
        });
        $('pre code').each(function (i, block) {
            hljs.highlightBlock(block);
        });
    }

    /**
    * 
    */
    ls.input_responses = function (response, updateStream) {
        var filter = 0;
        if (response.response == "ok") {
            // $('#debug').text(count + ":" + response.latest);
            //count++;
            if (response.session) {
                var bef = '.ls_db_name';
                if ($('#ls_dashboard ' + bef).length == 0) {
                    bef = '.ls_db_none';
                }
                for (var update in response.session) {
                    var id = response.session[update][0];
                    var classes = response.session[update][2];
                    if (id == 'deleted') {
                        $('#' + update).remove();
                        $('#ls_stream tr').each(function () {
                            if ($(this).data('user') == update) {
                                $(this).remove();
                            }
                        });
                        filter = 1;
                    } else {
                        var name = response.session[update][1];
                        var classes = response.session[update][2];
                        var time = response.session[update][3];
                        var add = false;
                        var nameChange = false;

                        if (name.length > 0) {
                            var nameTd = $('#' + update + " .ls_db_name");
                            if (nameTd.length) {
                                if (nameTd.text() != name) {
                                    nameChange = true;
                                }
                            }

                            nameTd = $('#' + id + " .ls_db_name");
                            if (nameTd.length) {
                                if (nameTd.text() != name) {
                                    nameChange = true;
                                }
                            }
                        }

                        // alert(id + "," + name + ",to:" + update);
                        // User already listed
                        if ($('#' + update).length > 0) {
                            if (update != id) {
                                $('#' + id).remove();
                                if (classes) {
                                    $('#' + update).data('class', classes);
                                    if (update.indexOf('user') == 0) {
                                        $('#' + update).find('td').eq(0)
												.removeClass().addClass(
														'ls_db_user');
                                    }
                                    filter = 1;
                                }
                                ls.set_time(update, time);
                            } else {
                                filter = 1;
                                ls.set_time(id, time);
                                if (classes
										&& ($('#' + update).data('class') != classes)) {
                                    $('#' + update).data('class', classes);
                                }
                                if (name.length > 0) {
                                    var row = $('#' + id);
                                    row.insertBefore($('#ls_dashboard ' + bef)
											.eq(0).parent());
                                    $('#ls_dashboard .ls_db_name').each(
											function () {
											    if ($(this).text()
														.toLowerCase() < name
														.toLowerCase()) {
											        row.insertAfter($(this)
															.parent());
											    }
											});
                                }
                            }
                        }
                        // New user
                        else if (update == id) {
                            add = true;
                        }
                        // Newly logged in user previously listed
                        else if ($('#' + id).length > 0) {
                            ls.set_time(id, time);
                            $('#' + id).prop('id', update);
                            $('#lsc_' + id).prop('id', 'lsc_' + update);
                            $('#' + id).data('class', classes);
                            $('#ls_dashboard th').each(function () {
                                var lsid = $(this).data('ls');
                                $("#" + id + lsid).prop('id', update + lsid);
                            });
                        } else {
                            add = true;
                        }

                        if (nameChange) {
                            $('#' + id + " .ls_db_name").html(
									ls.escape_html(name));
                            $('#ls_stream tr').each(
									function () {
									    if ($(this).data('user') == id) {
									        $(this).data('user', update);
									        $(this).find('td').eq(1).html(
													ls.escape_html(name));
									    }
									});
                        }

                        if (add) {
                            var row = $("<tr id='" + update
									+ "' class='ls_db_show' data-class='"
									+ classes + "'></tr>");
                            var cl = '';
                            if (update.indexOf('session') == 0) {
                                cl = ' class="ls_db_session" ';
                            }
                            row.append($('<td ' + cl
									+ '><input type="checkbox" name="lsc_'
									+ update + '" id="lsc_' + update
									+ '"/></td>'));
                            row.append($("<td class='ls_db_name'>" + name
									+ "</td>"));
                            var tcell = $("<td class='ls_db_time'></td>");
                            ls.set_time(tcell, time);
                            row.append(tcell);
                            var ind = 0;
                            $('#ls_dashboard th')
									.each(
											function () {
											    if (ind >= 3) {
											        var cell = $("<td></td>");
											        if ($(this).data('input')) {
											            cell.data('input', '');
											            if ($(this)
																.hasClass(
																		'ls_db_input_hide')) {
											                cell
																	.addClass('ls_db_input_hide');
											            } else {
											                cell
																	.addClass('ls_db_input_show');
											            }
											            row.append(cell);
											        } else {
											            var link = $("<span>&nbsp;</span>");
											            if ($(this).data(
																'hasinput')) {
											                link = $(
																	"<a class='ls_db_input_a ls_db_input_a_empty'>&#133;</a>")
																	.on(
																			'click',
																			function (
																					e) {
																			    ls
																						.db_toggle_input($(this));
																			});
											            }

											            var sp = $(
																"<span class='ls_respspan ls_resp0'></span>")
																.prop(
																		'id',
																		update
																				+ $(
																						this)
																						.data(
																								'ls'))
																.append(link);
											            row.append(cell
																.append(sp));
											        }
											    }
											    ind++;
											});
                            row.insertBefore($('#ls_dashboard ' + bef).eq(0)
									.parent());
                            $('#ls_dashboard .ls_db_name').each(
									function () {
									    if ($(this).text().toLowerCase() < name
												.toLowerCase()) {
									        row.insertAfter($(this).parent());
									    }
									});
                            filter = 1;
                        }
                    }
                }
            }
            for (var update in response.updates) {
                for (var resp in response.updates[update]) {
                    $("#" + update + resp).removeClass().addClass(
							'ls_respspan ls_resp'
									+ response.updates[update][resp]);
                }
            }
            if (response.inputs.length > 0 && filter == 0) {
                filter = 2;
            }

            for (var i = 0; i < response.inputs.length; i++) {
                var n = response.inputs[i][1];
                var time = response.inputs[i][3];
                var th = $("#ls_dashboard th[data-input=lsf_" + n + "]");
                var col = th.parent().children().index(th);
                var td = $('#' + response.inputs[i][0]).find("td").eq(col);
                td.data('input', response.inputs[i][2]);
                ls.db_input_update(td, $('select[name=lsf_' + n + ']'), true);

                if (updateStream) {
                    var add = true;
                    $('#ls_stream tr')
							.each(
									function () {
									    if ($(this).data('user') == response.inputs[i][0]
												&& $(this).data('iname') == 'lsf_'
														+ n) {
									        add = ($(this).find('td').data(
													'time') != time);
									    }
									});
                    if (add) {
                        var tr = $('<tr></tr>');
                        tr.data('user', response.inputs[i][0]).data('iname',
								'lsf_' + n);
                        var tcell = $('<td></td>');
                        ls.set_time(tcell, time);
                        tr.append(tcell);
                        var n2 = $(
								'#' + response.inputs[i][0] + " td.ls_db_name")
								.html();
                        tr.append($('<td>' + n2 + '</td>'));
                        tr.append($('<td>' + n + '</td>'));
                        td = $('<td>Value</td>');
                        td.data('input', response.inputs[i][2]);
                        ls.db_input_update(td, $('select[name=lsf_' + n + ']'),
								true);
                        tr.append(td);
                        tr.insertAfter($('#ls_stream tr').eq(0));
                    }
                }
            }
            if (filter != 0) {
                ls.db_filter(filter);
            }
            $('#ls_dashboard_time').val(response.latest);
        } else {
            alert("Session timed out, please re-logon");
        }
    }

    /**
    * 
    */
    ls.submission = function (email) {
        $.ajax({
            type: "post",
            dataType: "json",
            url: lsAjax.ajaxurl,
            data: {
                action: "ls_submission",
                type: "sub",
                email: email,
                post_id: lsAjax.post_id,
                nonce: nonce
            },
            success: function (response) {
                // alert(response.response);
            }
        });
    }

    /**
    * 
    */
    ls.login = function () {
        $.ajax({
            type: "post",
            dataType: "json",
            url: lsAjax.ajaxurl,
            data: {
                action: "ls_submission",
                type: "login",
                post_id: lsAjax.post_id,
                username: $('.ls_username').eq(0).val(),
                password: $('.ls_password').eq(0).val()
            },
            success: function (response) {
                if (response.loggedin) {
                    lsAjax.login_warn = 0;
                    $(".ls_loginphide").removeClass("ls_loginpshow").addClass(
							"ls_loginphide");
                    lsAjax.nonce = response.nonce;
                    var d = document.createElement('div');
                    d.innerHTML = response.logouturl;
                    $("span.ls_loginname").text(response.name);
                    $("a.ls_logouturl").attr('href', d.firstChild.nodeValue);
                    d.innerHTML = response.dburl;
                    $("a.ls_dbouturl").attr('href', d.firstChild.nodeValue);
                    var ele = $(".ls_loginmenushow");
                    var ele2 = $(".ls_loginmenuhide");
                    ele.removeClass("ls_loginmenushow").addClass(
							"ls_loginmenuhide");
                    ele2.removeClass("ls_loginmenuhide").addClass(
							"ls_loginmenushow");
                    var ls;
                    for (ls = 0; ls < lsAjax.lss.length; ls++) {
                        var resp = response.lss['ls_' + lsAjax.lss[ls]];
                        if (resp) {
                            $("input[data-menu=ls_menu" + ls + "]")
									.removeClass(lsAjax.LS_STYLES).addClass(
											resp);
                        }
                    }
                    for (var i in response.lss.inputs) {
                        $("input[name=" + i + "]").val(response.lss.inputs[i]);
                    }
                } else {
                    alert(response.message);
                }
            }
        });
    }

    /**
    * 
    */
    ls.get_selected = function () {
        var radios = $('input[name=ls_selection]');
        return radios.index(radios.filter(':checked'));
    }

    /**
    * 
    */
    ls.move_up = function () {
        var selected = ls.get_selected();
        if (selected > 0) {
            $('#ls_content ul li').eq(selected - 1).before(
					$('#ls_content ul li').eq(selected));
        }
    }

    /**
    * 
    */
    ls.move_down = function () {
        var selected = ls.get_selected();
        if (selected < $('input[name=ls_selection]').length - 1) {
            $('#ls_content ul li').eq(selected + 1).after(
					$('#ls_content ul li').eq(selected));
        }
    }

    /**
    * 
    */
    ls.delete_item = function () {
        var selected = ls.get_selected();
        if (selected >= 0) {
            $('#ls_content ul li').eq(selected).remove();
            if ($('input[name=ls_selection]').length == selected) {
                selected--;
            }
            $('input[name=ls_selection]').eq(selected).prop('checked', true);
        }
    }

    /**
    * 
    */
    ls.switch_mode = function () {
    }

    /**
    * 
    */
    ls.add_handlers = function () {
        $('#ls_results input[data-item]')
				.click(
						function (e) {
						    e.preventDefault();
						    var selected = ls.get_selected();
						    var id = $(this).data("item");
						    var title = $('span[data-item=' + id + ']').html();
						    var li = "<li class='ls_course_item_edit'><input name='ls_item[]' type='hidden' value='"
									+ id
									+ "' /><input name='ls_selection' type='radio' value='"
									+ id
									+ "' checked='checked'/>"
									+ title
									+ "</li>";
						    if (selected == -1) {
						        $('#ls_content ul').append(li);
						    } else if ($('#ls_insert_mode').prop('checked')) {
						        $('#ls_content ul li').eq(selected).after(li);
						    } else {
						        $('#ls_content ul li').eq(selected).before(li);
						    }
						});
        $('#ls_more').click(function (e) {
            e.preventDefault();
            $('#ls_limit').val($('#ls_limit').val() + lsAjax.ls_page);
            ls.search();
        });
    }

    /**
    * 
    */
    ls.search = function () {
        $.ajax({
            type: "post",
            dataType: "html",
            url: lsAjax.ajaxurl,
            data: {
                action: "ls_submission",
                type: "search",
                ls_title: $('#ls_title').val(),
                ls_limit: $('#ls_limit').val(),
                tag_id: $('#tag_id').val(),
                author: $('#author').val(),
                subject: $('#subject').val(),
                orderby: $('#orderby').val(),
                post_id: lsAjax.post_id,
                nonce: lsAjax.nonce,
                ls_search_in: $('#ls_search_in').val()
            },
            success: function (response) {
                $('#ls_results').html(response);
                ls.add_handlers();
            }
        });
    }

    /**
    * 
    */
    ls.dashboard = function () {
        $.ajax({
            type: "post",
            dataType: "json",
            url: lsAjax.ajaxurl,
            data: {
                action: "ls_submission",
                type: "dashboard",
                post_id: lsAjax.post_id,
                nonce: lsAjax.nonce,
                from: $('#ls_dashboard_time').val()
            },
            success: function (response) {
                // $('#debug').text(response.debug);
                //alert(response.debug);
                ls.input_responses(response, true);
                //alert("done");
                setTimeout(function () {
                    ls.dashboard()
                }, 10000);
            },
            error: function (a, b, c) {
                alert(a.responseText + "," + b + "," + c);
            }
        });
    }

    /**
    * 
    */
    ls.login_warn = function () {
        if (lsAjax.login_warn > 0) {
            $(".ls_loginphide").removeClass("ls_loginphide").addClass(
					"ls_loginpshow");
        }
    }

    /**
    * 
    */
    ls.db_highlight = function (col, highlight) {
        if (col > 0) {
            if (highlight) {
                $("#ls_dashboard tr").each(function () {
                    $(this).find("td").eq(col).addClass("ls_hover")
                });
                $("#ls_dashboard tr").each(function () {
                    $(this).find("th").eq(col).find("div").addClass("ls_hover")
                });
            } else {
                $("#ls_dashboard tr").each(function () {
                    $(this).find("td").eq(col).removeClass("ls_hover")
                });
                $("#ls_dashboard tr").each(
						function () {
						    $(this).find("th").eq(col).find("div").removeClass(
									"ls_hover")
						});
            }
        }
    }

    /**
    * 
    */
    ls.db_filter = function (filter) {
        var val = $('#ls_filter').val();
        $('#ls_p_purge').show();
        $('#ls_p_remove_class').show();
        if (val != -1) {
            $('.ls_class_name').html(
					ls.escape_html($('#ls_filter option:selected').text()));
        } else {
            $('.ls_class_name').html("All my classes");
        }
        var line = 1;
        if (filter != 2) {
            $('#ls_dashboard tr').each(
					function () {
					    if (($(this).data('class') == 'head')) {
					        $(this).show();
					    } else if (($(this).data('class') == 'none')) {
					        if (line > 1) {
					            $(this).hide();
					        } else {
					            $(this).show();
					        }
					    } else if ((val == -1)
								|| ($(this).data('class').indexOf(
										'|' + val + '|') != -1)) {
					        $(this).show();
					        $(this).removeClass('ls_db_odd ls_db_even')
									.addClass(
											(line % 2) == 1 ? 'ls_db_odd'
													: 'ls_db_even');
					        $(this).show();
					        line++;
					    } else {
					        $(this).hide();
					    }
					});
        }

        var line = 1;
        $('#ls_stream tr')
				.each(
						function () {
						    if (typeof ($(this).data('user')) !== 'undefined') {
						        var s = $('#' + $(this).data('user'));
						        if ((val == -1)
										|| (s.data('class').indexOf(
												'|' + val + '|') != -1)) {
						            $(this).show();
						            $(this)
											.removeClass('ls_db_odd ls_db_even')
											.addClass(
													(line % 2) == 1 ? 'ls_db_odd'
															: 'ls_db_even');
						            $(this).show();
						            line++;
						        } else {
						            $(this).hide();
						        }
						    }
						});
    }

    //
    // Prepare the document according to what post-type has been loaded
    //
    $(document).ready(
			function () {
			    if (lsAjax.view_mode == 0) {
			        $("#ls_slides div[data-menu^=ls_menu]").each(
							function () {
							    $(
										"#ls_slides input[data-menu="
												+ $(this).data("menu")
												+ "item]").val(
										$(this).find("h1").eq(0).text());
							});
			        var i = 0;
			        $(".ls_menu input.ls_menu_input_ls").each(function () {
			            $(this).on("click", {
			                value: i++
			            }, function (e) {
			                e.preventDefault();
			                ls.mark(0, -1);
			                ls.set_selected_index(e.data.value);
			            })
			        });
			        i = 0;
			        $(".ls_menu input.ls_menuimg").each(function () {
			            $(this).on("click", {
			                value: i++
			            }, function (e) {
			                e.preventDefault();
			                ls.mark(0, -1);
			                ls.set_selected_index(e.data.value);
			            })
			        });
			        ls.populate();
			        $("a#openGallery").click(function (e) {
			            e.preventDefault();
			            ls.set_selected_index(lsAjax.select);
			        });
			        var i;
			        for (i = 0; i < LS_CLASSES.length; i++) {
			            $(".ls_lightsal" + i).on("click", {
			                value: i
			            }, function (e) {
			                e.preventDefault();
			                ls.mark(ls.getSelectedIndex(), e.data.value);
			            });
			            $(".ls_lightsl" + i).on("click", {
			                value: i
			            }, function (e) {
			                e.preventDefault();
			                ls.mark(ls.getSelectedIndex(), e.data.value);
			            });
			        }
			        $("#ls_login").click(function (e) {
			            e.preventDefault();
			            ls.login();
			        });
			        ls.set_selected_index(parseInt(lsAjax.select));
			        if (lsAjax.login_warn > 0) {
			            setTimeout(function () {
			                ls.login_warn()
			            }, lsAjax.login_warn);
			        }
			        hljs.initHighlightingOnLoad();
			    } else if (lsAjax.view_mode == 3) {
			        $('#ls_dashboard td').on(
							'mouseover mouseleave',
							function (e) {
							    ls.db_highlight($(this).index(),
										e.type == 'mouseover');
							});
			        $('#ls_dashboard th').on(
							'mouseover mouseleave',
							function (e) {
							    ls.db_highlight($(this).index(),
										e.type == 'mouseover');
							});

			        $('#ls_filter').change(function (e) {
			            ls.db_filter(1);
			        });
			        $('#ls_filter_but').hide();
			        $('#ls_merge').on('click', function (e) {
			            e.preventDefault();
			            ls.merge();
			        });
			        $('#ls_remove').on('click', function (e) {
			            e.preventDefault();
			            ls.remove(false);
			        });
			        $('#ls_purge').on('click', function (e) {
			            e.preventDefault();
			            ls.remove(true);
			        });

			        $('select[name^=lsf_]').change(function (e) {
			            ls.db_format($(this));
			        });

			        $('a.ls_db_input_a').on('click', function (e) {
			            ls.db_toggle_input($(this));
			        });

			        $('#ls_change').on('click', function (e) {
			            e.preventDefault();
			            $('#ls_man').hide();
			            if ($('#ls_dops').is(':visible')) {
			                $('#ls_dops').hide();
			            } else {
			                $('#ls_dops').show();
			            }
			        });
			        $('#ls_manage').on('click', function (e) {
			            e.preventDefault();
			            $('#ls_dops').hide();
			            if ($('#ls_man').is(':visible')) {
			                $('#ls_man').hide();
			            } else if ($('#ls_dashboard').is(':visible')) {
			                $('#ls_man').show();
			            } else {
			                $('#ls_man').hide();
			            }
			        });

			        $('#ls_db_stream').on('click', function (e) {
			            $('#ls_dashboard').hide();
			            $('#ls_stream').show();
			            $('#ls_man').hide();
			        });
			        $('#ls_db_view').on('click', function (e) {
			            $('#ls_dashboard').show();
			            $('#ls_stream').hide();
			        });
			        hljs.initHighlightingOnLoad();
			        setTimeout(function () {
			            ls.dashboard()
			        }, 10000);
			    } else {
			        // unbind meta box collapse
			        $('.postbox h3, .postbox .handlediv')
							.off('click.postboxes');
			        if (lsAjax.view_mode == 2 || lsAjax.view_mode == 4) {
			            $("#ls_search_sub").click(function (e) {
			                e.preventDefault();
			                ls.search();
			            });
			            $("#ls_move_up").click(function (e) {
			                e.preventDefault();
			                ls.move_up();
			            });
			            $("#ls_move_down").click(function (e) {
			                e.preventDefault();
			                ls.move_down();
			            });
			            $("#ls_delete").click(function (e) {
			                e.preventDefault();
			                ls.delete_item();
			            });
			            $("#ls_bulk").click(function (e) {
			                e.preventDefault();
			                ls.switch_mode();
			            });
			            ls.add_handlers();
			        }
			    }
			});
} (jQuery));
