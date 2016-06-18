<!--##session phpconfig##-->
<!--##
	var nSearchFlds = 0; // Number of search fields

	// Row fields variables
	var sRowFldNames = SYSTEMFUNCTIONS.RowFieldNames(); // List of row field names
	var arRows = sRowFldNames.split("\r\n");
	var nRows = arRows.length; // Number of row fields
	gnGrps = 0; // NOT used

	// Row field array
	var arGrpFlds = [];
	var arFirstGrpFld = [];
	for (var i = 0; i < nRows; i++) {
		if (GetFldObj(arRows[i])) {
			var grpfld = [];
			grpfld['FldName'] = gsFldName; // Field name
			grpfld['FldVar'] = gsFldVar; // Field variable
			grpfld['FldObj'] = gsFldObj; // Field object
			grpfld['SessionFldVar'] = gsSessionFldVar; // Session field var
			grpfld['PopupFilter'] = (goFld.FldType == 201 || goFld.FldType == 203) ? false : IsPopupFilter(goFld); // Popup filter, skip if memo
			grpfld['FilterName'] = goFld.FldFilterName; // Filter name
			grpfld['UseRange'] = (goFld.FldUseRange) ? "true" : "false"; // Field use range
			grpfld['ShowSummary'] = goFld.FldGroupByShowSummary; // Show summary required
			if (IsPopupFilter(goFld)) nSearchFlds += 1;

			arGrpFlds[arGrpFlds.length] = grpfld;

			// Save first group
			if (i == 0)
				arFirstGrpFld = grpfld;

		}
	}; // End for i
	var bShowSummaryView = false;

	// Column field variables
	var sColFldName = SYSTEMFUNCTIONS.ColumnFieldNames(); // Column field name
	if (!GetFldObj(sColFldName)) {
		throw new Error("Invalid column field: " + sColFldName);
	}
	var COLFIELD = goFld;
	var sColFldVar = gsFldVar;
	var sColFldParm = gsFldParm;
	var sColFldObj = gsFldObj;
	var sColFldObjFldType = "$" + sColFldObj + "->FldType";
	var bColSearch = IsPopupFilter(COLFIELD);
	var bColExtSearch = IsExtendedFilter(COLFIELD);
	if (bColSearch) nSearchFlds += 1;
	var sColUseRange = (COLFIELD.FldUseRange) ? "true" : "false"; // Field use range
	var sColFldQc = COLFIELD.FldQuoteS;
	var sColSessionFldVar = gsSessionFldVar;
	var sColPopupFldVar = gsPopupFldVar;
	var sColFldDateType = "";
	var bColFldDateSelect = false;
	if (ew_GetFieldType(COLFIELD.FldType) == 2) {
		sColFldDateType = COLFIELD.FldColumnDateType;
		bColFldDateSelect = COLFIELD.FldColumnDateSelect && (sColFldDateType == "q" || sColFldDateType == "m");
	}
	var bColFldBoolean = (ew_GetFieldType(COLFIELD.FldType) == 4);
	var sColFld = gsFld;
	var sColFldType = ((sColFldDateType == "q" || sColFldDateType == "m") && !bColFldDateSelect) ? 3 : COLFIELD.FldType;
	if (sColFldDateType == "q" || sColFldDateType == "m") {
		sColFldObjFldType = 3;
	}
	var sColFldCellStyle = FieldCellStyle(COLFIELD);
	var sColFldViewStyle = FieldViewStyle(COLFIELD);
	var sColDateFldCellStyle = sColFldCellStyle;
	var sColDateFldViewStyle = sColFldViewStyle;
	if (ew_IsNotEmpty(sColFldCellStyle)) sColFldCellStyle += " ";
	sColFldCellStyle += "vertical-align: top;";

	var BaseRs = nRows;
	var BaseRsAgg = 0;
	var nGrps = nRows;

	// Set up Column Date Field
	var sColDateFld = "";
	var sColDateFldName = sColFldName;
	var sColDateFldParm = sColFldParm;
	var sColDateFldCaption = "";
	if (sColFldDateType == "q" || sColFldDateType == "m") {
		sColDateFld = ew_DbGrpSql("y",0).replace(/%s/g, sColFld);
		sColDateFldName = "YEAR__" + sColFldParm;
		BaseRs += 1;
		if (bColFldDateSelect) BaseRsAgg += 1;
		sColDateFldVar = "x_" + sColDateFldName;
		sColDateFldParm = sColDateFldName;
		//sColDateFldObj = gsTblVar + "->" + sColDateFldParm;
		sColDateFldObj = gsPageObj + "->" + sColDateFldParm;
		sColDateSessionFldVar = gsTblVar + "_" + sColDateFldName;
		sColDateFldType = 3; // Integer type
		sColDateFldCaption = "Year";
		if (!bColFldDateSelect) {
			var grpfld = [];
			grpfld['FldName'] = sColDateFldName;
			grpfld['FldVar'] = sColDateFldVar;
			grpfld['FldObj'] = sColDateFldObj;
			grpfld['SessionFldVar'] = sColDateSessionFldVar;
			grpfld['PopupFilter'] = bColSearch; // Popup
			grpfld['FilterName'] = ""; // No advanced filter
			grpfld['UseRange'] = "false";
			grpfld['ShowSummary'] = false; // No summary
			nGrps = nRows + 1;
			arGrpFlds[arGrpFlds.length] = grpfld;
		} else {
			nGrps = nRows;
		}
	};

	// Disable show summary for last group
	arGrpFlds[nGrps-1]['ShowSummary'] = false;

	// Summary Field variables
	var sSmryFldName = SYSTEMFUNCTIONS.SummaryFieldNames(); // Summary field name
	if (!GetFldObj(sSmryFldName)) {
		throw new Error("Invalid summary field: " + sSmryFldName);
	}
	var SMRYFIELD = goFld;
	var sSmryType = SMRYFIELD.FldSummaryType;
	var sSmryTypeCaption = "";
	switch (sSmryType) {
		case "AVG": sSmryTypeCaption = "<?php echo $ReportLanguage->Phrase(\"RptAvg\") ?>"; break;
		case "COUNT": sSmryTypeCaption = "<?php echo $ReportLanguage->Phrase(\"RptCnt\") ?>"; break;
		case "MAX": sSmryTypeCaption = "<?php echo $ReportLanguage->Phrase(\"RptMax\") ?>"; break;
		case "MIN": sSmryTypeCaption = "<?php echo $ReportLanguage->Phrase(\"RptMin\") ?>"; break;
		case "SUM": sSmryTypeCaption = "<?php echo $ReportLanguage->Phrase(\"RptSum\") ?>"; break;
	};
	var sSmryInitValue, sRptSmryType, sRptPageSmryType, sRptGrandSmryType;
	if (sSmryType == "SUM" || sSmryType == "COUNT") {
		sSmryInitValue = "0";
		sRptSmryType = "<?php echo $ReportLanguage->Phrase(\"Total\") ?>";
		sRptPageSmryType = "<?php echo $ReportLanguage->Phrase(\"RptPageTotal\") ?>";
		sRptGrandSmryType = "<?php echo $ReportLanguage->Phrase(\"RptGrandTotal\") ?>";
	} else if (sSmryType == "MIN" || sSmryType == "MAX") {
		sSmryInitValue = "NULL";
		sRptSmryType = "<?php echo $ReportLanguage->Phrase(\"Summary\") ?>";
		sRptPageSmryType = "<?php echo $ReportLanguage->Phrase(\"RptPageSummary\") ?>";
		sRptGrandSmryType = "<?php echo $ReportLanguage->Phrase(\"RptGrandSummary\") ?>";
	} else if (sSmryType == "AVG") {
		sSmryInitValue = "0";
		sRptSmryType = "<?php echo $ReportLanguage->Phrase(\"Summary\") ?>";
		sRptPageSmryType = "<?php echo $ReportLanguage->Phrase(\"RptPageSummary\") ?>";
		sRptGrandSmryType = "<?php echo $ReportLanguage->Phrase(\"RptGrandSummary\") ?>";
	};
	var sSmryFld = gsFld;
	var sSmryFldVar = gsFldVar;
	var sSmryFldParm = gsFldParm;
	var sSmryFldObj = gsFldObj;

	bShowYearSelection = (bColFldDateSelect && ew_IsNotEmpty(sColDateFldName) && (sColFldDateType == "q" || sColFldDateType == "m"));
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
	var $DisplayGrps = <!--##=iGrpPerPage##-->; // Groups per page
	var $GrpRange = 10;

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

	var $Cnt, $Val, $Smry;
<!--## if (sSmryType == "AVG") { ##-->
	var $ValCnt, $SmryCnt;
