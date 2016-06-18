<!--##session phpconfig##-->
<!--##
	var sColFldName = ""; // Column field name (NOT USED)
	var sColDateFldName = ""; // Column date field name (NOT USED)
	var sColFldDateType = ""; // Column date field type (NOT USED)

	var nSearchFlds = 0; // Number of search fields

	// Group Fields variables
	var arGrpFlds = [];
	var sGroupFldNames = SYSTEMFUNCTIONS.GroupByFieldNames(); // List of group field names
	var nGrps = 0;
	var arFirstGrpFld = [];
	if (ew_IsNotEmpty(sGroupFldNames)) {
		arGrps = sGroupFldNames.split("\r\n");
		nGrps = arGrps.length; // Number of group fields
		gnGrps = nGrps;
		for (var i = 0; i < nGrps; i++) {
			if (GetFldObj(arGrps[i])) {
				var grpfld = [];
				grpfld['FldName'] = gsFldName; // Field name
				grpfld['FldVar'] = gsFldVar; // Field variable
				grpfld['FldObj'] = gsFldObj; // Field object
				grpfld['SessionFldVar'] = gsSessionFldVar; // Session field var
				grpfld['PopupFilter'] = (goFld.FldType == 201 || goFld.FldType == 203) ? false : IsPopupFilter(goFld); // Popup filter, skip if memo
				grpfld['FilterName'] = goFld.FldFilterName; // Popup filter name
				grpfld['UseRange'] = (goFld.FldUseRange) ? "true" : "false"; // Field use range
				grpfld['ShowSummary'] = goFld.FldGroupByShowSummary; // Show summary required
				grpfld['GroupByType'] = goFld.FldGroupByType; // Field grouping type
				grpfld['GroupByInterval'] = goFld.FldGroupByInterval; // Field grouping interval
				grpfld['GroupSql'] = ew_DbGrpSql(goFld.FldGroupByType, goFld.FldGroupByInterval); // Field grouping sql
				if (IsPopupFilter(goFld)) nSearchFlds += 1;

				arGrpFlds[arGrpFlds.length] = grpfld;

				// Save first group
				if (i == 0)
					arFirstGrpFld = grpfld;

			}
		}
	};

	// Detail Fields variables
	var arDtlFlds = [];
	var arSmry = [];
	arSmry[arSmry.length] = ["Sum", "Avg", "Min", "Max", "Cnt"];
	arSmry[arSmry.length] = [false, false, false, false, false];
	var nDtls = 0;
	for (var i = 0; i < nAllFldCount; i++) {
		if (GetFldObj(arAllFlds[i])) {
			if (goFld.FldList && goFld.FldGroupBy <= 0) {
				var dtlfld = [];
				dtlfld['FldName'] = goFld.FldName; // Field name
				dtlfld['FldVar'] = goFld.FldVar; // Field variable
				dtlfld['FldObj'] = gsFldObj; // Field object
				dtlfld['SessionFldVar'] = gsSessionFldVar; // Session field var
				dtlfld['PopupFilter'] = (goFld.FldType == 201 || goFld.FldType == 203) ? false : IsPopupFilter(goFld); // Popup filter, skip if memo
				dtlfld['FilterName'] = goFld.FldFilterName; // Popup filter name
				dtlfld['UseRange'] = (goFld.FldUseRange) ? "true" : "false"; // Field use range
				dtlfld['SkipZeroOrNull'] = goFld.FldRptSkipNull; // Skip zero or null value for summary
				if (IsPopupFilter(goFld)) nSearchFlds += 1;

				arDtlFlds[arDtlFlds.length] = dtlfld;
				nDtls += 1;

				arSmry[arSmry.length] = [goFld.FldRptAggSum, goFld.FldRptAggAvg, goFld.FldRptAggMin, goFld.FldRptAggMax, goFld.FldRptAggCnt];
				if (goFld.FldRptAggSum) arSmry[1][0] = true;
				if (goFld.FldRptAggAvg) arSmry[1][1] = true;
				if (goFld.FldRptAggMin) arSmry[1][2] = true;
				if (goFld.FldRptAggMax) arSmry[1][3] = true;
				if (goFld.FldRptAggCnt) arSmry[1][4] = true;
			}
		}
	}; // End for i

	bHasSummaryFields = (arSmry[1][0] || arSmry[1][1] || arSmry[1][2] || arSmry[1][3] || arSmry[1][4]);
	bShowDetails = TABLE.TblRptShowDetails;
	if (nGrps <= 0 && !(bHasSummaryFields && (TABLE.TblRptShowPageTotal || TABLE.TblRptShowGrandTotal))) bShowDetails = true;
	var bShowSummaryView = false;
	if (!bShowDetails && bHasSummaryFields) {
		bShowSummaryView = TABLE.TblRptShowSummaryView; // Use summary view
	};

	// Remove grouping fields without show summary
	if (bShowSummaryView) {
		var nGrps2 = nGrps;
		var arGrpFlds2 = arGrpFlds.slice(0);
		for (var i = nGrps2-1; i >= 0; i--) {
			if (!arGrpFlds[i]['ShowSummary']) { // Show summary not enabled for last group
				if (i == nGrps-1)
					nGrps -= 1;
				else
					nGrps = 0; // Incorrect setting, show detail/summary instead
				if (nGrps > 0) {
					arGrpFlds.splice(-1,1); // Remove last group
				} else { // Restore and show detail/summary
					arGrpFlds = arGrpFlds2.slice(0);
					nGrps = nGrps2;
					bShowDetails = true;
					bShowSummaryView = false;
					break;
				}
			}
		}
	};

	// Calculate slot size
	if (bShowSummaryView) {
		nSlots = nGrps;
		for (var i = 2; i < arSmry.length; i++) {
			for (j = 0; j < arSmry[i].length; j++)
				if (arSmry[i][j]) nSlots += 1;
		}
	} else {
		nSlots = nGrps + nDtls;
	};
##-->
<!--##/session##-->


<?php
<!--##session phpmain##-->

	// Initialize common variables
	var $ExportOptions; // Export options
	var $SearchOptions; // Search options
	var $FilterOptions; // Filter options

	// Paging variables
	var $RecIndex = 0; // Record index
	var $RecCount = 0; // Record count
	var $StartGrp = 0; // Start group
	var $StopGrp = 0; // Stop group
	var $TotalGrps = 0; // Total groups
	var $GrpCount = 0; // Group count
	var $GrpCounter = array(); // Group counter
	var $DisplayGrps = <!--##=iGrpPerPage##-->; // Groups per page
	var $GrpRange = 10;

<!--## if (bShowSummaryView) { ##-->
	var $LastGrpCount = 0; // Last group count
