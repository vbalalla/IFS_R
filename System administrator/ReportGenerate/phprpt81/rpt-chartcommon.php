<!--##session chart_save##-->
<!--##
	// Save current chart name
	var sCurrentChartName = goCht.ChartName;
##-->
<!--##/session##-->


<!--##session chart_load##-->
<!--##
	// Restore chart object
	GetChtObj(sCurrentChartName);
##-->
<!--##/session##-->


<!--##session chart_common##-->
<!--##
	var sChartTblVar = gsTblVar;
	var sChartChtVar = gsChartVar;
	var sChartClickUrl = "#";
	var sChartClickCaption = "";
	var sChartDivClass = "";
	var bDashboardChartHasUserIDFld = false;
	var curCht = goCht;
	var HasSourceReport = false;
	if (IsDashBoard) {
		var iDashboardChartWidth = 550;
		var iDashboardChartHeight = 440;
		if (TABLE.TblDashboardType == "" || TABLE.TblDashboardType == "vertical")
			sChartDivClass = "ewDashboardChartVertical";
		else if (TABLE.TblDashboardType == "horizontal")
			sChartDivClass = "ewDashboardChartHorizontal";
		else if (TABLE.TblDashboardType == "custom")
			sChartDivClass = "ewDashboardChartCustom";
		var CURRENTABLE = TABLE; // Save current table
		if (DB.Tables.TableExist(goCht.ChartSourceTable) && DB.Tables(goCht.ChartSourceTable).Charts.ChartExist(goCht.ChartSourceChart)) {
			iDashboardChartWidth = parseInt(goCht.ChartWidth);
			if (iDashboardChartWidth <= 0) iDashboardChartWidth = 550;
			iDashboardChartHeight = parseInt(goCht.ChartHeight);
			if (iDashboardChartHeight <= 0) iDashboardChartHeight = 440;
			var sChartSourceTable = goCht.ChartSourceTable;
			var sChartSourceChart = goCht.ChartSourceChart;
			TABLE = DB.Tables(sChartSourceTable);
			HasSourceReport = TABLE.TblGen;
			bDashboardChartHasUserIDFld = (bUserID && ew_IsNotEmpty(TABLE.TblUserIDFld));
			sChartTblVar = TABLE.TblVar;
			sChartClickCaption = "<?php echo $ReportLanguage->TablePhrase(\"" + ew_Quote(sChartTblVar) + "\", \"TblCaption\") ?>";
			if (TABLE.TblType == "REPORT") {
				sChartClickUrl = ew_GetFileNameByCtrlID(TABLE.TblReportType);
				SRCTABLE = DB.Tables(TABLE.TblRptSrc);
				bCustomViewSrcTable = (SRCTABLE.TblType == "CUSTOMVIEW" && TABLE.TblReportType == "summary");
			} else {
				sChartClickUrl = ew_GetFileNameByCtrlID("rpt");
				bCustomViewSrcTable = (TABLE.TblType == "CUSTOMVIEW");
			}
			ew_LoadCurrentCharts();
			ew_LoadCurrentFields();
			goChts = goTblChts.Charts;
			GetChtObj(sChartSourceChart);
			sChartChtVar = goCht.ChartVar;
		}
	}
	var IsCrosstabChart = (TABLE.TblReportType == "crosstab");
	var sChartDivName = sChartTblVar + "_" + sChartChtVar;
	var sChartId = "cht_" + sChartDivName;
	if (PROJ.OutputNameLCase) sChartId = sChartId.toLowerCase();

	// Current chart object = goCht
	var sChartXFldName = goCht.ChartXFldName; // Chart X-axis Field Name
	var sChartYFldNameList = goCht.ChartYFldName; // Chart Y-axis Field Names (separated by \r\n)
	var sChartXFldSql;
	var arChartYFlds, nChartYFlds, sChartYFldName, sChartYFldSql;
	if (ew_IsNotEmpty(sChartYFldNameList)) {
		if (sChartYFldNameList.substr(sChartYFldNameList.length-2) == "\r\n") sChartYFldNameList = sChartYFldNameList.substr(0,sChartYFldNameList.length-2);
		arChartYFlds = sChartYFldNameList.split("\r\n");
		nChartYFlds = arChartYFlds.length;
		sChartYFldName = ew_UnQuote(arChartYFlds[0]).replace(/\"\"/g, "\"");
	} else {
		nChartYFlds = 0;
		sChartYFldName = "";
	}
	var sChartYAxisList = goCht.ChartSeriesYAxis; // Chart Y-axis (comma separated)
	var arChartYAxis, nChartYAxis, sChartYAxis;
	if (ew_IsNotEmpty(sChartYAxisList)) {
		if (sChartYAxisList.substr(sChartYAxisList.length-1) == ",") sChartYAxisList = sChartYAxisList.substr(0,sChartYAxisList.length-1);
		arChartYAxis = sChartYAxisList.split(",");
		nChartYAxis = arChartYAxis.length;
		sChartYAxis = arChartYAxis[0];
	} else {
		nChartYAxis = 0;
		sChartYAxis = "1";
	}
	var sChartNFldName = goCht.ChartNameFldName; // Chart name field (Candlestick only)
	var sChartSFldName = goCht.ChartSeriesFldName; // Chart Series Field Name
	var sChartSFldSqlOrder = goCht.ChartSeriesFldOrder; // Series Field Order
	var sChartSFldSql = "";

	// Chart variables
	var sChartName = gsChartName;
	var sChartVar = gsChartVar;
	var sChartObj = gsChartObj;
	var iChartType = goCht.ChartType;

	if (IsShowChart(goCht)) {

		// Chart parms
		var nChartParms = 0;

		// Chart type
		nChartParms += 1;

		// Chart series type
		var iChartSeriesType = goCht.ChartSeriesType;
		var sChartSummaryTypeList = goCht.ChartSummaryType;
		var arChartSummaryType, nChartSummaryType, sChartSummaryType;
		if (ew_IsNotEmpty(sChartSummaryTypeList)) {
			if (sChartSummaryTypeList.substr(sChartSummaryTypeList.length-1) == ",") sChartSummaryTypeList = sChartSummaryTypeList.substr(0,sChartSummaryTypeList.length-1);
			arChartSummaryType = sChartSummaryTypeList.split(",");
			nChartSummaryType = arChartSummaryType.length;
			sChartSummaryType = arChartSummaryType[0];
		} else {
			nChartSummaryType = 0;
			sChartSummaryType = "SUM";
		};

		// Check chart type
		if (ew_IsEmpty(iChartType) || isNaN(iChartType)) iChartType = 1;
		if (iChartType < 9 || iChartType == 22 || iChartType == 101 || iChartType == 104) { // Clear Series field if single series chart
			sChartSFldName = "";
			iChartSeriesType = 0;
		} else if (ew_IsEmpty(sChartSFldName) && nChartYFlds <= 1) { // Degrade if not multi-series
			switch (iChartType) {
				case 9:
				case 14:
					iChartType = 1; break;
				case 10:
				case 15:
					iChartType = 5; break;
				case 11: iChartType = 4; break;
				case 12:
				case 16:
					iChartType = 7; break;
				case 13:
				case 17:
				case 102:
				case 103:
					iChartType = 3; break;
				case 18: iChartType = 5; break;
				case 19: iChartType = 1; break;
			}
			iChartSeriesType = 0;
		} else if (iChartType == 20 && nChartYFlds != 4) { // Degrade Candlestick to Line 2D if not 4 Y fields
			iChartType = 4;
		};
		//if (nChartYFlds > 1 && iChartSeriesType == 1) { // Multi-column, clear series field
		if (iChartSeriesType == 1) { // Multi-column, clear series field
			sChartSFldName = "";
			sChartSFldSqlOrder = "";
		} else if (ew_IsNotEmpty(sChartSFldName) && iChartSeriesType == 0) { // Series field, use single Y field
			nChartYFlds = 1;
			arChartYFlds = sChartYFldName.split("\r\n");
		};

		// Chart width
		var iChartWidth = parseInt(goCht.ChartWidth);
		if (iChartWidth <= 0) iChartWidth = 550;

		// Chart height
		var iChartHeight = parseInt(goCht.ChartHeight);
		if (iChartHeight <= 0) iChartHeight = 400;

		// Use grid component
		var bChartUseGridComponent = false;
		if (iChartType > 8) bChartUseGridComponent = false;
		var iChartGridHeight = 200;
		var oChartGridConfig = {};

		// Chart bg color
		nChartParms += 1;
		var sChartBgColor = goCht.ChartBgColor;

		// Chart caption
		nChartParms += 1;
		var sChartCaption = goCht.ChartCaption;

		// Chart X Axis Name
		nChartParms += 1;
		var sChartXAxisName = goCht.ChartXAxisName;

		// Chart Y Axis Name
		var iChartYDefaultDecimalPrecision = -1;
		if (iChartType == 18 || iChartType == 19) {
			var p1 = -1, p2 = -1;
			var sChartPYAxisName = goCht.ChartPYAxisName;
			if (ew_IsNotEmpty(sChartPYAxisName)) {
				nChartParms += 1;
				var yfld = goTblFlds.Fields[sChartPYAxisName];
				if (yfld && (yfld.FldFmtType == "Currency" || yfld.FldFmtType == "Number"))
					p1 = yfld.FldNumDigits;
			}
			var sChartSYAxisName = goCht.ChartSYAxisName;
			if (ew_IsNotEmpty(sChartSYAxisName)) {
				nChartParms += 1;
				var yfld = goTblFlds.Fields[sChartSYAxisName];
				if (yfld && (yfld.FldFmtType == "Currency" || yfld.FldFmtType == "Number"))
					p2 = yfld.FldNumDigits;
			}
			if (p1 == p2 && p1 > -1)
				iChartYDefaultDecimalPrecision = p1;
		} else {
			var sChartYAxisName = goCht.ChartYAxisName;
			if (ew_IsNotEmpty(sChartYAxisName)) {
				nChartParms += 1;
				var yfld = goTblFlds.Fields[sChartYAxisName];
				if (yfld && (yfld.FldFmtType == "Currency" || yfld.FldFmtType == "Number"))
					iChartYDefaultDecimalPrecision = yfld.FldNumDigits;
			}
		};

		var iChartYAxisMinValue = goCht.ChartYAxisMinValue;
		var iChartYAxisMaxValue = goCht.ChartYAxisMaxValue;

		// Chart show names
		nChartParms += 1;
		var bChartShowNames = goCht.ChartShowNames;
		var sChartShowNames = (bChartShowNames) ? 1 : 0;

		// Chart show values
		nChartParms += 1;
		var bChartShowValues = goCht.ChartShowValues;
		var sChartShowValues = (bChartShowValues) ? 1 : 0;

		// Chart show hover
		nChartParms += 1;
		var bChartShowHover = goCht.ChartShowHover;
		var sChartShowHover = (bChartShowHover) ? 1 : 0;

		// Chart alpha
		nChartParms += 1;
		var iChartAlpha = goCht.ChartAlpha;
		if (iChartAlpha < 0 || iChartAlpha > 100) iChartAlpha = 50;

		// Chart color palette
		nChartParms += 1;
		var sChartColorPalette = goCht.ChartColorPalette;

		var iChartSortType = goCht.ChartSortType;
		if (ew_IsEmpty(iChartSortType)) iChartSortType = 0; // Default no sort
		var sChartXFldSqlOrder;
		if (iChartSortType == 1) {
			sChartXFldSqlOrder = "ASC";
		} else if (iChartSortType == 2) {
			sChartXFldSqlOrder = "DESC";
		} else {
			sChartXFldSqlOrder = "";
		};

		var sChartSortSeq = goCht.ChartSortSeq.trim();
		if (!IsArrayString(sChartSortSeq))
			sChartSortSeq = "\"" + ew_Quote(sChartSortSeq) + "\"";

		var sFldSql;
		var sXAxisDateFormat, sNameDateFormat, sChartFldSql, sChartFldSqlOrderBy;

		if (iChartType == 20) { // Candlestick

			if (ew_IsNotEmpty(sChartXFldName)) {
				var CHARTXFIELD = goTblFlds.Fields[sChartXFldName];
				if (ew_GetFieldType(CHARTXFIELD.FldType) == 2) {
					sXAxisDateFormat = CHARTXFIELD.FldDtFormat;
				}
				if (bCustomViewSrcTable) {
					sFldSql = ew_QuotedName(CHARTXFIELD.FldName); // Use field name
				} else {
					sFldSql = ew_FieldSqlName(CHARTXFIELD); // Get Chart X Field
				}
				sChartFldSql = sFldSql + ", ''";
				if (iChartSortType == 1) {
					sChartFldSqlOrderBy = sFldSql + " ASC";
				} else if (iChartSortType == 2) {
					sChartFldSqlOrderBy = sFldSql + " DESC";
				} else {
					sChartFldSqlOrderBy = sFldSql + " ASC";
				}
			} else {
				sChartFldSql = "'', ''";
				sChartFldSqlOrderBy = "";
			}
			for (var j = 0; j < arChartYFlds.length; j++) {
				var sFldName = ew_UnQuote(arChartYFlds[j]).replace(/\"\"/g, "\"");
				sChartFldSql += ", ";
				if (ew_IsEmpty(sFldName)) {
					sChartFldSql += "0";
				} else {
					var CHARTYFIELD = goTblFlds.Fields[sFldName];
					if (bCustomViewSrcTable) {
						sFldSql = ew_QuotedName(CHARTYFIELD.FldName); // Use field name
					} else {
						sFldSql = ew_FieldSqlName(CHARTYFIELD); // Get Chart Y field
					}
					sChartFldSql += sFldSql;
				}
			};
			if (ew_IsNotEmpty(sChartNFldName)) {
				var CHARTNFIELD = goTblFlds.Fields[sChartNFldName];
				if (ew_GetFieldType(CHARTNFIELD.FldType) == 2) {
					sNameDateFormat = CHARTNFIELD.FldDtFormat;
				}
				if (bCustomViewSrcTable) {
					sFldSql = ew_QuotedName(CHARTNFIELD.FldName); // Use field name
				} else {
					sFldSql = ew_FieldSqlName(CHARTNFIELD); // Get Chart name field
				}
				sChartFldSql += ", " + sFldSql;
			};

		} else { // Non candle-stick

			if (ew_IsNotEmpty(sChartXFldName)) {
				var CHARTXFIELD = goTblFlds.Fields[sChartXFldName];
				if (ew_GetFieldType(CHARTXFIELD.FldType) == 2) {
					sXAxisDateFormat = CHARTXFIELD.FldDtFormat;
				}
			}
			if (sChartYFldName == sColFldName) { // Column Field as Y field
				sChartYFldSql = "<YAxisField>";
				if (ew_IsNotEmpty(sChartSummaryType)) sChartYFldSql = sChartSummaryType + "(" + sChartYFldSql + ")";
			} else {
				sChartYFldSql = "";
				for (var j = 0; j < arChartYFlds.length; j++) {
					if (j > 0) sChartYFldSql += ", ";
					var sFldName = ew_UnQuote(arChartYFlds[j]).replace(/\"\"/g, "\"");
					if (ew_IsEmpty(sFldName)) {
						sFldSql = "0";
					} else {
						var CHARTYFIELD = goTblFlds.Fields[sFldName];
						if (bCustomViewSrcTable) {
							sFldSql = ew_QuotedName(CHARTYFIELD.FldName); // Use field name
						} else {
							sFldSql = ew_FieldSqlName(CHARTYFIELD); // Get Chart Y Field
						}
						if (!IsAggregateSql(sFldSql)) {
							var sChartSmryType;
							if (j <= nChartSummaryType-1) {
								sChartSmryType = arChartSummaryType[j];
							} else {
								sChartSmryType = sChartSummaryType;
							}
							if (CHARTYFIELD.FldRptSkipNull && ew_GetFieldType(CHARTYFIELD.FldType) == 1)
								sFldSql = ew_NullIfFunction(sFldSql);
							if (ew_IsNotEmpty(sChartSmryType)) sFldSql = sChartSmryType + "(" + sFldSql + ")";
						}
					}
					sChartYFldSql += sFldSql;
				};
			};

			var sChartFldDateType = "";
			if (sChartXFldName == sColFldName) { // Handle date type if equal to column field
				sChartXDateFldType = sColFldDateType;
				sChartXDateFldName = sColDateFldName;
				sChartXDateFldCaption = sColDateFldCaption;
				if (bCustomViewSrcTable) {
					sChartXFldSql = ew_QuotedName(sColFldName);
				} else {
					sChartXFldSql = ew_FieldSqlName(COLFIELD);
				}
				if (sColFldDateType == "y") {
					sXAxisDateFormat = "\"y\"";
					sChartXFldSql = ew_DbGrpSql("y",0).replace(/%s/g, sChartXFldSql);
				} else if (sColFldDateType == "q") {
					if (bColFldDateSelect) {
						sChartFldDateType = "xq";
						sChartXFldSql = ew_DbGrpSql("xq",0).replace(/%s/g, sChartXFldSql);
					} else {
						sChartFldDateType = "xyq";
						sChartXFldSql = ew_DbGrpSql("q",0).replace(/%s/g, sChartXFldSql);
					}
				} else if (sColFldDateType == "m") {
					if (bColFldDateSelect) {
						sChartFldDateType = "xm";
						sChartXFldSql = ew_DbGrpSql("xm",0).replace(/%s/g, sChartXFldSql);
					} else {
						sChartFldDateType = "xym";
						sChartXFldSql = ew_DbGrpSql("m",0).replace(/%s/g, sChartXFldSql);
					}
				};
			} else if (ew_IsNotEmpty(sChartXFldName)) {
				sChartXDateFldType = "";
				sChartXDateFldName = "";
				sChartXDateFldCaption = "";
				if (bCustomViewSrcTable) {
					sChartXFldSql = ew_QuotedName(CHARTXFIELD.FldName);
				} else {
					sChartXFldSql = ew_FieldSqlName(CHARTXFIELD);
				}
			};

			if (ew_IsNotEmpty(sChartSFldName)) {
				var CHARTSFLD = goTblFlds.Fields[sChartSFldName];
				if (bCustomViewSrcTable) {
					sChartSFldSql = ew_QuotedName(CHARTSFLD.FldName);
				} else {
					sChartSFldSql = ew_FieldSqlName(CHARTSFLD);
				}
				if (sChartSFldName == sColFldName) { // Handle date type if equal to column field
					if (sColFldDateType == "y") {
						sChartSFldSql = ew_DbGrpSql("y",0).replace(/%s/g, sChartSFldSql);
					} else if (sColFldDateType == "q") {
						if (bColFldDateSelect) {
							sChartFldDateType = "sq";
							sChartSFldSql = ew_DbGrpSql("xq",0).replace(/%s/g, sChartSFldSql);
						} else {
							sChartFldDateType = "syq";
							sChartSFldSql = ew_DbGrpSql("q",0).replace(/%s/g, sChartSFldSql);
						}
					} else if (sColFldDateType == "m") {
						if (bColFldDateSelect) {
							sChartFldDateType = "sm";
							sChartSFldSql = ew_DbGrpSql("xm",0).replace(/%s/g, sChartSFldSql);
						} else {
							sChartFldDateType = "sym";
							sChartSFldSql = ew_DbGrpSql("m",0).replace(/%s/g, sChartSFldSql);
						}
					}
				}
			};

		};

		var sPageBreakType = "", sChartClass = "ewChartTop", sPageBreakTag = "";
		if (arChtPageBreak[goCht.ChartVar] == 1) { // Page break on top
			sPageBreakType = "before";
			sChartClass = "ewChartBottom";
			sPageBreakTag = " data-page-break=\"before\"";
		} else if (arChtPageBreak[goCht.ChartVar] == 2) { // Page break on bottom
			sPageBreakType = "after";
			sChartClass = "ewChartTop";
			sPageBreakTag = " data-page-break=\"after\"";
		}
		sPageBreakCheck = "($" + gsPageObj + "->Export == \"print\" || $" + gsPageObj + "->Export == \"pdf\" || $" + gsPageObj + "->Export == \"email\" || $" + gsPageObj + "->Export == \"excel\" && defined(\"EWR_USE_PHPEXCEL\") || $" + gsPageObj + "->Export == \"word\" && defined(\"EWR_USE_PHPWORD\"))";

	}; // End show chart

