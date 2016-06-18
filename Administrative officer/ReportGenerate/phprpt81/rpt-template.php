<!--##session common_config##-->
<!--##
	var bExport = IsExport();
	if (bExport) {
		sTmplExpStart = "<?php if (@$gsExport == \"\") { ?>";
		sTmplExpEnd = "<?php } ?>";
		sTmplHtmlExpStart = "<?php if (@$gsExport == \"\" || @$gsExport == \"print\") { ?>";
		sTmpHtmlExpElse = "<?php } else { ?>";
		sTmplHtmlExpEnd = "<?php } ?>";
		sTmplHtmlEmailExpStart = "<?php if (@$gsExport == \"\" || @$gsExport == \"print\" || @$gsExport == \"email\" && @$gsEmailContentType == \"url\") { ?>";
		sTmplHtmlEmailExpEnd = "<?php } ?>";
	} else {
		sTmplExpStart = "";
		sTmplExpEnd = "";
		sTmplHtmlExpStart = "";
		sTmpHtmlExpElse = "";
		sTmplHtmlExpEnd = "";
		sTmplHtmlEmailExpStart = "";
		sTmplHtmlEmailExpEnd = "";
	}
	sTmplSkipStart = "<?php if (@!$gbSkipHeaderFooter) { ?>";
	sTmplSkipEnd = "<?php } ?>";
	sDrillSkipStart = "<?php if (@!$gbDrillDownInPanel) { ?>"
	sDrillSkipEnd = "<?php } ?>";

	bUseEmailExport = UseEmailExport(); // Export to Email
	bUsePdfExport = UsePdfExport(); // Export PDF

	bUseJSTemplate = UseJSTemplate(); // Use JS Template

	bDisableProjectStyles = PROJ.GetV("DisableProjectStyles");
	
	sBrand = "";
	sBrandHref = "";
	
	sMobileMenuNavbarClass = (PROJ.GetV("ThemeMobileMenuInverted") == "1") ? "navbar-inverse" : "navbar-default";
##-->
<!--##/session##-->

<!--##session header_top##-->
<!--## if (bUseEmailExport || bUsePdfExport) { ##-->
<?php if (@$gsExport == "email" || @$gsExport == "pdf") ob_clean(); ?>
<!--## } ##-->
<!--##
	if (!bGenCompatHeader) {
##-->
<?php
// Responsive layout
if (ewr_IsResponsiveLayout()) {
	$gsHeaderRowClass = "<!--##=sHiddenMobileClass##--> ewHeaderRow";
	$gsMenuColumnClass = "<!--##=sHiddenMobileClass##--> ewMenuColumn";
	$gsSiteTitleClass = "<!--##=sHiddenMobileClass##--> ewSiteTitle";
} else {
	$gsHeaderRowClass = "ewHeaderRow";
	$gsMenuColumnClass = "ewMenuColumn";
	$gsSiteTitleClass = "ewSiteTitle";
}
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $ReportLanguage->ProjectPhrase("BodyTitle") ?></title>
<!--##=SYSTEMFUNCTIONS.CharSet()##-->
<!--##
	}
##-->
<!--##=sTmplHtmlEmailExpStart##-->
<script type="text/javascript">
var EWR_RELATIVE_PATH = "<?php echo $EWR_RELATIVE_PATH ?>";
function ewr_GetScript(url) { document.write("<" + "script type=\"text/javascript\" src=\"" + EWR_RELATIVE_PATH + url + "\"><" + "/script>"); }
function ewr_GetCss(url) { document.write("<link rel=\"stylesheet\" type=\"text/css\" href=\"" + EWR_RELATIVE_PATH + url + "\">"); }
var EWR_LANGUAGE_ID = "<?php echo $gsLanguage ?>";
var EWR_DATE_SEPARATOR = "<!--##=PROJ.DateSeparator || "/"##-->"; // Default date separator
var EWR_DECIMAL_POINT = "<?php echo $EWR_DEFAULT_DECIMAL_POINT ?>";
var EWR_THOUSANDS_SEP = "<?php echo $EWR_DEFAULT_THOUSANDS_SEP ?>";
	<!--## if (bUseEmailExport) { ##-->
