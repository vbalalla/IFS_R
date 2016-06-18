<?php
<!--##session report_sort_function##-->
<!--##
	var arSortFlds = [];
	var nFlds = 0;
	if (TABLE.TblReportType == "crosstab") {

		for (var i = 0; i < nGrps; i++) {
			var sortfld = [];
			sortfld['FldName'] = arGrpFlds[i]['FldName']; // FldName
			sortfld['FldVar'] = arGrpFlds[i]['FldVar']; // FldVar
			arSortFlds[arSortFlds.length] = sortfld;
			nFlds += 1;
		}

	} else {

		for (var i = 0; i < nGrps; i++) {
			var sortfld = [];
			sortfld['FldName'] = arGrpFlds[i]['FldName']; // FldName
			sortfld['FldVar'] = arGrpFlds[i]['FldVar']; // FldVar
			arSortFlds[arSortFlds.length] = sortfld;
			nFlds += 1;
		}
		for (var i = 0; i < nDtls; i++) {
			var sortfld = [];
			sortfld['FldName'] = arDtlFlds[i]['FldName']; // FldName
			sortfld['FldVar'] = arDtlFlds[i]['FldVar']; // FldVar
			arSortFlds[arSortFlds.length] = sortfld;
			nFlds += 1;
		}

	}
##-->
	//-------------------------------------------------------------------------------
	// Function GetSort
	// - Return Sort parameters based on Sort Links clicked
	// - Variables setup: Session[EWR_TABLE_SESSION_ORDER_BY], Session["sort_Table_Field"]

	function GetSort() {

	<!--##
		// Get default order by
		sDefaultOrderByFlds = SYSTEMFUNCTIONS.OrderByFieldNames();
		sDefaultOrderBy = "";
		if (ew_IsNotEmpty(sDefaultOrderByFlds)) {
			arDefaultOrderByFlds = sDefaultOrderByFlds.split("\r\n");
			for (var i = 0; i < arDefaultOrderByFlds.length; i++) {
				sFldName = arDefaultOrderByFlds[i].trim();
				goFld = goTblFlds.Fields[sFldName];
				sDefaultOrderBy += ew_FieldSqlName(goFld) + " " + goFld.FldOrder + ", ";
			}
			if (ew_IsNotEmpty(sDefaultOrderBy)) sDefaultOrderBy = sDefaultOrderBy.substr(0, sDefaultOrderBy.length-2);
		}
	##-->
		if ($this->DrillDown)
			return "<!--##=ew_Quote(sDefaultOrderBy)##-->";

	<!--## if (iSortType == 2) { ##-->
		// Check for Ctrl pressed
		$bCtrl = (@$_GET["ctrl"] <> "");
	<!--## } ##-->

		// Check for a resetsort command
		if (strlen(@$_GET["cmd"]) > 0) {
			$sCmd = @$_GET["cmd"];
			if ($sCmd == "resetsort") {
				$this->setOrderBy("");
				$this->setStartGroup(1);
	<!--##
		for (var i = 0; i < nFlds; i++) {
			sFldParm = arSortFlds[i]['FldVar'].substr(2);
			sFldObj = "this->" + sFldParm;
	##-->
				$<!--##=sFldObj##-->->setSort("");
	<!--##
		}
	##-->
			}

		// Check for an Order parameter
		} elseif (@$_GET["order"] <> "") {
			$this->CurrentOrder = ewr_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
	<!--##
		for (var i = 0; i < nFlds; i++) {
			sFldName = arSortFlds[i]['FldName'];
			sFldParm = arSortFlds[i]['FldVar'].substr(2);
			sFldObj = "this->" + sFldParm;
			if (iSortType == 1) { // Single Column Sort
	##-->
			$this->UpdateSort($<!--##=sFldObj##-->); // <!--##=sFldName##-->
	<!--##
			} else if (iSortType == 2) { // Multi Column Sort
	##-->
			$this->UpdateSort($<!--##=sFldObj##-->, $bCtrl); // <!--##=sFldName##-->
	<!--##
			}
		}
	##-->
			$sSortSql = $this->SortSql();
			$this->setOrderBy($sSortSql);
			$this->setStartGroup(1);
		}

	<!--##
		if (ew_IsNotEmpty(sDefaultOrderBy)) {
	##-->
		// Set up default sort
		if ($this->getOrderBy() == "") {
			$this->setOrderBy("<!--##=ew_Quote(sDefaultOrderBy)##-->");
	<!--##
			for (var i = 0; i < arDefaultOrderByFlds.length; i++) {
				sFldName = arDefaultOrderByFlds[i].trim();
				goFld = goTblFlds.Fields[sFldName];
				sFldVar = goFld.FldVar;
				sFldParm = sFldVar.substr(2);
				sFldObj = "this->" + sFldParm;
				sFldOrderBy = goFld.FldOrder;
	##-->
			$<!--##=sFldObj##-->->setSort("<!--##=sFldOrderBy##-->");
	<!--##
			}
	##-->
		}
	<!--##
		}
	##-->

		return $this->getOrderBy();
	}

<!--## if (bChartDynamicSort) { ##-->

	//-------------------------------------------------------------------------------
	// Function GetChartSort
	//

	function GetChartSort() {

		// Check for a resetsort command
		if (strlen(@$_GET["cmd"]) > 0) {
			$sCmd = @$_GET["cmd"];
			if ($sCmd == "resetsort") {
	<!--##
		for (var i = 0, len = arAllCharts.length; i < len; i++) {
			if (GetChtObj(arAllCharts[i])) {
				if (IsShowChart(goCht) && goCht.ChartSortType == 5) {
	##-->
				$this-><!--##=gsChartVar##-->->setSort(0);
	<!--##
				}
			}
		}
	##-->
			}

		// Check for chartorder parameter
		} elseif (@$_GET["chartorder"] <> "") {
			$chartorder = ewr_StripSlashes(@$_GET["chartorder"]);
			$chartordertype = ewr_StripSlashes(@$_GET["chartordertype"]);
	<!--##
		for (var i = 0, len = arAllCharts.length; i < len; i++) {
			if (GetChtObj(arAllCharts[i])) {
				if (IsShowChart(goCht) && goCht.ChartSortType == 5) {
	##-->
			if ($chartorder == "<!--##=gsChartVar##-->")
				$this-><!--##=gsChartVar##-->->setSort($chartordertype);
	<!--##
				}
			}
		}
	##-->

		}

		// Restore chart sort type from Session
	<!--##
		for (var i = 0, len = arAllCharts.length; i < len; i++) {
			if (GetChtObj(arAllCharts[i])) {
				if (IsShowChart(goCht) && goCht.ChartSortType == 5) {
	##-->
		$this-><!--##=gsChartVar##-->->ChartSortType = $this-><!--##=gsChartVar##-->->getSort();
	<!--##
				}
			}
		}
	##-->

	}

<!--## } ##-->

<!--##/session##-->
?>