<!--## } ##-->

	var $ColSpan;

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

	<!--## if (ew_IsNotEmpty(sGrpPerPageList)) { ##-->
		// Set up groups per page dynamically
		$this->SetUpDisplayGrps();
	<!--## } ##-->
	
		// Set up Breadcrumb
		if ($this->Export == "")
			$this->SetupBreadcrumb();

		// Get sort
		$this->Sort = $this->GetSort();

	<!--## if (bChartDynamicSort) { ##-->
		// Get chart sort
		$this->GetChartSort();
	<!--## } ##-->

		// Popup values and selections
	<!--##
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
		};

		if (bColSearch || bColExtSearch) {
	##-->
		$this-><!--##=sColFldParm##-->->SelectionList = "";
		$this-><!--##=sColFldParm##-->->DefaultSelectionList = "";
		$this-><!--##=sColFldParm##-->->ValueList = "";
	<!--##
		}

		if (bColFldDateSelect  && ew_IsNotEmpty(sColDateFldName)) {
	##-->
		$this-><!--##=sColDateFldParm##-->->SelectionList = "";
		$this-><!--##=sColDateFldParm##-->->DefaultSelectionList = "";
		$this-><!--##=sColDateFldParm##-->->ValueList = "";
	<!--##
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

		// Handle Ajax popup
		$this->ProcessAjaxPopup();

	<!--## if (bReportExtFilter || bShowYearSelection || nSearchFlds > 0) { ##-->
		// Restore filter list
		$this->RestoreFilterList();
	<!--## } ##-->

		// Extended filter
		$sExtendedFilter = "";

	<!--## if (bColFldDateSelect && ew_IsNotEmpty(sColDateFldName)) { ##-->
		// Add year filter
		if ($this-><!--##=sColDateFldParm##-->->SelectionList <> "") {
			if ($this->Filter <> "") $this->Filter .= " AND ";
			$this->Filter .= "<!--##=ew_Quote(sColDateFld)##--> = " . $this-><!--##=sColDateFldParm##-->->SelectionList;
		}
	<!--## } ##-->

	<!--## if (bReportExtFilter) { ##-->

		// Build extended filter
		$sExtendedFilter = $this->GetExtendedFilter();
		ewr_AddFilter($this->Filter, $sExtendedFilter);

	<!--## } ##-->

		// Load columns to array
		$this->GetColumns();

		// Build popup filter
		$sPopupFilter = $this->GetPopupFilter();
		//ewr_SetDebugMsg("popup filter: " . $sPopupFilter);
		ewr_AddFilter($this->Filter, $sPopupFilter);

	<!--## if (bReportExtFilter || bShowYearSelection || nSearchFlds > 0) { ##-->
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

		// Get total group count
		$sGrpSort = ewr_UpdateSortFields($this->getSqlOrderByGroup(), $this->Sort, 2); // Get grouping field only
		$sSql = ewr_BuildReportSql($this->getSqlSelectGroup(), $this->getSqlWhere(), $this->getSqlGroupBy(), "", $this->getSqlOrderByGroup(), $this->Filter, $sGrpSort);
		$this->TotalGrps = $this->GetGrpCnt($sSql);

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

		// Get total groups
		$rsgrp = $this->GetGrpRs($sSql, $this->StartGrp, $this->DisplayGrps);

		// Init detail recordset
		$rs = NULL;

		// Set up column attributes
		$this-><!--##=sColFldParm##-->->ViewAttrs["style"] = "<!--##=ew_Quote(sColFldViewStyle)##-->";
		$this-><!--##=sColFldParm##-->->CellAttrs["style"] = "<!--##=ew_Quote(sColFldCellStyle)##-->";

		$this->SetupFieldCount();

	}

<!--##/session##-->
?>

<!--##session report_content##-->
<!--##=sExpStart##-->
<!--##
	if (bColSearch) {
##-->
<script type="text/javascript">
<?php $jsdb = ewr_GetJsDb($<!--##=sColFldObj##-->, <!--##=sColFldObjFldType##-->); ?>
ewr_CreatePopup("<!--##=sColSessionFldVar##-->", <?php echo $jsdb ?>); // Popup filters
</script>
<!--##
	}
##-->
<!--##=sExpEnd##-->

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
	}; // End for i, Chart on top
##-->

<!--##=sHtmlExpStart##-->
</div>
<!-- Top container (end) -->

	<!-- left container (begin) -->
	<div id="ewLeft" class="ewLeft">
<!--##=sHtmlExpEnd##-->

	<!-- left slot -->
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
	}; // End for i, chart on left
##-->

<!--##=sHtmlExpStart##-->
	</div>
	<!-- left container (end) -->

	<!-- center container (report) (begin) -->
	<div id="ewCenter" class="ewCenter">
<!--##=sHtmlExpEnd##-->

	<!-- center slot -->

<!--##include rpt-extfilter.php/report_drilldownlist##-->

<!-- crosstab report starts -->

