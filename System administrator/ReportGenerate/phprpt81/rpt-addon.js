<!--##session js_chart##-->
<!--##=sJsExpStart##-->
<script type="text/javascript">

// Create page object
var <!--##=sPageObj##--> = new ewr_Page("<!--##=sPageObj##-->");

// Page properties
<!--##=sPageObj##-->.PageID = "<!--##=CTRL.CtrlID##-->"; // Page ID
var EWR_PAGE_ID = <!--##=sPageObj##-->.PageID;

<!--## if (SYSTEMFUNCTIONS.ClientScriptExist("Table","Chart_Rendering")) { ##-->
// Extend page with Chart_Rendering function
<!--##=sPageObj##-->.Chart_Rendering = <!--##~SYSTEMFUNCTIONS.GetClientScript("Table","Chart_Rendering")##-->
<!--## } ##-->

<!--## if (SYSTEMFUNCTIONS.ClientScriptExist("Table","Chart_Rendered")) { ##-->
// Extend page with Chart_Rendered function
<!--##=sPageObj##-->.Chart_Rendered = <!--##~SYSTEMFUNCTIONS.GetClientScript("Table","Chart_Rendered")##-->
<!--## } ##-->

</script>
<!--##=sJsExpEnd##-->
<!--##/session##-->


<!--##session js_validate##-->
<!--##
	if (bReportExtFilter || bShowYearSelection || nSearchFlds > 0) {
##-->
<script type="text/javascript">

// Form object
var <!--##=sFormName##--> = new ewr_Form("<!--##=sFormName##-->");

<!--## if (bReportExtFilter || bShowYearSelection) { ##-->

// Validate method
<!--##=sFormName##-->.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	<!--##
		for (var i = 0; i < nFldCount; i++) {
			if (GetFldObj(arFlds[i])) {
				if (IsExtendedFilter(goFld) && IsTextFilter(goFld)) {
	##-->
	<!--##~SYSTEMFUNCTIONS.JsValidator()##-->
	<!--##
				}
			}
		};

		if (SYSTEMFUNCTIONS.ClientScriptExist("Table","Form_CustomValidate")) {
	##-->
	// Call Form Custom Validate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	<!--##
		}
	##--> 
	return true;
}

<!--## if (SYSTEMFUNCTIONS.ClientScriptExist("Table","Form_CustomValidate")) { ##-->
// Form_CustomValidate method
<!--##=sFormName##-->.Form_CustomValidate = <!--##~SYSTEMFUNCTIONS.GetClientScript("Table","Form_CustomValidate")##-->
<!--## } ##-->

<?php if (EWR_CLIENT_VALIDATE) { ?>
<!--##=sFormName##-->.ValidateRequired = true; // Uses JavaScript validation
<?php } else { ?>
<!--##=sFormName##-->.ValidateRequired = false; // No JavaScript validation
<?php } ?>

// Use Ajax
<!--##
	for (var i = 0; i < nFldCount; i++) {
		if (GetFldObj(arFlds[i])) {
			if (IsUseAjax(goFld) || IsAutoSuggest(goFld)) {
				var id = "sv_" + gsFldVar.substr(2);
##-->
<!--##=sFormName##-->.Lists["<!--##=ew_AddSquareBrackets(id, goFld)##-->"] = <!--##=SYSTEMFUNCTIONS.SelectionList()##-->;
<!--##
			}
		}
	}
##-->

<!--## if (PROJ.GetV("SearchPanelCollapsed") && !TABLE.TblShowBlankListPage) { ##-->
// Init search panel as collapsed
if (<!--##=sFormName##-->) <!--##=sFormName##-->.InitSearchPanel = true;
<!--## } ##-->

<!--## } ##-->

</script>
<!--##
	};
##-->
<!--##/session##-->
