/*
 *** -----------------------------------------
 ***  IMPORTANT - DO NOT CHANGE
 ***

 ***********************
 * Common variables
 */

// Global system variable: bDBMsAccess, bDBMsSql, bDBMySql, bDBPostgreSql, bDBOracle

// Table level variables - set up in rpt-phpcommon-table.php
var gsTblVar;
var gsTblName;

// Chart level variables
var goChts;
var goCht;
var gsChartName;
var gsChartVar;
var gsChartObj;

// Chart array
var arAllCharts;

// Field level variables
var goFlds;
var goFld;
var gsFld;
var gsFldQuoteS, gsFldQuoteE;
var gsFldName;
var gsFldVar;
var gsFldParm;
var gsFldObj;
var gsSessionFldVar;

// Field arrays
var arKeyFlds;
var arFlds;
var arAllFlds;

// Dropdown filter
var gsDdValFldVar, pfxDdVal = "sv_";
var gsDdDefaultValue;

// Extended filter related
var gsSv1FldVar, pfxSv1 = "sv_"; // Don't change, must be the same as pfxDdVal
var gsSv2FldVar, pfxSv2 = "sv2_";
var gsSo1FldVar, pfxSo1 = "so_"; // Don't change
var gsSo2FldVar, pfxSo2 = "so2_";
var gsScFldVar, pfxSc = "sc_";

// Drill down filter related
var pfxDrOpt = "do_";
var pfxDrFtr = "df_";
var pfxDrLst = "dl_";

// Reports
var pfxRangeFrom = "rf_";
var pfxRangeTo = "rt_";
var pfxSel = "sel_";

// Popup filter
var gsPopupFldVar, sfxPopup = "_Popup";

// User level prefix
var pfxUserLevel = ReadReg("HKCU\\Software\\PHPReportMaker\\8.0\\Settings\\General\\UserLevelTableNamePrefix");
if (pfxUserLevel == "") pfxUserLevel = ReadReg("HKCU\\Software\\PHPReportMaker\\7.0\\Settings\\General\\UserLevelTableNamePrefix");
if (pfxUserLevel == "") pfxUserLevel = "||PHPReportMaker||";

/**
 ************************
 * Commonly used functions
 */

// Read registry
function ReadReg(RegPath) {
	try {
		var obj = new ActiveXObject("WScript.Shell");
		return obj.RegRead(RegPath);
	} catch(e) {
		return "";
	}
}

// Convert Data
function ConvertData(v, t) {
	try {
		switch (t.toLowerCase()) {
			case "boolean": if (String(v) == "1" || v === true) return 1; else return 0;
			case "integer": return int(v);
			case "long": return int(v);
			case "single": return float(v);
			case "double": return double(v);
			default: return v;
		}
	} catch(e) {
		return v;
	}
}

// Get chart object
function GetChtObj(chtname) {
	CHART = TABLE.Charts(chtname);
	goCht = goChts[chtname];
	if (goCht) {
		gsChartVar = goCht.ChartVar;
		gsChartName = goCht.ChartName;
	} else {
		gsChartVar = CHART.ChartVar;
		gsChartName = CHART.ChartName;
	}
	//gsChartObj = gsTblVar + "->" + gsChartVar;
	gsChartObj = gsPageObj + "->" + gsChartVar;

	return true;
}

function GetFieldTypeName(FldTyp) {
	switch (FldTyp) {
		//Case adBigInt, adInteger, adSmallInt, adTinyInt, adSingle, adDouble, adNumeric, adCurrency, adUnsignedTinyInt, adUnsignedSmallInt, adUnsignedInt, adUnsignedBigInt, 139
		case 20:
		case 3:
		case 2:
		case 16:
		case 4:
		case 5:
		case 131:
		case 6:
		case 17:
		case 18:
		case 19:
		case 21:
		case 139:
			return "EWR_DATATYPE_NUMBER";
		//Case adDate, adDBDate, adDBTimeStamp, 146
		case 7:
		case 133:
		case 135:
		case 146:
			return "EWR_DATATYPE_DATE";
		//Case adDBTime, 145
		case 134:
		case 145:
			return "EWR_DATATYPE_TIME";
		//Case adLongVarChar, adLongVarWChar
		case 201:
		case 203:
			return "EWR_DATATYPE_MEMO";
		//Case adChar, adWChar, adVarChar, adVarWChar, 141
		case 129:
		case 130:
		case 200:
		case 202:
		case 141:
			return "EWR_DATATYPE_STRING";
		//Case adBoolean
		case 11:
			return "EWR_DATATYPE_BOOLEAN";
		//Case adGUID
		case 72:
			return "EWR_DATATYPE_GUID";
		//Case adBinary, adVarBinary, adLongVarBinary
		case 128:
		case 204:
		case 205:
			return "EWR_DATATYPE_BLOB";
		default:
			return "EWR_DATATYPE_OTHER";
	}
}

