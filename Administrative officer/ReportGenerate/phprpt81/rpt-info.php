<!--##session infoconfig##-->
<!--##
	sAggPfx = "";
	sAggSfx = "";
	sWhere = "";

	// Dashboard report
	if (TABLE.TblReportType == "dashboard") {
		// Skip
	// Crosstab report
	} else if (TABLE.TblReportType == "crosstab") {
	
		// Row fields variables
		sRowFldNames = SYSTEMFUNCTIONS.RowFieldNames(); // List of row field names
		arRows = sRowFldNames.split("\r\n");
		nRows = arRows.length; // Number of row fields
	
		// Column field variables
		sColFldName = SYSTEMFUNCTIONS.ColumnFieldNames(); // Column field Name
		COLFIELD = goTblFlds.Fields[sColFldName];
		sColFld = ew_FieldSqlName(COLFIELD);
		goFld = COLFIELD;
		if (ew_GetFieldType(COLFIELD.FldType) == 2) {
			sColFldDateType = COLFIELD.FldColumnDateType;
			bColFldDateSelect = COLFIELD.FldColumnDateSelect;
		} else {
			sColFldDateType = "";
			bColFldDateSelect = false;
		}
		if (sColFldDateType == "q") {
			sColumnCaptions = "$ReportLanguage->Phrase(\"Qtr1\") . \",\" . $ReportLanguage->Phrase(\"Qtr2\") . \",\"" +
									" . $ReportLanguage->Phrase(\"Qtr3\") . \",\" . $ReportLanguage->Phrase(\"Qtr4\")";
			sColumnNames = "Qtr1,Qtr2,Qtr3,Qtr4";
			sColumnValues = "1,2,3,4"; // Column values
		} else if (sColFldDateType == "m") {
			sColumnCaptions = "$ReportLanguage->Phrase(\"MonthJan\") . \",\" . $ReportLanguage->Phrase(\"MonthFeb\") . \",\"" +
								" . $ReportLanguage->Phrase(\"MonthMar\") . \",\" . $ReportLanguage->Phrase(\"MonthApr\") . \",\"" +
								" . $ReportLanguage->Phrase(\"MonthMay\") . \",\" . $ReportLanguage->Phrase(\"MonthJun\") . \",\"" +
								" . $ReportLanguage->Phrase(\"MonthJul\") . \",\" . $ReportLanguage->Phrase(\"MonthAug\") . \",\"" +
								" . $ReportLanguage->Phrase(\"MonthSep\") . \",\" . $ReportLanguage->Phrase(\"MonthOct\") . \",\"" +
								" . $ReportLanguage->Phrase(\"MonthNov\") . \",\" . $ReportLanguage->Phrase(\"MonthDec\")";
			sColumnNames = "MonthJan,MonthFeb,MonthMar,MonthApr,MonthMay,MonthJun,MonthJul,MonthAug,MonthSep,MonthOct,MonthNov,MonthDec";
			sColumnValues = "1,2,3,4,5,6,7,8,9,10,11,12"; // Column values
		} else {
			sColumnCaptions = "\"\"";
			sColumnNames = "";
			sColumnValues = ""; // Column values
		}
		sSelect = SYSTEMFUNCTIONS.CrosstabSql("SELECT");
		sSelectAgg = SYSTEMFUNCTIONS.CrosstabSql("SELECT AGGREGATE"); // Select Aggregate
		sFrom = SYSTEMFUNCTIONS.CrosstabSql("FROM");
		sWhere = "\"" + ew_Quote2(SYSTEMFUNCTIONS.CrosstabSql("WHERE")) + "\"";
		sGroupBy = SYSTEMFUNCTIONS.CrosstabSql("GROUP BY");
		sGroupByAgg = SYSTEMFUNCTIONS.CrosstabSql("GROUP BY AGGREGATE"); // Group By Aggregate
		// Get first grouping field
		sFirstGroupFld = SYSTEMFUNCTIONS.RowFieldNames();
		if (ew_IsNotEmpty(sFirstGroupFld)) {
			sFirstGroupFld = sFirstGroupFld.split("\r\n")[0];
			FIRSTGROUPFLD = goTblFlds.Fields[sFirstGroupFld];
			sFirstGroupFldSql = ew_FieldSqlName(FIRSTGROUPFLD);
			sFirstGroupFldOrderType = FIRSTGROUPFLD.FldOrder;
			if (ew_IsEmpty(sFirstGroupFldOrderType)) sFirstGroupFldOrderType = "ASC";
		} else {
			sFirstGroupFldSql = "";
		}
		sOrderBy = SYSTEMFUNCTIONS.CrosstabSql("ORDER BY");
		sDistinctSelect = SYSTEMFUNCTIONS.DistinctColumnSql("SELECT");
		sDistinctSqlWhere = sWhere;
		sDistinctOrderBy = sDistinctSelect;
		// Column field sort sequence
		if (ew_IsEmpty(COLFIELD.FldOrder)) {
			sDistinctOrderBy = "";
		} else {
			sDistinctOrderBy += " " + COLFIELD.FldOrder;
		}

		// Summary field variables
		sSmryFldName = SYSTEMFUNCTIONS.SummaryFieldNames(); // Summary field name
		SMRYFIELD = goTblFlds.Fields[sSmryFldName];
		sSmryFld = ew_FieldSqlName(SMRYFIELD);
		sSmryType = SMRYFIELD.FldSummaryType;

	} else { // Summary/simple report

		if (TABLE.TblReportType == "summary") {

			sSelect = SYSTEMFUNCTIONS.ReportSql("SELECT");
			sFrom = SYSTEMFUNCTIONS.ReportSql("FROM");
			sWhere = "\"" + ew_Quote2(SYSTEMFUNCTIONS.ReportSql("WHERE")) + "\"";
			sGroupBy = SYSTEMFUNCTIONS.ReportSql("GROUP BY");
			// Get first grouping field
			sFirstGroupFld = SYSTEMFUNCTIONS.GroupByFieldNames();
			if (ew_IsNotEmpty(sFirstGroupFld)) {
				sFirstGroupFld = sFirstGroupFld.split("\r\n")[0];
				FIRSTGROUPFLD = goTblFlds.Fields[sFirstGroupFld];
				sFirstGroupFldSql = ew_FieldSqlName(FIRSTGROUPFLD);
				sFirstGroupFldGroupByType = FIRSTGROUPFLD.FldGroupByType;
				sFirstGroupFldGroupByInterval = FIRSTGROUPFLD.FldGroupByInterval;
				sFirstGroupDbGrpSql = ew_DbGrpSql(sFirstGroupFldGroupByType, sFirstGroupFldGroupByInterval);
				if (ew_IsNotEmpty(sFirstGroupDbGrpSql)) {
					sFirstGroupFldSql = sFirstGroupDbGrpSql.replace(/%s/g, sFirstGroupFldSql);
					sSelect += ", " + sFirstGroupFldSql;
				}
				sFirstGroupFldOrderType = FIRSTGROUPFLD.FldOrder;
				if (ew_IsEmpty(sFirstGroupFldOrderType)) sFirstGroupFldOrderType = "ASC";
			} else {
				sFirstGroupFldSql = "";
				sFirstGroupFldOrderType = "";
			}
			// Get summary fields
			sAggFlds = SYSTEMFUNCTIONS.ReportSql("SELECT AGGREGATE");
			sAggPfxFlds = SYSTEMFUNCTIONS.ReportSql("AGGREGATE PREFIX");
			if (ew_IsNotEmpty(sAggPfxFlds)) {
				sAggPfx = "SELECT " + sAggPfxFlds + " FROM (";
				sAggSfx = ") AS " + ew_QuotedName("TMPTABLE");
			}
			sHaving = SYSTEMFUNCTIONS.ReportSql("HAVING");
			sOrderBy = SYSTEMFUNCTIONS.ReportSql("ORDER BY");

		} else { // Simple report (rpt)

			if (TABLE.TblType == "REPORT") {
				WRKTABLE = DB.Tables(TABLE.TblRptSrc);
			} else {
				WRKTABLE = TABLE;
			}
			if (WRKTABLE.TblType == "CUSTOMVIEW") {
				sLimitPart = ew_SQLPart(WRKTABLE.TblCustomSQL, "LIMIT").trim();
				sGroupBy = ew_SQLPart(WRKTABLE.TblCustomSql, "GROUP BY");
				sHaving = ew_SQLPart(WRKTABLE.TblCustomSql, "HAVING");
				if (ew_IsNotEmpty(sLimitPart) || (ew_IsNotEmpty(sGroupBy) && ew_IsNotEmpty(sHaving))) {
					sSelect = "*";
					sFrom = "(" + WRKTABLE.TblCustomSQL + ") " + ew_QuotedName("EW_CV_" + WRKTABLE.TblVar);
					sWhere = "";
					sGroupBy = "";
					sHaving = "";
				} else {
					sSelect = ew_SQLPart(WRKTABLE.TblCustomSql, "SELECT");
					sFrom = ew_SQLPart(WRKTABLE.TblCustomSql, "FROM");
					sWhere = "\"" + ew_Quote2(ew_SQLPart(WRKTABLE.TblCustomSql, "WHERE")) + "\"";
					sGroupBy = ew_SQLPart(WRKTABLE.TblCustomSql, "GROUP BY");
					sHaving = ew_SQLPart(WRKTABLE.TblCustomSql, "HAVING");
					sOrderBy = ew_SQLPart(WRKTABLE.TblCustomSql, "ORDER BY");
				}
			} else {
				sSelect = "*";
				sFrom = ew_TableName(WRKTABLE);
				sWhere = WRKTABLE.TblFilter;
				sGroupBy = "";
				sHaving = "";
				sOrderBy = "";
				sLimitPart = "";
			}
			// Get summary fields
			if (TABLE.TblType != "REPORT") {
				sAggFlds = SYSTEMFUNCTIONS.ReportSql("SELECT AGGREGATE");
			}
		}

	}
	if (sWhere == "") sWhere = "\"\""; // Empty String
