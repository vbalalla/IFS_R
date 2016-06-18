<?php

// Global variable for table object
$Job_Position_Vs_Number_of_Interviews = NULL;

//
// Table class for Job Position Vs Number of Interviews
//
class crJob_Position_Vs_Number_of_Interviews extends crTableCrosstab {

//	var $SelectLimit = TRUE;
	var $Job_Positions_Vs_Number_of_Interviews;
	var $dateCreated;
	var $jbName;
	var $Number_Of_Interviews;
	var $YEAR__dateCreated;

	//
	// Table class constructor
	//
	function __construct() {
		global $ReportLanguage;
		$this->TableVar = 'Job_Position_Vs_Number_of_Interviews';
		$this->TableName = 'Job Position Vs Number of Interviews';
		$this->TableType = 'REPORT';
		$this->ExportAll = TRUE;
		$this->ExportPageBreakCount = 0;

		// dateCreated
		$this->dateCreated = new crField('Job_Position_Vs_Number_of_Interviews', 'Job Position Vs Number of Interviews', 'x_dateCreated', 'dateCreated', '`dateCreated`', 133, EWR_DATATYPE_DATE, 5);
		$this->dateCreated->FldDefaultErrMsg = str_replace("%s", "/", $ReportLanguage->Phrase("IncorrectDateYMD"));
		$this->fields['dateCreated'] = &$this->dateCreated;
		$this->dateCreated->DateFilter = "";
		$this->dateCreated->SqlSelect = "";
		$this->dateCreated->SqlOrderBy = "";

		// jbName
		$this->jbName = new crField('Job_Position_Vs_Number_of_Interviews', 'Job Position Vs Number of Interviews', 'x_jbName', 'jbName', '`jbName`', 200, EWR_DATATYPE_STRING, -1);
		$this->jbName->GroupingFieldId = 1;
		$this->fields['jbName'] = &$this->jbName;
		$this->jbName->DateFilter = "";
		$this->jbName->SqlSelect = "";
		$this->jbName->SqlOrderBy = "";

		// Number_Of_Interviews
		$this->Number_Of_Interviews = new crField('Job_Position_Vs_Number_of_Interviews', 'Job Position Vs Number of Interviews', 'x_Number_Of_Interviews', 'Number_Of_Interviews', '`Number_Of_Interviews`', 20, EWR_DATATYPE_NUMBER, -1);
		$this->Number_Of_Interviews->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['Number_Of_Interviews'] = &$this->Number_Of_Interviews;
		$this->Number_Of_Interviews->DateFilter = "";
		$this->Number_Of_Interviews->SqlSelect = "";
		$this->Number_Of_Interviews->SqlOrderBy = "";

		// YEAR__dateCreated
		$this->YEAR__dateCreated = new crField('Job_Position_Vs_Number_of_Interviews', 'Job Position Vs Number of Interviews', 'x_YEAR__dateCreated', 'YEAR__dateCreated', 'YEAR(`dateCreated`)', 3, EWR_DATATYPE_NUMBER, 0, FALSE);
		$this->fields['YEAR__dateCreated'] = &$this->YEAR__dateCreated;
		$this->YEAR__dateCreated->SqlSelect = "SELECT DISTINCT YEAR(`dateCreated`) FROM " . $this->getSqlFrom();
		$this->YEAR__dateCreated->SqlOrderBy = "YEAR(`dateCreated`)";

		// Job Positions Vs Number of Interviews
		$this->Job_Positions_Vs_Number_of_Interviews = new crChart('Job_Position_Vs_Number_of_Interviews', 'Job Position Vs Number of Interviews', 'Job_Positions_Vs_Number_of_Interviews', 'Job Positions Vs Number of Interviews', 'jbName', 'Number_Of_Interviews', '', 5, 'SUM', 900, 500);
		$this->Job_Positions_Vs_Number_of_Interviews->SqlSelect = "SELECT `jbName`, '', SUM(`Number_Of_Interviews`) FROM ";
		$this->Job_Positions_Vs_Number_of_Interviews->SqlGroupBy = "`jbName`";
		$this->Job_Positions_Vs_Number_of_Interviews->SqlOrderBy = "";
		$this->Job_Positions_Vs_Number_of_Interviews->SeriesDateType = "";
	}

