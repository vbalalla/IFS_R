<!--##session ewconfig##-->
<!--##
	var DefaultDateFormat;
	switch (PROJ.DefaultDateFormat) {
		case 5:
		case 9:
		case 12:
		case 15:
			DefaultDateFormat = "yyyy" + sDateSeparator + "mm" + sDateSeparator + "dd";
			break;
		case 6:
		case 10:
		case 13:
		case 16:
			DefaultDateFormat = "mm" + sDateSeparator + "dd" + sDateSeparator + "yyyy";
			break;
		case 7:
		case 11:
		case 14:
		case 17:
			DefaultDateFormat = "dd" + sDateSeparator + "mm" + sDateSeparator + "yyyy";
			break;
	};

	var bUserTable = ew_HasUserTable();
	var bDynamicUserLevel = (bUserTable && (DB.UseDynamicUserLevel && ew_IsNotEmpty(DB.UserLevelTbl) && ew_IsNotEmpty(DB.SecUserLevelFld)));

	// Smtp Server and Port
	var sSmtpServer = PROJ.SmtpServer;
	if (ew_IsEmpty(sSmtpServer)) sSmtpServer = "localhost";
	var iSmtpServerPort = PROJ.SmtpServerPort;
	if (iSmtpServerPort <= 0) iSmtpServerPort = 25;

	if (bUserTable && ew_IsNotEmpty(PROJ.SecTbl)) {
		SECTABLE = DB.Tables(PROJ.SecTbl);
	}
##-->
<?php
// PHP Report Maker 8 - configuration

// Relative path
if (!isset($EWR_RELATIVE_PATH)) $EWR_RELATIVE_PATH = "";

