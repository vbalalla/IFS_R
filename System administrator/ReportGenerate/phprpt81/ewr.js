<!--##session jscommon##-->

/**
 * JavaScript for PHP Report Maker 8
 * @license (C)2007-2014 e.World Technology Ltd.
 */
var ewrEmailDialog, ewrExportDialog, ewrPopups = {}, $rowindex$ = null;
var ewrDrillCharts = [], ewrExporting = false, ewrExportCharts = [];
var EWR_TABLE_CLASSNAME = "ewTable";
var EWR_GRID_CLASSNAME = "ewGrid";
var EWR_TABLE_ROW_CLASSNAME = "ewTableRow";
var EWR_TABLE_ALT_ROW_CLASSNAME = "ewTableAltRow";
var EWR_ITEM_TEMPLATE_CLASSNAME = "ewTemplate";
var EWR_ITEM_TABLE_CLASSNAME = "ewItemTable";
var EWR_TABLE_LAST_ROW_CLASSNAME = "ewTableLastRow";
var EWR_TABLE_LAST_COL_CLASSNAME = "ewTableLastCol";
var EWR_TABLE_BORDER_BOTTOM_CLASSNAME = "ewTableBorderBottom";
var EWR_UNFORMAT_YEAR = 50;
var EWR_POPUP_MINWIDTH = 200;
var EWR_POPUP_MINHEIGHT = 150;
var EWR_POPUP_DEFAULTHEIGHT = 200;
var EWR_EMPTY_VALUE = "##empty##";
var EWR_NULL_VALUE = "##null##";
var ewr_ClientScriptInclude = jQuery.getScript;
var ewrLightboxSettings = { transition: "none", photo: true, opacity: 0.5 };

// Forms object
var ewrForms = {};

// Init icon tooltip
function ewr_InitIcons() {
	var $ = jQuery;
	$(".ewIcon, .ewTableHeaderPopup .icon-filter").closest("a, button").add(".ewLightbox, .ewDrillLink").tooltip({
		container: "body",
		trigger: (EWR_IS_MOBILE) ? "manual" : "hover",
		placement: "bottom"
	});
}

// Init page
jQuery(function($) {
	$("div.ewTableHeaderBtn").each(function() {
		var $this = $(this), padding = 0;
		$this.find(".ewTableHeaderSort:has(.caret), .ewTableHeaderPopup").each(function() {
			padding += $(this).width();
		});
		if (padding)
			$this.css(EWR_CSS_FLIP ? "padding-left" : "padding-right", padding + 4);
	});
	ewr_InitIcons();
	$.extend(ewrLightboxSettings, {
		title: ewLanguage.Phrase("LightboxTitle"),
		current: ewLanguage.Phrase("LightboxCurrent"),
		previous: ewLanguage.Phrase("LightboxPrevious"),
		next: ewLanguage.Phrase("LightboxNext"),
		close: ewLanguage.Phrase("LightboxClose"),
		xhrError: ewLanguage.Phrase("LightboxXhrError"),
		imgError: ewLanguage.Phrase("LightboxImgError")
	});
	$(".ewLightbox").each(function() {
		var $this = $(this);
		if ($this.colorbox)
			$this.colorbox($.extend({rel: $this.data("rel")}, ewrLightboxSettings));
	});
	$("table." + EWR_TABLE_CLASSNAME).each(ewr_SetupTable); // Init tables
	$("table." + EWR_GRID_CLASSNAME).each(ewr_SetupGrid); // Init grids
	$.later($(".ewChart").length * 100, null, function() { // Adjust footer
		var $window = $(window), $content = $("#ewContentTable"), bh = $("body").height();
		$content.css("min-height", $content.outerHeight());
		$window.resize(function() {
			if ($window.height() > bh)
				$content.css("height", $window.height() - (!$("#ewHeaderRow").is(":hidden") ? $("#ewHeaderRow").height() : 0) - (!$("#ewMobileMenu").is(":hidden") ? $("#ewMobileMenu").height() : 0) - (!$("#ewMenuRow").is(":hidden") ? $("#ewMenuRow").height() : 0) - (!$("#ewFooterRow").is(":hidden") ? $("#ewFooterRow").height() : 0));
		}).triggerHandler("resize");
	});
	$("input[name=pageno]").keypress(function(e) {
		if (e.which == 13) {
			var url = window.location.href, p = url.lastIndexOf(window.location.search);
			window.location = url.substr(0, p) + "?" + this.name + "=" + parseInt(this.value);
			return false;
		}
	});
	$("input[data-calendar]").each(ewr_CreateCalendar); // Init calendars
	$(window).keydown(function(e) {
		window._ctrlKey = e.ctrlKey;
	}).keyup(function(e){
		window._ctrlKey = 0;
	});
	$(".ewIframe").iframeAutoHeight();
	if ((typeof EW_USE_JAVASCRIPT_MESSAGE == "undefined" || !EW_USE_JAVASCRIPT_MESSAGE) && EWR_USE_JAVASCRIPT_MESSAGE)
		ewr_ShowMessage(); // Show message
});

// Attach event by element id or name
function ewr_On(el, sType, fn) {
	var $ = jQuery, $el;
	if ($.isString(el)) { // String, if not "xxx([])" => selector
		$el = (!/^\w+(\[\])?$/.test(el)) ? $(el) : $("[id='" + el + "'],[name='" + el + "']");
	} else {
		$el = $(el);
	}
	$el.on(sType, fn);
}
var ewrEvent = { on: ewr_On }; // Backward compatibility for ewEvent.on

// Batch
function ewr_Batch(el, method, o, overrides) {
	var $ = jQuery, collection = [], scope = (overrides) ? o : null;			
	el = (el && (el.tagName || el.item)) ? el : $(el).get(); // Skip $(el) when possible
	if (el && method) {
		if (el.tagName || el.length === undefined) // Element or not array-like 
			return method.call(scope, el, o);
		for (var i = 0; i < el.length; ++i)
			collection[collection.length] = method.call(scope || el[i], el[i], o);
	} else			
		return false;
	return collection;
}

// Select elements by selector
// Pass in a selector and an optional context (if no context is provided the root "document" is used). Runs the specified selector and returns an array of matched DOMElements.
function ewr_Select(selector, context, fn) {
	var $ = jQuery, $root = $(context);
	var els = $root.find(selector).get();
	if ($.isFunction(fn)) {
		els = ewr_Batch(els, fn);
	} else if ($.isString(fn)) {
		els = ewr_Batch(els, new Function(fn));
	}
	return els;
}

// Filter elements by selector
// Takes in a set of DOMElements, filters them against the specified selector, and returns the results. The selector can be a full selector (e.g. "div > span.foo") and not just a fragment.
function ewr_Matches(selector, set, fn) {
	var $ = jQuery, els = $(set).filter(selector).get();
	if ($.isFunction(fn)) {
		els = ewr_Batch(els, fn);
	} else if ($.isString(fn)) {
		els = ewr_Batch(els, new Function(fn));
	}
	return els;
}

// Export charts
function ewr_ExportCharts(el, url, exportid, f) {

	if (ewrExporting)
		return;

	exportid += "_" + (new Date()).getTime();
	url += (url.split("?").length > 1 ? "&" : "?") + "exportid=" + exportid;

	var $ = jQuery, $el = $(el), method = (f) ? "post" : "get";
	if ($el.is(".dropdown-menu a"))
		$el = $el.closest(".btn-group");

	function _export() {
		if (f && !/&(amp;)?custom=1/.test(url)) {
			data = $(f).serialize();
			data += "&token=" + EWR_TOKEN; // Add token
			$.post(url, data, function(result) {
				ewr_ShowMessage(result);
			});
		} else {
			if (/export=(word|excel|pdf|email)&(amp;)?custom=1/.test(url)) {
				if (/export=email/.test(url)) {
					url = url.replace(/export=email&(amp;)?/i, ""); // Remove duplicate export=email (exists in form)
					url += "&" + $(f).serialize();
				}
				$("iframe.ewExport").remove();
				$("<iframe>").attr("src", url).addClass("ewExport").hide().appendTo($("body").css("cursor", "wait"));
				$.later(5000, null, function() { $("body").css("cursor", "default"); });
			} else {
				ewr_Redirect(url, f, method);
			}			
		}
	}

	if (ewrExportCharts.length == 0) // No charts, just submit the form
		return _export();

	var chartcnt = 0; // Reset
	
	// Export chart
	function chartExport(id) {
		var cht = FusionCharts(id), chartid = "cht_" + id.substr(6), divid = "div_export_" + id.substr(6), $svg = $("#" + divid + " svg");
		var data = { "stream_type": "svg", "stream": cht.getSVGString(), "token": EWR_TOKEN, // Add token			
			"meta_bgColor": "#ffffff", "meta_bgAlpha": "1", // Do not change
			"meta_DOMId": id, "meta_width": $svg.attr("width"), "meta_height": $svg.attr("height"),
			"parameters": "exportfilename=" + exportid + "_" + id  + ".png|exportformat=png|exportaction=download|exportparameters=undefined"					
		};
		$.ajax({ "url": EWR_CHART_EXPORT_HANDLER, "data": data, "cache": false, "type": "POST", "success": function() {
			chartcnt++;
			if (chartcnt == ewrExportCharts.length) { // All charts exported
				_export();
				ewrExporting = false;
				$("body").css("cursor", "default");
			} else { // Next chart
				chartExport(ewrExportCharts[chartcnt]);
			}		
		}}).fail(function(xhr, status, error) {
			ewrExporting = false;
			$("body").css("cursor", "default");
			alert(error);
		});
	}

	// Export charts
	ewrExporting = true;
	$("body").css("cursor", "wait");
	chartExport(ewrExportCharts[0]); // Export first chart

}

// Create a popup filter
function ewr_CreatePopup(name, db) {
	ewrPopups[name] = db;
}

// Show popup filter
function ewr_ShowPopup(e, popupname, useRange, rangeFrom, rangeTo) {

	ewr_StopPropagation(e);
	var $ = jQuery, $el = $(this).tooltip("hide");		
	
	var _addOKBtn = function() { // OK button
		var $tip = $el.data("bs.popover").tip(), form = $tip.find("form")[0];
		var $btn = $('<button type="button" class="btn btn-primary btn-sm ewPopupBtn">' + ewLanguage.Phrase("OK") + '</button>')
			.click(function() {
				if (form && !ewr_SelectedEntry(form, popupname)) {
					alert(ewLanguage.Phrase("PopupNoValue"));
				} else {
					if (form)
						form.submit();
					$el.popover("hide");
				}				
			});
		$tip.find(".modal-footer").append($btn).show();
	}
	
	var _addCancelBtn = function() { // Cancel button		
		var $tip = $el.data("bs.popover").tip(), form = $tip.find("form")[0];
		var $btn = $('<button type="button" class="btn btn-default btn-sm ewPopupBtn">' + ewLanguage.Phrase("Cancel") + '</button>')
			.click(function() {
				$el.popover("hide");
			});
		$tip.find(".modal-footer").append($btn);
	}

	if (!$el.data("bs.popover")) {
		$el.popover({
			html: true,
			placement: "bottom",
			trigger: "manual",
			template: '<div class="popover">' +
				'<div class="popover-content" style="overflow: auto;"></div><div class="modal-footer" style="display: none;"></div><div class="ewResizeHandle"></div></div>',
			content: '<div class="ewrLoading"><div class="ewSpinner"><div></div></div> ' +
				ewLanguage.Phrase("Loading").replace("%s", "") + '</div>',
			container: $("#ewrPopupFilterDialog")
		}).on("shown.bs.popover", function(e) {
			if (ewrPopups[popupname]) { // Load popup if already loaded
				ewr_SetPopupContent.call($el[0], popupname, ewrPopups[popupname], useRange, rangeFrom, rangeTo);
				_addOKBtn();
				_addCancelBtn();
			} else { // Load popup by Ajax if not loaded yet
				$.ajax({
					cache: false,
					dataType: "json",
					data: "popup=" + popupname,
					url: window.location.href.split("?")[0].split("#")[0],
					success: function(db) {
						var args = $el.data("args");
						ewr_CreatePopup(args.popupname, db);
						ewr_SetPopupContent.call($el[0], args.popupname, db, args.useRange, args.rangeFrom, args.rangeTo);
						_addOKBtn();
						_addCancelBtn();
					},
					error: function(o) {
						if (o.responseText) {
							var $tip = $el.data("bs.popover").tip();
							$tip.find(".popover-content").empty().append('<p class="text-error">' + o.responseText + '</p>');
							$tip.find(".modal-footer").empty();
							_addOKBtn();
						}
					}
				});
			}	
		});
		var $tip = $el.data("bs.popover").tip(), $bd = $tip.find(".popover-content"),
			padding = $bd.innerHeight() - $bd.height(), fh; 
		$tip.drag("start", function(ev, dd) {
				var $this = $(this);
				dd.width = $this.width();
				dd.height = $this.height();
				fh = $tip.find(".modal-footer").outerHeight();
			}).drag(function(ev, dd) {				
				var $this = $(this), w = Math.max(EWR_POPUP_MINWIDTH, dd.width + dd.deltaX),
					h = Math.max(EWR_POPUP_MINHEIGHT, dd.height + dd.deltaY);
				$this.css({ width: w, height: h });
				$this.find(".popover-content").height(h - fh - padding);			
			}, { handle: ".ewResizeHandle" });		
	}
	$el.data("args", {"popupname": popupname, "useRange": useRange, "rangeFrom": rangeFrom, "rangeTo": rangeTo}).popover("show");

}