##-->
<!--##/session##-->


<!--##session infoclass##-->
<?php
// Global variable for table object
$<!--##=gsTblVar##--> = NULL;

//
// Table class for <!--##=gsTblName##-->
//

<!--##
	if (TABLE.TblReportType == "dashboard") {
##-->

class cr<!--##=gsTblVar##--> extends crTableBase {

	//
	// Table class constructor
	//
	function __construct() {
		global $ReportLanguage;

		$this->TableVar = '<!--##=gsTblVar##-->';
		$this->TableName = '<!--##=ew_SQuote(gsTblName)##-->';
		$this->TableType = '<!--##=TABLE.TblType##-->';

	}

<!--##
	} else {
##-->

<!--## if (TABLE.TblReportType == "crosstab") { ##-->
class cr<!--##=gsTblVar##--> extends crTableCrosstab {
<!--## } else { ##-->
class cr<!--##=gsTblVar##--> extends crTableBase {
<!--## } ##-->

//	var $SelectLimit = <!--##=ew_Val(bDBMySql || bDBPostgreSql)##-->;

<!--##
	for (var i = 0, len = arAllCharts.length; i < len; i++) {
		if (GetChtObj(arAllCharts[i])) {
			if (IsShowChart(goCht)) {
##-->
	var $<!--##=gsChartVar##-->;
<!--##
			}
		}
	}
##-->

<!--##
	for (var i = 0; i < nAllFldCount; i++) {
		if (GetFldObj(arAllFlds[i])) {
##-->
	var $<!--##=gsFldParm##-->;
<!--##
		}
	}

	if (TABLE.TblReportType == "crosstab") { // Crosstab Report
		if (sColFldDateType == "q" || sColFldDateType == "m") {
##-->
	var $<!--##=sColDateFldParm##-->;
<!--##
		}
	}
##-->

	//
	// Table class constructor
	//
	function __construct() {
		global $ReportLanguage;

		$this->TableVar = '<!--##=gsTblVar##-->';
		$this->TableName = '<!--##=ew_SQuote(gsTblName)##-->';
		$this->TableType = '<!--##=TABLE.TblType##-->';

		$this->ExportAll = <!--##=ew_Val(bExportAll)##-->;

		$this->ExportPageBreakCount = <!--##=iExportPageBreakCount##-->;

<!--##
	for (var i = 0; i < nAllFldCount; i++) {
		if (GetFldObj(arAllFlds[i])) {
			if (goFld.FldFmtType == "Date/Time") {
				lFldDateTimeFormat = goFld.FldDtFormat;
			} else {
				lFldDateTimeFormat = "-1";
			}
			sFldDateFilter = goFld.FldDateSearch;
##-->
		// <!--##=gsFldName##-->
		$this-><!--##=gsFldParm##--> = new crField('<!--##=gsTblVar##-->', '<!--##=ew_SQuote(gsTblName)##-->', '<!--##=gsFldVar##-->', '<!--##=ew_SQuote(gsFldName)##-->', '<!--##=ew_SQuote(gsFld)##-->', <!--##=goFld.FldType##-->, <!--##=GetFieldTypeName(goFld.FldType)##-->, <!--##=lFldDateTimeFormat##-->);
	<!--## if (TABLE.TblReportType == "crosstab" && goFld.FldRowID > 0) { ##-->
		$this-><!--##=gsFldParm##-->->GroupingFieldId = <!--##=goFld.FldRowID##-->;
	<!--## } else if (TABLE.TblReportType == "summary" && goFld.FldGroupBy > 0) { ##-->
		$this-><!--##=gsFldParm##-->->GroupingFieldId = <!--##=goFld.FldGroupBy##-->;
	<!--## } ##-->
	<!--## if (goFld.FldViewTag == "IMAGE" && !ew_IsBinaryField(goFld)) { ##-->
		<!--## if (ew_IsNotEmpty(goFld.FldUploadPath)) { ##-->
		$this-><!--##=gsFldParm##-->->UploadPath = <!--##=goFld.FldUploadPath##-->;
		<!--## } else { ##-->
		$this-><!--##=gsFldParm##-->->UploadPath = EWR_UPLOAD_DEST_PATH;
		<!--## } ##-->
	<!--## } ##-->
	<!--## if (goFld.FldViewThumbnail) { ##-->
		$this-><!--##=gsFldParm##-->->ImageResize = TRUE;
	<!--## } ##-->
	<!--## if (ew_IsNotEmpty(goFld.FldValidate)) { ##-->
		$this-><!--##=gsFldParm##-->->FldDefaultErrMsg = <!--##=SYSTEMFUNCTIONS.PhpDefaultMsg()##-->;
	<!--## } ##-->
		$this->fields['<!--##=ew_SQuote(gsFldParm)##-->'] = &$this-><!--##=gsFldParm##-->;
		$this-><!--##=gsFldParm##-->->DateFilter = "<!--##=ew_Quote(sFldDateFilter)##-->";
<!--##
			bGenFilter = IsPopupFilter(goFld); // Generate popup filter
			if (TABLE.TblReportType == "summary" && FIELD.FldGroupBy > 0) {
				sGrpFld = ew_DbGrpSql(goFld.FldGroupByType, goFld.FldGroupByInterval);
			} else {
				sGrpFld = "";
				// Handle crosstab year filter
				if (TABLE.TblReportType == "crosstab" && goFld.FldName == sColFldName && sColFldDateType == "y")
					gsFld = ew_DbGrpSql("y",0).replace(/%s/g, gsFld);
			}
			if (bGenFilter || ew_IsDbGrpFld(goFld.FldGroupByType)) {
				sGrpFld = sGrpFld.replace(/%s/g, gsFld);
				sOrderByFld = gsFld;
				if (ew_IsNotEmpty(sGrpFld)) {
					sOrderByFld = sGrpFld.replace(/%s/g, gsFld);
				}
				if (ew_IsNotEmpty(goFld.FldTagLnkOrderBy))  sOrderByFld += " " + goFld.FldTagLnkOrderBy;
				if (ew_IsNotEmpty(sGrpFld)) sGrpFld = ", " + sGrpFld + " AS " + ew_QuotedName("ew_report_groupvalue");
##-->
		$this-><!--##=gsFldParm##-->->SqlSelect = "SELECT DISTINCT <!--##=ew_Quote2(gsFld)##--><!--##=ew_Quote2(sGrpFld)##--> FROM " . $this->getSqlFrom();
		$this-><!--##=gsFldParm##-->->SqlOrderBy = "<!--##=ew_Quote2(sOrderByFld)##-->";
<!--##
			} else {
##-->
		$this-><!--##=gsFldParm##-->->SqlSelect = "";
		$this-><!--##=gsFldParm##-->->SqlOrderBy = "";
<!--##
			}

			if (goFld.FldSearchMultiValue) {
##-->
		$this-><!--##=gsFldParm##-->->FldDelimiter = $GLOBALS["EWR_CSV_DELIMITER"];
<!--##
			}

			if (TABLE.TblReportType == "summary" && goFld.FldGroupBy > 0) {
##-->
		$this-><!--##=gsFldParm##-->->FldGroupByType = "<!--##=goFld.FldGroupByType##-->";
		$this-><!--##=gsFldParm##-->->FldGroupInt = "<!--##=goFld.FldGroupByInterval##-->";
		$this-><!--##=gsFldParm##-->->FldGroupSql = "<!--##=ew_DbGrpSql(goFld.FldGroupByType, goFld.FldGroupByInterval).replace("\\", "\\\\")##-->";
<!--##
			}

			sFilterName = FIELD.FldFilterName;
			var arOption = [];
			var nFilters = 0;
			if (ew_IsNotEmpty(sFilterName)) {
				arFilter = sFilterName.split(",");
				for (var j = 0; j < arFilter.length; j++) {
					sFilter = ew_UnQuote(arFilter[j]);
					nFilterOptions = FILTERS.OptionCount(sFilter);
					for (var k = 1; k <= nFilterOptions; k++) {
						nFilters += 1;
						var option = [];
						option[0] = FILTERS.OptionName(sFilter, k);
						option[1] = FILTERS.Expression(sFilter, k);
						arOption[arOption.length] = option;
					}
				}
			}
			if (nFilters <= 0) {
			} else {
				for (var j = 0; j < nFilters; j++) {
##-->
		ewr_RegisterFilter($this-><!--##=gsFldParm##-->, "@@<!--##=ew_Quote(arOption[j][0])##-->", $ReportLanguage->Phrase("<!--##=ew_Quote(arOption[j][0])##-->"), "<!--##=ew_Quote(arOption[j][1])##-->");
<!--##
				}
			}

			var sDrillDownUrl = ew_FieldDrillDownUrl(goFld);
			if (sDrillDownUrl != "\"\"") {
##-->
		$this-><!--##=gsFldParm##-->->DrillDownUrl = <!--##=sDrillDownUrl##-->;
<!--##
			}

		}
	}

	if (TABLE.TblReportType == "crosstab") { // Crosstab report
		if (sColFldDateType == "q" || sColFldDateType == "m") {
##-->
		// <!--##=sColDateFldName##-->
		$this-><!--##=sColDateFldParm##--> = new crField('<!--##=gsTblVar##-->', '<!--##=ew_SQuote(gsTblName)##-->', '<!--##=sColDateFldVar##-->', '<!--##=ew_SQuote(sColDateFldName)##-->', '<!--##=ew_SQuote(sColDateFld)##-->', <!--##=sColDateFldType##-->, <!--##=GetFieldTypeName(sColDateFldType)##-->, 0, FALSE);
		$this->fields['<!--##=ew_SQuote(sColDateFldParm)##-->'] = &$this-><!--##=sColDateFldParm##-->;
		$this-><!--##=sColDateFldParm##-->->SqlSelect = "SELECT DISTINCT <!--##=ew_Quote2(sColDateFld)##--> FROM " . $this->getSqlFrom();
		$this-><!--##=sColDateFldParm##-->->SqlOrderBy = "<!--##=ew_Quote2(sColDateFld)##-->";
<!--##
			var sDrillDownUrl = ew_FieldDrillDownUrl(COLFIELD);
			if (sDrillDownUrl != "\"\"") {
##-->
		$this-><!--##=sColDateFldParm##-->->DrillDownUrl = <!--##=sDrillDownUrl##-->;
<!--##
			}
		}
	}
##-->

<!--##
	// Generate charts definition
	for (var i = 0, len = arAllCharts.length; i < len; i++) {
		if (GetChtObj(arAllCharts[i])) {
##-->
<!--##include rpt-chartcommon.php/chart_common##-->
<!--##
			if (IsShowChart(goCht)) {
				if (iChartSortType == 5) {
					sChartXFldSqlOrderBy = sChartXFldSql;
				} else if (ew_IsNotEmpty(sChartXFldSqlOrder)) {
					sChartXFldSqlOrderBy = sChartXFldSql + " " + sChartXFldSqlOrder;
				} else {
					sChartXFldSqlOrderBy = "";
				}
				if (ew_IsNotEmpty(sChartSFldSqlOrder)) {
					sChartSFldSqlOrderBy = sChartSFldSql + " " + sChartSFldSqlOrder;
				} else {
					sChartSFldSqlOrderBy = "";
				}
				if (ew_IsNotEmpty(sChartSFldSql)) {
					if (ew_IsNotEmpty(sChartXFldSqlOrderBy) || ew_IsNotEmpty(sChartSFldSqlOrderBy)) {
						if (ew_IsEmpty(sChartXFldSqlOrderBy)) sChartXFldSqlOrderBy = sChartXFldSql;
						if (ew_IsEmpty(sChartSFldSqlOrderBy)) sChartSFldSqlOrderBy = sChartSFldSql;
						sChartXFldSqlOrderBy += ", " + sChartSFldSqlOrderBy;
					}
				}
##-->
		// <!--##=gsChartName##-->
		$this-><!--##=gsChartVar##--> = new crChart('<!--##=gsTblVar##-->', '<!--##=ew_SQuote(gsTblName)##-->', '<!--##=gsChartVar##-->', '<!--##=ew_SQuote(gsChartName)##-->', '<!--##=ew_SQuote(sChartXFldName)##-->', '<!--##=ew_SQuote(sChartYFldName)##-->', '<!--##=ew_SQuote(sChartSFldName)##-->', <!--##=iChartType##-->, '<!--##=sChartSummaryType##-->', <!--##=iChartWidth##-->, <!--##=iChartHeight##-->);
<!--## if (bChartUseGridComponent) { ##-->
		$this-><!--##=gsChartVar##-->->UseGridComponent = TRUE;
		$this-><!--##=gsChartVar##-->->ChartGridHeight = <!--##=iChartGridHeight##-->;
<!--## } ##-->
<!--## if (iChartType == 20) { ##-->
		$this-><!--##=gsChartVar##-->->SqlSelect = "SELECT <!--##=ew_Quote2(sChartFldSql)##--> FROM ";
		$this-><!--##=gsChartVar##-->->SqlGroupBy = "";
		$this-><!--##=gsChartVar##-->->SqlOrderBy = "<!--##=ew_Quote2(sChartFldSqlOrderBy)##-->";
		$this-><!--##=gsChartVar##-->->SeriesDateType = "";
<!--## } else if (ew_IsNotEmpty(sChartSFldSql)) { ##-->
		$this-><!--##=gsChartVar##-->->SqlSelect = "SELECT <!--##=ew_Quote2(sChartXFldSql)##-->, <!--##=ew_Quote2(sChartSFldSql)##-->, <!--##=ew_Quote2(sChartYFldSql)##--> FROM ";
		$this-><!--##=gsChartVar##-->->SqlGroupBy = "<!--##=ew_Quote2(sChartXFldSql)##-->, <!--##=ew_Quote2(sChartSFldSql)##-->";
		$this-><!--##=gsChartVar##-->->SqlOrderBy = "<!--##=ew_Quote2(sChartXFldSqlOrderBy)##-->";
		$this-><!--##=gsChartVar##-->->SeriesDateType = "<!--##=sChartFldDateType##-->";
		$this-><!--##=gsChartVar##-->->SqlSelectSeries = "SELECT DISTINCT <!--##=ew_Quote2(sChartSFldSql)##--> FROM ";
		$this-><!--##=gsChartVar##-->->SqlGroupBySeries = "<!--##=ew_Quote2(sChartSFldSql)##-->";
		$this-><!--##=gsChartVar##-->->SqlOrderBySeries = "<!--##=ew_Quote2(sChartSFldSqlOrderBy)##-->";
<!--## } else { ##-->
		$this-><!--##=gsChartVar##-->->SqlSelect = "SELECT <!--##=ew_Quote2(sChartXFldSql)##-->, '', <!--##=ew_Quote2(sChartYFldSql)##--> FROM ";
		$this-><!--##=gsChartVar##-->->SqlGroupBy = "<!--##=ew_Quote2(sChartXFldSql)##-->";
		$this-><!--##=gsChartVar##-->->SqlOrderBy = "<!--##=ew_Quote2(sChartXFldSqlOrderBy)##-->";
		$this-><!--##=gsChartVar##-->->SeriesDateType = "<!--##=sChartFldDateType##-->";
<!--## } ##-->
<!--## if (ew_IsNotEmpty(sXAxisDateFormat)) { ##-->
		$this-><!--##=gsChartVar##-->->XAxisDateFormat = <!--##=sXAxisDateFormat##-->;
<!--## } ##-->
<!--## if (ew_IsNotEmpty(sNameDateFormat)) { ##-->
		$this-><!--##=gsChartVar##-->->NameDateFormat = <!--##=sNameDateFormat##-->;
<!--## } ##-->
<!--##
				var sDrillDownUrl = ew_ChartDrillDownUrl(goCht);
				if (sDrillDownUrl != "\"\"") {
##-->
		$this-><!--##=gsChartVar##-->->ChartDrillDownUrl = <!--##=sDrillDownUrl##-->;
<!--##
				}
				if (iChartYDefaultDecimalPrecision > -1) {
##-->
		$this-><!--##=gsChartVar##-->->ChartDefaultDecimalPrecision = <!--##=iChartYDefaultDecimalPrecision##-->;
<!--##
				}
			}
		}
	}
##-->
	}

<!--## if (iSortType == 2) { ##-->
	// Multiple column sort
	function UpdateSort(&$ofld, $ctrl) {
<!--## } else { ##-->
	// Single column sort
	function UpdateSort(&$ofld) {
<!--## } ##-->
		if ($this->CurrentOrder == $ofld->FldName) {
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
		} else {
<!--## if (iSortType == 2) { ##-->
			if ($ofld->GroupingFieldId == 0 && !$ctrl) $ofld->setSort("");
<!--## } else { ##-->
			if ($ofld->GroupingFieldId == 0) $ofld->setSort("");
<!--## } ##-->
		}
	}

	// Get Sort SQL
	function SortSql() {
		$sDtlSortSql = "";
		$argrps = array();
		foreach ($this->fields as $fld) {
			if ($fld->getSort() <> "") {
				if ($fld->GroupingFieldId > 0) {
					if ($fld->FldGroupSql <> "")
						$argrps[$fld->GroupingFieldId] = str_replace("%s", $fld->FldExpression, $fld->FldGroupSql) . " " . $fld->getSort();
					else
						$argrps[$fld->GroupingFieldId] = $fld->FldExpression . " " . $fld->getSort();
				} else {
					if ($sDtlSortSql <> "") $sDtlSortSql .= ", ";
					$sDtlSortSql .= $fld->FldExpression . " " . $fld->getSort();
				}
			}
		}
		$sSortSql = "";
		foreach ($argrps as $grp) {
			if ($sSortSql <> "") $sSortSql .= ", ";
			$sSortSql .= $grp;
		}
		if ($sDtlSortSql <> "") {
			if ($sSortSql <> "") $sSortSql .= ",";
			$sSortSql .= $sDtlSortSql;
		}
		return $sSortSql;
	}

<!--##
	// Crosstab report
	if (TABLE.TblReportType == "crosstab") {
##-->
	// Table level SQL

	// Column field
	var $ColumnField = "";
	function getColumnField() {
		return ($this->ColumnField <> "") ? $this->ColumnField : "<!--##=ew_Quote2(sColFld)##-->";
	}
	function setColumnField($v) {
		$this->ColumnField = $v;
	}

	// Column date type
	var $ColumnDateType = "";
	function getColumnDateType() {
		return ($this->ColumnDateType <> "") ? $this->ColumnDateType : "<!--##=sColFldDateType##-->";
	}
	function setColumnDateType($v) {
		$this->ColumnDateType = $v;
	}

	// Summary field
	var $SummaryField = "";
	function getSummaryField() {
		return ($this->SummaryField <> "") ? $this->SummaryField : "<!--##=ew_Quote2(sSmryFld)##-->";
	}
	function setSummaryField($v) {
		$this->SummaryField = $v;
	}

	// Summary type
	var $SummaryType = "";
	function getSummaryType() {
		return ($this->SummaryType <> "") ? $this->SummaryType : "<!--##=sSmryType##-->";
	}
	function setSummaryType($v) {
		$this->SummaryType = $v;
	}

	// Column captions
	var $ColumnCaptions = "";
	function getColumnCaptions() {
		global $ReportLanguage;
		return ($this->ColumnCaptions <> "") ? $this->ColumnCaptions : <!--##=sColumnCaptions##-->;
	}
	function setColumnCaptions($v) {
		$this->ColumnCaptions = $v;
	}

	// Column names
	var $ColumnNames = "";
	function getColumnNames() {
		return ($this->ColumnNames <> "") ? $this->ColumnNames : "<!--##=sColumnNames##-->";
	}
	function setColumnNames($v) {
		$this->ColumnNames = $v;
	}

	// Column values
	var $ColumnValues = "";
	function getColumnValues() {
		return ($this->ColumnValues <> "") ? $this->ColumnValues : "<!--##=sColumnValues##-->";
	}
	function setColumnValues($v) {
		$this->ColumnValues = $v;
	}

	// From
	var $_SqlFrom = "";
	function getSqlFrom() {
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "<!--##=ew_Quote2(sFrom)##-->";
	}
	function SqlFrom() { // For backward compatibility
		return $this->getSqlFrom();
	}
	function setSqlFrom($v) {
		$this->_SqlFrom = $v;
	}

	// Select
	var $_SqlSelect = "";
	function getSqlSelect() {
		return ($this->_SqlSelect <> "") ? $this->_SqlSelect : "SELECT <!--##=ew_Quote2(sSelect)##--> FROM " . $this->getSqlFrom();
	}
	function SqlSelect() { // For backward compatibility
		return $this->getSqlSelect();
	}
	function setSqlSelect($v) {
		$this->_SqlSelect = $v;
	}

<!--##
	sWhere = sWhere.trim();
	sFilter = sSrcFilter.trim();
##-->

	// Where
	var $_SqlWhere = "";
	function getSqlWhere() {
		$sWhere = ($this->_SqlWhere <> "") ? $this->_SqlWhere : <!--##=sWhere##-->;
<!--## if (ew_IsNotEmpty(sFilter)) { ##-->
		$sFilter = <!--##=sFilter##-->;
		ewr_AddFilter($sWhere, $sFilter);
<!--## } ##-->
		return $sWhere;
	}
	function SqlWhere() { // For backward compatibility
		return $this->getSqlWhere();
	}
	function setSqlWhere($v) {
		$this->_SqlWhere = $v;
	}

	// Group By
	var $_SqlGroupBy = "";
	function getSqlGroupBy() {
		return ($this->_SqlGroupBy <> "") ? $this->_SqlGroupBy : "<!--##=ew_Quote2(sGroupBy)##-->";
	}
	function SqlGroupBy() { // For backward compatibility
		return $this->getSqlGroupBy();
	}
	function setSqlGroupBy($v) {
		$this->_SqlGroupBy = $v;
	}

	// Having
	var $_SqlHaving = "";
	function getSqlHaving() {
		return ($this->_SqlHaving <> "") ? $this->_SqlHaving : "";
	}
	function SqlHaving() { // For backward compatibility
		return $this->getSqlHaving();
	}
	function setSqlHaving($v) {
		$this->_SqlHaving = $v;
	}

	// Order By
	var $_SqlOrderBy = "";
	function getSqlOrderBy() {
		return ($this->_SqlOrderBy <> "") ? $this->_SqlOrderBy : "<!--##=ew_Quote2(sOrderBy)##-->";
	}
	function SqlOrderBy() { // For backward compatibility
		return $this->getSqlOrderBy();
	}
	function setSqlOrderBy($v) {
		$this->_SqlOrderBy = $v;
	}

<!--## if (ew_IsNotEmpty(sDistinctSelect)) { ##-->

	// Select Distinct
	var $_SqlDistinctSelect = "";
	function getSqlDistinctSelect() {
		return ($this->_SqlDistinctSelect <> "") ? $this->_SqlDistinctSelect : "SELECT DISTINCT <!--##=ew_Quote2(sDistinctSelect)##--> FROM <!--##=ew_Quote2(sFrom)##-->";
	}
	function SqlDistinctSelect() { // For backward compatibility
		return $this->getSqlDistinctSelect();
	}
	function setSqlDistinctSelect($v) {
		$this->_SqlDistinctSelect = $v;
	}

<!--##
	sDistinctSqlWhere = sDistinctSqlWhere.trim();
	sFilter = sSrcFilter.trim();
##-->

	// Distinct Where
	var $_SqlDistinctWhere = "";
	function getSqlDistinctWhere() {
		$sWhere = ($this->_SqlDistinctWhere <> "") ? $this->_SqlDistinctWhere : <!--##=sDistinctSqlWhere##-->;
<!--## if (ew_IsNotEmpty(sFilter)) { ##-->
		$sFilter = <!--##=sFilter##-->;
		ewr_AddFilter($sWhere, $sFilter);
<!--## } ##-->
		return $sWhere;
	}
	function SqlDistinctWhere() { // For backward compatibility
		return $this->getSqlDistinctWhere();
	}
	function setSqlDistinctWhere($v) {
		$this->_SqlDistinctWhere = $v;
	}

	// Distinct Order By
	var $_SqlDistinctOrderBy = "";
	function getSqlDistinctOrderBy() {
		return ($this->_SqlDistinctOrderBy <> "") ? $this->_SqlDistinctOrderBy : "<!--##=ew_Quote2(sDistinctOrderBy)##-->";
	}
	function SqlDistinctOrderBy() { // For backward compatibility
		return $this->getSqlDistinctOrderBy();
	}
	function setSqlDistinctOrderBy($v) {
		$this->_SqlDistinctOrderBy = $v;
	}

<!--## } ##-->

<!--##
	if (bColFldDateSelect && ew_IsNotEmpty(sColDateFldName)) {
		sYearSql = SYSTEMFUNCTIONS.CrosstabYearSql();
##-->

	// Crosstab Year
	var $_SqlCrosstabYear = "";
	function getSqlCrosstabYear() {
		return ($this->_SqlCrosstabYear <> "") ? $this->_SqlCrosstabYear : "<!--##=ew_Quote2(sYearSql)##-->";
	}
	function SqlCrosstabYear() { // For backward compatibility
		return $this->getSqlCrosstabYear();
	}
	function setSqlCrosstabYear($v) {
		$this->_SqlCrosstabYear = $v;
	}

<!--##
	}
##-->

	var $ColCount;
	var $Col;
	var $DistinctColumnFields = "";

	// Load column values
	function LoadColumnValues($filter = "") {

		global $conn;
		global $ReportLanguage;

	<!--##
		if (sColFldDateType == "q" || sColFldDateType == "m") {
	##-->
		$arColumnCaptions = explode(",", $this->getColumnCaptions());
		$arColumnNames = explode(",", $this->getColumnNames());
		$arColumnValues = explode(",", $this->getColumnValues());

		// Get distinct column count
		$this->ColCount = count($arColumnNames);

	<!--##
		} else {
	##-->
		// Build SQL
		$sSql = ewr_BuildReportSql($this->getSqlDistinctSelect(), $this->getSqlDistinctWhere(), "", "", $this->getSqlDistinctOrderBy(), $filter, "");

		// Load recordset
		$rscol = $conn->Execute($sSql);

		// Get distinct column count
		$this->ColCount = ($rscol) ? $rscol->RecordCount() : 0;
/* Uncomment to show phrase
		if ($this->ColCount == 0) {
			if ($rscol) $rscol->Close();
			echo "<p>" . $ReportLanguage->Phrase("NoDistinctColVals") . $sSql . "</p>";
			exit();
		}
*/
	<!--##
		};
	##-->

		$this->Col = &ewr_Init2DArray($this->ColCount+1, <!--##=nGrps+1##-->, NULL);

	<!--## if (sColFldDateType == "q" || sColFldDateType == "m") { ##-->

		for ($colcnt = 1; $colcnt <= $this->ColCount; $colcnt++) {
			$this->Col[$colcnt] = new crCrosstabColumn($arColumnValues[$colcnt-1], $arColumnCaptions[$colcnt-1], TRUE);
		}

	<!--## } else { ##-->

		$colcnt = 0;
		while (!$rscol->EOF) {
			if (is_null($rscol->fields[0])) {
				$wrkValue = EWR_NULL_VALUE;
				$wrkCaption = $ReportLanguage->Phrase("NullLabel");
			} elseif ($rscol->fields[0] == "") {
				$wrkValue = EWR_EMPTY_VALUE;
				$wrkCaption = $ReportLanguage->Phrase("EmptyLabel");
			} else {
				$wrkValue = $rscol->fields[0];
				$wrkCaption = $rscol->fields[0];
			}
			$colcnt++;
			$this->Col[$colcnt] = new crCrosstabColumn($wrkValue, $wrkCaption, TRUE);
			$rscol->MoveNext();
		}
		$rscol->Close();

	<!--## } ##-->

	<!--## if (sColFldDateType == "q" || sColFldDateType == "m") { ##-->

		// Update crosstab sql
		$sSqlFlds = "";
		for ($i = 0; $i < $this->ColCount; $i++) {
			$sFld = ewr_CrossTabField($this->getSummaryType(), $this->getSummaryField(),
				$this->getColumnField(), $this->getColumnDateType(), $arColumnValues[$i], "", $arColumnNames[$i]);
			if ($sSqlFlds <> "")
				$sSqlFlds .= ", ";
			$sSqlFlds .= $sFld;
		}

	<!--## } else { ##-->

		// Update crosstab sql
		$sSqlFlds = "";
		for ($colcnt = 1; $colcnt <= $this->ColCount; $colcnt++) {
			$sFld = ewr_CrossTabField($this->getSummaryType(), $this->getSummaryField(), $this->getColumnField(), $this->getColumnDateType(), $this->Col[$colcnt]->Value, "<!--##=sColFldQc##-->", "C" . $colcnt);
			if ($sSqlFlds <> "")
				$sSqlFlds .= ", ";
			$sSqlFlds .= $sFld;
		}

	<!--## } ##-->

		$this->DistinctColumnFields = $sSqlFlds;

	}

	// Get chart sql
	function GetChartColumnSql() {

		// Update chart sql if Y Axis = Column Field

	<!--## if (sColFldDateType == "q" || sColFldDateType == "m") { ##-->
		$arColumnValues = explode(",", $this->getColumnValues());
	<!--## } ##-->

		$SqlChartWork = "";
		for ($i = 0; $i < $this->ColCount; $i++) {
			if ($this->Col[$i+1]->Visible) {

	<!--## if (sColFldDateType == "q" || sColFldDateType == "m") { ##-->

				$sChtFld = ewr_CrossTabField("SUM", $this->getSummaryField(), $this->getColumnField(), $this->getColumnDateType(), $arColumnValues[$i], "");

	<!--## } else { ##-->

				$sChtFld = ewr_CrossTabField("SUM", $this->getSummaryField(), $this->getColumnField(), $this->getColumnDateType(), $this->Col[$i+1]->Value, "<!--##=sColFldQc##-->");

	<!--## } ##-->

				if ($SqlChartWork != "") $SqlChartWork .= "+";
				$SqlChartWork .= $sChtFld;
			}
		}
		if ($SqlChartWork == "") $SqlChartWork = "0";
		return $SqlChartWork;

	}

<!--##
	} else { // Summary/simple report
##-->

	// Table level SQL

	// From
	var $_SqlFrom = "";
	function getSqlFrom() {
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "<!--##=ew_Quote2(sFrom)##-->";
	}
	function SqlFrom() { // For backward compatibility
		return $this->getSqlFrom();
	}
	function setSqlFrom($v) {
		$this->_SqlFrom = $v;
	}

	// Select
	var $_SqlSelect = "";
	function getSqlSelect() {
		return ($this->_SqlSelect <> "") ? $this->_SqlSelect : "SELECT <!--##=ew_Quote2(sSelect)##--> FROM " . $this->getSqlFrom();
	}
	function SqlSelect() { // For backward compatibility
		return $this->getSqlSelect();
	}
	function setSqlSelect($v) {
		$this->_SqlSelect = $v;
	}

<!--##
	sWhere = sWhere.trim();
	sFilter = sSrcFilter.trim();
##-->

	// Where
	var $_SqlWhere = "";
	function getSqlWhere() {
		$sWhere = ($this->_SqlWhere <> "") ? $this->_SqlWhere : <!--##=sWhere##-->;
<!--## if (ew_IsNotEmpty(sFilter)) { ##-->
		$sFilter = <!--##=sFilter##-->;
		ewr_AddFilter($sWhere, $sFilter);
<!--## } ##-->
		return $sWhere;
	}
	function SqlWhere() { // For backward compatibility
		return $this->getSqlWhere();
	}
	function setSqlWhere($v) {
		$this->_SqlWhere = $v;
	}

	// Group By
	var $_SqlGroupBy = "";
	function getSqlGroupBy() {
		return ($this->_SqlGroupBy <> "") ? $this->_SqlGroupBy : "<!--##=ew_Quote2(sGroupBy)##-->";
	}
	function SqlGroupBy() { // For backward compatibility
		return $this->getSqlGroupBy();
	}
	function setSqlGroupBy($v) {
		$this->_SqlGroupBy = $v;
	}

	// Having
	var $_SqlHaving = "";
	function getSqlHaving() {
		return ($this->_SqlHaving <> "") ? $this->_SqlHaving : "<!--##=ew_Quote2(sHaving)##-->";
	}
	function SqlHaving() { // For backward compatibility
		return $this->getSqlHaving();
	}
	function setSqlHaving($v) {
		$this->_SqlHaving = $v;
	}

	// Order By
	var $_SqlOrderBy = "";
	function getSqlOrderBy() {
		return ($this->_SqlOrderBy <> "") ? $this->_SqlOrderBy : "<!--##=ew_Quote2(sOrderBy)##-->";
	}
	function SqlOrderBy() { // For backward compatibility
		return $this->getSqlOrderBy();
	}
	function setSqlOrderBy($v) {
		$this->_SqlOrderBy = $v;
	}

<!--##
	}
##-->

<!--## if (TABLE.TblReportType == "crosstab" || TABLE.TblReportType == "summary") { ##-->

	// Table Level Group SQL

	// First Group Field
	var $_SqlFirstGroupField = "";
	function getSqlFirstGroupField() {
		return ($this->_SqlFirstGroupField <> "") ? $this->_SqlFirstGroupField : "<!--##=ew_Quote2(sFirstGroupFldSql)##-->";
	}
	function SqlFirstGroupField() { // For backward compatibility
		return $this->getSqlFirstGroupField();
	}
	function setSqlFirstGroupField($v) {
		$this->_SqlFirstGroupField = $v;
	}

	// Select Group
	var $_SqlSelectGroup = "";
	function getSqlSelectGroup() {
		return ($this->_SqlSelectGroup <> "") ? $this->_SqlSelectGroup : "SELECT DISTINCT " . $this->getSqlFirstGroupField() . " FROM " . $this->getSqlFrom();
	}
	function SqlSelectGroup() { // For backward compatibility
		return $this->getSqlSelectGroup();
	}
	function setSqlSelectGroup($v) {
		$this->_SqlSelectGroup = $v;
	}

	// Order By Group
	var $_SqlOrderByGroup = "";
	function getSqlOrderByGroup() {
		return ($this->_SqlOrderByGroup <> "") ? $this->_SqlOrderByGroup : "<!--##=ew_Quote2((sFirstGroupFldSql + " " + sFirstGroupFldOrderType).trim())##-->";
	}
	function SqlOrderByGroup() { // For backward compatibility
		return $this->getSqlOrderByGroup();
	}
	function setSqlOrderByGroup($v) {
		$this->_SqlOrderByGroup = $v;
	}

<!--## } ##-->

<!--## if (TABLE.TblReportType == "crosstab") { ##-->

	// Select Aggregate
	var $_SqlSelectAgg = "";
	function getSqlSelectAgg() {
		return ($this->_SqlSelectAgg <> "") ? $this->_SqlSelectAgg : "SELECT <!--##=ew_Quote2(sSelectAgg)##--> FROM " . $this->getSqlFrom();
	}
	function SqlSelectAgg() { // For backward compatibility
		return $this->getSqlSelectAgg();
	}
	function setSqlSelectAgg($v) {
		$this->_SqlSelectAgg = $v;
	}

	// Group By Aggregate
	var $_SqlGroupByAgg = "";
	function getSqlGroupByAgg() {
		return ($this->_SqlGroupByAgg <> "") ? $this->_SqlGroupByAgg : "<!--##=ew_Quote2(sGroupByAgg)##-->";
	}
	function SqlGroupByAgg() { // For backward compatibility
		return $this->getSqlGroupByAgg();
	}
	function setSqlGroupByAgg($v) {
		$this->_SqlGroupByAgg = $v;
	}

<!--## } else if (TABLE.TblReportType == "summary" || TABLE.TblReportType == "rpt") { ##-->

	// Select Aggregate
	var $_SqlSelectAgg = "";
	function getSqlSelectAgg() {
		return ($this->_SqlSelectAgg <> "") ? $this->_SqlSelectAgg : "SELECT <!--##=ew_Quote2(sAggFlds)##--> FROM " . $this->getSqlFrom();
	}
	function SqlSelectAgg() { // For backward compatibility
		return $this->getSqlSelectAgg();
	}
	function setSqlSelectAgg($v) {
		$this->_SqlSelectAgg = $v;
	}

	// Aggregate Prefix
	var $_SqlAggPfx = "";
	function getSqlAggPfx() {
		return ($this->_SqlAggPfx <> "") ? $this->_SqlAggPfx : "<!--##=ew_Quote2(sAggPfx)##-->";
	}
	function SqlAggPfx() { // For backward compatibility
		return $this->getSqlAggPfx();
	}
	function setSqlAggPfx($v) {
		$this->_SqlAggPfx = $v;
	}

	// Aggregate Suffix
	var $_SqlAggSfx = "";
	function getSqlAggSfx() {
		return ($this->_SqlAggSfx <> "") ? $this->_SqlAggSfx : "<!--##=ew_Quote2(sAggSfx)##-->";
	}
	function SqlAggSfx() { // For backward compatibility
		return $this->getSqlAggSfx();
	}
	function setSqlAggSfx($v) {
		$this->_SqlAggSfx = $v;
	}

	// Select Count
	var $_SqlSelectCount = "";
	function getSqlSelectCount() {
		return ($this->_SqlSelectCount <> "") ? $this->_SqlSelectCount : "SELECT COUNT(*) FROM " . $this->getSqlFrom();
	}
	function SqlSelectCount() { // For backward compatibility
		return $this->getSqlSelectCount();
	}
	function setSqlSelectCount($v) {
		$this->_SqlSelectCount = $v;
	}

<!--## } ##-->

	// Sort URL
	function SortUrl(&$fld) {
<!--## if (iSortType == 0) { ##-->
		return "";
<!--## } else { ##-->
		if ($this->Export <> "" ||
			<!--## if (bDBMySql || bDBPostgreSql) { ##-->
			in_array($fld->FldType, array(128, 204, 205))) { // Unsortable data type
			<!--## } else { ##-->
			in_array($fld->FldType, array(141, 201, 203, 128, 204, 205))) { // Unsortable data type
			<!--## } ##-->
				return "";
		} elseif ($fld->Sortable) {
			//$sUrlParm = "order=" . urlencode($fld->FldName) . "&ordertype=" . $fld->ReverseSort();
			$sUrlParm = "order=" . urlencode($fld->FldName) . "&amp;ordertype=" . $fld->ReverseSort();
			return ewr_CurrentPage() . "?" . $sUrlParm;
		} else {
			return "";
		}
<!--## } ##-->
	}

<!--## if (bTableHasUserIDFld) { ##-->
<!--##
	goFld = goTblFlds.Fields[TABLE.TblUserIDFld];
	sTblUserIDFldName = ew_FieldSqlName(goFld);
	sTblUserIDFldTypeName = GetFieldTypeName(goFld.FldType);
	SECTBL = DB.Tables(PROJ.SecTbl);
	if (SECTBL.TblType == "CUSTOMVIEW") {
		sFromPart = ew_SQLPart(SECTBL.TblCustomSQL, "FROM");
	} else {
		sFromPart = ew_TableName(SECTBL);
	}
	FIELD = SECTBL.Fields(DB.SecuUserIDFld);
	sUserIDFldName = ew_FieldSqlName(FIELD);
	sUserIDFldTypeName = GetFieldTypeName(FIELD.FldType);
	sUserIDQuoteS = FIELD.FldQuoteS;
	sUserIDQuoteE = FIELD.FldQuoteE;
	if (bParentUserID) {
		FIELD = SECTBL.Fields(DB.SecuParentUserIDFld);
		sParentUserIDFldName = ew_FieldSqlName(FIELD);
		sQuoteS = FIELD.FldQuoteS;
		sQuoteE = FIELD.FldQuoteE;
	}
##-->
	// User ID filter
	function GetUserIDFilter() {
		global $Security;
		$sUserID = $Security->CurrentUserID();
		$sUserIDFilter = "";
		if (!$Security->IsAdmin()) {
			$sUserIDFilter = $Security->UserIDList();
			if ($sUserIDFilter <> "")
				$sUserIDFilter = '<!--##=ew_SQuote(sTblUserIDFldName)##--> IN (' . $sUserIDFilter . ')';
	<!--## if (bParentUserID) { ##-->
			$sParentUserIDFilter = $this->GetParentUserIDQuery($sUserID);
			$sUserIDFilter = "($sUserIDFilter) OR ($sParentUserIDFilter)";
	<!--## } ##-->
		}

	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Table","UserID_Filtering")) { ##-->
		// Call Row Rendered event
		$this->UserID_Filtering($sUserIDFilter);
	<!--## } ##-->

		return $sUserIDFilter;
	}

	<!--## if (bParentUserID) { ##-->

	// Function to get the user id query for parent user
	function GetParentUserIDQuery($sUserID) {

		if ($sUserID == "-1") {
			$sWrk = "";
		} else {
			$arUser = $this->ChildUserIDList($sUserID);
			$sWrk = $this->UserIDList($arUser);
		}

		if ($sWrk <> "")
			$sWrk = "<!--##=ew_Quote2(sTblUserIDFldName)##--> IN (" . $sWrk . ")";

		return $sWrk;

	}
	<!--## } ##-->

	// Function to get the child user id list for this user
	function ChildUserIDList($sUserID) {
		global $conn;

		// Get all values
		if ($sUserID == "-1") {
			$sSql = "SELECT <!--##=ew_Quote2(sUserIDFldName)##--> FROM <!--##=ew_Quote2(sFromPart)##-->";
		} else {
	<!--## if (bParentUserID) { ##-->
			$sSql = "SELECT <!--##=ew_Quote2(sUserIDFldName)##--> FROM <!--##=ew_Quote2(sFromPart)##--> WHERE <!--##=ew_Quote2(sParentUserIDFldName)##--> = " . ewr_QuotedValue($sUserID, <!--##=sUserIDFldTypeName##-->) . " OR <!--##=ew_Quote2(sUserIDFldName)##--> = " . ewr_QuotedValue($sUserID, <!--##=sUserIDFldTypeName##-->);
	<!--## } else { ##-->
			$sSql = "SELECT <!--##=ew_Quote2(sUserIDFldName)##--> FROM <!--##=ew_Quote2(sFromPart)##--> WHERE <!--##=ew_Quote2(sUserIDFldName)##--> = " . ewr_QuotedValue($sUserID, <!--##=sUserIDFldTypeName##-->);
	<!--## } ##-->
		}

		$rs = $conn->Execute($sSql);
		$arUser = array();
		if ($rs) {
			while (!$rs->EOF) {
				$arUser[] = $rs->fields('<!--##=ew_SQuote(DB.SecuUserIDFld)##-->');
				$rs->MoveNext();
			}
			$rs->Close();
		}
		sort($arUser);

	<!--## if (bParentUserID) { ##-->
		// Recurse all levels (hierarchical user id)
		if (EWR_USER_ID_IS_HIERARCHICAL) {
			$sCurUserIDList = $this->UserIDList($arUser);
			$sUserIDList = "";
			while ($sUserIDList <> $sCurUserIDList) {
				$arUserWrk = array();
				$sSql = "SELECT <!--##=ew_Quote2(sUserIDFldName)##--> FROM <!--##=ew_Quote2(sFromPart)##--> WHERE <!--##=ew_Quote2(sParentUserIDFldName)##--> IN (" . $sCurUserIDList . ") OR <!--##=ew_Quote2(sUserIDFldName)##--> = " . ewr_QuotedValue($sUserID, <!--##=sUserIDFldTypeName##-->);
				if ($rs = $conn->Execute($sSql)) {
					while (!$rs->EOF) {
						$arUserWrk[] = $rs->fields('<!--##=ew_SQuote(DB.SecuUserIDFld)##-->');
						$rs->MoveNext();
					}
					$rs->Close();
				}
				sort($arUserWrk);
				$sUserIDList = $sCurUserIDList;
				$sCurUserIDList = $this->UserIDList($arUserWrk);
			}
			$arUser = $arUserWrk;
		}
	<!--## } ##-->

		return $arUser;
	}

	function UserIDList($ar) {
		$sWrk = "";
		if (is_array($ar)) {
			$cntar = count($ar);
			for ($i = 0; $i < $cntar; $i++) {
				if ($sWrk <> "")
					$sWrk .= ", ";
				$sWrk .= ewr_QuotedValue($ar[$i], <!--##=sUserIDFldTypeName##-->);
			}
		}
		return $sWrk;
	}

<!--## } ##-->

<!--##
	} // Non-dashboard reports
##-->

	// Table level events
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Table","Page_Selecting")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Table","Page_Breaking")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Table","Row_Rendering")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Table","Cell_Rendered")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Table","Row_Rendered")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Table","UserID_Filtering")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Table","Page_FilterLoad")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Table","Page_FilterValidated")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Table","Page_Filtering")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Table","Email_Sending")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Table","Lookup_Selecting")##-->

}
?>
<!--##/session##-->