// Debug
define("EWR_DEBUG_ENABLED", <!--##=ew_Val(PROJ.GetV("Debug"))##-->, TRUE); // True to debug
if (EWR_DEBUG_ENABLED) {
	@ini_set("display_errors", "1"); // Display errors
	error_reporting(E_ALL ^ E_NOTICE); // Report all errors except E_NOTICE
}

define("EWR_PROJECT_ID", "<!--##=PROJ.ProjID##-->", TRUE); // Project ID
define("EWR_PROJECT_NAME", "<!--##=ew_Quote(sProjName)##-->", TRUE); // Project Name
define("EWR_PROJECT_VAR", "<!--##=sProjVar##-->", TRUE); // Project Var
define("EWR_CONFIG_FILE_FOLDER", EWR_PROJECT_VAR . "<!--##=ew_FolderPath("_security")##-->", TRUE); // Config file folder

define("EWR_IS_WINDOWS", (strtolower(substr(PHP_OS, 0, 3)) === 'win'), TRUE); // Is Windows OS
define("EWR_IS_PHP5", (phpversion() >= "5.2.0"), TRUE); // Is PHP 5 or later
if (!EWR_IS_PHP5) die("This script requires PHP 5.2. You are running " . phpversion() . ".");
define("EWR_PATH_DELIMITER", ((EWR_IS_WINDOWS) ? "\\" : "/"), TRUE); // Path delimiter

// Set up font path
$EWR_FONT_PATH = realpath('./<!--##=ew_FolderPath("_font")##-->');

// Language settings
define("EWR_LANGUAGE_FOLDER", $EWR_RELATIVE_PATH . "<!--##=sLanguageFolder##-->", TRUE);
$EWR_LANGUAGE_FILE = array();
<!--##
	for (var i = 0; i < arLanguageFile.length; i++) {
		sFile = arLanguageFile[i];
		sLanguageId = LANGUAGE.GetFileId(sFile);
		if (i == 0 || sFile == sDefaultLanguageFile)
			sDefaultLanguageId = sLanguageId;
##-->
$EWR_LANGUAGE_FILE[] = array("<!--##=ew_Quote(sLanguageId)##-->", "", "<!--##=sFile##-->");
<!--##
	} // Language
##-->
define("EWR_LANGUAGE_DEFAULT_ID", "<!--##=ew_Quote(sDefaultLanguageId)##-->", TRUE);
define("EWR_SESSION_LANGUAGE_ID", EWR_PROJECT_VAR . "_LanguageId", TRUE); // Language ID
if (!function_exists('xml_parser_create') && !class_exists("DOMDocument")) die("This script requires PHP XML Parser or DOM.");
define('EWR_USE_DOM_XML', ((!function_exists('xml_parser_create') && class_exists("DOMDocument")) || <!--##=ew_Val(PROJ.GetV("UseDomXml"))##-->), TRUE);

// Page Token
define("EWR_TOKEN_NAME", "token", TRUE);
define("EWR_SESSION_TOKEN", EWR_PROJECT_NAME . "_Token", TRUE);

// Database connection
<!--## if (bDBMySql || bDBPostgreSql) { ##-->
define("EWR_CONN_HOST", '<!--##=DB.DBHOST##-->', TRUE);
define("EWR_CONN_PORT", <!--##=DB.DBPort##-->, TRUE);
define("EWR_CONN_USER", '<!--##=ew_SQuote(DB.DBUID)##-->', TRUE);
define("EWR_CONN_PASS", '<!--##=ew_SQuote(DB.DBPwd)##-->', TRUE);
define("EWR_CONN_DB", '<!--##=ew_SQuote(DB.DBName)##-->', TRUE);
define("EWR_CONN_SCHEMA", '<!--##=ew_SQuote(DB.DBSchema)##-->', TRUE);
<!--## } else if (bDBOracle) { ##-->
define("EWR_CONN_HOST", FALSE, TRUE);
define("EWR_CONN_PORT", FALSE, TRUE);
define("EWR_CONN_USER", '<!--##=ew_SQuote(DB.DBUID)##-->', TRUE);
define("EWR_CONN_PASS", '<!--##=ew_SQuote(DB.DBPwd)##-->', TRUE);
define("EWR_CONN_DB", '<!--##=ew_SQuote(GetOracleServiceName(DB.DBConnstr))##-->', TRUE);
define("EWR_CONN_SCHEMA", '<!--##=ew_SQuote(DB.DBSchema)##-->', TRUE);
<!--## } ##-->

// Set up database error function
$EWR_ERROR_FN = 'ewr_ErrorFn';

// ADODB (Access/SQL Server)
define("EWR_CODEPAGE", <!--##=PROJ.CodePage##-->, TRUE); // Code page

define("EWR_CHARSET", "<!--##=PROJ.Charset##-->", TRUE); // Project charset

define("EWR_DBMSNAME", '<!--##=ew_SQuote(DB.DBDBMSName)##-->', TRUE); // DBMS Name
define("EWR_IS_MSACCESS", <!--##=ew_Val(bDBMsAccess)##-->, TRUE); // Access
define("EWR_IS_MSSQL", <!--##=ew_Val(bDBMsSql)##-->, TRUE); // SQL Server
define("EWR_IS_MYSQL", <!--##=ew_Val(bDBMySql)##-->, TRUE); // MySQL
define("EWR_IS_POSTGRESQL", <!--##=ew_Val(bDBPostgreSql)##-->, TRUE); // PostgreSQL
define("EWR_IS_ORACLE", <!--##=ew_Val(bDBOracle)##-->, TRUE); // Oracle

define("EWR_DB_QUOTE_START", "<!--##=ew_Quote(DB.DBQuoteS)##-->", TRUE);
define("EWR_DB_QUOTE_END", "<!--##=ew_Quote(DB.DBQuoteE)##-->", TRUE);

// Remove XSS
define("EWR_REMOVE_XSS", <!--##=ew_Val(PROJ.GetV("RemoveXSS"))##-->, TRUE);

$EWR_XSS_ARRAY = array('javascript', 'vbscript', 'expression', '<applet', '<meta', '<xml', '<blink', '<link', '<style', '<script', '<embed', '<object', '<iframe', '<frame', '<frameset', '<ilayer', '<layer', '<bgsound', '<title', '<base',
'onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');

// Check Token
define("EWR_CHECK_TOKEN", <!--##=ew_Val(PROJ.GetV("CheckPostToken"))##-->, TRUE); // Check post token

// General
$EWR_ROOT_RELATIVE_PATH = "<!--##=PROJ.RootRelativePath##-->"; // Relative path of app root
define("EWR_DEFAULT_DATE_FORMAT", "<!--##=DefaultDateFormat##-->", TRUE); // Default date format
define("EWR_DATE_SEPARATOR", "<!--##=sDateSeparator##-->", TRUE); // Date separator
define("EWR_UNFORMAT_YEAR", 50, TRUE); // Unformat year
define("EWR_RANDOM_KEY", '<!--##=ew_RandomKey()##-->', TRUE); // Random key for encryption
define("EWR_PROJECT_STYLESHEET_FILENAME", "<!--##=GetProjCssFileName()##-->", TRUE); // Project stylesheet file name
define("EWR_CHART_WIDTH", 550, TRUE);
define("EWR_CHART_HEIGHT", 440, TRUE);
define("EWR_CHART_SHOW_BLANK_SERIES", <!--##=ew_Val(PROJ.GetV("ShowBlankSeriesForChart"))##-->, TRUE); // Show blank series
define("EWR_CHART_SHOW_ZERO_IN_STACK_CHART", <!--##=ew_Val(PROJ.GetV("ShowZeroInStackChart"))##-->, TRUE); // Show zero in stack chart

// Drill down setting
define("EWR_USE_DRILLDOWN_PANEL", <!--##=ew_Val(PROJ.GetV("UseDrillDownPanel"))##-->, TRUE); // Use popup panel for drill down

<!--##
	// Get encoding from project charset (iconv assumed)
    sEncoding = "";
    if (ew_IsNotEmpty(PROJ.GetV("Encoding"))) sEncoding = PROJ.GetV("Encoding");
	if (ew_IsEmpty(sEncoding)) sEncoding = CharsetToIconvEncoding(PROJ.Charset);
##-->
/**
 * Character encoding
 * Note: If you use non English languages, you need to set character encoding
 * for some features. Make sure either iconv functions or multibyte string
 * functions are enabled and your encoding is supported. See PHP manual for
 * details.
 */
define("EWR_ENCODING", "<!--##=sEncoding##-->", TRUE); // Character encoding
define("EWR_IS_DOUBLE_BYTE", in_array(EWR_ENCODING, array("GBK", "BIG5", "SHIFT_JIS")), TRUE); // Double-byte character encoding

/**
 * MySQL charset (for SET NAMES statement, not used by default)
 * Note: Read http://dev.mysql.com/doc/refman/5.0/en/charset-connection.html
 * before using this setting.
 */
<!--##
	// Get MySQL charset from project charset
	sEncoding = "";
	if (ew_IsNotEmpty(PROJ.GetV("MySQLCharset"))) sEncoding = PROJ.GetV("MySQLCharset");
	if (ew_IsEmpty(sEncoding)) sEncoding = CharsetToMySqlCharset(PROJ.Charset);
##-->
define("EWR_MYSQL_CHARSET", "<!--##=sEncoding##-->", TRUE);

/**
 * Password (MD5 and case-sensitivity)
 * Note: If you enable MD5 password, make sure that the passwords in your
 * user table are stored as MD5 hash (32-character hexadecimal number) of the
 * clear text password. If you also use case-insensitive password, convert the
 * clear text passwords to lower case first before calculating MD5 hash.
 * Otherwise, existing users will not be able to login.
 */
define("EWR_ENCRYPTED_PASSWORD", <!--##=ew_Val(PROJ.MD5Password)##-->, TRUE); // Use encrypted password
define("EWR_CASE_SENSITIVE_PASSWORD", <!--##=ew_Val(PROJ.CaseSensitivePassword)##-->, TRUE); // Case-sensitive password

/**
 * Numeric and monetary formatting options
 * Note: DO NOT CHANGE THE FOLLOWING $DEFAULT_* VARIABLES!
 * If you want to use custom settings, customize the language file,
 * set "use_system_locale" to "0" to override localeconv and customize the
 * phrases under the <locale> node for ew_FormatCurrency/Number/Percent functions
 * Also read http://www.php.net/localeconv for description of the constants
*/
$EWR_DEFAULT_LOCALE = json_decode('{"decimal_point":".","thousands_sep":"","int_curr_symbol":"$","currency_symbol":"$","mon_decimal_point":".","mon_thousands_sep":"","positive_sign":"","negative_sign":"-","int_frac_digits":2,"frac_digits":2,"p_cs_precedes":1,"p_sep_by_space":0,"n_cs_precedes":1,"n_sep_by_space":0,"p_sign_posn":1,"n_sign_posn":1}', TRUE); 
$EWR_DEFAULT_DECIMAL_POINT = &$EWR_DEFAULT_LOCALE["decimal_point"];
$EWR_DEFAULT_THOUSANDS_SEP = &$EWR_DEFAULT_LOCALE["thousands_sep"];
$EWR_DEFAULT_CURRENCY_SYMBOL = &$EWR_DEFAULT_LOCALE["currency_symbol"];
$EWR_DEFAULT_MON_DECIMAL_POINT = &$EWR_DEFAULT_LOCALE["mon_decimal_point"];
$EWR_DEFAULT_MON_THOUSANDS_SEP = &$EWR_DEFAULT_LOCALE["mon_thousands_sep"];
$EWR_DEFAULT_POSITIVE_SIGN = &$EWR_DEFAULT_LOCALE["positive_sign"];
$EWR_DEFAULT_NEGATIVE_SIGN = &$EWR_DEFAULT_LOCALE["negative_sign"];
$EWR_DEFAULT_FRAC_DIGITS = &$EWR_DEFAULT_LOCALE["frac_digits"];
$EWR_DEFAULT_P_CS_PRECEDES = &$EWR_DEFAULT_LOCALE["p_cs_precedes"];
$EWR_DEFAULT_P_SEP_BY_SPACE = &$EWR_DEFAULT_LOCALE["p_sep_by_space"];
$EWR_DEFAULT_N_CS_PRECEDES = &$EWR_DEFAULT_LOCALE["n_cs_precedes"];
$EWR_DEFAULT_N_SEP_BY_SPACE = &$EWR_DEFAULT_LOCALE["n_sep_by_space"];
$EWR_DEFAULT_P_SIGN_POSN = &$EWR_DEFAULT_LOCALE["p_sign_posn"];
$EWR_DEFAULT_N_SIGN_POSN = &$EWR_DEFAULT_LOCALE["n_sign_posn"];
<!--## if (!bMultiLanguage && PROJ.SetLocale && ew_IsNotEmpty(PROJ.Locale)) { ##-->
define("EWR_REPORT_DEFAULT_LOCALE", '<!--##=ew_SQuote(PROJ.Locale)##-->', TRUE);
if (!json_decode(EWR_REPORT_DEFAULT_LOCALE)) // String, not JSON
	@setlocale(LC_ALL, EWR_REPORT_DEFAULT_LOCALE);
<!--## } ##-->

$EWR_DEFAULT_TIME_ZONE = "<!--##=PROJ.GetV("DefaultTimeZone")##-->";

// Use responsive layout
$EWR_USE_RESPONSIVE_LAYOUT = <!--##=ew_Val(PROJ.GetV("UseResponsiveLayout"))##-->;

// Use css flip
define("EWR_CSS_FLIP", <!--##=ew_Val(PROJ.GetV("UseCssFlip"))##-->, TRUE);

// Filter
define("EWR_SHOW_CURRENT_FILTER", <!--##=ew_Val(PROJ.GetV("ShowCurrentFilter"))##-->, TRUE); // True to show current filter
define("EWR_SHOW_DRILLDOWN_FILTER", <!--##=ew_Val(PROJ.GetV("ShowDrillDownFilter"))##-->, TRUE); // True to show drill down filter

// Session names
define("EWR_SESSION_STATUS", EWR_PROJECT_VAR . "_status", TRUE); // Login Status
define("EWR_SESSION_USER_NAME", EWR_SESSION_STATUS . "_UserName", TRUE); // User Name
define("EWR_SESSION_USER_ID", EWR_SESSION_STATUS . "_UserID", TRUE); // User ID
define("EWR_SESSION_USER_LEVEL_ID", EWR_SESSION_STATUS . "_UserLevel", TRUE); // User Level ID
define("EWR_SESSION_USER_LEVEL", EWR_SESSION_STATUS . "_UserLevelValue", TRUE); // User Level
define("EWR_SESSION_PARENT_USER_ID", EWR_SESSION_STATUS . "_ParentUserID", TRUE); // Parent User ID
define("EWR_SESSION_SYSTEM_ADMIN", EWR_PROJECT_VAR . "_SysAdmin", TRUE); // System Admin
define("EWR_SESSION_AR_USER_LEVEL", EWR_PROJECT_VAR . "_arUserLevel", TRUE); // User Level Array
define("EWR_SESSION_AR_USER_LEVEL_PRIV", EWR_PROJECT_VAR . "_arUserLevelPriv", TRUE); // User Level Privilege Array
define("EWR_SESSION_MESSAGE", EWR_PROJECT_VAR . "_Message", TRUE); // System Message
define("EWR_SESSION_FAILURE_MESSAGE", EWR_PROJECT_VAR . "_Failure_Message", TRUE); // System error message
define("EWR_SESSION_SUCCESS_MESSAGE", EWR_PROJECT_VAR . "_Success_Message", TRUE); // System success message
define("EWR_SESSION_WARNING_MESSAGE", EWR_PROJECT_VAR . "_Warning_Message", TRUE); // Warning message

// Hard-coded admin
define("EWR_ADMIN_USER_NAME", "<!--##=ew_Quote2(PROJ.SecLoginID)##-->", TRUE);
define("EWR_ADMIN_PASSWORD", "<!--##=ew_Quote2(PROJ.SecPasswd)##-->", TRUE);
define("EWR_USE_CUSTOM_LOGIN", TRUE, TRUE); // Use custom login

// User admin
<!--##
	var sUserSelect = "";
	if (ew_IsNotEmpty(PROJ.SecLoginIDFld)) {
		TABLE = DB.Tables(PROJ.SecTbl);
		if (TABLE.TblType == "CUSTOMVIEW") {
			sLimitPart = ew_SQLPart(TABLE.TblCustomSQL, "LIMIT").trim();
			sGroupByPart = ew_SQLPart(WRKTABLE.TblCustomSQL, "GROUP BY").trim();
			if (ew_IsEmpty(sLimitPart) && ew_IsEmpty(sGroupByPart)) {
				sSelect = ew_SQLPart(TABLE.TblCustomSql, "SELECT");
				sFrom = ew_SQLPart(TABLE.TblCustomSql, "FROM");
			} else {
				sSelect = "*";
				sFrom = "(" + TABLE.TblCustomSQL + ") " + ew_QuotedName("EW_CV_" + TABLE.TblVar);
			}
		} else {
			sSelect = "*";
			sFrom = ew_TableName(TABLE);
		}
		sUserSelect = "SELECT " + sSelect + " FROM " + sFrom;
	}
##-->
define("EWR_LOGIN_SELECT_SQL", "<!--##=ew_Quote(sUserSelect)##-->", TRUE);

// User table filters
<!--##
	if (bUserTable) {
		if (SECTABLE.TblType == "CUSTOMVIEW") {
			sUserTable = ew_SQLPart(SECTABLE.TblCustomSQL, "FROM");
		} else {
			sUserTable = ew_TableName(SECTABLE);
		}
		if (ew_IsNotEmpty(PROJ.SecLoginIDFld)) {
			FIELD = SECTABLE.Fields(PROJ.SecLoginIDFld);
			sFld = ew_FieldSqlName(FIELD);
			sFldQuoteS = FIELD.FldQuoteS;
			sFldQuoteE = FIELD.FldQuoteE;
			sFilter = sFld + " = " + sFldQuoteS + "%u" + sFldQuoteE;
		} else {
			sFilter = "";
		}
##-->
define("EWR_USER_TABLE", "<!--##=ew_Quote(sUserTable)##-->",  TRUE);
define("EWR_USER_NAME_FILTER", "<!--##=ew_Quote(sFilter)##-->",  TRUE);
<!--##
		if (ew_IsNotEmpty(DB.SecuUserIDFld)) {
			FIELD = SECTABLE.Fields(DB.SecuUserIDFld);
			sFld = ew_FieldSqlName(FIELD);
			sFldQuoteS = FIELD.FldQuoteS;
			sFldQuoteE = FIELD.FldQuoteE;
			sFilter = sFld + " = " + sFldQuoteS + "%u" + sFldQuoteE;
		} else {
			sFilter = "";
		}
##-->
define("EWR_USER_ID_FILTER", "<!--##=ew_Quote(sFilter)##-->",  TRUE);
<!--##
		if (PROJ.SecRegisterActivate && ew_IsNotEmpty(PROJ.SecRegisterActivateFld)) {
			FIELD = SECTABLE.Fields(PROJ.SecRegisterActivateFld);
			sFld = ew_FieldSqlName(FIELD);
			sFldQuoteS = FIELD.FldQuoteS;
			sFldQuoteE = FIELD.FldQuoteE;
			sFldValue = ActivateFieldValue(FIELD);
			sFilter = sFld + " = " + sFldQuoteS + sFldValue + sFldQuoteE;
		} else {
			sFilter = "";
		}
##-->
define("EWR_USER_ACTIVATE_FILTER", "<!--##=ew_Quote(sFilter)##-->",  TRUE);
<!--##
	}
##-->

// User level constants
define("EWR_ALLOW_LIST", 8, TRUE); // List
define("EWR_ALLOW_REPORT", 8, TRUE); // Report
define("EWR_ALLOW_ADMIN", 16, TRUE); // Admin

// User id constants
define("EWR_USER_ID_IS_HIERARCHICAL", <!--##=ew_Val(PROJ.GetV("HierarchicalUserID"))##-->, TRUE); // Hierarchical user id

<!--## if (bDynamicUserLevel) { ##-->
// Dynamic User Level settings
// User level definition table/field names
@define("EWR_USER_LEVEL_TABLE", "<!--##=ew_Quote(ew_TableName(DB.Tables(DB.UserLevelTbl)))##-->", TRUE);
@define("EWR_USER_LEVEL_ID_FIELD", "<!--##=ew_Quote(ew_QuotedName(DB.UserLevelIdFld))##-->", TRUE);
@define("EWR_USER_LEVEL_NAME_FIELD", "<!--##=ew_Quote(ew_QuotedName(DB.UserLevelNameFld))##-->", TRUE);

// User Level privileges table/field names
@define("EWR_USER_LEVEL_PRIV_TABLE", "<!--##=ew_Quote(ew_TableName(DB.Tables(DB.UserLevelPrivTbl)))##-->", TRUE);
@define("EWR_USER_LEVEL_PRIV_TABLE_NAME_FIELD", "<!--##=ew_Quote(ew_QuotedName(DB.UserLevelPrivTblNameFld))##-->", TRUE);
@define("EWR_USER_LEVEL_PRIV_TABLE_NAME_FIELD_2", "<!--##=ew_Quote(DB.UserLevelPrivTblNameFld)##-->", TRUE);
@define("EWR_USER_LEVEL_PRIV_TABLE_NAME_FIELD_SIZE", 255, TRUE);
@define("EWR_USER_LEVEL_PRIV_USER_LEVEL_ID_FIELD", "<!--##=ew_Quote(ew_QuotedName(DB.UserLevelPrivUserLevelFld))##-->", TRUE);
@define("EWR_USER_LEVEL_PRIV_PRIV_FIELD", "<!--##=ew_Quote(ew_QuotedName(DB.UserLevelPrivPrivFld))##-->", TRUE);
<!--## } ##-->

// Table level constants
<!--## if (PROJ.AppCompat && PROJ.AppCompatVersion >= 9) { ##-->
define("EWR_TABLE_PREFIX", "<!--##=PROJ.ProjID##-->", TRUE);
<!--## } else { ##-->
define("EWR_TABLE_PREFIX", "<!--##=pfxUserLevel##-->", TRUE);
<!--## } ##-->
define("EWR_TABLE_PREFIX_OLD", "<!--##=pfxUserLevel##-->", TRUE);
define("EWR_TABLE_GROUP_PER_PAGE", "grpperpage", TRUE);
define("EWR_TABLE_START_GROUP", "start", TRUE);
define("EWR_TABLE_ORDER_BY", "order", TRUE);
define("EWR_TABLE_ORDER_BY_TYPE", "ordertype", TRUE);
define("EWR_TABLE_SORT", "sort", TRUE); // Table sort
define("EWR_TABLE_SORTCHART", "sortc", TRUE); // Table sort chart
define("EWR_TABLE_MASTER_TABLE", "mastertable", TRUE); // Master table
define("EWR_TABLE_PAGE_NO", "pageno", TRUE); // Page number

// Data types
define("EWR_DATATYPE_NONE", 0, TRUE);
define("EWR_DATATYPE_NUMBER", 1, TRUE);
define("EWR_DATATYPE_DATE", 2, TRUE);
define("EWR_DATATYPE_STRING", 3, TRUE);
define("EWR_DATATYPE_BOOLEAN", 4, TRUE);
define("EWR_DATATYPE_MEMO", 5, TRUE);
define("EWR_DATATYPE_BLOB", 6, TRUE);
define("EWR_DATATYPE_TIME", 7, TRUE);
define("EWR_DATATYPE_GUID", 8, TRUE);
define("EWR_DATATYPE_OTHER", 9, TRUE);

// Row types
define("EWR_ROWTYPE_DETAIL", 1, TRUE); // Row type detail
define("EWR_ROWTYPE_TOTAL", 2, TRUE); // Row type group summary

// Row total types
define("EWR_ROWTOTAL_GROUP", 1, TRUE); // Page summary
define("EWR_ROWTOTAL_PAGE", 2, TRUE); // Page summary
define("EWR_ROWTOTAL_GRAND", 3, TRUE); // Grand summary

// Row total sub types
define("EWR_ROWTOTAL_FOOTER", 1, TRUE); // Footer
define("EWR_ROWTOTAL_SUM", 2, TRUE); // SUM
define("EWR_ROWTOTAL_AVG", 3, TRUE); // AVG
define("EWR_ROWTOTAL_MIN", 4, TRUE); // MIN
define("EWR_ROWTOTAL_MAX", 5, TRUE); // MAX
define("EWR_ROWTOTAL_CNT", 6, TRUE); // CNT

// Empty/Null/Not Null/Init/all values
define("EWR_EMPTY_VALUE", "##empty##", TRUE);
define("EWR_NULL_VALUE", "##null##", TRUE);
define("EWR_NOT_NULL_VALUE", "##notnull##", TRUE);
define("EWR_INIT_VALUE", "##init##", TRUE);
define("EWR_ALL_VALUE", "##all##", TRUE);

// Boolean values for ENUM('Y'/'N') or ENUM(1/0)
define("EWR_TRUE_STRING", "'Y'", TRUE);
define("EWR_FALSE_STRING", "'N'", TRUE);

// SQL formats
define("EWR_YEAR_SQL", "<!--##=ew_Quote(ew_DbGrpSql("y",0))##-->", TRUE);
define("EWR_QUARTER_SQL", "<!--##=ew_Quote(ew_DbGrpSql("xq",0))##-->", TRUE);
define("EWR_MONTH_SQL", "<!--##=ew_Quote(ew_DbGrpSql("xm",0))##-->", TRUE);

// Use token in URL (reserved, not used, do NOT change!)
define("EWR_USE_TOKEN_IN_URL", FALSE, TRUE);

// Email
define("EWR_EMAIL_COMPONENT", "PHPMAILER", TRUE); // Always use PHPMAILER
define("EWR_SMTP_SERVER", "<!--##=sSmtpServer##-->", TRUE); // SMTP server
define("EWR_SMTP_SERVER_PORT", <!--##=iSmtpServerPort##-->, TRUE); // SMTP server port
define("EWR_SMTP_SECURE_OPTION", "<!--##=PROJ.SmtpSecureOption.toLowerCase()##-->", TRUE);
define("EWR_SMTP_SERVER_USERNAME", "<!--##=PROJ.SMTPServerUsername##-->", TRUE); // SMTP server user name
define("EWR_SMTP_SERVER_PASSWORD", "<!--##=PROJ.SMTPServerPassword##-->", TRUE); // SMTP server password
define("EWR_MAX_EMAIL_RECIPIENT", <!--##=PROJ.GetV("MaxEmailRecipient")##-->, TRUE);
define("EWR_MAX_EMAIL_SENT_COUNT", <!--##=PROJ.GetV("MaxEmailSentCount")##-->, TRUE);
define("EWR_MAX_EMAIL_SENT_PERIOD", <!--##=PROJ.GetV("MaxEmailSentPeriod")##-->, TRUE);
define("EWR_EXPORT_EMAIL_COUNTER", EWR_SESSION_STATUS . "_EmailCounter", TRUE);
define("EWR_EMAIL_CHARSET", EWR_CHARSET, TRUE); // Email charset
define("EWR_EMAIL_WRITE_LOG", <!--##=ew_Val(PROJ.GetV("EmailWriteLog"))##-->, TRUE); // Write to log file
define("EWR_EMAIL_LOG_SIZE_LIMIT", <!--##=PROJ.GetV("EmailLogSizeLimit")##-->, TRUE); // Email log field size limit
define("EWR_EMAIL_WRITE_LOG_TO_DATABASE", <!--##=ew_Val(PROJ.GetV("EmailWriteLogToDatabase"))##-->, TRUE); // Write email log to database
<!--##
	var sEmailLogTable = PROJ.GetV("EmailLogTable");
	if (DB.Tables.TableExist(sEmailLogTable))
		sEmailLogTable = SqlTableName(DB.Tables(sEmailLogTable));
##-->
define("EWR_EMAIL_LOG_TABLE_NAME", "<!--##=ew_Quote(sEmailLogTable)##-->", TRUE); // Email log table name
define("EWR_EMAIL_LOG_FIELD_NAME_DATETIME", "<!--##=PROJ.GetV("EmailLogFieldDateTime")##-->", TRUE); // Email log DateTime field name
define("EWR_EMAIL_LOG_FIELD_NAME_IP", "<!--##=PROJ.GetV("EmailLogFieldIp")##-->", TRUE); // Email log IP field name
define("EWR_EMAIL_LOG_FIELD_NAME_SENDER", "<!--##=PROJ.GetV("EmailLogFieldSender")##-->", TRUE); // Email log Sender field name
define("EWR_EMAIL_LOG_FIELD_NAME_RECIPIENT", "<!--##=PROJ.GetV("EmailLogFieldRecipient")##-->", TRUE); // Email log Recipient field name
define("EWR_EMAIL_LOG_FIELD_NAME_SUBJECT", "<!--##=PROJ.GetV("EmailLogFieldSubject")##-->", TRUE); // Email log Subject field name
define("EWR_EMAIL_LOG_FIELD_NAME_MESSAGE", "<!--##=PROJ.GetV("EmailLogFieldMessage")##-->", TRUE); // Email log Message field name

// Export records
$EWR_EXPORT = array(
	"email" => "ExportEmail",
	"print" => "ExportHtml",
	"html" => "ExportHtml",
	"word" => "ExportWord",
	"excel" => "ExportExcel",
	"pdf" => "ExportPdf"
);

define("EWR_USE_COLORBOX", <!--##=ew_Val(PROJ.GetV("UseColorbox"))##-->, TRUE); // Use Colorbox
define("EWR_MULTIPLE_UPLOAD_SEPARATOR", "<!--##=PROJ.GetV("UploadMultipleSeparator")##-->", TRUE); // Multiple upload separator

// MIME types
$EWR_MIME_TYPES = array(
	"pdf"	=>	"application/pdf",
	"exe"	=>	"application/octet-stream",
	"zip"	=>	"application/zip",
	"doc"	=>	"application/msword",
	"docx"	=>	"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
	"xls"	=>	"application/vnd.ms-excel",
	"xlsx"	=>	"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
	"ppt"	=>	"application/vnd.ms-powerpoint",
	"pptx"	=>	"application/vnd.openxmlformats-officedocument.presentationml.presentation",
	"gif"	=>	"image/gif",
	"png"	=>	"image/png",
	"jpeg"	=>	"image/jpg",
	"jpg"	=>	"image/jpg",
	"mp3"	=>	"audio/mpeg",
	"wav"	=>	"audio/x-wav",
	"mpeg"	=>	"video/mpeg",
	"mpg"	=>	"video/mpeg",
	"mpe"	=>	"video/mpeg",
	"mov"	=>	"video/quicktime",
	"avi"	=>	"video/x-msvideo",
	"3gp"	=>	"video/3gpp",
	"css"	=>	"text/css",
	"js"	=>	"application/javascript",
	"htm"	=>	"text/html",
	"html"	=>	"text/html"
);

// Image resize
define("EWR_UPLOADED_FILE_MODE", 0666, TRUE); // Uploaded file mode
define("EWR_UPLOAD_TMP_PATH", "", TRUE); // User upload temp path (relative to app root) e.g. "tmp/"
define("EWR_UPLOAD_DEST_PATH", "<!--##=PROJ.UploadPath##-->", TRUE); // Upload destination path (relative to app root)
define("EWR_THUMBNAIL_DEFAULT_WIDTH", <!--##=PROJ.GetV("ThumbnailDefaultWidth")##-->, TRUE); // Thumbnail default width
define("EWR_THUMBNAIL_DEFAULT_HEIGHT", <!--##=PROJ.GetV("ThumbnailDefaultHeight")##-->, TRUE); // Thumbnail default height
define("EWR_THUMBNAIL_DEFAULT_QUALITY", <!--##=PROJ.GetV("ThumbnailDefaultQuality")##-->, TRUE); // Thumbnail default qualtity (JPEG)
define("EWR_IMAGE_ALLOWED_FILE_EXT", "gif,jpg,png,bmp", TRUE); // Allowed file extensions for images

// Use ILIKE for PostgreSql
define("EWR_USE_ILIKE_FOR_POSTGRESQL", <!--##=ew_Val(PROJ.GetV("PostgreSqlUseIlike"))##-->, TRUE);

// Use collation for MySQL
define("EWR_LIKE_COLLATION_FOR_MYSQL", "<!--##=ew_Quote(PROJ.GetV("MySqlLikeCollation"))##-->", TRUE);

// Use collation for MsSQL
define("EWR_LIKE_COLLATION_FOR_MSSQL", "<!--##=ew_Quote(PROJ.GetV("MsSqlLikeCollation"))##-->", TRUE);

// Comma separated values delimiter
$EWR_CSV_DELIMITER = ",";

// Use mobile menu
$EWR_USE_MOBILE_MENU = <!--##=ew_Val(PROJ.GetV("UseMobileMenu"))##-->;

// Float fields default decimal position
define("EWR_DEFAULT_DECIMAL_PRECISION", 2, TRUE);

// Validate option
define("EWR_CLIENT_VALIDATE", <!--##=ew_Val(PROJ.ClientValidate)##-->, TRUE);
define("EWR_SERVER_VALIDATE", <!--##=ew_Val(PROJ.ServerValidate)##-->, TRUE);

// Auto suggest max entries
define("EWR_AUTO_SUGGEST_MAX_ENTRIES", <!--##=PROJ.GetV("AutoSuggestMaxEntries")##-->, TRUE);

// Checkbox and radio button groups
define("EWR_ITEM_TEMPLATE_CLASSNAME", "ewTemplate", TRUE);
define("EWR_ITEM_TABLE_CLASSNAME", "ewItemTable", TRUE);

// Cookies
define("EWR_COOKIE_EXPIRY_TIME", time() + 365*24*60*60, TRUE); // Change cookie expiry time here

// Page break content
define("EWR_EXPORT_PAGE_BREAK_CONTENT", "<div class=\"ewPageBreak\">&nbsp;</div>", TRUE);

<!--## if (!bMultiLanguage) { ##-->
/**
 * Time zone
 * Read http://www.php.net/date_default_timezone_set for details
 * and http://www.php.net/timezones for supported time zones
*/
if (function_exists("date_default_timezone_set"))
	date_default_timezone_set($EWR_DEFAULT_TIME_ZONE);
<!--## } ##-->

if (!isset($conn)) {
	// Common objects
	$conn = NULL; // Connection
	$rs = NULL; // Recordset
	$rsgrp = NULL; // Recordset
	$Page = NULL; // Page
	$OldPage = NULL; // Old Page
	$Table = NULL; // Main table
	$Security = NULL; // Security

	// Current language
	$gsLanguage = "";

	// Token
	$gsToken = "";
}

if (!isset($ReportLanguage)) {
	$ReportLanguage = NULL; // Language
}

// Chart
$Chart = NULL;

// Timer
$gsTimer = NULL;

// Used by header.php, export checking
$gsExport = "";
$gsCustomExport = "";
$gsExportFile = "";
$gbDrillDownInPanel = FALSE;
$gsEmailContentType = "";

// Used by header.php/footer.php, skip header/footer checking
$gbSkipHeaderFooter = FALSE;
$gbOldSkipHeaderFooter = $gbSkipHeaderFooter;

// Used by extended filter
$gsFormError = "";

// Debug message
$gsDebugMsg = "";
if (!isset($ADODB_OUTP)) $ADODB_OUTP = 'ewr_SetDebugMsg';

// Keep temp images name for PDF export for delete
$gTmpImages = array();

// Mobile detect
$MobileDetect = NULL;

// Breadcrumb
$ReportBreadcrumb = NULL;
define("EWR_SESSION_BREADCRUMB", EWR_PROJECT_NAME . "_Breadcrumb", TRUE);

// FusionCharts Free
define("EWR_FUSIONCHARTS_FREE", <!--##=ew_Val(UseFusionChartsFree)##-->, TRUE); // For Gannt, Candlestick and Funnel charts only
define("EWR_FUSIONCHARTS_FREE_CHART_PATH", "FusionChartsFree/Charts/", TRUE);
define("EWR_FUSIONCHARTS_FREE_JSCLASS_FILE", "FusionChartsFree/JSClass/FusionCharts.js", TRUE);

// Gantt charts
define("EWR_GANTT_INTERVAL_YEAR", 5, TRUE);
define("EWR_GANTT_INTERVAL_QUARTER", 4, TRUE);
define("EWR_GANTT_INTERVAL_MONTH", 3, TRUE);
define("EWR_GANTT_INTERVAL_WEEK", 2, TRUE);
define("EWR_GANTT_INTERVAL_DAY", 1, TRUE);
define("EWR_GANTT_INTERVAL_NONE", 0, TRUE);
define("EWR_GANTT_WEEK_START", 1, TRUE); // 0 (for Sunday) through 6 (for Saturday)
define("EWR_GANTT_USE_ADODB_TIME", FALSE, TRUE); // Use ADODB date time (for dates outside 1901-2038 on Unix and 1970-2038 on Windows)
if (EWR_GANTT_USE_ADODB_TIME)
	include_once $EWR_RELATIVE_PATH . "adodb5/adodb-time.inc.php";
?>
<!--##/session##-->

<!--##session ewconfigmenu##-->
<?php
// Menu
define("EWR_MENUBAR_ID", "RootMenu", TRUE);
define("EWR_MENUBAR_BRAND", "", TRUE);
define("EWR_MENUBAR_BRAND_HYPERLINK", "", TRUE);
define("EWR_MENUBAR_CLASSNAME", "", TRUE);
//define("EWR_MENU_CLASSNAME", "nav nav-list", TRUE);
define("EWR_MENU_CLASSNAME", "dropdown-menu", TRUE);
define("EWR_SUBMENU_CLASSNAME", "dropdown-menu", TRUE);
define("EWR_SUBMENU_DROPDOWN_IMAGE", "", TRUE);
define("EWR_SUBMENU_DROPDOWN_ICON_CLASSNAME", "", TRUE);
define("EWR_MENU_DIVIDER_CLASSNAME", "divider", TRUE);
define("EWR_MENU_ITEM_CLASSNAME", "dropdown-submenu", TRUE);
define("EWR_SUBMENU_ITEM_CLASSNAME", "dropdown-submenu", TRUE);
define("EWR_MENU_ACTIVE_ITEM_CLASS", "active", TRUE);
define("EWR_SUBMENU_ACTIVE_ITEM_CLASS", "active", TRUE);
define("EWR_MENU_ROOT_GROUP_TITLE_AS_SUBMENU", FALSE, TRUE);
define("EWR_SHOW_RIGHT_MENU", FALSE, TRUE);
?>
<!--##/session##-->

<!--##session ewconfigpdf##-->
<?php
define("EWR_PDF_STYLESHEET_FILENAME", "", TRUE); // Export PDF CSS styles
?>
<!--##/session##-->

<!--##session ewconfigchart##-->
<?php
// FusionCharts Trial
define("EWR_FUSIONCHARTS_PATH", "FusionChartsTrial/js/", TRUE);
?>
<!--##/session##-->