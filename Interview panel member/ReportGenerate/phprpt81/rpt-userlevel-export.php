<!--##session userlevel##-->
<?php

define("EW_REPORT_TABLE_PREFIX", "<!--##=pfxUserLevel##-->", TRUE);
define("EW_REPORT_LANGUAGE_FOLDER", "<!--##=sLanguageFolder##-->", TRUE);

<!--##
	if (bGenCompatHeader && bSecurityEnabled) {
	
		if (bStaticUserLevel) {
##-->
// Function to set up static User Level Security for reports
function SetUpReportUserLevel(&$ar) {
	// User Level definitions for reports
<!--##
			for (var i = 1, len = DB.Tables.Count(); i <= len; i++) {
				TABLE = DB.Tables.Seq(i);
				sTblName = TABLE.TblName;
				sTblSec = TABLE.TblSecurity;
				arGroup = sTblSec.split(";");
				for (var j = 0; j < arGroup.length; j++) {
					arLvl = arGroup[j].split(",");
					iUserLevelID = arLvl[0];
					iUserLevelName = arLvl[1];
					iUserLevel = arLvl[2];
##-->
	$ar[] = array(EW_REPORT_TABLE_PREFIX . "<!--##=sTblName##-->", <!--##=iUserLevelID##-->, <!--##=iUserLevel##-->);
<!--##
				}
			}
##-->
}
<!--##
		} else {
##-->
// Function to set up static User Level Security for reports
function SetUpReportUserLevel(&$ar) {
	// No User Level definitions for reports
}
<!--##
		}

		if (bDynamicUserLevel) {
##-->
// Dynamic User Level settings
<!--##
			for (var i = 1, len = DB.Tables.Count(); i <= len; i++) {
				TABLE = DB.Tables.Seq(i);
				if (TABLE.TblLoaded) {
					sTblName = TABLE.TblName;
					sTblVar = TABLE.TblVar;
					sTblCaption = TABLE.TblCaption;
##-->
$EW_USER_LEVEL_TABLE_NAME[] = EW_REPORT_TABLE_PREFIX . '<!--##=ew_SQuote(sTblName)##-->';
$EW_USER_LEVEL_TABLE_VAR[] = '<!--##=ew_SQuote(sTblVar)##-->';
$EW_USER_LEVEL_TABLE_CAPTION[] = '<!--##=ew_SQuote(sTblCaption)##-->';
<!--##
				}
			}

		} else {
##-->
// No Dynamic User Level Settings
<!--##
		}

	}
##-->
?>
<!--##/session##-->