// Get field object
function GetFldObj(fldname) {
	FIELD = TABLE.Fields(fldname);
	goFld = goFlds[fldname];
	if (goFld) {
		gsFldParm = goFld.FldParm;
		gsFldVar = goFld.FldVar;
		gsFldName = goFld.FldName;
		gsFld = ew_FieldSqlName(goFld);
		gsFldQuoteS = goFld.FldQuoteS;
		gsFldQuoteE = goFld.FldQuoteE;
	} else {
		gsFldParm = FIELD.FldParm;
		gsFldVar = FIELD.FldVar;
		gsFldName = FIELD.FldName;
		gsFld = ew_FieldSqlName(FIELD);
		gsFldQuoteS = FIELD.FldQuoteS;
		gsFldQuoteE = FIELD.FldQuoteE;
	}
	//gsFldObj = gsTblVar + "->" + gsFldParm;
	gsFldObj = gsPageObj + "->" + gsFldParm;
	gsSessionFldVar = gsTblVar + "_" + gsFldParm;

	// Dropdown filter
	gsDdValFldVar = pfxDdVal + gsFldParm;

	// Extended filter related
	gsSv1FldVar = pfxSv1 + gsFldParm;
	gsSv2FldVar = pfxSv2 + gsFldParm;
	gsSo1FldVar = pfxSo1 + gsFldParm;
	gsSo2FldVar = pfxSo2 + gsFldParm;
	gsScFldVar = pfxSc + gsFldParm;

	// Report
	gsPopupFldVar = gsSessionFldVar + sfxPopup;

	return true;
}

function GetFldVal(fld, fldtype) {
	if (bDBMySql) {
		return fld;
	} else {
		if (ew_GetFieldType(fldtype) == 4) {
			return "((" + fld + ") ? \"1\" : \"0\")";
		} else if (fldtype == 18 || fldtype == 19 || fldtype == 131 || fldtype == 139) {
			return "ewr_Conv(" + fld + ", " + fldtype + ")";
		} else {
			return fld;
		}
	}
}

function GetSearchDefaultValue() {
	var sDefaultValue = goFld.FldDefaultSearch;
	if (ew_IsEmpty(sDefaultValue)) {
		sDefaultValue = "";
	} else if (!IsArrayString(sDefaultValue)) {
		sDefaultValue = "array(" + sDefaultValue + ")";
	}
	return sDefaultValue;
}