<!--## } ##-->

	var $Sort = "";
	var $Filter = "";
	var $PageFirstGroupFilter = "";
	var $UserIDFilter = "";
	var $DrillDown = FALSE;
	var $DrillDownInPanel = FALSE;
	var $DrillDownList = "";

	// Clear field for ext filter
	var $ClearExtFilter = "";
	var $PopupName = "";
	var $PopupValue = "";
	var $FilterApplied;
	var $SearchCommand = FALSE;

	var $ShowHeader;
	var $GrpFldCount = 0;
	var $SubGrpFldCount = 0;
	var $DtlFldCount = 0;

	var $Cnt, $Col, $Val, $Smry, $Mn, $Mx, $GrandCnt, $GrandSmry, $GrandMn, $GrandMx;
	var $TotCount;
	var $GrandSummarySetup = FALSE;

	var $GrpIdx;

	//
	// Page main
	//
	function Page_Main() {
		global $rs;
		global $rsgrp;
		global $Security;
		global $gsFormError;
		global $gbDrillDownInPanel;
		global $ReportBreadcrumb;
		global $ReportLanguage;

	<!--## if (bTableHasUserIDFld) { ##-->
		// Set up User ID
		$this->UserIDFilter = $this->GetUserIDFilter();
		$this->Filter = $this->UserIDFilter;
	<!--## } ##-->

	<!--## if (nParms > 0) { ##-->
		// Handle drill down
		$sDrillDownFilter = $this->GetDrillDownFilter();
		$gbDrillDownInPanel = $this->DrillDownInPanel;
		if ($this->DrillDown)
			ewr_AddFilter($this->Filter, $sDrillDownFilter);
	<!--## } ##-->

		// Aggregate variables
		// 1st dimension = no of groups (level 0 used for grand total)
		// 2nd dimension = no of fields
		$nDtls = <!--##=nDtls+1##-->;
		$nGrps = <!--##=nGrps+1##-->;
		$this->Val = &ewr_InitArray($nDtls, 0);
		$this->Cnt = &ewr_Init2DArray($nGrps, $nDtls, 0);
		$this->Smry = &ewr_Init2DArray($nGrps, $nDtls, 0);
		$this->Mn = &ewr_Init2DArray($nGrps, $nDtls, NULL);
		$this->Mx = &ewr_Init2DArray($nGrps, $nDtls, NULL);
		$this->GrandCnt = &ewr_InitArray($nDtls, 0);
		$this->GrandSmry = &ewr_InitArray($nDtls, 0);
		$this->GrandMn = &ewr_InitArray($nDtls, NULL);
		$this->GrandMx = &ewr_InitArray($nDtls, NULL);

		// Set up array if accumulation required: array(Accum, SkipNullOrZero)
	<!--##
		accum = "array(FALSE, FALSE)"; // First column not used
		for (var i = 0; i < nDtls; i++) {
			var isSkip = arDtlFlds[i]['SkipZeroOrNull'] ? "TRUE" : "FALSE";
			var isSmry = (arSmry[i+2][0] || arSmry[i+2][1] || arSmry[i+2][2] || arSmry[i+2][3] || arSmry[i+2][4]) ? "TRUE" : "FALSE";
			accum += ", array(" + isSmry + "," + isSkip +")";
		}
	##-->
		$this->Col = array(<!--##=accum##-->);

	<!--## if (ew_IsNotEmpty(sGrpPerPageList)) { ##-->
		// Set up groups per page dynamically
		$this->SetUpDisplayGrps();
	<!--## } ##-->
	
		// Set up Breadcrumb
		if ($this->Export == "")
			$this->SetupBreadcrumb();

	<!--## if (nGrps > 0) { ##-->
	<!--##
		// Group popup & selection values
		for (var i = 0; i < nGrps; i++) {
			sFldObj = "this->" + arGrpFlds[i]['FldVar'].substr(2);
			bGenFilter = arGrpFlds[i]['PopupFilter'];
			if (bGenFilter) {
	##-->
		$<!--##=sFldObj##-->->SelectionList = "";
		$<!--##=sFldObj##-->->DefaultSelectionList = "";
		$<!--##=sFldObj##-->->ValueList = "";
	<!--##
			}
		}
	##-->
	<!--## } ##-->

	<!--##
		// Detail popup & selection values
		for (var i = 0; i < nDtls; i++) {
			sFldObj = "this->" + arDtlFlds[i]['FldVar'].substr(2);
			bGenFilter = arDtlFlds[i]['PopupFilter'];
			if (bGenFilter) {
	##-->
		$<!--##=sFldObj##-->->SelectionList = "";
		$<!--##=sFldObj##-->->DefaultSelectionList = "";
		$<!--##=sFldObj##-->->ValueList = "";
	<!--##
			}
		}
	##-->

	<!--## if (bReportExtFilter || nSearchFlds > 0) { ##-->
		// Check if search command
		$this->SearchCommand = (@$_GET["cmd"] == "search");

		// Load default filter values
		$this->LoadDefaultFilters();
	<!--## } ##-->

	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Table","Page_FilterLoad")) { ##-->
		// Load custom filters
		$this->Page_FilterLoad();
	<!--## } ##-->

		// Set up popup filter
		$this->SetupPopup();

		// Load group db values if necessary
		$this->LoadGroupDbValues();

		// Handle Ajax popup
		$this->ProcessAjaxPopup();

		// Extended filter
		$sExtendedFilter = "";

	<!--## if (bReportExtFilter || bShowYearSelection || nSearchFlds > 0) { ##-->
		// Restore filter list
		$this->RestoreFilterList();
	<!--## } ##-->

	<!--## if (bReportExtFilter) { ##-->

		// Build extended filter
		$sExtendedFilter = $this->GetExtendedFilter();
		ewr_AddFilter($this->Filter, $sExtendedFilter);

	<!--## } ##-->

		// Build popup filter
		$sPopupFilter = $this->GetPopupFilter();
		//ewr_SetDebugMsg("popup filter: " . $sPopupFilter);
		ewr_AddFilter($this->Filter, $sPopupFilter);

	<!--## if (bReportExtFilter || nSearchFlds > 0) { ##-->
		// Check if filter applied
		$this->FilterApplied = $this->CheckFilter();
	<!--## } else { ##-->
		// No filter
		$this->FilterApplied = FALSE;
		$this->FilterOptions->GetItem("savecurrentfilter")->Visible = FALSE;
		$this->FilterOptions->GetItem("deletefilter")->Visible = FALSE;
	<!--## } ##-->

	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Table","Page_Selecting")) { ##-->
		// Call Page Selecting event
		$this->Page_Selecting($this->Filter);
	<!--## } ##-->

	<!--## if (TABLE.TblShowBlankListPage) { ##-->
		// Requires search criteria
		if (($this->Filter == $this->UserIDFilter || $gsFormError != "") && !$this->DrillDown)
			$this->Filter = "0=101";
	<!--## } ##-->

		$this->SearchOptions->GetItem("resetfilter")->Visible = $this->FilterApplied;

		// Get sort
		$this->Sort = $this->GetSort();

	<!--## if (bChartDynamicSort) { ##-->
		// Get chart sort
		$this->GetChartSort();
	<!--## } ##-->

	<!--## if (nGrps > 0) { ##-->
		// Get total group count
		$sGrpSort = ewr_UpdateSortFields($this->getSqlOrderByGroup(), $this->Sort, 2); // Get grouping field only
		$sSql = ewr_BuildReportSql($this->getSqlSelectGroup(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderByGroup(), $this->Filter, $sGrpSort);
		$this->TotalGrps = $this->GetGrpCnt($sSql);
	<!--## } else { ##-->
		// Get total count
		$sSql = ewr_BuildReportSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(), $this->Filter, $this->Sort);
		$this->TotalGrps = $this->GetCnt($sSql);
	<!--## } ##-->

		if ($this->DisplayGrps <= 0 || $this->DrillDown) // Display all groups
			$this->DisplayGrps = $this->TotalGrps;
		$this->StartGrp = 1;

		// Show header
	<!--## if (nSearchFlds > 0) { ##-->
		$this->ShowHeader = TRUE;
	<!--## } else { ##-->
		$this->ShowHeader = ($this->TotalGrps > 0);
	<!--## } ##-->

		// Set up start position if not export all
		if ($this->ExportAll && $this->Export <> "")
		    $this->DisplayGrps = $this->TotalGrps;
		else
			$this->SetUpStartGroup(); 

		// Set no record found message
		if ($this->TotalGrps == 0) {
			<!--## if (bUserLevel && !bAnonymous) { ##-->
			if ($Security->CanList()) {
			<!--## } ##-->
				if ($this->Filter == "0=101") {
					$this->setWarningMessage($ReportLanguage->Phrase("EnterSearchCriteria"));
				} else {
					$this->setWarningMessage($ReportLanguage->Phrase("NoRecord"));
				}
			<!--## if (bUserLevel && !bAnonymous) { ##-->
			} else {
				$this->setWarningMessage($ReportLanguage->Phrase("NoPermission"));
			}
			 <!--## } ##-->
		}

		// Hide export options if export
		if ($this->Export <> "")
			$this->ExportOptions->HideAllOptions();

		// Hide search/filter options if export/drilldown
		if ($this->Export <> "" || $this->DrillDown) {
			$this->SearchOptions->HideAllOptions();
			$this->FilterOptions->HideAllOptions();
		}

	<!--## if (nGrps > 0) { ##-->

		// Get current page groups
		$rsgrp = $this->GetGrpRs($sSql, $this->StartGrp, $this->DisplayGrps);

		// Init detail recordset
		$rs = NULL;

	<!--## } else { ##-->

		// Get current page records
		$rs = $this->GetRs($sSql, $this->StartGrp, $this->DisplayGrps);

	<!--## } ##-->

		$this->SetupFieldCount();

	}

<!--##/session##-->
?>


<!--##session report_content##-->

<!--##=sHtmlExpStart##-->
<!-- container (begin) -->
<div id="ewContainer" class="ewContainer">

<!-- top container (begin) -->
<div id="ewTop" class="ewTop">
<a id="top"></a>
<!--##=sHtmlExpEnd##-->

<!-- top slot -->
<div class="ewToolbar">
<!--##include rpt-phpcommon.php/breadcrumb##-->
<?php
if (!$<!--##=gsPageObj##-->->DrillDownInPanel) {
	$<!--##=gsPageObj##-->->ExportOptions->Render("body");
	$<!--##=gsPageObj##-->->SearchOptions->Render("body");
	$<!--##=gsPageObj##-->->FilterOptions->Render("body");
}
?>
<!--##include rpt-phpcommon.php/language##-->
<div class="clearfix"></div>
</div>

<!--##include rpt-phpcommon.php/header-message##-->
<!--##include rpt-phpcommon.php/common-message##-->

<!--## if (!bShowReport && !bShowChart) { ##-->
<p class="ewMessage"><!--##@NoReportOrCharts##--></p>
<!--## } ##-->

<!--##
	// Generate charts (on top)
	for (var i = 0, len = arAllCharts.length; i < len; i++) {
		if (GetChtObj(arAllCharts[i])) {
			if (IsShowChart(goCht, 1)) {
##-->
<!--##include rpt-chartcommon.php/chart_common##-->
<!--##include rpt-chartcommon.php/chart_html##-->
<!--##include rpt-chartcommon.php/chart_include##-->
<!--##
			}
		}
	}; // End for i, charts on top
##-->

<!--##=sHtmlExpStart##-->
</div>
<!-- top container (end) -->

	<!-- left container (begin) -->
	<div id="ewLeft" class="ewLeft">
<!--##=sHtmlExpEnd##-->

	<!-- Left slot -->

<!--##
	// Generate charts (on left)
	for (var i = 0, len = arAllCharts.length; i < len; i++) {
		if (GetChtObj(arAllCharts[i])) {
			if (IsShowChart(goCht, 2)) {
##-->
<!--##include rpt-chartcommon.php/chart_common##-->
<!--##include rpt-chartcommon.php/chart_html##-->
<!--##include rpt-chartcommon.php/chart_include##-->
<!--##
			}
		}
	}; // End for i, charts on left
##-->

<!--##=sHtmlExpStart##-->
	</div>
	<!-- left container (end) -->

	<!-- center container - report (begin) -->
	<div id="ewCenter" class="ewCenter">
<!--##=sHtmlExpEnd##-->

	<!-- center slot -->

<!--##include rpt-extfilter.php/report_drilldownlist##-->

<!-- summary report starts -->

<!--##=sSkipPdfExpStart##-->
<div id="report_summary">
<!--##include rpt-extfilter.php/report_extfilter_html##-->
<!--##=sSkipPdfExpEnd##-->

<!--## if (bShowReport) { ##-->

<?php
// Set the last group to display if not export all
if ($<!--##=gsPageObj##-->->ExportAll && $<!--##=gsPageObj##-->->Export <> "") {
	$<!--##=gsPageObj##-->->StopGrp = $<!--##=gsPageObj##-->->TotalGrps;
} else {
	$<!--##=gsPageObj##-->->StopGrp = $<!--##=gsPageObj##-->->StartGrp + $<!--##=gsPageObj##-->->DisplayGrps - 1;
}

// Stop group <= total number of groups
if (intval($<!--##=gsPageObj##-->->StopGrp) > intval($<!--##=gsPageObj##-->->TotalGrps))
	$<!--##=gsPageObj##-->->StopGrp = $<!--##=gsPageObj##-->->TotalGrps;

$<!--##=gsPageObj##-->->RecCount = 0;
$<!--##=gsPageObj##-->->RecIndex = 0;

// Get first row
if ($<!--##=gsPageObj##-->->TotalGrps > 0) {
<!--## if (nGrps > 0) { ##-->
	$<!--##=gsPageObj##-->->GetGrpRow(1);
	<!--##
		if (nGrps >= 2) {
			for (var i = 2; i <= nGrps; i++) {
	##-->
	$<!--##=gsPageObj##-->->GrpCounter[<!--##=i-2##-->] = 1;
	<!--##
			}
		}
	##-->
<!--## } else { ##-->
	$<!--##=gsPageObj##-->->GetRow(1);
<!--## } ##-->
	$<!--##=gsPageObj##-->->GrpCount = 1;
}

<!--## if (nGrps > 0) { ##-->

$<!--##=gsPageObj##-->->GrpIdx = ewr_InitArray($<!--##=gsPageObj##-->->StopGrp - $<!--##=gsPageObj##-->->StartGrp + 1, -1);

while ($rsgrp && !$rsgrp->EOF && $<!--##=gsPageObj##-->->GrpCount <= $<!--##=gsPageObj##-->->DisplayGrps || $<!--##=gsPageObj##-->->ShowHeader) {

<!--## } else { ##-->

$<!--##=gsPageObj##-->->GrpIdx = ewr_InitArray(2, -1);
$<!--##=gsPageObj##-->->GrpIdx[0] = -1;
$<!--##=gsPageObj##-->->GrpIdx[1] = $<!--##=gsPageObj##-->->StopGrp - $<!--##=gsPageObj##-->->StartGrp + 1;

while ($rs && !$rs->EOF && $<!--##=gsPageObj##-->->GrpCount <= $<!--##=gsPageObj##-->->DisplayGrps || $<!--##=gsPageObj##-->->ShowHeader) {

<!--## } ##-->

	// Show dummy header for custom template
	// Show header
	if ($<!--##=gsPageObj##-->->ShowHeader) {
?>

<!--## if (nGrps > 0) { ##-->
<?php if ($<!--##=gsPageObj##-->->GrpCount > 1) { ?>
</tbody>
<!--##include rpt-phpcommon-table.php/report-footer##-->
<span data-class="tpb<?php echo $<!--##=gsPageObj##-->->GrpCount-1 ?>_<!--##=gsTblVar##-->"><?php echo $<!--##=gsPageObj##-->->PageBreakContent ?></span>
<?php } ?>
<!--## } ##-->

<!--##include rpt-phpcommon-table.php/report-header##-->

<thead>
	<!-- Table header -->
	<tr class="ewTableHeader">
	<!--##
		for (var i = 0; i < nGrps; i++) {
			lvl = i + 1;
			sFldName = arGrpFlds[i]['FldName'];
			sFldVar = arGrpFlds[i]['FldVar'];
			sFldParm = sFldVar.substr(2);
			sFldObj = arGrpFlds[i]['FldObj'];
			sSessionFldVar = arGrpFlds[i]['SessionFldVar'];
			bGenFilter = arGrpFlds[i]['PopupFilter'];
			sUseRange = arGrpFlds[i]['UseRange'];
			GetFldObj(sFldName);
			sTDStyle = FieldTD_Header(goFld);
			sClassId = gsTblVar + "_" + sFldParm;
			sJsSort = " class=\"ewTableHeaderBtn ewPointer " + sClassId + "\" onclick=\"ewr_Sort(event,'<?php echo $" + gsPageObj + "->SortUrl($" + sFldObj + ") ?>'," + iSortType + ");\"";			
	##-->
<?php if ($<!--##=sFldObj##-->->Visible) { ?>
<?php if ($<!--##=gsPageObj##-->->Export <> "" || $<!--##=gsPageObj##-->->DrillDown) { ?>
	<td data-field="<!--##=sFldParm##-->"><div class="<!--##=sClassId##-->"<!--##=sTDStyle##-->><span class="ewTableHeaderCaption"><?php echo $<!--##=sFldObj##-->->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="<!--##=sFldParm##-->">
<?php if ($<!--##=gsPageObj##-->->SortUrl($<!--##=sFldObj##-->) == "") { ?>
		<div class="ewTableHeaderBtn <!--##=sClassId##-->"<!--##=sTDStyle##-->>
			<span class="ewTableHeaderCaption"><?php echo $<!--##=sFldObj##-->->FldCaption() ?></span>
<!--## if (bGenFilter) { ##-->
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, '<!--##=sSessionFldVar##-->', <!--##=sUseRange##-->, '<?php echo $<!--##=sFldObj##-->->RangeFrom; ?>', '<?php echo $<!--##=sFldObj##-->->RangeTo; ?>');" id="<!--##=sFldVar##--><?php echo $<!--##=gsPageObj##-->->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
<!--## } ##-->
		</div>
<?php } else { ?>
		<div<!--##=sJsSort##--><!--##=sTDStyle##-->>
			<span class="ewTableHeaderCaption"><?php echo $<!--##=sFldObj##-->->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($<!--##=sFldObj##-->->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($<!--##=sFldObj##-->->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
<!--## if (bGenFilter) { ##-->
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, '<!--##=sSessionFldVar##-->', <!--##=sUseRange##-->, '<?php echo $<!--##=sFldObj##-->->RangeFrom; ?>', '<?php echo $<!--##=sFldObj##-->->RangeTo; ?>');" id="<!--##=sFldVar##--><?php echo $<!--##=gsPageObj##-->->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
<!--## } ##-->
		</div>
<?php } ?>

	</td>
<?php } ?>
<?php } ?>
	<!--##
		}; // End for i
	##-->

<!--## if (bShowSummaryView) { // Summary view ##-->

	<!--##
		for (var i = 2; i < arSmry.length; i++) {
			for (var j = 0; j < arSmry[i].length; j++) {
				if (arSmry[i][j]) {
					sSrcFldObj = arDtlFlds[i-2]['FldObj'];
					sFldParm = arDtlFlds[i-2]['FldVar'].substr(2);
					sSummaryType = arSmry[0][j];
	##-->
<?php if ($<!--##=sSrcFldObj##-->->Visible) { ?>
<td data-field="<!--##=sFldParm##-->">
	<div<!--##=sTDStyle##-->><span class="ewTableHeaderCaption"><?php echo $<!--##=sSrcFldObj##-->->FldCaption() ?> (<!--##=SummaryCaption(sSummaryType)##-->)</span></div>
</td>
<?php } ?>
	<!--##
				}
			}; // End for j
		}; // End for i
	##-->

<!--## } else { // Detail view ##-->

	<!--##
		for (var i = 0; i < nDtls; i++) {
			sFldName = arDtlFlds[i]['FldName'];
			sFldVar = arDtlFlds[i]['FldVar'];
			sFldParm = sFldVar.substr(2);
			sFldObj = arDtlFlds[i]['FldObj'];
			sSessionFldVar = arDtlFlds[i]['SessionFldVar'];
			bGenFilter = arDtlFlds[i]['PopupFilter'];
			sUseRange = arDtlFlds[i]['UseRange'];
			GetFldObj(sFldName);
			sTDStyle = FieldTD_Header(goFld);
			sClassId = gsTblVar + "_" + sFldParm;
			sJsSort = " class=\"ewTableHeaderBtn ewPointer " + sClassId + "\" onclick=\"ewr_Sort(event,'<?php echo $" + gsPageObj + "->SortUrl($" + sFldObj + ") ?>'," + iSortType + ");\"";			
	##-->
<?php if ($<!--##=sFldObj##-->->Visible) { ?>
<?php if ($<!--##=gsPageObj##-->->Export <> "" || $<!--##=gsPageObj##-->->DrillDown) { ?>
	<td data-field="<!--##=sFldParm##-->"><div class="<!--##=sClassId##-->"<!--##=sTDStyle##-->><span class="ewTableHeaderCaption"><?php echo $<!--##=sFldObj##-->->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="<!--##=sFldParm##-->">
<?php if ($<!--##=gsPageObj##-->->SortUrl($<!--##=sFldObj##-->) == "") { ?>
		<div class="ewTableHeaderBtn <!--##=sClassId##-->"<!--##=sTDStyle##-->>
			<span class="ewTableHeaderCaption"><?php echo $<!--##=sFldObj##-->->FldCaption() ?></span>
	<!--## if (bGenFilter) { ##-->
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, '<!--##=sSessionFldVar##-->', <!--##=sUseRange##-->, '<?php echo $<!--##=sFldObj##-->->RangeFrom; ?>', '<?php echo $<!--##=sFldObj##-->->RangeTo; ?>');" id="<!--##=sFldVar##--><?php echo $<!--##=gsPageObj##-->->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
	<!--## } ##-->
		</div>
<?php } else { ?>
		<div<!--##=sJsSort##--><!--##=sTDStyle##-->>
			<span class="ewTableHeaderCaption"><?php echo $<!--##=sFldObj##-->->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($<!--##=sFldObj##-->->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($<!--##=sFldObj##-->->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
<!--## if (bGenFilter) { ##-->
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, '<!--##=sSessionFldVar##-->', <!--##=sUseRange##-->, '<?php echo $<!--##=sFldObj##-->->RangeFrom; ?>', '<?php echo $<!--##=sFldObj##-->->RangeTo; ?>');" id="<!--##=sFldVar##--><?php echo $<!--##=gsPageObj##-->->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
<!--## } ##-->
		</div>
<?php } ?>
       
	</td>
<?php } ?>
<?php } ?>
	<!--##
		}; // End for i
	##-->

<!--## }; // End summary/detail view ##-->

	</tr>
</thead>
<tbody>
<?php
		if ($<!--##=gsPageObj##-->->TotalGrps == 0) break; // Show header only
		$<!--##=gsPageObj##-->->ShowHeader = FALSE;
	}


<!--## if (nGrps == 0) { ##-->

	$<!--##=gsPageObj##-->->RecCount++;
	$<!--##=gsPageObj##-->->RecIndex++;

<!--## } else { ##-->

	// Build detail SQL
	$sWhere = ewr_DetailFilterSQL($<!--##=arFirstGrpFld['FldObj']##-->, $<!--##=gsPageObj##-->->getSqlFirstGroupField(), $<!--##=arFirstGrpFld['FldObj']##-->->GroupValue());

	if ($<!--##=gsPageObj##-->->PageFirstGroupFilter <> "") $<!--##=gsPageObj##-->->PageFirstGroupFilter .= " OR ";
	$<!--##=gsPageObj##-->->PageFirstGroupFilter .= $sWhere;

	if ($<!--##=gsPageObj##-->->Filter != "")
		$sWhere = "($<!--##=gsPageObj##-->->Filter) AND ($sWhere)";
	$sSql = ewr_BuildReportSql($<!--##=gsPageObj##-->->getSqlSelect(), $<!--##=gsPageObj##-->->getSqlWhere(), $<!--##=gsPageObj##-->->getSqlGroupBy(), $<!--##=gsPageObj##-->->getSqlHaving(), $<!--##=gsPageObj##-->->getSqlOrderBy(), $sWhere, $<!--##=gsPageObj##-->->Sort);
	$rs = $conn->Execute($sSql);
	$rsdtlcnt = ($rs) ? $rs->RecordCount() : 0;
	if ($rsdtlcnt > 0)
		$<!--##=gsPageObj##-->->GetRow(1);

	<!--## if (nGrps == 1) { ##-->
	$<!--##=gsPageObj##-->->GrpIdx[$<!--##=gsPageObj##-->->GrpCount] = $rsdtlcnt;
	<!--## } else { ##-->
	$<!--##=gsPageObj##-->->GrpIdx[$<!--##=gsPageObj##-->->GrpCount] = array(-1);
	<!--##
		var suffix = "";
		for (var i = 2; i < nGrps; i++) {
			suffix += "[]";
	##-->
	$<!--##=gsPageObj##-->->GrpIdx[$<!--##=gsPageObj##-->->GrpCount]<!--##=suffix##--> = array(-1);
	<!--##
		}
	##-->
	<!--## } ##-->

	while ($rs && !$rs->EOF) { // Loop detail records
		$<!--##=gsPageObj##-->->RecCount++;
		$<!--##=gsPageObj##-->->RecIndex++;

<!--## } ##-->

		// Render detail row
		$<!--##=gsPageObj##-->->ResetAttrs();
		$<!--##=gsPageObj##-->->RowType = EWR_ROWTYPE_DETAIL;
		$<!--##=gsPageObj##-->->RenderRow();
?>

<!--## if (bShowDetails) { // Show details ##-->

	<tr<?php echo $<!--##=gsPageObj##-->->RowAttributes(); ?>>
<!--##
	for (var i = 0; i < nGrps; i++) {
		lvl = i + 1;
		sFldName = arGrpFlds[i]['FldName'];
		sFldObj = arGrpFlds[i]['FldObj'];
		sFldParm = arGrpFlds[i]['FldVar'].substr(2);
		GetFldObj(sFldName);
##-->
<?php if ($<!--##=sFldObj##-->->Visible) { ?>
		<td data-field="<!--##=sFldParm##-->"<?php echo $<!--##=sFldObj##-->->CellAttributes(); ?>><!--##=SYSTEMFUNCTIONS.FieldGroupView()##--></td>
<?php } ?>
<!--##
	}; // End for i
	
	for (var i = 0; i < nDtls; i++) {
		sFldName = arDtlFlds[i]['FldName'];
		sFldObj = arDtlFlds[i]['FldObj'];
		sFldParm = arDtlFlds[i]['FldVar'].substr(2);
		GetFldObj(sFldName);
##-->
<?php if ($<!--##=sFldObj##-->->Visible) { ?>
		<td data-field="<!--##=sFldParm##-->"<?php echo $<!--##=sFldObj##-->->CellAttributes() ?>><!--##=SYSTEMFUNCTIONS.FieldView()##--></td>
<?php } ?>
<!--##
	}; // End for i
##-->
	</tr>

<!--## }; // End show detail ##-->

<?php
		// Accumulate page summary
		$<!--##=gsPageObj##-->->AccumulateSummary();

		// Get next record
		$<!--##=gsPageObj##-->->GetRow(2);

<!--## if (nGrps <= 0) { // No grouping fields ##-->

	$<!--##=gsPageObj##-->->GrpCount++;

<!--## } else { // Grouping fields ##-->

		// Show Footers
?>
<!--##
	for (var i = nGrps-1; i >= 0; i--) {
		lvl = i + 1;
		if (lvl == 1) {
##-->
<?php
	} // End detail records loop
?>
<!--##
		}
		if (arGrpFlds[i]['ShowSummary']) { // Show summary required
			sFldName = arGrpFlds[i]['FldName'];
			GetFldObj(sFldName);
			GROUPFLD = goFld;
			sFldObj = arGrpFlds[i]['FldObj'];
			sSummary = "ewr_DisplayGroupValue($" + sFldObj + ", $" + sFldObj + "->GroupValue())";
##-->
<?php
	<!--## if (lvl > 1) { ##-->

	<!--## 
		if (i == nGrps-1) {
			suffix = "";
			for (var x = 2; x < nGrps; x++) {
				suffix += "[$" + gsPageObj + "->GrpCounter[" + (x-2) + "]]";
			}
	##-->
		if ($<!--##=gsPageObj##-->->ChkLvlBreak(<!--##=lvl##-->)) {
			$cnt = count(@$<!--##=gsPageObj##-->->GrpIdx[$<!--##=gsPageObj##-->->GrpCount]<!--##=suffix##-->);
			$<!--##=gsPageObj##-->->GrpIdx[$<!--##=gsPageObj##-->->GrpCount]<!--##=suffix##-->[$cnt] = $<!--##=gsPageObj##-->->RecCount;
		}
	<!--##
		}
	##-->

		if ($<!--##=gsPageObj##-->->ChkLvlBreak(<!--##=lvl##-->) && $<!--##=sFldObj##-->->Visible) {

	<!--## if (bShowSummaryView && i == nGrps-1) { ##-->
			$<!--##=gsPageObj##-->->LastGrpCount++; // Update last group count
	<!--## } ##-->

	<!--## } else { ##-->

		if ($<!--##=sFldObj##-->->Visible) {

	<!--## if (bShowSummaryView && nGrps == 1) { ##-->
			$<!--##=gsPageObj##-->->LastGrpCount++; // Update last group count
	<!--## } ##-->

	<!--## } ##-->
?>

<!--## if (bShowSummaryView) { // Summary view ##-->

<?php
<!--##
		for (var x = 2; x < arSmry.length; x++) {
			k = x-1;
			sFldName = arDtlFlds[k-1]['FldName'];
			sFldObj = arDtlFlds[k-1]['FldObj'];
##-->
			$<!--##=sFldObj##-->->Count = $<!--##=gsPageObj##-->->Cnt[<!--##=lvl##-->][<!--##=k##-->];
<!--##
			for (var j = 0; j < arSmry[x].length; j++) {
				if (arSmry[x][j]) {
##-->
<!--## if (j == 0) { // SUM ##-->
			$<!--##=sFldObj##-->->SumValue = $<!--##=gsPageObj##-->->Smry[<!--##=lvl##-->][<!--##=k##-->]; // Load SUM
<!--## } else if (j == 1) { // AVG ##-->
			$<!--##=sFldObj##-->->AvgValue = ($<!--##=sFldObj##-->->Count > 0) ? $<!--##=gsPageObj##-->->Smry[<!--##=lvl##-->][<!--##=k##-->]/$<!--##=sFldObj##-->->Count : 0; // Load AVG
<!--## } else if (j == 2) { // MIN ##-->
			$<!--##=sFldObj##-->->MinValue = $<!--##=gsPageObj##-->->Mn[<!--##=lvl##-->][<!--##=k##-->]; // Load MIN
<!--## } else if (j == 3) { // MAX ##-->
			$<!--##=sFldObj##-->->MaxValue = $<!--##=gsPageObj##-->->Mx[<!--##=lvl##-->][<!--##=k##-->]; // Load MAX
<!--## } else if (j == 4) { // CNT ##-->
			$<!--##=sFldObj##-->->CntValue = $<!--##=gsPageObj##-->->Cnt[<!--##=lvl##-->][<!--##=k##-->]; // Load CNT
<!--## } ##-->
<!--##
				}
			}; // End for j
		}; // End for x
##-->
			$<!--##=gsPageObj##-->->ResetAttrs();
			$<!--##=gsPageObj##-->->RowType = EWR_ROWTYPE_TOTAL;
			$<!--##=gsPageObj##-->->RowTotalType = EWR_ROWTOTAL_GROUP;
			$<!--##=gsPageObj##-->->RowTotalSubType = EWR_ROWTOTAL_FOOTER;
			$<!--##=gsPageObj##-->->RowGroupLevel = <!--##=lvl##-->;
			$<!--##=gsPageObj##-->->RenderRow();
?>
	<tr<?php echo $<!--##=gsPageObj##-->->RowAttributes(); ?>>
	<!--##
		sSmryGrpFldObj = arGrpFlds[lvl-1]['FldObj'];
		for (var x = 0; x < nGrps; x++) {
			sFldName = arGrpFlds[x]['FldName'];
			sFldParm = arGrpFlds[x]['FldVar'].substr(2);
			sGrpFldObj = arGrpFlds[x]['FldObj'];
			GetFldObj(sFldName);
	##-->
<?php if ($<!--##=sGrpFldObj##-->->Visible) { ?>
	<!--##
			if (x == lvl-1) {
	##-->
		<td data-field="<!--##=sFldParm##-->"<?php echo $<!--##=sGrpFldObj##-->->CellAttributes() ?>><!--##=SYSTEMFUNCTIONS.FieldGroupView()##-->&nbsp;<span class="ewDetailCount">(<?php echo ewr_FormatNumber($<!--##=gsPageObj##-->->Cnt[<!--##=lvl##-->][0],0,-2,-2,-2); ?><!--##@RptDtlRec##-->)</span></td>
	<!--##
			} else if (x < lvl) {
	##-->
		<td data-field="<!--##=sFldParm##-->"<?php echo $<!--##=sGrpFldObj##-->->CellAttributes() ?>><!--##=SYSTEMFUNCTIONS.FieldSummaryGroupView()##--></td>
	<!--##
			} else {
	##-->
		<td data-field="<!--##=sFldParm##-->"<?php echo $<!--##=sSmryGrpFldObj##-->->CellAttributes() ?>>&nbsp;</td>
	<!--##
			}
	##-->
<?php } ?>
	<!--##
		}; // End for x

		for (var x = 2; x < arSmry.length; x++) {
			sFldName = arDtlFlds[x-2]['FldName'];
			sFldParm = arDtlFlds[x-2]['FldVar'].substr(2);
			sFldObj = arDtlFlds[x-2]['FldObj'];
			GetFldObj(sFldName);
			for (var j = 0; j < arSmry[x].length; j++) {
				if (arSmry[x][j]) {
	##-->
<?php if ($<!--##=sFldObj##-->->Visible) { ?>
		<td data-field="<!--##=sFldParm##-->"<?php echo $<!--##=sFldObj##-->->CellAttributes() ?>><!--##=SYSTEMFUNCTIONS.FieldSummaryView(arSmry[0][j],lvl)##--></td>
<?php } ?>
	<!--##
				}
			}; // End for j
		}; // End for x
	##-->
	</tr>

<!--## } else { // Detail view ##-->

<?php
			$<!--##=gsPageObj##-->->ResetAttrs();
			$<!--##=gsPageObj##-->->RowType = EWR_ROWTYPE_TOTAL;
			$<!--##=gsPageObj##-->->RowTotalType = EWR_ROWTOTAL_GROUP;
			$<!--##=gsPageObj##-->->RowTotalSubType = EWR_ROWTOTAL_FOOTER;
			$<!--##=gsPageObj##-->->RowGroupLevel = <!--##=lvl##-->;
			$<!--##=gsPageObj##-->->RenderRow();
?>

	<tr<?php echo $<!--##=gsPageObj##-->->RowAttributes(); ?>>
<!--##
		for (var j = 0; j < i; j++) {
			sGrpFldObj = arGrpFlds[j]['FldObj'];
			sFldName = arGrpFlds[j]['FldName'];
			sFldParm = arGrpFlds[j]['FldVar'].substr(2);
			GetFldObj(sFldName);
##-->
<?php if ($<!--##=sGrpFldObj##-->->Visible) { ?>
<!--## if (bShowDetails) { ##-->
		<td data-field="<!--##=sFldParm##-->"<?php echo $<!--##=sGrpFldObj##-->->CellAttributes() ?>>&nbsp;</td>
<!--## } else { ##-->
		<td data-field="<!--##=sFldParm##-->"<?php echo $<!--##=sGrpFldObj##-->->CellAttributes() ?>><!--##=SYSTEMFUNCTIONS.FieldSummaryGroupView()##--></td>
<!--## } ##-->
<?php } ?>
<!--##
		}; // End for j

		if (ew_IsFieldDrillDown(GROUPFLD)) {
			sSmryPrefix = "<a<?php echo $" + sFldObj + "->LinkAttributes() ?>>";
			sSmrySuffix = "</a>";
		} else {
			sSmryPrefix = "";
			sSmrySuffix = "";
		}
		if (i == 0) {
			sFldCount = "$" + gsPageObj + "->GrpFldCount + $" + gsPageObj + "->DtlFldCount";
		} else {
			sFldCount = "$" + gsPageObj + "->SubGrpFldCount + $" + gsPageObj + "->DtlFldCount";
			if (i > 1)
				sFldCount += " - " + (i-1);
		}
##-->
<?php if (<!--##=sFldCount##--> > 0) { ?>
		<td colspan="<?php echo (<!--##=sFldCount##-->) ?>"<?php echo $<!--##=sFldObj##-->->CellAttributes() ?>><!--##@RptSumHead##--> <?php echo $<!--##=sFldObj##-->->FldCaption() ?>: <!--##=sSmryPrefix##--><?php echo $<!--##=sFldObj##-->->GroupViewValue ?><!--##=sSmrySuffix##--> <span class="ewDirLtr">(<?php echo ewr_FormatNumber($<!--##=gsPageObj##-->->Cnt[<!--##=lvl##-->][0],0,-2,-2,-2) ?><!--##@RptDtlRec##-->)</span></td>
<?php } ?>
</tr>
<!--##
		sGrpFldObj = arGrpFlds[i]['FldObj'];
		for (var j = 0; j < arSmry[1].length; j++) {
			if (arSmry[1][j]) {
##-->
<?php
			$<!--##=gsPageObj##-->->ResetAttrs();
<!--##
				for (var k = 1; k <= nDtls; k++) {
					if (arSmry[k+1][j]) {
						sFldObj = arDtlFlds[k-1]['FldObj'];
##-->
			$<!--##=sFldObj##-->->Count = $<!--##=gsPageObj##-->->Cnt[<!--##=lvl##-->][<!--##=k##-->];
<!--## if (j == 0) { // SUM ##-->
			$<!--##=sFldObj##-->->SumValue = $<!--##=gsPageObj##-->->Smry[<!--##=lvl##-->][<!--##=k##-->]; // Load SUM
<!--## } else if (j == 1) { // AVG ##-->
			$<!--##=sFldObj##-->->AvgValue = ($<!--##=sFldObj##-->->Count > 0)? $<!--##=gsPageObj##-->->Smry[<!--##=lvl##-->][<!--##=k##-->]/$<!--##=sFldObj##-->->Count : 0; // Load AVG
<!--## } else if (j == 2) { // MIN ##-->
			$<!--##=sFldObj##-->->MinValue = $<!--##=gsPageObj##-->->Mn[<!--##=lvl##-->][<!--##=k##-->]; // Load MIN
<!--## } else if (j == 3) { // MAX ##-->
			$<!--##=sFldObj##-->->MaxValue = $<!--##=gsPageObj##-->->Mx[<!--##=lvl##-->][<!--##=k##-->]; // Load MAX
<!--## } else if (j == 4) { // CNT ##-->
			$<!--##=sFldObj##-->->CntValue = $<!--##=gsPageObj##-->->Cnt[<!--##=lvl##-->][<!--##=k##-->]; // Load CNT
<!--## } ##-->
<!--##
					}
				}; // End for k
##-->
			$<!--##=gsPageObj##-->->RowTotalSubType = EWR_ROWTOTAL_<!--##=arSmry[0][j].toUpperCase()##-->;
			$<!--##=gsPageObj##-->->RenderRow();
?>
	<tr<?php echo $<!--##=gsPageObj##-->->RowAttributes(); ?>>
<!--##
				for (k = 0; k < i; k++) {
					sGrpFldObj = arGrpFlds[k]['FldObj'];
					sFldParm = arGrpFlds[k]['FldVar'].substr(2);
##-->
<?php if ($<!--##=sGrpFldObj##-->->Visible) { ?>
		<td data-field="<!--##=sFldParm##-->"<?php echo $<!--##=sGrpFldObj##-->->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<!--##
				}; // End for k
				sGrpFldObj = arGrpFlds[i]['FldObj'];
##-->
		<td colspan="<?php echo ($<!--##=gsPageObj##-->->GrpFldCount - <!--##=i##-->) ?>"<?php echo $<!--##=sGrpFldObj##-->->CellAttributes() ?>><!--##=SummaryCaption(arSmry[0][j])##--></td>
<!--##
				for (var k = 1; k <= nDtls; k++) {
					sFldName = arDtlFlds[k-1]['FldName'];
					sFldParm = arDtlFlds[k-1]['FldVar'].substr(2);
					GetFldObj(sFldName);
##-->
<?php if ($<!--##=gsFldObj##-->->Visible) { ?>
<!--##
					if (arSmry[k+1][j]) {
##-->
		<td data-field="<!--##=sFldParm##-->"<?php echo $<!--##=sFldObj##-->->CellAttributes() ?>><!--##=SYSTEMFUNCTIONS.FieldSummaryView(arSmry[0][j],lvl)##--></td>
<!--##
					} else {
##-->
		<td data-field="<!--##=sFldParm##-->"<?php echo $<!--##=sGrpFldObj##-->->CellAttributes() ?>>&nbsp;</td>
<!--##
					}
##-->
<?php } ?>
<!--##
				}; // End for k
##-->
	</tr>
<!--##
			}
		}; // End for j
##-->	

<!--## }; // End Summary/Detail view ##-->

<?php
			// Reset level <!--##=lvl##--> summary
			$<!--##=gsPageObj##-->->ResetLevelSummary(<!--##=lvl##-->);

		} // End show footer check

	<!--## if (nGrps >= 2 && lvl > 1) { ##-->
		if ($<!--##=gsPageObj##-->->ChkLvlBreak(<!--##=lvl##-->)) {
			$<!--##=gsPageObj##-->->GrpCounter[<!--##=lvl-2##-->]++;
	<!--##
		suffix = "";
		for (j = lvl; j < nGrps; j++) {
			suffix += "[$" + gsPageObj + "->GrpCounter[" + (j-2) + "]]";
	##-->
			if (!$rs->EOF)
				$<!--##=gsPageObj##-->->GrpIdx[$<!--##=gsPageObj##-->->GrpCount]<!--##=suffix##--> = array(-1);
			$<!--##=gsPageObj##-->->GrpCounter[<!--##=j-1##-->] = 1;
	<!--##
		}
	##-->
		}
	<!--## } ##-->

?>
<!--##
		} // End show summary
	}; // End for i
##-->
<?php

<!--## if (nGrps > 0) { ##-->

	// Next group
	$<!--##=gsPageObj##-->->GetGrpRow(2);

	// Show header if page break
	if ($<!--##=gsPageObj##-->->Export <> "")
		$<!--##=gsPageObj##-->->ShowHeader = ($<!--##=gsPageObj##-->->ExportPageBreakCount == 0) ? FALSE : ($<!--##=gsPageObj##-->->GrpCount % $<!--##=gsPageObj##-->->ExportPageBreakCount == 0);

	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist(sCtrlType,"Page_Breaking")) { ##-->
	// Page_Breaking server event
	if ($<!--##=gsPageObj##-->->ShowHeader)
		$<!--##=gsPageObj##-->->Page_Breaking($<!--##=gsPageObj##-->->ShowHeader, $<!--##=gsPageObj##-->->PageBreakContent);
	<!--## } ##-->

	$<!--##=gsPageObj##-->->GrpCount++;
	<!--##
		if (nGrps >= 2) {
			for (j = 1; j < nGrps; j++) {
	##-->
	$<!--##=gsPageObj##-->->GrpCounter[<!--##=nGrps-j-1##-->] = 1;
	<!--##
			}
		}
	##-->

	// Handle EOF
	if (!$rsgrp || $rsgrp->EOF)
		$<!--##=gsPageObj##-->->ShowHeader = FALSE;

<!--## } ##-->

<!--## }; // End grouping fields ##-->

} // End while

?>
<?php if ($<!--##=gsPageObj##-->->TotalGrps > 0) { ?>
</tbody>
<tfoot>
<!--## if (TABLE.TblRptShowPageTotal) { ##-->
<!--##
	// Hide page total if grand total = page total
	if (TABLE.TblRptShowGrandTotal) {
		sCheckPageTotalStart = "<?php if (($" + gsPageObj + "->StopGrp - $" + gsPageObj + "->StartGrp + 1) <> $" + gsPageObj + "->TotalGrps) { ?>";
		sCheckPageTotalEnd = "<?php } ?>";
	} else {
		sCheckPageTotalStart = "";
		sCheckPageTotalEnd = "";
	}
##-->
<!--##=sCheckPageTotalStart##-->
<?php
	$<!--##=gsPageObj##-->->ResetAttrs();
	$<!--##=gsPageObj##-->->RowType = EWR_ROWTYPE_TOTAL;
	$<!--##=gsPageObj##-->->RowTotalType = EWR_ROWTOTAL_PAGE;
	$<!--##=gsPageObj##-->->RowTotalSubType = EWR_ROWTOTAL_FOOTER;
	$<!--##=gsPageObj##-->->RowAttrs["class"] = "ewRptPageSummary";
	$<!--##=gsPageObj##-->->RenderRow();
?>

<!--## if (bShowSummaryView) { // Summary view ##-->

<?php
<!--##
		for (var i = 2; i < arSmry.length; i++) {
			k = i-1;
			sFldObj = arDtlFlds[k-1]['FldObj'];
##-->
	$<!--##=sFldObj##-->->Count = $<!--##=gsPageObj##-->->Cnt[0][<!--##=k##-->];
<!--##
			for (var j = 0; j < arSmry[i].length; j++) {
				if (arSmry[i][j]) {
##-->
<!--## if (j == 0) { // SUM ##-->
	$<!--##=sFldObj##-->->SumValue = $<!--##=gsPageObj##-->->Smry[0][<!--##=k##-->]; // Load SUM
<!--## } else if (j == 1) { // AVG ##-->
	$<!--##=sFldObj##-->->AvgValue = ($<!--##=sFldObj##-->->Count > 0) ? $<!--##=gsPageObj##-->->Smry[0][<!--##=k##-->]/$<!--##=sFldObj##-->->Count : 0; // Load AVG
<!--## } else if (j == 2) { // MIN ##-->
	$<!--##=sFldObj##-->->MinValue = $<!--##=gsPageObj##-->->Mn[0][<!--##=k##-->]; // Load MIN
<!--## } else if (j == 3) { // MAX ##-->
	$<!--##=sFldObj##-->->MaxValue = $<!--##=gsPageObj##-->->Mx[0][<!--##=k##-->]; // Load MAX
<!--## } else if (j == 4) { // CNT ##-->
	$<!--##=sFldObj##-->->CntValue = $<!--##=gsPageObj##-->->Cnt[0][<!--##=k##-->]; // Load CNT
<!--## } ##-->
<!--##
				}
			}; // End for j
		}; // End for i
##-->
	$<!--##=gsPageObj##-->->ResetAttrs();
	$<!--##=gsPageObj##-->->RowType = EWR_ROWTYPE_TOTAL;
	$<!--##=gsPageObj##-->->RowTotalType = EWR_ROWTOTAL_PAGE;
	$<!--##=gsPageObj##-->->RowTotalSubType = EWR_ROWTOTAL_FOOTER;
	$<!--##=gsPageObj##-->->RowAttrs["class"] = "ewRptPageSummary";
	$<!--##=gsPageObj##-->->RenderRow();
?>
	<tr<?php echo $<!--##=gsPageObj##-->->RowAttributes(); ?>>
	<!--##
		for (var i = 0; i < nGrps; i++) {
			sFldParm = arGrpFlds[i]['FldVar'].substr(2);
			sGrpFldObj = arGrpFlds[i]['FldObj'];
			if (i == 0) {
	##-->
		<td><!--##@RptPageTotal##-->&nbsp;<span class="ewDetailCount">(<?php echo ewr_FormatNumber($<!--##=gsPageObj##-->->Cnt[0][0],0,-2,-2,-2); ?><!--##@RptDtlRec##-->)</span></td>
	<!--##
			} else {
	##-->
<?php if ($<!--##=sGrpFldObj##-->->Visible) { ?>
		<td data-field="<!--##=sFldParm##-->">&nbsp;</td>
<?php } ?>
	<!--##
			}
		}; // End for i

		for (var i = 2; i < arSmry.length; i++) {
			sFldName = arDtlFlds[i-2]['FldName'];
			sFldParm = arDtlFlds[i-2]['FldVar'].substr(2);
			sFldObj = arDtlFlds[i-2]['FldObj'];
			GetFldObj(sFldName);
			for (j = 0; j < arSmry[i].length; j++) {
				if (arSmry[i][j]) {
	##-->
<?php if ($<!--##=sFldObj##-->->Visible) { ?>
		<td data-field="<!--##=sFldParm##-->"<?php echo $<!--##=sFldObj##-->->CellAttributes() ?>><!--##=SYSTEMFUNCTIONS.FieldSummaryView("page"+arSmry[0][j],0)##--></td>
<?php } ?>
	<!--##
				}
			}; // End for j
		}; // End for i
	##-->
	</tr>

<!--## } else { // Detail view ##-->

	<tr<?php echo $<!--##=gsPageObj##-->->RowAttributes(); ?>><td colspan="<?php echo ($<!--##=gsPageObj##-->->GrpFldCount + $<!--##=gsPageObj##-->->DtlFldCount) ?>"><!--##@RptPageTotal##--> <span class="ewDirLtr">(<?php echo ewr_FormatNumber($<!--##=gsPageObj##-->->Cnt[0][0],0,-2,-2,-2); ?><!--##@RptDtlRec##-->)</span></td></tr>

	<!--##
	for (var j = 0; j < arSmry[1].length; j++) {
		if (arSmry[1][j]) {
	##-->
<?php
	$<!--##=gsPageObj##-->->ResetAttrs();
<!--##
		for (var k = 1; k <= nDtls; k++) {
			if (arSmry[k+1][j]) {
				sFldObj = arDtlFlds[k-1]['FldObj'];
##-->
	$<!--##=sFldObj##-->->Count = $<!--##=gsPageObj##-->->Cnt[0][<!--##=k##-->];
<!--## if (j == 0) { // SUM ##-->
	$<!--##=sFldObj##-->->SumValue = $<!--##=gsPageObj##-->->Smry[0][<!--##=k##-->]; // Load SUM
<!--## } else if (j == 1) { // AVG ##-->
	$<!--##=sFldObj##-->->AvgValue = ($<!--##=sFldObj##-->->Count > 0) ? $<!--##=gsPageObj##-->->Smry[0][<!--##=k##-->]/$<!--##=sFldObj##-->->Count : 0; // Load AVG
<!--## } else if (j == 2) { // MIN ##-->
	$<!--##=sFldObj##-->->MinValue = $<!--##=gsPageObj##-->->Mn[0][<!--##=k##-->]; // Load MIN
<!--## } else if (j == 3) { // MAX ##-->
	$<!--##=sFldObj##-->->MaxValue = $<!--##=gsPageObj##-->->Mx[0][<!--##=k##-->]; // Load MAX
<!--## } else if (j == 4) { // CNT ##-->
	$<!--##=sFldObj##-->->CntValue = $<!--##=gsPageObj##-->->Cnt[0][<!--##=k##-->]; // Load CNT
<!--## } ##-->
	$<!--##=gsPageObj##-->->RowTotalSubType = EWR_ROWTOTAL_<!--##=arSmry[0][j].toUpperCase()##-->;
<!--##
			}
		}; // End for k
##-->
	$<!--##=gsPageObj##-->->RowAttrs["class"] = "ewRptPageSummary";
	$<!--##=gsPageObj##-->->RenderRow();
?>
	<tr<?php echo $<!--##=gsPageObj##-->->RowAttributes(); ?>>
	<!--##
		if (nGrps > 0) {
	##-->
<?php if ($<!--##=gsPageObj##-->->GrpFldCount > 0) { ?>
		<td colspan="<?php echo $<!--##=gsPageObj##-->->GrpFldCount ?>" class="ewRptGrpAggregate"><!--##=SummaryCaption(arSmry[0][j])##--></td>
<?php } ?>
	<!--##
		}
	##-->

	<!--##
		for (var k = 1; k <= nDtls; k++) {
			sFldName = arDtlFlds[k-1]['FldName'];
			sFldParm = arDtlFlds[k-1]['FldVar'].substr(2);
			sFldObj = arDtlFlds[k-1]['FldObj'];
	##-->
<?php if ($<!--##=sFldObj##-->->Visible) { ?>
	<!--##
			if (arSmry[k+1][j]) {
				GetFldObj(sFldName);
				if (nGrps == 0) {
					sSmryCaption = SummaryCaption(arSmry[0][j]);
				} else {
					sSmryCaption = "";
				}
				if (ew_IsNotEmpty(sSmryCaption)) sSmryCaption += "<?php echo $ReportLanguage->Phrase(\"RptSeparator\"); ?>";
	##-->
		<td data-field="<!--##=sFldParm##-->"<?php echo $<!--##=sFldObj##-->->CellAttributes() ?>><!--##=sSmryCaption##--><!--##=SYSTEMFUNCTIONS.FieldSummaryView("page"+arSmry[0][j],0)##--></td>
	<!--##
			} else {
	##-->
		<td data-field="<!--##=sFldParm##-->"<?php echo $<!--##=sFldObj##-->->CellAttributes() ?>>&nbsp;</td>
	<!--##
			}
	##-->
<?php } ?>
	<!--##
		}; // End for k
	##-->
	</tr>
	<!--##
		}
	}; // End for j
	##-->

<!--## }; // End Summary/Detail view ##-->

<!--##=sCheckPageTotalEnd##-->
<!--## }; // End show page total ##-->
<!--## if (TABLE.TblRptShowGrandTotal) { ##-->
<?php
	$<!--##=gsPageObj##-->->ResetAttrs();
	$<!--##=gsPageObj##-->->RowType = EWR_ROWTYPE_TOTAL;
	$<!--##=gsPageObj##-->->RowTotalType = EWR_ROWTOTAL_GRAND;
	$<!--##=gsPageObj##-->->RowTotalSubType = EWR_ROWTOTAL_FOOTER;
	$<!--##=gsPageObj##-->->RowAttrs["class"] = "ewRptGrandSummary";
	$<!--##=gsPageObj##-->->RenderRow();
?>

<!--## if (bShowSummaryView) { // Summary view ##-->

<?php
<!--##
		for (var i = 2; i < arSmry.length; i++) {
			k = i-1;
			sFldObj = arDtlFlds[k-1]['FldObj'];
##-->
	$<!--##=sFldObj##-->->Count = $<!--##=gsPageObj##-->->GrandCnt[<!--##=k##-->];
<!--##
			for (var j = 0; j < arSmry[i].length; j++) {
				if (arSmry[i][j]) {
##-->
<!--## if (j == 0) { // SUM ##-->
	$<!--##=sFldObj##-->->SumValue = $<!--##=gsPageObj##-->->GrandSmry[<!--##=k##-->]; // Load SUM
<!--## } else if (j == 1) { // AVG ##-->
	$<!--##=sFldObj##-->->AvgValue = ($<!--##=sFldObj##-->->Count > 0) ? $<!--##=gsPageObj##-->->GrandSmry[<!--##=k##-->]/$<!--##=sFldObj##-->->Count : 0; // Load AVG
<!--## } else if (j == 2) { // MIN ##-->
	$<!--##=sFldObj##-->->MinValue = $<!--##=gsPageObj##-->->GrandMn[<!--##=k##-->]; // Load MIN
<!--## } else if (j == 3) { // MAX ##-->
	$<!--##=sFldObj##-->->MaxValue = $<!--##=gsPageObj##-->->GrandMx[<!--##=k##-->]; // Load MAX
<!--## } else if (j == 4) { // CNT ##-->
	$<!--##=sFldObj##-->->CntValue = $<!--##=gsPageObj##-->->GrandCnt[<!--##=k##-->]; // Load CNT
<!--## } ##-->
<!--##
				}
			}; // End for j
		}; // End for i
##-->
	$<!--##=gsPageObj##-->->ResetAttrs();
	$<!--##=gsPageObj##-->->RowType = EWR_ROWTYPE_TOTAL;
	$<!--##=gsPageObj##-->->RowTotalType = EWR_ROWTOTAL_GRAND;
	$<!--##=gsPageObj##-->->RowTotalSubType = EWR_ROWTOTAL_FOOTER;
	$<!--##=gsPageObj##-->->RowAttrs["class"] = "ewRptGrandSummary";
	$<!--##=gsPageObj##-->->RenderRow();
?>
	<tr<?php echo $<!--##=gsPageObj##-->->RowAttributes(); ?>>
	<!--##
		for (var i = 0; i < nGrps; i++) {
			sGrpFldParm = arGrpFlds[i]['FldVar'].substr(2);
			sGrpFldObj = arGrpFlds[i]['FldObj'];
			if (i == 0) {
	##-->
		<td><!--##@RptGrandTotal##-->&nbsp;<span class="ewDetailCount">(<?php echo ewr_FormatNumber($<!--##=gsPageObj##-->->TotCount,0,-2,-2,-2); ?><!--##@RptDtlRec##-->)</span></td>
	<!--##
			} else {
	##-->
<?php if ($<!--##=sGrpFldObj##-->->Visible) { ?>
		<td data-field="<!--##=sFldParm##-->">&nbsp;</td>
<?php } ?>
	<!--##
			}
		}; // End for i

		for (var i = 2; i < arSmry.length; i++) {
			sFldName = arDtlFlds[i-2]['FldName'];
			sFldParm = arDtlFlds[i-2]['FldVar'].substr(2);
			sFldObj = arDtlFlds[i-2]['FldObj'];
			GetFldObj(sFldName);
			for (var j = 0; j < arSmry[i].length; j++) {
				if (arSmry[i][j]) {
	##-->
<?php if ($<!--##=sFldObj##-->->Visible) { ?>
		<td data-field="<!--##=sFldParm##-->"<?php echo $<!--##=sFldObj##-->->CellAttributes() ?>><!--##=SYSTEMFUNCTIONS.FieldSummaryView("grand"+arSmry[0][j],0)##--></td>
<?php } ?>
	<!--##
				}
			}; // End for j
		}; // End for i
	##-->
	</tr>

<!--## } else { // Detail view ##-->

	<tr<?php echo $<!--##=gsPageObj##-->->RowAttributes(); ?>><td colspan="<?php echo ($<!--##=gsPageObj##-->->GrpFldCount + $<!--##=gsPageObj##-->->DtlFldCount) ?>"><!--##@RptGrandTotal##--> <span class="ewDirLtr">(<?php echo ewr_FormatNumber($<!--##=gsPageObj##-->->TotCount,0,-2,-2,-2); ?><!--##@RptDtlRec##-->)</span></td></tr>

	<!--##
	for (var j = 0; j < arSmry[1].length; j++) {
		if (arSmry[1][j]) {
	##-->
<?php
	$<!--##=gsPageObj##-->->ResetAttrs();
<!--##
		for (var k = 1; k <= nDtls; k++) {
			if (arSmry[k+1][j]) {
				sFldObj = arDtlFlds[k-1]['FldObj'];
##-->
	$<!--##=sFldObj##-->->Count = $<!--##=gsPageObj##-->->GrandCnt[<!--##=k##-->];
<!--## if (j == 0) { // SUM ##-->
	$<!--##=sFldObj##-->->SumValue = $<!--##=gsPageObj##-->->GrandSmry[<!--##=k##-->]; // Load SUM
<!--## } else if (j == 1) { // AVG ##-->
	$<!--##=sFldObj##-->->AvgValue = ($<!--##=sFldObj##-->->Count > 0) ? $<!--##=gsPageObj##-->->GrandSmry[<!--##=k##-->]/$<!--##=sFldObj##-->->Count : 0; // Load AVG
<!--## } else if (j == 2) { // MIN ##-->
	$<!--##=sFldObj##-->->MinValue = $<!--##=gsPageObj##-->->GrandMn[<!--##=k##-->]; // Load MIN
<!--## } else if (j == 3) { // MAX ##-->
	$<!--##=sFldObj##-->->MaxValue = $<!--##=gsPageObj##-->->GrandMx[<!--##=k##-->]; // Load MAX
<!--## } else if (j == 4) { // CNT ##-->
	$<!--##=sFldObj##-->->CntValue = $<!--##=gsPageObj##-->->GrandCnt[<!--##=k##-->]; // Load CNT
<!--## } ##-->
	$<!--##=gsPageObj##-->->RowTotalSubType = EWR_ROWTOTAL_<!--##=arSmry[0][j].toUpperCase()##-->;
<!--##
			}
		}; // End for k
##-->
	$<!--##=gsPageObj##-->->RowAttrs["class"] = "ewRptGrandSummary";
	$<!--##=gsPageObj##-->->RenderRow();
?>
	<tr<?php echo $<!--##=gsPageObj##-->->RowAttributes(); ?>>
	<!--##
		if (nGrps > 0) {
	##-->
<?php if ($<!--##=gsPageObj##-->->GrpFldCount > 0) { ?>
		<td colspan="<?php echo $<!--##=gsPageObj##-->->GrpFldCount ?>" class="ewRptGrpAggregate"><!--##=SummaryCaption(arSmry[0][j])##--></td>
<?php } ?>
	<!--##
		}
	##-->
	<!--##
		for (var k = 1; k <= nDtls; k++) {
			sFldName = arDtlFlds[k-1]['FldName'];
			sFldParm = arDtlFlds[k-1]['FldVar'].substr(2);
			sFldObj = arDtlFlds[k-1]['FldObj'];
	##-->
<?php if ($<!--##=sFldObj##-->->Visible) { ?>
	<!--##
			if (arSmry[k+1][j]) {
				GetFldObj(sFldName);
				if (nGrps == 0) {
					sSmryCaption = SummaryCaption(arSmry[0][j]);
				} else {
					sSmryCaption = "";
				}
				if (ew_IsNotEmpty(sSmryCaption)) sSmryCaption += "<?php echo $ReportLanguage->Phrase(\"RptSeparator\"); ?>";
	##-->
		<td data-field="<!--##=sFldParm##-->"<?php echo $<!--##=sFldObj##-->->CellAttributes() ?>><!--##=sSmryCaption##--><!--##=SYSTEMFUNCTIONS.FieldSummaryView("grand"+arSmry[0][j],0)##--></td>
	<!--##
			} else {
	##-->
		<td data-field="<!--##=sFldParm##-->"<?php echo $<!--##=sFldObj##-->->CellAttributes() ?>>&nbsp;</td>
	<!--##
			}
	##-->
<?php } ?>
	<!--##
		}; // End for k
	##-->
	</tr>
	<!--##
		}
	}; // End for j
	##-->	

<!--## }; // End Summary/Detail view ##-->

<!--## }; // End show grand total ##-->
	</tfoot>
<?php } elseif (!$<!--##=gsPageObj##-->->ShowHeader && <!--##=ew_Val(nSearchFlds > 0)##-->) { // No header displayed ?>
<!--##include rpt-phpcommon-table.php/report-header##-->
<?php } ?>

<?php if ($<!--##=gsPageObj##-->->TotalGrps > 0 || <!--##=ew_Val(nSearchFlds > 0)##-->) { // Show footer ?>
<!--##include rpt-phpcommon-table.php/report-footer##-->
<?php } ?>

<!--## }; // End Show Report ##-->

<!--##=sSkipPdfExpStart##-->
</div>
<!--##=sSkipPdfExpEnd##-->

<!--## if (bShowReport) { ##-->
<!--##include rpt-phpcommon-table.php/customtemplate##-->
<!--## }; ##-->

<!-- Summary Report Ends -->

<!--##=sHtmlExpStart##-->
	</div>
	<!-- center container - report (end) -->

	<!-- right container (begin) -->
	<div id="ewRight" class="ewRight">
<!--##=sHtmlExpEnd##-->

	<!-- Right slot -->
<!--##
	// Generate charts (on right)
	for (var i = 0, len = arAllCharts.length; i < len; i++) {
		if (GetChtObj(arAllCharts[i])) {
			if (IsShowChart(goCht, 3)) {
##-->
<!--##include rpt-chartcommon.php/chart_common##-->
<!--##include rpt-chartcommon.php/chart_html##-->
<!--##include rpt-chartcommon.php/chart_include##-->
<!--##
			}
		}
	}; // End for i, charts on right
##-->

<!--##=sHtmlExpStart##-->
	</div>
	<!-- right container (end) -->

<div class="clearfix"></div>

<!-- bottom container (begin) -->
<div id="ewBottom" class="ewBottom">
<!--##=sHtmlExpEnd##-->

	<!-- Bottom slot -->
<!--##
	// Generate charts (on bottom)
	for (var i = 0, len = arAllCharts.length; i < len; i++) {
		if (GetChtObj(arAllCharts[i])) {
			if (IsShowChart(goCht, 4)) {
##-->
<!--##include rpt-chartcommon.php/chart_common##-->
<!--##include rpt-chartcommon.php/chart_html##-->
<!--##include rpt-chartcommon.php/chart_include##-->
<!--##
			}
		}
	}; // End for i, charts on bottom
##-->

<!--##=sHtmlExpStart##-->
	</div>
<!-- Bottom Container (End) -->

</div>
<!-- Table Container (End) -->
<!--##=sHtmlExpEnd##-->

<!--##include rpt-phpcommon.php/footer-message##-->
<?php
// Close recordsets
if ($rsgrp) $rsgrp->Close();
if ($rs) $rs->Close();
?>
<!--##/session##-->


<?php
<!--##session phpfunction##-->

	<!--## if (nGrps > 0) { ##-->
	// Check level break
	function ChkLvlBreak($lvl) {
		switch ($lvl) {
	<!--##
	for (var i = 0; i < nGrps; i++) {
		sFldObj = "this->" + arGrpFlds[i]['FldVar'].substr(2);
		if (i > 0) {
			sChk = " || $this->ChkLvlBreak(" + i + "); // Recurse upper level";
		} else {
			sChk = ";";
		}
		fld = "$" + sFldObj + "->CurrentValue";
		oldfld = "$" + sFldObj + "->OldValue";
		grpfld = "$" + sFldObj + "->GroupValue()";
		grpoldfld = "$" + sFldObj + "->GroupOldValue()";
	##-->
			case <!--##=i+1##-->:
				return (is_null(<!--##=fld##-->) && !is_null(<!--##=oldfld##-->)) ||
					(!is_null(<!--##=fld##-->) && is_null(<!--##=oldfld##-->)) ||
					(<!--##=grpfld##--> <> <!--##=grpoldfld##-->)<!--##=sChk##-->
	<!--##
	}; // End for i
	##-->
		}
	}
	<!--## } ##-->

	// Accummulate summary
	function AccumulateSummary() {
		$cntx = count($this->Smry);
		for ($ix = 0; $ix < $cntx; $ix++) {
			$cnty = count($this->Smry[$ix]);
			for ($iy = 1; $iy < $cnty; $iy++) {
				if ($this->Col[$iy][0]) { // Accumulate required
					$valwrk = $this->Val[$iy];
					if (is_null($valwrk)) {
						if (!$this->Col[$iy][1])
							$this->Cnt[$ix][$iy]++;
					} else {
						$accum = (!$this->Col[$iy][1] || !is_numeric($valwrk) || $valwrk <> 0);
						if ($accum) {
							$this->Cnt[$ix][$iy]++;
							if (is_numeric($valwrk)) {
								$this->Smry[$ix][$iy] += $valwrk;
								if (is_null($this->Mn[$ix][$iy])) {
									$this->Mn[$ix][$iy] = $valwrk;
									$this->Mx[$ix][$iy] = $valwrk;
								} else {
									if ($this->Mn[$ix][$iy] > $valwrk) $this->Mn[$ix][$iy] = $valwrk;
									if ($this->Mx[$ix][$iy] < $valwrk) $this->Mx[$ix][$iy] = $valwrk;
								}
							}
						}
					}
				}
			}
		}
		$cntx = count($this->Smry);
		for ($ix = 0; $ix < $cntx; $ix++) {
			$this->Cnt[$ix][0]++;
		}
	}

	// Reset level summary
	function ResetLevelSummary($lvl) {
		// Clear summary values
		$cntx = count($this->Smry);
		for ($ix = $lvl; $ix < $cntx; $ix++) {
			$cnty = count($this->Smry[$ix]);
			for ($iy = 1; $iy < $cnty; $iy++) {
				$this->Cnt[$ix][$iy] = 0;
				if ($this->Col[$iy][0]) {
					$this->Smry[$ix][$iy] = 0;
					$this->Mn[$ix][$iy] = NULL;
					$this->Mx[$ix][$iy] = NULL;
				}
			}
		}
		$cntx = count($this->Smry);
		for ($ix = $lvl; $ix < $cntx; $ix++) {
			$this->Cnt[$ix][0] = 0;
		}

	<!--## if (bShowSummaryView && nGrps > 1) { ##-->
		//Reset last group count
		if ($lvl == <!--##=nGrps-1##-->)
			$this->LastGrpCount = 0;
	<!--## } ##-->

		// Reset record count
		$this->RecCount = 0;

	}

	// Accummulate grand summary
	function AccumulateGrandSummary() {
		$this->TotCount++;
		$cntgs = count($this->GrandSmry);
		for ($iy = 1; $iy < $cntgs; $iy++) {
			if ($this->Col[$iy][0]) {
				$valwrk = $this->Val[$iy];
				if (is_null($valwrk) || !is_numeric($valwrk)) {
					if (!$this->Col[$iy][1])
						$this->GrandCnt[$iy]++;
				} else {
					if (!$this->Col[$iy][1] || $valwrk <> 0) {
						$this->GrandCnt[$iy]++;
						$this->GrandSmry[$iy] += $valwrk;
						if (is_null($this->GrandMn[$iy])) {
							$this->GrandMn[$iy] = $valwrk;
							$this->GrandMx[$iy] = $valwrk;
						} else {
							if ($this->GrandMn[$iy] > $valwrk) $this->GrandMn[$iy] = $valwrk;
							if ($this->GrandMx[$iy] < $valwrk) $this->GrandMx[$iy] = $valwrk;
						}
					}
				}
			}
		}
	}

<!--## if (nGrps > 0) { ##-->
	// Get group count
	function GetGrpCnt($sql) {
		global $conn;

		$rsgrpcnt = $conn->Execute($sql);

		$grpcnt = ($rsgrpcnt) ? $rsgrpcnt->RecordCount() : 0;

		if ($rsgrpcnt) $rsgrpcnt->Close();
		return $grpcnt;
	}

	// Get group recordset
	function GetGrpRs($wrksql, $start = -1, $grps = -1) {
		global $conn;
		$conn->raiseErrorFn = $GLOBALS["EWR_ERROR_FN"];
	<!--## if (bDBMySql || bDBPostgreSql || bDBOracle) { ##-->
		$rswrk = $conn->SelectLimit($wrksql, $grps, $start - 1);
	<!--## } else { ##-->
		$rswrk = $conn->Execute($wrksql);
		if ($start > 1)
			$rswrk->Move($start - 1);
	<!--## } ##-->
		$conn->raiseErrorFn = '';
		return $rswrk;
	}

	// Get group row values
	function GetGrpRow($opt) {
		global $rsgrp;

		if (!$rsgrp)
			return;

		if ($opt == 1) { // Get first group
			//$rsgrp->MoveFirst(); // NOTE: no need to move position
			$this-><!--##=arFirstGrpFld['FldVar'].substr(2)##-->->setDbValue(""); // Init first value
		} else { // Get next group
			$rsgrp->MoveNext();
		}

		if (!$rsgrp->EOF)
			$this-><!--##=arFirstGrpFld['FldVar'].substr(2)##-->->setDbValue($rsgrp->fields[0]);

		if ($rsgrp->EOF) {
			$this-><!--##=arFirstGrpFld['FldVar'].substr(2)##-->->setDbValue("");
		}

	}

<!--## } else { ##-->

	// Get count
	function GetCnt($sql) {
		global $conn;
		$rscnt = $conn->Execute($sql);
		$cnt = ($rscnt) ? $rscnt->RecordCount() : 0;
		if ($rscnt) $rscnt->Close();
		return $cnt;
	}

	// Get recordset
	function GetRs($wrksql, $start, $grps) {
		global $conn;
		$conn->raiseErrorFn = $GLOBALS["EWR_ERROR_FN"];
	<!--## if (bDBMySql || bDBPostgreSql || bDBOracle) { ##-->
		$rswrk = $conn->SelectLimit($wrksql, $grps, $start - 1);
	<!--## } else { ##-->
		$rswrk = $conn->Execute($wrksql);
		if ($start > 1)
			$rswrk->Move($start - 1);
	<!--## } ##-->
		$conn->raiseErrorFn = '';
		return $rswrk;
	}

<!--## } ##-->

	// Get row values
	function GetRow($opt) {
		global $rs;
		if (!$rs)
			return;
		if ($opt == 1) { // Get first row
	//		$rs->MoveFirst(); // NOTE: no need to move position
	<!--## if (nGrps > 0) { ##-->
			if ($this->GrpCount == 1) {
	<!--## } ##-->
				$this->FirstRowData = array();
	<!--##
		for (var i = 0; i < nAllFldCount; i++) {
			GetFldObj(arAllFlds[i]);
			if (!ew_IsBinaryField(goFld) && goFld.FldType != 201 && goFld.FldType != 203) { // Blob / adLongVarChar / adLongVarWChar
	##-->
				$this->FirstRowData['<!--##=gsFldParm##-->'] = ewr_Conv($rs->fields('<!--##=ew_SQuote(gsFldName)##-->'),<!--##=goFld.FldType##-->);
	<!--##
			}
		}
	##-->
	<!--## if (nGrps > 0) { ##-->
			}
	<!--## } ##-->
		} else { // Get next row
			$rs->MoveNext();
		}
		if (!$rs->EOF) {
	<!--##
		for (var i = 0; i < nAllFldCount; i++) {
			GetFldObj(arAllFlds[i]);
			sFldObj = "this->" + gsFldParm;
			if (gsFldName == arFirstGrpFld['FldName']) {
	##-->
			if ($opt <> 1) {
				if (is_array($<!--##=sFldObj##-->->GroupDbValues))
					$<!--##=sFldObj##-->->setDbValue(@$<!--##=sFldObj##-->->GroupDbValues[$rs->fields('<!--##=ew_SQuote(gsFldName)##-->')]);
				else
					$<!--##=sFldObj##-->->setDbValue(ewr_GroupValue($<!--##=sFldObj##-->, $rs->fields('<!--##=ew_SQuote(gsFldName)##-->')));
			}
	<!--##
			} else {
	##-->
			$<!--##=sFldObj##-->->setDbValue($rs->fields('<!--##=ew_SQuote(gsFldName)##-->'));
	<!--##
			}
		}

		for (var i = 0; i < nDtls; i++) {
			sFldObj = "this->" + arDtlFlds[i]['FldVar'].substr(2);
	##-->
			$this->Val[<!--##=i+1##-->] = $<!--##=sFldObj##-->->CurrentValue;
	<!--##
		}
	##-->
		} else {
	<!--##
		for (var i = 0; i < nAllFldCount; i++) {
			GetFldObj(arAllFlds[i]);
			sFldObj = "this->" + gsFldParm;
	##-->
			$<!--##=sFldObj##-->->setDbValue("");
	<!--##
		}
	##-->
		}
	}

	//  Set up starting group
	function SetUpStartGroup() {

		// Exit if no groups
		if ($this->DisplayGrps == 0)
			return;

		// Check for a 'start' parameter
		if (@$_GET[EWR_TABLE_START_GROUP] != "") {
			$this->StartGrp = $_GET[EWR_TABLE_START_GROUP];
			$this->setStartGroup($this->StartGrp);
		} elseif (@$_GET["pageno"] != "") {
			$nPageNo = $_GET["pageno"];
			if (is_numeric($nPageNo)) {
				$this->StartGrp = ($nPageNo-1)*$this->DisplayGrps+1;
				if ($this->StartGrp <= 0) {
					$this->StartGrp = 1;
				} elseif ($this->StartGrp >= intval(($this->TotalGrps-1)/$this->DisplayGrps)*$this->DisplayGrps+1) {
					$this->StartGrp = intval(($this->TotalGrps-1)/$this->DisplayGrps)*$this->DisplayGrps+1;
				}
				$this->setStartGroup($this->StartGrp);
			} else {
				$this->StartGrp = $this->getStartGroup();
			}
		} else {
			$this->StartGrp = $this->getStartGroup();
		}

		// Check if correct start group counter
		if (!is_numeric($this->StartGrp) || $this->StartGrp == "") { // Avoid invalid start group counter
			$this->StartGrp = 1; // Reset start group counter
			$this->setStartGroup($this->StartGrp);
		} elseif (intval($this->StartGrp) > intval($this->TotalGrps)) { // Avoid starting group > total groups
			$this->StartGrp = intval(($this->TotalGrps-1)/$this->DisplayGrps) * $this->DisplayGrps + 1; // Point to last page first group
			$this->setStartGroup($this->StartGrp);
		} elseif (($this->StartGrp-1) % $this->DisplayGrps <> 0) {
			$this->StartGrp = intval(($this->StartGrp-1)/$this->DisplayGrps) * $this->DisplayGrps + 1; // Point to page boundary
			$this->setStartGroup($this->StartGrp);
		}

	}

	// Load group db values if necessary
	function LoadGroupDbValues() {
		global $conn;

	<!--##
		for (var i = 0; i < nGrps; i++) {
			sGroupByType = arGrpFlds[i]['GroupByType'];
			if (ew_IsDbGrpFld(sGroupByType)) {
				sFldName = arGrpFlds[i]['FldName'];
				sFldObj = "this->" + arGrpFlds[i]['FldVar'].substr(2);
				GetFldObj(sFldName);
	##-->
		// Set up <!--##=sFldName##--> GroupDbValues
		$sSql = ewr_BuildReportSql($<!--##=sFldObj##-->->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $<!--##=sFldObj##-->->SqlOrderBy, "", "");
		$rswrk = $conn->Execute($sSql);
		while ($rswrk && !$rswrk->EOF) {
			$<!--##=sFldObj##-->->setDbValue($rswrk->fields[0]);
			if (!is_null($<!--##=sFldObj##-->->CurrentValue) && $<!--##=sFldObj##-->->CurrentValue <> "") {
				$grpval = $rswrk->fields('ew_report_groupvalue');
				$<!--##=sFldObj##-->->GroupDbValues[$<!--##=sFldObj##-->->CurrentValue] = $grpval;
			}
			$rswrk->MoveNext();
		}
		if ($rswrk)
			$rswrk->Close();
	<!--##
			}
		}
	##-->

	}

	// Process Ajax popup
	function ProcessAjaxPopup() {
		global $conn, $ReportLanguage;
		$fld = NULL;

		if (@$_GET["popup"] <> "") {

			$popupname = $_GET["popup"];

			// Check popup name
	<!--##
		for (var i = 0; i < nGrps; i++) {
			bGenFilter = arGrpFlds[i]['PopupFilter'];
			sGroupByType = arGrpFlds[i]['GroupByType'];
			if (bGenFilter) {
				sFldName = arGrpFlds[i]['FldName'];
				sFldObj = "this->" + arGrpFlds[i]['FldVar'].substr(2);
				sFilterName = arGrpFlds[i]['FilterName'];
				GetFldObj(sFldName);
				sFldSelectFilter = goFld.FldSelectFilter.trim();
	##-->
			// Build distinct values for <!--##=sFldName##-->
			if ($popupname == '<!--##=gsSessionFldVar##-->') {
	<!--##
				// Boolean or ENUM/SET field
				if (ew_GetFieldType(goFld.FldType) == 4 || goFld.FldTypeName == "ENUM" || goFld.FldTypeName == "SET") {
					sValueList = GetFieldValues(goFld);
					arval = sValueList.split(",");
					for (var j = 0; j < arval.length; j++) {
						sValue = arval[j];
						if (SYSTEMFUNCTIONS.IsBoolFld()) {
							sName = "ewr_BooleanName(" + arval[j] + ")";
						} else {
							sName = arval[j];
						}
	##-->
				ewr_SetupDistinctValues($<!--##=sFldObj##-->->ValueList, <!--##=sValue##-->, <!--##=sName##-->, FALSE);
	<!--##
					}
				} else {
					if (ew_IsNotEmpty(sFilterName)) {
	##-->
				ewr_SetupDistinctValuesFromFilter($<!--##=sFldObj##-->->ValueList, $<!--##=sFldObj##-->->AdvancedFilters); // Set up popup filter
	<!--##
					}
					sFld = "$" + sFldObj + "->GroupValue()";
					sFormatFld = SYSTEMFUNCTIONS.ScriptViewFormat(sFld);
					if (ew_IsNotEmpty(sFormatFld)) sFld = sFormatFld;
					sFld = "ewr_DisplayGroupValue($" + sFldObj + "," + sFld + ")";
					if (ew_IsNotEmpty(sGroupByType) && sGroupByType != "n") {
						sCheckDup = "TRUE";
					} else {
						sCheckDup = "FALSE";
					}
	##-->
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;
	<!--## if (sFldSelectFilter != "") { ##-->
				$lookuptblfilter = <!--##=sFldSelectFilter##-->;
				if (strval($lookuptblfilter) <> "") {
					ewr_AddFilter($sFilter, $lookuptblfilter);
				}
	<!--## } ##-->
				$sSql = ewr_BuildReportSql($<!--##=sFldObj##-->->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $<!--##=sFldObj##-->->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$<!--##=sFldObj##-->->setDbValue($rswrk->fields[0]);
					if (is_null($<!--##=sFldObj##-->->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($<!--##=sFldObj##-->->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
	<!--## if (ew_IsNotEmpty(sGroupByType) && sGroupByType != "n" && i == 0) { ##-->
						$<!--##=sFldObj##-->->setDbValue($rswrk->fields('ew_report_groupvalue'));
	<!--## } ##-->
						$<!--##=sFldObj##-->->GroupViewValue = <!--##=sFld##-->;
						ewr_SetupDistinctValues($<!--##=sFldObj##-->->ValueList, $<!--##=sFldObj##-->->GroupValue(), $<!--##=sFldObj##-->->GroupViewValue, <!--##=sCheckDup##-->);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($<!--##=sFldObj##-->->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($<!--##=sFldObj##-->->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
	<!--##
				}
	##-->
				$fld = &$<!--##=sFldObj##-->;
			}
	<!--##
			}
		}

		for (var i = 0; i < nDtls; i++) {
			bGenFilter = arDtlFlds[i]['PopupFilter'];
			if (bGenFilter) {
				sFldName = arDtlFlds[i]['FldName'];
				sFldObj = "this->" + arDtlFlds[i]['FldVar'].substr(2);
				sFilterName = arDtlFlds[i]['FilterName'];
				GetFldObj(sFldName);
				sFldSelectFilter = goFld.FldSelectFilter.trim();
	##-->
			// Build distinct values for <!--##=sFldName##-->
			if ($popupname == '<!--##=gsSessionFldVar##-->') {
	<!--##
				// Boolean or ENUM/SET field
				if (ew_GetFieldType(goFld.FldType) == 4 || goFld.FldTypeName == "ENUM" || goFld.FldTypeName == "SET") {
					sValueList = GetFieldValues(goFld);
					arval = sValueList.split(",");
					for (var j = 0; j < arval.length; j++) {
						sValue = arval[j];
						if (SYSTEMFUNCTIONS.IsBoolFld()) {
							sName = "ewr_BooleanName(" + arval[j] + ")";
						} else {
							sName = arval[j];
						}
	##-->
				ewr_SetupDistinctValues($<!--##=sFldObj##-->->ValueList, <!--##=sValue##-->, <!--##=sName##-->, FALSE);
	<!--##
					}
				} else {
					if (ew_IsNotEmpty(sFilterName)) {
	##-->
				ewr_SetupDistinctValuesFromFilter($<!--##=sFldObj##-->->ValueList, $<!--##=sFldObj##-->->AdvancedFilters); // Set up popup filter
	<!--##
					}
					sFld = "$" + sFldObj + "->CurrentValue";
					sFormatFld = SYSTEMFUNCTIONS.ScriptViewFormat(sFld);
					if (ew_IsNotEmpty(sFormatFld)) sFld = sFormatFld;
	##-->
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;
	<!--## if (sFldSelectFilter != "") { ##-->
				$lookuptblfilter = <!--##=sFldSelectFilter##-->;
				if (strval($lookuptblfilter) <> "") {
					ewr_AddFilter($sFilter, $lookuptblfilter);
				}
	<!--## } ##-->
				$sSql = ewr_BuildReportSql($<!--##=sFldObj##-->->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $<!--##=sFldObj##-->->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$<!--##=sFldObj##-->->setDbValue($rswrk->fields[0]);
					if (is_null($<!--##=sFldObj##-->->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($<!--##=sFldObj##-->->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$<!--##=sFldObj##-->->ViewValue = <!--##=sFld##-->;
						ewr_SetupDistinctValues($<!--##=sFldObj##-->->ValueList, $<!--##=sFldObj##-->->CurrentValue, $<!--##=sFldObj##-->->ViewValue, FALSE, $<!--##=sFldObj##-->->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($<!--##=sFldObj##-->->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($<!--##=sFldObj##-->->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
	<!--##
				}
	##-->
				$fld = &$<!--##=sFldObj##-->;
			}
	<!--##
			}
		}
	##-->

			// Output data as Json
			if (!is_null($fld)) {
				$jsdb = ewr_GetJsDb($fld, $fld->FldType);
				ob_end_clean();
				echo $jsdb;
				exit();
			}

		}

	}

	// Set up popup
	function SetupPopup() {

		global $conn, $ReportLanguage;

		if ($this->DrillDown)
			return;

		// Process post back form
		if (ewr_IsHttpPost()) {

			$sName = @$_POST["popup"]; // Get popup form name
			if ($sName <> "") {

				$cntValues = (is_array(@$_POST["<!--##=pfxSel##-->$sName"])) ? count($_POST["<!--##=pfxSel##-->$sName"]) : 0;
				if ($cntValues > 0) {
					$arValues = ewr_StripSlashes($_POST["<!--##=pfxSel##-->$sName"]);

					if (trim($arValues[0]) == "") // Select all
						$arValues = EWR_INIT_VALUE;

	<!--## if (bReportExtFilter) { ##-->
					$this->PopupName = $sName;
					if (ewr_IsAdvancedFilterValue($arValues) || $arValues == EWR_INIT_VALUE)
						$this->PopupValue = $arValues;

					if (!ewr_MatchedArray($arValues, $_SESSION["sel_$sName"])) {
						if ($this->HasSessionFilterValues($sName))
							$this->ClearExtFilter = $sName; // Clear extended filter for this field
					}
	<!--## } ##-->

					$_SESSION["<!--##=pfxSel##-->$sName"] = $arValues;
					$_SESSION["<!--##=pfxRangeFrom##-->$sName"] = ewr_StripSlashes(@$_POST["<!--##=pfxRangeFrom##-->$sName"]);
					$_SESSION["<!--##=pfxRangeTo##-->$sName"] = ewr_StripSlashes(@$_POST["<!--##=pfxRangeTo##-->$sName"]);
					$this->ResetPager();

				}
			}

		// Get 'reset' command
		} elseif (@$_GET["cmd"] <> "") {

			$sCmd = $_GET["cmd"];
			if (strtolower($sCmd) == "reset") {
	<!--##
		for (var i = 0; i < nGrps; i++) {
			bGenFilter = arGrpFlds[i]['PopupFilter'];
			if (bGenFilter) {
				sFldVar = arGrpFlds[i]['FldVar'];
				sFldParm = sFldVar.substr(2);
	##-->
				$this->ClearSessionSelection('<!--##=sFldParm##-->');
	<!--##
			}
		}

		for (var i = 0; i < nDtls; i++) {
			bGenFilter = arDtlFlds[i]['PopupFilter'];
			if (bGenFilter) {
				sFldVar = arDtlFlds[i]['FldVar'];
				sFldParm = sFldVar.substr(2);
	##-->
				$this->ClearSessionSelection('<!--##=sFldParm##-->');
	<!--##
			}
		}
	##-->
				$this->ResetPager();
			}

		}

		// Load selection criteria to array

	<!--##
		for (var i = 0; i < nGrps; i++) {
			bGenFilter = arGrpFlds[i]['PopupFilter'];
			if (bGenFilter) {
				sFldName = arGrpFlds[i]['FldName'];
				sFldVar = arGrpFlds[i]['FldVar'];
				sFldParm = sFldVar.substr(2);
				sFldObj = "this->" + arGrpFlds[i]['FldVar'].substr(2);
				sSessionFldVar = arGrpFlds[i]['SessionFldVar'];
	##-->
		// Get <!--##=sFldName##--> selected values
		if (is_array(@$_SESSION["<!--##=pfxSel##--><!--##=sSessionFldVar##-->"])) {
			$this->LoadSelectionFromSession('<!--##=sFldParm##-->');
		} elseif (@$_SESSION["<!--##=pfxSel##--><!--##=sSessionFldVar##-->"] == EWR_INIT_VALUE) { // Select all
			$<!--##=sFldObj##-->->SelectionList = "";
		}
	<!--##
			}
		}

		for (var i = 0; i < nDtls; i++) {
			bGenFilter = arDtlFlds[i]['PopupFilter'];
			if (bGenFilter) {
				sFldName = arDtlFlds[i]['FldName'];
				sFldVar = arDtlFlds[i]['FldVar'];
				sFldParm = sFldVar.substr(2);
				sFldObj = "this->" + arDtlFlds[i]['FldVar'].substr(2);
				sSessionFldVar = arDtlFlds[i]['SessionFldVar'];
	##-->
		// Get <!--##=sFldName##--> selected values
		if (is_array(@$_SESSION["<!--##=pfxSel##--><!--##=sSessionFldVar##-->"])) {
			$this->LoadSelectionFromSession('<!--##=sFldParm##-->');
		} elseif (@$_SESSION["<!--##=pfxSel##--><!--##=sSessionFldVar##-->"] == EWR_INIT_VALUE) { // Select all
			$<!--##=sFldObj##-->->SelectionList = "";
		}
	<!--##
			}
		}
	##-->

	}

	// Reset pager
	function ResetPager() {
		// Reset start position (reset command)
		$this->StartGrp = 1;
		$this->setStartGroup($this->StartGrp);
	}

	<!--## if (ew_IsNotEmpty(sGrpPerPageList)) { ##-->
	<!--##include rpt-pager.php/setupdisplaygrps##-->
	<!--## } ##-->

	// Render row
	function RenderRow() {
		global $conn, $rs, $Security, $ReportLanguage;

		if ($this->RowTotalType == EWR_ROWTOTAL_GRAND && !$this->GrandSummarySetup) { // Grand total

			$bGotCount = FALSE;
			$bGotSummary = FALSE;

			// Get total count from sql directly
			$sSql = ewr_BuildReportSql($this->getSqlSelectCount(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $this->Filter, "");
			$rstot = $conn->Execute($sSql);
			if ($rstot) {
				$this->TotCount = ($rstot->RecordCount()>1) ? $rstot->RecordCount() : $rstot->fields[0];
				$rstot->Close();
				$bGotCount = TRUE;
			} else {
				$this->TotCount = 0;
			}

	<!--## if (bHasSummaryFields) { ##-->

			// Get total from sql directly
			$sSql = ewr_BuildReportSql($this->getSqlSelectAgg(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $this->Filter, "");
			$sSql = $this->getSqlAggPfx() . $sSql . $this->getSqlAggSfx();
			$rsagg = $conn->Execute($sSql);
			if ($rsagg) {
	<!--##
		for (var k = 1; k <= nDtls; k++) {
	##-->
				$this->GrandCnt[<!--##=k##-->] = $this->TotCount;
	<!--##
			for (var j = 0; j < arSmry[k+1].length; j++) {
				bGenSmry = true;
				if (arSmry[k+1][j]) {
					sFldVar = arDtlFlds[k-1]['FldVar'];
					sFldParm = sFldVar.substr(2).toLowerCase();
					sSumFld = "sum_" + sFldParm;
					sMinFld = "min_" + sFldParm;
					sMaxFld = "max_" + sFldParm;
					sCntFld = "cnt_" + sFldParm;
					if (j == 0 || j == 1) { // SUM / AVG
						if (bGenSmry) {
	##-->
				$this->GrandSmry[<!--##=k##-->] = $rsagg->fields("<!--##=sSumFld##-->");
	<!--##
							bGenSmry = false; // No need to gen further
						}
					} else if (j == 2) { // MIN
	##-->
				$this->GrandMn[<!--##=k##-->] = $rsagg->fields("<!--##=sMinFld##-->");
	<!--##
					} else if (j == 3) { // MAX
	##-->
				$this->GrandMx[<!--##=k##-->] = $rsagg->fields("<!--##=sMaxFld##-->");
	<!--##
					} else if (j == 4) { // CNT
	##-->
				$this->GrandCnt[<!--##=k##-->] = $rsagg->fields("<!--##=sCntFld##-->");
	<!--##
					}
				}
			}
		}
	##-->
				$rsagg->Close();
				$bGotSummary = TRUE;

			}

	<!--## } else { ##-->

		$bGotSummary = TRUE;

	<!--## } ##-->

			// Accumulate grand summary from detail records
			if (!$bGotCount || !$bGotSummary) {

				$sSql = ewr_BuildReportSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $this->Filter, "");
				$rs = $conn->Execute($sSql);
				if ($rs) {
					$this->GetRow(1);
					while (!$rs->EOF) {
						$this->AccumulateGrandSummary();
						$this->GetRow(2);
					}
					$rs->Close();
				}

			}

			$this->GrandSummarySetup = TRUE; // No need to set up again

		}

	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Table","Row_Rendering")) { ##-->	
		// Call Row_Rendering event
		$this->Row_Rendering();
	<!--## } ##-->

		//
		// Render view codes
		//

		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row

	<!--##
		for (var i = 0; i < nGrps; i++) {
			sFldName = arGrpFlds[i]['FldName'];
			sFldObj = "this->" + arGrpFlds[i]['FldVar'].substr(2);
			if (GetFldObj(sFldName)) {
	##-->
			// <!--##=sFldName##-->
			<!--##~SYSTEMFUNCTIONS.ScriptGroupSummaryView()##-->
			$<!--##=sFldObj##-->->GroupViewValue = ewr_DisplayGroupValue($<!--##=sFldObj##-->, $<!--##=sFldObj##-->->GroupViewValue);
			$<!--##=sFldObj##-->->GroupSummaryOldValue = $<!--##=sFldObj##-->->GroupSummaryValue;
			$<!--##=sFldObj##-->->GroupSummaryValue = $<!--##=sFldObj##-->->GroupViewValue;
	<!--## if (bShowSummaryView && i != 0) { ##-->
			$<!--##=sFldObj##-->->GroupSummaryViewValue = $<!--##=sFldObj##-->->GroupSummaryValue;
	<!--## } else { ##-->
			$<!--##=sFldObj##-->->GroupSummaryViewValue = ($<!--##=sFldObj##-->->GroupSummaryOldValue <> $<!--##=sFldObj##-->->GroupSummaryValue) ? $<!--##=sFldObj##-->->GroupSummaryValue : "&nbsp;";
	<!--## } ##-->

	<!--##
			}
		}

		for (var i = 0; i < nDtls; i++) {
			sFldName = arDtlFlds[i]['FldName'];
			if (GetFldObj(sFldName)) {
				sFldObj = "this->" + gsFldParm;
				for (var j = 0; j < arSmry[i+2].length; j++) {
					if (arSmry[i+2][j]) {
	##-->
			// <!--##=sFldName##-->
			<!--##~SYSTEMFUNCTIONS.ScriptSummaryView(arSmry[0][j])##-->
	<!--##
						if (bShowSummaryView) {
	##-->
			$<!--##=sFldObj##-->->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : (($this->RowGroupLevel < <!--##=nGrps##-->) ? "ewRptGrpSummary" . $this->RowGroupLevel : (($this->LastGrpCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow"));
	<!--##
						} else {
	##-->
			$<!--##=sFldObj##-->->CellAttrs["class"] =  ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;
	<!--##
						}
					}
				}
	##-->

	<!--##
			}
		}
	##-->

	<!--##
		for (var i = 0; i < nGrps; i++) {
			sFldName = arGrpFlds[i]['FldName'];
			if (GetFldObj(sFldName)) {
	##-->
			// <!--##=sFldName##-->
			<!--##~SYSTEMFUNCTIONS.ScriptSummaryViewRefer()##-->
	<!--##
			}
		}

		for (var i = 0; i < nDtls; i++) {
			sFldName = arDtlFlds[i]['FldName'];
			if (GetFldObj(sFldName)) {
	##-->
			// <!--##=sFldName##-->
			<!--##~SYSTEMFUNCTIONS.ScriptSummaryViewRefer()##-->
	<!--##
			}
		}
	##-->

		} else {

	<!--##
		for (var i = 0; i < nGrps; i++) {
			sFldName = arGrpFlds[i]['FldName'];
			sFldObj = "this->" + arGrpFlds[i]['FldVar'].substr(2);
			if (GetFldObj(sFldName)) {
	##-->
			// <!--##=sFldName##-->
			<!--##~SYSTEMFUNCTIONS.ScriptGroupView()##-->
			$<!--##=sFldObj##-->->GroupViewValue = ewr_DisplayGroupValue($<!--##=sFldObj##-->, $<!--##=sFldObj##-->->GroupViewValue);
			if ($<!--##=sFldObj##-->->GroupValue() == $<!--##=sFldObj##-->->GroupOldValue() && !$this->ChkLvlBreak(<!--##=i+1##-->))
				$<!--##=sFldObj##-->->GroupViewValue = "&nbsp;";
	<!--##
			}
		}

		for (var i = 0; i < nDtls; i++) {
			sFldName = arDtlFlds[i]['FldName'];
			if (GetFldObj(sFldName)) {
	##-->
			// <!--##=sFldName##-->
			<!--##~SYSTEMFUNCTIONS.ScriptView()##-->
	<!--##
			}
		}
	##-->

	<!--##
		for (var i = 0; i < nGrps; i++) {
			sFldName = arGrpFlds[i]['FldName'];
			if (GetFldObj(sFldName)) {
	##-->
			// <!--##=sFldName##-->
			<!--##~SYSTEMFUNCTIONS.ScriptViewRefer()##-->
	<!--##
			}
		}

		for (var i = 0; i < nDtls; i++) {
			sFldName = arDtlFlds[i]['FldName'];
			if (GetFldObj(sFldName)) {
	##-->
			// <!--##=sFldName##-->
			<!--##~SYSTEMFUNCTIONS.ScriptViewRefer()##-->
	<!--##
			}
		}
	##-->

		}

	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Table","Cell_Rendered")) { ##-->

		// Call Cell_Rendered event
		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row
	<!--##
		for (var i = 0; i < nGrps; i++) {
			sFldName = arGrpFlds[i]['FldName'];
			sFldObj = "this->" + arGrpFlds[i]['FldVar'].substr(2);
	##-->
			// <!--##=sFldName##-->
			$CurrentValue = $<!--##=sFldObj##-->->GroupViewValue;
			$ViewValue = &$<!--##=sFldObj##-->->GroupViewValue;
			$ViewAttrs = &$<!--##=sFldObj##-->->ViewAttrs;
			$CellAttrs = &$<!--##=sFldObj##-->->CellAttrs;
			$HrefValue = &$<!--##=sFldObj##-->->HrefValue;
			$LinkAttrs = &$<!--##=sFldObj##-->->LinkAttrs;
			$this->Cell_Rendered($<!--##=sFldObj##-->, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
	<!--##
		}

		for (var i = 0; i < nDtls; i++) {
			sFldName = arDtlFlds[i]['FldName'];
			sFldObj = "this->" + arDtlFlds[i]['FldVar'].substr(2);
			for (var j = 0; j < arSmry[i+2].length; j++) {
				if (arSmry[i+2][j]) {
	##-->
			// <!--##=sFldName##-->
			$CurrentValue = $<!--##=sFldObj##-->-><!--##=ew_SummaryValueName(arSmry[0][j])##-->;
			$ViewValue = &$<!--##=sFldObj##-->-><!--##=ew_SummaryViewValueName(arSmry[0][j])##-->;
			$ViewAttrs = &$<!--##=sFldObj##-->->ViewAttrs;
			$CellAttrs = &$<!--##=sFldObj##-->->CellAttrs;
			$HrefValue = &$<!--##=sFldObj##-->->HrefValue;
			$LinkAttrs = &$<!--##=sFldObj##-->->LinkAttrs;
			$this->Cell_Rendered($<!--##=sFldObj##-->, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
	<!--##
				}
			}
		}
	##-->

		} else {

	<!--##
		for (var i = 0; i < nGrps; i++) {
			sFldName = arGrpFlds[i]['FldName'];
			sFldObj = "this->" + arGrpFlds[i]['FldVar'].substr(2);
	##-->
			// <!--##=sFldName##-->
			$CurrentValue = $<!--##=sFldObj##-->->GroupValue();
			$ViewValue = &$<!--##=sFldObj##-->->GroupViewValue;
			$ViewAttrs = &$<!--##=sFldObj##-->->ViewAttrs;
			$CellAttrs = &$<!--##=sFldObj##-->->CellAttrs;
			$HrefValue = &$<!--##=sFldObj##-->->HrefValue;
			$LinkAttrs = &$<!--##=sFldObj##-->->LinkAttrs;
			$this->Cell_Rendered($<!--##=sFldObj##-->, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
	<!--##
		}

		for (var i = 0; i < nDtls; i++) {
			sFldName = arDtlFlds[i]['FldName'];
			sFldObj = "this->" + arDtlFlds[i]['FldVar'].substr(2);
	##-->
			// <!--##=sFldName##-->
			$CurrentValue = $<!--##=sFldObj##-->->CurrentValue;
			$ViewValue = &$<!--##=sFldObj##-->->ViewValue;
			$ViewAttrs = &$<!--##=sFldObj##-->->ViewAttrs;
			$CellAttrs = &$<!--##=sFldObj##-->->CellAttrs;
			$HrefValue = &$<!--##=sFldObj##-->->HrefValue;
			$LinkAttrs = &$<!--##=sFldObj##-->->LinkAttrs;
			$this->Cell_Rendered($<!--##=sFldObj##-->, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
	<!--##
		}
	##-->
		}

	<!--## } ##-->

	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Table","Row_Rendered")) { ##-->
		// Call Row_Rendered event
		$this->Row_Rendered();
	<!--## } ##-->

		$this->SetupFieldCount();
	}

	// Setup field count
	function SetupFieldCount() {

		$this->GrpFldCount = 0;
		$this->SubGrpFldCount = 0;
		$this->DtlFldCount = 0;
	<!--##
		for (var i = 0; i < nGrps; i++) {
			sFldName = arGrpFlds[i]['FldName'];
			sFldObj = "this->" + arGrpFlds[i]['FldVar'].substr(2);
			if (i == 0) {
	##-->
		if ($<!--##=sFldObj##-->->Visible) $this->GrpFldCount += 1;
	<!--##
			} else {
	##-->
		if ($<!--##=sFldObj##-->->Visible) { $this->GrpFldCount += 1; $this->SubGrpFldCount += 1; }
	<!--##
			}
		}

		for (var i = 0; i < nDtls; i++) {
			sFldName = arDtlFlds[i]['FldName'];
			sFldObj = "this->" + arDtlFlds[i]['FldVar'].substr(2);
	##-->
		if ($<!--##=sFldObj##-->->Visible) $this->DtlFldCount += 1;
	<!--##
		}
	##-->

	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $ReportBreadcrumb;
		$ReportBreadcrumb = new crBreadcrumb();
		$url = substr(ewr_CurrentUrl(), strrpos(ewr_CurrentUrl(), "/")+1);
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$ReportBreadcrumb->Add("<!--##=CTRL.CtrlID##-->", $this->TableVar, $url, "", $this->TableVar, TRUE);
	}
<!--##/session##-->
?>