var EWR_MAX_EMAIL_RECIPIENT = <?php echo EWR_MAX_EMAIL_RECIPIENT ?>;
	<!--## } ##-->
var EWR_DISABLE_BUTTON_ON_SUBMIT = <!--##=ew_JsVal(bDisableButtonOnSubmit)##-->;
var EWR_IMAGES_FOLDER = "<!--##=ew_FolderPath("_images")##-->/"; // Image folder
var EWR_LOOKUP_FILE_NAME = "<!--##=ew_GetFileNameByCtrlID("lookup", false)##-->"; // Lookup file name
var EWR_AUTO_SUGGEST_MAX_ENTRIES = <?php echo EWR_AUTO_SUGGEST_MAX_ENTRIES ?>; // Auto-Suggest max entries
var EWR_USE_JAVASCRIPT_MESSAGE = <!--##=ew_JsVal(bUseJavaScriptMessage)##-->;
<?php if (ewr_IsMobile()) { ?>
var EWR_IS_MOBILE = true;
<?php } else { ?>
var EWR_IS_MOBILE = false;
<?php } ?>
var EWR_PROJECT_STYLESHEET_FILENAME = "<?php echo EWR_PROJECT_STYLESHEET_FILENAME ?>"; // Project style sheet
var EWR_PDF_STYLESHEET_FILENAME = "<?php echo (EWR_PDF_STYLESHEET_FILENAME == "" ? EWR_PROJECT_STYLESHEET_FILENAME : EWR_PDF_STYLESHEET_FILENAME) ?>"; // Export PDF style sheet
var EWR_TOKEN = "<?php echo @$gsToken ?>";
var EWR_CSS_FLIP = <?php echo (EWR_CSS_FLIP) ? "true" : "false" ?>;
</script>
<!--##=sTmplHtmlEmailExpEnd##-->
<?php if (@$gsExport == "" || @$gsExport == "print") { ?>
<script type="text/javascript">
if (!window.jQuery || !jQuery.fn.alert) {
	ewr_GetCss("bootstrap3/css/<?php echo ewr_CssFile("bootstrap.css") ?>");
<!--## if (PROJ.GetV("ThemeUseBootstrapTheme") == "1") { ##-->
	ewr_GetCss("bootstrap3/css/<?php echo ewr_CssFile("bootstrap-theme.css") ?>"); // Optional theme
<!--## } ##-->
}
<!--## if (PROJ.GetV("UseColorbox")) { ##-->
ewr_GetCss("colorbox/colorbox.css");
<!--## } ##-->  
<!--## if (!bDisableProjectStyles) { ##-->
<?php if (!@$gbDrillDownInPanel) { ?>
ewr_GetCss("<?php echo ewr_CssFile(EWR_PROJECT_STYLESHEET_FILENAME) ?>");
<!--##include rpt-menuext.php/menuextcss##-->
<?php } ?>
<!--## } ##-->
</script>
<!--## if (!bDisableProjectStyles) { ##-->
<?php if (ewr_IsMobile()) { ?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php } ?>
<!--## } ##-->
<!--##=SYSTEMFUNCTIONS.CSSFile()##-->
<?php } else { ?>
<style type="text/css">
<?php $cssfile = (@$gsExport == "pdf") ? (EWR_PDF_STYLESHEET_FILENAME == "" ? EWR_PROJECT_STYLESHEET_FILENAME : EWR_PDF_STYLESHEET_FILENAME) : EWR_PROJECT_STYLESHEET_FILENAME ?>
<?php echo file_get_contents($cssfile) ?>
</style>
<?php } ?>
<!--##=sTmplExpStart##-->
<!--##
    if (IsPopupCalendar()) {
##-->
<script type="text/javascript">
if (!window.Calendar) {
	ewr_GetCss("jscalendar/calendar.css");
	ewr_GetScript("jscalendar/calendar.min.js");
	ewr_GetScript("jscalendar/lang/calendar-en.js");
	ewr_GetScript("jscalendar/calendar-setup.js");
}
</script>
<!--##
    }