function GetDropdownDefaultValue() {
	var sDdDefaultValue = goFld.FldDefault;
	var sYr = goFld.FldDateSearchDefaultYear;
	var sQtr = goFld.FldDateSearchDefaultQuarter;
	var sMth = goFld.FldDateSearchDefaultMonth;
	var sDy = goFld.FldDateSearchDefaultDay;
	switch (goFld.FldDateSearch.toLowerCase()) {
		case "year":
			if (ew_IsNotEmpty(sYr)) {
				sDdDefaultValue = sYr;
			}
			break;
		case "quarter":
			if (ew_IsNotEmpty(sYr) && ew_IsNotEmpty(sQtr)) {
				if (!isNaN(sYr) && !isNaN(sQtr)) {
					sDdDefaultValue = "\"" + sYr + "|" + sQtr + "\"";
				} else {
					sDdDefaultValue = sYr + " . \"|\" . " + sQtr;
				}
			}
			break;
		case "month":
			if (ew_IsNotEmpty(sYr) && ew_IsNotEmpty(sMth)) {
				if (!isNaN(sYr) && !isNaN(sMth)) {
					sDdDefaultValue = "\"" + sYr + "|" + sMth.pad("0", 2) + "\"";
				} else {
					sDdDefaultValue = sYr + " . \"|\" . " + sMth;
				}
			}
			break;
		case "day":
			if (ew_IsNotEmpty(sYr) && ew_IsNotEmpty(sMth) && ew_IsNotEmpty(sDy)) {
				if (!isNaN(sYr) && !isNaN(sMth) && !isNaN(sDy)) {
					sDdDefaultValue = "\"" + sYr + "|" + sMth.pad("0", 2) + "|" + sDy.pad("0", 2) + "\"";
				} else {
					sDdDefaultValue = sYr + " . \"|\" . " + sMth + " . \"|\" . " + sDy;
				}
			}
			break;
		default:
			if (goFld.FldHtmlTag == "CHECKBOX" || (goFld.FldHtmlTag == "SELECT" && goFld.FldSelectMultiple)) {
				if (ew_IsNotEmpty(sDdDefaultValue) && ew_ContainText(sDdDefaultValue, ",") && !IsArrayString(sDdDefaultValue)) {
					sDdDefaultValue = "array(" + sDdDefaultValue + ")";
				}
			}
	}
	if (ew_IsEmpty(sDdDefaultValue)) sDdDefaultValue = "EWR_INIT_VALUE";
	return sDdDefaultValue;
}

// String is array(...) or array[...]
function IsArrayString(str) {
	if (/array\([^\)]*\)$/.test(str.trim()) || /\[[^\]]*\]$/.test(str.trim()))
		return true;
	else
		return false;
}

// Check if is aggregate
function IsAggregateSql(sql) {
	var wrksql = sql.trim();
	var i = wrksql.indexOf("(");
	if (i >= 0) {
		wrksql = wrksql.substr(0,i-1).toUpperCase();
		if (wrksql == "AVG" || wrksql == "COUNT" || wrksql == "MAX" || wrksql == "MIN" || wrksql == "SUM") {
			return true;
		}
	}
	return false;
}

// Return field value list
function GetFieldValues(f) {
	var values = f.FldTagValues;
	var list = "";
	var val = "";
	if (SYSTEMFUNCTIONS.IsBoolFld() && ew_GetFieldHtmlTag(f) == "CHECKBOX") {
		var ar = values.split("\r\n");
		for (var i = 0; i < ar.length; i++) {
			val = ar[i].split(",")[0];
			val = ew_UnQuote(val);
			if (val == "1" || val.toUpperCase() == "Y" || val.toUpperCase() == "YES" || val.toUpperCase() == "T" || val.toUpperCase() == "TRUE") break;
		}
		list = ew_DoubleQuote(val,1);
	} else if (ew_IsNotEmpty(values)) {
		var ar = values.split("\r\n");
		for (var i = 0; i < ar.length; i++) {
			if (ew_IsNotEmpty(ar[i].trim())) {
				val = ar[i].split(",")[0];
				list += ew_DoubleQuote(val,1) + ",";
			}
		}
		if (list.length > 0) list = list.substr(0,list.length-1);
	}
	return list;
}

// Return activate field value
function ActivateFieldValue(f) {
	var val;
	switch (ew_GetFieldType(f.FldType)) {
	case 4: // Boolean
		val = "1";
		if (bDBMsAccess)
			val = "True";
		break;
	case 1: // Numeric
		val = 1;
		break;
	default:
		if (f.NativeDataType == 247) { // ENUM
			if (ew_HasTagValue(f, "Y")) // Assume ENUM(Y,N)
				val = "Y";
			else
				val = "1";
		} else {
			val = "Y";
		}
	}
	return val;
}

// Check if Popup Filter
function IsPopupFilter(f) {
	return ew_IsPopupFilter(f);
}

// Check if Extended Filter
function IsExtendedFilter(f) {
	return f.FldExtendedBasicSearch;
}

// Check if Text filter
function IsTextFilter(f) {
	return !IsDateFilter(f) && (f.FldHtmlTag != "SELECT" && f.FldHtmlTag != "RADIO" && f.FldHtmlTag != "CHECKBOX");
}

