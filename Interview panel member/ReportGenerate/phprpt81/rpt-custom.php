<!--##session content##-->
<!--##include rpt-phpcommon.php/phpcommon-config##-->
<!--##include rpt-custom.php/phpconfig##-->

<!--## if (bIncludeFiles) { ##-->
<!--##include rpt-phpcommon.php/phpcommon-directive##-->
<!--##=SYSTEMFUNCTIONS.IncludeFile("rptconfig","")##-->
<?php $EWR_ROOT_RELATIVE_PATH = "<!--##=sAppRootRelativePath##-->"; ?>
<!--##=SYSTEMFUNCTIONS.IncludeFile("_adodb","")##-->
<!--##=SYSTEMFUNCTIONS.IncludeFile("phprptfn","")##-->
<!--##=SYSTEMFUNCTIONS.IncludeFile("phprptuserfn","")##-->
<!--##include rpt-phpcommon-scripts.php/phppageclassbegin##-->
<!--##include rpt-custom.php/phpmain##-->
<!--##include rpt-custom.php/phpfunction##-->
<!--##include rpt-phpcommon-scripts.php/phpevents##-->
<!--##include rpt-phpcommon-scripts.php/phppageclassend##-->
<!--##include rpt-phpcommon-scripts.php/phpload##-->
<!--##=SYSTEMFUNCTIONS.IncludeFile("rptheader","")##-->
<!--## } ##-->

<!--##
	sBreadcrumbCheckStart = "";
	sBreadcrumbCheckEnd = "";
	sExpStart = "";
	sExpEnd = "";
##-->

<!--## if (bIncludeFiles) { ##-->
<div class="ewToolbar">
<!--##include rpt-phpcommon.php/breadcrumb##-->
<!--##include rpt-phpcommon.php/language##-->
<div class="clearfix"></div>
</div>
<!--## } ##-->

<!--##~SYSTEMFUNCTIONS.GetCustomTemplate()##-->

<!--## if (bIncludeFiles) { ##-->
<?php if (EWR_DEBUG_ENABLED) echo ewr_DebugMsg(); ?>
<!--##=SYSTEMFUNCTIONS.IncludeFile("rptfooter","")##-->
<!--##include rpt-phpcommon-scripts.php/phpunload##-->
<!--## } ##-->
<!--##/session##-->


<!--##session phpconfig##-->
<!--##
	// Set up table var
	gsTblVar = TABLE.TblVar;

	// Include other table class
	dIncludeTable = {};

	// Set up source table
	if (ew_IsNotEmpty(TABLE.TblRptSrc)) {
		SRCTABLE = DB.Tables(TABLE.TblRptSrc);
		if (SRCTABLE) {
			sSrcTblVar = SRCTABLE.TblVar;
			sTblObj = sSrcTblVar;
			CURRENTTABLE = TABLE; // Save current table
			TABLE = SRCTABLE; // Set table object
			dIncludeTable[sSrcTblVar] = ew_GetFileNameByCtrlID("info");
			TABLE = CURRENTTABLE; // Restore current table
		}
	}

	var iChartCnt = 0, iFcfChartCnt = 0;
	var sFn = TABLE.TblName;
	var bIncludeFiles = (TABLE.IncludeFiles && sFn.toLowerCase().substr(sFn.length-4) == ".php");

	// Custom file relative path
	sRelativePath = "";
	sRelativePathPrefix = "";
	sAppRootRelativePath = "";
	sRelativePath = ew_DestRelPath(TABLE.OutputFolder);
	if (sRelativePath != "") {
		sRelativePathPrefix = "$EWR_RELATIVE_PATH . ";
		sAppRootRelativePath = ew_AppRootRelPath(TABLE.OutputFolder);
	}
##-->
<!--##/session##-->


<?php
<!--##session phpmain##-->

	//
	// Page main
	//
	function Page_Main() {
		// Set up Breadcrumb
		$this->SetupBreadcrumb();
	}
<!--##/session##-->
?>


<?php
<!--##session phpfunction##-->
	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $ReportBreadcrumb;
		$ReportBreadcrumb = new crBreadcrumb();
		$url = substr(ewr_CurrentUrl(), strrpos(ewr_CurrentUrl(), "/")+1);
		$ReportBreadcrumb->Add("<!--##=CTRL.CtrlID##-->", "<!--##=gsTblVar##-->", $url, "", "<!--##=gsTblVar##-->", TRUE);
	}
<!--##/session##-->
?>