##-->
<!--##=sTmplExpEnd##-->
<!--##=sTmplHtmlEmailExpStart##-->
<script type="text/javascript">if (!window.jQuery) ewr_GetScript("<!--##=ew_FolderPath("_jquery")##-->/jquery-1.11.3.min.js");</script>
<!--##=sTmplHtmlEmailExpEnd##-->
<!--##/session##-->

<!--##session header_top_2##-->
<!--##=sTmplHtmlEmailExpStart##-->
<?php if (@$gsCustomExport == "") { ?>
<script type="text/javascript" src="<?php echo $EWR_RELATIVE_PATH . EWR_FUSIONCHARTS_PATH ?>fusioncharts.js"></script>
<script type="text/javascript" src="<?php echo $EWR_RELATIVE_PATH . EWR_FUSIONCHARTS_PATH ?>themes/fusioncharts.theme.ocean.js"></script>
<script type="text/javascript" src="<?php echo $EWR_RELATIVE_PATH . EWR_FUSIONCHARTS_PATH ?>themes/fusioncharts.theme.carbon.js"></script>
<script type="text/javascript" src="<?php echo $EWR_RELATIVE_PATH . EWR_FUSIONCHARTS_PATH ?>themes/fusioncharts.theme.zune.js"></script>
<script type="text/javascript" src="<?php echo $EWR_RELATIVE_PATH . EWR_FUSIONCHARTS_FREE_JSCLASS_FILE ?>"></script>
<script type="text/javascript">
var EWR_CHART_EXPORT_HANDLER = "<?php echo ewr_ConvertFullUrl("<!--##=ew_GetFileNameByCtrlID("FusionChartsExportHandler")##-->") ?>";
</script>
<?php } ?>
<script type="text/javascript">if (window.jQuery && !window.jQuery.browser) ewr_GetScript("<!--##=ew_FolderPath("_jquery")##-->/jquery.browser.js");</script>
<script type="text/javascript">if (window.jQuery && !window.jQuery.iframeAutoHeight) ewr_GetScript("<!--##=ew_FolderPath("_jquery")##-->/jquery.iframe-auto-height.js");</script>
<script type="text/javascript">if (window.jQuery && !window.jQuery.localStorage) ewr_GetScript("<!--##=ew_FolderPath("_jquery")##-->/jquery.storageapi.min.js");</script>
<!--## if (PROJ.GetV("UseColorbox")) { ##-->
<?php if (@$gsExport == "") { ?>
<script type="text/javascript">if (window.jQuery && !jQuery.colorbox) ewr_GetScript("colorbox/jquery.colorbox-min.js");</script>
<?php } ?>
<!--## } ##-->	
<script type="text/javascript">ewr_GetScript("<!--##=ew_FolderPath("_js")##-->/<!--##=ew_GetFileNameByCtrlID("ewr.js", false)##-->");</script>
<!--## if (bUseJSTemplate) { ##-->
<?php if (@$gsExport <> "") { ?>
<script type="text/javascript">if (window.jQuery && !window.jQuery.views) ewr_GetScript("<!--##=ew_FolderPath("_js")##-->/<!--##=ew_GetFileNameByCtrlID("jsrender.min.js", false)##-->");</script>
<?php } ?>
<!--## } ##-->
<script type="text/javascript">
if (window._jQuery) ewr_Extend(jQuery);
if (window.jQuery && !jQuery.fn.alert) ewr_GetScript("bootstrap3/js/bootstrap.min.js");
if (window.jQuery && !jQuery.typeahead) ewr_GetScript("<!--##=ew_FolderPath("_js") + "/" + ew_GetFileNameByCtrlID("typeahead.js", false)##-->");	
<!--## if (PROJ.GetV("UseHandlebarsJs")) { ##-->
if (!window.Handlebars) ewr_GetScript("<!--##=ew_FolderPath("_js") + "/" + ew_GetFileNameByCtrlID("handlebars.js", false)##-->"); 
<!--## } ##-->
</script>
<!--##=sTmplHtmlEmailExpEnd##-->
<!--##=sTmplHtmlExpStart##-->
<script type="text/javascript">
<?php echo $ReportLanguage->ToJSON() ?>
</script>
<!--##
	if (!bGenCompatHeader) {
##-->
<!--##include rpt-menuext.php/menuextjs##-->
<!--##
	}