// Check if Auto Suggest
function IsAutoSuggest(f) {
	return IsTextFilter(f) && f.FldSelectType == "Table";
}

// Check if Date Filter
function IsDateFilter(f) {
	return ew_IsDateFilter(f);
}

// Check if use Ajax
function IsUseAjax(f) {
	return IsExtendedFilter(goFld) && (IsDateFilter(goFld) || (!IsTextFilter(goFld) && !(ew_GetFieldType(goFld.FldType) == 4 || goFld.FldTypeName == "ENUM" || goFld.FldTypeName == "SET")));
}

function IsShowChart(cht, pos) {
	return (cht.ShowChart && ew_IsNotEmpty(cht.ChartXFldName) && ew_IsNotEmpty(cht.ChartYFldName) && (!pos || pos == cht.ChartPosition));
}

// Check if popup calendar required
function IsPopupCalendar() {
	for (var i = 1, tlen = DB.Tables.Count(); i <= tlen; i++) {
		var WRKTABLE = DB.Tables(i);
		if (WRKTABLE.TblGen) {
			for (var j = 1, flen = WRKTABLE.Fields.Count(); j <= flen; j++) {
				var WRKFLD = WRKTABLE.Fields(j);
				if (WRKFLD.FldGenerate && WRKFLD.FldPopCalendar)
					return true;
			}
		}
	}
	return false;
}

function FieldTD_Header(f) {
	var sStyle = FieldCellStyle(f);
	if (ew_IsNotEmpty(sStyle)) sStyle = " style=\"" + sStyle + "\"";
	return sStyle;
}

function FieldCellStyle(f) {
	var bFldColumnWrap = f.FldColumnWrap;
	var sFldColumnWidth = f.FldColumnWidth;
	var sStyle = "";
	if (ew_IsNotEmpty(f.FldAlign)) {
		sStyle += "text-align: " + f.FldAlign + ";";
	}
	if (ew_IsNotEmpty(sFldColumnWidth)) {
		if (ew_IsNotEmpty(sStyle)) sStyle += " ";
		sStyle += "width: " + sFldColumnWidth;
		if (!isNaN(sFldColumnWidth)) sStyle += "px";
		sStyle += ";";
	}
	if (!bFldColumnWrap) {
		if (ew_IsNotEmpty(sStyle)) sStyle += " ";
		sStyle += "white-space: nowrap;";
	}
	return sStyle;
}

function FieldViewStyle(f) {
	var sStyle = "";
	if (f.FldBold) {
		sStyle += "font-weight: bold;";
	}
	if (f.FldItalic) {
		if (ew_IsNotEmpty(sStyle)) sStyle += " ";
		sStyle += "font-style: italic;";
	}
	//if (ew_IsNotEmpty(f.FldAlign)) {
	//	if (ew_IsNotEmpty(sStyle)) sStyle += " ";
	//	sStyle += "text-align: " + f.FldAlign + ";";
	//}
	return sStyle;
}

function FieldTD_Item(f) {
	//return FieldTD_Header(f);
	return ""; // Set up in RenderRow / RenderListRow
}