	// Single column sort
	function UpdateSort(&$ofld) {
		if ($this->CurrentOrder == $ofld->FldName) {
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
		} else {
			if ($ofld->GroupingFieldId == 0) $ofld->setSort("");
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

	// Table level SQL
	// Column field

	var $ColumnField = "";

	function getColumnField() {
		return ($this->ColumnField <> "") ? $this->ColumnField : "`dateCreated`";
	}

	function setColumnField($v) {
		$this->ColumnField = $v;
	}

	// Column date type
	var $ColumnDateType = "";

	function getColumnDateType() {
		return ($this->ColumnDateType <> "") ? $this->ColumnDateType : "q";
	}

	function setColumnDateType($v) {
		$this->ColumnDateType = $v;
	}

	// Summary field
	var $SummaryField = "";

	function getSummaryField() {
		return ($this->SummaryField <> "") ? $this->SummaryField : "`Number_Of_Interviews`";
	}

	function setSummaryField($v) {
		$this->SummaryField = $v;
	}

	// Summary type
	var $SummaryType = "";

	function getSummaryType() {
		return ($this->SummaryType <> "") ? $this->SummaryType : "SUM";
	}

	function setSummaryType($v) {
		$this->SummaryType = $v;
	}

	// Column captions
	var $ColumnCaptions = "";

	function getColumnCaptions() {
		global $ReportLanguage;
		return ($this->ColumnCaptions <> "") ? $this->ColumnCaptions : $ReportLanguage->Phrase("Qtr1") . "," . $ReportLanguage->Phrase("Qtr2") . "," . $ReportLanguage->Phrase("Qtr3") . "," . $ReportLanguage->Phrase("Qtr4");
	}

	function setColumnCaptions($v) {
		$this->ColumnCaptions = $v;
	}

	// Column names
	var $ColumnNames = "";

	function getColumnNames() {
		return ($this->ColumnNames <> "") ? $this->ColumnNames : "Qtr1,Qtr2,Qtr3,Qtr4";
	}

	function setColumnNames($v) {
		$this->ColumnNames = $v;
	}

	// Column values
	var $ColumnValues = "";

	function getColumnValues() {
		return ($this->ColumnValues <> "") ? $this->ColumnValues : "1,2,3,4";
	}

	function setColumnValues($v) {
		$this->ColumnValues = $v;
	}

	// From
	var $_SqlFrom = "";

	function getSqlFrom() {
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`jobpositionvsinterviews`";
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
		return ($this->_SqlSelect <> "") ? $this->_SqlSelect : "SELECT `jbName`, YEAR(`dateCreated`) AS `YEAR__dateCreated`, <DistinctColumnFields> FROM " . $this->getSqlFrom();
	}

	function SqlSelect() { // For backward compatibility
		return $this->getSqlSelect();
	}

	function setSqlSelect($v) {
		$this->_SqlSelect = $v;
	}

	// Where
	var $_SqlWhere = "";

	function getSqlWhere() {
		$sWhere = ($this->_SqlWhere <> "") ? $this->_SqlWhere : "";
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
		return ($this->_SqlGroupBy <> "") ? $this->_SqlGroupBy : "`jbName`, YEAR(`dateCreated`)";
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
		return ($this->_SqlOrderBy <> "") ? $this->_SqlOrderBy : "`jbName` ASC, YEAR(`dateCreated`)";
	}

	function SqlOrderBy() { // For backward compatibility
		return $this->getSqlOrderBy();
	}

	function setSqlOrderBy($v) {
		$this->_SqlOrderBy = $v;
	}

	// Crosstab Year
	var $_SqlCrosstabYear = "";

	function getSqlCrosstabYear() {
		return ($this->_SqlCrosstabYear <> "") ? $this->_SqlCrosstabYear : "SELECT DISTINCT YEAR(`dateCreated`) AS `YEAR__dateCreated` FROM `jobpositionvsinterviews` ORDER BY YEAR(`dateCreated`)";
	}

	function SqlCrosstabYear() { // For backward compatibility
		return $this->getSqlCrosstabYear();
	}

	function setSqlCrosstabYear($v) {
		$this->_SqlCrosstabYear = $v;
	}
	var $ColCount;
	var $Col;
	var $DistinctColumnFields = "";

	// Load column values
	function LoadColumnValues($filter = "") {
		global $conn;
		global $ReportLanguage;
		$arColumnCaptions = explode(",", $this->getColumnCaptions());
		$arColumnNames = explode(",", $this->getColumnNames());
		$arColumnValues = explode(",", $this->getColumnValues());

		// Get distinct column count
		$this->ColCount = count($arColumnNames);
		$this->Col = &ewr_Init2DArray($this->ColCount+1, 2, NULL);
		for ($colcnt = 1; $colcnt <= $this->ColCount; $colcnt++) {
			$this->Col[$colcnt] = new crCrosstabColumn($arColumnValues[$colcnt-1], $arColumnCaptions[$colcnt-1], TRUE);
		}

		// Update crosstab sql
		$sSqlFlds = "";
		for ($i = 0; $i < $this->ColCount; $i++) {
			$sFld = ewr_CrossTabField($this->getSummaryType(), $this->getSummaryField(),
				$this->getColumnField(), $this->getColumnDateType(), $arColumnValues[$i], "", $arColumnNames[$i]);
			if ($sSqlFlds <> "")
				$sSqlFlds .= ", ";
			$sSqlFlds .= $sFld;
		}
		$this->DistinctColumnFields = $sSqlFlds;
	}

	// Get chart sql
	function GetChartColumnSql() {

		// Update chart sql if Y Axis = Column Field
		$arColumnValues = explode(",", $this->getColumnValues());
		$SqlChartWork = "";
		for ($i = 0; $i < $this->ColCount; $i++) {
			if ($this->Col[$i+1]->Visible) {
				$sChtFld = ewr_CrossTabField("SUM", $this->getSummaryField(), $this->getColumnField(), $this->getColumnDateType(), $arColumnValues[$i], "");
				if ($SqlChartWork != "") $SqlChartWork .= "+";
				$SqlChartWork .= $sChtFld;
			}
		}
		if ($SqlChartWork == "") $SqlChartWork = "0";
		return $SqlChartWork;
	}

	// Table Level Group SQL
	// First Group Field

	var $_SqlFirstGroupField = "";

	function getSqlFirstGroupField() {
		return ($this->_SqlFirstGroupField <> "") ? $this->_SqlFirstGroupField : "`jbName`";
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
		return ($this->_SqlOrderByGroup <> "") ? $this->_SqlOrderByGroup : "`jbName` ASC";
	}

	function SqlOrderByGroup() { // For backward compatibility
		return $this->getSqlOrderByGroup();
	}

	function setSqlOrderByGroup($v) {
		$this->_SqlOrderByGroup = $v;
	}

	// Select Aggregate
	var $_SqlSelectAgg = "";

	function getSqlSelectAgg() {
		return ($this->_SqlSelectAgg <> "") ? $this->_SqlSelectAgg : "SELECT YEAR(`dateCreated`) AS `YEAR__dateCreated`, <DistinctColumnFields> FROM " . $this->getSqlFrom();
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
		return ($this->_SqlGroupByAgg <> "") ? $this->_SqlGroupByAgg : "YEAR(`dateCreated`)";
	}

	function SqlGroupByAgg() { // For backward compatibility
		return $this->getSqlGroupByAgg();
	}

	function setSqlGroupByAgg($v) {
		$this->_SqlGroupByAgg = $v;
	}

	// Sort URL
	function SortUrl(&$fld) {
		return "";
	}

	// Table level events
	// Page Selecting event
	function Page_Selecting(&$filter) {

		// Enter your code here	
	}

	// Page Breaking event
	function Page_Breaking(&$break, &$content) {

		// Example:
		//$break = FALSE; // Skip page break, or
		//$content = "<div style=\"page-break-after:always;\">&nbsp;</div>"; // Modify page break content

	}

	// Row Rendering event
	function Row_Rendering() {

		// Enter your code here	
	}

	// Cell Rendered event
	function Cell_Rendered(&$Field, $CurrentValue, &$ViewValue, &$ViewAttrs, &$CellAttrs, &$HrefValue, &$LinkAttrs) {

		//$ViewValue = "xxx";
		//$ViewAttrs["style"] = "xxx";

	}

	// Row Rendered event
	function Row_Rendered() {

		// To view properties of field class, use:
		//var_dump($this-><FieldName>); 

	}

	// User ID Filtering event
	function UserID_Filtering(&$filter) {

		// Enter your code here
	}

	// Load Filters event
	function Page_FilterLoad() {

		// Enter your code here
		// Example: Register/Unregister Custom Extended Filter
		//ewr_RegisterFilter($this-><Field>, 'StartsWithA', 'Starts With A', 'GetStartsWithAFilter'); // With function, or
		//ewr_RegisterFilter($this-><Field>, 'StartsWithA', 'Starts With A'); // No function, use Page_Filtering event
		//ewr_UnregisterFilter($this-><Field>, 'StartsWithA');

	}

	// Page Filter Validated event
	function Page_FilterValidated() {

		// Example:
		//$this->MyField1->SearchValue = "your search criteria"; // Search value

	}

	// Page Filtering event
	function Page_Filtering(&$fld, &$filter, $typ, $opr = "", $val = "", $cond = "", $opr2 = "", $val2 = "") {

		// Note: ALWAYS CHECK THE FILTER TYPE ($typ)! Example:
		// if ($typ == "dropdown" && $fld->FldName == "MyField") // Dropdown filter
		//     $filter = "..."; // Modify the filter
		// if ($typ == "extended" && $fld->FldName == "MyField") // Extended filter
		//     $filter = "..."; // Modify the filter
		// if ($typ == "popup" && $fld->FldName == "MyField") // Popup filter
		//     $filter = "..."; // Modify the filter
		// if ($typ == "custom" && $opr == "..." && $fld->FldName == "MyField") // Custom filter, $opr is the custom filter ID
		//     $filter = "..."; // Modify the filter

	}

	// Email Sending event
	function Email_Sending(&$Email, &$Args) {

		//var_dump($Email); var_dump($Args); exit();
		return TRUE;
	}

	// Lookup Selecting event
	function Lookup_Selecting($fld, &$filter) {

		// Enter your code here
	}
}
?>
