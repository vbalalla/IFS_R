<?php
<!--##session phpmain##-->

	//
	// Page main
	//
	function Page_Main() {
		global $Security;
		global $ReportLanguage;

<!--##

	// Get default start page
	var sStartPage = PROJ.StartPage;
	if (sStartPage == PROJ.DefaultPage) sStartPage = ""; // Make sure not same as default page
	var bCustomUrl = (ew_IsNotEmpty(sStartPage));

	// Get Default Table List Page
	var sFn = "";
	var sUrl = "";
	var sListUrl = "";
	var sDefaultUrl = "";
	var DEFTABLE = null;
	for (var i = 0, len = goTbls.length; i < len; i++) {
		TABLE = goTbls[i];
		if (TABLE.TblGen) {
			if (TABLE.TblType == "REPORT") {
				if (TABLE.TblReportType == "custom") {
					sUrl = TABLE.TblName;
					if (PROJ.OutputNameLCase) sUrl = sUrl.toLowerCase();
					sFn = sUrl;
					// Custom file output folder
					sUrl = ew_OutputRelPath(TABLE.OutputFolder) + sUrl;
				} else {
					sUrl = ew_GetFileNameByCtrlID(TABLE.TblReportType); // Get report page
					sFn = sUrl;
				}
			} else {
				sUrl = ew_GetFileNameByCtrlID("rpt"); // Get simple report page
				sFn = sUrl;
			}

			if (sUrl == sStartPage || sFn == sStartPage) { // Default start page
				DEFTABLE = TABLE;
				sDefaultUrl = sUrl;
				bCustomUrl = false;
			}
			if (TABLE.TblDefault && sDefaultUrl == "") { // Default table
				DEFTABLE = TABLE;
				sDefaultUrl = sUrl;
			}
			if (sListUrl == "") { // First table
				DEFTABLE = TABLE;
				sListUrl = sUrl;
			}
		}
	} // Table


	if (sDefaultUrl == "") sDefaultUrl = sListUrl;

	if (bCustomUrl) {
##-->
		$this->Page_Terminate("<!--##=sStartPage##-->"); // Exit and go to default page
<!--##
	} else if (!bSecurityEnabled) {
##-->
		$this->Page_Terminate("<!--##=sDefaultUrl##-->"); // Exit and go to default page
<!--##
	} else {
##-->
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
			<!--## if (bUserLevel) { ##-->
		$Security->LoadUserLevel(); // Load User Level
			<!--## } ##-->
<!--##
		if (DEFTABLE != null) {
			TABLE = DEFTABLE;
			iAnonymous = TABLE.TblAnonymous;
			bAnonymous = ((iAnonymous & 8) == 8);
			sMenuChkStart = "";
			sMenuChkEnd = "";
			if (!bAnonymous) {
				if (bUserLevel) {
					sMenuChkStart = "if ($Security->AllowList(CurrentProjectID() . '" + ew_SQuote(TABLE.TblName) + "'))";
					sMenuChkEnd = "";
				} else {
					sMenuChkStart = "if ($Security->IsLoggedIn())";
					sMenuChkEnd = "";
				}
			}
##-->
		<!--##=sMenuChkStart##-->
			$this->Page_Terminate("<!--##=sDefaultUrl##-->"); // Exit and go to default page
		<!--##=sMenuChkEnd##-->
<!--##
		}

		for (var i = 0, len = goTbls.length; i < len; i++) {
			TABLE = goTbls[i];
			if (TABLE.TblGen && (TABLE.TblName != DEFTABLE.TblName)) {
				if (TABLE.TblType == "REPORT")
					sRedirectFn = ew_GetFileNameByCtrlID(TABLE.TblReportType); // Get report page
				else
					sRedirectFn = ew_GetFileNameByCtrlID("rpt"); // Get simple report page
				iAnonymous = TABLE.TblAnonymous;
				bAnonymous = ((iAnonymous & 8) == 8);
				sMenuChkStart = "";
				sMenuChkEnd = "";
				if (!bAnonymous) {
					if (bUserLevel) {
						sMenuChkStart = "if ($Security->AllowList(CurrentProjectID() . '" + ew_SQuote(TABLE.TblName) + "'))";
						sMenuChkEnd = "";
					} else {
						sMenuChkStart = "if ($Security->IsLoggedIn())";
						sMenuChkEnd = "";
					}
				}
##-->
		<!--##=sMenuChkStart##-->
			$this->Page_Terminate("<!--##=sRedirectFn##-->");
		<!--##=sMenuChkEnd##-->
<!--##
			}
		} // Table
##-->
		if ($Security->IsLoggedIn()) {
			$this->setFailureMessage("<p>" . $ReportLanguage->Phrase("NoPermission") . "</p><p><a href=\"<!--##=sFnLogout##-->\">" . $ReportLanguage->Phrase("BackToLogin") . "</a></p>");
		} else {
			$this->Page_Terminate("<!--##=sFnLogin##-->"); // Exit and go to login page
		}
<!--##
	}
##-->
	}
<!--##/session##-->
?>

<!--##session default_htm##-->
<!--##include rpt-phpcommon.php/common-message##-->
<!--##/session##-->