function CharsetToIconvEncoding(Charset) {
	switch (Charset.toLowerCase()) {
		case "iso-8859-6": return "ISO-8859-6";
		case "x-mac-arabic": return "MacArabic";
		case "windows-1256": return "CP1256";
		case "iso-8859-4": return "ISO-8859-4";
		case "windows-1257": return "CP1257";
		case "ibm852": return "CP852";
		case "iso-8859-2": return "ISO-8859-2";
		case "x-mac-ce": return "MacCentralEurope";
		case "windows-1250": return "CP1250";
		case "gb2312": return "GBK";
		case "hz-gb-2312": return "GBK";
		case "big5": return "BIG5";
		case "cp866": return "CP866";
		case "iso-8859-5": return "ISO-8859-5";
		case "koi8-r": return "KOI8-R";
		case "koi8-u": return "KOI8-U";
		case "x-mac-cyrillic": return "MacCyrillic";
		case "windows-1251": return "CP1251";
		case "iso-8859-7": return "ISO-8859-7";
		case "x-mac-greek": return "MacGreek";
		case "windows-1253": return "CP1253";
		case "iso-8859-8-i": return "ISO-8859-8";
		case "iso-8859-8": return "ISO-8859-8";
		case "x-mac-hebrew": return "MacHebrew";
		case "windows-1255": return "CP1255";
		case "x-mac-icelandic": return "MacIceland";
		case "euc-jp": return "EUC-JP";
		case "iso-2022-jp": return "ISO-2022-JP";
		case "shift_jis": return "SHIFT_JIS";
		case "euc-kr": return "EUC-KR";
		case "iso-2022-kr": return "ISO-2022-KR";
		case "Johab": return "JOHAB";
		case "iso-8859-3": return "ISO-8859-3";
		case "iso-8859-15": return "ISO-8859-15";
		case "windows-874": return "CP874";
		case "ibm857": return "CP857";
		case "iso-8859-9": return "ISO-8859-9";
		case "x-mac-turkish": return "MacTurkish";
		case "windows-1254": return "CP1254";
		case "utf-16": return "UTF-16";
		case "utf-8": return "UTF-8";
		case "windows-1258": return "CP1258";
		case "ibm850": return "CP850";
		case "iso-8859-1": return "ISO-8859-1";
		case "macintosh": return "Macintosh";
		case "windows-1252": return "CP1252";
		// Add your encodings here
		default: return "";
	}
}

function CharsetToMySqlCharset(Charset) {
	switch (Charset.toLowerCase()) {
		//case "iso-8859-6": return "";
		//case "x-mac-arabic": return "";
		case "windows-1256": return "cp1256";
		//case "iso-8859-4": return "";
		case "windows-1257": return "cp1257";
		case "ibm852": return "cp852";
		case "iso-8859-2": return "latin2";
		case "x-mac-ce": return "macce";
		case "windows-1250": return "cp1250";
		case "gb2312": return "gb2312";
		//case "hz-gb-2312": return "";
		case "big5": return "big5";
		case "cp866": return "cp866";
		//case "iso-8859-5": return "";
		case "koi8-r": return "koi8r";
		case "koi8-u": return "koi8u";
		//case "x-mac-cyrillic": return "";
		case "windows-1251": return "cp1251";
		case "iso-8859-7": return "greek";
		//case "x-mac-greek": return "";
		//case "windows-1253": return "";
		case "iso-8859-8-i": return "hebrew";
		case "iso-8859-8": return "hebrew";
		//case "x-mac-hebrew": return "";
		//case "windows-1255": return "";
		case "x-mac-icelandic": return "";
		case "euc-jp": return "ujis";
		//case "iso-2022-jp": return "";
		case "shift_jis": return "sjis";
		case "euc-kr": return "euckr";
		//case "iso-2022-kr": return "";
		//case "Johab": return "";
		//case "iso-8859-3": return "";
		//case "iso-8859-15": return "";
		//case "windows-874": return "";
		//case "ibm857": return "";
		case "iso-8859-9": return "latin5";
		//case "x-mac-turkish": return "";
		//case "windows-1254": return "";
		case "utf-16": return "ucs2";
		case "utf-8": return "utf8";
		//case "windows-1258": return "";
		case "ibm850": return "cp850";
		case "iso-8859-1": return "latin1";
		case "macintosh": return "macroman";
		case "windows-1252": return "latin1";
		// Add your encodings here
		default: return "";
	}
}

function IsMsAccess() {
	return bDBMsAccess;
}

function IsMsSQL() {
	return bDBMsSql;
}

function IsMySQL() {
	return bDBMySql;
}

function IsPostgreSQL() {
	return bDBPostgreSql;
}

function IsOracle() {
	return bDBOracle;
}

// Get Oracle service name from connection string
function GetOracleServiceName(ConnStr) {
	sName = "";
	var wrkstr = ConnStr.toUpperCase();
	var p1 = wrkstr.indexOf("DATA SOURCE=");
	if (p1 > 0) {
		p1 += 12; // Skip "Data Source=";
		p2 = ConnStr.indexOf(";", p1);
		if (p2 > p1)
			sName = ConnStr.substr(p1, p2-p1);
		else
			sName = ConnStr.substr(p1);
	}
	return sName;
}

function UseMysqlt() {
	return PROJ.GetV("UseMysqlt");
}