##-->
<!--##/session##-->

<!--##session header_top_3##-->
<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Global","Page_Head")) { ##-->
<?php
<!--##~SYSTEMFUNCTIONS.GetServerScript("Global","Page_Head")##-->
?>
<!--## } ##-->
<script type="text/javascript">ewr_GetScript("<!--##=ew_FolderPath("_js")##-->/<!--##=ew_GetFileNameByCtrlID("rptuser.js", false)##-->");</script>
<!--##~SYSTEMFUNCTIONS.GetClientScript("Global","Client Script")##-->
<!--##=sTmplHtmlExpEnd##-->
<!--##
	if (!bGenCompatHeader) {
##-->
<!--##=SYSTEMFUNCTIONS.FavIcon()##-->
</head>
<!--## if (PROJ.GetV("UseCssFlip")) { ##-->
<body dir="rtl">
<!--## } else { ##-->
<body>
<!--## } ##-->
<!--##=sTmplSkipStart##-->
<!--##=sTmplExpStart##-->
<div class="ewLayout">
	<!-- header (begin) --><!-- *** Note: Only licensed users are allowed to change the logo *** -->
	<div id="ewHeaderRow" class="<?php echo $gsHeaderRowClass ?>"><!--##=SYSTEMFUNCTIONS.HeaderLogo()##--></div>
<?php if (ewr_IsResponsiveLayout()) { ?>
<nav id="ewMobileMenu" role="navigation" class="navbar hidden-print <!--##=sMobileMenuNavbarClass##--> <!--##=sVisibleMobileClass##-->">
	<div class="container-fluid"><!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button data-target="#ewMenu" data-toggle="collapse" class="navbar-toggle" type="button">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
<!--##
	if (sBrandHref == "") sBrandHref = "#";
	if (sBrand == "") sBrand = "<?php echo $ReportLanguage->ProjectPhrase(\"BodyTitle\") ?>";
##-->
			<a class="navbar-brand" href="<!--##=sBrandHref##-->"><!--##=sBrand##--></a>
		</div>
		<div id="ewMenu" class="collapse navbar-collapse" style="height: auto;"><!-- Begin Main Menu -->
<?php
	$RootMenu = new crMenu("MobileMenu");
	$RootMenu->MenuBarClassName = "";
	$RootMenu->MenuClassName = "nav navbar-nav";
	$RootMenu->SubMenuClassName = "dropdown-menu";
	$RootMenu->SubMenuDropdownImage = "";
	$RootMenu->SubMenuDropdownIconClassName = "icon-arrow-down";
	$RootMenu->MenuDividerClassName = "divider";
	$RootMenu->MenuItemClassName = "dropdown";
	$RootMenu->SubMenuItemClassName = "dropdown";
	$RootMenu->MenuActiveItemClassName = "active";
	$RootMenu->SubMenuActiveItemClassName = "active";
	$RootMenu->MenuRootGroupTitleAsSubMenu = TRUE;
	$RootMenu->MenuLinkDropdownClass = "ewDropdown";
	$RootMenu->MenuLinkClassName = "icon-arrow-right";
?>
<?php include_once $EWR_RELATIVE_PATH . "<!--##=ew_GetFileNameByCtrlID("rptmobilemenu")##-->" ?>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>
<?php } ?>
	<!-- header (end) -->
<!--##
	}
##-->
<!--##/session##-->