// Set popup fitler content
function ewr_SetPopupContent(name, db, useRange, rangeFrom, rangeTo) {
	var $ = jQuery, cnt = 0, $el = $(this);
	$.map(db, function(record) {
		if (record["s"] === false)
			cnt++;
	});
	var selectall = (cnt == 0), showdivider = false, checkedall = selectall ? " checked=\"checked\"" : "";
	var $form = $("<form id=\"" + name + "_FilterForm\" class=\"form-horizontal\" method=\"post\">" +
		"<input type=\"hidden\" name=\"token\" value=\"" + EWR_TOKEN + "\">" +
		"<input type=\"hidden\" name=\"popup\" value=\"" + name + "\">" +
		"<div class=\"ewPopupContainer\"></div></form>");
	var $container = $form.find("div"); 
	if (useRange) {
		$container.append("<div class=\"ewPopupItem\"><table class=\"ewPopupRange\">" +
			"<tr><td>" + ewLanguage.Phrase("PopupFrom") + "</td><td>" +
			"<select class=\"form-control\" name=\"rf_" + name + "\" style=\"width: auto;\" onchange=\"ewr_SelectRange(this, '" + name + "');\">" +
			"</select></td></tr>" +
			"<tr><td>" + ewLanguage.Phrase("PopupTo") + "</td><td>" +
			"<select class=\"form-control\" name=\"rt_" + name + "\" style=\"width: auto;\" onchange=\"ewr_SelectRange(this, '" + name + "');\">" +
			"</select></td></tr></table></div>");
		var $select = $container.find("select").first();
		$select.append("<option value=\"\">" + ewLanguage.Phrase("PopupSelect") + "</option>");
		$.map(db, function(record) {
			var key = record["k"], val = record["v"];
			if (key.substring(0,2) != "@@" && key != EWR_NULL_VALUE && key != EWR_EMPTY_VALUE) {
				var selected = (key == rangeFrom) ? " selected=\"selected\"" : "";
				$select.append("<option value=\"" + key + "\"" + selected + ">" + val + "</option>");
			}
		});
		var $select = $container.find("select").last();		
		$select.append("<option value=\"\">" + ewLanguage.Phrase("PopupSelect") + "</option>");
		$.map(db, function(record) {
			var key = record["k"], val = record["v"];
			if (key.substring(0,2) != "@@" && key != EWR_NULL_VALUE && key != EWR_EMPTY_VALUE) {
				selected = (key == rangeTo) ? " selected=\"selected\"" : "";
				$select.append("<option value=\"" + key + "\"" + selected + ">" + val + "</option>");
			}
		});
		$container.append("<div class=\"ewPopupDivider\"></div>");		
	}
	$container.append("<div class=\"ewPopupItem checkbox\"><label><input type=\"checkbox\" name=\"sel_" + name + "[]\" value=\"\" onclick=\"ewr_SelectAll(this);\"" + checkedall + ">" +
		ewLanguage.Phrase("PopupAll") + "</label></div>");
	$.map(db, function(record) {
		var key = record["k"], val = record["v"], checked = record["s"] ? " checked=\"checked\"" : "";
		if (key.substring(0,2) == "@@") {
			showdivider = true;
		} else if (showdivider) {
			showdivider = false;
			$container.append("<div class=\"ewPopupDivider\"></div>");
		}
		$container.append("<div class=\"ewPopupItem checkbox\"><label><input type=\"checkbox\" name=\"sel_" + name + "[]\" value=\"" + key + "\" onclick=\"ewr_UpdateSelectAll(this);\"" + checked + ">" + val + "</label></div>");
	});	
	var $tip = $el.data("bs.popover").tip();
	$tip.find(".popover-content").empty().append($form);
	$tip.find(".modal-footer").empty();
}

// Check if selected
function ewr_SelectedEntry(f, name) {
	var $ = jQuery, $els = $(f.elements["sel_" + name + "[]"]);
	return ($els.length > 0) ? $els.filter(":not(:first):checked").length > 0 : $els.prop("checked");
}

// Select all
function ewr_SelectAll(el) {
	ewr_ClearRange(el); // Clear any range set
	jQuery(el.form.elements).filter("input:checkbox[name='" + el.name + "']:not(:first)").prop("checked", el.checked);
}

// Update select all
function ewr_UpdateSelectAll(el) {
	ewr_ClearRange(el); // Clear any range set
	var f = el.form;
	if (f) f.elements[el.name][0].checked = jQuery(el.form.elements).filter("input:checkbox[name='" + el.name + "']:not(:first):not(:checked)").length == 0;
}

// Select range
function ewr_SelectRange(el) {
	var from, to, f = el.form, name = el.name.replace(/^r[ft]_/, ""); 
	if (f.elements["rf_" + name].selectedIndex > -1)
		from = f.elements["rf_" + name].options[f.elements["rf_" + name].selectedIndex].value;
	if (f.elements["rt_" + name].selectedIndex > -1)
		to = f.elements["rt_" + name].options[f.elements["rt_" + name].selectedIndex].value;
	if (!$.isValue(from) || !$.isValue(to) || from === "" || to === "")
		return;
	ewr_SetRange(el, from, to, true);
}

// Clear range
function ewr_ClearRange(el) {
	var f = el.form, name = el.name.replace(/^sel__/, "").replace(/\[\]$/, "");
	var from = f.elements["rf_" + name], to = f.elements["rt_" + name];
	if (from && to && from.selectedIndex > 0 && to.selectedIndex > 0) {
		from.selectedIndex = 0;
		to.selectedIndex = 0;
		ewr_SetRange(el, from.options[from.selectedIndex].value, to.options[to.selectedIndex].value, false);
	}
}

// Set range
function ewr_SetRange(el, from, to, set) {
	var $ = jQuery, f = el.form, name = el.name.replace(/^r[ft]_/, "sel_") + "[]", inRange;
	$(f.elements).filter("input:checkbox[name='" + name + "']").each(function() {
		if (this.value == from)
			inRange = true;
		if (inRange)
			this.checked = set;
		else
			if (set) this.checked = false;
		if (this.value == to)
			inRange = false;
	});
}

// Page Object
function ewr_Page(name) {
	this.Name = name;
	this.PageID = "";

	// Validate function
	this.ValidateRequired = true;
}

// Forms object/function
var ewrForms = function(el) { // Id or element
	if (el) {
		var $ = jQuery, id;
		if ($.isString(el)) { // Id
			id = el;
		} else { // Element
			id = $(ewr_GetForm(el)).attr("id");
		}
		if (id) {   
			for (var i in this) {
				if (i === id)
					return this[i];
			}
		}
	}
	return undefined;
}

// Form class
function ewr_Form(id) {
	var $ = jQuery;	
	this.ID = id; // Same ID as the form
	this.$Element = null;
	this.Form = null;
	this.InitSearchPanel = false; // Expanded by default

	// Change search operator
	this.SrchOprChanged = function(el) {
		var form = this.GetForm(), $form = $(form), elem = $.isString(el) ? form.elements[el] : el;
		if (!elem)
			return;
		var param = /^so2_/.test(elem.id) ? elem.id.substr(4) : elem.id.substr(3), val = $(elem).val(), isBetween = val == "BETWEEN",
			isNullOpr = val == "IS NULL" || val == "IS NOT NULL";
		if (/^so_/.test(elem.id))
			$form.find("[name^=sv_" + param + "],[name^=cal_sv_" + param + "]").prop("disabled", isNullOpr);
		if (/^so2_/.test(elem.id))
			$form.find("[name^=sv2_" + param + "],[name^=cal_sv2_" + param + "]").prop("disabled", isNullOpr);
		$form.find("span.btw0_" + param).toggle(!isBetween).end().find("span.btw1_" + param).toggle(isBetween)
			.find(":input").prop("disabled", !isBetween);
	}

	// Validate
	this.ValidateRequired = true;
	this.Validate = null;

	// Disable form
	this.DisableForm = function() {
		if (!EWR_DISABLE_BUTTON_ON_SUBMIT)
			return;
		var form = this.GetForm();
		$(form).find(":submit, :reset").prop("disabled", true).addClass("disabled"); 
	}

	// Enable form
	this.EnableForm = function() {
		if (!EWR_DISABLE_BUTTON_ON_SUBMIT)
			return;
		var form = this.GetForm();
		$(form).find(":submit, :reset").prop("disabled", false).removeClass("disabled");
	}
	
	// Before submit
	this.PreSubmit = function() {
		var form = this.GetForm(), $form = $(form);
		$form.find("input[name^=s_],input[name^=sx_],input[name^=q_]").prop("disabled", true); // Do not submit these values
	}

	// Submit
	this.Submit = function(action) {
		var form = this.GetForm(), $form = $(form);
		this.DisableForm();
		//this.UpdateTextArea();
		if (!this.Validate || this.Validate()) {
			if (action)
				form.action = action;
			this.PreSubmit();
			form.submit();
		} else {
			this.EnableForm();	
		}		
		return false;
	}	

	// Check empty row
	this.EmptyRow = null;

	// Multi-page
	this.MultiPage = null;

	// Dynamic selection lists
	this.Lists = {};

	// AutoSuggests
	this.AutoSuggests = {};

	// Get the HTML form object
	this.GetForm = function() {
		if (!this.Form) {			
			this.$Element = $("#" + this.ID);
			if (this.$Element.is("form")) { // HTML form
				this.Form = this.$Element[0];
			} else if (this.$Element.is("div")) { // DIV => Grid page
				this.Form = this.$Element.closest("form")[0];	
			}
		}
		return this.Form;
	}

	// Update dynamic selection lists
	this.UpdateOpts = function(rowindex) {
		if (rowindex === $rowindex$) // Null => return, undefined => update all
			return;		
		var lists = [], form = this.GetForm();
		for (var id in this.Lists) {
			var parents = this.Lists[id].ParentFields.slice(); // Clone
			var ajax = this.Lists[id].Ajax;
			if ($.isValue(rowindex)) {
				id = id.replace(/^x_/, "x" + rowindex + "_");
				for (var i = 0, len = parents.length; i < len; i++)						
					parents[i] = parents[i].replace(/^x_/, "x" + rowindex + "_");
			}
			if (ajax) { // Ajax 
				var pvalues = [];
				for (var i = 0, len = parents.length; i < len; i++)						
					pvalues[pvalues.length] = ewr_GetOptValues(parents[i], form); // Save the initial values of the parent lists	
				lists[lists.length] = [id, pvalues, true, false];
			} else { // Non-Ajax
				ewr_UpdateOpt.call(this, id, parents, null, false);	
			}
		}

		// Update the Ajax lists
		for (var i = 0, cnt = lists.length; i < cnt; i++)
			ewr_UpdateOpt.apply(this, lists[i]);
	}
	
	// Create AutoSuggest
	this.CreateAutoSuggest = function(id, forceselect) {
		this.AutoSuggests[id] = new ewr_AutoSuggest(id, this, forceselect);
	}

	// Show error message
	this.OnError = function(el, msg) {
		return ewr_OnError(this, el, msg); 
	}
	
	// Get form data
	this.GetFormData = function() {
		var form = this.GetForm(), $form = $(form);
		if (ewr_HasFormData(form)) {
			this.PreSubmit();
			return $form.serialize();
		}
		return "";			
	}
	
	// Set up filters
	this.SetupFilters = function(e, filters) {
		var id = this.ID, data = this.FilterList, $sf = $(".ewSaveFilter[data-form=" + id + "]"),
			$df = $(".ewDeleteFilter[data-form=" + id + "]"),
			$delete = $df.parent("li").toggleClass("dropdown-submenu", !!filters.length).toggleClass("disabled", !filters.length), 
			$save = $sf.parent("li").toggleClass("disabled", !data),
			$btn = $(e.target);
		$save.off("click.ew").on("click.ew", function(e) { // Save filter
			ewr_Prompt(ewLanguage.Phrase("EnterFilterName"), function(name) {
				filters[filters.length] = [name, data];	
				$.localStorage.set(id + "_filters", filters);
			}, true);
			return false;
		}).prevAll().remove();
		$df.next("ul.dropdown-menu").remove();		
		if (filters.length) {			
			$menu = $df.closest("ul.dropdown-menu");
			$submenu = $("<ul class='dropdown-menu'></ul>");			
			for (var i in filters) {
				if (!$.isArray(filters[i]))
					continue;
				$("<li><a data-index='" + i + "' href='#'>" + filters[i][0] + "</a></li>").on("click", function(e) { // Delete
					var i = $(this).find("a[data-index]").data("index");
					ewr_Prompt(ewLanguage.Phrase("DeleteFilterConfirm").replace("%s", filters[i][0]), function() {
						delete(filters[i]);
						$.localStorage.set(id + "_filters", filters);
					});
					return false;
				}).appendTo($submenu);
				$("<li><a class='ewFilterList' data-index='" + i + "' href='#'>" + filters[i][0] + "</a></li>").insertBefore($save).on("click", function(e) {
					var i = $(this).find("a[data-index]").data("index");
					$("<form>").attr({method: "post", action: location.href.split("?")[0]})
						.append($("<input type='hidden'>").attr({name: "cmd", value: "resetfilter"}), $("<input type='hidden'>").attr({name: "token", value: EWR_TOKEN}), $("<input type='hidden'>").attr({name: "filter", value: JSON.stringify(filters[i][1])}))
						.appendTo("body").submit();
					return false;
				});

			}
			$("<li class='divider'></li>").insertBefore($save);			
			$delete.append($submenu);			
		}
	}

	// Init form
	this.Init = function() {
		var self = this, form = this.GetForm(), $form = $(form);
		if (!form)
			return;		

		// Search panel
		if (this.InitSearchPanel && !ewr_HasFormData(form))
			$("#" + this.ID + "_SearchPanel").removeClass("in");
			
		// Search panel toggle
		$(".ewSearchToggle[data-form=" + this.ID + "]").on("click.bs.button", function() {
			$("#" + $(this).data("form") + "_SearchPanel").collapse("toggle");
		});

		// Search operators
		$form.find("select[id^=so_]").each(function() {
			var $this = $(this).change();
			if ($this.val() != "BETWEEN")
				$form.find("#so2_" + this.id.substr(2)).change();
		});
		
		// Filters button		
		if (window.localStorage) {
			$(".ewFilterOption." + this.ID + " .ewButtonDropdown").on("show.bs.dropdown", function(e) {
				var filters = $.localStorage.get(self.ID + "_filters") || []; 
				var ar = $.grep(filters, function(val) {
					if ($.isArray(val) && val.length == 2)
						return val;
				});
				self.SetupFilters(e, ar);				
			});					 
		} else
			$(".ewFilterOption").hide();

		// Dynamic selection lists
		this.UpdateOpts();

		// Bind submit event
		if (this.$Element.is("form")) { // Not Grid page
			$form.submit(function(e) {
				return self.Submit();
			});
		}

		// Store form object as data
		this.$Element.data("form", this);
	}

	// Add to the global forms object	
	ewrForms[this.ID] = this;
}

// Prompt
function ewr_Prompt(text, cb, input) {
	var $ = jQuery, $dlg = $("#ewrPrompt"), $body = $dlg.find(".modal-body").empty();
	if (input) { // prompt
		$body.append('<div class="form-group">' +
			'<label class="control-label">' + text + '</label>' +
			'<input type="text" class="form-control" style="display: block; width: 100%;"></div>');
		$input = $body.find("input").click(function() {
			$input.parent().removeClass("has-error");
		});
		$dlg.find(".modal-footer .btn-primary").unbind().click(function(e) { // OK
			var val = $.trim($input.val());
			if (val == "") {
				$input.parent().addClass("has-error");
				$input[0].focus();
			} else {
				$dlg.modal("hide");
				if ($.isFunction(cb))
					cb(val);				
			}
		});
		$dlg.on("shown.bs.modal", function(e) {
			$input[0].focus();	
		});	
	} else { // confirm
		$body.append("<div>" + text + "</div>");
		$dlg.find(".modal-footer .btn-primary").unbind().click(function(e) { // OK
			$dlg.modal("hide");
			if ($.isFunction(cb))
				cb();	
		});
	}	
	$dlg.modal("show");
}

// Find form
function ewr_GetForm(el) {
	var $ = jQuery, $el = $(el), $f = $el.closest(".ewForm");
	if (!$f[0]) // Element not inside form
		$f = $el.closest(".ewGrid").find(".ewForm");	
	return $f[0];
}

// Check search form data
function ewr_HasFormData(form) {
	var $ = jQuery, els = $(form).find("[name^=sv_][value!=''][value!='{value}'],[name^=sv2_][value!=''][value!='{value}']").get();
	for (var i = 0, len = els.length; i < len; i++) {
		var el = els[i];
		if (/^(so|so2)_/.test(el.name)) {
			if (/^IS/.test($(el).val()))
				return true;
		} else if (el.type == "checkbox" || el.type == "radio") {
			if (el.checked)
				return true;
		} else if (el.type == "select-one" || el.type == "select-multiple") {
			for (var j = 0, cnt = el.options.length; j < cnt; j++) {
				if (el.options[j].selected && el.options[j].value != "" && el.options[j].value != "##all##")
					return true;
			}
		} else if (el.type == "text" || el.type == "hidden" || el.type == "textarea") {
			return true;
		}
	}
	return false;
}