function UseADODB() {
	return UseMysqlt() || !bDBMySql;
}

function UseEmailExport() {
	for (var i = 0, len = goTbls.length; i < len; i++) {
		var WRKTABLE = goTbls[i];
		var bTblGen = WRKTABLE.TblGen;
		if (bTblGen) {
			if ((!WRKTABLE.TblUseGlobal && WRKTABLE.TblExportEmail) || (WRKTABLE.TblUseGlobal && PROJ.ExportEmail))
				return true;
		}
	}
	return false;
}

function UsePdfExport() {
	for (var i = 0, len = goTbls.length; i < len; i++) {
		var WRKTABLE = goTbls[i];
		var bTblGen = WRKTABLE.TblGen;
		if (bTblGen) {
			if ((!WRKTABLE.TblUseGlobal && WRKTABLE.TblExportPDF) || (WRKTABLE.TblUseGlobal && PROJ.ExportPDF))
				return true;
		}
	}
	return false;
}

function UseJSTemplate() {

	function UseJSTemplateScript(WRKTABLE, ScriptName, ReportType) {
		if (CUSTOMSCRIPTS.ScriptExist("Template", "Table", ScriptName, ReportType, WRKTABLE.TblName, "")) {
			var wrkcode = CUSTOMSCRIPTS.ScriptItem("Template", "Table", ScriptName, ReportType, WRKTABLE.TblName, "").ScriptCode;
			return (wrkcode.trim() != "");
		}
		return false;
	}

	for (var i = 0, len = goTbls.length; i < len; i++) {
		var WRKTABLE = goTbls[i];
		var bTblGen = WRKTABLE.TblGen;
		if (bTblGen) {
			if (WRKTABLE.TblReportType == "rpt") {
				if (UseJSTemplateScript(WRKTABLE, "CustomTemplateHeader", WRKTABLE.TblReportType) ||
					UseJSTemplateScript(WRKTABLE, "CustomTemplateBody", WRKTABLE.TblReportType) ||
					UseJSTemplateScript(WRKTABLE, "CustomTemplateFooter", WRKTABLE.TblReportType))
					return true;
			} else if (WRKTABLE.TblReportType == "summary") {
				if (UseJSTemplateScript(WRKTABLE, "CustomTemplateHeader", WRKTABLE.TblReportType) || 
					UseJSTemplateScript(WRKTABLE, "CustomTemplateGroupHeader", WRKTABLE.TblReportType) || 
					UseJSTemplateScript(WRKTABLE, "CustomTemplateBody", WRKTABLE.TblReportType) || 
					UseJSTemplateScript(WRKTABLE, "CustomTemplateGroupFooter", WRKTABLE.TblReportType) || 
					UseJSTemplateScript(WRKTABLE, "CustomTemplateFooter", WRKTABLE.TblReportType))
					return true;
			} else if (WRKTABLE.TblReportType == "dashboard") {
				if (UseJSTemplateScript(WRKTABLE, "CustomTemplate", WRKTABLE.TblReportType))
					return true;
			}
		}
	}
	return false;
}

// Return if Export is required
function IsExport() {
	for (var i = 0, len = goTbls.length; i < len; i++) {
		var WRKTABLE = goTbls[i];
		var bTblGen = WRKTABLE.TblGen;
		if (bTblGen) {
			var bUseGlobal = WRKTABLE.TblUseGlobal;
			bExport = (bUseGlobal) ? PROJ.ExportHtml : WRKTABLE.TblExportHtml;
			if (bExport) return true;
			bExport = (bUseGlobal) ? PROJ.ExportWord : WRKTABLE.TblExportWord;
			if (bExport) return true;
			bExport = (bUseGlobal) ? PROJ.ExportExcel : WRKTABLE.TblExportExcel;
			if (bExport) return true;
			bExport = (bUseGlobal) ? PROJ.ExportEmail : WRKTABLE.TblExportEmail;
			if (bExport) return true;
			bExport = (bUseGlobal) ? PROJ.ExportPDF : WRKTABLE.TblExportPDF;
			if (bExport) return true;
		}
	}
	return false;
}

