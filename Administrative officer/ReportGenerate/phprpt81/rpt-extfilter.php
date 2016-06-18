<!--##session report_extfilter_html##-->
<!--##	
	if (bReportExtFilter || bShowYearSelection || nSearchFlds > 0) {
		var sSearchPanelClass = PROJ.GetV("SearchPanelCollapsed") ? "" : " in";
		var iColCnt = 0;
		var iRowCnt = 0;
##-->
<!--##=sExpStart##-->
<!-- Search form (begin) -->
<form name="<!--##=sFormName##-->" id="<!--##=sFormName##-->" class="form-inline ewForm ewExtFilterForm" action="<?php echo ewr_CurrentPage() ?>">
<?php $SearchPanelClass = ($Page->Filter <> "") ? " in" : "<!--##=sSearchPanelClass##-->"; ?>

<!--## if (bReportExtFilter || bShowYearSelection) { ##-->

<div id="<!--##=sFormName##-->_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<!--## if (bShowYearSelection) { ##-->
<!--##
	iColCnt += 1;
	if (!bReportExtFilter || bAutoPostBack) {
		sPostBack = " onchange=\"ewrForms['" + sFormName + "'].Submit();\"";
	} else {
		sPostBack = "";
	}
##-->
<!-- Year selection -->
	<!--##
		if ((iColCnt-1) % iExtSearchFldPerRow == 0) {
			iRowCnt += 1;
	##-->
<div id="r_<!--##=iRowCnt##-->" class="ewRow">
	<!--## } ##-->
<div id="c_<!--##=sColDateFldParm##-->" class="ewCell form-group">
	<label for="<!--##=sColDateFldName##-->" class="ewSearchCaption ewLabel"><?php echo $ReportLanguage->Phrase("Year"); ?></label>
	<span class="control-group ewSearchField">
	<select id="<!--##=sColDateFldName##-->" class="form-control" name="<!--##=sColDateFldName##-->"<!--##=sPostBack##-->>
<?php
// Set up array
if (is_array($<!--##=sColDateFldObj##-->->ValueList)) {
	$cntyr = count($<!--##=sColDateFldObj##-->->ValueList);
	for ($yearIdx = 0; $yearIdx < $cntyr; $yearIdx++) {
		$yearValue = $<!--##=sColDateFldObj##-->->ValueList[$yearIdx];
		$yearSelected = (strval($yearValue) == strval($<!--##=sColDateFldObj##-->->SelectionList)) ? " selected=\"selected\"" : "";
?>
	<option value="<?php echo $yearValue ?>"<?php echo $yearSelected ?>><?php echo $yearValue ?></option>
<?php
	}
}
?>
	</select>
	</span>
</div>
	<!--## if (iColCnt % iExtSearchFldPerRow == 0) { ##-->
</div>
	<!--## } ##-->
<!--## }; // End show year selection ##-->
<!--## if (bReportExtFilter) { ##-->
<!--##
	for (var i = 0; i < nFldCount; i++) {
		if (GetFldObj(arFlds[i])) {
			sForLabel = (ew_UseForLabel(goFld) || goFld.FldHtmlTag == "NO") ? " for=\"sv_" + gsFldParm + "\"" : "";
			// Non-text filters
			if (IsExtendedFilter(goFld) && !IsTextFilter(goFld)) {
				iColCnt += 1;
				if (IsDateFilter(goFld)) {
					sDropDownType = "$" + gsFldObj + "->DateFilter";
					sFldDtFormat = goFld.FldDtFormat;
				} else if (ew_GetFieldType(goFld.FldType) == 2) {
					sDropDownType = "\"date\"";
					sFldDtFormat = goFld.FldDtFormat;
				} else if (SYSTEMFUNCTIONS.IsBoolFld()) {
					sDropDownType = "\"boolean\"";
					sFldDtFormat = 0;
				} else {
					sDropDownType = "\"\"";
					sFldDtFormat = 0;
				}
				if (ew_IsEmpty(sFldDtFormat)) sFldDtFormat = 0;
				//if (bAutoPostBack) {
				//	sPostBack = " onchange=\"this.form.submit();\"";
				//} else {
				//	sPostBack = "";
				//}
##-->
	<!--##
		if ((iColCnt-1) % iExtSearchFldPerRow == 0) {
			iRowCnt += 1;
	##-->
<div id="r_<!--##=iRowCnt##-->" class="ewRow">
	<!--## } ##-->
<div id="c_<!--##=gsFldParm##-->" class="ewCell form-group">
	<label<!--##=sForLabel##--> class="ewSearchCaption ewLabel"><?php echo $<!--##=gsFldObj##-->->FldCaption() ?></label>
	<span class="ewSearchField"><!--##~SYSTEMFUNCTIONS.FieldSearchLookup(bAutoPostBack)##--></span>
</div>
	<!--## if (iColCnt % iExtSearchFldPerRow == 0) { ##-->
</div>
	<!--## } ##-->
<!--##
			}; // End non-text filters

			// Extended filters
			if (IsExtendedFilter(goFld) && IsTextFilter(goFld)) {
				iColCnt += 1;
				sFldSrchOpr = goFld.FldSrchOpr;
				sFldSrchOpr2 = goFld.FldSrchOpr2;
				if (sFldSrchOpr == "BETWEEN") sFldSrchOpr2 = "";
				IsUserSelect = (goFld.FldSrchOpr == "USER SELECT" && ew_GetFieldType(goFld.FldType) != 4);
				sInitStyle = "";
				if (IsUserSelect && ew_IsEmpty(sFldSrchOpr2)) sInitStyle = " style=\"display: none\"";
##-->
	<!--##
		if ((iColCnt-1) % iExtSearchFldPerRow == 0) {
			iRowCnt += 1;
	##-->
<div id="r_<!--##=iRowCnt##-->" class="ewRow">
	<!--## } ##-->
<div id="c_<!--##=gsFldParm##-->" class="ewCell form-group">
	<label<!--##=sForLabel##--> class="ewSearchCaption ewLabel"><?php echo $<!--##=gsFldObj##-->->FldCaption() ?></label>
	<span class="ewSearchOperator"><!--##=SYSTEMFUNCTIONS.FieldOperator()##--></span>
	<span class="control-group ewSearchField"><!--##=SYSTEMFUNCTIONS.FieldSearch()##--></span>
		<!--## if (ew_IsNotEmpty(sFldSrchOpr2)) { ##-->
	<span class="ewSearchCond btw0_<!--##=gsFldParm##-->"<!--##=sInitStyle##-->><!--##=SYSTEMFUNCTIONS.FieldSearchCondition()##--></span>
		<!--## } ##-->
		<!--## if (sFldSrchOpr == "BETWEEN" || IsUserSelect) { ##-->
	<span class="ewSearchCond btw1_<!--##=gsFldParm##-->"<!--##=sInitStyle##-->><!--##@AND##--></span>
		<!--## } ##-->
		<!--## if (ew_IsNotEmpty(sFldSrchOpr2)) { ##-->
	<span class="ewSearchOperator btw0_<!--##=gsFldParm##-->"<!--##=sInitStyle##-->><!--##=SYSTEMFUNCTIONS.FieldOperator2()##--></span>
		<!--## } ##-->
		<!--## if (ew_IsNotEmpty(sFldSrchOpr2) || sFldSrchOpr == "BETWEEN" || IsUserSelect) { ##-->
	<span class="ewSearchField<!--## if (ew_IsEmpty(sFldSrchOpr2)) { ##--> btw1_<!--##=gsFldParm##--><!--## } ##-->"<!--##=sInitStyle##-->><!--##=SYSTEMFUNCTIONS.FieldSearch2()##--></span>
		<!--## } ##-->
</div>
	<!--## if (iColCnt % iExtSearchFldPerRow == 0) { ##-->
</div>
	<!--## } ##-->
<!--##
			}; // End extended filter
		}
	}; // End for
##-->
	<!--## if (iColCnt % iExtSearchFldPerRow != 0) { ##-->
</div>
	<!--## } ##-->
<!--## }; // End report extended filter ##-->

<!--## if (bReportExtFilter && !bAutoPostBack) { ##-->
<div class="ewRow"><input type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary" value="<!--##@Search##-->">
<input type="reset" name="btnreset" id="btnreset" class="btn hide" value="<!--##@Reset##-->"></div>
<!--## } ##-->

</div>

<!--## } ##-->

</form>
<script type="text/javascript">
<!--##=sFormName##-->.Init();
<!--##=sFormName##-->.FilterList = <?php echo $Page->GetFilterList() ?>;
</script>
<!-- Search form (end) -->
<!--##=sExpEnd##-->
<!--##
	};
	if (bReportExtFilter || nSearchFlds > 0) {
##-->
<?php if ($<!--##=gsPageObj##-->->ShowCurrentFilter) { ?>
<?php $<!--##=gsPageObj##-->->ShowFilterList() ?>
<?php } ?>
<!--##
	};
##-->
<!--##/session##-->

<!--##session report_drilldownlist##-->
<!--##
	if (nParms > 0) {
##-->
<?php if ($<!--##=gsPageObj##-->->ShowDrillDownFilter) { ?>
<?php $<!--##=gsPageObj##-->->ShowDrillDownList() ?>
<?php } ?>
<!--##
	};
##-->
<!--##/session##-->

<?php
<!--##session report_extfilter_function##-->
<!--##
	if (bReportExtFilter) {
##-->

	// Return extended filter
	function GetExtendedFilter() {
		global $gsFormError;

		$sFilter = "";

		if ($this->DrillDown)
			return "";

		$bPostBack = ewr_IsHttpPost();
		$bRestoreSession = TRUE;
		$bSetupFilter = FALSE;

		// Reset extended filter if filter changed
		if ($bPostBack) {
<!--##
	for (var i = 0; i < nFldCount; i++) {
		if (GetFldObj(arFlds[i])) {
			if (IsPopupFilter(goFld) && IsExtendedFilter(goFld)) {
				if (!IsTextFilter(goFld)) {
##-->
			// Set/clear dropdown for field <!--##=gsFldName##-->
			if ($this->PopupName == '<!--##=gsTblVar##-->_<!--##=gsFldParm##-->' && $this->PopupValue <> "") {
				if ($this->PopupValue == EWR_INIT_VALUE)
					$this-><!--##=gsFldParm##-->->DropDownValue = EWR_ALL_VALUE;
				else
					$this-><!--##=gsFldParm##-->->DropDownValue = $this->PopupValue;
				$bRestoreSession = FALSE; // Do not restore
			} elseif ($this->ClearExtFilter == '<!--##=gsTblVar##-->_<!--##=gsFldParm##-->') {
				$this->SetSessionDropDownValue(EWR_INIT_VALUE, '<!--##=gsFldParm##-->');
			}
<!--##
				} else {
##-->
			// Clear extended filter for field <!--##=gsFldName##-->
			if ($this->ClearExtFilter == '<!--##=gsTblVar##-->_<!--##=gsFldParm##-->')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', '<!--##=gsFldParm##-->');
<!--##
				}
			}
		}
	}
##-->

		// Reset search command
		} elseif (@$_GET["cmd"] == "reset") {

			// Load default values
<!--##
	for (var i = 0; i < nFldCount; i++) {
		if (GetFldObj(arFlds[i])) {
			sFldObj = "this->" + gsFldParm;
			if (IsExtendedFilter(goFld)) {
				if (!IsTextFilter(goFld)) {
##-->
			$this->SetSessionDropDownValue($<!--##=sFldObj##-->->DropDownValue, '<!--##=gsFldParm##-->'); // Field <!--##=gsFldName##-->
<!--##
				} else {
##-->
			$this->SetSessionFilterValues($<!--##=sFldObj##-->->SearchValue, $<!--##=sFldObj##-->->SearchOperator, $<!--##=sFldObj##-->->SearchCondition, $<!--##=sFldObj##-->->SearchValue2, $<!--##=sFldObj##-->->SearchOperator2, '<!--##=gsFldParm##-->'); // Field <!--##=gsFldName##-->
<!--##
				}
			}
		}
	}
##-->

			//$bSetupFilter = TRUE; // No need to set up, just use default

		} else {

			$bRestoreSession = !$this->SearchCommand;

<!--##
	for (var i = 0; i < nFldCount; i++) {
		if (GetFldObj(arFlds[i])) {
			sFldObj = "this->" + gsFldParm;
			if (IsExtendedFilter(goFld)) {
				if (!IsTextFilter(goFld)) {
##-->
			// Field <!--##=gsFldName##-->
			if ($this->GetDropDownValue($<!--##=sFldObj##-->->DropDownValue, '<!--##=gsFldParm##-->')) {
				$bSetupFilter = TRUE;
			} elseif ($<!--##=sFldObj##-->->DropDownValue <> EWR_INIT_VALUE && !isset($_SESSION['<!--##=pfxDdVal##--><!--##=gsSessionFldVar##-->'])) {
				$bSetupFilter = TRUE;
			}
<!--##
				} else {
##-->
			// Field <!--##=gsFldName##-->
			if ($this->GetFilterValues($<!--##=sFldObj##-->)) {
				$bSetupFilter = TRUE;
			}
<!--##
				}
			}
		}
	}
##-->

			if (!$this->ValidateForm()) {
				$this->setFailureMessage($gsFormError);
				return $sFilter;
			}

		}

		// Restore session
		if ($bRestoreSession) {

<!--##
	for (var i = 0; i < nFldCount; i++) {
		if (GetFldObj(arFlds[i])) {
			sFldObj = "this->" + gsFldParm;
			if (IsExtendedFilter(goFld)) {
				if (!IsTextFilter(goFld)) {
##-->
			$this->GetSessionDropDownValue($<!--##=sFldObj##-->); // Field <!--##=gsFldName##-->
<!--##
				} else {
##-->
			$this->GetSessionFilterValues($<!--##=sFldObj##-->); // Field <!--##=gsFldName##-->
<!--##
				}
			}
		}
	}
##-->

		}

	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Table","Page_FilterValidated")) { ##-->
		// Call page filter validated event
		$this->Page_FilterValidated();
	<!--## } ##-->

		// Build SQL
<!--##
	for (var i = 0; i < nFldCount; i++) {
		if (GetFldObj(arFlds[i])) {
			sFldObj = "this->" + gsFldParm;
			if (IsExtendedFilter(goFld)) {
				if (!IsTextFilter(goFld)) {
					if (IsDateFilter(goFld)) {
						sDropDownType = "$" + sFldObj + "->DateFilter";
					} else {
						sDropDownType = "\"\"";
					}
##-->
		$this->BuildDropDownFilter($<!--##=sFldObj##-->, $sFilter, <!--##=sDropDownType##-->, FALSE, TRUE); // Field <!--##=gsFldName##-->
<!--##
				} else {
##-->
		$this->BuildExtendedFilter($<!--##=sFldObj##-->, $sFilter, FALSE, TRUE); // Field <!--##=gsFldName##-->
<!--##
				}
			}
		}
	}
##-->

		// Save parms to session
<!--##
	for (var i = 0; i < nFldCount; i++) {
		if (GetFldObj(arFlds[i])) {
			sFldObj = "this->" + gsFldParm;
			if (IsExtendedFilter(goFld)) {
				if (!IsTextFilter(goFld)) {
##-->
		$this->SetSessionDropDownValue($<!--##=sFldObj##-->->DropDownValue, '<!--##=gsFldParm##-->'); // Field <!--##=gsFldName##-->
<!--##
				} else {
##-->
		$this->SetSessionFilterValues($<!--##=sFldObj##-->->SearchValue, $<!--##=sFldObj##-->->SearchOperator, $<!--##=sFldObj##-->->SearchCondition, $<!--##=sFldObj##-->->SearchValue2, $<!--##=sFldObj##-->->SearchOperator2, '<!--##=gsFldParm##-->'); // Field <!--##=gsFldName##-->
<!--##
				}
			}
		}
	}
##-->

		// Setup filter
		if ($bSetupFilter) {
<!--##
	for (var i = 0; i < nFldCount; i++) {
		if (GetFldObj(arFlds[i])) {
			sFldObj = "this->" + gsFldParm;
			// Skip reset filter for column date fields
			if (gsFldName == sColFldName && (sColFldDateType == "q" || sColFldDateType == "m")) {
				bGenerate = false;
			} else {
				bGenerate = (IsPopupFilter(goFld) && IsExtendedFilter(goFld));
			}
	
			if (bGenerate) {
##-->
			// Field <!--##=gsFldName##-->
<!--##
				if (!IsTextFilter(goFld)) {
					if (IsDateFilter(goFld)) {
						sDropDownType = "$" + sFldObj + "->DateFilter";
##-->
			$sWrk = "";
			$this->BuildDropDownFilter($<!--##=sFldObj##-->, $sWrk, <!--##=sDropDownType##-->);
			ewr_LoadSelectionFromFilter($<!--##=sFldObj##-->, $sWrk, $<!--##=sFldObj##-->->SelectionList, $<!--##=sFldObj##-->->DropDownValue);
<!--##
					} else if (goFld.FldSearchMultiValue) {
##-->
			ewr_LoadSelectionList($<!--##=sFldObj##-->->SelectionList, $<!--##=sFldObj##-->->DropDownValue);
<!--##
					} else {
##-->
			$sWrk = "";
			$this->BuildDropDownFilter($<!--##=sFldObj##-->, $sWrk, "");
			ewr_LoadSelectionFromFilter($<!--##=sFldObj##-->, $sWrk, $<!--##=sFldObj##-->->SelectionList, $<!--##=sFldObj##-->->DropDownValue);
<!--##
					}
				} else {
##-->
			$sWrk = "";
			$this->BuildExtendedFilter($<!--##=sFldObj##-->, $sWrk);
			ewr_LoadSelectionFromFilter($<!--##=sFldObj##-->, $sWrk, $<!--##=sFldObj##-->->SelectionList);
<!--##
				}
##-->
			$_SESSION['<!--##=pfxSel##--><!--##=gsSessionFldVar##-->'] = ($<!--##=sFldObj##-->->SelectionList == "") ? EWR_INIT_VALUE : $<!--##=sFldObj##-->->SelectionList;
<!--##
			}
		}
	}
##-->
		}

<!--##
	for (var i = 0; i < nFldCount; i++) {
		if (GetFldObj(arFlds[i])) {
			sFldObj = "this->" + gsFldParm;
			if (IsExtendedFilter(goFld)) {
				if (IsDateFilter(goFld) || !IsTextFilter(goFld)) {
					if (ew_GetFieldType(goFld.FldType) == 2) {
						sValueType = "date";
					} else {
						sValueType = "";
					}
					// Enum or Set field
					if (ew_GetFieldType(goFld.FldType) == 4 || goFld.FldTypeName == "ENUM" || goFld.FldTypeName == "SET") {
						sValueList = GetFieldValues(goFld);
						sValueList = "array(" + sValueList + ")";
##-->
		// Field <!--##=gsFldName##-->
		$<!--##=sFldObj##-->->DropDownList = <!--##=sValueList##-->;
<!--##
					} else {
##-->
		// Field <!--##=gsFldName##-->
		ewr_LoadDropDownList($<!--##=sFldObj##-->->DropDownList, $<!--##=sFldObj##-->->DropDownValue);
<!--##
					}
				}
			}
		}
	}
##-->

		return $sFilter;

	}

	// Build dropdown filter
	function BuildDropDownFilter(&$fld, &$FilterClause, $FldOpr, $Default = FALSE, $SaveFilter = FALSE) {
		$FldVal = ($Default) ? $fld->DefaultDropDownValue : $fld->DropDownValue;
		$sSql = "";
		if (is_array($FldVal)) {
			foreach ($FldVal as $val) {
				$sWrk = $this->GetDropDownFilter($fld, $val, $FldOpr);
	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Table","Page_Filtering")) { ##-->
				// Call Page Filtering event
				if (substr($val, 0, 2) <> "@@") $this->Page_Filtering($fld, $sWrk, "dropdown", $FldOpr, $val);
	<!--## } ##-->
				if ($sWrk <> "") {
					if ($sSql <> "")
						$sSql .= " OR " . $sWrk;
					else
						$sSql = $sWrk;
				}
			}
		} else {
			$sSql = $this->GetDropDownFilter($fld, $FldVal, $FldOpr);
	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Table","Page_Filtering")) { ##-->
			// Call Page Filtering event
			if (substr($FldVal, 0, 2) <> "@@") $this->Page_Filtering($fld, $sSql, "dropdown", $FldOpr, $FldVal);
	<!--## } ##-->
		}
		if ($sSql <> "") {
			ewr_AddFilter($FilterClause, $sSql);
			if ($SaveFilter) $fld->CurrentFilter = $sSql;
		}
	}

	function GetDropDownFilter(&$fld, $FldVal, $FldOpr) {
		$FldName = $fld->FldName;
		$FldExpression = $fld->FldExpression;
		$FldDataType = $fld->FldDataType;
		$FldDelimiter = $fld->FldDelimiter;
		$FldVal = strval($FldVal);
		$sWrk = "";
		if ($FldVal == EWR_NULL_VALUE) {
			$sWrk = $FldExpression . " IS NULL";
		} elseif ($FldVal == EWR_NOT_NULL_VALUE) {
			$sWrk = $FldExpression . " IS NOT NULL";
		} elseif ($FldVal == EWR_EMPTY_VALUE) {
			$sWrk = $FldExpression . " = ''";
		} elseif ($FldVal == EWR_ALL_VALUE) {
			$sWrk = "1 = 1";
		} else {
			if (substr($FldVal, 0, 2) == "@@") {
				$sWrk = $this->GetCustomFilter($fld, $FldVal);
			} elseif ($FldDelimiter <> "" && trim($FldVal) <> "") {
				$sWrk = ewr_GetMultiSearchSql($FldExpression, trim($FldVal));
			} else {
				if ($FldVal <> "" && $FldVal <> EWR_INIT_VALUE) {
					if ($FldDataType == EWR_DATATYPE_DATE && $FldOpr <> "") {
						$sWrk = ewr_DateFilterString($FldExpression, $FldOpr, $FldVal, $FldDataType);
					} else {
						$sWrk = ewr_FilterString("=", $FldVal, $FldDataType);
						if ($sWrk <> "") $sWrk = $FldExpression . $sWrk;
					}
				}
			}
		}
		return $sWrk;
	}

	// Get custom filter
	function GetCustomFilter(&$fld, $FldVal) {
		$sWrk = "";
		if (is_array($fld->AdvancedFilters)) {
			foreach ($fld->AdvancedFilters as $filter) {
				if ($filter->ID == $FldVal && $filter->Enabled) {
					$sFld = $fld->FldExpression;
					$sFn = $filter->FunctionName;
					$wrkid = (substr($filter->ID,0,2) == "@@") ? substr($filter->ID,2) : $filter->ID;
					if ($sFn <> "")
						$sWrk = $sFn($sFld);
					else
						$sWrk = "";
	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Table","Page_Filtering")) { ##-->
					$this->Page_Filtering($fld, $sWrk, "custom", $wrkid);
	<!--## } ##-->
					break;
				}
			}
		}
		return $sWrk;
	}

	// Build extended filter
	function BuildExtendedFilter(&$fld, &$FilterClause, $Default = FALSE, $SaveFilter = FALSE) {
		$sWrk = ewr_GetExtendedFilter($fld, $Default);
	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Table","Page_Filtering")) { ##-->
		if (!$Default)
			$this->Page_Filtering($fld, $sWrk, "extended", $fld->SearchOperator, $fld->SearchValue, $fld->SearchCondition, $fld->SearchOperator2, $fld->SearchValue2);
	<!--## } ##-->
		if ($sWrk <> "") {
			ewr_AddFilter($FilterClause, $sWrk);
			if ($SaveFilter) $fld->CurrentFilter = $sWrk;
		}
	}

	// Get drop down value from querystring
	function GetDropDownValue(&$sv, $parm) {
		if (ewr_IsHttpPost())
			return FALSE; // Skip post back
		if (isset($_GET["<!--##=pfxDdVal##-->$parm"])) {
			$sv = ewr_StripSlashes(@$_GET["<!--##=pfxDdVal##-->$parm"]);
			return TRUE;
		}
		return FALSE;
	}

	// Get filter values from querystring
	function GetFilterValues(&$fld) {
		$parm = substr($fld->FldVar, 2);
		if (ewr_IsHttpPost())
			return; // Skip post back
		$got = FALSE;
		if (isset($_GET["<!--##=pfxSv1##-->$parm"])) {
			$fld->SearchValue = ewr_StripSlashes(@$_GET["<!--##=pfxSv1##-->$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["<!--##=pfxSo1##-->$parm"])) {
			$fld->SearchOperator = ewr_StripSlashes(@$_GET["<!--##=pfxSo1##-->$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["<!--##=pfxSc##-->$parm"])) {
			$fld->SearchCondition = ewr_StripSlashes(@$_GET["<!--##=pfxSc##-->$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["<!--##=pfxSv2##-->$parm"])) {
			$fld->SearchValue2 = ewr_StripSlashes(@$_GET["<!--##=pfxSv2##-->$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["<!--##=pfxSo2##-->$parm"])) {
			$fld->SearchOperator2 = ewr_StripSlashes($_GET["<!--##=pfxSo2##-->$parm"]);
			$got = TRUE;
		}
		return $got;
	}

	// Set default ext filter
	function SetDefaultExtFilter(&$fld, $so1, $sv1, $sc, $so2, $sv2) {
		$fld->DefaultSearchValue = $sv1; // Default ext filter value 1
		$fld->DefaultSearchValue2 = $sv2; // Default ext filter value 2 (if operator 2 is enabled)
		$fld->DefaultSearchOperator = $so1; // Default search operator 1
		$fld->DefaultSearchOperator2 = $so2; // Default search operator 2 (if operator 2 is enabled)
		$fld->DefaultSearchCondition = $sc; // Default search condition (if operator 2 is enabled)
	}

	// Apply default ext filter
	function ApplyDefaultExtFilter(&$fld) {
		$fld->SearchValue = $fld->DefaultSearchValue;
		$fld->SearchValue2 = $fld->DefaultSearchValue2;
		$fld->SearchOperator = $fld->DefaultSearchOperator;
		$fld->SearchOperator2 = $fld->DefaultSearchOperator2;
		$fld->SearchCondition = $fld->DefaultSearchCondition;
	}

	// Check if Text Filter applied
	function TextFilterApplied(&$fld) {
		return (strval($fld->SearchValue) <> strval($fld->DefaultSearchValue) ||
			strval($fld->SearchValue2) <> strval($fld->DefaultSearchValue2) ||
			(strval($fld->SearchValue) <> "" &&
				strval($fld->SearchOperator) <> strval($fld->DefaultSearchOperator)) ||
			(strval($fld->SearchValue2) <> "" &&
				strval($fld->SearchOperator2) <> strval($fld->DefaultSearchOperator2)) ||
			strval($fld->SearchCondition) <> strval($fld->DefaultSearchCondition));
	}

	// Check if Non-Text Filter applied
	function NonTextFilterApplied(&$fld) {
		if (is_array($fld->DropDownValue)) {
			if (is_array($fld->DefaultDropDownValue)) {
				if (count($fld->DefaultDropDownValue) <> count($fld->DropDownValue))
					return TRUE;
				else
					return (count(array_diff($fld->DefaultDropDownValue, $fld->DropDownValue)) <> 0);
			} else {
				return TRUE;
			}
		} else {
			if (is_array($fld->DefaultDropDownValue))
				return TRUE;
			else
				$v1 = strval($fld->DefaultDropDownValue);
			if ($v1 == EWR_INIT_VALUE)
				$v1 = "";
			$v2 = strval($fld->DropDownValue);
			if ($v2 == EWR_INIT_VALUE || $v2 == EWR_ALL_VALUE)
				$v2 = "";
			return ($v1 <> $v2);
		}
	}

	// Get dropdown value from session
	function GetSessionDropDownValue(&$fld) {
		$parm = substr($fld->FldVar, 2);
		$this->GetSessionValue($fld->DropDownValue, '<!--##=pfxDdVal##--><!--##=gsTblVar##-->_' . $parm);
	}

	// Get filter values from session
	function GetSessionFilterValues(&$fld) {
		$parm = substr($fld->FldVar, 2);
		$this->GetSessionValue($fld->SearchValue, '<!--##=pfxSv1##--><!--##=gsTblVar##-->_' . $parm);
		$this->GetSessionValue($fld->SearchOperator, '<!--##=pfxSo1##--><!--##=gsTblVar##-->_' . $parm);
		$this->GetSessionValue($fld->SearchCondition, '<!--##=pfxSc##--><!--##=gsTblVar##-->_' . $parm);
		$this->GetSessionValue($fld->SearchValue2, '<!--##=pfxSv2##--><!--##=gsTblVar##-->_' . $parm);
		$this->GetSessionValue($fld->SearchOperator2, '<!--##=pfxSo2##--><!--##=gsTblVar##-->_' . $parm);
	}

	// Get value from session
	function GetSessionValue(&$sv, $sn) {
		if (array_key_exists($sn, $_SESSION))
			$sv = $_SESSION[$sn];
	}

	// Set dropdown value to session
	function SetSessionDropDownValue($sv, $parm) {
		$_SESSION['<!--##=pfxDdVal##--><!--##=gsTblVar##-->_' . $parm] = $sv;
	}

	// Set filter values to session
	function SetSessionFilterValues($sv1, $so1, $sc, $sv2, $so2, $parm) {
		$_SESSION['<!--##=pfxSv1##--><!--##=gsTblVar##-->_' . $parm] = $sv1;
		$_SESSION['<!--##=pfxSo1##--><!--##=gsTblVar##-->_' . $parm] = $so1;
		$_SESSION['<!--##=pfxSc##--><!--##=gsTblVar##-->_' . $parm] = $sc;
		$_SESSION['<!--##=pfxSv2##--><!--##=gsTblVar##-->_' . $parm] = $sv2;
		$_SESSION['<!--##=pfxSo2##--><!--##=gsTblVar##-->_' . $parm] = $so2;
	}

	// Check if has Session filter values
	function HasSessionFilterValues($parm) {
		return ((@$_SESSION['<!--##=pfxDdVal##-->' . $parm] <> "" && @$_SESSION['<!--##=pfxDdVal##-->' . $parm] <> EWR_INIT_VALUE) ||
			(@$_SESSION['<!--##=pfxSv1##-->' . $parm] <> "" && @$_SESSION['<!--##=pfxSv1##-->' . $parm] <> EWR_INIT_VALUE) ||
			(@$_SESSION['<!--##=pfxSv2##-->' . $parm] <> "" && @$_SESSION['<!--##=pfxSv2##-->' . $parm] <> EWR_INIT_VALUE));
	}

	// Dropdown filter exist
	function DropDownFilterExist(&$fld, $FldOpr) {
		$sWrk = "";
		$this->BuildDropDownFilter($fld, $sWrk, $FldOpr);
		return ($sWrk <> "");
	}

	// Extended filter exist
	function ExtendedFilterExist(&$fld) {
		$sExtWrk = "";
		$this->BuildExtendedFilter($fld, $sExtWrk);
		return ($sExtWrk <> "");
	}

	// Validate form
	function ValidateForm() {
		global $ReportLanguage, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EWR_SERVER_VALIDATE)
			return ($gsFormError == "");

	<!--##
		for (var i = 0; i < nFldCount; i++) {
			if (GetFldObj(arFlds[i])) {
				if (IsExtendedFilter(goFld) && IsTextFilter(goFld)) {
	##-->
		<!--##~SYSTEMFUNCTIONS.PhpValidator()##-->
	<!--##
				}
			}
		} // Field
	##-->

		// Return validate result
		$ValidateForm = ($gsFormError == "");

	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Table","Form_CustomValidate")) { ##-->
		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			$gsFormError .= ($gsFormError <> "") ? "<p>&nbsp;</p>" : "";
			$gsFormError .= $sFormCustomError;
		}
	<!--## } ##-->
	
		return $ValidateForm;

	}

<!--##
	}
##-->
<!--##
	if (bReportExtFilter || nSearchFlds > 0) {
##-->
	// Clear selection stored in session
	function ClearSessionSelection($parm) {
		$_SESSION["<!--##=pfxSel##--><!--##=gsTblVar##-->_$parm"] = "";
		$_SESSION["<!--##=pfxRangeFrom##--><!--##=gsTblVar##-->_$parm"] = "";
		$_SESSION["<!--##=pfxRangeTo##--><!--##=gsTblVar##-->_$parm"] = "";
	}

	// Load selection from session
	function LoadSelectionFromSession($parm) {
		$fld = &$this->fields($parm);
		$fld->SelectionList = @$_SESSION["<!--##=pfxSel##--><!--##=gsTblVar##-->_$parm"];
		$fld->RangeFrom = @$_SESSION["<!--##=pfxRangeFrom##--><!--##=gsTblVar##-->_$parm"];
		$fld->RangeTo = @$_SESSION["<!--##=pfxRangeTo##--><!--##=gsTblVar##-->_$parm"];
	}

	// Load default value for filters
	function LoadDefaultFilters() {

		/**
		* Set up default values for non Text filters
		*/
<!--##
	for (var i = 0; i < nFldCount; i++) {
		if (GetFldObj(arFlds[i])) {
			sFldObj = "this->" + gsFldParm;
			if (IsExtendedFilter(goFld) && !IsTextFilter(goFld)) {
				sDdDefaultValue = GetDropdownDefaultValue();
				if (IsDateFilter(goFld)) {
					sDropDownType = "$" + sFldObj + "->DateFilter";
				} else {
					sDropDownType = "\"\"";
				}
##-->
		// Field <!--##=gsFldName##-->
		$<!--##=sFldObj##-->->DefaultDropDownValue = <!--##=sDdDefaultValue##-->;
		if (!$this->SearchCommand) $<!--##=sFldObj##-->->DropDownValue = $<!--##=sFldObj##-->->DefaultDropDownValue;
<!--##
				if (IsPopupFilter(goFld)) {
##-->
		$sWrk = "";
		$this->BuildDropDownFilter($<!--##=sFldObj##-->, $sWrk, <!--##=sDropDownType##-->, TRUE);
		ewr_LoadSelectionFromFilter($<!--##=sFldObj##-->, $sWrk, $<!--##=sFldObj##-->->DefaultSelectionList);
		if (!$this->SearchCommand) $<!--##=sFldObj##-->->SelectionList = $<!--##=sFldObj##-->->DefaultSelectionList;
<!--##
				}
			}
		}
	}
##-->

		/**
		* Set up default values for extended filters
		* function SetDefaultExtFilter(&$fld, $so1, $sv1, $sc, $so2, $sv2)
		* Parameters:
		* $fld - Field object
		* $so1 - Default search operator 1
		* $sv1 - Default ext filter value 1
		* $sc - Default search condition (if operator 2 is enabled)
		* $so2 - Default search operator 2 (if operator 2 is enabled)
		* $sv2 - Default ext filter value 2 (if operator 2 is enabled)
		*/
<!--##
	for (var i = 0; i < nFldCount; i++) {
		if (GetFldObj(arFlds[i])) {
			sFldObj = "this->" + gsFldParm;
			if (IsExtendedFilter(goFld) && IsTextFilter(goFld)) {
				sDefaultValue = goFld.FldDefault;
				if (ew_IsEmpty(sDefaultValue)) sDefaultValue = "NULL";
				sDefaultSrchOpr = goFld.FldSrchOpr;
				if (ew_IsEmpty(sDefaultSrchOpr) || sDefaultSrchOpr == "IS NULL" || sDefaultSrchOpr == "IS NOT NULL") sDefaultSrchOpr = "=";
				sDefaultValue2 = goFld.FldDefault2;
				if (ew_IsEmpty(sDefaultValue2)) sDefaultValue2 = "NULL";
				sDefaultSrchOpr2 = goFld.FldSrchOpr2;
				if (sDefaultSrchOpr != "BETWEEN" && ew_IsEmpty(sDefaultSrchOpr2)) sDefaultValue2 = "NULL";
				if (ew_IsEmpty(sDefaultSrchOpr2) || sDefaultSrchOpr2 == "IS NULL" || sDefaultSrchOpr2 == "IS NOT NULL") sDefaultSrchOpr2 = "=";
##-->
		// Field <!--##=gsFldName##-->
		$this->SetDefaultExtFilter($<!--##=sFldObj##-->, "<!--##=sDefaultSrchOpr##-->", <!--##=sDefaultValue##-->, 'AND', "<!--##=sDefaultSrchOpr2##-->", <!--##=sDefaultValue2##-->);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($<!--##=sFldObj##-->);
<!--##
				if (IsPopupFilter(goFld)) {
##-->
		$sWrk = "";
		$this->BuildExtendedFilter($<!--##=sFldObj##-->, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($<!--##=sFldObj##-->, $sWrk, $<!--##=sFldObj##-->->DefaultSelectionList);
		if (!$this->SearchCommand) $<!--##=sFldObj##-->->SelectionList = $<!--##=sFldObj##-->->DefaultSelectionList;
<!--##
				}
			}
		}
	}
##-->

		/**
		* Set up default values for popup filters
		*/
<!--##
	for (var i = 0; i < nFldCount; i++) {
		if (GetFldObj(arFlds[i])) {
			sFldObj = "this->" + gsFldParm;
			if (IsPopupFilter(goFld)) {
				sDefaultSelectionList = GetSearchDefaultValue();
##-->
		// Field <!--##=gsFldName##-->
		// $<!--##=sFldObj##-->->DefaultSelectionList = array("val1", "val2");
<!--## if (ew_IsNotEmpty(sDefaultSelectionList)) { ##-->
		$<!--##=sFldObj##-->->DefaultSelectionList = <!--##=sDefaultSelectionList##-->;
		if ($<!--##=sFldObj##-->->SelectionList == "" && !$this->SearchCommand) $<!--##=sFldObj##-->->SelectionList = $<!--##=sFldObj##-->->DefaultSelectionList;
<!--## } ##-->
<!--##
			}
		}
	}
##-->

	}
<!--##
	}
##-->
<!--##
	if (bReportExtFilter || bShowYearSelection || nSearchFlds > 0) {
##-->
	// Check if filter applied
	function CheckFilter() {

<!--##
	if (bShowYearSelection) { // Column Year filter
##-->
		// Year Filter
		if (@$_SESSION["<!--##=pfxSel##--><!--##=sColDateSessionFldVar##-->"] <> "")
			return TRUE;
<!--##
	}
##-->

<!--##
	for (var i = 0; i < nFldCount; i++) {
		if (GetFldObj(arFlds[i])) {
			sFldObj = "this->" + gsFldParm;
			if (IsExtendedFilter(goFld)) {
				if (!IsTextFilter(goFld)) {
##-->
		// Check <!--##=gsFldName##--> extended filter
		if ($this->NonTextFilterApplied($<!--##=sFldObj##-->))
			return TRUE;
<!--##
				} else {
##-->
		// Check <!--##=gsFldName##--> text filter
		if ($this->TextFilterApplied($<!--##=sFldObj##-->))
			return TRUE;
<!--##
				}
			}
			if (IsPopupFilter(goFld)) {
##-->
		// Check <!--##=gsFldName##--> popup filter
		if (!ewr_MatchedArray($<!--##=sFldObj##-->->DefaultSelectionList, $<!--##=sFldObj##-->->SelectionList))
			return TRUE;
<!--##
			}
		}
	}
##-->

<!--##
	if (ew_IsNotEmpty(sColDateFldName) && !bColFldDateSelect && bColSearch) { // Column Year field (without filter)
##-->
		// Check <!--##=sColDateFldName##--> popup filter
		if (!ewr_MatchedArray($this-><!--##=sColDateFldParm##-->->DefaultSelectionList, $this-><!--##=sColDateFldParm##-->->SelectionList))
			return TRUE;
<!--##
	}
##-->

		return FALSE;

	}

	// Show list of filters
	function ShowFilterList() {
		global $ReportLanguage;

		// Initialize
		$sFilterList = "";

<!--##
	if (bShowYearSelection) { // Column Year filter
##-->
		// Year Filter
		if (strval($this-><!--##=sColDateFldParm##-->->SelectionList) <> "") {
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $ReportLanguage->Phrase("Year") . "</span>";
			$sFilterList .= "<span class=\"ewFilterValue\">" . $this-><!--##=sColDateFldParm##-->->SelectionList . "</span></div>";
		}
<!--##
	}
##-->

<!--##
	if (ew_IsNotEmpty(sColDateFldName) && !bColFldDateSelect && (sColFldDateType == "q" || sColFldDateType == "m") && bColSearch) { // Column Year field (without filter)
##-->
		// Year Filter
		if (is_array($this-><!--##=sColDateFldParm##-->->SelectionList)) {
			$sWrk = ewr_JoinArray($this-><!--##=sColDateFldParm##-->->SelectionList, ", ", EWR_DATATYPE_NUMBER);
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $ReportLanguage->Phrase("Year") . "</span>";
			$sFilterList .= "<span class=\"ewFilterValue\">$sWrk</span></div>";
		}
<!--##
	}
##-->

<!--##
	for (var i = 0; i < nFldCount; i++) {
		if (GetFldObj(arFlds[i])) {
			sFldObj = "this->" + gsFldParm;
			if (IsPopupFilter(goFld) || IsExtendedFilter(goFld)) {
##-->
		// Field <!--##=gsFldName##-->
		$sExtWrk = "";
		$sWrk = "";
<!--##
				if (IsExtendedFilter(goFld) && !IsTextFilter(goFld)) {
					if (IsDateFilter(goFld)) {
						sDropDownType = "$" + sFldObj + "->DateFilter";
					} else {
						sDropDownType = "\"\"";
					}
##-->
		$this->BuildDropDownFilter($<!--##=sFldObj##-->, $sExtWrk, <!--##=sDropDownType##-->);
<!--##
				} else if (IsExtendedFilter(goFld) && IsTextFilter(goFld)) {
##-->
		$this->BuildExtendedFilter($<!--##=sFldObj##-->, $sExtWrk);
<!--##
				}
				if (IsPopupFilter(goFld)) {
##-->
		if (is_array($<!--##=sFldObj##-->->SelectionList))
			$sWrk = ewr_JoinArray($<!--##=sFldObj##-->->SelectionList, ", ", <!--##=GetFieldTypeName(goFld.FldType)##-->);
<!--##
				}
##-->
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $<!--##=sFldObj##-->->FldCaption() . "</span>" . $sFilter . "</div>";
<!--##
			}
		}
	}
##-->

<!--## if (bUseCustomTemplate) { ##-->
		$divstyle = ($this->Export <> "") ? " style=\"display: none;\"" : "";
		$divdataclass = ($this->Export <> "") ? " data-class=\"tp_current_filters\"" : "";
<!--## } else { ##-->
		$divstyle = "";
		$divdataclass = "";
<!--## } ##-->

		// Show Filters
		if ($sFilterList <> "") {
			$sMessage = "<div class=\"ewDisplayTable\"" . $divstyle . "><div id=\"ewrFilterList\" class=\"alert alert-info\"" . $divdataclass . "><div id=\"ewrCurrentFilters\">" . $ReportLanguage->Phrase("CurrentFilters") . "</div>" . $sFilterList . "</div></div>";
	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist(sCtrlType,"Message_Showing")) { ##-->
			$this->Message_Showing($sMessage, "");
	<!--## } ##-->
			echo $sMessage;
		}

	}

	// Get list of filters
	function GetFilterList() {

		// Initialize
		$sFilterList = "";

<!--##
	if (bShowYearSelection) { // Column Year filter
##-->
		// Year Filter
		if (strval($this-><!--##=sColDateFldParm##-->->SelectionList) <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= "\"sel_<!--##=sColDateFldParm##-->\":\"" . ewr_JsEncode2($this-><!--##=sColDateFldParm##-->->SelectionList) . "\"";
		}
<!--##
	}
##-->

<!--##
	if (ew_IsNotEmpty(sColDateFldName) && !bColFldDateSelect && (sColFldDateType == "q" || sColFldDateType == "m") && bColSearch) { // Column Year field (without filter)
##-->
		// Year Filter
		if (is_array($this-><!--##=sColDateFldParm##-->->SelectionList)) {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sWrk = ewr_JoinArray($this-><!--##=sColDateFldParm##-->->SelectionList, ", ", EWR_DATATYPE_NUMBER);
			$sFilterList .= "\"sel_<!--##=sColDateFldParm##-->\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
<!--##
	}
##-->

<!--##
	for (var i = 0; i < nFldCount; i++) {
		if (GetFldObj(arFlds[i])) {
			sFldObj = "this->" + gsFldParm;
			if (IsPopupFilter(goFld) || IsExtendedFilter(goFld)) {
##-->
		// Field <!--##=gsFldName##-->
		$sWrk = "";
<!--##
				if (IsExtendedFilter(goFld) && !IsTextFilter(goFld)) {
					if (IsDateFilter(goFld)) {
						sDropDownType = "$" + sFldObj + "->DateFilter";
					} else {
						sDropDownType = "\"\"";
					}
##-->
		$sWrk = ($this-><!--##=gsFldParm##-->->DropDownValue <> EWR_INIT_VALUE) ? $this-><!--##=gsFldParm##-->->DropDownValue : "";
		if (is_array($sWrk))
			$sWrk = implode("||", $sWrk);
		if ($sWrk <> "")
			$sWrk = "\"sv_<!--##=gsFldParm##-->\":\"" . ewr_JsEncode2($sWrk) . "\"";
<!--##
				} else if (IsExtendedFilter(goFld) && IsTextFilter(goFld)) {
##-->
		if ($this-><!--##=gsFldParm##-->->SearchValue <> "" || $this-><!--##=gsFldParm##-->->SearchValue2 <> "") {
			$sWrk = "\"sv_<!--##=gsFldParm##-->\":\"" . ewr_JsEncode2($this-><!--##=gsFldParm##-->->SearchValue) . "\"," .
				"\"so_<!--##=gsFldParm##-->\":\"" . ewr_JsEncode2($this-><!--##=gsFldParm##-->->SearchOperator) . "\"," .
				"\"sc_<!--##=gsFldParm##-->\":\"" . ewr_JsEncode2($this-><!--##=gsFldParm##-->->SearchCondition) . "\"," .
				"\"sv2_<!--##=gsFldParm##-->\":\"" . ewr_JsEncode2($this-><!--##=gsFldParm##-->->SearchValue2) . "\"," .
				"\"so2_<!--##=gsFldParm##-->\":\"" . ewr_JsEncode2($this-><!--##=gsFldParm##-->->SearchOperator2) . "\"";
		}
<!--##
				}
				if (IsPopupFilter(goFld)) {
##-->
		if ($sWrk == "") {
			$sWrk = ($<!--##=sFldObj##-->->SelectionList <> EWR_INIT_VALUE) ? $<!--##=sFldObj##-->->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_<!--##=gsFldParm##-->\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
<!--##
				}
##-->
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}
<!--##
			}
		}
	}
##-->

		// Return filter list in json
		if ($sFilterList <> "")
			return "{" . $sFilterList . "}";
		else
			return "null";

	}

	// Restore list of filters
	function RestoreFilterList() {

		// Return if not reset filter
		if (@$_POST["cmd"] <> "resetfilter")
			return FALSE;

		$filter = json_decode(ewr_StripSlashes(@$_POST["filter"]), TRUE);

<!--##
	if (bShowYearSelection) { // Column Year filter
##-->
		// Year Filter
		if (array_key_exists("sel_<!--##=sColDateFldParm##-->", $filter)) {
			$ar = $filter["sel_<!--##=sColDateFldParm##-->"];
			$this-><!--##=sColDateFldParm##-->->SelectionList = $ar;
			$_SESSION["<!--##=pfxSel##--><!--##=sColDateSessionFldVar##-->"] = $ar;
		}
<!--##
	}
##-->

<!--##
	if (ew_IsNotEmpty(sColDateFldName) && !bColFldDateSelect && (sColFldDateType == "q" || sColFldDateType == "m") && bColSearch) { // Column Year field (without filter)
##-->
		// Year Filter
		if (array_key_exists("sel_<!--##=sColDateFldParm##-->", $filter)) {
			$sWrk = $filter["sel_<!--##=sColDateFldParm##-->"];
			if (strpos($sWrk, ", ") !== FALSE)
				$sWrk = explode(", ", $sWrk);
			else
				$sWrk = array($sWrk);
			$this-><!--##=sColDateFldParm##-->->SelectionList = $sWrk;
			$_SESSION["<!--##=pfxSel##--><!--##=sColDateSessionFldVar##-->"] = $sWrk;
		}
<!--##
	}
##-->

<!--##
	for (var i = 0; i < nFldCount; i++) {
		if (GetFldObj(arFlds[i])) {
			sFldObj = "this->" + gsFldParm;
			if (IsPopupFilter(goFld) || IsExtendedFilter(goFld)) {
##-->
		// Field <!--##=gsFldName##-->
		$bRestoreFilter = FALSE;
<!--##
				if (IsExtendedFilter(goFld) && !IsTextFilter(goFld)) {
##-->
		if (array_key_exists("sv_<!--##=gsFldParm##-->", $filter)) {
			$sWrk = $filter["sv_<!--##=gsFldParm##-->"];
			if (strpos($sWrk, "||") !== FALSE)
				$sWrk = explode("||", $sWrk);
			$this->SetSessionDropDownValue($sWrk, "<!--##=gsFldParm##-->");
			$bRestoreFilter = TRUE;
		}
<!--##
				} else if (IsExtendedFilter(goFld) && IsTextFilter(goFld)) {
##-->
		if (array_key_exists("sv_<!--##=gsFldParm##-->", $filter) || array_key_exists("so_<!--##=gsFldParm##-->", $filter) ||
			array_key_exists("sc_<!--##=gsFldParm##-->", $filter) ||
			array_key_exists("sv2_<!--##=gsFldParm##-->", $filter) || array_key_exists("so2_<!--##=gsFldParm##-->", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_<!--##=gsFldParm##-->"], @$filter["so_<!--##=gsFldParm##-->"], @$filter["sc_<!--##=gsFldParm##-->"], @$filter["sv2_<!--##=gsFldParm##-->"], @$filter["so2_<!--##=gsFldParm##-->"], "<!--##=gsFldParm##-->");
			$bRestoreFilter = TRUE;
		}
<!--##
				}
				if (IsPopupFilter(goFld)) {
##-->
		if (array_key_exists("sel_<!--##=gsFldParm##-->", $filter)) {
			$sWrk = $filter["sel_<!--##=gsFldParm##-->"];
			$sWrk = explode("||", $sWrk);
			$this-><!--##=gsFldParm##-->->SelectionList = $sWrk;
			$_SESSION["<!--##=pfxSel##--><!--##=gsSessionFldVar##-->"] = $sWrk;
<!--##
					if (IsExtendedFilter(goFld)) {
						if (!IsTextFilter(goFld)) {
##-->
			$this->SetSessionDropDownValue(EWR_INIT_VALUE, "<!--##=gsFldParm##-->"); // Clear drop down
<!--##
						} else {
##-->
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "<!--##=gsFldParm##-->"); // Clear extended filter
<!--##
						}
					}
##-->
			$bRestoreFilter = TRUE;
		}
<!--##
				}
##-->
		if (!$bRestoreFilter) { // Clear filter
<!--##
			if (IsExtendedFilter(goFld)) {
				if (IsTextFilter(goFld)) {
##-->
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "<!--##=gsFldParm##-->");
<!--##
				} else {
##-->
			$this->SetSessionDropDownValue(EWR_INIT_VALUE, "<!--##=gsFldParm##-->");
<!--##
				}
				if (IsPopupFilter(goFld)) {
##-->
			$this-><!--##=gsFldParm##-->->SelectionList = "";
			$_SESSION["<!--##=pfxSel##--><!--##=gsSessionFldVar##-->"] = "";
<!--##
				}
			}
##-->
		}
<!--##
			}
		}
	}
##-->

	}

<!--##
	}
##-->

	// Return popup filter
	function GetPopupFilter() {

		$sWrk = "";

		if ($this->DrillDown)
			return "";

<!--##
	for (var i = 0; i < nFldCount; i++) {
		if (GetFldObj(arFlds[i])) {
			sFldObj = "this->" + gsFldParm;
			if (IsPopupFilter(goFld)) {
				if (IsExtendedFilter(goFld)) {
					if (IsTextFilter(goFld)) {
##-->
		if (!$this->ExtendedFilterExist($<!--##=sFldObj##-->)) {
<!--##
					} else {
						if (IsDateFilter(goFld)) {
							sDropDownType = "$" + sFldObj + "->DateFilter";
						} else {
							sDropDownType = "\"\"";
						}
##-->
		if (!$this->DropDownFilterExist($<!--##=sFldObj##-->, <!--##=sDropDownType##-->)) {
<!--##
					}
				}
##-->
			if (is_array($<!--##=sFldObj##-->->SelectionList)) {
<!--##
				if (gsFldName == sColFldName && (sColFldDateType == "y" || sColFldDateType == "q" || sColFldDateType == "m")) {
					if (sColFldDateType == "y") {
						gsFld = ew_DbGrpSql("y",0).replace(/%s/g, gsFld);
					} else if (sColFldDateType == "q") {
						gsFld = ew_DbGrpSql("xq",0).replace(/%s/g, gsFld);
					} else {
						gsFld = ew_DbGrpSql("xm",0).replace(/%s/g, gsFld);
					}
##-->
				$sFilter = ewr_FilterSQL($<!--##=sFldObj##-->, "<!--##=ew_Quote(gsFld)##-->", EWR_DATATYPE_NUMBER);
<!--## } else { ##-->
				$sFilter = ewr_FilterSQL($<!--##=sFldObj##-->, "<!--##=ew_Quote(gsFld)##-->", <!--##=GetFieldTypeName(goFld.FldType)##-->);
<!--## } ##-->
	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Table","Page_Filtering")) { ##-->
				// Call Page Filtering event
				$this->Page_Filtering($<!--##=sFldObj##-->, $sFilter, "popup");
	<!--## } ##-->
				$<!--##=sFldObj##-->->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
<!--##
				if (IsExtendedFilter(goFld)) {
##-->
		}
<!--##
				}
			}
		}
	};
##-->

<!--##
    if (ew_IsNotEmpty(sColDateFldName) && !bColFldDateSelect && (sColFldDateType == "q" || sColFldDateType == "m") && bColSearch) { // Column Year field (without filter)
##-->
		// Year Filter
		if (is_array($this-><!--##=sColDateFldParm##-->->SelectionList)) {
			ewr_AddFilter($sWrk, ewr_FilterSQL($this-><!--##=sColDateFldParm##-->, "<!--##=ew_Quote(sColDateFld)##-->", EWR_DATATYPE_NUMBER));
		}
<!--##
    }
##--> 

		return $sWrk;
	}

<!--##
	if (nParms > 0) {
##-->

	// Return drill down filter
	function GetDrillDownFilter() {
		global $ReportLanguage;

		$sFilterList = "";
		$filter = "";

		$post = ewr_StripSlashes($_POST);
		$opt = @$post["d"];
		if ($opt == "1" || $opt == "2") {

			$mastertable = @$post["s"]; // Get source table

	<!--##
		for (var i = 0; i < nParms; i++) {
			if (GetFldObj(arParmFlds[i])) {
	##-->
			$sql = @$post["<!--##=gsFldParm##-->"];
			$sql = ewr_Decrypt($sql);
			$sql = str_replace("@<!--##=gsFldParm##-->", "<!--##=ew_Quote(gsFld)##-->", $sql);
			if ($sql <> "") {
				if ($filter <> "") $filter .= " AND ";
				$filter .= $sql;
				if ($sql <> "1=1")
					$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this-><!--##=gsFldParm##-->->FldCaption() . "</span><span class=\"ewFilterValue\">$sql</span></div>";
			}
	<!--##
			}
		} // Parm field
	##-->

			// Save to session
			$_SESSION[EWR_PROJECT_NAME . "_" . $this->TableVar . "_" . EWR_TABLE_MASTER_TABLE] = $mastertable;
			$_SESSION['<!--##=pfxDrOpt##--><!--##=gsTblVar##-->'] = $opt;
			$_SESSION['<!--##=pfxDrFtr##--><!--##=gsTblVar##-->'] = $filter;
			$_SESSION['<!--##=pfxDrLst##--><!--##=gsTblVar##-->'] = $sFilterList;

		} elseif (@$_GET["cmd"] == "resetdrilldown") { // Clear drill down

			$_SESSION[EWR_PROJECT_NAME . "_" . $this->TableVar . "_" . EWR_TABLE_MASTER_TABLE] = "";
			$_SESSION['<!--##=pfxDrOpt##--><!--##=gsTblVar##-->'] = "";
			$_SESSION['<!--##=pfxDrFtr##--><!--##=gsTblVar##-->'] = "";
			$_SESSION['<!--##=pfxDrLst##--><!--##=gsTblVar##-->'] = "";

		} else { // Restore from Session

			$opt = @$_SESSION['<!--##=pfxDrOpt##--><!--##=gsTblVar##-->'];
			$filter = @$_SESSION['<!--##=pfxDrFtr##--><!--##=gsTblVar##-->'];
			$sFilterList = @$_SESSION['<!--##=pfxDrLst##--><!--##=gsTblVar##-->'];

		}

		if ($opt == "1" || $opt == "2")
			$this->DrillDown = TRUE;

		if ($opt == "1") {
			$this->DrillDownInPanel = TRUE;
			$GLOBALS["gbSkipHeaderFooter"] = TRUE;
		}

		if ($filter <> "") {
			if ($sFilterList == "")
				$sFilterList = "<div><span class=\"ewFilterValue\">" . $ReportLanguage->Phrase("DrillDownAllRecords") . "</span></div>";
			$this->DrillDownList = "<div id=\"ewrDrillDownFilters\">" . $ReportLanguage->Phrase("DrillDownFilters") . "</div>" . $sFilterList;
		}

		return $filter;
	}

	// Show drill down filters
	function ShowDrillDownList() {

<!--## if (bUseCustomTemplate) { ##-->
		$divstyle = ($this->Export <> "") ? " style=\"display: none;\"" : "";
		$divdataclass = ($this->Export <> "") ? " data-class=\"tp_current_filters\"" : "";
<!--## } else { ##-->
		$divstyle = "";
		$divdataclass = "";
<!--## } ##-->

		if ($this->DrillDownList <> "") {
			$sMessage = "<div id=\"ewrDrillDownList\" class=\"ewDisplayTable\"" . $divstyle . "><div class=\"alert alert-info\"" . $divdataclass . ">" . $this->DrillDownList . "</div></div>";
	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist(sCtrlType,"Message_Showing")) { ##-->
			$this->Message_Showing($sMessage, "");
	<!--## } ##-->
			echo $sMessage;
		}

	}

<!--##
	}
##-->

<!--##
	if (bHasDrillDownFields) {
##-->

	// Return drill down SQL
	// - fld = source field object
	// - target = target field name
	// - rowtype = row type
	//  * 0 = detail
	//  * 1 = group
	//  * 2 = page
	//  * 3 = grand
	// - parm = filter/column index
	//  * -1  = use field filter value / current/old value
	//  * 0   = use grouping/column field value
	//  * > 0 = use column index
	function GetDrillDownSQL($fld, $target, $rowtype, $parm = 0) {
		$sql = "";
<!--## if (arGrpFlds.length > 0) { ##-->
		// Handle grand/page total
		if ($fld->FldVar == "<!--##=arFirstGrpFld['FldVar']##-->") { // First grouping field
			if ($rowtype == EWR_ROWTOTAL_GRAND) { // Grand total
				$sql = $fld->CurrentFilter;
				if ($sql == "")
					$sql = "1=1"; // Show all records
			} elseif ($rowtype == EWR_ROWTOTAL_PAGE && $this->PageFirstGroupFilter <> "") { // Page total
				$sql = str_replace($fld->FldExpression, "@" . $target, "(" . $this->PageFirstGroupFilter . ")");
			}
		}
<!--## } ##-->
		// Handle group/row/column field
		if ($parm >= 0 && $sql == "") {
			switch (substr($fld->FldVar,2)) {
<!--##
	for (var i = 0; i < nAllFldCount; i++) {
		if (GetFldObj(arAllFlds[i])) {
			if (IsDrillDownSource(goFld)) {
				if (gsFldName == sColFldName) {
					if (sColDateFldName != "" && (sColFldDateType == "q" || sColFldDateType == "m")) {
						var sqltype = (sColFldDateType == "q") ? "xq" : "xm";
						if (bColFldDateSelect) { // Year selection (quarter/month)
##-->
			case "<!--##=sColFldParm##-->":
				$sql = "<!--##=ew_DbGrpSql("y",0)##-->";
				$sql = str_replace("%s", "@" . $target, $sql) . " = " . ewr_QuotedValue($this-><!--##=sColDateFldParm##-->->SelectionList, EWR_DATATYPE_NUMBER);
				$colsql = "";
				if ($parm >= 1 && $parm <= $this->ColCount) {
					$colsql = "<!--##=ew_DbGrpSql(sqltype,0)##-->";
					$colsql = str_replace("%s", "@" . $target, $colsql) . " = " . ewr_QuotedValue($this->Col[$parm]->Value, EWR_DATATYPE_NUMBER);
				}
				ewr_AddFilter($sql, $colsql);
				break;
<!--##
						} else { // Without year selection (quarter/month)
##-->
			case "<!--##=sColFldParm##-->":
				if ($rowtype == 0) { // Add year filter for detail record
					$sql = "<!--##=ew_DbGrpSql("y",0)##-->";
					$sql = str_replace("%s", "@" . $target, $sql) . " = " . ewr_QuotedValue($this-><!--##=sColDateFldParm##-->->CurrentValue, EWR_DATATYPE_NUMBER);
				} elseif (is_array($this-><!--##=sColDateFldParm##-->->SelectionList)) { // Year popup filter
					$sql = "<!--##=ew_DbGrpSql("y",0)##-->";
					$sql = str_replace("%s", "@" . $target, $sql) . " IN (" . ewr_JoinArray($this-><!--##=sColDateFldParm##-->->SelectionList, ", ", EWR_DATATYPE_NUMBER) . ")";
				}
				$colsql = "";
				if ($parm >= 1 && $parm <= $this->ColCount) {
					$colsql = "<!--##=ew_DbGrpSql(sqltype,0)##-->";
					$colsql = str_replace("%s", "@" . $target, $colsql) . " = " . ewr_QuotedValue($this->Col[$parm]->Value, EWR_DATATYPE_NUMBER);
				}
				ewr_AddFilter($sql, $colsql);
				break;
			case "<!--##=sColDateFldParm##-->":
				$sql = "<!--##=ew_DbGrpSql("y",0)##-->";
				$sql = str_replace("%s", "@" . $target, $sql) . " = " . ewr_QuotedValue($fld->CurrentValue, EWR_DATATYPE_NUMBER);
				break;
<!--##
						}
					} else if (sColFldDateType == "y") { // Year
##-->
			case "<!--##=gsFldParm##-->":
				if ($parm >= 1 && $parm <= $this->ColCount) {
					$sql = "<!--##=ew_DbGrpSql("y",0)##-->";
					$sql = str_replace("%s", "@" . $target, $sql) . " = " . ewr_QuotedValue($this->Col[$parm]->Value, EWR_DATATYPE_NUMBER);
				}
				break;
<!--##
					} else { // Non date column field
##-->
			case "<!--##=gsFldParm##-->":
				if ($parm >= 1 && $parm <= $this->ColCount) {
					$sql = "@" . $target . " = " . ewr_QuotedValue($this->Col[$parm]->Value, <!--##=GetFieldTypeName(sColFldType)##-->);
				}
				break;
<!--##
					} // End column field
				} else {
					var bIsGroupField = false;
					for (var j = 0; j < nGrps; j++) {
						if (arGrpFlds[j]['FldName'] == gsFldName) {
							bIsGroupField = true;
							break;
						}
					}
					if (bIsGroupField) { // Grouping field
##-->
			case "<!--##=gsFldParm##-->":
				if ($fld->FldGroupSql <> "") {
					$sql = str_replace("%s", "@" . $target, $fld->FldGroupSql) . " = " . ewr_QuotedValue(($rowtype == 0) ? $fld->CurrentValue : $fld->OldValue, EWR_DATATYPE_STRING);
					ewr_AddFilter($sql, str_replace($fld->FldExpression, "@" . $target, $fld->CurrentFilter));
				} else {
					$sql = "@" . $target . " = " . ewr_QuotedValue(($rowtype == 0) ? $fld->CurrentValue : $fld->OldValue, $fld->FldDataType);
				}
				break;
<!--##
					}
				}
			}
		}
	};
##-->
			}
		}
		// Detail field
		if ($sql == "" && $rowtype == 0)
			if ($fld->CurrentFilter <> "") // Use current filter
				$sql = str_replace($fld->FldExpression, "@" . $target, $fld->CurrentFilter);
			elseif ($fld->CurrentValue <> "") // Use current value for detail row
				$sql = "@" . $target . "=" . ewr_QuotedValue($fld->CurrentValue, $fld->FldDataType);
		return $sql;
	}

<!--##
	}
##-->

<!--##/session##-->
?>

<?php
<!--##session phpevents##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Table","Form_CustomValidate")##-->
<!--##/session##-->
?>