##-->
<!--##
	EXT_CHART = null;

	if (ew_IsNotEmpty(sChartXFldName) && ew_IsNotEmpty(sChartYFldName)) {
		var EXT = EXTS("FusionCharts");
		var EXT_PROJ = EXT.PROJ; // Extended project
		var EXT_DB = EXT.PROJ.DB; // Extended database
		if (EXT_DB.Tables.TableExist(TABLE.TblName)) {
			var EXT_TABLE = EXT_DB.Tables(TABLE.TblName);
			if (EXT_TABLE.Charts.ChartExist(goCht.ChartName)) {
				EXT_CHART = EXT_TABLE.Charts(goCht.ChartName);
			}
		}
	}

	// Check if use grid component
	if (EXT_CHART && EXT_CHART.Properties.PropertyExist("useGridComponent"))
		bChartUseGridComponent = EXT_CHART.Properties("useGridComponent");
	if (EXT_CHART && EXT_CHART.Properties.PropertyExist("gridComponentHeight"))
		iChartGridHeight = EXT_CHART.Properties("gridComponentHeight");
	if (iChartGridHeight <= 0) iChartGridHeight = 200;

	if (IsDashBoard) {
		TABLE = CURRENTABLE; // Restore current table/charts
		ew_LoadCurrentCharts();
		goChts = goTblChts.Charts;
		goCht = curCht;
	}