function GenInfo() {
	return TABLE && (TABLE.TblType != "REPORT" && TABLE.TblReportType == "rpt" ||
		TABLE.TblType == "REPORT" && ew_InArray(TABLE.TblReportType, ["summary", "crosstab", "gantt"]));
}

function GenHeader() {
	return (!PROJ.AppCompat || (PROJ.AppCompat && ew_IsNotEmpty(PROJ.AppHeader)));
}

function GenFooter() {
	return (!PROJ.AppCompat || (PROJ.AppCompat && ew_IsNotEmpty(PROJ.AppFooter)));
}

function GenLogin() {
	return (!PROJ.AppCompat || (PROJ.AppCompat && ew_IsEmpty(PROJ.AppLogin)));
}

function GenLogout() {
	return (!PROJ.AppCompat || (PROJ.AppCompat && ew_IsEmpty(PROJ.AppLogout)));
}

function GenDefault() {
	return (!PROJ.AppCompat || (PROJ.AppCompat && ew_IsEmpty(PROJ.AppDefault)));
}

function GetProjCssFileName() {
	var filename = PROJ.ProjVar + ".css";
	if (PROJ.OutputNameLCase) {
		filename = filename.toLowerCase();
	}
	if (ew_IsNotEmpty(ew_FolderPath("_css"))) {
		filename = ew_FolderPath("_css") + "/" + filename;
	}
	return filename;
}

function SummaryCaption(typ) {
	return "<?php echo $ReportLanguage->Phrase(\"Rpt" + typ + "\"); ?>";
}

var UseFusionChartsFree = true; // v8

function IsFCFChart(typ) {
	return UseFusionChartsFree && (typ == 20 || typ == 21 || typ == 22); // v8
}

function UsePhpExcel() {
	return ew_GetCtrlById("phpexcel") != null;
}

function UsePhpWord() {
	return ew_GetCtrlById("phpword") != null;
}

// Check if Table has drill down parameter
function HasDrillDownParm(t) {
	for (var i = 1, cnt = t.Fields.Count(); i <= cnt; i++) {
		var f = t.Fields(i);
		if (f.FldGenerate && f.FldDrillParameter)
			return true;
	}
	return false;
}

// Check if Drill Down source field
function IsDrillDownSource(f) {
	for (var i = 0; i < nAllFldCount; i++) {
		var fld = goFlds[arAllFlds[i]];
		if (ew_IsFieldDrillDown(fld)) {
			var sFldDrillSourceFields = fld.FldDrillSourceFields.trim();
			if (sFldDrillSourceFields.substr(sFldDrillSourceFields.length-2) == "||")
				sFldDrillSourceFields = sFldDrillSourceFields.substr(0,sFldDrillSourceFields.length-2);
			var arSourceFlds = sFldDrillSourceFields.split("||");
			for (var j = 0, cnt = arSourceFlds.length; j < cnt; j++) {
				var SOURCEFLD = goFlds[arSourceFlds[j]];
				if (SOURCEFLD.FldName == f.FldName)
					return true;
			}
		}
	}
	for (var i = 0, len = arAllCharts.length; i < len; i++) {
		var cht = goChts[arAllCharts[i]];
		if (IsShowChart(cht) && ew_IsChartDrillDown(cht)) {
			var sChtDrillSourceFields = cht.ChartDrillSourceFields.trim();
			if (sChtDrillSourceFields.substr(sChtDrillSourceFields.length-2) == "||")
				sChtDrillSourceFields = sChtDrillSourceFields.substr(0,sChtDrillSourceFields.length-2);
			var arSourceFlds = sChtDrillSourceFields.split("||");
			for (var j = 0, cnt = arSourceFlds.length; j < cnt; j++) {
				var SOURCEFLD = goFlds[arSourceFlds[j]];
				if (SOURCEFLD.FldName == f.FldName)
					return true;
			}
		}
	}; // End for i
	return false;
}

function SqlTableName(t) {
	var name;
	name = ew_QuotedName(t.TblName);
	if (ew_IsNotEmpty(t.TblSchema))
		name = ew_QuotedName(t.TblSchema) + "." + name;
	return name;
}

/*
 ***
 ***  IMPORTANT - DO NOT CHANGE
 *** -----------------------------------------
 */