<!--##=sSkipPdfExpStart##-->
<div id="report_crosstab">
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
if (intval($<!--##=gsPageObj##-->->StopGrp) > intval($<!--##=gsPageObj##-->->TotalGrps)) {
	$<!--##=gsPageObj##-->->StopGrp = $<!--##=gsPageObj##-->->TotalGrps;
}

// Navigate
$<!--##=gsPageObj##-->->RecCount = 0;
$<!--##=gsPageObj##-->->RecIndex = 0;

// Get first row
if ($<!--##=gsPageObj##-->->TotalGrps > 0) {
	$<!--##=gsPageObj##-->->GetGrpRow(1);
	$<!--##=gsPageObj##-->->GrpCount = 1;
}

while ($rsgrp && !$rsgrp->EOF && $<!--##=gsPageObj##-->->GrpCount <= $<!--##=gsPageObj##-->->DisplayGrps || $<!--##=gsPageObj##-->->ShowHeader) {

	// Show header
	if ($<!--##=gsPageObj##-->->ShowHeader) {
?>

<?php if ($<!--##=gsPageObj##-->->GrpCount > 1) { ?>
</tbody>
<!--##include rpt-phpcommon-table.php/report-footer##-->
<?php echo $<!--##=gsPageObj##-->->PageBreakContent ?>
<?php } ?>

<!--##include rpt-phpcommon-table.php/report-header##-->

<thead>
	<!-- Table header -->
	<tr class="ewTableHeader">
<?php if ($<!--##=gsPageObj##-->->GrpFldCount > 0) { ?>
		<td class="ewRptColSummary" colspan="<?php echo $<!--##=gsPageObj##-->->GrpFldCount ?>"><div><?php echo $<!--##=sSmryFldObj##-->->FldCaption() ?>&nbsp;(<!--##=sSmryTypeCaption##-->)&nbsp;</div></td>
<?php } ?>
		<td class="ewRptColHeader" colspan="<?php echo @$<!--##=gsPageObj##-->->ColSpan ?>">
			<div class="ewTableHeaderBtn">
				<span class="ewTableHeaderCaption"><?php echo $<!--##=sColFldObj##-->->FldCaption() ?></span>
<!--## if (bColSearch) { ##-->
<!--##=sExpStart##-->
				<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, '<!--##=sColSessionFldVar##-->', <!--##=sColUseRange##-->, '<?php echo $<!--##=sColFldObj##-->->RangeFrom ?>', '<?php echo $<!--##=sColFldObj##-->->RangeTo ?>');" name="<!--##=sColFldVar##-->" id="<!--##=sColFldVar##-->"><span class="icon-filter"></span></a>
<!--##=sExpEnd##-->
<!--## } ##-->
			</div>
		</td>
	</tr>
	<tr class="ewTableHeader">
<!--##
	for (var i = 0; i < nGrps; i++) {
		sFldName = arGrpFlds[i]['FldName'];
		sFldVar = arGrpFlds[i]['FldVar'];
		sFldParm = sFldVar.substr(2);
		sFldObj = arGrpFlds[i]['FldObj'];
		sSessionFldVar = arGrpFlds[i]['SessionFldVar'];
		bGenFilter = arGrpFlds[i]['PopupFilter'];
		sUseRange = arGrpFlds[i]['UseRange'];
		if (sFldName == sColDateFldName) {
			sFldCaption = "$ReportLanguage->Phrase(\"Year\")";
			sTDStyle = FieldTD_Header(COLFIELD);
		} else if (GetFldObj(sFldName)) {
			sFldCaption = "$" + sFldObj + "->FldCaption()";
			sTDStyle = FieldTD_Header(goFld);
		}
		sClassId = gsTblVar + "_" + sFldParm;
		sJsSort = " class=\"ewTableHeaderBtn ewPointer " + sClassId + "\" onclick=\"ewr_Sort(event,'<?php echo $" + gsPageObj + "->SortUrl($" + sFldObj + ") ?>'," + iSortType + ");\"";
##-->
<?php if ($<!--##=sFldObj##-->->Visible) { ?>
<?php if ($<!--##=gsPageObj##-->->Export <> "" || $<!--##=gsPageObj##-->->DrillDown) { ?>
	<td data-field="<!--##=sFldParm##-->">
		<div class="<!--##=sClassId##-->"<!--##=sTDStyle##-->><span class="ewTableHeaderCaption"><?php echo <!--##=sFldCaption##--> ?></span></div>
	</td>
<?php } else { ?>
	<td data-field="<!--##=sFldParm##-->">
<?php if ($<!--##=gsPageObj##-->->SortUrl($<!--##=sFldObj##-->) == "") { ?>
		<div<!--##=sJsSort##--><!--##=sTDStyle##-->>
			<span class="ewTableHeaderCaption"><?php echo <!--##=sFldCaption##--> ?></span>			
<!--## if (bGenFilter) { ##-->
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, '<!--##=sSessionFldVar##-->', <!--##=sUseRange##-->, '<?php echo $<!--##=sFldObj##-->->RangeFrom; ?>', '<?php echo $<!--##=sFldObj##-->->RangeTo; ?>');" id="<!--##=sFldVar##-->"><span class="icon-filter"></span></a>
<!--## } ##-->
		</div>
<?php } else { ?>
		<div<!--##=sJsSort##--><!--##=sTDStyle##-->>
			<span class="ewTableHeaderCaption"><?php echo <!--##=sFldCaption##--> ?></span>
			<span class="ewTableHeaderSort"><?php if ($<!--##=sFldObj##-->->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($<!--##=sFldObj##-->->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
<!--## if (bGenFilter) { ##-->
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, '<!--##=sSessionFldVar##-->', <!--##=sUseRange##-->, '<?php echo $<!--##=sFldObj##-->->RangeFrom; ?>', '<?php echo $<!--##=sFldObj##-->->RangeTo; ?>');" id="<!--##=sFldVar##-->"><span class="icon-filter"></span></a>
<!--## } ##-->
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<!--##
	}; // End for i

	// Set current column field
	GetFldObj(sColFldName);
	sFld = "$" + gsPageObj + "->SummaryCurrentValue[$iy-1]";
	sFormatFld = SYSTEMFUNCTIONS.ScriptViewFormat(sFld);
	if (ew_IsNotEmpty(sFormatFld)) sFld = sFormatFld;
##-->

<!-- Dynamic columns begin -->
<?php
	$cntval = count($<!--##=gsPageObj##-->->Val);
	for ($iy = 1; $iy < $cntval; $iy++) {
		if ($<!--##=gsPageObj##-->->Col[$iy]->Visible) {
			$<!--##=gsPageObj##-->->SummaryCurrentValue[$iy-1] = $<!--##=gsPageObj##-->->Col[$iy]->Caption;
			$<!--##=gsPageObj##-->->SummaryViewValue[$iy-1] = <!--##=sFld##-->;
?>
		<td class="ewTableHeader"<?php echo $<!--##=sColFldObj##-->->CellAttributes() ?>><div<?php echo $<!--##=sColFldObj##-->->ViewAttributes() ?>><?php echo $<!--##=gsPageObj##-->->SummaryViewValue[$iy-1]; ?></div></td>
<?php
		}
	}
?>
<!-- Dynamic columns end -->
<!--## if (TABLE.TblRowSum) { ##-->
		<td class="ewTableHeader"<?php echo $<!--##=sColFldObj##-->->CellAttributes() ?>><div<?php echo $<!--##=sColFldObj##-->->ViewAttributes() ?>><!--##=sRptSmryType##--></div></td>
<!--## } ##-->
	</tr>
</thead>
<tbody>
<?php
		if ($<!--##=gsPageObj##-->->TotalGrps == 0) break; // Show header only
		$<!--##=gsPageObj##-->->ShowHeader = FALSE;
	}

	// Build detail SQL
	$sWhere = ewr_DetailFilterSQL($<!--##=arFirstGrpFld['FldObj']##-->, $<!--##=gsPageObj##-->->getSqlFirstGroupField(), $<!--##=arFirstGrpFld['FldObj']##-->->GroupValue());

	if ($<!--##=gsPageObj##-->->PageFirstGroupFilter <> "") $<!--##=gsPageObj##-->->PageFirstGroupFilter .= " OR ";
	$<!--##=gsPageObj##-->->PageFirstGroupFilter .= $sWhere;

	if ($<!--##=gsPageObj##-->->Filter != "")
		$sWhere = "($<!--##=gsPageObj##-->->Filter) AND ($sWhere)";
	$sSql = ewr_BuildReportSql(str_replace("<DistinctColumnFields>", $<!--##=gsPageObj##-->->DistinctColumnFields, $<!--##=gsPageObj##-->->getSqlSelect()), $<!--##=gsPageObj##-->->getSqlWhere(), $<!--##=gsPageObj##-->->getSqlGroupBy(), "", $<!--##=gsPageObj##-->->getSqlOrderBy(), $sWhere, $<!--##=gsPageObj##-->->Sort);
	$rs = $conn->Execute($sSql);
	$rsdtlcnt = ($rs) ? $rs->RecordCount() : 0;
	if ($rsdtlcnt > 0)
		$<!--##=gsPageObj##-->->GetRow(1);
	while ($rs && !$rs->EOF) {
		$<!--##=gsPageObj##-->->RecCount++;
		$<!--##=gsPageObj##-->->RecIndex++;

		// Render row
		$<!--##=gsPageObj##-->->ResetAttrs();
		$<!--##=gsPageObj##-->->RowType = EWR_ROWTYPE_DETAIL;
		$<!--##=gsPageObj##-->->RenderRow();
?>
	<tr<?php echo $<!--##=gsPageObj##-->->RowAttributes(); ?>>
<!--##
	for (var i = 0; i < nGrps; i++) {
		sFldName = arGrpFlds[i]['FldName'];
		sFldParm = arGrpFlds[i]['FldVar'].substr(2);
		sFldObj = arGrpFlds[i]['FldObj'];
		gsFldObj = sFldObj;
##-->
<?php if ($<!--##=sFldObj##-->->Visible) { ?>
<!--##
		if (sFldName == sColDateFldName) {
			var gv = "$" + sFldObj + "->GroupViewValue";
			var ctl = "<?php echo " + gv + " ?>";
			if (ew_IsFieldDrillDown(COLFIELD)) {
				ctl = SYSTEMFUNCTIONS.FieldViewHref(ctl,gv);
			}
##-->
		<!-- <!--##=sFldName##--> -->
		<td data-field="<!--##=sFldParm##-->"<?php echo $<!--##=sFldObj##-->->CellAttributes(); ?>><span<?php echo $<!--##=sFldObj##-->->ViewAttributes(); ?>><!--##=ctl##--></span></td>
<!--##
		} else if (GetFldObj(sFldName)) {
##-->
		<!-- <!--##=sFldName##--> -->
		<td data-field="<!--##=sFldParm##-->"<?php echo $<!--##=sFldObj##-->->CellAttributes(); ?>><!--##=SYSTEMFUNCTIONS.FieldGroupView()##--></td>
<!--##
		}
##-->
<?php } ?>
<!--##
	}; // End for i

	//  Set current field = summary field
	GetFldObj(sSmryFldName);
	if (ew_IsFieldDrillDown(SMRYFIELD)) {
		sSmryPrefix = "<a<?php echo $" + gsPageObj + "->SummaryLinkAttributes($iy-1) ?>>";
		sSmrySuffix = "</a>";
	} else {
		sSmryPrefix = "";
		sSmrySuffix = "";
	}
##-->

<!-- Dynamic columns begin -->
<?php
		$cntcol = count($<!--##=gsPageObj##-->->SummaryViewValue);
		for ($iy = 1; $iy <= $cntcol; $iy++) {
			$bColShow = ($iy <= $<!--##=gsPageObj##-->->ColCount) ? $<!--##=gsPageObj##-->->Col[$iy]->Visible : TRUE;
			$sColDesc = ($iy <= $<!--##=gsPageObj##-->->ColCount) ? $<!--##=gsPageObj##-->->Col[$iy]->Caption : $ReportLanguage->Phrase("Summary");
			if ($bColShow) {
?>
		<!-- <?php echo $sColDesc; ?> -->
		<td<?php echo $<!--##=gsPageObj##-->->SummaryCellAttributes($iy-1) ?>><span<?php echo $<!--##=gsPageObj##-->->SummaryViewAttributes($iy-1); ?>><!--##=sSmryPrefix##--><?php echo $<!--##=gsPageObj##-->->SummaryViewValue[$iy-1] ?><!--##=sSmrySuffix##--></span></td>
<?php
			}
		}
?>
<!-- Dynamic columns end -->
	</tr>
<?php
		// Accumulate page summary
		$<!--##=gsPageObj##-->->AccumulateSummary();

		// Get next record
		$<!--##=gsPageObj##-->->GetRow(2);

?>
<!--##
	for (var i = nGrps-1; i >= 0; i--) {
		var lvl = i + 1;
		if (lvl == 1) {
##-->
<?php
	} // End detail records loop
?>
<!--##
		}
		if (arGrpFlds[i]['ShowSummary']) { // Show summary required
			sFldName = arGrpFlds[i]['FldName'];
			sFldObj = arGrpFlds[i]['FldObj'];
			GetFldObj(sFldName);
##-->
<?php

		// Process summary level <!--##=lvl##-->
		if ($<!--##=gsPageObj##-->->ChkLvlBreak(<!--##=lvl##-->) && $<!--##=sFldObj##-->->Visible) {
			$<!--##=gsPageObj##-->->ResetAttrs();
			$<!--##=gsPageObj##-->->RowType = EWR_ROWTYPE_TOTAL;
			$<!--##=gsPageObj##-->->RowTotalType = EWR_ROWTOTAL_GROUP;
			$<!--##=gsPageObj##-->->RowTotalSubType = EWR_ROWTOTAL_FOOTER;
			$<!--##=gsPageObj##-->->RowGroupLevel = <!--##=lvl##-->;
			$<!--##=gsPageObj##-->->RenderRow();
?>
	<!-- Summary <!--##=sFldName##--> (level <!--##=lvl##-->) -->
	<tr<?php echo $<!--##=gsPageObj##-->->RowAttributes(); ?>>
<!--##
			for (var j = 0; j < i; j++) {
				sFldParm = arGrpFlds[j]['FldVar'].substr(2);
				var sGrpFldObj = arGrpFlds[j]['FldObj'];
##-->
		<td data-field="<!--##=sFldParm##-->"<?php echo $<!--##=sGrpFldObj##-->->CellAttributes() ?>>&nbsp;</td>
<!--##
			};

			if (ew_IsFieldDrillDown(goFld)) {
				sRowPrefix = "<a<?php echo $" + sFldObj + "->LinkAttributes() ?>>";
				sRowSuffix = "</a>";
			} else {
				sRowPrefix = "";
				sRowSuffix = "";
			}
##-->
		<td colspan="<?php echo ($<!--##=gsPageObj##-->->GrpFldCount - <!--##=lvl-1##-->) ?>"<?php echo $<!--##=sFldObj##-->->CellAttributes() ?>><!--##@RptSumHead##--> <?php echo $<!--##=sFldObj##-->->FldCaption() ?>: <!--##=sRowPrefix##--><?php echo $<!--##=sFldObj##-->->GroupViewValue ?><!--##=sRowSuffix##--></td>

<!--##
			//  Summary field drill down
			if (ew_IsFieldDrillDown(SMRYFIELD)) {
				sSmryPrefix = "<a<?php echo $" + gsPageObj + "->SummaryLinkAttributes($iy-1) ?>>";
				sSmrySuffix = "</a>";
			} else {
				sSmryPrefix = "";
				sSmrySuffix = "";
			}
##-->

<!-- Dynamic columns begin -->
<?php
	$cntcol = count($<!--##=gsPageObj##-->->SummaryViewValue);
	for ($iy = 1; $iy <= $cntcol; $iy++) {
		$bColShow = ($iy <= $<!--##=gsPageObj##-->->ColCount) ? $<!--##=gsPageObj##-->->Col[$iy]->Visible : TRUE;
		$sColDesc = ($iy <= $<!--##=gsPageObj##-->->ColCount) ? $<!--##=gsPageObj##-->->Col[$iy]->Caption : $ReportLanguage->Phrase("Summary");
		if ($bColShow) {
?>
		<!-- <?php echo $sColDesc; ?> -->
		<td<?php echo $<!--##=gsPageObj##-->->SummaryCellAttributes($iy-1) ?>><span<?php echo $<!--##=gsPageObj##-->->SummaryViewAttributes($iy-1); ?>><!--##=sSmryPrefix##--><?php echo $<!--##=gsPageObj##-->->SummaryViewValue[$iy-1] ?><!--##=sSmrySuffix##--></span></td>
<?php
		}
	}
?>
<!-- Dynamic columns end -->
	</tr>
<?php
			// Reset level <!--##=lvl##--> summary
			$<!--##=gsPageObj##-->->ResetLevelSummary(<!--##=lvl##-->);
		}
?>
<!--##
		} // End show summary
	}; // End for i
##-->
<?php
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

	// Handle EOF
	if (!$rsgrp || $rsgrp->EOF)
		$<!--##=gsPageObj##-->->ShowHeader = FALSE;
}
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
			$<!--##=gsPageObj##-->->RowAttrs["class"] = "ewRptPageSummary";
			$<!--##=gsPageObj##-->->RenderRow();
?>
	<!-- Page Summary -->
	<tr<?php echo $<!--##=gsPageObj##-->->RowAttributes(); ?>>
<?php if ($<!--##=gsPageObj##-->->GrpFldCount > 0) { ?>
	<td colspan="<?php echo $<!--##=gsPageObj##-->->GrpFldCount ?>"><!--##=sRptPageSmryType##--></td>
<?php } ?>
<!--##
	if (ew_IsFieldDrillDown(SMRYFIELD)) {
		sSmryPrefix = "<a<?php echo $" + gsPageObj + "->SummaryLinkAttributes($iy-1) ?>>";
		sSmrySuffix = "</a>";
	} else {
		sSmryPrefix = "";
		sSmrySuffix = "";
	}
##-->
<!-- Dynamic columns begin -->
<?php
	$cntcol = count($<!--##=gsPageObj##-->->SummaryViewValue);
	for ($iy = 1; $iy <= $cntcol; $iy++) {
		$bColShow = ($iy <= $<!--##=gsPageObj##-->->ColCount) ? $<!--##=gsPageObj##-->->Col[$iy]->Visible : TRUE;
		$sColDesc = ($iy <= $<!--##=gsPageObj##-->->ColCount) ? $<!--##=gsPageObj##-->->Col[$iy]->Caption : $ReportLanguage->Phrase("Summary");
		if ($bColShow) {
?>
		<!-- <?php echo $sColDesc; ?> -->
		<td<?php echo $<!--##=gsPageObj##-->->SummaryCellAttributes($iy-1) ?>><span<?php echo $<!--##=gsPageObj##-->->SummaryViewAttributes($iy-1); ?>><!--##=sSmryPrefix##--><?php echo $<!--##=gsPageObj##-->->SummaryViewValue[$iy-1] ?><!--##=sSmrySuffix##--></span></td>
<?php
		}
	}
?>
<!-- Dynamic columns end -->
	</tr>
<!--##=sCheckPageTotalEnd##-->
<!--## }; // End show page total ##-->
<!--## if (TABLE.TblRptShowGrandTotal) { ##-->
<?php
			$<!--##=gsPageObj##-->->ResetAttrs();
			$<!--##=gsPageObj##-->->RowType = EWR_ROWTYPE_TOTAL;
			$<!--##=gsPageObj##-->->RowTotalType = EWR_ROWTOTAL_GRAND;
			$<!--##=gsPageObj##-->->RowAttrs["class"] = "ewRptGrandSummary";
			$<!--##=gsPageObj##-->->RenderRow();
?>
	<!-- Grand Total -->
	<tr<?php echo $<!--##=gsPageObj##-->->RowAttributes(); ?>>
<?php if ($<!--##=gsPageObj##-->->GrpFldCount > 0) { ?>
	<td colspan="<?php echo $<!--##=gsPageObj##-->->GrpFldCount ?>"><!--##=sRptGrandSmryType##--></td>
<?php } ?>
<!--##
	if (ew_IsFieldDrillDown(SMRYFIELD)) {
		sSmryPrefix = "<a<?php echo $" + gsPageObj + "->SummaryLinkAttributes($iy-1) ?>>";
		sSmrySuffix = "</a>";
	} else {
		sSmryPrefix = "";
		sSmrySuffix = "";
	}
##-->
<!-- Dynamic columns begin -->
<?php
	$cntcol = count($<!--##=gsPageObj##-->->SummaryViewValue);
	for ($iy = 1; $iy <= $cntcol; $iy++) {
		$bColShow = ($iy <= $<!--##=gsPageObj##-->->ColCount) ? $<!--##=gsPageObj##-->->Col[$iy]->Visible : TRUE;
		$sColDesc = ($iy <= $<!--##=gsPageObj##-->->ColCount) ? $<!--##=gsPageObj##-->->Col[$iy]->Caption : $ReportLanguage->Phrase("Summary");
		if ($bColShow) {
?>
		<!-- <?php echo $sColDesc; ?> -->
		<td<?php echo $<!--##=gsPageObj##-->->SummaryCellAttributes($iy-1) ?>><span<?php echo $<!--##=gsPageObj##-->->SummaryViewAttributes($iy-1); ?>><!--##=sSmryPrefix##--><?php echo $<!--##=gsPageObj##-->->SummaryViewValue[$iy-1] ?><!--##=sSmrySuffix##--></span></td>
<?php
		}
	}
?>
<!-- Dynamic columns end -->
	</tr>
<!--## }; // End show grand total ##-->
</tfoot>
<?php } elseif (!$<!--##=gsPageObj##-->->ShowHeader && <!--##=ew_Val(nSearchFlds > 0)##-->) { // No header displayed ?>
<!--##include rpt-phpcommon-table.php/report-header##-->
<?php } ?>

<?php if ($<!--##=gsPageObj##-->->TotalGrps > 0 || <!--##=ew_Val(nSearchFlds > 0)##-->) { // Show footer ?>
<!--##include rpt-phpcommon-table.php/report-footer##-->
<?php } ?>

<!--## }; // End show report ##-->

<!--##=sSkipPdfExpStart##-->
</div>
<!--##=sSkipPdfExpEnd##-->

<!-- Crosstab report ends -->

<!--##=sHtmlExpStart##-->
	</div>
	<!-- center container (report) (end) -->

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
<!-- Bottom container (end) -->

</div>
<!-- container (end) -->
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
	// Get column values
	function GetColumns() {

		global $conn;
		global $ReportLanguage;

		$this->LoadColumnValues($this->Filter);

		// 1st dimension = no of groups (level 0 used for grand total)
		// 2nd dimension = no of distinct values
		$nGrps = <!--##=nGrps##-->;
		$this->Val = &ewr_InitArray($this->ColCount+1, NULL);
		$this->ValCnt = &ewr_InitArray($this->ColCount+1, NULL);
		$this->Cnt = &ewr_Init2DArray($this->ColCount+1, $nGrps+1, NULL);
		$this->Smry = &ewr_Init2DArray($this->ColCount+1, $nGrps+1, NULL);
		$this->SmryCnt = &ewr_Init2DArray($this->ColCount+1, $nGrps+1, NULL);

		// Reset summary values
		$this->ResetLevelSummary(0);

	<!--## if (bColSearch) { ##-->
		// Set up column search values
		for ($i = 1; $i <= $this->ColCount; $i++) {
			$wrkValue = $this->Col[$i]->Value;
			$wrkCaption = $this->Col[$i]->Caption;
			$this-><!--##=sColFldParm##-->->ValueList[$wrkValue] = $wrkCaption;
		}
	<!--## } ##-->


		// Get active columns
		if (!is_array($this-><!--##=sColFldParm##-->->SelectionList)) {
			$this->ColSpan = $this->ColCount;
		} else {
			$this->ColSpan = 0;
			for ($i = 1; $i <= $this->ColCount; $i++) {
				$bSelected = FALSE;
				$cntsel = count($this-><!--##=sColFldParm##-->->SelectionList);
				for ($j = 0; $j < $cntsel; $j++) {
					if (ewr_CompareValue($this-><!--##=sColFldParm##-->->SelectionList[$j], $this->Col[$i]->Value, <!--##=String(sColFldObjFldType).replace(gsPageObj,"this")##-->)) {
						$this->ColSpan++;
						$bSelected = TRUE;
						break;
					}
				}
				$this->Col[$i]->Visible = $bSelected;
			}
		}

	<!--## if (TABLE.TblRowSum) { ##-->
		$this->ColSpan++; // Add summary column
	<!--## } ##-->

	}

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
	//		$rsgrp->MoveFirst(); // NOTE: no need to move position
			$this-><!--##=arFirstGrpFld['FldVar'].substr(2)##-->->setDbValue(""); // Init first value
		} else { // Get next group
			$rsgrp->MoveNext();
		}

		if (!$rsgrp->EOF) {
			$this-><!--##=arFirstGrpFld['FldVar'].substr(2)##-->->setDbValue($rsgrp->fields[0]);
		} else {
			$this-><!--##=arFirstGrpFld['FldVar'].substr(2)##-->->setDbValue("");
		}
	}

	// Get row values
	function GetRow($opt) {
		global $rs;
		if (!$rs)
			return;
		if ($opt == 1) { // Get first row
	//		$rs->MoveFirst(); // NOTE: no need to move position
		} else { // Get next row
			$rs->MoveNext();
		}
		if (!$rs->EOF) {
	<!--##
		for (var i = 0; i < nGrps; i++) {
			sFldName = arGrpFlds[i]['FldName'];
			sFldObj = "this->" + arGrpFlds[i]['FldVar'].substr(2);
			if (sFldName == arFirstGrpFld['FldName']) {
	##-->
			if ($opt <> 1)
				$<!--##=sFldObj##-->->setDbValue($rs->fields('<!--##=ew_SQuote(sFldName)##-->'));
	<!--##
			} else {
	##-->
			$<!--##=sFldObj##-->->setDbValue($rs->fields('<!--##=ew_SQuote(sFldName)##-->'));
	<!--##
			}
		};
	##-->
	<!--## if (sSmryType == "AVG") { ##-->
			$cntval = count($this->Val);
			for ($ix = 1; $ix < $cntval; $ix++) {
				$this->Val[$ix] = $rs->fields[$ix*2+<!--##=BaseRs##-->-2];
				$this->ValCnt[$ix] = $rs->fields[$ix*2+<!--##=BaseRs##-->-1];
			}
	<!--## } else { ##-->
			$cntval = count($this->Val);
			for ($ix = 1; $ix < $cntval; $ix++)
				$this->Val[$ix] = $rs->fields[$ix+<!--##=BaseRs##-->-1];
	<!--## } ##-->
		} else {
	<!--##
		for (var i = 0; i < nGrps; i++) {
			sFldObj = "this->" + arGrpFlds[i]['FldVar'].substr(2);
	##-->
			$<!--##=sFldObj##-->->setDbValue("");
	<!--##
		};
	##-->
		}
	}

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
		};
	##-->
		}
	}

	// Accummulate summary
	function AccumulateSummary() {
		$cntx = count($this->Smry);
		for ($ix = 1; $ix < $cntx; $ix++) {
			$cnty = count($this->Smry[$ix]);
			for ($iy = 0; $iy < $cnty; $iy++) {
				$valwrk = $this->Val[$ix];
				$this->Cnt[$ix][$iy]++;
				$this->Smry[$ix][$iy] = ewr_SummaryValue($this->Smry[$ix][$iy], $valwrk, $this->getSummaryType());
	<!--## if (sSmryType == "AVG") { ##-->
				$cntwrk = $this->ValCnt[$ix];
				$this->SmryCnt[$ix][$iy] += $cntwrk;
	<!--## } ##-->
			}
		}
	}

	// Reset level summary
	function ResetLevelSummary($lvl) {
		// Clear summary values
		$cntx = count($this->Smry);
		for ($ix = 1; $ix < $cntx; $ix++) {
			$cnty = count($this->Smry[$ix]);
			for ($iy = $lvl; $iy < $cnty; $iy++) {
				$this->Cnt[$ix][$iy] = 0;
				$this->Smry[$ix][$iy] = <!--##=sSmryInitValue##-->;
	<!--## if (sSmryType == "AVG") { ##-->
				$this->SmryCnt[$ix][$iy] = 0;
	<!--## } ##-->
			}
		}
		// Reset record count
		$this->RecCount = 0;
	}

	// Set up starting group
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
			sValueList = "";
			if (bGenFilter) {
				sFldName = arGrpFlds[i]['FldName'];
				sFldObj = "this->" + arGrpFlds[i]['FldVar'].substr(2);
				sSessionFldVar = arGrpFlds[i]['SessionFldVar'];
				sFilterName = arGrpFlds[i]['FilterName'];
				if (sFldName == sColDateFldName) {
					sFld = "$" + sFldObj + "->GroupValue()";
					sFldSelectFilter = "";
				} else if (GetFldObj(sFldName)) {
					sFld = "$" + sFldObj + "->GroupValue()";
					sFormatFld = SYSTEMFUNCTIONS.ScriptViewFormat(sFld);
					if (ew_IsNotEmpty(sFormatFld)) sFld = sFormatFld;
					// Boolean or ENUM/SET field
					if (ew_GetFieldType(goFld.FldType) == 4 || goFld.FldTypeName == "ENUM" || goFld.FldTypeName == "SET") {
						sValueList = GetFieldValues(goFld);
					}
					sFldSelectFilter = goFld.FldSelectFilter.trim();
				};
	##-->
			// Build distinct values for <!--##=sFldName##-->
			if ($popupname == '<!--##=sSessionFldVar##-->') {
	<!--##
				// ENUM/SET field
				if (ew_IsNotEmpty(sValueList)) {
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
					};

				} else {
					if (ew_IsNotEmpty(sFilterName)) {
	##-->
				ewr_SetupDistinctValuesFromFilter($<!--##=sFldObj##-->->ValueList, $<!--##=sFldObj##-->->AdvancedFilters); // Set up popup filter
	<!--##
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
	<!--## if (sFldName == sColDateFldName) { ##-->
						// Skip null values for column date field
	<!--## } else { ##-->
						$bNullValue = TRUE;
	<!--## } ##-->
					} elseif ($<!--##=sFldObj##-->->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$<!--##=sFldObj##-->->GroupViewValue = <!--##=sFld##-->;
						ewr_SetupDistinctValues($<!--##=sFldObj##-->->ValueList, $<!--##=sFldObj##-->->GroupValue(), $<!--##=sFldObj##-->->GroupViewValue, FALSE);
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
		};
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
		<!--## if ((sColFldDateType == "q" || sColFldDateType == "m") && !bColFldDateSelect) { ##-->
					if ($sName == "<!--##=sColDateSessionFldVar##-->" && $this->HasSessionFilterValues("<!--##=sColSessionFldVar##-->"))
						$this->ClearExtFilter = "<!--##=sColSessionFldVar##-->"; // Clear extended filter
					elseif ($sName == "<!--##=sColSessionFldVar##-->")
						$this->ClearExtFilter = ""; // Ignore
		<!--## } ##-->
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
		if (bShowYearSelection) { // Column Year filter
	##-->
				$_SESSION["<!--##=pfxSel##--><!--##=sColDateSessionFldVar##-->"] = "";
	<!--##
		};

		for (var i = 0; i < nGrps; i++) {
			bGenFilter = arGrpFlds[i]['PopupFilter'];
			if (bGenFilter) {
				sFldVar = arGrpFlds[i]['FldVar'];
				sFldParm = sFldVar.substr(2);
	##-->
				$this->ClearSessionSelection('<!--##=sFldParm##-->');
	<!--##
			}
		};

		if (bColSearch || bColExtSearch) {
	##-->
				$_SESSION["<!--##=pfxSel##--><!--##=sColSessionFldVar##-->"] = "";
				$_SESSION["<!--##=pfxRangeFrom##--><!--##=sColSessionFldVar##-->"] = "";
				$_SESSION["<!--##=pfxRangeTo##--><!--##=sColSessionFldVar##-->"] = "";
	<!--##
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
		};

		if (bColSearch || bColExtSearch) {
	##-->
		if (is_array(@$_SESSION["<!--##=pfxSel##--><!--##=sColSessionFldVar##-->"])) {
			$this-><!--##=sColFldParm##-->->SelectionList = @$_SESSION["<!--##=pfxSel##--><!--##=sColSessionFldVar##-->"];
			$this-><!--##=sColFldParm##-->->RangeFrom = @$_SESSION["<!--##=pfxRangeFrom##--><!--##=sColSessionFldVar##-->"];
			$this-><!--##=sColFldParm##-->->RangeTo = @$_SESSION["<!--##=pfxRangeTo##--><!--##=sColSessionFldVar##-->"];
		} elseif (@$_SESSION["<!--##=pfxSel##--><!--##=sColSessionFldVar##-->"] == EWR_INIT_VALUE) { // Select all
			$this-><!--##=sColFldParm##-->->SelectionList = "";
		}
	<!--##
		}
	##-->

	<!--## if (bColFldDateSelect  && ew_IsNotEmpty(sColDateFldName)) { ##-->
		// Process query string
		if (@$_GET["<!--##=sColDateFldName##-->"] <> "") {
			$this-><!--##=sColDateFldParm##-->->setQueryStringValue($_GET["<!--##=sColDateFldName##-->"]);
			if (is_numeric($this-><!--##=sColDateFldParm##-->->QueryStringValue)) {
				$_SESSION["<!--##=pfxSel##--><!--##=sColDateSessionFldVar##-->"] = $this-><!--##=sColDateFldParm##-->->QueryStringValue;
				$this->ResetPager();
			}
		}
		$this-><!--##=sColDateFldParm##-->->SelectionList = @$_SESSION["<!--##=pfxSel##--><!--##=sColDateSessionFldVar##-->"];

		// Get distinct year
		$rsyear = $conn->Execute($this->getSqlCrosstabYear());
		if ($rsyear) {
			while (!$rsyear->EOF) {
				if (!is_null($rsyear->fields[0]))
					$this-><!--##=sColDateFldParm##-->->ValueList[] = $rsyear->fields[0];
				$rsyear->MoveNext();
			}
			$rsyear->Close();
		}
		if (is_array($this-><!--##=sColDateFldParm##-->->ValueList)) {
			if (strval($this-><!--##=sColDateFldParm##-->->SelectionList) == "") {
				$this-><!--##=sColDateFldParm##-->->SelectionList = $this-><!--##=sColDateFldParm##-->->ValueList[0];
			}
		}
	<!--## } ##-->

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
		global $conn, $Security, $ReportLanguage;

		// Set up summary values
	<!--## if (TABLE.TblRowSum) { ##-->
		$colcnt = $this->ColCount+1;
	<!--## } else { ##-->
		$colcnt = $this->ColCount;
	<!--## } ##-->

		$this->SummaryCellAttrs = &ewr_InitArray($colcnt, NULL);
		$this->SummaryViewAttrs = &ewr_InitArray($colcnt, NULL);
		$this->SummaryLinkAttrs = &ewr_InitArray($colcnt, NULL);
		$this->SummaryCurrentValue = &ewr_InitArray($colcnt, NULL);
		$this->SummaryViewValue = &ewr_InitArray($colcnt, NULL);

	<!--## if (TABLE.TblRowSum) { ##-->
		$rowsmry = <!--##=sSmryInitValue##-->;
		$rowcnt = 0;
	<!--## } ##-->

		if ($this->RowTotalType == EWR_ROWTOTAL_GRAND) { // Grand total
			// Aggregate SQL
			$sSql = ewr_BuildReportSql(str_replace("<DistinctColumnFields>", $this->DistinctColumnFields, $this->getSqlSelectAgg()), $this->getSqlWhere(), $this->getSqlGroupByAgg(), "", "", $this->Filter, "");
			$rsagg = $conn->Execute($sSql);
			if ($rsagg && !$rsagg->EOF) $rsagg->MoveFirst();
		}

		for ($i = 1; $i <= $this->ColCount; $i++) {
			if ($this->Col[$i]->Visible) {

				if ($this->RowType == EWR_ROWTYPE_DETAIL) { // Detail row
					$thisval = $this->Val[$i];
	<!--## if (sSmryType == "AVG") { ##-->
					$thiscnt = $this->ValCnt[$i];
	<!--## } ##-->
				} elseif ($this->RowTotalType == EWR_ROWTOTAL_GROUP) { // Group total
					$thisval = $this->Smry[$i][$this->RowGroupLevel];
	<!--## if (sSmryType == "AVG") { ##-->
					$thiscnt = $this->SmryCnt[$i][$this->RowGroupLevel];
	<!--## } ##-->
				} elseif ($this->RowTotalType == EWR_ROWTOTAL_PAGE) { // Page total
					$thisval = $this->Smry[$i][0];
	<!--## if (sSmryType == "AVG") { ##-->
					$thiscnt = $this->SmryCnt[$i][0];
	<!--## } ##-->
				} elseif ($this->RowTotalType == EWR_ROWTOTAL_GRAND) { // Grand total
	<!--## if (sSmryType == "AVG") { ##-->
					$thisval = ($rsagg && !$rsagg->EOF) ? $rsagg->fields[$i*2+<!--##=BaseRsAgg##-->-2] : 0;
					$thiscnt = ($rsagg && !$rsagg->EOF) ? $rsagg->fields[$i*2+<!--##=BaseRsAgg##-->-1] : 0;
	<!--## } else { ##-->
					$thisval = ($rsagg && !$rsagg->EOF) ? $rsagg->fields[$i+<!--##=BaseRsAgg##-->-1] : 0;
	<!--## } ##-->
				}
	<!--## if (sSmryType == "AVG") { ##-->
				$this->SummaryCurrentValue[$i-1] = ($thiscnt > 0) ? $thisval / $thiscnt : 0;
	<!--## } else { ##-->
				$this->SummaryCurrentValue[$i-1] = $thisval;
	<!--## } ##-->

	<!--## if (TABLE.TblRowSum) { ##-->
				$rowsmry = ewr_SummaryValue($rowsmry, $thisval, $this->getSummaryType());
		<!--## if (sSmryType == "AVG") { ##-->
				$rowcnt += $thiscnt;
		<!--## } ##-->
	<!--## } ##-->

			}
		}

		if ($this->RowTotalType == EWR_ROWTOTAL_GRAND) { // Grand total
			if ($rsagg) $rsagg->Close();
		}

	<!--## if (TABLE.TblRowSum) { ##-->
		<!--## if (sSmryType == "AVG") { ##-->
		$this->SummaryCurrentValue[$this->ColCount] = ($rowcnt > 0) ? $rowsmry / $rowcnt : 0;
		<!--## } else { ##-->
		$this->SummaryCurrentValue[$this->ColCount] = $rowsmry;
		<!--## } ##-->
	<!--## } ##-->

	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Table","Row_Rendering")) { ##-->	
		// Call Row_Rendering event
		$this->Row_Rendering();
	<!--## } ##-->

		//
		//  Render view codes
		//
		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row
	<!--##
		for (var i = 0; i < nGrps; i++) {
			sFldName = arGrpFlds[i]['FldName'];
			if (sFldName == sColDateFldName) {
				sFldObj = "this->" + sColDateFldParm;
	##-->
			// <!--##=sColDateFldName##-->
			$<!--##=sFldObj##-->->GroupViewValue = $<!--##=sFldObj##-->->GroupOldValue();
			$<!--##=sFldObj##-->->ResetAttrs();
			$<!--##=sFldObj##-->->ViewAttrs["style"] = "<!--##=ew_Quote(sColDateFldViewStyle)##-->";
			$<!--##=sFldObj##-->->CellAttrs["style"] = "<!--##=ew_Quote(sColDateFldCellStyle)##-->";
			$<!--##=sFldObj##-->->CellAttrs["class"] = ($this->RowGroupLevel == <!--##=i+1##-->) ? "ewRptGrpSummary<!--##=i+1##-->" : "ewRptGrpField<!--##=i+1##-->";
	<!--##
			} else if (GetFldObj(sFldName)) {
				sFldObj = "this->" + arGrpFlds[i]['FldVar'].substr(2);
	##-->
			// <!--##=sFldName##-->
			<!--##~SYSTEMFUNCTIONS.ScriptGroupSummaryView()##-->
	<!--##
			}
		};
	##-->

	<!--##
		GetFldObj(sSmryFldName);
		sFld = "$this->SummaryCurrentValue[$i]";
		sFormatFld = SYSTEMFUNCTIONS.ScriptViewFormat(sFld);
		if (ew_IsNotEmpty(sFormatFld)) sFld = sFormatFld;
		sSmryFldViewStyle = FieldViewStyle(goFld);
		sSmryFldCellStyle = FieldCellStyle(goFld);
	##-->
			// Set up summary values
			$scvcnt = count($this->SummaryCurrentValue);
			for ($i = 0; $i < $scvcnt; $i++) {
				$this->SummaryViewValue[$i] = <!--##=sFld##-->;
				$this->SummaryViewAttrs[$i]["style"] = "<!--##=sSmryFldViewStyle##-->";
				$this->SummaryCellAttrs[$i]["style"] = "<!--##=sSmryFldCellStyle##-->";
				$this->SummaryCellAttrs[$i]["class"] = ($this->RowTotalType == EWR_ROWTOTAL_GROUP) ? "ewRptGrpSummary" . $this->RowGroupLevel : "";

	<!--## if (ew_IsFieldDrillDown(SMRYFIELD)) { ##-->
				if ($this->Export == "") {
					$url = $this-><!--##=sSmryFldParm##-->->DrillDownUrl;
	<!--##
			var DRILLTABLE = DB.Tables.Item(SMRYFIELD.FldDrillTable);
			var sDrillTblVar = DRILLTABLE.TblVar;
			var sFldDrillSourceFields = SMRYFIELD.FldDrillSourceFields.trim();
			if (sFldDrillSourceFields.substr(sFldDrillSourceFields.length-2) == "||")
				sFldDrillSourceFields = sFldDrillSourceFields.substr(0,sFldDrillSourceFields.length-2);
			var arSourceFlds = sFldDrillSourceFields.split("||");
			var sFldDrillTargetFields = SMRYFIELD.FldDrillTargetFields.trim();
			if (sFldDrillTargetFields.substr(sFldDrillTargetFields.length-2) == "||")
				sFldDrillTargetFields = sFldDrillTargetFields.substr(0,sFldDrillTargetFields.length-2);
			var arTargetFlds = sFldDrillTargetFields.split("||");
			if (arSourceFlds.length == arTargetFlds.length) {
				for (var i = 0, cnt = arTargetFlds.length; i < cnt; i++) {
					var SOURCEFLD = goTblFlds.Fields[arSourceFlds[i]];
					var sSourceFldVar = SOURCEFLD.FldVar;
					if (SMRYFIELD.FldVar == sSourceFldVar)
						var sSourceFldObj = sFldObj;
					else
						var sSourceFldObj = "this->" + sSourceFldVar.substr(2);
					if (sSourceFldVar == sColFldVar)
						var sColumnParm = ", $i+1";
					else
						var sColumnParm = "";
					var TARGETFLD = goTblFlds.Fields[arTargetFlds[i]];
					var sTargetFldParm = TARGETFLD.FldVar.substr(2);
					if (SOURCEFLD.FldRowID > 1) {
	##-->
					$parm = ($this->RowTotalType == EWR_ROWTOTAL_GROUP && $this->RowGroupLevel >= <!--##=SOURCEFLD.FldRowID##-->) ? 0 : -1;
					$url = str_replace("f<!--##=i##-->", ewr_Encrypt($this->GetDrillDownSQL($<!--##=sSourceFldObj##-->, "<!--##=sTargetFldParm##-->", $this->RowTotalType, $parm)), $url);
	<!--##
					} else {
	##-->
					$url = str_replace("f<!--##=i##-->", ewr_Encrypt($this->GetDrillDownSQL($<!--##=sSourceFldObj##-->, "<!--##=sTargetFldParm##-->", $this->RowTotalType<!--##=sColumnParm##-->)), $url);
	<!--##
					}
				}
			}
	##-->
					$this->SummaryLinkAttrs[$i]["title"] = ewr_JsEncode($GLOBALS["ReportLanguage"]->Phrase("ClickToDrillDown"));
					$this->SummaryLinkAttrs[$i]["class"] = "ewDrillLink";
					$this->SummaryLinkAttrs[$i]["onclick"] = ewr_DrillDownJs($url, '<!--##=gsSessionFldVar##-->', $GLOBALS["ReportLanguage"]->TablePhrase('<!--##=sDrillTblVar##-->', 'TblCaption'), $this->UseDrillDownPanel);
				}
	<!--## } ##-->

			}

	<!--##
		for (var i = 0; i < nGrps; i++) {
			sFldName = arGrpFlds[i]['FldName'];
			if (sFldName == sColDateFldName) {
				// No refer script
			} else if (GetFldObj(sFldName)) {
	##-->
			// <!--##=sFldName##-->
			<!--##~SYSTEMFUNCTIONS.ScriptSummaryViewRefer()##-->
	<!--##
			}
		};
	##-->

		} else {

	<!--##
		for (var i = 0; i < nGrps; i++) {
			sFldName = arGrpFlds[i]['FldName'];
			if (sFldName == sColDateFldName) {
				sFldObj = "this->" + sColDateFldParm;
	##-->
			// <!--##=sColDateFldName##-->
			$<!--##=sFldObj##-->->GroupViewValue = $<!--##=sFldObj##-->->GroupValue();
			$<!--##=sFldObj##-->->ResetAttrs();
			$<!--##=sFldObj##-->->ViewAttrs["style"] = "<!--##=ew_Quote(sColDateFldViewStyle)##-->";
			$<!--##=sFldObj##-->->CellAttrs["style"] = "<!--##=ew_Quote(sColDateFldCellStyle)##-->";
			$<!--##=sFldObj##-->->CellAttrs["class"] = "ewRptGrpField<!--##=i+1##-->";
			if ($<!--##=sFldObj##-->->GroupValue() == $<!--##=sFldObj##-->->GroupOldValue() && !$this->ChkLvlBreak(<!--##=i+1##-->))
				$<!--##=sFldObj##-->->GroupViewValue = "&nbsp;";
	<!--##
			} else if (GetFldObj(sFldName)) {
				sFldObj = "this->" + gsFldParm;
	##-->
			// <!--##=sFldName##-->
			<!--##~SYSTEMFUNCTIONS.ScriptGroupView()##-->
			if ($<!--##=sFldObj##-->->GroupValue() == $<!--##=sFldObj##-->->GroupOldValue() && !$this->ChkLvlBreak(<!--##=i+1##-->))
				$<!--##=sFldObj##-->->GroupViewValue = "&nbsp;";
	<!--##
			}
		};
	##-->

	<!--##
		GetFldObj(sSmryFldName);
		sFld = "$this->SummaryCurrentValue[$i]";
		sFormatFld = SYSTEMFUNCTIONS.ScriptViewFormat(sFld);
		if (ew_IsNotEmpty(sFormatFld)) sFld = sFormatFld;
		sSmryFldViewStyle = FieldViewStyle(goFld);
		sSmryFldCellStyle = FieldCellStyle(goFld);
	##-->
			// Set up summary values
			$scvcnt = count($this->SummaryCurrentValue);
			for ($i = 0; $i < $scvcnt; $i++) {
				$this->SummaryViewValue[$i] = <!--##=sFld##-->;
				$this->SummaryViewAttrs[$i]["style"] = "<!--##=sSmryFldViewStyle##-->";
				$this->SummaryCellAttrs[$i]["style"] = "<!--##=sSmryFldCellStyle##-->";
				$this->SummaryCellAttrs[$i]["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

	<!--## if (ew_IsFieldDrillDown(SMRYFIELD)) { ##-->
				if ($this->Export == "") {
					$url = $this-><!--##=sSmryFldParm##-->->DrillDownUrl;
	<!--##
			var DRILLTABLE = DB.Tables.Item(SMRYFIELD.FldDrillTable);
			var sDrillTblVar = DRILLTABLE.TblVar;
			var sFldDrillSourceFields = SMRYFIELD.FldDrillSourceFields.trim();
			if (sFldDrillSourceFields.substr(sFldDrillSourceFields.length-2) == "||")
				sFldDrillSourceFields = sFldDrillSourceFields.substr(0,sFldDrillSourceFields.length-2);
			var arSourceFlds = sFldDrillSourceFields.split("||");
			var sFldDrillTargetFields = SMRYFIELD.FldDrillTargetFields.trim();
			if (sFldDrillTargetFields.substr(sFldDrillTargetFields.length-2) == "||")
				sFldDrillTargetFields = sFldDrillTargetFields.substr(0,sFldDrillTargetFields.length-2);
			var arTargetFlds = sFldDrillTargetFields.split("||");
			if (arSourceFlds.length == arTargetFlds.length) {
				for (var i = 0, cnt = arTargetFlds.length; i < cnt; i++) {
					var SOURCEFLD = goTblFlds.Fields[arSourceFlds[i]];
					var sSourceFldVar = SOURCEFLD.FldVar;
					if (SMRYFIELD.FldVar == sSourceFldVar)
						var sSourceFldObj = sFldObj;
					else
						var sSourceFldObj = "this->" + sSourceFldVar.substr(2);
					if (sSourceFldVar == sColFldVar)
						var sColumnParm = ", $i+1";
					else
						var sColumnParm = "";
					var TARGETFLD = goTblFlds.Fields[arTargetFlds[i]];
					var sTargetFldParm = TARGETFLD.FldVar.substr(2);
	##-->
					$url = str_replace("f<!--##=i##-->", ewr_Encrypt($this->GetDrillDownSQL($<!--##=sSourceFldObj##-->, "<!--##=sTargetFldParm##-->", 0<!--##=sColumnParm##-->)), $url);
	<!--##
				}
			}
	##-->
					$this->SummaryLinkAttrs[$i]["title"] = ewr_JsEncode($GLOBALS["ReportLanguage"]->Phrase("ClickToDrillDown"));
					$this->SummaryLinkAttrs[$i]["class"] = "ewDrillLink";
					$this->SummaryLinkAttrs[$i]["onclick"] = ewr_DrillDownJs($url, '<!--##=gsSessionFldVar##-->', $GLOBALS["ReportLanguage"]->TablePhrase('<!--##=sDrillTblVar##-->', 'TblCaption'), $this->UseDrillDownPanel);
				}
	<!--## } ##-->

			}

	<!--##
		for (var i = 0; i < nGrps; i++) {
			sFldName = arGrpFlds[i]['FldName'];
			if (sFldName == sColDateFldName) {
				goFld = COLFIELD;
				var sFldHrefFld = goFld.FldHrefFld; // Save href field
				goFld.FldHrefFld = ""; // No href field
				gsFldParm = sColDateFldParm; // Set up field parm
	##-->
			// <!--##=sFldName##-->
			<!--##~SYSTEMFUNCTIONS.ScriptViewRefer()##-->
	<!--##
				goFld.FldHrefFld = sFldHrefFld; // Restore href field
			} else if (GetFldObj(sFldName)) {
	##-->
			// <!--##=sFldName##-->
			<!--##~SYSTEMFUNCTIONS.ScriptViewRefer()##-->
	<!--##
			}
		};
	##-->

		}

		// Call Cell_Rendered event
	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Table","Cell_Rendered")) { ##-->
		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row
	<!--##
		for (var i = 0; i < nGrps; i++) {
			sFldName = arGrpFlds[i]['FldName'];
			if (sFldName == sColDateFldName) {
				sFldObj = "this->" + sColDateFldParm;
	##-->
			// <!--##=sColDateFldName##-->
			$this->CurrentIndex = <!--##=i##-->; // Group Index
			$CurrentValue = $<!--##=sFldObj##-->->GroupOldValue();
			$ViewValue = &$<!--##=sFldObj##-->->GroupViewValue;
			$ViewAttrs = &$<!--##=sFldObj##-->->ViewAttrs;
			$CellAttrs = &$<!--##=sFldObj##-->->CellAttrs;
			$HrefValue = &$<!--##=sFldObj##-->->HrefValue;
			$LinkAttrs = &$<!--##=sFldObj##-->->LinkAttrs;
			$this->Cell_Rendered($<!--##=sFldObj##-->, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
	<!--##
			} else {
				sFldObj = "this->" + arGrpFlds[i]['FldVar'].substr(2);
	##-->
			// <!--##=sFldName##-->
			$this->CurrentIndex = <!--##=i##-->; // Current index
			$CurrentValue = $<!--##=sFldObj##-->->GroupOldValue();
			$ViewValue = &$<!--##=sFldObj##-->->GroupViewValue;
			$ViewAttrs = &$<!--##=sFldObj##-->->ViewAttrs;
			$CellAttrs = &$<!--##=sFldObj##-->->CellAttrs;
			$HrefValue = &$<!--##=sFldObj##-->->HrefValue;
			$LinkAttrs = &$<!--##=sFldObj##-->->LinkAttrs;
			$this->Cell_Rendered($<!--##=sFldObj##-->, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
	<!--##
			}
		};
	##-->

			for ($i = 0; $i < $scvcnt; $i++) {
				$this->CurrentIndex = $i;
				$CurrentValue = $this->SummaryCurrentValue[$i];
				$ViewValue = &$this->SummaryViewValue[$i];
				$ViewAttrs = &$this->SummaryViewAttrs[$i];
				$CellAttrs = &$this->SummaryCellAttrs[$i];
				$HrefValue = "";
				$LinkAttrs = &$this->SummaryLinkAttrs[$i];
				$this->Cell_Rendered($this-><!--##=sSmryFldParm##-->, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
			}

		} else {

	<!--##
		for (var i = 0; i < nGrps; i++) {
			sFldName = arGrpFlds[i]['FldName'];
			sFldObj = "this->" + arGrpFlds[i]['FldVar'].substr(2);
	##-->
			// <!--##=sFldName##-->
			$this->CurrentIndex = <!--##=i##-->; // Group index
			$CurrentValue = $<!--##=sFldObj##-->->GroupValue();
			$ViewValue = &$<!--##=sFldObj##-->->GroupViewValue;
			$ViewAttrs = &$<!--##=sFldObj##-->->ViewAttrs;
			$CellAttrs = &$<!--##=sFldObj##-->->CellAttrs;
			$HrefValue = &$<!--##=sFldObj##-->->HrefValue;
			$LinkAttrs = &$<!--##=sFldObj##-->->LinkAttrs;
			$this->Cell_Rendered($<!--##=sFldObj##-->, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
	<!--##
		};
	##-->

			for ($i = 0; $i < $scvcnt; $i++) {
				$this->CurrentIndex = $i;
				$CurrentValue = $this->SummaryCurrentValue[$i];
				$ViewValue = &$this->SummaryViewValue[$i];
				$ViewAttrs = &$this->SummaryViewAttrs[$i];
				$CellAttrs = &$this->SummaryCellAttrs[$i];
				$HrefValue = "";
				$LinkAttrs = &$this->SummaryLinkAttrs[$i];
				$this->Cell_Rendered($this-><!--##=sSmryFldParm##-->, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
			}

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
	<!--##
		for (var i = 0; i < nGrps; i++) {
			sFldName = arGrpFlds[i]['FldName'];
			sFldObj = "this->" + arGrpFlds[i]['FldVar'].substr(2);
	##-->
		if ($<!--##=sFldObj##-->->Visible) $this->GrpFldCount += 1;
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