<!--##session userfunction##-->
<?php
// Global user functions

// Filter for 'Last Month' (example)
function GetLastMonthFilter($FldExpression) {
	$today = getdate();
	$lastmonth = mktime(0, 0, 0, $today['mon']-1, 1, $today['year']);
	$sVal = date("Y|m", $lastmonth);
	$sWrk = $FldExpression . " BETWEEN " .
		ewr_QuotedValue(ewr_DateVal("month", $sVal, 1), EWR_DATATYPE_DATE) .
		" AND " .
		ewr_QuotedValue(ewr_DateVal("month", $sVal, 2), EWR_DATATYPE_DATE);
	return $sWrk;
}

// Filter for 'Starts With A' (example)
function GetStartsWithAFilter($FldExpression) {
	return $FldExpression . ewr_Like("'A%'");
}

<!--##~SYSTEMFUNCTIONS.GetServerScript("Global","Page_Loading")##-->
<!--##~SYSTEMFUNCTIONS.GetServerScript("Global","Page_Rendering")##-->
<!--##~SYSTEMFUNCTIONS.GetServerScript("Global","Page_Unloaded")##-->
<!--##~SYSTEMFUNCTIONS.GetServerScript("Global","Global Code")##-->
?>
<!--##/session##-->