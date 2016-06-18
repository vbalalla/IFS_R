<!--##session phpconfig##-->
<!--##
	var sColFldName = ""; // Column field name (NOT USED)
	var sColDateFldName = ""; // Column date field name (NOT USED)
	var sColFldDateType = ""; // Column date field type (NOT USED)

	// Default PDF Settings (NOT USED)
	lPageBreakRecordCount = 0;
	sPageOrientation = "portrait";
	sPageSize = "a4";

	var nSearchFlds = 0; // Number of search fields

	// Parameter Fields variables
	var arParmFlds = [];
	var sParmFldNames = SYSTEMFUNCTIONS.ParmFieldNames(); // List of parameter field names
	var nParms = 0;
	if (ew_IsNotEmpty(sParmFldNames)) {
		arParmFlds = sParmFldNames.split("\r\n");
		nParms = arParmFlds.length; // Number of parm fields
	};

	sTaskTable = "";
	sTaskIdField = "";
	sTaskNameField = "";
	sTaskStartField = "";
	sTaskEndField = "";
	sTaskFromTaskIdField = "";
	sTaskMilestoneField = "";
	sCategoryName1 = "";
	sCategoryName2 = "";
	sCategoryName3 = "";
	sProcessTable = "";
	sProcessField1 = "";
	sProcessField2 = "";
	sProcessField3 = "";
	sProcessField4 = "";
	sProcessField5 = "";
	sProcessField6 = "";
	sConnectorTable = "";
	iChartWidth = 750;
	iChartHeight = 450;
	var EXT = EXTS("Gantt Chart");
	if (EXT.Enabled) {
		if (EXT.PROJ.DB.Tables.TableExist(TABLE.TblName)) {
			var EXT_TABLE = EXT.PROJ.DB.Tables(TABLE.TblName);
			sTaskTable = EXT_TABLE.Properties("TaskTable").Value;
			sTaskIdField = EXT_TABLE.Properties("TaskIdField").Value;
			sTaskNameField = EXT_TABLE.Properties("TaskNameField").Value;
			sTaskStartField = EXT_TABLE.Properties("TaskStartField").Value;
			sTaskEndField = EXT_TABLE.Properties("TaskEndField").Value;
			sTaskFromTaskIdField = EXT_TABLE.Properties("TaskFromTaskIdField").Value;
			sTaskMilestoneField = EXT_TABLE.Properties("TaskMilestoneField").Value;
			sCategoryName1 = EXT_TABLE.Properties("CategoryName1").Value;
			sCategoryName2 = EXT_TABLE.Properties("CategoryName2").Value;
			sCategoryName3 = EXT_TABLE.Properties("CategoryName3").Value;
			sProcessTable = EXT_TABLE.Properties("ProcessTable").Value;
			sProcessField1 = EXT_TABLE.Properties("ProcessField1").Value;
			sProcessField2 = EXT_TABLE.Properties("ProcessField2").Value;
			sProcessField3 = EXT_TABLE.Properties("ProcessField3").Value;
			sProcessField4 = EXT_TABLE.Properties("ProcessField4").Value;
			sProcessField5 = EXT_TABLE.Properties("ProcessField5").Value;
			sProcessField6 = EXT_TABLE.Properties("ProcessField6").Value;
			sConnectorTable = EXT_TABLE.Properties("ConnectorTable").Value;
			if (EXT_TABLE.Properties("GanttChartWidth") && ew_IsNotEmpty(EXT_TABLE.Properties("GanttChartWidth").Value))
				iChartWidth = EXT_TABLE.Properties("GanttChartWidth").Value;
			if (EXT_TABLE.Properties("GanttChartHeight") && ew_IsNotEmpty(EXT_TABLE.Properties("GanttChartHeight").Value))
				iChartHeight = EXT_TABLE.Properties("GanttChartHeight").Value;
		}
	}
	sHeaderColor = PROJ.GetV("ThemeTableHeaderBackColor").replace(/#/g, "");
	sHeaderFontColor = PROJ.GetV("ThemeTableHeaderTextColor").replace(/#/g, "");
	sCatColor = PROJ.GetV("ThemeTableBodyAlternatingRowColor").replace(/#/g, "");
	sCatFontColor = PROJ.GetV("ThemeTableBodyAlternatingRowTextColor").replace(/#/g, "");
	sBorderColor = PROJ.GetV("ThemeGridBorderColor").replace(/#/g, "");
	if (ew_IsNotEmpty(sProcessTable)) {
		PROCTABLE = DB.Tables(sProcessTable);
		sProcTblVar = PROCTABLE.TblVar;
		if (ew_IsNotEmpty(sProcessField1)) {
			PROCFIELD = PROCTABLE.Fields(sProcessField1);
			sProcessFldVar1 = PROCFIELD.FldParm;
		}
		if (ew_IsNotEmpty(sProcessField2)) {
			PROCFIELD = PROCTABLE.Fields(sProcessField2);
			sProcessFldVar2 = PROCFIELD.FldParm;
		}
		if (ew_IsNotEmpty(sProcessField3)) {
			PROCFIELD = PROCTABLE.Fields(sProcessField3);
			sProcessFldVar3 = PROCFIELD.FldParm;
		}
		if (ew_IsNotEmpty(sProcessField4)) {
			PROCFIELD = PROCTABLE.Fields(sProcessField4);
			sProcessFldVar4 = PROCFIELD.FldParm;
		}
		if (ew_IsNotEmpty(sProcessField5)) {
			PROCFIELD = PROCTABLE.Fields(sProcessField5);
			sProcessFldVar5 = PROCFIELD.FldParm;
		}
		if (ew_IsNotEmpty(sProcessField6)) {
			PROCFIELD = PROCTABLE.Fields(sProcessField6);
			sProcessFldVar6 = PROCFIELD.FldParm;
		}
	}

	// No grouping fields
	nGrps = 0;

	// Detail Fields variables
	var arGrpFlds = [];
	var arDtlFlds = [];
	var nDtls = 0;
	for (var i = 1, cnt = TABLE.Fields.Count(); i <= cnt; i++) {
		FIELD = TABLE.Fields.Seq(i);
		if (FIELD.FldList && FIELD.FldGenerate) {
			var dtlfld = [];
			dtlfld['FldName'] = FIELD.FldName; // Field name
			dtlfld['FldVar'] = FIELD.FldVar; // Field var
			dtlfld['FldObj'] = gsTblVar + "->" + FIELD.FldParm; // Field object
			arDtlFlds[arDtlFlds.length] = dtlfld;
			nDtls += 1;
		}
	}; // End for i

	bShowChart = true; // Always show chart
##-->
<!--##/session##-->


<?php
<!--##session phpmain##-->

	// Initialize common variables
	var $Gantt; // Gantt chart

	var $ExportOptions; // Export options
	var $SearchOptions; // Search options
	var $FilterOptions; // Filter options

	// Clear field for extended filter
	var $ClearExtFilter = "";
	var $FilterApplied;

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
	var $TaskIdFilter = "";
	var $TaskNameFilter = "";
	var $UserIDFilter = "";
	var $DrillDown = FALSE;
	var $DrillDownInPanel = FALSE;

	var $SearchCommand = FALSE;

	var $ShowFirstHeader;

	var $Cnt, $Col, $Val, $Smry, $Mn, $Mx, $GrandSmry, $GrandMn, $GrandMx;
	var $TotCount;

	//
	// Page main
	//
	function Page_Main() {
		global $DisplayGrps;
		global $rs;
<!--## if (bTableHasUserIDFld) { ##-->
		global $Security;
<!--## } ##-->
		global $gsFormError;
		global $ReportLanguage;
		global $ReportBreadcrumb;

	<!--## if (bTableHasUserIDFld) { ##-->
		// Set up User ID
		$this->UserIDFilter = $this->GetUserIDFilter();
		$this->Filter = $this->UserIDFilter;
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

	<!--## if (bReportExtFilter) { ##-->

		// Check if search command
		$this->SearchCommand = (@$_GET["cmd"] == "search");

		// Load default filter values
		$this->LoadDefaultFilters();

	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Table","Page_FilterLoad")) { ##-->
		// Load custom filters
		$this->Page_FilterLoad();
	<!--## } ##-->

		// Extended filter
		$sExtendedFilter = "";

		// Build extended filter
		$sExtendedFilter = $this->GetExtendedFilter();
		ewr_AddFilter($this->Filter, $sExtendedFilter);

		// Set up task id / task name filter
	<!--##
		for (var i = 1, cnt = TABLE.Fields.Count(); i <= cnt; i++) {
			FIELD = TABLE.Fields.Seq(i);
			if (FIELD.FldGenerate && IsExtendedFilter(FIELD)) {
				sFldName = FIELD.FldName;
				sFldParm = FIELD.FldParm;
				sFldObj = "this->" + sFldParm;
				if (sFldName == sTaskIdField) {
					sFilter = "$this->TaskIdFilter";
				} else if (sFldName == sTaskNameField) {
					sFilter = "$this->TaskNameFilter";
				} else {
					sFilter = "";
				}
				if (ew_IsNotEmpty(sFilter)) {
					if (!IsTextFilter(FIELD)) {
	##-->
		$this->BuildDropDownFilter($<!--##=sFldObj##-->, <!--##=sFilter##-->, "");
	<!--##
					} else {
	##-->
		$this->BuildExtendedFilter($<!--##=sFldObj##-->, <!--##=sFilter##-->);
	<!--##
					}
				}
			}
		}
	##-->

		// Check if filter applied
		$this->FilterApplied = $this->CheckFilter();
		$this->SearchOptions->GetItem("resetfilter")->Visible = $this->FilterApplied;

	<!--## } else { ##-->

		// No filter
		$this->FilterApplied = FALSE;

	<!--## } ##-->

		// Get total count
		$sSql = ewr_BuildReportSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(), $this->Filter, $this->Sort);
		$this->TotalGrps = $this->GetCnt($sSql);

		// Display all groups
		if ($this->DisplayGrps <= 0)
			$this->DisplayGrps = $this->TotalGrps;
		$this->StartGrp = 1;

		// Show header
		$this->ShowFirstHeader = ($this->TotalGrps > 0);
		//$this->ShowFirstHeader = TRUE; // Uncomment to always show header

		// Set up start position if not export all
		if ($this->ExportAll && $this->Export <> "")
		    $this->DisplayGrps = $this->TotalGrps;
		else
			$this->SetUpStartGroup(); 

		// Get current page records
		$rs = $this->GetRs($sSql, $this->StartGrp, $this->DisplayGrps);

		// Create gantt chart
		$this->Gantt = new crGantt("<!--##=ew_Quote(sTaskTable)##-->", "<!--##=ew_Quote(sTaskIdField)##-->", "<!--##=ew_Quote(sTaskNameField)##-->", "<!--##=ew_Quote(sTaskStartField)##-->", "<!--##=ew_Quote(sTaskEndField)##-->");
		$this->Gantt->TblVar = "<!--##=gsTblVar##-->";
		$this->Gantt->ID = $this->Gantt->TblVar;
		$this->Gantt->Table = $GLOBALS['<!--##=gsTblVar##-->'];
		$this->Gantt->TaskFilter = $this->Filter;
		$this->Gantt->TaskIdFilter = $this->TaskIdFilter;
		$this->Gantt->TaskNameFilter = $this->TaskNameFilter;
<!--## if (ew_IsNotEmpty(sTaskMilestoneField)) { ##-->
		$this->Gantt->TaskMilestoneDateField = "<!--##=ew_Quote(sTaskMilestoneField)##-->";
<!--## } ##-->
<!--## if (ew_IsNotEmpty(sTaskFromTaskIdField)) { ##-->
		$this->Gantt->TaskFromTaskIdField = "<!--##=ew_Quote(sTaskFromTaskIdField)##-->";
<!--## } ##-->

		// Colors from theme
		$this->Gantt->HeaderColor = '<!--##=sHeaderColor##-->';
		$this->Gantt->HeaderFontColor = '<!--##=sHeaderFontColor##-->';
		$this->Gantt->CategoryColor = '<!--##=sCatColor##-->';
		$this->Gantt->CategoryFontColor = '<!--##=sCatFontColor##-->';
		
		// Add other chart attributes directly, e.g.
		$this->Gantt->ChartAttrs["canvasBorderColor"] = '<!--##=sBorderColor##-->';

		// Add categories
		$this->Gantt->AddCategories("<!--##=ew_Quote(sCategoryName1)##-->"); // Category 1
		$this->Gantt->AddCategories("<!--##=ew_Quote(sCategoryName2)##-->"); // Category 2
		$this->Gantt->AddCategories("<!--##=ew_Quote(sCategoryName3)##-->"); // Category 3
		$this->Gantt->ProcessTable = "<!--##=ew_Quote(sProcessTable)##-->"; // ProcessTable

		// Add DataColumn
<!--## if (ew_IsNotEmpty(sProcessTable) && ew_IsNotEmpty(sProcessField1)) { ##-->
		$this->Gantt->AddDataColumn("<!--##=ew_Quote(sProcessField1)##-->", $ReportLanguage->FieldPhrase("<!--##=sProcTblVar##-->", "<!--##=sProcessFldVar1##-->", "FldCaption"));
<!--## } ##-->
<!--## if (ew_IsNotEmpty(sProcessTable) && ew_IsNotEmpty(sProcessField2)) { ##-->
		$this->Gantt->AddDataColumn("<!--##=ew_Quote(sProcessField2)##-->", $ReportLanguage->FieldPhrase("<!--##=sProcTblVar##-->", "<!--##=sProcessFldVar2##-->", "FldCaption"));
<!--## } ##-->
<!--## if (ew_IsNotEmpty(sProcessTable) && ew_IsNotEmpty(sProcessField3)) { ##-->
		$this->Gantt->AddDataColumn("<!--##=ew_Quote(sProcessField3)##-->", $ReportLanguage->FieldPhrase("<!--##=sProcTblVar##-->", "<!--##=sProcessFldVar3##-->", "FldCaption"));
<!--## } ##-->
<!--## if (ew_IsNotEmpty(sProcessTable) && ew_IsNotEmpty(sProcessField4)) { ##-->
		$this->Gantt->AddDataColumn("<!--##=ew_Quote(sProcessField4)##-->", $ReportLanguage->FieldPhrase("<!--##=sProcTblVar##-->", "<!--##=sProcessFldVar4##-->", "FldCaption"));
<!--## } ##-->
<!--## if (ew_IsNotEmpty(sProcessTable) && ew_IsNotEmpty(sProcessField5)) { ##-->
		$this->Gantt->AddDataColumn("<!--##=ew_Quote(sProcessField5)##-->", $ReportLanguage->FieldPhrase("<!--##=sProcTblVar##-->", "<!--##=sProcessFldVar5##-->", "FldCaption"));
<!--## } ##-->
<!--## if (ew_IsNotEmpty(sProcessTable) && ew_IsNotEmpty(sProcessField6)) { ##-->
		$this->Gantt->AddDataColumn("<!--##=ew_Quote(sProcessField6)##-->", $ReportLanguage->FieldPhrase("<!--##=sProcTblVar##-->", "<!--##=sProcessFldVar6##-->", "FldCaption"));
<!--## } ##-->

		// Connector table
		$this->Gantt->ConnectorTable = "<!--##=ew_Quote(sConnectorTable)##-->"; // ConnectorTable

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


<!--##session report_content##-->

<!--##=sChartExpStart##-->
<script src="<?php echo EWR_FUSIONCHARTS_FREE_JSCLASS_FILE ?>" type="text/javascript"></script>
<!--##=sChartExpEnd##-->

<!--##=sExpStart##-->
<!-- Table Container (Begin) -->
<div id="ewContainer" class="ewContainer">

<!-- Top Container (Begin) -->
<div id="ewTop" class="ewTop">
<!-- top slot -->
<a name="top"></a>
<!--##=sExpEnd##-->

<div class="ewToolbar">
<!--##include rpt-phpcommon.php/breadcrumb##-->
<!--##include rpt-phpcommon.php/language##-->
<div class="clearfix"></div>
</div>

<!--##include rpt-phpcommon.php/header-message##-->
<!--##include rpt-phpcommon.php/common-message##-->

<!--##=sExpStart##-->
</div>
<!-- Top Container (End) -->

	<!-- Left Container (Begin) -->
	<div id="ewLeft" class="ewLeft">
	<!-- Left slot -->
	</div>
	<!-- Left Container (End) -->

	<!-- Center Container - Report (Begin) -->
	<div id="ewCenter" class="ewCenter">
	<!-- center slot -->
<!--##=sExpEnd##-->

<!--##include rpt-extfilter.php/report_extfilter_html##-->

<!--## if (bShowReport) { ##-->
<!-- report starts -->
<div id="report">
<div class="ewGrid">

<!--## if (bTopPageLink) { ##-->
<!--##=sExpStart##-->
<div class="ewGridUpperPanel">
<!--##include rpt-pager.php/phppager##-->
</div>
<div class="clearfix"></div>
<!--##=sExpEnd##-->
<!--## } ##-->

<!-- Report Grid (Begin) -->
<div class="<?php if (ewr_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table class="table ewTable">

<?php
// Set the last group to display if not export all
if ($<!--##=gsTblVar##-->->ExportAll && $<!--##=gsTblVar##-->->Export <> "") {
	$<!--##=sPageObj##-->->StopGrp = $<!--##=sPageObj##-->->TotalGrps;
} else {
	$<!--##=sPageObj##-->->StopGrp = $<!--##=sPageObj##-->->StartGrp + $<!--##=sPageObj##-->->DisplayGrps - 1;
}

// Stop group <= total number of groups
if (intval($<!--##=sPageObj##-->->StopGrp) > intval($<!--##=sPageObj##-->->TotalGrps))
	$<!--##=sPageObj##-->->StopGrp = $<!--##=sPageObj##-->->TotalGrps;

$<!--##=sPageObj##-->->RecCount = 0;
$<!--##=sPageObj##-->->RecIndex = 0;

// Get first row
if ($<!--##=sPageObj##-->->TotalGrps > 0) {
	$<!--##=sPageObj##-->->GetRow(1);
	$<!--##=sPageObj##-->->GrpCount = 1;
}

while (($rs && !$rs->EOF && $<!--##=sPageObj##-->->GrpCount <= $<!--##=sPageObj##-->->DisplayGrps) || $<!--##=sPageObj##-->->ShowFirstHeader) {

	// Show header
	if ($<!--##=sPageObj##-->->ShowFirstHeader) {
?>
	<thead>
	<tr class="ewTableHeader">
<!--##
	for (var i = 0; i < nDtls; i++) {
		sFldName = arDtlFlds[i]['FldName'];
		sFldObj = arDtlFlds[i]['FldObj'];
		sFldVar = arDtlFlds[i]['FldVar'];
		sFldParm = sFldVar.substr(2);
		GetFldObj(sFldName);
		sTDStyle = FieldTD_Header(goFld);
		sClassId = gsTblVar + "_" + sFldParm;
		sJsSort = " class=\"ewTableHeaderBtn ewPointer " + sClassId + "\" onclick=\"ewr_Sort(event,'<?php echo $" + gsTblVar + "->SortUrl($" + sFldObj + ") ?>'," + iSortType + ");\"";
##-->
<td data-field="<!--##=sFldParm##-->">
<?php if ($<!--##=gsTblVar##-->->SortUrl($<!--##=sFldObj##-->) == "") { ?>
		<div class="<!--##=sClassId##-->"<!--##=sTDStyle##-->><span class="ewTableHeaderCaption"><?php echo $<!--##=sFldObj##-->->FldCaption() ?></span></div>
<?php } else { ?>
		<div<!--##=sJsSort##--><!--##=sTDStyle##-->>
			<span class="ewTableHeaderCaption"><?php echo $<!--##=sFldObj##-->->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($<!--##=sFldObj##-->->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($<!--##=sFldObj##-->->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
</td>
<!--##
	}
##-->
	</tr>
	</thead>
	<tbody>
<?php
		$<!--##=sPageObj##-->->ShowFirstHeader = FALSE;
	}

	$<!--##=sPageObj##-->->RecCount++;
	$<!--##=sPageObj##-->->RecIndex++;

	// Set row color
	$<!--##=gsTblVar##-->->ResetAttrs();
	$<!--##=gsTblVar##-->->RowType = EWR_ROWTYPE_DETAIL;
	$<!--##=sPageObj##-->->RenderRow();
?>
	<tr<?php echo $<!--##=gsTblVar##-->->RowAttributes(); ?>>
<!--##
	for (var i = 0; i < nDtls; i++) {
		sFldName = arDtlFlds[i]['FldName'];
		sFldObj = arDtlFlds[i]['FldObj'];
		GetFldObj(sFldName);
##-->
		<td<?php echo $<!--##=sFldObj##-->->CellAttributes() ?>><!--##=SYSTEMFUNCTIONS.FieldView()##--></td>
<!--##
	}
##-->
	</tr>
<?php
	// Get next record
	$<!--##=sPageObj##-->->GetRow(2);
	$<!--##=sPageObj##-->->GrpCount++;
} // End while
?>
	</tbody>
</table>
</div>

<!--## if (bBottomPageLink) { ##-->
	<!--## if (bTopPageLink) { ##-->
<?php if ($<!--##=sPageObj##-->->TotalGrps > 0) { ?>
	<!--## } ##-->
<!--##=sExpStart##-->
<div class="ewGridLowerPanel">
<!--##include rpt-pager.php/phppager##-->
</div>
<div class="clearfix"></div>
<!--##=sExpEnd##-->
	<!--## if (bTopPageLink) { ##-->
<?php } ?>
	<!--## } ##-->
<!--## } ##-->

</div>

</div>
<!-- Summary Report Ends -->
<!--## } // End show report ##-->

<!--##=sExpStart##-->
	</div>
	<!-- Center Container - Report (End) -->

	<!-- Right Container (Begin) -->
	<div id="ewRight" class="ewRight">
	<!-- Right slot -->
	</div>
	<!-- Right Container (End) -->

<div class="clearfix"></div>

<!-- Bottom Container (Begin) -->
	<div id="ewBottom" class="ewBottom">
	<!-- Bottom slot -->
<!--##=sExpEnd##-->
<!--##=sChartExpStart##-->
<!--##include rpt-ganttchart.php/chart_content##-->
<!--##=sChartExpEnd##-->
<!--##=sExpStart##-->
	</div>
<!-- Bottom Container (End) -->

</div>
<!-- Table Container (End) -->
<!--##=sExpEnd##-->

<!--##include rpt-phpcommon.php/footer-message##-->
<?php
// Close recordset
if ($rs) $rs->Close();
?>
<!--##/session##-->


<?php
<!--##session phpfunction##-->

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
		for (var i = 0; i < nAllFldCount; i++) {
			GetFldObj(arAllFlds[i]);
			sFldObj = "this->" + gsFldParm;
	##-->
			$<!--##=sFldObj##-->->setDbValue($rs->fields('<!--##=ew_SQuote(gsFldName)##-->'));
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

	// Reset pager
	function ResetPager() {
		// Reset start position (reset command)
		$this->StartGrp = 1;
		$this->setStartGroup($this->StartGrp);
	}

	<!--## if (ew_IsNotEmpty(sGrpPerPageList)) { ##-->
	<!--##include rpt-pager.php/setupdisplaygrps##-->
	<!--## } ##-->

	function RenderRow() {
		global $conn, $Security, $ReportLanguage;

	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Table","Row_Rendering")) { ##-->	
		// Call Row_Rendering event
		$this->Row_Rendering();
	<!--## } ##-->

		/* --------------------
		'  Render view codes
		' --------------------- */

	<!--##
		for (var i = 0; i < nDtls; i++) {
			sFldName = arDtlFlds[i]['FldName'];
			GetFldObj(sFldName);
	##-->
		// <!--##=sFldName##-->
		<!--##~SYSTEMFUNCTIONS.ScriptView()##-->
	<!--##
		}
	##-->

	<!--##
		for (var i = 0; i < nDtls; i++) {
			sFldName = arDtlFlds[i]['FldName'];
			GetFldObj(sFldName);
	##-->
		// <!--##=sFldName##-->
		<!--##~SYSTEMFUNCTIONS.ScriptViewRefer()##-->
	<!--##
		}
	##-->

	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Table","Row_Rendered")) { ##-->
		// Call Row_Rendered event
		$this->Row_Rendered();
	<!--## } ##-->

	}

<!--##/session##-->
?>