// Update a dynamic selection list
// - obj {HTMLElement|array[HTMLElement]|string|array[string]} target HTML element(s) or the id of the element(s) 
// - parentId {array[string]|array[array]} parent field element names or data
// - async {boolean|null} async(true) or sync(false) or non-Ajax(null)
// - change {boolean} trigger onchange event
function ewr_UpdateOpt(obj, parentId, async, change) {
	var $ = jQuery, self = this, $this = $(this);
	var exit = function() {
		$this.dequeue();
	};	
	if (!obj || obj.length == 0)
		return exit();
	var f = (this.Form) ? this.Form : (this.form) ? this.form : null;
	if (this.form && /^x\d+_/.test(this.id)) // Has row index => grid
		f = ewr_GetForm(this); // Detail grid or HTML form
	if (!f)
		return exit();
	var frm = (this.Form) ? this : ewrForms[f.id];
	if (!frm)
		return exit();
	var args = $.makeArray(arguments);	
	if (this.form && $.isArray(obj) && $.isString(obj[0])) { // Array of id (onchange/onclick event)
		for (var i = 0, len = obj.length; i < len; i++)
			$this.queue($.proxy(ewr_UpdateOpt, self, obj[i], parentId, async, change));
		var list = frm.Lists[this.id.replace(/^[xy]\d*_/, "x_")];
		return exit();
	}
	if ($.isString(obj))
		obj = ewr_GetElements(obj, f);
	var ar = ewr_GetOptValues(obj);
	var oid = ewr_GetId(obj, false);
	if (!oid)
		return exit();
	var nid = oid.replace(/^([xy])(\d*)_/, "x_");
	var prefix = RegExp.$1;	
	var rowindex = RegExp.$2;
	var arp = [];
	if ($.isUndefined(parentId)) { // Parent IDs not specified, use default
		parentId = frm.Lists[nid].ParentFields.slice(); // Clone
		if (rowindex != "") {
			for (var i = 0, len = parentId.length; i < len; i++)
				parentId[i] = parentId[i].replace(/^x_/, "x" + rowindex + "_");
		} else if (prefix == "y") {
//			for (var i = 0, len = parentId.length; i < len; i++) {
//				var yid = parentId[i].replace(/^x_/, "y_");
//				var yobj = ewr_GetElements(yid, f);
//				if (yobj.type || yobj.length > 0) // Has y_* parent
//					parentId[i] = yid; // Changes with y_* parent
//			}
		}
	}
	if ($.isArray(parentId) && parentId.length > 0) {
		if ($.isArray(parentId[0])) { // Array of array => data
			arp = parentId;
		} else if ($.isString(parentId[0])) { // Array of string => Parent IDs
			for (var i = 0, len = parentId.length; i < len; i++)
				arp[arp.length] = ewr_GetOptValues(parentId[i], f);				
		}
	}
	if (!ewr_IsAutoSuggest(obj)) // Do not clear Auto-Suggest
		ewr_ClearOpt(obj);
	var addOpt = function(aResults) {
		for (var i = 0, cnt = aResults.length; i < cnt; i++) {
			var args = {data: aResults[i], parents: arp, valid: true, name: ewr_GetId(obj), form: f};			
			$(document).trigger("addoption", [args]);			
			if (args.valid)
				ewr_NewOpt(obj, aResults[i], f);
		}
		if (!obj.options && obj.length) { // Radio/Checkbox list
			ewr_RenderOpt(obj, f);
			obj = ewr_GetElements(oid, f); // Update the list
		}
		ewr_SelectOpt(obj, ar);
		if (change !== false)
			$(obj).first().change();
	}
	if ($.isUndefined(async)) // Async not specified, use default
		async = frm.Lists[nid].Ajax;
	if (!$.isBoolean(async)) { // Non-Ajax
		var ds = frm.Lists[nid].Options;
		addOpt(ds);
		if (/s(ea)?rch$/.test(f.id) && prefix == "x") { // Search form
			args[0] = oid.replace(/^x_/, "y_");
			ewr_UpdateOpt.apply(this, args); // Update the y_* element
		}
		return exit();
	} else { // Ajax		
		var name = ewr_GetId(obj), data = $(f).find("#s_" + name).val();
		if (!data)
			return exit();
		data += "&type=updateopt&name=" + name; // Name of the target element 
		if (ewr_IsAutoSuggest(obj) && this.Form) // Auto-Suggest (init form or auto-fill)
			data += "&v0=" + encodeURIComponent(ar[0]); // Filter by the current value
		for (var i = 0, cnt = arp.length; i < cnt; i++) // Filter by parent fields
			data += "&v" + (i+1) + "=" + encodeURIComponent(arp[i].join(","));
		data += "&token=" + EWR_TOKEN; // Add token
		$.post(EWR_LOOKUP_FILE_NAME, data, function(result) {
			addOpt(result || []);
		}, "json").always(function() {
			$this.dequeue();
		});
		if (/s(ea)?rch$/.test(f.id) && prefix == "x") { // Search form
			args[0] = oid.replace(/^x_/, "y_");
			ewr_UpdateOpt.apply(this, args); // Update the y_* element
		}		
	}
}

// Clear existing options
function ewr_ClearOpt(obj) {
	if (obj.options) { // Selection list
		var lo = 1;
		for (var i = obj.length - 1; i >= lo; i--)
			if (obj.options[i].value.substr(0,2) != "@@") // Do not clear custom filter
				obj.options[i] = null;
	} else if (obj.length) { // Radio/Checkbox list
		var $ = jQuery, id = ewr_GetId(obj),
			p = ewr_GetElement("dsl_" + id, obj[0].form);
		$(p).data("options", []).find("table." + EWR_ITEM_TABLE_CLASSNAME).remove();
		var els = ewr_GetElements(id, p);
		for (var i = 0; i < els.length; i++) {
			var el = els[i];
			var val = el.value;
			if (val.substr(0,2) == "@@") { // Add custom filter to array
				var label = ewrDom.getAncestorBy(el, function(lbl) {return ewr_SameText(lbl.tagName, "LABEL");});
				var txt = label ? label.innerHTML.replace(/<[^>]*>/g, '') : val;
				ewr_NewOpt(obj, [val, txt], el.form);
			}
		}
	} else if (ewr_IsAutoSuggest(obj)) {
		var o = ewr_GetAutoSuggest(obj);
		o._options = [];
		o.input.value = "";
		obj.value = "";
	}
}

// Get the id or name of an element
// - remove {boolean} remove square brackets, default: true
function ewr_GetId(el, remove) {
	var $ = jQuery, id = "";
	if ($.isString(el)) {
		id = el;
	} else {
		id = $(el).attr("name") || $(el).attr("id"); // Use name first (id may have suffix)
	}
	if (remove !== false && /\[\]$/.test(id)) // Ends with []
		id = id.substr(0, id.length-2); 	
	return id;
}

// Get existing selected values as an array
function ewr_GetOptValues(el, form) {
	var $ = jQuery, obj = ($.isString(el)) ? ewr_GetElements(el, form) : el;
	if (obj.options) { // Selection list
		return $(obj).find("option:selected[value!='']").map(function() {
			return this.value;
		}).get();
	} else if ($.isNumber(obj.length)) { // Radio/Checkbox list, or element not found
		return $(obj).filter(":checked[value!='{value}']").map(function() {
			return this.value;
		}).get();
	} else { // Text/Hidden
		return [obj.value];	
	}	
}

// Get form element(s) as single element or array of radio/checkbox
function ewr_GetElements(name, root) {
	var $ = jQuery, root = $.isString(root) ? "#" + root : root, selector = "[name='" + name + "']";
	selector = "input" + selector + ",select" + selector + ",textarea" + selector + ",button" + selector;
	var $els = (root) ? $(root).find(selector) : $(selector); // Exclude template element
	if ($els.length == 1 && $els.is(":not(:checkbox):not(:radio)"))
		return $els[0];
	return $els.get();
}

// Create combobox option 
function ewr_NewOpt(obj, ar, f) {
	var $ = jQuery, args = {data: ar, id: ewr_GetId(obj), form: f};
	$(document).trigger("newoption", [args]);	
	ar = args.data;
	var value = ar[0];
	var text = ar[1];	
	for (var i = 2; i <= 4; i++) {
		if (ar[i] && ar[i] != "") {
			if (text != "")
				text += ewr_ValueSeparator(i-1, obj);
			text += ar[i];
		}
	}
	if (obj.options) { // Selection list
		obj.options[obj.length] = new Option(text, value, false, false);
	} else if (obj.length) { // Radio/Checkbox list
		var $ = jQuery, $p = $(ewr_GetElement("dsl_" + ewr_GetId(obj), f)), opts = $p.data("options"); // Parent element		
		if ($p[0] && opts)
			opts[opts.length] = {val:value, lbl:text};
	} else if (ewr_IsAutoSuggest(obj)) { // Auto-Suggest
		var o = ewr_GetAutoSuggest(obj);
		o._options[o._options.length] = {val:value, lbl:text};
	}
	return text;
}

// Render the options
function ewr_RenderOpt(obj, f) {
	var id = ewr_GetId(obj); 
	var $ = jQuery, p = ewr_GetElement("dsl_" + id, f), $p = $(p); // Parent element	
	if (!p || !$p.data("options"))
		return;
	var t = ewr_GetElement("tp_" + id, f); 	
	if (!t)
		return;
	var cols = parseInt($p.data("repeatcolumn"), 10) || 5;
	if (EWR_IS_MOBILE)
		cols = 1;
	var $tpl = $(t).contents(), opts = $p.data("options"), type = $tpl.attr("type"),
		$tbl = $("<table class=\"" + EWR_ITEM_TABLE_CLASSNAME + "\"></table>"), $tr;
	if (opts && opts.length) {
		for (var i = 0, cnt = opts.length; i < cnt; i++) {
			if (i % cols == 0)
				$tr = $("<tr></tr>");
			var $el = $tpl.clone(true).val(opts[i].val);
			var $lbl = $("<div>" + opts[i].lbl + "</div>").prepend($el.attr("id", $el.attr("id") + "_" + i)).wrapInner("<label class=\"" + type + "-inline\"></label>");
			$("<td></td>").append($lbl).appendTo($tr);
			if (i % cols == cols - 1) {
				$tbl.append($tr);
			} else if (i == cnt - 1) { // Last
				for (var j = (i % cols) + 1; j < cols; j++)
					$tr.append("<td>&nbsp;</td>");
				$tbl.append($tr);
			} 		
		}
		$p.append($tbl);		
	}
	$p.data("options", []);		
}

// Get display value separator
function ewr_ValueSeparator(index, obj) {
	return ", ";
}

// Select combobox option
function ewr_SelectOpt(obj, value_array) {
	if (!obj || !value_array)
		return;
	var $ = jQuery;
	if (obj.options) { // Selection list
		$(obj).val(value_array);
		if (obj.type == "select-one" && obj.selectedIndex == -1)
			obj.selectedIndex = 0; // Make sure an option is selected (IE)
	} else if (obj.length) { // Radio/Checkbox list
		if (obj.length == 1 && obj[0].type == "checkbox" && obj[0].value != "{value}") { // Assume boolean field // P802
			obj[0].checked = (ewr_ConvertToBool(obj[0].value) === ewr_ConvertToBool(value_array[0]));
		} else {
			$(obj).val(value_array);
		}
	} else if (ewr_IsAutoSuggest(obj) && value_array.length == 1) {
		var o = ewr_GetAutoSuggest(obj);
		for (var i = 0, len = o._options.length; i < len; i++) {
			if (o._options[i].val == value_array[0]) {
				obj.value = o._options[i].val;
				o.input.value = o._options[i].lbl;
				break;
			}
		}
	} else if (obj.type) {
		obj.value = value_array.join(",");
	}

	// Auto-select if only one option
	function isAutoSelect(el) {
		if (!$(el).data("autoselect")) // Is data-autoselect="false"
			return false;
		var form = ewr_GetForm(el);
		if (form) {
			if (/s(ea)?rch$/.test(form.id)) // Search forms
				return false;
			var nid = el.id.replace(/^([xy])(\d*)_/, "x_");
			if (nid in ewrForms[form.id].Lists && ewrForms[form.id].Lists[nid].ParentFields.length == 0) // No parent fields
				return false;
			return true;
		}
		return false;
	} 
	if (obj.options) { // Selection List
		if (obj.type == "select-one" && obj.options.length == 2 && !obj.options[1].selected && isAutoSelect(obj)) {
			obj.options[1].selected = true;
		} else if (obj.type == "select-multiple" && obj.options.length == 1 && !obj.options[0].selected && isAutoSelect(obj)) {
			obj.options[0].selected = true;
		}
	} else if (obj.length) { // Radio/Checkbox list
		if (obj.length == 2 && isAutoSelect(obj[1]))
			obj[1].checked = true;
	} else if (ewr_IsAutoSuggest(obj)) {
		var o = ewr_GetAutoSuggest(obj);
		if (o._options.length == 1 && isAutoSelect(obj)) {
			obj.value = o._options[0].val;
			o.input.value = o._options[0].lbl;
		}
	}
}

// Auto-Suggest
//function ewr_AutoSuggest(elValue, frm, forceSelection, maxEntries) {
//	var nid = elValue.replace(/^[xy](\d*|\$rowindex\$)_/, "x_");
//	var rowindex = RegExp.$1;
//	var oEmpty = {ac:{}}; // Empty Auto-Suggest object
//	if (rowindex == "$rowindex$")
//		return oEmpty;
//	var form = frm.GetForm(); 
//	var elInput = ewr_GetElement("sx_" + elValue, form);
//	if (!elInput)
//		return oEmpty;
//	var elContainer = ewr_GetElement("sc_" + elValue, form);
//	var elSQL = ewr_GetElement("q_" + elValue, form);
//	var elMessage = ewr_GetElement("em_" + elValue, form);	
//	var elParent = frm.Lists[nid].ParentFields.slice(); // Clone
//	for (var i = 0, len = elParent.length; i < len; i++)
//		elParent[i] = elParent[i].replace(/^x_/, "x" + rowindex + "_");
//	this.input = elInput;
//	this.element = ewr_GetElement(elValue, form);
//	this._options = [];
//	var self = this, $ = jQuery, $input = $(this.input), $element = $(this.element);
//
//	// Format display value (Note: Override this function if link field <> display field)
//	this.formatResult = function(ar) {
//		return ar[0];
//	};
//
//	// Generate request
//	this.generateRequest = function(sQuery) {
//		var data = elSQL.value;
//		if (elParent.length > 0) {
//			for (var i = 0, len = elParent.length; i < len; i++) {
//				var arp = ewr_GetOptValues(elParent[i], form);
//				data += "&v" + (i+1) + "=" + encodeURIComponent(arp.join(","));
//			}
//		}
//		return "type=autosuggest&name=" + this.element.name + "&q=" + sQuery + "&" + data; 
//	};
//
//	// Set the selected item to the actual value field
//	this.setValue = function(v) {
//		v = v || $input.val();
//		var ar = jQuery.grep($input.data("results"), function(item) {
//			return item[item.length - 1] == v;
//		});
//		if (ar.length == 0) { // No results
//			if (forceSelection && v) { // Query not empty						
//				$input.val("").closest(".control-group").addClass("error");
//				$(elMessage).show();
//				$element.val("").change();
//				return;
//			}
//		} else {
//			var i = $input.data("typeahead").$menu.find('.active').index();
//			v = $input.data("results")[i][0];
//		}				
//		$element.val(v).change();
//	};
//
//	// Create Typeahead	
//	$input.prop("autocomplete", "off").typeahead({	
//		minLength: 1,
//		items: maxEntries,
//
//		// Matcher
//		matcher: function(item) {
//	      return true;
//	    },
//
//		// Sorter
//		sorter: function (items) {
//			return items;
//		},
//
//		// Source
//		source: function(query, process) {
//			$.post(EWR_LOOKUP_FILE_NAME, self.generateRequest(query), function(data) {
//				$input.data("results", data || []);
//				var ar = $.map($input.data("results"), function(item) {
//					return item[item.length] = self.formatResult(item);
//				});
//				if (ar.length == 0)
//					$input.data("typeahead").$menu.html("");
//				process(ar);
//			}, "json");		
//		},
//
//		// Updater
//		updater: function(item) {
//			self.setValue(item);
//			return item;
//		}		
//	}).blur(function(e) {
//		if ($input.data("typeahead").shown)
//			return;
//		self.setValue();
//	}).focus(function(e) {
//		$input.closest(".control-group").removeClass("error");
//		$(elMessage).hide();
//	});
//	this.ac = $input.data("typeahead");	
//}