<!--##session menu##-->
<!--##
	if (!bGenCompatHeader) {
##-->
	<!-- content (begin) -->
	<!-- content (begin) -->
	<div id="ewContentTable" class="ewContentTable">
		<div id="ewContentRow">
			<div id="ewMenuColumn" class="<?php echo $gsMenuColumnClass ?>">

				<!-- left column (begin) -->
				<div class="ewMenu">
<?php include_once "<!--##=ew_GetFileNameByCtrlID("rptmenu")##-->" ?>
				</div>
				<!-- left column (end) -->

			</div>
<!--##
	}
##-->
<!--##/session##-->

<!--##session header_bottom##-->
<!--##
	if (!bGenCompatHeader) {
##-->
			<div id="ewContentColumn" class="ewContentColumn">
				<!-- right column (begin) -->
				<h4 class="<?php echo $gsSiteTitleClass ?>"><?php echo $ReportLanguage->ProjectPhrase("BodyTitle") ?></h4>
<!--##=sTmplExpEnd##-->
<!--##=sTmplSkipEnd##-->
<!--##
	}
##-->
<!--##/session##-->





<!--##session footer##-->
<!--##
	if (!bGenCompatHeader) {
##-->
<!--##=sTmplExpStart##-->
<!--##=sTmplSkipStart##-->			
			<?php if (isset($gsTimer)) $gsTimer->Stop(); ?>
			<!-- right column (end) -->
			</div>
		</div>
	</div>
	<!-- content (end) -->
	<!-- footer (begin) --><!-- *** Note: Only licensed users are allowed to remove or change the following copyright statement. *** -->
	<div id="ewFooterRow" class="ewFooterRow">
		<div class="ewFooterText"><?php echo $ReportLanguage->ProjectPhrase("FooterText"); ?></div>
		<!-- Place other links, for example, disclaimer, here -->
	</div>
	<!-- footer (end) -->	
</div>
<!--##=sTmplSkipEnd##-->
<!--##=sTmplExpEnd##-->
<!--##
	}
##-->
<!--##=sTmplExpStart##-->
<!--## if (bUseEmailExport) { ##-->
<!-- email dialog -->
<div id="ewrEmailDialog" class="modal"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title"></h4></div>
<div class="modal-body">
<!--##=SYSTEMFUNCTIONS.IncludeFile("ewemail","other")##-->
</div><div class="modal-footer"><button type="button" class="btn btn-primary ewButton"><?php echo $ReportLanguage->Phrase("SendEmailBtn") ?></button><button type="button" class="btn btn-default ewButton" data-dismiss="modal" aria-hidden="true"><?php echo $ReportLanguage->Phrase("Cancel") ?></button></div></div></div></div>
<!--## } ##-->
<!-- message box -->
<div id="ewrMsgBox" class="modal"><div class="modal-dialog"><div class="modal-content"><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-primary ewButton" data-dismiss="modal" aria-hidden="true"><?php echo $ReportLanguage->Phrase("OK") ?></button></div></div></div></div>
<!-- prompt -->
<div id="ewrPrompt" class="modal"><div class="modal-dialog"><div class="modal-content"><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-primary ewButton"><?php echo $ReportLanguage->Phrase("OK") ?></button><button type="button" class="btn btn-default ewButton" data-dismiss="modal"><?php echo $ReportLanguage->Phrase("Cancel") ?></button></div></div></div></div>
<!-- popup filter -->
<div id="ewrPopupFilterDialog"></div>
<!-- export chart -->
<div id="ewrExportDialog"></div>
<!-- drill down -->
<!--##=sDrillSkipStart##-->
<div id="ewrDrillDownPanel"></div>
<!--##=sDrillSkipEnd##-->
<!--##=sTmplExpEnd##-->
<!--##
	if (!bGenCompatHeader) {
##-->
<!--##=sTmplExpStart##-->
<!--##~SYSTEMFUNCTIONS.GetClientScript("Global","Startup Script")##-->
<!--##=sTmplExpEnd##-->
</body>
</html>
<!--##
	}
##-->
<!--##/session##-->