##-->
<!--##/session##-->


<!--##session chart_include##-->
<?php
<!--##
	if (IsDashBoard) {
##-->
// Set up table object
$Table = &$<!--##=sChartTblVar##-->;
	<!--## if (bDashboardChartHasUserIDFld) { ##-->
$<!--##=gsPageObj##-->->Filter = $Table->GetUserIDFilter();
	<!--## } else { ##-->
$<!--##=gsPageObj##-->->Filter = "";
	<!--## } ##-->
	<!--## if (IsCrosstabChart) { ##-->
$Table->LoadColumnValues();
	<!--## } ##-->
<!--##
	}
##-->
// Set up chart object
$Chart = &$Table-><!--##=sChartChtVar##-->;

<!--## if (IsDashBoard) { ##-->
$Chart->ChartWidth = <!--##=iDashboardChartWidth##-->;
$Chart->ChartHeight = <!--##=iDashboardChartHeight##-->;
<!--## } ##-->

<!--## if (IsDashBoard && HasSourceReport) { ##-->
$Chart->SetChartParm("clickurl", "<!--##=ew_Quote(sChartClickUrl)##-->", TRUE); // Add click url
<!--## } ##-->

// Set up chart SQL
<!--## if (IsCrosstabChart) { ##-->
$SqlSelect = str_replace("<DistinctColumnFields>", $Table->DistinctColumnFields, $Table->getSqlSelect());
$SqlChartSelect = str_replace("<YAxisField>", $Table->GetChartColumnSql(), $Chart->SqlSelect);
<!--## } else { ##-->
$SqlSelect = $Table->getSqlSelect();
$SqlChartSelect = $Chart->SqlSelect;
<!--## } ##-->
<!--## if (bCustomViewSrcTable) { ##-->
$sSqlChartBase = "(" . ewr_BuildReportSql($SqlSelect, $Table->getSqlWhere(), $Table->getSqlGroupBy(), $Table->getSqlHaving(), (EWR_IS_MSSQL) ? $Table->getSqlOrderBy() : "", $<!--##=gsPageObj##-->->Filter, "") . ") EW_TMP_TABLE";
<!--## } else { ##-->
$sSqlChartBase = $Table->getSqlFrom();
<!--## } ##-->
<!--## if (ew_IsNotEmpty(sChartSFldName)) { // Series field ##-->
	<!--## if (sChartSFldName == sColFldName && bColFldDateSelect && sColFldDateType == "q") { ##-->
$Chart->Series[] = ewr_QuarterName(1);
$Chart->Series[] = ewr_QuarterName(2);
$Chart->Series[] = ewr_QuarterName(3);
$Chart->Series[] = ewr_QuarterName(4);
	<!--## } else if (sChartSFldName == sColFldName && bColFldDateSelect && sColFldDateType == "m") { ##-->
$Chart->Series[] = ewr_MonthName(1);
$Chart->Series[] = ewr_MonthName(2);
$Chart->Series[] = ewr_MonthName(3);
$Chart->Series[] = ewr_MonthName(4);
$Chart->Series[] = ewr_MonthName(5);
$Chart->Series[] = ewr_MonthName(6);
$Chart->Series[] = ewr_MonthName(7);
$Chart->Series[] = ewr_MonthName(8);
$Chart->Series[] = ewr_MonthName(9);
$Chart->Series[] = ewr_MonthName(10);
$Chart->Series[] = ewr_MonthName(11);
$Chart->Series[] = ewr_MonthName(12);
	<!--## } else { ##-->
// Load chart series from sql directly
<!--## if (bCustomViewSrcTable) { ##-->
$sSql = $Chart->SqlSelectSeries . $sSqlChartBase;
$sSql = ewr_BuildReportSql($sSql, "", $Chart->SqlGroupBySeries, "", $Chart->SqlOrderBySeries, "", "");
<!--## } else { ##-->
$sSql = $Chart->SqlSelectSeries . $sSqlChartBase;
$sSql = ewr_BuildReportSql($sSql, $Table->getSqlWhere(), $Chart->SqlGroupBySeries, "", $Chart->SqlOrderBySeries, $<!--##=gsPageObj##-->->Filter, "");
<!--## } ##-->
$Chart->ChartSeriesSql = $sSql;
	<!--## } ##-->
<!--## } else if (nChartYFlds > 1) { // Multiple Y fields ##-->
<!--##
	for (var j = 0; j < arChartYFlds.length; j++) {
		var sFldName = ew_UnQuote(arChartYFlds[j]).replace(/\"\"/g, "\"");
		var CHARTYFIELD = goTblFlds.Fields[sFldName];
		var sFldObj = "Table->" + CHARTYFIELD.FldParm;
		if (iChartType == 18 || iChartType == 19) {
			if (j <= nChartYAxis-1) {
				sYAxis = arChartYAxis[j];
			} else {
				sYAxis = sChartYAxis;
			}
			if (sYAxis == "2") {
				sFldSeriesYAxis = "S"; // Secondary
			} else {
				sFldSeriesYAxis = "P"; // Primary
			}
##-->
$Chart->Series[] = array($<!--##=sFldObj##-->->FldCaption(), "<!--##=sFldSeriesYAxis##-->");
<!--##
		} else {
##-->
$Chart->Series[] = $<!--##=sFldObj##-->->FldCaption();
<!--##
		}
	}; // End for
##-->
<!--## } ##-->
// Load chart data from sql directly
<!--## if (iChartSortType == 5) { // Run time sort ##-->
$Chart->SqlOrderBy .= ($Chart->ChartSortType == 2) ? " DESC" : "";
<!--## } ##-->
<!--## if (bCustomViewSrcTable) { ##-->
$sSql = $SqlChartSelect . $sSqlChartBase;
$sSql = ewr_BuildReportSql($sSql, "", $Chart->SqlGroupBy, "", $Chart->SqlOrderBy, "", "");
<!--## } else { ##-->
$sSql = $SqlChartSelect . $sSqlChartBase;
$sSql = ewr_BuildReportSql($sSql, $Table->getSqlWhere(), $Chart->SqlGroupBy, "", $Chart->SqlOrderBy, $<!--##=gsPageObj##-->->Filter, "");
<!--## } ##-->
$Chart->ChartSql = $sSql;
$Chart->DrillDownInPanel = $<!--##=gsPageObj##-->->DrillDownInPanel;
<!--## if (IsDashBoard) { ##-->
$Chart->ChartDrillDownUrl = ""; // No drill down for dashboard
<!--## } ##-->
<!--##
	if (!IsDashBoard && ew_IsChartDrillDown(goCht)) {
		var DRILLTABLE = DB.Tables.Item(goCht.ChartDrillTable);
		var arSourceFlds = goCht.ChartDrillSourceFields.split("||");
		var arTargetFlds = goCht.ChartDrillTargetFields.split("||");
		if (arSourceFlds.length == arTargetFlds.length) {
##-->
// Update chart drill down url from filter
<!--##
			for (var j = 0, cnt = arTargetFlds.length; j < cnt; j++) {
				var SOURCEFLD = goTblFlds.Fields[arSourceFlds[j]];
				var sSourceFldName = SOURCEFLD.FldName;
				var sSourceFldObj = gsPageObj + "->" + SOURCEFLD.FldVar.substr(2);
				var TARGETFLD = DRILLTABLE.Fields.Item(arTargetFlds[j]);
				var sTargetFldVar = TARGETFLD.FldVar;
				var sTargetFldParm = sTargetFldVar.substr(2);
				if (sSourceFldName != sChartXFldName && sSourceFldName != sChartSFldName) { // NOT X Axis/Series Field
					if (sSourceFldName == sColFldName && sColDateFldName != "") { // Column date field
						rowtype = 3;
						parm = 0;
					} else {
						rowtype = 0;
						parm = -1;
					}
##-->
$Chart->ChartDrillDownUrl = str_replace("f<!--##=j##-->", ewr_Encrypt($<!--##=gsPageObj##-->->GetDrillDownSQL($<!--##=sSourceFldObj##-->, "<!--##=sTargetFldParm##-->", <!--##=rowtype##-->, <!--##=parm##-->)), $Chart->ChartDrillDownUrl);
<!--##
				}
			}
		}
	}
##-->

// Set up page break
if (<!--##=sPageBreakCheck##--> && $<!--##=gsPageObj##-->->ExportChartPageBreak) {
<!--## if (SYSTEMFUNCTIONS.ServerScriptExist(sCtrlType,"Page_Breaking")) { ##-->
	// Page_Breaking server event
	$<!--##=gsPageObj##-->->Page_Breaking($<!--##=gsPageObj##-->->ExportChartPageBreak, $<!--##=gsPageObj##-->->PageBreakContent);
<!--## } ##-->
	$Chart->PageBreakType = "<!--##=sPageBreakType##-->";
	$Chart->PageBreak = $Table->ExportChartPageBreak;
	$Chart->PageBreakContent = $Table->PageBreakContent;
}

// Set up show temp image
$Chart->ShowChart = (<!--##=sChartExportShow##-->);
$Chart->ShowTempImage = (<!--##=sChartExportShowTempImage##-->);
?>

<!--##
	if (IsDashBoard) { // Use source table/chart name
		var CURRENTTABLE = TABLE; // Save table
		var CURRENTCHART = CHART; // Save chart
		if (DB.Tables.TableExist(goCht.ChartSourceTable)) {
			TABLE = DB.Tables(goCht.ChartSourceTable);
			if (TABLE.Charts.ChartExist(goCht.ChartSourceChart))
				CHART = TABLE.Charts(goCht.ChartSourceChart);
		}
	}
##-->
<!--##=SYSTEMFUNCTIONS.IncludeFile("rptchart","")##-->
<!--##
	if (IsDashBoard) {
		TABLE = CURRENTTABLE; // Restore table
		CHART = CURRENTCHART; // Restore chart
	}
##-->

<!--## if (goCht.ChartPosition == 4 && !IsDashBoard) { ##-->
<?php if ($<!--##=gsPageObj##-->->Export <> "email" && !$<!--##=gsPageObj##-->->DrillDown) { ?>
<!--##=sExpStart##-->
<a href="javascript:void(0);" class="ewTopLink" onclick="$(document).scrollTop($('#top').offset().top);"><!--##@Top##--></a>
<!--##=sExpEnd##-->
<?php } ?>
<!--## } ##-->

<!--##/session##-->


<!--##session chart_html##-->
<a id="<!--##=sChartId##-->"></a>

<!--## if (IsDashBoard && HasSourceReport && IsFCFChart(iChartType)) { // Dashboard ##-->
<div class="<!--##=sChartDivClass##-->" onclick="window.location.href='<!--##=sChartClickUrl##-->';return false;">
<!--## } else { ##-->
<div class="<!--##=sChartDivClass##-->">
<!--## } ##-->

<div id="div_ctl_<!--##=sChartDivName##-->" class="ewChart">
<!--## if (!IsDashBoard) { // Not dash board ##-->
<!--## if (goCht.ChartSortType == 5) { ##-->
<!--##=sExpStart##-->
<div class="ewChartSort">
<form class="ewForm form-horizontal" action="<?php echo ewr_CurrentPage() ?>#<!--##=sChartId##-->">
<?php echo $ReportLanguage->Phrase("ChartOrder") ?>&nbsp;
<select id="chartordertype" name="chartordertype" class="form-control" onchange="this.form.submit();">
<option value="1"<?php if ($<!--##=sChartObj##-->->ChartSortType == "1") echo " selected=\"selected\"" ?>><?php echo $ReportLanguage->Phrase("ChartOrderXAsc") ?></option>
<option value="2"<?php if ($<!--##=sChartObj##-->->ChartSortType == "2") echo " selected=\"selected\"" ?>><?php echo $ReportLanguage->Phrase("ChartOrderXDesc") ?></option>
<option value="3"<?php if ($<!--##=sChartObj##-->->ChartSortType == "3") echo " selected=\"selected\"" ?>><?php echo $ReportLanguage->Phrase("ChartOrderYAsc") ?></option>
<option value="4"<?php if ($<!--##=sChartObj##-->->ChartSortType == "4") echo " selected=\"selected\"" ?>><?php echo $ReportLanguage->Phrase("ChartOrderYDesc") ?></option>
</select>
<input type="hidden" id="chartorder" name="chartorder" value="<!--##=gsChartVar##-->">
</form>
</div>
<!--##=sExpEnd##-->
<!--## } ##-->
<!--## } ##-->
<div id="div_<!--##=sChartDivName##-->" class="ewChartDiv"></div>
<!-- grid component -->
<div id="div_<!--##=sChartDivName##-->_grid" class="ewChartGrid"></div>
</div>

</div>

<!--##/session##-->


<!--##session chart_config##-->
<?php
// Set up chart
//$Chart = &$Table-><!--##=gsChartVar##-->;

// Initialize chart data
$Chart->ID = "<!--##=gsTblVar##-->_<!--##=gsChartVar##-->"; // Chart ID
$Chart->SetChartParms(array(array("type", "<!--##=iChartType##-->", FALSE),
	array("seriestype", "<!--##=iChartSeriesType##-->", FALSE)));  // Chart type / Chart series type
<!--## if (ew_IsNotEmpty(sChartBgColor)) { ##-->
$Chart->SetChartParm("bgcolor", "<!--##=sChartBgColor##-->", TRUE); // Background color
<!--## } ##-->
$Chart->SetChartParms(array(array("caption", $Chart->ChartCaption()),
	array("xaxisname", $Chart->ChartXAxisName()))); // Chart caption / X axis name
<!--## if (iChartType == 18 || iChartType == 19) { // Combination Charts ##-->
$Chart->SetChartParms(array(array("PYAxisName", $Chart->ChartPYAxisName()),
	array("SYAxisName", $Chart->ChartSYAxisName()))); // Primary Y axis name / Secondary Y axis name
<!--## } else { ##-->
$Chart->SetChartParm("yaxisname", $Chart->ChartYAxisName(), TRUE); // Y axis name
<!--## } ##-->
$Chart->SetChartParms(array(array("shownames", "<!--##=sChartShowNames##-->"),
	array("showvalues", "<!--##=sChartShowValues##-->"),
	array("showhovercap", "<!--##=sChartShowHover##-->"))); // Show names / Show values / Show hover
<!--## if (iChartAlpha > 0) { ##-->
$Chart->SetChartParm("alpha", "<!--##=iChartAlpha##-->", FALSE); // Chart alpha
<!--## } ##-->
<!--## if (ew_IsNotEmpty(sChartColorPalette)) { ##-->
$Chart->SetChartParm("colorpalette", "<!--##=sChartColorPalette##-->", FALSE); // Chart color palette
<!--## } ##-->
?>
<!--## if (ew_IsNotEmpty(sChartXFldName) && ew_IsNotEmpty(sChartYFldName)) { ##-->
<?php
<!--##
if (EXT_CHART != null) {
	var parmdata = "", gridcfg = {};
	var bUseFusionChartExport = false;
	//for (var prp in EXT_CHART.Properties) {
	for (var enumerator = new Enumerator(EXT_CHART.Properties); !enumerator.atEnd(); enumerator.moveNext()) {
		var prp = enumerator.item();
		var name = prp.Name;
		var value = prp.Value;
		if (name != "ChartSeq" && name != "ChartName" && name != "useGridComponent" && name != "gridComponentHeight" && ew_IsNotEmpty(name) && ew_IsNotEmpty(value)) {
			if (/^grid/.test(name)) { // Grid parameters
				name = name.replace(/^grid\w/, name.substr(4, 1).toLowerCase());
				gridcfg[name] = String(value);
			} else {
				value = ConvertData(value, prp.DataType);
				if (name == "exportEnabled") // v8.1
                    bUseFusionChartExport = (String(value) == "1"); // v8.1
				if (parmdata != "") parmdata += ",\r\n\t";
				
				parmdata += "array(\"" + ew_Quote(name) + "\", \"" + ew_Quote(value) + "\")";
			}
		}
	}
	if (parmdata != "") parmdata += ",\r\n\t";
	if (bUseFusionChartExport) { // v8.1
		parmdata += "array(\"exportEnabled\", \"1\"),\r\n\t" +
		 "array(\"exportAtClient\", \"1\"),\r\n\t" +
		 "array(\"exportAction\", \"download\"),\r\n\t" +
		 "array(\"exportShowMenuItem\", \"1\")";
	} else {
		parmdata += "array(\"exportEnabled\", \"1\"),\r\n\t" +
		 "array(\"exportHandler\", ewr_ConvertFullUrl(\"" + ew_GetFileNameByCtrlID("FusionChartsExportHandler", false) + "\")),\r\n\t" +
		 "array(\"exportAtClient\", \"0\"),\r\n\t" +
		 "array(\"exportAction\", \"save\"),\r\n\t" +
		 "array(\"exportDialogMessage\", $ReportLanguage->Phrase(\"ExportChart\")),\r\n\t" +
		 "array(\"exportShowMenuItem\", \"0\")";
	}
	if (parmdata != "") {
##-->
$Chart->SetChartParms(array(<!--##=parmdata##-->));
<!--##
	}
	if (gridcfg) {
##-->
$Chart->ChartGridConfig = '<!--##=ew_SQuote(JSON.stringify(gridcfg))##-->';
<!--##
	}
} 

// Define trend lines
for (var j = 1, cnt = CHART.Trendlines.Count(); j <= cnt; j++) {
	var TREND = CHART.Trendlines.Seq(j);
	if (TREND.TrendShow) {
		sStartValue = TREND.TrendStartValue;
		if (ew_IsEmpty(sStartValue)) sStartValue = 0;
		sEndValue = TREND.TrendEndValue;
		if (ew_IsEmpty(sEndValue)) sEndValue = 0;
		sColor = TREND.TrendColor;
		sDispValue = TREND.TrendDisplayValue;
		sThickness = TREND.TrendThickness;
		if (ew_IsEmpty(sThickness)) sThickness = 1;
		sIsTrendZone = (TREND.TrendIsTrendZone) ? "1" : "0";
		sShowOnTop = (TREND.TrendShowOnTop) ? "1" : "0";
		sAlpha = TREND.TrendAlpha;
		if (ew_IsEmpty(sAlpha)) sAlpha = 0;
		sToolText = TREND.TrendToolText;
		sValueOnRight = (TREND.TrendValueOnRight) ? "1" : "0";
		sDashed = (TREND.TrendDashed) ? "1" : "0";
		sDashLen = TREND.TrendDashLen;
		if (ew_IsEmpty(sDashLen)) sDashLen = 0;
		sDashGap = TREND.TrendDashGap;
		if (ew_IsEmpty(sDashGap)) sDashGap = 0;
		if (CHART.ChartType == 18 || CHART.ChartType == 19)
			sParentYAxis = (TREND.TrendSecondaryYAxis) ? "S" : "P";
		else
			sParentYAxis = "";
##-->
$Chart->Trends[] = array(<!--##=sStartValue##-->, <!--##=sEndValue##-->, "<!--##=sColor##-->", "<!--##=ew_Quote(sDispValue)##-->", <!--##=sThickness##-->, "<!--##=sIsTrendZone##-->", "<!--##=sShowOnTop##-->", <!--##=sAlpha##-->, "<!--##=ew_Quote(sToolText)##-->", "<!--##=sValueOnRight##-->", "<!--##=sDashed##-->", <!--##=sDashLen##-->, <!--##=sDashGap##-->, "<!--##=ew_Quote(sParentYAxis)##-->");
<!--##
	}
}
##-->
?>
<!--## } ##-->
<!--##/session##-->


<!--##session chart_content##-->
<?php

	// Setup chart series data
	if ($Chart->ChartSeriesSql <> "") {
		ewr_LoadChartSeries($Chart->ChartSeriesSql, $Chart);
		if (EWR_DEBUG_ENABLED)
			echo "<p>(Chart Series SQL): " . $Chart->ChartSeriesSql . "</p>";
	}

	// Setup chart data
	if ($Chart->ChartSql <> "") {
		ewr_LoadChartData($Chart->ChartSql, $Chart);
		if (EWR_DEBUG_ENABLED)
			echo "<p>(Chart SQL): " . $Chart->ChartSql . "</p>";
	}

<!--##
	if (iChartSortType == 5) // Run time sort
		sChartSortType = "$Chart->ChartSortType";
	else
		sChartSortType = iChartSortType;
	if (ew_IsNotEmpty(sChartSFldName)) {
##-->
	ewr_SortMultiChartData($Chart->Data, <!--##=sChartSortType##-->, <!--##=sChartSortSeq##-->);
<!--##
	} else {
##-->
	ewr_SortChartData($Chart->Data, <!--##=sChartSortType##-->, <!--##=sChartSortSeq##-->);
<!--##
	}
##-->

	// Render chart
	$Chart->LoadChartParms();
	$chartxml = $Chart->ChartXml();
?>
<span class="<!--##=sChartClass##-->">
<?php
	// Show page break content
	if ($Chart->PageBreak && $Chart->PageBreakType == "before")
		echo $Chart->PageBreakContent;

	if ($Chart->ShowChart) { // Show actual chart
		<!--##
			ScrollChart = "FALSE";
			if (EXT_CHART != null) {
				if (EXT_CHART.Properties("numVisiblePlot") && ew_IsNotEmpty(EXT_CHART.Properties("numVisiblePlot").Value)) {
					if (parseInt(EXT_CHART.Properties("numVisiblePlot").Value) > 0) {
						ScrollChart = "TRUE";
					} else {
						ScrollChart = "FALSE";
					}
				}
			}
		##-->
		echo $Chart->ShowChartFC($chartxml, <!--##=ScrollChart##-->, $Chart->DrillDownInPanel);

	} elseif ($Chart->ShowTempImage) { // Show temp image

		$TmpChartImage = ewr_TmpChartImage("chart_<!--##=gsTblVar##-->_<!--##=gsChartVar##-->", <!--##=ew_Val(bUseCustomTemplate)##-->);
		$TmpGridImage = ewr_TmpChartImage("chart_<!--##=gsTblVar##-->_<!--##=gsChartVar##-->_grid", <!--##=ew_Val(bUseCustomTemplate)##-->);
		if ($TmpChartImage <> "") {
?>
<?php if ($<!--##=gsPageObj##-->->Export == "word" && defined('EWR_USE_PHPWORD') || $<!--##=gsPageObj##-->->Export == "excel" && defined('EWR_USE_PHPEXCEL')) { ?>
<table class="ewChart"<!--##=sPageBreakTag##-->>
<tr><td><img src="<?php echo $TmpChartImage ?>" alt=""><br><?php if ($TmpGridImage <> "") { ?>
<img src="<?php echo $TmpGridImage ?>" alt=""><?php } ?></td></tr>
</table>
<?php } else { ?>
<div class="ewChart"<!--##=sPageBreakTag##-->><img src="<?php echo $TmpChartImage ?>" alt=""><br><?php if ($TmpGridImage <> "") { ?>
<img src="<?php echo $TmpGridImage ?>" alt=""><?php } ?></div>
<?php } ?>
<?php
		}
	}

	// Show page break content
	if ($Chart->PageBreak && $Chart->PageBreakType == "after")
		echo $Chart->PageBreakContent;
?>
</span>
<!--##/session##-->