// Auto-Suggest
function ewr_AutoSuggest(elValue, frm, forceSelection) {
	var nid = elValue.replace(/^[xy](\d*|\$rowindex\$)_/, "x_");
	var rowindex = RegExp.$1;
	var oEmpty = {typeahead:{}}; // Empty Auto-Suggest object
	if (rowindex == "$rowindex$")
		return oEmpty;
	var form = frm.GetForm(); 
	var elInput = ewr_GetElement("sx_" + elValue, form);
	if (!elInput)
		return oEmpty;
	var elContainer = ewr_GetElement("sc_" + elValue, form);
	var elSQL = ewr_GetElement("q_" + elValue, form);
	var elParent = frm.Lists[nid].ParentFields.slice(); // Clone
	for (var i = 0, len = elParent.length; i < len; i++)
		elParent[i] = elParent[i].replace(/^x_/, "x" + rowindex + "_");
	this.input = elInput;
	this.element = ewr_GetElement(elValue, form);
	this.highlight = true;
	this.hint = true;
	this.minLength = 1;
	this.limit = EWR_AUTO_SUGGEST_MAX_ENTRIES;
	this.templates = null; // Custom Templates
	this._options = [];
	var self = this, $ = jQuery, $input = $(this.input), $element = $(this.element);
	this.$input = $input;

	// Format display value in textbox
	this.formatResult = function(ar) {
		var dv = ar[1];
		if (dv) {
			for (var i = 2; i <= 4; i++)
				dv += (ar[i]) ? ewr_ValueSeparator(i - 1, elValue) + ar[i] : "";
			return dv;
		}
		return ar[0];
	};
	
	// Set the selected item to the actual value field
	this.setValue = function(v) {
		v = v || $input.val();
		var ar = $input.data("results") ? $.map($input.data("results"), function(item, i) { 
			if (item["value"] == v) // Value exists
				return i; // Return the index
		}) : [];
		if (ar.length == 0) { // No results
			if (this._options && this._options.length && this._options[this._options.length - 1].lbl == v) // Value added by Add Option dialog
				return;
			if (forceSelection && v) { // Query not empty
				$input.typeahead("val", "").attr("placeholder", ewLanguage.Phrase("ValueNotExist"))
					.parent().append('<span class="glyphicon glyphicon-warning-sign form-control-feedback"></span>')
					.closest(".form-group,[id^='el_']").addClass("has-error has-feedback");
				$element.val("").change();
				return;
			}
		} else {
			if (!/srch$/.test(form.id) || forceSelection) { // Force selection or not search form
				var i = ar[0]; // Get the index
				if (i > -1)
					v = $input.data("results")[i][0];
			}
		}
		$element.val(v).change(); // Set field value
	};

	// Generate request
	this.generateRequest = function() {
		var data = elSQL.value;
		if (elParent.length > 0) {
			for (var i = 0, len = elParent.length; i < len; i++) {
				var arp = ewr_GetOptValues(elParent[i], form);
				data += "&v" + (i+1) + "=" + encodeURIComponent(arp.join(","));
			}
		}
		return "type=autosuggest&name=" + this.element.name + "&" + data;
	};
	
	// Add events
	$input.on("typeahead:selected", function(e, d) {
		self.setValue(d.text);
	}).blur(function(e) {
		var ta = $input.data("ttTypeahead");
		if (ta && ta.dropdown.isOpen) { // If dropdown is opened
			var i = $input.data("ttTypeahead").dropdown._getCursor().index();
			if (i > -1) { // A suggestion is highlighted
				var val = $input.data("results")[i][1];
				$input.typeahead("val", val);		
			}
		}
		self.setValue();
	}).focus(function(e) {
		$input.attr("placeholder", $element.attr("placeholder"))
			.closest(".form-group,[id^='el_']").removeClass("has-error has-feedback")
			.find(".form-control-feedback").remove();
	});

	$element.data("autosuggest", this);
	
	// Create Bloodhound and Typeahead
	$(function() {
		// Bloodhound
		var o = {
			datumTokenizer: function(d) {
				return Bloodhound.tokenizers.whitespace(d.value);
			},
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			limit: self.limit,
			remote: {
				url: EWR_LOOKUP_FILE_NAME,
				replace: function(url, query) {
					this.ajax.data = self.generateRequest();
					return url + "?q=" + encodeURIComponent(query) + "&rnd=" + ewr_Random();
				},
				ajax: { type: "POST", data: self.generateRequest() },
				filter: function(data) {
					$input.data("results", data || []);
					var ar = $.map($input.data("results"), function(item) {
						var val = item["value"] = self.formatResult(item); // Format the item and save as property
						return { lf: item[0], df1: item[1], df2: item[2], df3: item[3], df4: item[4], value: val }; // Return as object
					});
					return ar;
				}
			}
		};
		// Trigger "bloodhound" event
		$element.trigger("bloodhound", [o]);
		// Create Bloodhound
		self.engine = new Bloodhound(o);
		self.engine.initialize();
		// Typeahead
		var args = [{
			highlight: self.highlight,
			minLength: self.minLength,
			hint: self.hint
		},
		{
			name: frm.ID + "-" + elValue,
			source: self.engine.ttAdapter(),
			templates: self.templates
		}];
		// Trigger "typeahead" event
		$element.trigger("typeahead", [args]);
		// Create Typeahead
		$input.typeahead.apply($input, args);
		$input.off("blur.tt");
		self.typeahead = $input.data("ttTypeahead");
		self.typeahead.$node.css("display", "").addClass("ewAutoSuggest");
	});
}

// Get first element (not necessarily form element)
function ewr_GetElement(name, root) {
	var $ = jQuery, root = $.isString(root) ? "#" + root : root,
		selector = "#" + name.replace(/([\$\[\]])/g, "\\$1") + ",[name='" + name + "']";
	return (root) ? $(root).find(selector)[0] : $(selector).first()[0];
}

// Check if same text
function ewr_SameText(o1, o2) {
	return (String(o1).toLowerCase() == String(o2).toLowerCase());
}

// Check if same string
function ewr_SameStr(o1, o2) {
	return (String(o1) == String(o2));
}

// Check if an element is in array
function ewr_InArray(el, ar) {
	if (!ar)
		return -1;	
	for (var i = 0, len = ar.length; i < len; i++) {
		if (ewr_SameStr(ar[i], el))
			return i;
	}		
	return -1;
}

// Submit language form
//###function ewr_SubmitLanguageForm(f) {
//	if (!f || !f.language || !f.language.value)
//		return;
//	var url = window.location.href;
//	if (window.location.search) {
//		var query = window.location.search;
//		var param = {};			
//		query.replace(/(?:\?|&)([^&=]*)=?([^&]*)/g, function ($0, $1, $2) {
//			if ($1)
//				param[$1] = $2;
//		});
//		param["language"] = encodeURIComponent(f.language.value);
//		var q = "?";
//		for (var i in param)
//			q += i + "=" + param[i] + "&";
//		q = q.substr(0, q.length-1);
//		var p = url.lastIndexOf(window.location.search);
//		url = url.substr(0, p) + q;			
//	} else {
//		url += "?language=" + encodeURIComponent(f.language.value);
//	}
//	window.location = url;
//}

// Set language
function ewr_SetLanguage(el) {
	var $ = jQuery, val = $(el).val();
	if (!val)
		return;
	var url = window.location.href;
	if (window.location.search) {
		var query = window.location.search;
		var param = {};			
		query.replace(/(?:\?|&)([^&=]*)=?([^&]*)/g, function ($0, $1, $2) {
			if ($1)
				param[$1] = $2;
		});
		param["language"] = encodeURIComponent(val);
		var q = "?";
		for (var i in param)
			q += i + "=" + param[i] + "&";
		q = q.substr(0, q.length-1);
		var p = url.lastIndexOf(window.location.search);
		url = url.substr(0, p) + q;			
	} else {
		url += "?language=" + encodeURIComponent(val);
	}
	window.location = url;
}

// Get Ctrl key for multiple column sort
function ewr_Sort(e, url, type) {
	if (type == 2 && e.ctrlKey)
		url += "&ctrl=1";
	location = url;
	return true;
}

// Check if hidden textbox (Auto-Suggest)
function ewr_IsAutoSuggest(el) {
	var $ = jQuery, $el = $(el);
	return (el && $el.is(":text:hidden") && $el.data("typeahead"));	
}

// Get AutoSuggest instance
function ewr_GetAutoSuggest(el) {
	return ewrForms(el).AutoSuggests[el.id];
}

// Set focus
function ewr_SetFocus(obj) {
	if (!obj)
		return;
	var $ = jQuery, $obj = $(obj);
	if (!obj.options && obj.length) { // Radio/Checkbox list 	
		obj = $obj.filter("[value!='{value}']")[0];
	} else if (ewr_IsAutoSuggest(obj)) { // Auto-Suggest
		obj = ewr_GetAutoSuggest(obj).input; 
	}	
	var $cg = $obj.closest(".form-group").addClass("has-error");
	$obj.focus().select().one("click keypress", function() {
		$cg.removeClass("has-error");
	});
}

function ewr_OnError(frm, el, msg) {
	alert(msg); 
	jQuery.later(100, this, "ewr_SetFocus", el); // Focus later to make sure editors are created
	return false;
}

// Check if object has value
function ewr_HasValue(obj) {
	return ewr_GetOptValues(obj).join("") != "";
}

// Encode html
function ewr_HtmlEncode(text) {
	var str = text;
	str = str.replace(/&/g, '&amp');
	str = str.replace(/\"/g, '&quot;');
	str = str.replace(/</g, '&lt;');
	str = str.replace(/>/g, '&gt;'); 
	return str;
}

// Extended basic search clear form
function ewr_ClearForm(form){
	var $ = jQuery;
	$(form).find("[id^=sv_],[id^=sv2_]").each(function() {
		if (this.type == "checkbox" || this.type == "radio") {
			this.checked = false;
		} else if (this.type == "select-one") {
			this.selectedIndex = 0;
		} else if (this.type == "select-multiple") {
			$(this).find("option").prop("selected", false);
		} else if (this.type == "text" || this.type == "textarea") {
			this.value = "";
			if (ewr_IsAutoSuggest(this))
				ewr_GetAutoSuggest(this).input.value = "";
		}
	});
}

// Stop propagation
function ewr_StopPropagation(e) {
	if (e.stopPropagation) {
		e.stopPropagation();
	} else {
		e.cancelBubble = true;
	}
}

// Setup table
//###function ewr_SetupTable(index, tbl, force) {
//	var $ = jQuery, $tbl = $(tbl), $rows = $(tbl.rows);
//	if (!tbl || !tbl.rows || !force && $tbl.data("isset") || tbl.tBodies.length == 0)
//		return;	
//	var rows = $rows.map(function() {
//		$(this.cells).removeClass(EWR_TABLE_LAST_ROW_CLASSNAME).last().addClass(EWR_TABLE_LAST_COL_CLASSNAME); // Cell of last column
//		return this;
//	}).get();
//	if (rows.length) {
//		var r = rows[rows.length - 1];
//		$(r.cells).each(function() {
//			$(this).addClass(EWR_TABLE_LAST_ROW_CLASSNAME);	
//		});
//	}
//	ewr_SetupGrid(index, $tbl.closest("." + EWR_GRID_CLASSNAME)[0], force);
//	$tbl.data("isset", true);
//}

// Setup table
function ewr_SetupTable(index, tbl, force) {
	var $ = jQuery, $tbl = $(tbl), $rows = $(tbl.rows);
	if (!tbl || !tbl.rows || !force && $tbl.data("isset") || tbl.tBodies.length == 0)
		return;
	var n = $rows.filter("[data-rowindex=1]").length || $rows.filter("[data-rowindex=0]").length || 1; // Alternate color every n rows
	var rows = $rows.filter(":not(." + EWR_ITEM_TEMPLATE_CLASSNAME + "):not(:hidden)").each(function() {
		$(this.cells).removeClass(EWR_TABLE_LAST_ROW_CLASSNAME).last().addClass(EWR_TABLE_LAST_COL_CLASSNAME); // Cell of last column
	}).get();
	var div = $tbl.parentsUntil(".ewGrid", ".table-responsive")[0];
	if (rows.length) {
		for (var i = 1; i <= n; i++) {
			var r = rows[rows.length - i]; // Last rows
			$(r.cells).each(function() {
				if (this.rowSpan == i) // Cell of last row
					$(this).addClass(EWR_TABLE_LAST_ROW_CLASSNAME)
						.toggleClass(EWR_TABLE_BORDER_BOTTOM_CLASSNAME, !!div && div.offsetHeight > tbl.offsetHeight);
			});
		}
	}
//###	var form = $tbl.closest("form")[0];
//	var attach = form && $(form.elements).filter("input#a_list:not([value^=grid])").length > 0;
//	$(tbl.tBodies[tbl.tBodies.length - 1].rows) // Use last TBODY (avoid Opera bug)
//		.filter(":not(." + EWR_ITEM_TEMPLATE_CLASSNAME + "):not(." + EWR_TABLE_PREVIEW_ROW_CLASSNAME + "):not(:hidden)")
//		.each(function(i) {
//			var $r = $(this);
//			if (attach && !$r.data("isset")) {
//				if (EWR_InArray($r.data("rowtype"), [EWR_ROWTYPE_ADD, EWR_ROWTYPE_EDIT]) > -1) // Add/Edit row
//					$r.on("mouseover", function() {this.edit = true;}).addClass(EWR_TABLE_EDIT_ROW_CLASSNAME);
//				$r.on("mouseover", EWR_MouseOver).on("mouseout", EWR_MouseOut).on("click", EWR_Click);
//				$r.data("isset", true);
//			}
//			var sw = i % (2 * n) < n;
//			$r.toggleClass(EWR_TABLE_ROW_CLASSNAME, sw).toggleClass(EWR_TABLE_ALT_ROW_CLASSNAME, !sw);
//		});
	ewr_SetupGrid(index, $tbl.closest("." + EWR_GRID_CLASSNAME)[0], force);
	$tbl.data("isset", true);
}

// Setup grid //###
//function ewr_SetupGrid(index, grid, force) {
//	var $ = jQuery, $grid = $(grid);
//	if (!grid || !force && $grid.data("isset"))
//		return;
//	//var rowcnt = $grid.find("table." + EWR_TABLE_CLASSNAME + " > tbody:first > tr").length;
//	var rowcnt = $grid.find("table." + EWR_TABLE_CLASSNAME + " > thead:first > tr").length;
//	var $divupper = $grid.find("div.ewGridUpperPanel");
//	var $divmiddle = $grid.find("div.ewGridMiddlePanel");
//	var $divlower = $grid.find("div.ewGridLowerPanel");
//	var noborder = rowcnt == 0; 
//	if ($divupper[0] && $divlower[0]) {
//		$divlower.toggleClass("hide", rowcnt == 0);
//		$divupper.toggleClass("ewNoBorderBottom", noborder);
//	} else if ($divupper[0] && !$divlower[0]) {
//		$divupper.toggleClass("ewNoBorderBottom", noborder);		
//	} else if ($divlower[0] && !$divupper[0]) {
//		$divlower.toggleClass("ewNoBorderTop", noborder);
//	}
//	$grid.data("isset", true);
//}

// Setup grid
function ewr_SetupGrid(index, grid, force) {
	var $ = jQuery, $grid = $(grid);
	if (!grid || !force && $grid.data("isset"))
		return;
	$grid.find(".ewGridUpperPanel, .ewGridLowerPanel").each(function() {
		var $this = $(this);		
		if (!$this.text().trim()) $this.hide();
	});
	var	rowcnt = $grid.find("table." + EWR_TABLE_CLASSNAME + " > tbody:first > tr").length;
	if (rowcnt == 0 && !$grid.find(".ewGridUpperPanel, .ewGridLowerPanel")[0])
		$grid.hide();
	$grid.data("isset", true);
}

// Popover Start Drag event
function ewr_DragStart(ev, dd) {
	var $ = jQuery, $this = $(this), $body = $("body");
	dd.limit = $body.offset();
	dd.limit.bottom = dd.limit.top + $body.outerHeight() - $this.outerHeight();
	dd.limit.right = dd.limit.left + $body.outerWidth() - $this.outerWidth();
}

// Popover Drag event
function ewr_Drag(ev, dd){
	var $ = jQuery, $this = $(this), $body = $("body"),
		ml = parseInt($this.css("margin-left"), 10),
		mt = parseInt($this.css("margin-top"), 10);
	var x = ($this.outerWidth() > $body.outerWidth()) ? Math.max(dd.limit.left - ml, dd.offsetX - ml) :
		Math.min(dd.limit.right - ml, Math.max(dd.limit.left - ml, dd.offsetX - ml));
	var y = ($this.outerHeight() > $body.outerHeight()) ? Math.max(dd.limit.top - mt, dd.offsetY - mt) :
		Math.min(dd.limit.bottom - mt, Math.max(dd.limit.top - mt, dd.offsetY - mt));
	$this.css({	top: y, left: x });
}

// Show dialog for email sending
// Argument object members:
// - lnk {string} email link id
// - hdr {string} dialog header
// - url {string} URL of the email script
// - exportid - export id
// - el - element
function ewr_EmailDialogShow(oArg) {
	var $ = jQuery, $dlg = ewrEmailDialog || $("#ewrEmailDialog");
	if (!$dlg)
		return;
	var $f = $dlg.find(".modal-body form").data("args", oArg);
	var frm = $f.data("form"); 
	if (!frm) {
		frm = new ewr_Form($f.attr("id"));
		frm.Validate = function() {
			var elm, fobj = this.GetForm(); 
			elm = fobj.elements["sender"];
			if (elm && !ewr_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterSenderEmail"));
			if (elm && !ewr_CheckEmailList(elm.value, 1))
				return this.OnError(elm, ewLanguage.Phrase("EnterProperSenderEmail"));
			elm = fobj.elements["recipient"];
			if (elm && !ewr_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRecipientEmail"));
			if (elm && !ewr_CheckEmailList(elm.value, EWR_MAX_EMAIL_RECIPIENT))
				return this.OnError(elm, ewLanguage.Phrase("EnterProperRecipientEmail"));
			elm = fobj.elements["cc"];
			if (elm && !ewr_CheckEmailList(elm.value, EWR_MAX_EMAIL_RECIPIENT))
				return this.OnError(elm, ewLanguage.Phrase("EnterProperCcEmail"));
			elm = fobj.elements["bcc"];
			if (elm && !ewr_CheckEmailList(elm.value, EWR_MAX_EMAIL_RECIPIENT))
				return this.OnError(elm, ewLanguage.Phrase("EnterProperBccEmail"));
			elm = fobj.elements["subject"];
			if (elm && !ewr_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterSubject"));
			return true;
		};
		frm.Submit = function() {
			var fobj = this.GetForm(), $fobj = $(fobj), args = $fobj.data("args");		
			var exporttype = ewr_GetOptValues("contenttype", fobj);
			if (args.url && args.exportid && exporttype == "html") {
				if (!this.Validate())
					return false;				
				$dlg.modal("hide");
				ewr_ExportCharts(args.el, args.url, args.exportid, fobj);
			} else {	
				var data = $fobj.serialize();
				data += "&token=" + EWR_TOKEN; // Add token
				$.post(fobj.action, data, function(result) {
					ewr_ShowMessage(result);
				});
				$dlg.modal("hide");				
			}
			return false;
		};		
		$f.data("form", frm);
	}
	$dlg.modal("hide").find(".modal-title").html(oArg.hdr);
	$dlg.find(".modal-footer .btn-primary").unbind().click(function(e) {
		e.preventDefault();
		if (frm.Submit())
			$dlg.modal("hide");
	});
	ewrEmailDialog = $dlg.modal("show");
}

// Ajax query
// Prerequisite: Output encrypted SQL by Client Script or Startup Script, e.g.
//   var sql = "<?php echo ewr_Encrypt("SELECT xxx FROM xxx WHERE xxx = '{query_value}'") ?>";
//   - where "{query_value}" will be replaced by runtime value.
//   - s {string} Encrypted SQL
//   - data {string|object} string to replace to replace "{query_value}" in SQL, or
//     object (e.g. {"q": xxx, "q1": xxx, "q2": yyy}) to replace additional values "{query_value_n}" in SQL
//   - callback {function} callback function for async request (see http://api.jquery.com/jQuery.post/), empty for sync request
// Note: Return value is string or array of string.
function ewr_Ajax(sql, data, callback) {
	if (!sql)
		return undefined;
	var $ = jQuery, obj = { s: sql };
	obj = $.extend(obj, ($.isObject(data)) ? data : { q: data });			
	if ($.isFunction(callback)) { // Async
		$.post(EWR_LOOKUP_FILE_NAME, obj, callback, "json");
	} else { // Sync
		var result = $.ajax({ async: false, type: "POST", data: obj }).responseText;
		var aResults = $.parseJSON(result);	      

		// Check if single row or single value
		if (aResults.length == 1) { // Single row
			aResults = aResults[0];
			if ($.isArray(aResults) && aResults.length == 1) { // Single column
				return aResults[0]; // Return a value
			} else {
				return aResults; // Return a row
			}	
		}
		return aResults;
	}
}

// Show drill down
function ewr_ShowDrillDown(e, obj, url, id, hdr) {
	if (e ? e.ctrlKey : window._ctrlKey) {
		ewr_Redirect(url.replace("?d=1&", "?d=2&")); // Change d parameter to 2
		return false;
	}
	var $ = jQuery, pos = ($.isString(obj)) ? "top" : "bottom", //  isString(obj) => chart
		$obj = ($.isString(obj)) ? $("#" + obj) : $(obj);
	$obj.tooltip("hide");  
	var args = {"obj": $obj[0], "id": id, "url": url, "hdr": hdr};
	$(document).trigger("drilldown", [args]);
	var ar = args.url.split("?");
	args.file = (ar[0]) ? ar[0] : "";
	args.data = (ar[1]) ? ar[1] : "";
	args.data += (args.data != "" ? "&" : "") + "token=" + EWR_TOKEN; // Add token
	if (!$obj.data("bs.popover")) {
		$obj.popover({
			html: true,
			placement: pos,
			trigger: "manual",
			template: '<div class="popover"><h3 class="popover-title" style="cursor: move;"></h3>' +
				'<div class="popover-content"></div><div class="ewResizeHandle"></div></div>',
			content: '<div class="ewrLoading"><div class="ewSpinner"><div></div></div> ' +
				ewLanguage.Phrase("Loading").replace("%s", "") + '</div>',
			container: $("#ewrDrillDownPanel")
		}).on("show.bs.popover", function(e) {
			$obj.attr("data-original-title", "");	
		}).on("shown.bs.popover", function(e) {			
			if (!$obj.data("args"))
				return;
			$.ajax({
				cache: false,
				dataType: "html",
				type: "POST",
				data: $obj.data("args").data,
				url: $obj.data("args").file,
				success: function(data) {
					var $tip = $obj.data("bs.popover").tip()
						.drag("start", ewr_DragStart)
						.drag(ewr_Drag, { handle: ".popover-title" });
					if (args.hdr)
						$tip.find(".popover-title").empty().show()
							.append('<button type="button" class="close" aria-hidden="true">&times;</button>' + args.hdr)
							.find(".close").click(function() { $obj.popover("hide"); });
					var reb = /<body[^>]*>([\s\S]*?)<\/body\s*>/i;
					if (data.match(reb)) // Use HTML in document body only
						data = RegExp.$1;
					var html = ewr_StripScript(data);
					var $bd = $tip.find(".popover-content").html(html);
					if (EWR_IS_MOBILE) { // If mobile, insert the container table only
						var container = $bd.find("#ewContainer")[0];
						if (container)
							$bd.empty().append(container);
					}
					$bd.find(".ewTable").each(ewr_SetupTable);
					ewr_ExecScript(data, id);
				},
				error: function(o) {
					if (o.responseText) {
						var $tip = $el.data("bs.popover").tip();
						$tip.find(".popover-content").empty().append('<p class="text-error">' + o.responseText + '</p>');
					}
				}
			});
		}).on("hidden.bs.popover", function(e) {			
			for (var i = 0; i < ewrDrillCharts.length; i++) { // Dispose charts
				var cht = FusionCharts(ewrDrillCharts[i]);
				cht.dispose();
			}
			ewrDrillCharts = [];
			ewr_RemoveScript(id);		
		});
	}
	$obj.data("args", args).popover("show");	
}

// Execute JavaScript in HTML loaded by Ajax
function ewr_ExecScript(html, id) {
	var ar, i = 0, re = /<script([^>]*)>([\s\S]*?)<\/script\s*>/ig;
	while ((ar = re.exec(html)) != null) {
		var text = RegExp.$2;
		if (text != "" && !/ewr_Get(Css|Script)/i.test(text) &&
			/(\s+type\s*=\s*['"]*(text|application)\/(java|ecma)script['"]*)|^((?!\s+type\s*=).)*$/i.test(RegExp.$1))
			ewr_AddScript(text, "scr_" + id + "_" + i++);
	}
}

// Strip JavaScript in HTML loaded by Ajax
function ewr_StripScript(html) {
	var ar, re = /<script([^>]*)>([\s\S]*?)<\/script\s*>/ig;
	var str = html;
	while ((ar = re.exec(html)) != null) {
		var text = RegExp.lastMatch;
		if (/(\s+type\s*=\s*['"]*(text|application)\/(java|ecma)script['"]*)|^((?!\s+type\s*=).)*$/i.test(RegExp.$1))
			str = str.replace(text, "");
	}
	return str;
}

// Add SCRIPT tag
function ewr_AddScript(text, id) {
	var scr = document.createElement("SCRIPT");
	if (id)
		scr.id = id;
	scr.type = "text/javascript";
	scr.text = text;
	return document.body.appendChild(scr);
}

// Remove JavaScript added by Ajax
function ewr_RemoveScript(id) {
	if (id)
		jQuery("script[id^='scr_" + id + "_']").remove();
}

// Language class
function ewr_Language(obj) {
	this.obj = obj;
	this.Phrase = function(id) {
		return this.obj[id.toLowerCase()];
	};
}

// Apply client side template to a DIV
function ewr_ApplyTemplate(divId, tmplId, classId, exporttype, data) {
	var $ = jQuery, $tmpl = $("#" + tmplId);
	if (!$.views || !$tmpl[0])
		return;
	// Add script tags
	$('span[data-class^=tp],div[data-class^=tp]').html(function() {
		var $this = $(this);
		return '<scr' + 'ipt type="text/html" class="' + classId + '" id="' + $this.data('class') + '">' + $this.html() + '</scr' + 'ipt>';
	});
	if (!$tmpl.attr("type")) // Not script
		$tmpl.attr("type", "text/html");
	var args = {data: data || {}, id: divId, template: tmplId, enabled: true};
	$(document).trigger("rendertemplate", [args]);
	if (args.enabled)
		$("#" + divId).html($tmpl.render(args.data));
	// Export custom
	if (exporttype && exporttype != "print") {
		$(function() {
			var $meta = $("meta[http-equiv='Content-Type']");
			var html = "<html><head>";
			if ($meta[0])
				html += "<meta http-equiv='Content-Type' content='" + $meta.attr("content") + "'>";
			if (exporttype == "pdf") {
				html += "<link rel='stylesheet' type='text/css' href='" + EWR_PDF_STYLESHEET_FILENAME + "'>";
			} else {
				html += "<style>" + $.ajax({async: false, type: "GET", url: EWR_PROJECT_STYLESHEET_FILENAME}).responseText + "</style>";
			}
			html += "</" + "head><body>";
			$("span.ewChartTop").each(function(){ html += $(this).html(); });
			html += $("#" + divId).find('div[id^="ct_"]').html();
			$("span.ewChartBottom").each(function(){ html += $(this).html(); });
			html += "</body></html>";
			var url = window.location.href.split('?')[0];
			if (exporttype == "email") {
				var str = window.location.search.replace(/^\?/, "") + "&" + $.param({customexport: exporttype, data: html, filename: classId});				
				str += "&token=" + EWR_TOKEN; // Add token
				$.post(url, str, function(data) {
					ewr_ShowMessage(data);
				});
			} else {
				$("<form>").attr({method: "post", action: url})
					.append($("<input type='hidden'>").attr({name: "customexport", value: exporttype}), $("<input type='hidden'>").attr({name: "data", value: html}), $("<input type='hidden'>").attr({name: "filename", value: classId}))
					.appendTo("body").submit();
			}
			if (window.location != window.parent.location && window.parent.jQuery) // In iframe
				window.parent.jQuery("body").css("cursor", "default");
		})
	}
}

// Render client side template and return the rendered HTML
function ewr_RenderTemplate(tmplId) {
	var $ = jQuery, $tmpl = $("#" + tmplId);
	if (!$.views || !$tmpl[0])
		return;
	if (!$tmpl.attr("type")) // Not script
		$tmpl.attr("type", "text/html");
	var args = {data: {}, template: tmplId};
	$(document).trigger("rendertemplate", [args])
	return $tmpl.render(args.data);
}

// Show message dialog
function ewr_ShowMessage(msg) {
	if (window.location != window.parent.location && parent.ewr_ShowMessage) // In iframe
		return parent.ewr_ShowMessage(msg);
	var $ = jQuery, $div = $("div.ewMessageDialog:first");
	var html = msg || ($div.length ? (EWR_IS_MOBILE ? $div.text() : $div.html()) : "");
	if ($.trim(html) == "")
		return;
	if (EWR_IS_MOBILE) {
		alert(html);
	} else {
		var $dlg = $("#ewrMsgBox");
		$dlg.find(".modal-body").html(html);
		$dlg.modal("show");
	}
}

// Random number
function ewr_Random() {
	return Math.floor(Math.random() * 100001) + 100000;
}

// Toggle search operator
function ewr_ToggleSrchOpr(id, value) {
	var el = this.form.elements[id];
	if (!el)
		return;
	el.value = (el.value != value) ? value : "=";
}

// Validators

// Check US Date format (mm/dd/yyyy)
function ewr_CheckUSDate(object_value) {
	return ewr_CheckDateEx(object_value, "us", EWR_DATE_SEPARATOR);
}

// Check US Date format (mm/dd/yy)
function ewr_CheckShortUSDate(object_value) {
	return ewr_CheckDateEx(object_value, "usshort", EWR_DATE_SEPARATOR);
}

// Check Date format (yyyy/mm/dd)
function ewr_CheckDate(object_value) {
	return ewr_CheckDateEx(object_value, "std", EWR_DATE_SEPARATOR);
}

// Check Date format (yy/mm/dd)
function ewr_CheckShortDate(object_value) {
	return ewr_CheckDateEx(object_value, "stdshort", EWR_DATE_SEPARATOR);
}

// Check Euro Date format (dd/mm/yyyy)
function ewr_CheckEuroDate(object_value) {
	return ewr_CheckDateEx(object_value, "euro", EWR_DATE_SEPARATOR);
}

// Check Euro Date format (dd/mm/yy)
function ewr_CheckShortEuroDate(object_value) {
	return ewr_CheckDateEx(object_value, "euroshort", EWR_DATE_SEPARATOR);
}

// Check date format
//  Format: std/stdshort/us/usshort/euro/euroshort
function ewr_CheckDateEx(value, format, sep) {
	if (value == null || value.length == "")
		return true;
	while (value.indexOf("  ") > -1)
		value = value.replace(/  /g, " ");
	value = value.replace(/^\s*|\s*$/g, "");
	var arDT = value.split(" ");
	if (arDT.length > 0) {
		var re, sYear, sMonth, sDay;
		re = /^([0-9]{4})-([0][1-9]|[1][0-2])-([0][1-9]|[1|2][0-9]|[3][0|1])$/;
		if (ar = re.exec(arDT[0])) {
			sYear = ar[1];
			sMonth = ar[2];
			sDay = ar[3];
		} else {
			var wrksep = "\\" + sep;
			switch (format) {
				case "std":
					re = new RegExp("^(\\d{4})" + wrksep + "([0]?[1-9]|[1][0-2])" + wrksep + "([0]?[1-9]|[1|2]\\d|[3][0|1])$");
					break;
				case "stdshort":
					re = new RegExp("^(\\d{2})" + wrksep + "([0]?[1-9]|[1][0-2])" + wrksep + "([0]?[1-9]|[1|2]\\d|[3][0|1])$");
					break;
				case "us":
					re = new RegExp("^([0]?[1-9]|[1][0-2])" + wrksep + "([0]?[1-9]|[1|2]\\d|[3][0|1])" + wrksep + "(\\d{4})$");
					break;
				case "usshort":
					re = new RegExp("^([0]?[1-9]|[1][0-2])" + wrksep + "([0]?[1-9]|[1|2]\\d|[3][0|1])" + wrksep + "(\\d{2})$");
					break;
				case "euro":
					re = new RegExp("^([0]?[1-9]|[1|2]\\d|[3][0|1])" + wrksep + "([0]?[1-9]|[1][0-2])" + wrksep + "(\\d{4})$");
					break;
				case "euroshort":
					re = new RegExp("^([0]?[1-9]|[1|2]\\d|[3][0|1])" + wrksep + "([0]?[1-9]|[1][0-2])" + wrksep + "(\\d{2})$");
					break;
			}
			if (!re.test(arDT[0]))
				return false;
			var arD = arDT[0].split(sep);
			switch (format) {
				case "std":
				case "stdshort":
					sYear = ewr_UnformatYear(arD[0]);
					sMonth = arD[1];
					sDay = arD[2];
					break;
				case "us":
				case "usshort":
					sYear = ewr_UnformatYear(arD[2]);
					sMonth = arD[0];
					sDay = arD[1];
					break;
				case "euro":
				case "euroshort":
					sYear = ewr_UnformatYear(arD[2]);
					sMonth = arD[1];
					sDay = arD[0];
					break;
			}
		}
		if (!ewr_CheckDay(sYear, sMonth, sDay))
			return false;
	}
	if (arDT.length > 1 && !ewr_CheckTime(arDT[1]))
		return false;
	return true;
}

// Unformat 2 digit year to 4 digit year
function ewr_UnformatYear(yr) {
	if (yr.length == 2)
		return (yr > EWR_UNFORMAT_YEAR) ? "19" + yr : "20" + yr;
	return yr;
}

// Check day
function ewr_CheckDay(checkYear, checkMonth, checkDay) {
	checkYear = parseInt(checkYear, 10);
	checkMonth = parseInt(checkMonth, 10);
	checkDay = parseInt(checkDay, 10);
	var maxDay = (ewr_InArray(checkMonth, [4, 6, 9, 11]) > -1) ? 30 : 31;
	if (checkMonth == 2)
		maxDay = (checkYear % 4 > 0 || checkYear % 100 == 0 && checkYear % 400 > 0) ? 28 : 29;
	return ewr_CheckRange(checkDay, 1, maxDay);
}

// Check integer
function ewr_CheckInteger(object_value) {
	if (!object_value || object_value.length == 0)
		return true;
	if (object_value.indexOf(EWR_DECIMAL_POINT) > -1)
		return false;
	return ewr_CheckNumber(object_value);
}

// Check number
function ewr_CheckNumber(object_value) {
	object_value = String(object_value);
	if (!object_value || object_value.length == 0)
		return true;
	object_value = object_value.replace(/^\s*|\s*$/g, "");
	var re = new RegExp("^[+-]?(\\d{1,3}(" + ((EWR_THOUSANDS_SEP) ? "\\" + EWR_THOUSANDS_SEP + "?" : "") + "\\d{3})*(\\" +
		EWR_DECIMAL_POINT + "\\d+)?|\\" + EWR_DECIMAL_POINT + "\\d+)$");
	return re.test(object_value);
}

// Convert to float
function ewr_StrToFloat(object_value) {
	object_value = String(object_value);
	if (EWR_THOUSANDS_SEP != "") {
		var re = new RegExp("\\" + EWR_THOUSANDS_SEP, "g");
		object_value = object_value.replace(re, "");
	}
	if (EWR_DECIMAL_POINT != "")
		object_value = object_value.replace(EWR_DECIMAL_POINT, ".");
	return parseFloat(object_value);
}

// Convert string (yyyy-mm-dd hh:mm:ss) to date object
function ewr_StrToDate(object_value) {
	var re = /^(\d{4})-([0][1-9]|[1][0-2])-([0][1-9]|[1|2]\d|[3][0|1]) (?:(0\d|1\d|2[0-3]):([0-5]\d):([0-5]\d))?$/;
	var ar = object_value.replace(re, "$1 $2 $3 $4 $5 $6").split(" ");
	return new Date(ar[0], ar[1]-1, ar[2], ar[3], ar[4], ar[5]);
}

// Check range
function ewr_CheckRange(object_value, min_value, max_value) {
	if (!object_value || object_value.length == 0)
		return true;
	var $ = jQuery;
	if ($.isNumber(min_value) || $.isNumber(max_value)) { // Number
		if (ewr_CheckNumber(object_value))
			object_value = ewr_StrToFloat(object_value);
	}
	if (!$.isNull(min_value) && object_value < min_value)
		return false;
	if (!$.isNull(max_value) && object_value > max_value)
		return false;
	return true;
}

// Check time
function ewr_CheckTime(object_value) {
	if (!object_value || object_value.length == 0)
		return true;
	object_value = object_value.replace(/^\s*|\s*$/g, "");
	var re = /^(0\d|1\d|2[0-3]):[0-5]\d:[0-5]\d$/;
	return re.test(object_value);
}

// Check phone
function ewr_CheckPhone(object_value) {
	if (!object_value || object_value.length == 0)
		return true;
	object_value = object_value.replace(/^\s*|\s*$/g, "");
	var re = /^\(\d{3}\) ?\d{3}( |-)?\d{4}|^\d{3}( |-)?\d{3}( |-)?\d{4}$/;
	return re.test(object_value);
}

// Check zip
function ewr_CheckZip(object_value) {
	if (!object_value || object_value.length == 0)
		return true;
	object_value = object_value.replace(/^\s*|\s*$/g, "");
	var re = /^\d{5}$|^\d{5}-\d{4}$/;
	return re.test(object_value);
}

// Check credit card
function ewr_CheckCreditCard(object_value) {
	if (!object_value || object_value.length == 0)
		return true;
	var creditcard_string = object_value.replace(/\D/g, "");	
	if (creditcard_string.length == 0)
		return false;
	var doubledigit = creditcard_string.length % 2 == 1 ? false : true;
	var tempdigit, checkdigit = 0;
	for (var i = 0, len = creditcard_string.length; i < len; i++) {
		tempdigit = parseInt(creditcard_string.charAt(i));		
		if (doubledigit) {
			tempdigit *= 2;
			checkdigit += (tempdigit % 10);			
			if (tempdigit / 10 >= 1.0)
				checkdigit++;			
			doubledigit = false;
		}	else {
			checkdigit += tempdigit;
			doubledigit = true;
		}
	}		
	return (checkdigit % 10 == 0);
}

// Check social security number
function ewr_CheckSSC(object_value) {
	if (!object_value || object_value.length == 0)
		return true;
	object_value = object_value.replace(/^\s*|\s*$/g, "");
	var re = /^(?!000)([0-6]\d{2}|7([0-6]\d|7[012]))([ -]?)(?!00)\d\d\3(?!0000)\d{4}$/;
	return re.test(object_value);
}

// Check emails
function ewr_CheckEmailList(object_value, email_cnt) {
	if (!object_value || object_value.length == 0)
		return true;
	var arEmails = object_value.replace(/,/g, ";").split(";");
	for (var i = 0, len = arEmails.length; i < len; i++) {
		if (email_cnt > 0 && len > email_cnt)
			return false;
		if (!ewr_CheckEmail(arEmails[i]))
			return false;
	}
	return true;
}

// Check email
function ewr_CheckEmail(object_value) {
	if (!object_value || object_value.length == 0)
		return true;
	object_value = object_value.replace(/^\s*|\s*$/g, "");
	var re = /^[\w.%+-]+@[\w.-]+\.[A-Z]{2,6}$/i;
	return re.test(object_value);
}

// Check GUID {xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx}
function ewr_CheckGUID(object_value) {
	if (!object_value || object_value.length == 0)
		return true;
	object_value = object_value.replace(/^\s*|\s*$/g, "");
	var re = /^\{\w{8}-\w{4}-\w{4}-\w{4}-\w{12}\}$/;
	var re2 = /^\w{8}-\w{4}-\w{4}-\w{4}-\w{12}$/;
	return re.test(object_value) || re2.test(object_value);
}

// Check by regular expression
function ewr_CheckByRegEx(object_value, pattern) {
	if (!object_value || object_value.length == 0)
		return true;
	return (object_value.match(pattern)) ? true : false;
}

// Redirect by HTTP GET or POST
function ewr_Redirect(url, f, method) {
	if (!method || method.toLowerCase() == "post") { // Default
		var $ = jQuery, param = {}, $form = (f) ? $(f) : $("<form></form>").appendTo("body");
		$form.attr({ action: url.split("?")[0], method: "post" });
		url += (url.split("?").length > 1 ? "&" : "?") + "token=" + EWR_TOKEN;
		url.replace(/(?:\?|&)([^&=]*)=?([^&]*)/g, function ($0, $1, $2) {
			if ($1)
				$('<input type="hidden">').attr({ name: $1, value: $2 }).appendTo($form);
		});
		$form.submit();
	} else {
		window.location = url;
	}
}

// Create calendar
function ewr_CreateCalendar(index, el) {
	var $ = jQuery, $el = $(el),
		format = $el.data("format"),
		$btn = $('<button type="button"><span class="glyphicon glyphicon-calendar"></span></button>')
		.addClass("btn btn-default btn-sm").css({ "font-size": $el.css("font-size"), "height": $el.outerHeight() });
	if (!Calendar._DN)
		$.getScript(EWR_RELATIVE_PATH + "jscalendar/lang/calendar-en.js");
	$el.data("calendar", Calendar.setup({
		inputField: el, // input field
		showsTime: / %H:%M(:%S)?$/.test(format), // shows time
		ifFormat: format, // date format
		button: $btn[0], // button
		cache: true // reuse the same calendar object, where possible
	})).wrap('<div class="input-group"></div>').after($('<span class="input-group-btn"></span>').append($btn));
}

/**
* Parse a UA string. Called at instantiation to populate jQuery.ua
* Based on http://yuilibrary.com/yui/docs/api/files/yui_js_yui-ua.js.html
*
* @param {String} [subUA=navigator.userAgent] UA string to parse
* @return {Object} The UA object
*/

function ewr_UserAgent(subUA) {
    var numberify = function (s) {
        var c = 0;
        return parseFloat(s.replace(/\./g, function () {
            return (c++ === 1) ? '' : '.';
        }));
    },
	win = window,
	nav = win && win.navigator,
	o = {

		/**
            * Internet Explorer version number or 0.  Example: 6
            * @property ie
            * @type float
            * @static
            */
		ie: 0,

		/**
            * Opera version number or 0.  Example: 9.2
            * @property opera
            * @type float
            * @static
            */
		opera: 0,

		/**
            * Gecko engine revision number.  Will evaluate to 1 if Gecko
            * is detected but the revision could not be found. Other browsers
            * will be 0.  Example: 1.8
            * <pre>
            * Firefox 1.0.0.4: 1.7.8   <-- Reports 1.7
            * Firefox 1.5.0.9: 1.8.0.9 <-- 1.8
            * Firefox 2.0.0.3: 1.8.1.3 <-- 1.81
            * Firefox 3.0   <-- 1.9
            * Firefox 3.5   <-- 1.91
            * </pre>
            * @property gecko
            * @type float
            * @static
            */
		gecko: 0,

		/**
            * AppleWebKit version.  KHTML browsers that are not WebKit browsers
            * will evaluate to 1, other browsers 0.  Example: 418.9
            * <pre>
            * Safari 1.3.2 (312.6): 312.8.1 <-- Reports 312.8 -- currently the
            *                                   latest available for Mac OSX 10.3.
            * Safari 2.0.2:         416     <-- hasOwnProperty introduced
            * Safari 2.0.4:         418     <-- preventDefault fixed
            * Safari 2.0.4 (419.3): 418.9.1 <-- One version of Safari may run
            *                                   different versions of webkit
            * Safari 2.0.4 (419.3): 419     <-- Tiger installations that have been
            *                                   updated, but not updated
            *                                   to the latest patch.
            * Webkit 212 nightly:   522+    <-- Safari 3.0 precursor (with native
            * SVG and many major issues fixed).
            * Safari 3.0.4 (523.12) 523.12  <-- First Tiger release - automatic
            * update from 2.x via the 10.4.11 OS patch.
            * Webkit nightly 1/2008:525+    <-- Supports DOMContentLoaded event.
            *                                   yahoo.com user agent hack removed.
            * </pre>
            * http://en.wikipedia.org/wiki/Safari_version_history
            * @property webkit
            * @type float
            * @static
            */
		webkit: 0,

		/**
            * Safari will be detected as webkit, but this property will also
            * be populated with the Safari version number
            * @property safari
            * @type float
            * @static
            */
		safari: 0,

		/**
            * Chrome will be detected as webkit, but this property will also
            * be populated with the Chrome version number
            * @property chrome
            * @type float
            * @static
            */
		chrome: 0,

		/**
            * The mobile property will be set to a string containing any relevant
            * user agent information when a modern mobile browser is detected.
            * Currently limited to Safari on the iPhone/iPod Touch, Nokia N-series
            * devices with the WebKit-based browser, and Opera Mini.
            * @property mobile
            * @type string
            * @default null
            * @static
            */
		mobile: null,

		/**
            * Adobe AIR version number or 0.  Only populated if webkit is detected.
            * Example: 1.0
            * @property air
            * @type float
            */
		air: 0,

		/**
            * PhantomJS version number or 0.  Only populated if webkit is detected.
            * Example: 1.0
            * @property phantomjs
            * @type float
            */
		phantomjs: 0,

		/**
            * Detects Apple iPad's OS version
            * @property ipad
            * @type float
            * @static
            */
		ipad: 0,

		/**
            * Detects Apple iPhone's OS version
            * @property iphone
            * @type float
            * @static
            */
		iphone: 0,

		/**
            * Detects Apples iPod's OS version
            * @property ipod
            * @type float
            * @static
            */
		ipod: 0,

		/**
            * General truthy check for iPad, iPhone or iPod
            * @property ios
            * @type Boolean
            * @default null
            * @static
            */
		ios: null,

		/**
            * Detects Googles Android OS version
            * @property android
            * @type float
            * @static
            */
		android: 0,

		/**
            * Detects Kindle Silk
            * @property silk
            * @type float
            * @static
            */
		silk: 0,
		/**
            * Detects Ubuntu version
            * @property ubuntu
            * @type float
            * @static
            */
		ubuntu: 0,
		/**
            * Detects Kindle Silk Acceleration
            * @property accel
            * @type Boolean
            * @static
            */
		accel: false,

		/**
            * Detects Palms WebOS version
            * @property webos
            * @type float
            * @static
            */
		webos: 0,

		/**
            * Google Caja version number or 0.
            * @property caja
            * @type float
            */
		caja: nav && nav.cajaVersion,

		/**
            * Set to true if the page appears to be in SSL
            * @property secure
            * @type boolean
            * @static
            */
		secure: false,

		/**
            * The operating system.
            *
            * Possible values are `windows`, `macintosh`, `android`, `symbos`, `linux`, `rhino` and `ios`.
            *
            * @property os
            * @type string
            * @default null
            * @static
            */
		os: null,

		/**
            * The Nodejs Version
            * @property nodejs
            * @type float
            * @default 0
            * @static
            */
		nodejs: 0,

		/**
        * Window8/IE10 Application host environment
        * @property winjs
        * @type Boolean
        * @static
        */
		winjs: !!((typeof Windows !== "undefined") && Windows.System),

		/**
        * Are touch/msPointer events available on this device
        * @property touchEnabled
        * @type Boolean
        * @static
        */
		touchEnabled: false
	},
	ua = subUA || nav && nav.userAgent,
	loc = win && win.location,
	href = loc && loc.href,
	m;

    /**
	* The User Agent string that was parsed
	* @property userAgent
	* @type String
	* @static
	*/
    o.userAgent = ua;
    o.secure = href && (href.toLowerCase().indexOf('https') === 0);
    if (ua) {
        if ((/windows|win32/i).test(ua)) {
            o.os = 'windows';
        } else if ((/macintosh|mac_powerpc/i).test(ua)) {
            o.os = 'macintosh';
        } else if ((/android/i).test(ua)) {
            o.os = 'android';
        } else if ((/symbos/i).test(ua)) {
            o.os = 'symbos';
        } else if ((/linux/i).test(ua)) {
            o.os = 'linux';
        } else if ((/rhino/i).test(ua)) {
            o.os = 'rhino';
        }

        // Modern KHTML browsers should qualify as Safari X-Grade
        if ((/KHTML/).test(ua)) {
            o.webkit = 1;
        }
        if ((/IEMobile|XBLWP7/).test(ua)) {
            o.mobile = 'windows';
        }
        if ((/Fennec/).test(ua)) {
            o.mobile = 'gecko';
        }

        // Modern WebKit browsers are at least X-Grade
        m = ua.match(/AppleWebKit\/([^\s]*)/);
        if (m && m[1]) {
            o.webkit = numberify(m[1]);
            o.safari = o.webkit;
            if (/PhantomJS/.test(ua)) {
                m = ua.match(/PhantomJS\/([^\s]*)/);
                if (m && m[1]) {
                    o.phantomjs = numberify(m[1]);
                }
            }

            // Mobile browser check
            if (/ Mobile\//.test(ua) || (/iPad|iPod|iPhone/).test(ua)) {
                o.mobile = 'Apple'; // iPhone or iPod Touch
                m = ua.match(/OS ([^\s]*)/);
                if (m && m[1]) {
                    m = numberify(m[1].replace('_', '.'));
                }
                o.ios = m;
                o.os = 'ios';
                o.ipad = o.ipod = o.iphone = 0;
                m = ua.match(/iPad|iPod|iPhone/);
                if (m && m[0]) {
                    o[m[0].toLowerCase()] = o.ios;
                }
            } else {
                m = ua.match(/NokiaN[^\/]*|webOS\/\d\.\d/);
                if (m) {

                    // Nokia N-series, webOS, ex: NokiaN95
                    o.mobile = m[0];
                }
                if (/webOS/.test(ua)) {
                    o.mobile = 'WebOS';
                    m = ua.match(/webOS\/([^\s]*);/);
                    if (m && m[1]) {
                        o.webos = numberify(m[1]);
                    }
                }
                if (/ Android/.test(ua)) {
                    o.mobile = 'Android';
                    m = ua.match(/Android ([^\s]*);/);
                    if (m && m[1]) {
                        o.android = numberify(m[1]);
                    }
                }
                if (/Silk/.test(ua)) {
                    m = ua.match(/Silk\/([^\s]*)/);
                    if (m && m[1]) {
                        o.silk = numberify(m[1]);
                    }
                    if (!o.android) {
                        o.android = 2.34; //Hack for desktop mode in Kindle
                        o.os = 'Android';
                    }
                    if (/Accelerated=true/.test(ua)) {
                        o.accel = true;
                    }
                }
            }

            m = ua.match(/OPR\/(\d+\.\d+)/);

            if (m && m[1]) {
                // Opera 15+ with Blink (pretends to be both Chrome and Safari)
                o.opera = numberify(m[1]);
            } else {
                m = ua.match(/(Chrome|CrMo|CriOS)\/([^\s]*)/);
                if (m && m[1] && m[2]) {
                    o.chrome = numberify(m[2]); // Chrome
                    o.safari = 0; //Reset safari back to 0
                    if (m[1] === 'CrMo') {
                        o.mobile = 'chrome';
                    }
                } else {
                    m = ua.match(/AdobeAIR\/([^\s]*)/);
                    if (m) {
                        o.air = m[0]; // Adobe AIR 1.0 or better
                    }
                }
            }
        }

        m = ua.match(/Ubuntu\ (\d+\.\d+)/);
        if (m && m[1]) {

            o.os = 'linux';
            o.ubuntu = numberify(m[1]);

            m = ua.match(/\ WebKit\/([^\s]*)/);
            if (m && m[1]) {
                o.webkit = numberify(m[1]);
            }
            m = ua.match(/\ Chromium\/([^\s]*)/);
            if (m && m[1]) {
                o.chrome = numberify(m[1]);
            }
            if (/ Mobile$/.test(ua)) {
                o.mobile = 'Ubuntu';
            }
        }

        if (!o.webkit) { // not webkit
            // @todo check Opera/8.01 (J2ME/MIDP; Opera Mini/2.0.4509/1316; fi; U; ssr)
            if (/Opera/.test(ua)) {
                m = ua.match(/Opera[\s\/]([^\s]*)/);
                if (m && m[1]) {
                    o.opera = numberify(m[1]);
                }
                m = ua.match(/Version\/([^\s]*)/);
                if (m && m[1]) {
                    o.opera = numberify(m[1]); // opera 10+
                }
                if (/Opera Mobi/.test(ua)) {
                    o.mobile = 'opera';
                    m = ua.replace('Opera Mobi', '').match(/Opera ([^\s]*)/);
                    if (m && m[1]) {
                        o.opera = numberify(m[1]);
                    }
                }
                m = ua.match(/Opera Mini[^;]*/);
                if (m) {
                    o.mobile = m[0]; // ex: Opera Mini/2.0.4509/1316
                }
            } else { // not opera or webkit
                m = ua.match(/MSIE ([^;]*)|Trident.*; rv:([0-9.]+)/);

                if (m && (m[1] || m[2])) {
                    o.ie = numberify(m[1] || m[2]);
                } else { // not opera, webkit, or ie
                    m = ua.match(/Gecko\/([^\s]*)/);
                    if (m) {
                        o.gecko = 1; // Gecko detected, look for revision
                        m = ua.match(/rv:([^\s\)]*)/);
                        if (m && m[1]) {
                            o.gecko = numberify(m[1]);
                            if (/Mobile|Tablet/.test(ua)) {
                                o.mobile = "ffos";
                            }
                        }
                    }
                }
            }
        }
    }

    //Check for known properties to tell if touch events are enabled on this device or if
    //the number of MSPointer touchpoints on this device is greater than 0.

    if (win && nav && !(o.chrome && o.chrome < 6)) {
        o.touchEnabled = (("ontouchstart" in win) || (("msMaxTouchPoints" in nav) && (nav.msMaxTouchPoints > 0)));
    }

    //It was a parsed UA, do not assign the global value.
    if (!subUA) {
        if (typeof process === 'object') {
            if (process.versions && process.versions.node) {

                //NodeJS
                o.os = process.platform;
                o.nodejs = numberify(process.versions.node);
            }
        }
    }
    return o;
}

// Extend jQuery
function ewr_Extend(jQuery) {
	jQuery.extend({
		isBoolean: function(o) {
			return typeof o === 'boolean';
		},
		isNull: function(o) {
			return o === null;
		},
		isNumber: function(o) {
			return typeof o === 'number' && isFinite(o);
		},
		isObject: function(o) {
			return (o && (typeof o === 'object' || this.isFunction(o))) || false;
		},
		isString: function(o) {
			return typeof o === 'string';
		},
		isUndefined: function(o) {
			return typeof o === 'undefined';
		},
		isValue: function(o) {
			return (this.isObject(o) || this.isString(o) || this.isNumber(o) || this.isBoolean(o));
		},
		isDate: function(o) {
			return this.type(o) === 'date' && o.toString() !== 'Invalid Date' && !isNaN(o);
		},
		later: function(when, o, fn, data, periodic) {
			when = when || 0;
			o = o || {};
			var m = fn, d = data, f, r;
			if (this.isString(fn))
				m = o[fn];
			if (!m)
				return;
			if (!this.isUndefined(data) && !this.isArray(d))
				d = [data];
			f = function() {
				m.apply(o, d || []);
			};
			r = (periodic) ? setInterval(f, when) : setTimeout(f, when);
			return {
				interval: periodic,
				cancel: function() {
					if (this.interval) {
						clearInterval(r);
					} else {
						clearTimeout(r);
					}
				}
			};
		},
		ua: ewr_UserAgent()
	});
	
	/*! 
	 * jquery.event.drag - v 2.2
	 * Copyright (c) 2010 Three Dub Media - http://threedubmedia.com
	 * Open Source MIT License - http://threedubmedia.com/code/license
	 */
	(function( $ ){
	
	// Add the jquery instance method
	$.fn.drag = function( str, arg, opts ){
	
		// Figure out the event type
		var type = typeof str == "string" ? str : "",
	
		// Figure out the event handler...
		fn = jQuery.isFunction( str ) ? str : jQuery.isFunction( arg ) ? arg : null;
	
		// Fix the event type
		if ( type.indexOf("drag") !== 0 ) 
			type = "drag"+ type;
	
		// Were options passed
		opts = ( str == fn ? arg : opts ) || {};
	
		// Trigger or bind event handler
		return fn ? this.bind( type, opts, fn ) : this.trigger( type );
	};
	
	// Local refs (increase compression)
	var $event = $.event, 
	$special = $event.special,
	
	// Configure the drag special event 
	drag = $special.drag = {
	
		// These are the default settings
		defaults: {
			which: 1, // Mouse button pressed to start drag sequence
			distance: 0, // Distance dragged before dragstart
			not: ':input', // Selector to suppress dragging on target elements
			handle: null, // Selector to match handle target elements
			relative: false, //  true to use "position", false to use "offset"
			drop: true, //  false to suppress drop events, true or selector to allow
			click: false //  false to suppress click events after dragend (no proxy)
		},
	
		// The key name for stored drag data
		datakey: "dragdata",
	
		// Prevent bubbling for better performance
		noBubble: true,
	
		// Count bound related events
		add: function( obj ){ 
	
			// Read the interaction data
			var data = $.data( this, drag.datakey ),
	
			// Read any passed options 
			opts = obj.data || {};
	
			// Count another realted event
			data.related += 1;
	
			// Extend data options bound with this event
			// Don't iterate "opts" in case it is a node 
	
			$.each( drag.defaults, function( key, def ){
				if ( opts[ key ] !== undefined )
					data[ key ] = opts[ key ];
			});
		},
	
		// Forget unbound related events
		remove: function(){
			$.data( this, drag.datakey ).related -= 1;
		},
	
		// Configure interaction, capture settings
		setup: function(){
	
			// Check for related events
			if ( $.data( this, drag.datakey ) ) 
				return;
	
			// Initialize the drag data with copied defaults
			var data = $.extend({ related:0 }, drag.defaults );
	
			// Store the interaction data
			$.data( this, drag.datakey, data );
	
			// Bind the mousedown event, which starts drag interactions
			$event.add( this, "touchstart mousedown", drag.init, data );
	
			// Prevent image dragging in IE...
			if ( this.attachEvent ) 
				this.attachEvent("ondragstart", drag.dontstart ); 
		},
	
		// Destroy configured interaction
		teardown: function(){
			var data = $.data( this, drag.datakey ) || {};
	
			// Check for related events
			if ( data.related ) 
				return;
	
			// Remove the stored data
			$.removeData( this, drag.datakey );
	
			// Remove the mousedown event
			$event.remove( this, "touchstart mousedown", drag.init );
	
			// Enable text selection
			drag.textselect( true ); 
	
			// Un-prevent image dragging in IE...
			if ( this.detachEvent ) 
				this.detachEvent("ondragstart", drag.dontstart ); 
		},
	
		// Initialize the interaction
		init: function( event ){ 
	
			// Sorry, only one touch at a time
			if ( drag.touched ) 
				return;
	
			// The drag/drop interaction data
			var dd = event.data, results;
	
			// Check the which directive
			if ( event.which != 0 && dd.which > 0 && event.which != dd.which ) 
				return; 
	
			// Check for suppressed selector
			if ( $( event.target ).is( dd.not ) ) 
				return;
	
			// Check for handle selector
			if ( dd.handle && !$( event.target ).closest( dd.handle, event.currentTarget ).length ) 
				return;
			drag.touched = event.type == 'touchstart' ? this : null;
			dd.propagates = 1;
			dd.mousedown = this;
			dd.interactions = [ drag.interaction( this, dd ) ];
			dd.target = event.target;
			dd.pageX = event.pageX;
			dd.pageY = event.pageY;
			dd.dragging = null;
	
			// Handle draginit event... 
			results = drag.hijack( event, "draginit", dd );
	
			// Early cancel
			if ( !dd.propagates )
				return;
	
			// Flatten the result set
			results = drag.flatten( results );
	
			// Insert new interaction elements
			if ( results && results.length ){
				dd.interactions = [];
				$.each( results, function(){
					dd.interactions.push( drag.interaction( this, dd ) );
				});
			}
	
			// Remember how many interactions are propagating
			dd.propagates = dd.interactions.length;
	
			// Locate and init the drop targets
			if ( dd.drop !== false && $special.drop ) 
				$special.drop.handler( event, dd );
	
			// Disable text selection
			drag.textselect( false ); 
	
			// Bind additional events...
			if ( drag.touched )
				$event.add( drag.touched, "touchmove touchend", drag.handler, dd );
			else 
				$event.add( document, "mousemove mouseup", drag.handler, dd );
	
			// Helps prevent text selection or scrolling
			if ( !drag.touched || dd.live )
				return false;
		},	
	
		// Returns an interaction object
		interaction: function( elem, dd ){
			var offset = $( elem )[ dd.relative ? "position" : "offset" ]() || { top:0, left:0 };
			return {
				drag: elem, 
				callback: new drag.callback(), 
				droppable: [],
				offset: offset
			};
		},
	
		// Handle drag-releatd DOM events
		handler: function( event ){ 
	
			// Read the data before hijacking anything
			var dd = event.data;	
	
			// Handle various events
			switch ( event.type ){
	
				// Mousemove, check distance, start dragging
				case !dd.dragging && 'touchmove': 
					event.preventDefault();
				case !dd.dragging && 'mousemove':
	
					//  drag tolerance, x?+ y?= distance?
					if ( Math.pow(  event.pageX-dd.pageX, 2 ) + Math.pow(  event.pageY-dd.pageY, 2 ) < Math.pow( dd.distance, 2 ) ) 
						break; // Distance tolerance not reached
					event.target = dd.target; // Force target from "mousedown" event (fix distance issue)
					drag.hijack( event, "dragstart", dd ); // Trigger "dragstart"
					if ( dd.propagates ) // "dragstart" not rejected
						dd.dragging = true; // Activate interaction
	
				// Mousemove, dragging
				case 'touchmove':
					event.preventDefault();
				case 'mousemove':
					if ( dd.dragging ){
	
						// Trigger "drag"		
						drag.hijack( event, "drag", dd );
						if ( dd.propagates ){
	
							// Manage drop events
							if ( dd.drop !== false && $special.drop )
								$special.drop.handler( event, dd ); // "dropstart", "dropend"							
							break; // "drag" not rejected, stop		
						}
						event.type = "mouseup"; // Helps "drop" handler behave
					}
	
				// Mouseup, stop dragging
				case 'touchend': 
				case 'mouseup': 
				default:
					if ( drag.touched )
						$event.remove( drag.touched, "touchmove touchend", drag.handler ); // Remove touch events
					else 
						$event.remove( document, "mousemove mouseup", drag.handler ); // Remove page events	
					if ( dd.dragging ){
						if ( dd.drop !== false && $special.drop )
							$special.drop.handler( event, dd ); // "drop"
						drag.hijack( event, "dragend", dd ); // Trigger "dragend"	
					}
					drag.textselect( true ); // Enable text selection
	
					// If suppressing click events...
					if ( dd.click === false && dd.dragging )
						$.data( dd.mousedown, "suppress.click", new Date().getTime() + 5 );
					dd.dragging = drag.touched = false; // Deactivate element	
					break;
			}
		},
	
		// Re-use event object for custom events
		hijack: function( event, type, dd, x, elem ){
	
			// Not configured
			if ( !dd ) 
				return;
	
			// Remember the original event and type
			var orig = { event:event.originalEvent, type:event.type },
	
			// Is the event drag related or drog related?
			mode = type.indexOf("drop") ? "drag" : "drop",
	
			// Iteration vars
			result, i = x || 0, ia, $elems, callback,
			len = !isNaN( x ) ? x : dd.interactions.length;
	
			// Modify the event type
			event.type = type;
	
			// Remove the original event
			event.originalEvent = null;
	
			// Initialize the results
			dd.results = [];
	
			// Handle each interacted element
			do if ( ia = dd.interactions[ i ] ){
	
				// Validate the interaction
				if ( type !== "dragend" && ia.cancelled )
					continue;
	
				// Set the dragdrop properties on the event object
				callback = drag.properties( event, dd, ia );
	
				// Prepare for more results
				ia.results = [];
	
				// Handle each element
				$( elem || ia[ mode ] || dd.droppable ).each(function( p, subject ){
	
					// Identify drag or drop targets individually
					callback.target = subject;
	
					// Force propagtion of the custom event
					event.isPropagationStopped = function(){ return false; };
	
					// Handle the event	
					result = subject ? $event.dispatch.call( subject, event, callback ) : null;
	
					// Stop the drag interaction for this element
					if ( result === false ){
						if ( mode == "drag" ){
							ia.cancelled = true;
							dd.propagates -= 1;
						}
						if ( type == "drop" ){
							ia[ mode ][p] = null;
						}
					}
	
					// Assign any dropinit elements
					else if ( type == "dropinit" )
						ia.droppable.push( drag.element( result ) || subject );
	
					// Accept a returned proxy element 
					if ( type == "dragstart" )
						ia.proxy = $( drag.element( result ) || ia.drag )[0];
	
					// Remember this result	
					ia.results.push( result );
	
					// Forget the event result, for recycling
					delete event.result;
	
					// Break on cancelled handler
					if ( type !== "dropinit" )
						return result;
				});	
	
				// Flatten the results	
				dd.results[ i ] = drag.flatten( ia.results );	
	
				// Accept a set of valid drop targets
				if ( type == "dropinit" )
					ia.droppable = drag.flatten( ia.droppable );
	
				// Locate drop targets
				if ( type == "dragstart" && !ia.cancelled )
					callback.update(); 
			}
			while ( ++i < len )
	
			// Restore the original event & type
			event.type = orig.type;
			event.originalEvent = orig.event;
	
			// Return all handler results
			return drag.flatten( dd.results );
		},
	
		// Extend the callback object with drag/drop properties...
		properties: function( event, dd, ia ){		
			var obj = ia.callback;
	
			// Elements
			obj.drag = ia.drag;
			obj.proxy = ia.proxy || ia.drag;
	
			// Starting mouse position
			obj.startX = dd.pageX;
			obj.startY = dd.pageY;
	
			// Current distance dragged
			obj.deltaX = event.pageX - dd.pageX;
			obj.deltaY = event.pageY - dd.pageY;
	
			// Original element position
			obj.originalX = ia.offset.left;
			obj.originalY = ia.offset.top;
	
			// Adjusted element position
			obj.offsetX = obj.originalX + obj.deltaX; 
			obj.offsetY = obj.originalY + obj.deltaY;
	
			// Assign the drop targets information
			obj.drop = drag.flatten( ( ia.drop || [] ).slice() );
			obj.available = drag.flatten( ( ia.droppable || [] ).slice() );
			return obj;	
		},
	
		// Determine is the argument is an element or jquery instance
		element: function( arg ){
			if ( arg && ( arg.jquery || arg.nodeType == 1 ) )
				return arg;
		},
	
		// Flatten nested jquery objects and arrays into a single dimension array
		flatten: function( arr ){
			return $.map( arr, function( member ){
				return member && member.jquery ? $.makeArray( member ) : 
					member && member.length ? drag.flatten( member ) : member;
			});
		},
	
		// Toggles text selection attributes ON (true) or OFF (false)
		textselect: function( bool ){ 
			$( document )[ bool ? "unbind" : "bind" ]("selectstart", drag.dontstart )
				.css("MozUserSelect", bool ? "" : "none" );
	
			// .attr("unselectable", bool ? "off" : "on" )
			document.unselectable = bool ? "off" : "on"; 
		},
	
		// Suppress "selectstart" and "ondragstart" events
		dontstart: function(){ 
			return false; 
		},
	
		// Callback instance contructor
		callback: function(){}
	};
	
	// Callback methods
	drag.callback.prototype = {
		update: function(){
			if ( $special.drop && this.available.length )
				$.each( this.available, function( i ){
					$special.drop.locate( this, i );
				});
		}
	};
	
	// Patch $.event.$dispatch to allow suppressing clicks
	var $dispatch = $event.dispatch;
	$event.dispatch = function( event ){
		if ( $.data( this, "suppress."+ event.type ) - new Date().getTime() > 0 ){
			$.removeData( this, "suppress."+ event.type );
			return;
		}
		return $dispatch.apply( this, arguments );
	};
	
	// Event fix hooks for touch events...
	var touchHooks = 
	$event.fixHooks.touchstart = 
	$event.fixHooks.touchmove = 
	$event.fixHooks.touchend =
	$event.fixHooks.touchcancel = {
		props: "clientX clientY pageX pageY screenX screenY".split( " " ),
		filter: function( event, orig ) {
			if ( orig ){
				var touched = ( orig.touches && orig.touches[0] )
					|| ( orig.changedTouches && orig.changedTouches[0] )
					|| null; 
	
				//  iOS webkit: touchstart, touchmove, touchend
				if ( touched ) 
					$.each( touchHooks.props, function( i, prop ){
						event[ prop ] = touched[ prop ];
					});
			}
			return event;
		}
	};
	
	// Share the same special event configuration with related events...
	$special.draginit = $special.dragstart = $special.dragend = drag;
	})( jQuery );
}
ewr_Extend(jQuery);

// Dropdown based on Bootstrap
+function ($) {
  'use strict';

  // DROPDOWN CLASS DEFINITION
  // =========================

  var backdrop = '.dropdown-backdrop'
  //var toggle = '#ewMobileMenu .ewDropdown'
  var toggle = '.ewDropdown'
  var Dropdown = function (element) {
    $(element).on('click.bs.dropdown', this.toggle)
  }

  Dropdown.prototype.toggle = function (e) {
    var $this = $(this)

    if ($this.is('.disabled, :disabled')) return

    var $parent  = getParent($this)
    var isActive = $parent.hasClass('open')

    clearMenus(e)

    if (!isActive) {
		if ('ontouchstart' in document.documentElement && !$parent.closest('.navbar-nav').length) {
			// if mobile we use a backdrop because click events don't delegate
			$('<div class="dropdown-backdrop"/>').insertAfter($(this)).on('click', clearMenus)
		}
		
		var relatedTarget = { relatedTarget: this }
		$parent.trigger(e = $.Event('show.bs.dropdown', relatedTarget))
		
		if (e.isDefaultPrevented()) return
		
		$this.trigger('focus')
		
		$parent
			.toggleClass('open')
			.trigger('shown.bs.dropdown', relatedTarget)
		
		// adjust padding-left of the dropdown menu
		var $next = $this.next();
		if ($next.is('ul.dropdown-menu'))
			$next.find("> li > a").css("padding-left", (parseInt($this.css("padding-left"), 10) + 10) + "px") // add 10px
    }

    return false
  }

  Dropdown.prototype.keydown = function (e) {
    if (!/(38|40|27)/.test(e.keyCode)) return

    var $this = $(this)

    e.preventDefault()
    e.stopPropagation()

    if ($this.is('.disabled, :disabled')) return

    var $parent  = getParent($this)
    var isActive = $parent.hasClass('open')

    if (!isActive || (isActive && e.keyCode == 27)) {
      if (e.which == 27) $parent.find(toggle).trigger('focus')
      return $this.trigger('click')
    }

    var desc = ' li:not(.divider):visible a'
    var $items = $parent.find('[role="menu"]' + desc + ', [role="listbox"]' + desc)

    if (!$items.length) return

    var index = $items.index($items.filter(':focus'))

    if (e.keyCode == 38 && index > 0)                 index--                        // up
    if (e.keyCode == 40 && index < $items.length - 1) index++                        // down
    if (!~index)                                      index = 0

    $items.eq(index).trigger('focus')
  }

  function clearMenus(e) {
    if (e && e.which === 3) return
    $(backdrop).remove()
    $(toggle, $(e.currentTarget).is(toggle) ? $(e.currentTarget).parent() : document).each(function () {
		var $parent = getParent($(this))
		var relatedTarget = { relatedTarget: this }
		if (!$parent.hasClass('open')) return
		$parent.trigger(e = $.Event('hide.bs.dropdown', relatedTarget))
		if (e.isDefaultPrevented()) return
		$parent.removeClass('open').trigger('hidden.bs.dropdown', relatedTarget)
    })
  }

  function getParent($this) {
    var selector = $this.attr('data-target')

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && /#[A-Za-z]/.test(selector) && selector.replace(/.*(?=#[^\s]*$)/, '') //strip for ie7
    }

    var $parent = selector && $(selector)

    return $parent && $parent.length ? $parent : $this.parent()
  }


  // DROPDOWN PLUGIN DEFINITION
  // ==========================

  var old = $.fn.dropdown

  $.fn.dropdown = function (option) {
    return this.each(function () {
      var $this = $(this)
      var data  = $this.data('bs.dropdown')

      if (!data) $this.data('bs.dropdown', (data = new Dropdown(this)))
      if (typeof option == 'string') data[option].call($this)
    })
  }

  $.fn.dropdown.Constructor = Dropdown


  // DROPDOWN NO CONFLICT
  // ====================

  $.fn.dropdown.noConflict = function () {
    $.fn.dropdown = old
    return this
  }


  // APPLY TO STANDARD DROPDOWN ELEMENTS
  // ===================================

  $(document)
    .on('click.bs.dropdown.data-api', clearMenus)
    .on('click.bs.dropdown.data-api', '.dropdown form', function (e) { e.stopPropagation() })
    .on('click.bs.dropdown.data-api', toggle, Dropdown.prototype.toggle)
    .on('keydown.bs.dropdown.data-api', toggle + ', [role="menu"], [role="listbox"]', Dropdown.prototype.keydown)

}(jQuery);

<!--## if (PROJ.GetV("UseDateNumberJs")) { ##-->
<!--##include datenumber.js/datenumberjs##-->
<!--## } ##-->

<!--##/session##-->