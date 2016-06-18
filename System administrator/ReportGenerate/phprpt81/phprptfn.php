<!--##session reportfunctions##-->
<?php

// Functions for PHP Report Maker 8
// (C) 2007-2014 e.World Technology Limited

<!--##
	var fn = "%cls%info.php";
	// Database name
	sDbVar = DB.DBVar;
	if (PROJ.OutputNameLCase)
		sDbVar = sDbVar.toLowerCase();
	sDbHelperFile = ew_GetFileNameByCtrlID("dbhelper");
##-->
// Auto load class
function ewr_AutoLoad($class) {
	global $EWR_RELATIVE_PATH;
	if (substr($class, 0, 2) == "cr") {
<!--## if (PROJ.GetV("GenDatabaseHelper")) { ##-->
		if ($class == "cr<!--##=sDbVar##-->_db") {
			$file = "<!--##=ew_Quote(sDbHelperFile)##-->";
		} else {
<!--## } ##-->
		$fn = "<!--##=ew_Quote(fn)##-->";
		$file = str_replace("%cls%", substr($class, 2), $fn);
	<!--## if (PROJ.OutputNameLCase) { ##-->
		$file = strtolower($file);
	<!--## } ##-->
<!--## if (PROJ.GetV("GenDatabaseHelper")) { ##-->
		}
<!--## } ##-->
		if (file_exists($EWR_RELATIVE_PATH . $file))
			include $EWR_RELATIVE_PATH . $file;
	}
}

spl_autoload_register("ewr_AutoLoad");

if (!function_exists("G")) {
	function &G($name) {
		return $GLOBALS[$name];
	}
}

// Get connection object
if (!function_exists("Conn")) {
	function &Conn() {
    	return $GLOBALS["conn"];
	}
}

// Get security object
if (!function_exists("Security")) {
	function &Security() {
    	return $GLOBALS["Security"];
	}
}

// Get language object
if (!function_exists("Language")) {
	function &Language() {
    	return $GLOBALS["ReportLanguage"];
	}
}

// Get breadcrumb object
if (!function_exists("Breadcrumb")) {
	function &Breadcrumb() {
    	return $GLOBALS["ReportBreadcrumb"];
	}
}

// Is admin
if (!function_exists("IsAdmin")) {
	function IsAdmin() {
		global $Security;
		return (isset($Security)) ? $Security->IsAdmin() : (@$_SESSION[EWR_SESSION_SYS_ADMIN] == 1);
	}
}

// Get current project ID
function CurrentProjectID() {
	if (isset($GLOBALS["Page"]))
		return $GLOBALS["Page"]->ProjectID;
	return "<!--##=PROJ.ProjID##-->";
}

// Get current page object
function &CurrentPage() {
	return $GLOBALS["Page"];
}

// Get current main table object
function &CurrentTable() {
	return $GLOBALS["Table"];
}

/**
 * Langauge class for reports
 */
class crLanguage {
	var $LanguageId;
	var $Phrases = NULL;
	var $LanguageFolder = EWR_LANGUAGE_FOLDER;

	// Constructor
	function __construct($langfolder = "", $langid = "") {
		global $gsLanguage;
		if ($langfolder <> "")
			$this->LanguageFolder = $langfolder;
		$this->LoadFileList(); // Set up file list
		if ($langid <> "") { // Set up language id
			$this->LanguageId = $langid;
			$_SESSION[EWR_SESSION_LANGUAGE_ID] = $this->LanguageId;
		} elseif (@$_GET["language"] <> "") {
			$this->LanguageId = $_GET["language"];
			$_SESSION[EWR_SESSION_LANGUAGE_ID] = $this->LanguageId;
		} elseif (@$_SESSION[EWR_SESSION_LANGUAGE_ID] <> "") {
			$this->LanguageId = $_SESSION[EWR_SESSION_LANGUAGE_ID];
		} else {
			$this->LanguageId = EWR_LANGUAGE_DEFAULT_ID;
		}
		$gsLanguage = $this->LanguageId;
		$this->Load($this->LanguageId);
	}

	// Load language file list
	function LoadFileList() {
		global $EWR_LANGUAGE_FILE;
		if (is_array($EWR_LANGUAGE_FILE)) {
			$cnt = count($EWR_LANGUAGE_FILE);
			for ($i = 0; $i < $cnt; $i++)
				$EWR_LANGUAGE_FILE[$i][1] = $this->LoadFileDesc($this->LanguageFolder . $EWR_LANGUAGE_FILE[$i][2]);
		}
	}

	// Load language file description
	function LoadFileDesc($File) {
		if (EWR_USE_DOM_XML) {
			$this->Phrases = new crXMLDocument();
			if ($this->Phrases->Load($File))
				return $this->GetNodeAtt($this->Phrases->DocumentElement(), "desc");
		} else {
			$ar = ewr_Xml2Array(substr(file_get_contents($File), 0, 512)); // Just read the first part
			return (is_array($ar)) ? @$ar['ew-language']['attr']['desc'] : "";
		}
	}

	// Load language file
	function Load($id) {
		global $EWR_DEFAULT_DECIMAL_POINT, $EWR_DEFAULT_THOUSANDS_SEP, $EWR_DEFAULT_MON_DECIMAL_POINT, $EWR_DEFAULT_MON_THOUSANDS_SEP,
		$EWR_DEFAULT_CURRENCY_SYMBOL, $EWR_DEFAULT_POSITIVE_SIGN, $EWR_DEFAULT_NEGATIVE_SIGN, $EWR_DEFAULT_FRAC_DIGITS,
		$EWR_DEFAULT_P_CS_PRECEDES, $EWR_DEFAULT_P_SEP_BY_SPACE, $EWR_DEFAULT_N_CS_PRECEDES, $EWR_DEFAULT_N_SEP_BY_SPACE,
		$EWR_DEFAULT_P_SIGN_POSN, $EWR_DEFAULT_N_SIGN_POSN, $EWR_DEFAULT_LOCALE, $EWR_DEFAULT_TIME_ZONE;

		$sFileName = $this->GetFileName($id);
		if ($sFileName == "")
			$sFileName = $this->GetFileName(EWR_LANGUAGE_DEFAULT_ID);
		if ($sFileName == "")
			return;
		if (EWR_USE_DOM_XML) {
			$this->Phrases = new crXMLDocument();
			$this->Phrases->Load($sFileName);
		} else {
			if (is_array(@$_SESSION[EWR_PROJECT_NAME . "_" . $sFileName])) {
				$this->Phrases = $_SESSION[EWR_PROJECT_NAME . "_" . $sFileName];
			} else {
				$this->Phrases = ewr_Xml2Array(file_get_contents($sFileName));
			}
		}

		// Set up locale / currency format for language
		if ($this->LocalePhrase("use_system_locale") == "1") { // Use system locale
			$langLocale = $this->LocalePhrase("locale");
			if ($langLocale <> "")
				@setlocale(LC_ALL, $langLocale); // Set language locale
			extract(ewr_LocaleConv());
			if (!empty($decimal_point)) $EWR_DEFAULT_DECIMAL_POINT = $decimal_point;
			if (!empty($thousands_sep)) $EWR_DEFAULT_THOUSANDS_SEP = $thousands_sep;
			if (!empty($mon_decimal_point)) $EWR_DEFAULT_MON_DECIMAL_POINT = $mon_decimal_point;
			if (empty($EWR_DEFAULT_MON_DECIMAL_POINT)) $EWR_DEFAULT_MON_DECIMAL_POINT = $EWR_DEFAULT_DECIMAL_POINT;
			if (!empty($mon_thousands_sep)) $EWR_DEFAULT_MON_THOUSANDS_SEP = $mon_thousands_sep;
			if (empty($EWR_DEFAULT_MON_THOUSANDS_SEP)) $EWR_DEFAULT_MON_THOUSANDS_SEP = $EWR_DEFAULT_THOUSANDS_SEP;
			if (!empty($currency_symbol)) {
				if (EWR_CHARSET == "utf-8") {
					if ($int_curr_symbol == "EUR" && ord($currency_symbol) == 128) {
						$currency_symbol = "\xe2\x82\xac";
					} elseif ($int_curr_symbol == "GBP" && ord($currency_symbol) == 163) {
						$currency_symbol = "\xc2\xa3";
					} elseif ($int_curr_symbol == "JPY" && ord($currency_symbol) == 92) {
						$currency_symbol = "\xc2\xa5";
					}
				}
				$EWR_DEFAULT_CURRENCY_SYMBOL = $currency_symbol;
			}
			if (!empty($positive_sign)) $EWR_DEFAULT_POSITIVE_SIGN = $positive_sign;
			if (!empty($negative_sign)) $EWR_DEFAULT_NEGATIVE_SIGN = $negative_sign;
			if (!empty($frac_digits) && $frac_digits <> CHAR_MAX) $EWR_DEFAULT_FRAC_DIGITS = $frac_digits;
			if (!empty($p_cs_precedes) && $p_cs_precedes <> CHAR_MAX) $EWR_DEFAULT_P_CS_PRECEDES = $p_cs_precedes;
			if (!empty($p_sep_by_space) && $p_sep_by_space <> CHAR_MAX) $EWR_DEFAULT_P_SEP_BY_SPACE = $p_sep_by_space;
			if (!empty($n_cs_precedes) && $n_cs_precedes <> CHAR_MAX) $EWR_DEFAULT_N_CS_PRECEDES = $n_cs_precedes;
			if (!empty($n_sep_by_space) && $n_sep_by_space <> CHAR_MAX) $EWR_DEFAULT_N_SEP_BY_SPACE = $n_sep_by_space;
			if (!empty($p_sign_posn) && $p_sign_posn <> CHAR_MAX) $EWR_DEFAULT_P_SIGN_POSN = $p_sign_posn;
			if (!empty($n_sign_posn) && $n_sign_posn <> CHAR_MAX) $EWR_DEFAULT_N_SIGN_POSN = $n_sign_posn;
		} else { // Use language file
			$ar = array("p_cs_precedes", "p_sep_by_space", "n_cs_precedes", "n_sep_by_space");
			foreach ($EWR_DEFAULT_LOCALE as $key => $value) {
				if ($this->LocalePhrase($key) <> "")
					$EWR_DEFAULT_LOCALE[$key] = in_array($key, $ar) ? $this->LocalePhrase($key) == "1" : $this->LocalePhrase($key);
			}
		}

	<!--##
		bMultiLanguage = PROJ.MultiLanguage;
		if (bMultiLanguage) {
	##-->
		/**
		 * Time zone
		 * Read http://www.php.net/date_default_timezone_set for details
		 * and http://www.php.net/timezones for supported time zones
		*/
		// Set up time zone from language file for multi-language site
		if ($this->LocalePhrase("time_zone") <> "") $EWR_DEFAULT_TIME_ZONE = $this->LocalePhrase("time_zone");
		if (function_exists("date_default_timezone_set") && $EWR_DEFAULT_TIME_ZONE <> "")
			date_default_timezone_set($EWR_DEFAULT_TIME_ZONE);
	<!--##
		}
	##-->

	}

	// Get language file name
	function GetFileName($Id) {
		global $EWR_LANGUAGE_FILE;
		if (is_array($EWR_LANGUAGE_FILE)) {
			$cnt = count($EWR_LANGUAGE_FILE);
			for ($i = 0; $i < $cnt; $i++)
				if ($EWR_LANGUAGE_FILE[$i][0] == $Id) {
					return $this->LanguageFolder . $EWR_LANGUAGE_FILE[$i][2];
			}
		}
		return "";
	}

	// Get node attribute
	function GetNodeAtt($Nodes, $Att) {
		$value = ($Nodes) ? $this->Phrases->GetAttribute($Nodes, $Att) : "";
		//return ewr_ConvertFromUtf8($value);
		return $value;
	}

	// Set node attribute
	function SetNodeAtt($Nodes, $Att, $Value) {
		if ($Nodes)
			$this->Phrases->SetAttribute($Nodes, $Att, $Value);
	}

	// Get locale phrase
	function LocalePhrase($Id) {
		if (is_object($this->Phrases)) {
			return $this->GetNodeAtt($this->Phrases->SelectSingleNode("//locale/phrase[@id='" . strtolower($Id) . "']"), "value");
		} elseif (is_array($this->Phrases)) {
			return ewr_ConvertFromUtf8(@$this->Phrases['ew-language']['locale']['phrase'][strtolower($Id)]['attr']['value']);
		}
	}

	// Set locale phrase
	function setLocalePhrase($Id, $Value) {
		if (is_object($this->Phrases)) {
			$this->SetNodeAtt($this->Phrases->SelectSingleNode("//locale/phrase[@id='" . strtolower($Id) . "']"), "value", $Value);
		} elseif (is_array($this->Phrases)) {
			$this->Phrases['ew-language']['locale']['phrase'][strtolower($Id)]['attr']['value'] = $Value;
		}
	}

	// Get phrase
	function Phrase($Id, $UseText = FALSE) {
		if (is_object($this->Phrases)) {
			$ImageUrl = $this->GetNodeAtt($this->Phrases->SelectSingleNode("//global/phrase[@id='" . strtolower($Id) . "']"), "imageurl");
			$ImageWidth = $this->GetNodeAtt($this->Phrases->SelectSingleNode("//global/phrase[@id='" . strtolower($Id) . "']"), "imagewidth");
			$ImageHeight = $this->GetNodeAtt($this->Phrases->SelectSingleNode("//global/phrase[@id='" . strtolower($Id) . "']"), "imageheight");
			$ImageClass = $this->GetNodeAtt($this->Phrases->SelectSingleNode("//global/phrase[@id='" . strtolower($Id) . "']"), "class");
			$Text = $this->GetNodeAtt($this->Phrases->SelectSingleNode("//global/phrase[@id='" . strtolower($Id) . "']"), "value");
		} elseif (is_array($this->Phrases)) {
			$ImageUrl = ewr_ConvertFromUtf8(@$this->Phrases['ew-language']['global']['phrase'][strtolower($Id)]['attr']['imageurl']);
			$ImageWidth = ewr_ConvertFromUtf8(@$this->Phrases['ew-language']['global']['phrase'][strtolower($Id)]['attr']['imagewidth']);
			$ImageHeight = ewr_ConvertFromUtf8(@$this->Phrases['ew-language']['global']['phrase'][strtolower($Id)]['attr']['imageheight']);
			$ImageClass = ewr_ConvertFromUtf8(@$this->Phrases['ew-language']['global']['phrase'][strtolower($Id)]['attr']['class']);
			$Text = ewr_ConvertFromUtf8(@$this->Phrases['ew-language']['global']['phrase'][strtolower($Id)]['attr']['value']);
		}
		if (!$UseText && $ImageClass <> "") {
			return "<span data-phrase=\"" . $Id . "\" class=\"" . $ImageClass . "\" data-caption=\"" . ewr_HtmlEncode($Text) . "\"></span>";
		} elseif (!$UseText && $ImageUrl <> "") {
			$style = ($ImageWidth <> "") ? "width: " . $ImageWidth . "px;" : "";
			$style .= ($ImageHeight <> "") ? "height: " . $ImageHeight . "px;" : "";
			return "<img data-phrase=\"" . $Id . "\" src=\"" . ewr_HtmlEncode($ImageUrl) . "\" style=\"" . $style . "\" alt=\"" . ewr_HtmlEncode($Text) . "\" title=\"" . ewr_HtmlEncode($Text) . "\">";
		} else {
			return $Text;
		}
	}

	// Set phrase
	function setPhrase($Id, $Value) {
		if (is_object($this->Phrases)) {
			$this->SetNodeAtt($this->Phrases->SelectSingleNode("//global/phrase[@id='" . strtolower($Id) . "']"), "value", $Value);
		} elseif (is_array($this->Phrases)) {
			$this->Phrases['ew-language']['global']['phrase'][strtolower($Id)]['attr']['value'] = $Value;
		}
	}

	// Get project phrase
	function ProjectPhrase($Id) {
		if (is_object($this->Phrases)) {
			return $this->GetNodeAtt($this->Phrases->SelectSingleNode("//project/phrase[@id='" . strtolower($Id) . "']"), "value");
		} elseif (is_array($this->Phrases)) {
			return ewr_ConvertFromUtf8(@$this->Phrases['ew-language']['project']['phrase'][strtolower($Id)]['attr']['value']);
		}
	}

	// Set project phrase
	function setProjectPhrase($Id, $Value) {
		if (is_object($this->Phrases)) {
			$this->SetNodeAtt($this->Phrases->SelectSingleNode("//project/phrase[@id='" . strtolower($Id) . "']"), "value", $Value);
		} elseif (is_array($this->Phrases)) {
			$this->Phrases['ew-language']['project']['phrase'][strtolower($Id)]['attr']['value'] = $Value;
		}
	}

	// Get menu phrase
	function MenuPhrase($MenuId, $Id) {
		if (is_object($this->Phrases)) {
			return $this->GetNodeAtt($this->Phrases->SelectSingleNode("//project/menu[@id='" . $MenuId . "']/phrase[@id='" . strtolower($Id) . "']"), "value");
		} elseif (is_array($this->Phrases)) {
			return ewr_ConvertFromUtf8(@$this->Phrases['ew-language']['project']['menu'][$MenuId]['phrase'][strtolower($Id)]['attr']['value']);
		}
	}

	// Set menu phrase
	function setMenuPhrase($MenuId, $Id, $Value) {
		if (is_object($this->Phrases)) {
			$this->SetNodeAtt($this->Phrases->SelectSingleNode("//project/menu[@id='" . $MenuId . "']/phrase[@id='" . strtolower($Id) . "']"), "value", $Value);
		} elseif (is_array($this->Phrases)) {
			$this->Phrases['ew-language']['project']['menu'][$MenuId]['phrase'][strtolower($Id)]['attr']['value'] = $Value;
		}
	}

	// Get table phrase
	function TablePhrase($TblVar, $Id) {
		if (is_object($this->Phrases)) {
			return $this->GetNodeAtt($this->Phrases->SelectSingleNode("//project/table[@id='" . strtolower($TblVar) . "']/phrase[@id='" . strtolower($Id) . "']"), "value");
		} elseif (is_array($this->Phrases)) {
			return ewr_ConvertFromUtf8(@$this->Phrases['ew-language']['project']['table'][strtolower($TblVar)]['phrase'][strtolower($Id)]['attr']['value']);
		}
	}

	// Set table phrase
	function setTablePhrase($TblVar, $Id, $Value) {
		if (is_object($this->Phrases)) {
			$this->SetNodeAtt($this->Phrases->SelectSingleNode("//project/table[@id='" . strtolower($TblVar) . "']/phrase[@id='" . strtolower($Id) . "']"), "value", $Value);
		} elseif (is_array($this->Phrases)) {
			$this->Phrases['ew-language']['project']['table'][strtolower($TblVar)]['phrase'][strtolower($Id)]['attr']['value'] = $Value;
		}
	}

	// Get chart phrase
	function ChartPhrase($TblVar, $ChtVar, $Id) {
		if (is_object($this->Phrases)) {
			return $this->GetNodeAtt($this->Phrases->SelectSingleNode("//project/table[@id='" . strtolower($TblVar) . "']/chart[@id='" . strtolower($ChtVar) . "']/phrase[@id='" . strtolower($Id) . "']"), "value");
		} elseif (is_array($this->Phrases)) {
			return ewr_ConvertFromUtf8(@$this->Phrases['ew-language']['project']['table'][strtolower($TblVar)]['chart'][strtolower($ChtVar)]['phrase'][strtolower($Id)]['attr']['value']);
		}
	}

	// Set chart phrase
	function setChartPhrase($TblVar, $FldVar, $Id, $Value) {
		if (is_object($this->Phrases)) {
			$this->SetNodeAtt($this->Phrases->SelectSingleNode("//project/table[@id='" . strtolower($TblVar) . "']/chart[@id='" . strtolower($ChtVar) . "']/phrase[@id='" . strtolower($Id) . "']"), "value", $Value);
		} elseif (is_array($this->Phrases)) {
			$this->Phrases['ew-language']['project']['table'][strtolower($TblVar)]['chart'][strtolower($FldVar)]['phrase'][strtolower($Id)]['attr']['value'] = $Value;
		}
	}

	// Get field phrase
	function FieldPhrase($TblVar, $FldVar, $Id) {
		if (is_object($this->Phrases)) {
			return $this->GetNodeAtt($this->Phrases->SelectSingleNode("//project/table[@id='" . strtolower($TblVar) . "']/field[@id='" . strtolower($FldVar) . "']/phrase[@id='" . strtolower($Id) . "']"), "value");
		} elseif (is_array($this->Phrases)) {
			return ewr_ConvertFromUtf8(@$this->Phrases['ew-language']['project']['table'][strtolower($TblVar)]['field'][strtolower($FldVar)]['phrase'][strtolower($Id)]['attr']['value']);
		}
	}

	// Set field phrase
	function setFieldPhrase($TblVar, $FldVar, $Id, $Value) {
		if (is_object($this->Phrases)) {
			$this->SetNodeAtt($this->Phrases->SelectSingleNode("//project/table[@id='" . strtolower($TblVar) . "']/field[@id='" . strtolower($FldVar) . "']/phrase[@id='" . strtolower($Id) . "']"), "value", $Value);
		} elseif (is_array($this->Phrases)) {
			$this->Phrases['ew-language']['project']['table'][strtolower($TblVar)]['field'][strtolower($FldVar)]['phrase'][strtolower($Id)]['attr']['value'] = $Value;
		}
	}

	// Output XML as JSON
	function XmlToJSON($XPath) {
		$NodeList = $this->Phrases->SelectNodes($XPath);
		$Str = "{";
		foreach ($NodeList as $Node) {
			$Id = $this->GetNodeAtt($Node, "id");
			$Value = $this->GetNodeAtt($Node, "value");
			$Str .= "\"" . ewr_JsEncode2($Id) . "\":\"" . ewr_JsEncode2($Value) . "\",";
		}
		if (substr($Str, -1) == ",") $Str = substr($Str, 0, strlen($Str)-1);
		$Str .= "}";
		return $Str;
	}
	
	// Output array as JSON
	function ArrayToJSON($client) {
		$ar = @$this->Phrases['ew-language']['global']['phrase'];
		$Str = "{";
		if (is_array($ar)) {
			foreach ($ar as $id => $node) {
				$is_client = @$node['attr']['client'] == '1';
				$value = ewr_ConvertFromUtf8(@$node['attr']['value']);
				if (!$client || ($client && $is_client))
					$Str .= "\"" . ewr_JsEncode2($id) . "\":\"" . ewr_JsEncode2($value) . "\",";
			}
		}
		if (substr($Str, -1) == ",") $Str = substr($Str, 0, strlen($Str)-1);
		$Str .= "}";
		return $Str;
	}

	// Output all phrases as JSON
	function AllToJSON() {
		if (is_object($this->Phrases)) {
			return "var ewLanguage = new ewr_Language(" . $this->XmlToJSON("//global/phrase") . ");";
		} elseif (is_array($this->Phrases)) {
			return "var ewLanguage = new ewr_Language(" . $this->ArrayToJSON(FALSE) . ");";
		}
	}

	// Output client phrases as JSON
	function ToJSON() {
		if (is_object($this->Phrases)) {
			return "var ewLanguage = new ewr_Language(" . $this->XmlToJSON("//global/phrase[@client='1']") . ");";
		} elseif (is_array($this->Phrases)) {
			return "var ewLanguage = new ewr_Language(" . $this->ArrayToJSON(TRUE) . ");";
		}
	}

	// Output language selection form
	function SelectionForm() {
		global $EWR_LANGUAGE_FILE, $gsLanguage;
		$form = "";
		if (is_array($EWR_LANGUAGE_FILE)) {
			$cnt = count($EWR_LANGUAGE_FILE);
			if ($cnt > 1) {
				for ($i = 0; $i < $cnt; $i++) {
					$langid = $EWR_LANGUAGE_FILE[$i][0];
					$langphrase = $EWR_LANGUAGE_FILE[$i][1];
					$selected = ($langid == $gsLanguage) ? " selected=\"selected\"" : "";
					$phrase = $this->Phrase($langid);
					if ($phrase == "") // Use description for button
						$phrase = $langphrase;
					$form .= "<option value=\"" . $langid . "\"" . $selected . ">" . $phrase . "</option>";
				}
			}
		}
		if ($form <> "")
			$form = "<div class=\"ewLanguageOption\"><select class=\"form-control\" id=\"ewLanguage\" name=\"ewLanguage\" onchange=\"ewr_SetLanguage(this);\">" . $form . "</select></div>";
		return $form;
	}

}

// Get numeric formatting information
function ewr_LocaleConv() {
	$info = defined("EWR_REPORT_DEFAULT_LOCALE") ? json_decode(EWR_REPORT_DEFAULT_LOCALE, TRUE) : NULL;
	return ($info) ? $info : localeconv();
}

// Convert XML to array
function ewr_Xml2Array($contents) {
	if (!$contents) return array(); 
	
	if (!function_exists('xml_parser_create')) return FALSE;
	
	$get_attributes = 1; // Always get attributes. DO NOT CHANGE!

	// Get the XML Parser of PHP
	$parser = xml_parser_create();
	xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); // Always return in utf-8
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, trim($contents), $xml_values);
	xml_parser_free($parser);
	
	if (!$xml_values) return;
	
	$xml_array = array();
	$parents = array();
	$opened_tags = array();
	$arr = array();
	
	$current = &$xml_array;
	
	$repeated_tag_index = array(); // Multiple tags with same name will be turned into an array
	
	foreach ($xml_values as $data) {
	
		unset($attributes, $value); // Remove existing values
		
		// Extract these variables into the foreach scope
		// - tag(string), type(string), level(int), attributes(array)
		extract($data);
		
		$result = array();
		 
		if (isset($value))
			$result['value'] = $value; // Put the value in a assoc array
		
		// Set the attributes
		if (isset($attributes) and $get_attributes) {
			foreach ($attributes as $attr => $val)
				$result['attr'][$attr] = $val; // Set all the attributes in a array called 'attr'
		} 
		
		// See tag status and do the needed
		if ($type == "open") { // The starting of the tag '<tag>'
		
			$parent[$level-1] = &$current;
			if (!is_array($current) || !in_array($tag, array_keys($current))) { // Insert New tag
				if ($tag <> 'ew-language' && @$result['attr']['id'] <> '') { // 
					$last_item_index = $result['attr']['id'];
					$current[$tag][$last_item_index] = $result;
					$repeated_tag_index[$tag.'_'.$level] = 1;
					$current = &$current[$tag][$last_item_index];
				} else {
					$current[$tag] = $result;
					$repeated_tag_index[$tag.'_'.$level] = 0;
					$current = &$current[$tag];
				}
			} else { // Another element with the same tag name
				if ($repeated_tag_index[$tag.'_'.$level] > 0) { // If there is a 0th element it is already an array
					if (@$result['attr']['id'] <> '') {
						$last_item_index = $result['attr']['id'];
					} else {
						$last_item_index = $repeated_tag_index[$tag.'_'.$level];
					}
					$current[$tag][$last_item_index] = $result;
					$repeated_tag_index[$tag.'_'.$level]++;
				} else { // Make the value an array if multiple tags with the same name appear together
					$temp = $current[$tag];
					$current[$tag] = array();
					if (@$temp['attr']['id'] <> '') {
						$current[$tag][$temp['attr']['id']] = $temp;
					} else {
						$current[$tag][] = $temp;
					}
					if (@$result['attr']['id'] <> '') {
						$last_item_index = $result['attr']['id'];
					} else {
						$last_item_index = 1;
					}
					$current[$tag][$last_item_index] = $result;
					$repeated_tag_index[$tag.'_'.$level] = 2;
				} 
				$current = &$current[$tag][$last_item_index];
			}
		
		} elseif ($type == "complete") { // Tags that ends in one line '<tag />'
		
			if (!isset($current[$tag])) { // New key
				$current[$tag] = array(); // Always use array for "complete" type
				if (@$result['attr']['id'] <> '') {
					$current[$tag][$result['attr']['id']] = $result;
				} else {
					$current[$tag][] = $result;
				}
				$repeated_tag_index[$tag.'_'.$level] = 1;
			} else { // Existing key
				if (@$result['attr']['id'] <> '') {
			  	$current[$tag][$result['attr']['id']] = $result;
				} else {
					$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
				}
			  $repeated_tag_index[$tag.'_'.$level]++;
			}
		
		} elseif ($type == 'close') { // End of tag '</tag>'
			$current = &$parent[$level-1];
		}
	}
	 
	return($xml_array);
}

/**
 * XML document class
 */
class crXMLDocument {
	var $Encoding = "utf-8";
	var $RootTagName;
	var $RowTagName;
	var $XmlDoc = FALSE;
	var $XmlTbl;
	var $XmlRow;
	var $NullValue = 'NULL';

	function __construct($encoding = "") {
		if ($encoding <> "")
			$this->Encoding = $encoding;
		if ($this->Encoding <> "") {
			$this->XmlDoc = new DOMDocument("1.0", strval($this->Encoding));
		} else {
			$this->XmlDoc = new DOMDocument("1.0");
		}
	}

	function Load($filename) {
		$filepath = realpath($filename);
		return $this->XmlDoc->load($filepath);
	}
	
	function &DocumentElement() {
		$de = $this->XmlDoc->documentElement;
		return $de;
	}
	
	function GetAttribute($element, $name) {
		return ($element) ? ewr_ConvertFromUtf8($element->getAttribute($name)) : "";
	}
	
	function SetAttribute($element, $name, $value) {
		if ($element)
			$element->setAttribute($name, ewr_ConvertToUtf8($value));
	}
	
	function SelectSingleNode($query) {
		$elements = $this->SelectNodes($query);
		return ($elements->length > 0) ? $elements->item(0) : NULL;
	}
	
	function SelectNodes($query) {
		$xpath = new DOMXPath($this->XmlDoc);
		return $xpath->query($query);
	}
	
	function AddRoot($roottagname = 'table') {
		$this->RootTagName = $roottagname;
		$this->XmlTbl = $this->XmlDoc->createElement($this->RootTagName);
		$this->XmlDoc->appendChild($this->XmlTbl);
	}

	function AddRow($rowtagname = 'row') {
		$this->RowTagName = $rowtagname;
		$this->XmlRow = $this->XmlDoc->createElement($this->RowTagName);
		if ($this->XmlTbl)
			$this->XmlTbl->appendChild($this->XmlRow);
	}

	function AddField($name, $value) {
		if (is_null($value)) $value = $this->NullValue;
		$value = ewr_ConvertToUtf8($value); // Convert to UTF-8
		$xmlfld = $this->XmlDoc->createElement($name);
		$this->XmlRow->appendChild($xmlfld);
		$xmlfld->appendChild($this->XmlDoc->createTextNode($value));
	}

	function XML() {
		return $this->XmlDoc->saveXML();
	}

}

// Select nodes from XML document
function &ewr_SelectNodes(&$xmldoc, $query) {
	if ($xmldoc) {
		$xpath = new DOMXPath($xmldoc);
		return $xpath->query($query);
	}
	return NULL;
}

// Select single node from XML document
function &ewr_SelectSingleNode(&$xmldoc, $query) {
	$elements = ewr_SelectNodes($xmldoc, $query);
	return ($elements && $elements->length > 0) ? $elements->item(0) : NULL;
}

// Debug timer
class crTimer {
	var $StartTime;
	var $EndTime;
	
	function __construct($start = TRUE) {
		if ($start)
			$this->Start();
	}
	
	function GetTime() {
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
	
	// Get script start time
	function Start() {
		if (EWR_DEBUG_ENABLED)
			$this->StartTime = $this->GetTime();
	}
	
	// Display elapsed time (in seconds)
	function Stop() {
		if (EWR_DEBUG_ENABLED)
			$this->EndTime = $this->GetTime();
		if (isset($this->EndTime) && isset($this->StartTime) &&
			$this->EndTime > $this->StartTime)
			echo '<p>Page processing time: ' . ($this->EndTime - $this->StartTime) . ' seconds</p>';
	}

}

/**
 * Breadcrumb class
 */
class crBreadcrumb {
	var $Links = array();
	var $SessionLinks = array();
	var $Visible = TRUE;

	// Constructor
	function __construct() {
		global $ReportLanguage;
		$this->Links[] = array("home", "HomePage", "<!--##=sFnHomePage##-->", "ewHome", "", FALSE); // Home
	}

	// Check if an item exists
	function Exists($pageid, $table, $pageurl) {
		if (is_array($this->Links)) {
			$cnt = count($this->Links);
			for ($i = 0; $i < $cnt; $i++) {
				@list($id, $title, $url, $tablevar, $cur) = $this->Links[$i];
				if ($pageid == $id && $table == $tablevar && $pageurl == $url)
					return TRUE;
			}
		}
		return FALSE;
	}

	// Add breadcrumb
	function Add($pageid, $pagetitle, $pageurl, $pageurlclass = "", $table = "", $current = FALSE) {

		// Load session links
		$this->LoadSession();

		// Get list of master tables
		$mastertable = array();
		if ($table <> "") {
			$tablevar = $table;
			while (@$_SESSION[EWR_PROJECT_NAME . "_" . $tablevar . "_" . EWR_TABLE_MASTER_TABLE] <> "") {
				$tablevar = $_SESSION[EWR_PROJECT_NAME . "_" . $tablevar . "_" . EWR_TABLE_MASTER_TABLE];
				if (in_array($tablevar, $mastertable))
					break;
				$mastertable[] = $tablevar;
			}
		}

		// Add master links first
		if (is_array($this->SessionLinks)) {
			$cnt = count($this->SessionLinks);
			for ($i = 0; $i < $cnt; $i++) {
				@list($id, $title, $url, $cls, $tbl, $cur) = $this->SessionLinks[$i];
				if (in_array($tbl, $mastertable)) {
					if ($url == $pageurl)
						break;
					if (!$this->Exists($id, $tbl, $url))
						$this->Links[] = array($id, $title, $url, $cls, $tbl, FALSE);
				}
			}
		}

		// Add this link
		if (!$this->Exists($pageid, $table, $pageurl))
			$this->Links[] = array($pageid, $pagetitle, $pageurl, $pageurlclass, $table, $current);

		// Save session links
		$this->SaveSession();
	}

	// Save links to Session
	function SaveSession() {
		$_SESSION[EWR_SESSION_BREADCRUMB] = $this->Links;
	}

	// Load links from Session
	function LoadSession() {
		if (is_array(@$_SESSION[EWR_SESSION_BREADCRUMB]))
			$this->SessionLinks = $_SESSION[EWR_SESSION_BREADCRUMB];
	}

	// Load language phrase
	function LanguagePhrase($title, $table, $current) {
		global $ReportLanguage;
		$wrktitle = ($title == $table) ? $ReportLanguage->TablePhrase($title, "TblCaption") : $ReportLanguage->Phrase($title);
		if ($current)
			$wrktitle = "<span id=\"ewPageCaption\">" . $wrktitle . "</span>";
		return $wrktitle;
	}

	// Render
	function Render() {
		if (!$this->Visible)
			return;
		$nav = "<ul class=\"breadcrumb\">";
		if (is_array($this->Links)) {
			$cnt = count($this->Links);
			for ($i = 0; $i < $cnt; $i++) {
				list($id, $title, $url, $cls, $table, $cur) = $this->Links[$i];
				if ($i < $cnt - 1) {
					$nav .= "<li>";
				} else {
					$nav .= "<li class=\"active\">";
					$url = ""; // No need to show url for current page
				}
				$text = $this->LanguagePhrase($title, $table, $cur);
				$title = ewr_HtmlTitle($text);
				if ($url <> "") {
					$nav .= "<a href=\"" . ewr_GetUrl($url) . "\"";
					if ($title <> "" && $title <> $text)
						$nav .= " title=\"" . ewr_HtmlEncode($title) . "\"";
					if ($cls <> "")
						$nav .= " class=\"" . $cls . "\"";
					$nav .= ">" . $text . "</a>";
				} else {
					$nav .= $text;
				}
				$nav .= "</li>";
			}
		}
		$nav .= "</ul>";
		echo $nav;
	}
}

/**
 * Table classes
 */
// Common class for table and report
class crTableBase {

	var $TableVar;
	var $TableName;
	var $TableType;
	var $TableCaption = "";

	var $ShowCurrentFilter = EWR_SHOW_CURRENT_FILTER;
	var $ShowDrillDownFilter = EWR_SHOW_DRILLDOWN_FILTER;

	var $CurrentOrder; // Current order
	var $CurrentOrderType; // Current order type

	var $UseDrillDownPanel = EWR_USE_DRILLDOWN_PANEL; // Use drill down panel

	// Set table caption
	function setTableCaption($v) {
		$this->TableCaption = $v;
	}

	// Table caption
	function TableCaption() {
		global $ReportLanguage;
		if ($this->TableCaption <> "")
			return $this->TableCaption;
		else
			return $ReportLanguage->TablePhrase($this->TableVar, "TblCaption");
	}

	// Session Group Per Page
	function getGroupPerPage() {
		return @$_SESSION[EWR_PROJECT_VAR . "_" . $this->TableVar . "_grpperpage"];
	}
	function setGroupPerPage($v) {
		@$_SESSION[EWR_PROJECT_VAR . "_" . $this->TableVar . "_grpperpage"] = $v;
	}
	// Session Start Group
	function getStartGroup() {
		return @$_SESSION[EWR_PROJECT_VAR . "_" . $this->TableVar . "_start"];
	}
	function setStartGroup($v) {
		@$_SESSION[EWR_PROJECT_VAR . "_" . $this->TableVar . "_start"] = $v;
	}
	// Session Order By
	function getOrderBy() {
		return @$_SESSION[EWR_PROJECT_VAR . "_" . $this->TableVar . "_orderby"];
	}
	function setOrderBy($v) {
		@$_SESSION[EWR_PROJECT_VAR . "_" . $this->TableVar . "_orderby"] = $v;
	}

	var $fields = array();
	var $Export; // Export
	var $CustomExport; // Custom export
	var $FirstRowData = array(); // First row data
	var $ExportAll;
	var $ExportPageBreakCount = 1; // Export page break count
	var $ExportChartPageBreak = TRUE; // Page break for chart when export
	var $PageBreakContent = EWR_EXPORT_PAGE_BREAK_CONTENT;

	var $UseTokenInUrl = EWR_USE_TOKEN_IN_URL;

	var $RowType; // Row type
	var $RowTotalType; // Row total type
	var $RowTotalSubType; // Row total subtype
	var $RowGroupLevel; // Row group level
	var $RowAttrs = array(); // Row attributes

	// Reset attributes for table object
	function ResetAttrs() {
		$this->RowAttrs = array();
		foreach ($this->fields as $fld) {
			$fld->ResetAttrs();
		}
	}

	// Row attributes
	function RowAttributes() {
		$sAtt = "";
		foreach ($this->RowAttrs as $k => $v) {
			$sAtt .= " " . $k . "=\"" . ewr_HtmlEncode($v) . "\"";
		}
		return $sAtt;
	}

	// Field object by fldvar
	function &fields($fldvar) {
		return $this->fields[$fldvar];
	}

	// URL encode
	function UrlEncode($str) {
		return urlencode($str);
	}

	// Print
	function Raw($str) {
		return $str;
	}

}
// Class for crosstab
class crTableCrosstab extends crTableBase {

	// Summary cells
	var $SummaryCellAttrs;
	var $SummaryViewAttrs;
	var $SummaryLinkAttrs;
	var $SummaryCurrentValue;
	var $SummaryViewValue;
	var $CurrentIndex = -1;

	// Summary cell attributes
	function SummaryCellAttributes($i) {
		$sAtt = "";
		if (is_array($this->SummaryCellAttrs)) {
			if ($i >= 0 && $i < count($this->SummaryCellAttrs)) {
				$Attrs = $this->SummaryCellAttrs[$i];
				if (is_array($Attrs)) {
					foreach ($Attrs as $k => $v) {
						if (trim($v) <> "")
							$sAtt .= " " . $k . "=\"" . trim($v) . "\"";
					}
				}
			}
		}
		return $sAtt;
	}

	// Summary view attributes
	function SummaryViewAttributes($i) {
		$sAtt = "";
		if (is_array($this->SummaryViewAttrs)) {
			if ($i >= 0 && $i < count($this->SummaryViewAttrs)) {
				$Attrs = $this->SummaryViewAttrs[$i];
				if (is_array($Attrs)) {
					foreach ($Attrs as $k => $v) {
						if (trim($v) <> "")
							$sAtt .= " " . $k . "=\"" . trim($v) . "\"";
					}
				}
			}
		}
		return $sAtt;
	}

	// Summary link attributes
	function SummaryLinkAttributes($i) {
		$sAtt = "";
		if (is_array($this->SummaryLinkAttrs)) {
			if ($i >= 0 && $i < count($this->SummaryLinkAttrs)) {
				$Attrs = $this->SummaryLinkAttrs[$i];
				if (is_array($Attrs)) {
					foreach ($Attrs as $k => $v) {
						if (trim($v) <> "") {
							$sAtt .= " " . $k . "=\"" . trim($v) . "\"";
						}
					}
					if (@$Attrs["onclick"] <> "" && @$Attrs["href"] == "")
						$sAtt .= " href=\"javascript:void(0);\"";
				}
			}
		}
		return $sAtt;
	}

}


/**
 * Field class
 */
class crField {

	var $TblName; // Table name
	var $TblVar; // Table variable name
	var $FldName; // Field name
	var $FldVar; // Field variable name
	var $FldExpression; // Field expression (used in SQL)
	var $FldDefaultErrMsg; // Default error message
	var $FldType; // Field type
	var $FldDataType; // PHP Report Maker Field type
	var $FldDateTimeFormat; // Date time format
	var $Count; // Count
	var $SumValue; // Sum
	var $AvgValue; // Average
	var $MinValue; // Minimum
	var $MaxValue; // Maximum
	var $CntValue; // Count
	var $SumViewValue; // Sum
	var $AvgViewValue; // Average
	var $MinViewValue; // Minimum
	var $MaxViewValue; // Maximum
	var $CntViewValue; // Count
	var $OldValue; // Old Value
	var $CurrentValue; // Current value
	var $ViewValue; // View value
	var $HrefValue; // Href value
	var $DrillDownUrl = ""; // Drill down URL
	var $CurrentFilter = ""; // Current filter in use
	var $FormValue; // Form value
	var $QueryStringValue; // QueryString value
	var $DbValue; // Database value
	var $ImageWidth = 0; // Image width
	var $ImageHeight = 0; // Image height
	var $ImageResize = FALSE; // Image resize
	var $ResizeQuality = EWR_THUMBNAIL_DEFAULT_QUALITY; // Resize quality
	var $IsBlobImage = FALSE; // Is blob image
	var $Sortable = TRUE; // Sortable
	var $GroupingFieldId = 0; // Grouping field id
	var $UploadPath = EWR_UPLOAD_DEST_PATH; // Upload path
	var $TruncateMemoRemoveHtml = FALSE; // Remove HTML from memo field
	var $DefaultDecimalPrecision = EWR_DEFAULT_DECIMAL_PRECISION;
	var $UseColorbox = EWR_USE_COLORBOX; // Use Colorbox

	var $CellAttrs = array(); // Cell attributes
	var $ViewAttrs = array(); // View attributes
	var $LinkAttrs = array(); // Href attributes
	var $EditAttrs = array(); // Edit attributes
	var $PlaceHolder = "";

	var $FldGroupByType; // Group By Type
	var $FldGroupInt; // Group Interval
	var $FldGroupSql; // Group SQL
	var $GroupDbValues; // Group DB Values
	var $GroupViewValue; // Group View Value
	var $GroupSummaryOldValue; // Group Summary Old Value
	var $GroupSummaryValue; // Group Summary Value
	var $GroupSummaryViewValue; // Group Summary View Value
	var $SqlSelect; // Field SELECT
	var $SqlGroupBy; // Field GROUP BY
	var $SqlOrderBy; // Field ORDER BY
	var $ValueList; // Value List
	var $SelectionList; // Selection List
	var $DefaultSelectionList; // Default Selection List
	var $AdvancedFilters; // Advanced Filters
	var $RangeFrom; // Range From
	var $RangeTo; // Range To
	var $DropDownList; // Dropdown List
	var $DropDownValue; // Dropdown Value
	var $DefaultDropDownValue; // Default Dropdown Value
	var $DateFilter; // Date Filter
	var $SearchValue; // Search Value 1
	var $SearchValue2; // Search Value 2
	var $SearchOperator; // Search Operator 1
	var $SearchOperator2; // Search Operator 2
	var $SearchCondition; // Search Condition
	var $DefaultSearchValue; // Default Search Value 1
	var $DefaultSearchValue2; // Default Search Value 2
	var $DefaultSearchOperator; // Default Search Operator 1
	var $DefaultSearchOperator2; // Default Search Operator 2
	var $DefaultSearchCondition; // Default Search Condition
	var $FldDelimiter = ""; // Field delimiter (e.g. comma) for delimiter separated value
	var $Visible = TRUE; // Visible

	// Constructor
	function __construct($tblvar, $tblname, $fldvar, $fldname, $fldexpression, $fldtype, $flddatatype, $flddtfmt) {
		$this->TblVar = $tblvar;
		$this->TblName = $tblname;
		$this->FldVar = $fldvar;
		$this->FldName = $fldname;
		$this->FldExpression = $fldexpression;
		$this->FldType = $fldtype;
		$this->FldDataType = $flddatatype;
		$this->FldDateTimeFormat = $flddtfmt;
	}

	var $Caption = "";

	// Set field caption
	function setFldCaption($v) {
		$this->Caption = $v;
	}

	// Field caption
	function FldCaption() {
		global $ReportLanguage;
		if ($this->Caption <> "")
			return $this->Caption;
		else
			return $ReportLanguage->FieldPhrase($this->TblVar, substr($this->FldVar, 2), "FldCaption");
	}

	// Field title
	function FldTitle() {
		global $ReportLanguage;
		return $ReportLanguage->FieldPhrase($this->TblVar, substr($this->FldVar, 2), "FldTitle");
	}

	// Field image alt
	function FldAlt() {
		global $ReportLanguage;
		return $ReportLanguage->FieldPhrase($this->TblVar, substr($this->FldVar, 2), "FldAlt");
	}
	
	// Field error message
	function FldErrMsg() {
		global $ReportLanguage;
		$err = $ReportLanguage->FieldPhrase($this->TblVar, substr($this->FldVar, 2), "FldErrMsg");
		if ($err == "") $err = $this->FldDefaultErrMsg . " - " . $this->FldCaption();
		return $err;
	}

	// Reset attributes for field object
	function ResetAttrs() {
		$this->CellAttrs = array();
		$this->ViewAttrs = array();
	}

	// View Attributes
	function ViewAttributes() {
		$sAtt = "";
		$sStyle = "";
		if (intval($this->ImageWidth) > 0 && (!$this->ImageResize || ($this->ImageResize && intval($this->ImageHeight) <= 0)))
			$sStyle .= "width: " . intval($this->ImageWidth) . "px; ";
		if (intval($this->ImageHeight) > 0 && (!$this->ImageResize || ($this->ImageResize && intval($this->ImageWidth) <= 0)))
			$sStyle .= "height: " . intval($this->ImageHeight) . "px; ";
		$sStyle = trim($sStyle);
		if (@$this->ViewAttrs["style"] <> "")
			$sStyle .= " " . $this->ViewAttrs["style"];
		if (trim($sStyle) <> "")
			$sAtt .= " style=\"" . trim($sStyle) . "\"";
		foreach ($this->ViewAttrs as $k => $v) {
			if ($k <> "style" && trim($v) <> "")
				$sAtt .= " " . $k . "=\"" . trim($v) . "\"";
		}
		return $sAtt;
	}

	// Link Attributes
	function LinkAttributes() {
		$sAtt = "";
		$sHref = trim($this->HrefValue);
		foreach ($this->LinkAttrs as $k => $v) {
			if (trim($v) <> "") {
				if ($k == "href") {
					if ($sHref == "")
						$sHref = $v;
				} else {
					$sAtt .= " " . $k . "=\"" . trim($v) . "\"";
				}
			}
		}
		if ($sHref <> "")
			$sAtt .= " href=\"" . trim($sHref) . "\"";
		elseif (trim(@$this->LinkAttrs["onclick"]) <> "")
			$sAtt .= " href=\"javascript:void(0);\"";
		return $sAtt;
	}

	// Cell attributes
	function CellAttributes() {
		$sAtt = "";
		foreach ($this->CellAttrs as $k => $v) {
			if (trim($v) <> "")
				$sAtt .= " " . $k . "=\"" . trim($v) . "\"";
		}
		return $sAtt;
	}

	// Edit Attributes
	function EditAttributes() {
		$sAtt = "";
		foreach ($this->EditAttrs as $k => $v) {
			if (trim($v) <> "")
				$sAtt .= " " . $k . "=\"" . trim($v) . "\"";
		}
		return $sAtt;
	}

	// Sort
	function getSort() {
		return @$_SESSION[EWR_PROJECT_VAR . "_" . $this->TblVar . "_" . EWR_TABLE_SORT . "_" . $this->FldVar];
	}
	
	function setSort($v) {
		if (@$_SESSION[EWR_PROJECT_VAR . "_" . $this->TblVar . "_" . EWR_TABLE_SORT . "_" . $this->FldVar] <> $v) {
			$_SESSION[EWR_PROJECT_VAR . "_" . $this->TblVar . "_" . EWR_TABLE_SORT . "_" . $this->FldVar] = $v;
		}
	}
	
	function ReverseSort() {
		return ($this->getSort() == "ASC") ? "DESC" : "ASC";
	}

	// List view value
	function ListViewValue() {
		$value = trim(strval($this->ViewValue));
		if ($value <> "") {
			$value2 = trim(preg_replace('/<[^img][^>]*>/i', '', strval($value)));
			return ($value2 <> "") ? $this->ViewValue : "&nbsp;";
		} else {
			return "&nbsp;";
		}
	}

	// Form value
	function setFormValue($v) {
		$this->FormValue = ewr_StripSlashes($v);
		if (is_array($this->FormValue))
			$this->FormValue = implode(",", $this->FormValue);
		$this->CurrentValue = $this->FormValue;
	}

	// QueryString value
	function setQueryStringValue($v) {
		$this->QueryStringValue = ewr_StripSlashes($v);
		$this->CurrentValue = $this->QueryStringValue;
	}

	// Database value
	function setDbValue($v) {
		$this->OldValue = $this->DbValue;
		if (EWR_IS_MSSQL && ($this->FldType == 131 || $this->FldType == 139)) // MS SQL adNumeric/adVarNumeric field
			$this->DbValue = floatval($v);
		else
			$this->DbValue = $v;
		$this->CurrentValue = $this->DbValue;
	}

	// Group value
	function GroupValue() {
		return $this->getGroupValue($this->CurrentValue);
	}

	// Group old value
	function GroupOldValue() {
		return $this->getGroupValue($this->OldValue);
	}

	// Get group value
	function getGroupValue($v) {
		if ($this->GroupingFieldId == 1) {
			return $v;
		} elseif (is_array($this->GroupDbValues)) {
			return @$this->GroupDbValues[$v];
		} elseif ($this->FldGroupByType <> "" && $this->FldGroupByType <> "n") {
			return ewr_GroupValue($this, $v);
		} else {
			return $v;
		}
	}

	// Get temp image
	function GetTempImage() {
		if ($this->FldDataType == EWR_DATATYPE_BLOB) {
			$wrkdata = $this->DbValue;
			if (!empty($wrkdata)) {
				if ($this->ImageResize) {
					$wrkwidth = $this->ImageWidth;
					$wrkheight = $this->ImageHeight;
					ewr_ResizeBinary($wrkdata, $wrkwidth, $wrkheight, $this->ResizeQuality);
				}
				return ewr_TmpImage($wrkdata);
			}
		} else {
			$wrkfile = $this->DbValue;
			if (empty($wrkfile)) $wrkfile = $this->CurrentValue;
			if (!empty($wrkfile)) {
				$tmpfiles = explode(EWR_MULTIPLE_UPLOAD_SEPARATOR, $wrkfile);
				$tmpimage = "";
				foreach ($tmpfiles as $tmpfile) {
					if ($tmpfile <> "") {
						$imagefn = ewr_UploadPathEx(TRUE, $this->UploadPath) . $tmpfile;
						if ($this->ImageResize) {
							$wrkwidth = $this->ImageWidth;
							$wrkheight = $this->ImageHeight;
							$wrkdata = ewr_ResizeFileToBinary($imagefn, $wrkwidth, $wrkheight, $this->ResizeQuality);
							if ($tmpimage <> "")
								$tmpimage .= ",";
							$tmpimage .= ewr_TmpImage($wrkdata);
						} else {
							if ($tmpimage <> "")
								$tmpimage .= ",";
							$tmpimage .= ewr_ConvertFullUrl($this->UploadPath . $tmpfile);
						}
					}
				}
				return $tmpimage;
			}
		}
	}

}

// Javascript for drill down
function ewr_DrillDownJs($url, $id, $hdr, $usepanel = TRUE, $objid = "", $event = TRUE) {
	if (trim($url) == "") {
		return "";
	} else {
		if ($usepanel) {
			$obj = ($objid == "") ? "this" : "'" . ewr_JsEncode($objid) . "'";
			if ($event) {
				$wrkurl = preg_replace('/&(?!amp;)/', '&amp;', $url); // Replace & to &amp;
				return "ewr_ShowDrillDown(event, " . $obj . ", '" . ewr_JsEncode($wrkurl) . "', '" . ewr_JsEncode($id) . "', '" . ewr_JsEncode($hdr) . "'); return false;";
			} else {
				return "ewr_ShowDrillDown(null, " . $obj . ", '" . ewr_JsEncode($url) . "', '" . ewr_JsEncode($id) . "', '" . ewr_JsEncode($hdr) . "');";
			}
		} else {
			$wrkurl = str_replace("?d=1&", "?d=2&", $url); // Change d parameter to 2
			return "ewr_Redirect('" . ewr_JsEncode($wrkurl) . "');";
		}
	}
}

/**
 * Chart class
 */
class crChart {

	var $TblName; // Table name
	var $TblVar; // Table variable name
	var $ChartName; // Chart name
	var $ChartVar; // Chart variable name
	var $ChartXFldName; // Chart X Field name
	var $ChartYFldName; // Chart Y Field name
	var $ChartSFldName; // Chart Series Field name
	var $ChartType; // Chart Type
	var $ChartSortType; // Chart Sort Type
	var $ChartSummaryType; // Chart Summary Type
	var $ChartWidth; // Chart Width
	var $ChartHeight; // Chart Height
	var $ChartGridHeight = 200; // Chart grid height
	var $ChartGridConfig;
	var $ChartAlign; // Chart Align
	var $ChartDrillDownUrl = ""; // Chart drill down URL
	var $UseDrillDownPanel = EWR_USE_DRILLDOWN_PANEL; // Use drill down panel
	var $ChartDefaultDecimalPrecision = EWR_DEFAULT_DECIMAL_PRECISION;

	var $SqlSelect;
	var $SqlGroupBy;
	var $SqlOrderBy;
	var $XAxisDateFormat;
	var $NameDateFormat;
	var $SeriesDateType;
	var $SqlSelectSeries;
	var $SqlGroupBySeries;
	var $SqlOrderBySeries;

	var $UseGridComponent = FALSE;
	var $ChartSeriesSql;
	var $ChartSql;
	var $PageBreak = FALSE;
	var $PageBreakType = "";
	var $PageBreakContent = "";
	var $ShowChart = TRUE;
	var $ShowTempImage = FALSE;
	var $DrillDownInPanel = FALSE;

	var $ID;
	var $Parms = array();
	var $Trends;
	var $Data;
	var $ViewData;
	var $Series;
	var $XmlDoc;
	var $XmlRoot;

	// Constructor
	function __construct($tblvar, $tblname, $chartvar, $chartname, $xfld, $yfld, $sfld, $type, $smrytype, $width, $height, $align="") {
		$this->TblVar = $tblvar;
		$this->TblName = $tblname;
		$this->ChartVar = $chartvar;
		$this->ChartName = $chartname;
		$this->ChartXFldName = $xfld;
		$this->ChartYFldName = $yfld;
		$this->ChartSFldName = $sfld;
		$this->ChartType = $type;
		$this->ChartSummaryType = $smrytype;
		$this->ChartWidth = $width;
		$this->ChartHeight = $height;
		$this->ChartAlign = $align;
		$this->ID = NULL;
		$this->Parms = NULL;
		$this->Trends = NULL;
		$this->Data = NULL;
		$this->Series = NULL;
		$this->XmlDoc = new DOMDocument("1.0", "utf-8");
	}
	
	var $Caption = "";

	// Set field caption
	function setChartCaption($v) {
		$this->Caption = $v;
	}

	// Chart caption
	function ChartCaption() {
		global $ReportLanguage;
		if ($this->Caption <> "")
			return $this->Caption;
		else
			return $ReportLanguage->ChartPhrase($this->TblVar, $this->ChartVar, "ChartCaption");
	}

	// Function XAxisName
	function ChartXAxisName() {
		global $ReportLanguage;
		return $ReportLanguage->ChartPhrase($this->TblVar, $this->ChartVar, "ChartXAxisName");
	}

	// Function YAxisName
	function ChartYAxisName() {
		global $ReportLanguage;
		return $ReportLanguage->ChartPhrase($this->TblVar, $this->ChartVar, "ChartYAxisName");
	}

	// Function PYAxisName
	function ChartPYAxisName() {
		global $ReportLanguage;
		return $ReportLanguage->ChartPhrase($this->TblVar, $this->ChartVar, "ChartPYAxisName");
	}

	// Function SYAxisName
	function ChartSYAxisName() {
		global $ReportLanguage;
		return $ReportLanguage->ChartPhrase($this->TblVar, $this->ChartVar, "ChartSYAxisName");
	}

	// Sort
	function getSort() {
		return @$_SESSION[EWR_PROJECT_VAR . "_" . $this->TblVar . "_" . EWR_TABLE_SORTCHART . "_" . $this->ChartVar];
	}
	
	function setSort($v) {
		if (@$_SESSION[EWR_PROJECT_VAR . "_" . $this->TblVar . "_" . EWR_TABLE_SORTCHART . "_" . $this->ChartVar] <> $v) {
			$_SESSION[EWR_PROJECT_VAR . "_" . $this->TblVar . "_" . EWR_TABLE_SORTCHART . "_" . $this->ChartVar] = $v;
		}
	}

	// Set chart parameters
	function SetChartParm($Name, $Value, $Output) {
		$this->Parms[$Name] = array($Name, $Value, $Output);
	}

	// Set chart parameters
	function SetChartParms($parms) {
		if (is_array($parms)) {
			foreach ($parms as $parm) {
				if (!isset($parm[2]))
					$parm[2] = TRUE;
				$this->Parms[$parm[0]] = $parm;
			}
		}
	}

	// Set up default chart parm
	function SetupDefaultChartParm($key, $value) {
		if (is_array($this->Parms)) {
			$parm = $this->LoadParm($key);
			if (is_null($parm)) {
				$this->Parms[$key] = array($key, $value, TRUE);
			} elseif ($parm == "") {
				$this->SaveParm($key, $value);
			}
		}
	}

	// Load chart parm
	function LoadParm($key) {
		if (is_array($this->Parms) && array_key_exists($key, $this->Parms))
			return $this->Parms[$key][1];
		return NULL;
	}

	// Save chart parm
	function SaveParm($key, $value) {
		if (is_array($this->Parms)) {
			if (array_key_exists($key, $this->Parms))
				$this->Parms[$key][1] = $value;
			else
				$this->Parms[$key] = array($key, $value, TRUE);
		}
	}
	
	// Process chart parms
	function ProcessChartParms(&$Parms) {
		if ($this->IsFCFChart())
			return;
	
		$arParms[] = array("shownames", "showLabels");
		$arParms[] = array("showhovercap", "showToolTip");
		$arParms[] = array("rotateNames", "rotateLabels");
		$arParms[] = array("showColumnShadow", "showShadow");
		$arParms[] = array("showBarShadow", "showShadow");
		$arParms[] = array("hoverCapBgColor", "toolTipBgColor");
		$arParms[] = array("hoverCapBorderColor", "toolTipBorderColor");
		$arParms[] = array("hoverCapSepChar", "toolTipSepChar");
		$arParms[] = array("showAnchors", "drawAnchors");
		
		$cht_type = $this->LoadParm("type");
		if ($cht_type == 20) { // Candlestick // v8
			$arParms[] = array("yAxisMaxValue", "pYAxisMaxValue");
			$arParms[] = array("yAxisMinValue", "pYAxisMinValue");
		}

		// Rename chart parm
		foreach ($arParms as $p) {
			list($fromParm, $toParm) = $p;
			if (array_key_exists($fromParm, $Parms) && !array_key_exists($toParm, $Parms)) {
				$Parms[$toParm] = array($toParm, $Parms[$fromParm][1], TRUE);
				unset($Parms[$fromParm]);
			}
		}

	}

	function LoadChartParms() {

		// Initialize default values
		$this->SetupDefaultChartParm("caption", "Chart");

		// Show names/values/hover
		$this->SetupDefaultChartParm("shownames", "1"); // Default show names
		$this->SetupDefaultChartParm("showvalues", "1"); // Default show values
		
		// Process chart parms
		$this->ProcessChartParms($this->Parms);

		// Get showvalues/showhovercap
		$cht_showValues = (bool)$this->LoadParm("showvalues");
		$cht_showHoverCap = (bool)$this->LoadParm("showhovercap") || (bool)$this->LoadParm("showToolTip"); // v8
		
		// Tooltip // v8
		if ($cht_showHoverCap && !$this->LoadParm("showToolTip"))
			$this->SaveParm("showToolTip", "1");

		// Format percent for Pie charts
		$cht_showPercentageValues = $this->LoadParm("showPercentageValues");
		$cht_showPercentageInLabel = $this->LoadParm("showPercentageInLabel");
		$cht_type = $this->LoadParm("type");
		if ($cht_type == 2 || $cht_type == 6 || $cht_type == 8 || $cht_type == 101) {
			if (($cht_showHoverCap == "1" && $cht_showPercentageValues == "1") ||
			($cht_showValues == "1" && $cht_showPercentageInLabel == "1")) {
				$this->SetupDefaultChartParm("formatNumber", "1");
				$this->SaveParm("formatNumber", "1");
			}
		} elseif ($cht_type == 20) { // Candlestick
			$this->SetupDefaultChartParm("bearBorderColor", "E33C3C");
			$this->SetupDefaultChartParm("bearFillColor", "E33C3C");
			$this->SetupDefaultChartParm("showVolumeChart", "0"); // v8
			if ($this->LoadParm("showAsBars"))
				$this->SaveParm("plotPriceAs", "BAR");
		}

		// Hide legend for single series (Bar 3D / Column 2D / Line 2D / Area 2D)
		$scrollchart = (intval($this->LoadParm("numVisiblePlot")) > 0 && ($cht_type == 1 || $cht_type == 4 || $cht_type == 7)) ? 1 : 0;
		$cht_single_series = ($cht_type == 104 || $scrollchart == 1) ? 1 : 0;
		if ($cht_single_series == 1) {
			$this->SetupDefaultChartParm("showLegend", "0");
			$this->SaveParm("showLegend", "0");
		}

	}

	// Load view data
	function LoadViewData() {
		$sdt = $this->SeriesDateType;
		$xdt = $this->XAxisDateFormat;
		$ndt = ($this->ChartType == 20) ? $this->NameDateFormat : "";
		if ($sdt <> "") $xdt = $sdt;
		$this->ViewData = array();
		if ($sdt == "" && $xdt == "" && $ndt == "") { // No formatting, just copy
			$this->ViewData = $this->Data;
		} elseif (is_array($this->Data)) { // Format data
			$cntData = count($this->Data);
			for ($i = 0; $i < $cntData; $i++) {
				$temp = array();
				$chartrow = $this->Data[$i];
				$cntRow = count($chartrow);
				$temp[0] = ewr_ChartXValue($chartrow[0], $xdt); // X value
				$temp[1] = ewr_ChartSeriesValue($chartrow[1], $sdt); // Series value
				for ($j = 2; $j < $cntRow; $j++) {
					if ($ndt <> "" && $j == $cntRow-1)
						$temp[$j] = ewr_ChartXValue($chartrow[$j], $ndt); // Name value
					else
						$temp[$j] = $chartrow[$j]; // Y values
				}
				$this->ViewData[] = $temp;
			}
		}
	}

	// Chart Xml
	function ChartXml() {

		$this->LoadViewData();

	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Global","Chart_Rendering")) { ##-->
		$this->Chart_Rendering();
	<!--## } ##-->

		$cht_type = $this->LoadParm("type");

		// Format line color for Multi-Series Column Dual Y chart
		$cht_lineColor = ($cht_type == 18 || $cht_type == 19) ? $this->LoadParm("lineColor") : "";

		$chartseries = &$this->Series;
		$chartdata = &$this->ViewData;
		$cht_series = ((intval($cht_type) >= 9 && intval($cht_type) <= 19) || (intval($cht_type) >= 102 && intval($cht_type) <= 103)) ? 1 : 0; // $cht_series = 1 (Multi series charts)
		$cht_series_type = $this->LoadParm("seriestype");
		$cht_alpha = $this->LoadParm("alpha");

		// Hide legend for single series (Bar 3D / Column 2D / Line 2D / Area 2D)
		$scrollchart = (intval($this->LoadParm("numVisiblePlot")) > 0 && ($cht_type == 1 || $cht_type == 4 || $cht_type == 7)) ? 1 : 0;
		$cht_single_series = ($cht_type == 104 || $scrollchart == 1) ? 1 : 0;

		if (is_array($chartdata)) {
			$this->WriteChartHeader(); // Write chart header

			// Candlestick
			if ($cht_type == 20) {

				// Write candlestick cat
				if (count($chartdata[0]) >= 7) {
					$cats = $this->XmlDoc->createElement("categories");
					$this->XmlRoot->appendChild($cats);
					$cntcat = count($chartdata);
					for ($i = 0; $i < $cntcat; $i++) {
						$xindex = $i+1;
						$name = $chartdata[$i][6];
						if ($name <> "")
							$this->WriteChartCandlestickCatContent($cats, $xindex, $name);
					}
				}

				// Write candlestick data
				$data = $this->XmlDoc->createElement(EWR_FUSIONCHARTS_FREE ? "data" : "dataset");
				$this->XmlRoot->appendChild($data);
				$cntdata = count($chartdata);
				for ($i = 0; $i < $cntdata; $i++) {
					$open = is_null($chartdata[$i][2]) ? 0 : (float)$chartdata[$i][2];
					$high = is_null($chartdata[$i][3]) ? 0 : (float)$chartdata[$i][3];
					$low = is_null($chartdata[$i][4]) ? 0 : (float)$chartdata[$i][4];
					$close = is_null($chartdata[$i][5]) ? 0 : (float)$chartdata[$i][5];
					$xindex = $i+1;
					$lnk = $this->GetChartLink($this->ChartDrillDownUrl, $this->Data[$i]);
					$this->WriteChartCandlestickContent($data, $open, $high, $low, $close, $xindex, $lnk);
				}

			// Multi series
			} else if ($cht_series == 1) {

				// Multi-Y values
				if ($cht_series_type == "1") {

					// Write cat
					$cats = $this->XmlDoc->createElement("categories");
					$this->XmlRoot->appendChild($cats);
					$cntcat = count($chartdata);
					for ($i = 0; $i < $cntcat; $i++) {
						$name = $this->ChartFormatName($chartdata[$i][0]);
						$this->WriteChartCatContent($cats, $name);
					}

					// Write series
					$cntdata = count($chartdata);
					$cntseries = count($chartseries);
					if ($cntseries > count($chartdata[0])-2) $cntseries = count($chartdata[0])-2;
					for ($i = 0; $i < $cntseries; $i++) {
						$color = $this->GetPaletteColor($i);
						$bShowSeries = EWR_CHART_SHOW_BLANK_SERIES;
						$dataset = $this->XmlDoc->createElement("dataset");
						$this->WriteChartSeriesHeader($dataset, $chartseries[$i], $color, $cht_alpha, $cht_lineColor);
						$bWriteSeriesHeader = TRUE;
						for ($j = 0; $j < $cntdata; $j++) {
							$val = $chartdata[$j][$i+2];
							$val = (is_null($val)) ? 0 : (float)$val;
							if ($val <> 0) $bShowSeries = TRUE;
							$lnk = $this->GetChartLink($this->ChartDrillDownUrl, $this->Data[$j]);
							$this->WriteChartSeriesContent($dataset, $val, "", "", $lnk);
						}
						if ($bShowSeries)
							$this->XmlRoot->appendChild($dataset);
					}

				// Series field
				} else {

					// Get series names
					if (is_array($chartseries)) {
						$nSeries = count($chartseries);
					} else {
						$nSeries = 0;
					}

					// Write cat
					$cats = $this->XmlDoc->createElement("categories");
					$this->XmlRoot->appendChild($cats);
					$chartcats = array();
					$cntdata = count($chartdata);
					for ($i = 0; $i < $cntdata; $i++) {
						$name = $chartdata[$i][0];
						if (!in_array($name, $chartcats)) {
							$this->WriteChartCatContent($cats, $name);
							$chartcats[] = $name;
						}
					}

					// Write series
					for ($i = 0; $i < $nSeries; $i++) {
						$seriesname = (is_array($chartseries[$i])) ? $chartseries[$i][0] : $chartseries[$i];
						$color = $this->GetPaletteColor($i);
						$bShowSeries = EWR_CHART_SHOW_BLANK_SERIES;
						$dataset = $this->XmlDoc->createElement("dataset");
						$this->WriteChartSeriesHeader($dataset, $chartseries[$i], $color, $cht_alpha, $cht_lineColor);
						$cntcats = count($chartcats);
						$cntdata = count($chartdata);
						for ($j = 0; $j < $cntcats; $j++) {
							$val = 0;
							$lnk = "";
							for ($k = 0; $k < $cntdata; $k++) {
								if ($chartdata[$k][0] == $chartcats[$j] && $chartdata[$k][1] == $seriesname) {
									$val = $chartdata[$k][2];
									$val = (is_null($val)) ? 0 : (float)$val;
									if ($val <> 0) $bShowSeries = TRUE;
									$lnk = $this->GetChartLink($this->ChartDrillDownUrl, $this->Data[$k]);
									break;
								}
							}
							$this->WriteChartSeriesContent($dataset, $val, "", "", $lnk);
						}
						if ($bShowSeries)
							$this->XmlRoot->appendChild($dataset);
					}
				}

			// Show single series
			} elseif ($cht_single_series == 1) {

				// Write multiple cats
				$cats = $this->XmlDoc->createElement("categories");
				$this->XmlRoot->appendChild($cats);
				$cntcat = count($chartdata);
				for ($i = 0; $i < $cntcat; $i++) {
					$name = $this->ChartFormatName($chartdata[$i][0]);
					if ($chartdata[$i][1] <> "") 
						$name .= ", " . $chartdata[$i][1];
					$this->WriteChartCatContent($cats, $name);
				}

				// Write series
				$toolTipSep = $this->LoadParm("toolTipSepChar");
				if ($toolTipSep == "") $toolTipSep = ":";
				$cntdata = count($chartdata);
				$dataset = $this->XmlDoc->createElement("dataset");
				$this->WriteChartSeriesHeader($dataset, "", "", $cht_alpha, $cht_lineColor);
				for ($i = 0; $i < $cntdata; $i++) {
					$name = $this->ChartFormatName($chartdata[$i][0]);
					if ($chartdata[$i][1] <> "") 
						$name .= ", " . $chartdata[$i][1];
					$val = $chartdata[$i][2];
					$val = (is_null($val)) ? 0 : (float)$val;
					$color = $this->GetPaletteColor($i);
					$toolText = $name . $toolTipSep . $this->ChartFormatNumber($val);
					$lnk = $this->GetChartLink($this->ChartDrillDownUrl, $this->Data[$i]);
					$this->WriteChartSeriesContent($dataset, $val, $color, $cht_alpha, $lnk, $toolText);
					$this->XmlRoot->appendChild($dataset);
				}

			// Single series
			} else {

				$cntdata = count($chartdata);
				for ($i = 0; $i < $cntdata; $i++) {
					$name = $this->ChartFormatName($chartdata[$i][0]);
					$color = $this->GetPaletteColor($i);
					if ($chartdata[$i][1] <> "") 
						$name .= ", " . $chartdata[$i][1];
					$val = $chartdata[$i][2];
					$val = (is_null($val)) ? 0 : (float)$val;
					$lnk = $this->GetChartLink($this->ChartDrillDownUrl, $this->Data[$i]);
					$this->WriteChartContent($this->XmlRoot, $name, $val, $color, $cht_alpha, $lnk); // Get chart content
				}

			}

			// Get trend lines
			$this->WriteChartTrendLines();

		}
		$wrk = $this->XmlDoc->saveXML();

	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Global","Chart_Rendered")) { ##-->
		$this->Chart_Rendered($wrk);
	<!--## } ##-->

		return $this->XmlRoot ? $wrk : "";

		//ewr_Trace($wrk);
	}

	// Show Chart Xml
	function ShowChartXml() {
		// Build chart content
		$sChartContent = $this->ChartXml();
		header("Content-Type: text/xml; charset=UTF-8");
		// Write utf-8 BOM
		echo "\xEF\xBB\xBF";
		// Write utf-8 encoding
		echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";
		// Write content
		echo $sChartContent;
	}

	// Show Chart Text
	function ShowChartText() {
		// Build chart content
		$sChartContent = $this->ChartXml();
		header("Content-Type: text/plain; charset=UTF-8");
		// Write content
		echo $sChartContent;
	}

	// Get color
	function GetPaletteColor($i) {
		$colorpalette = $this->LoadParm("colorpalette");
		$ar_cht_colorpalette = explode("|", $colorpalette);
		if (is_array($ar_cht_colorpalette))
			$cntar = count($ar_cht_colorpalette);
		return $ar_cht_colorpalette[$i % $cntar];
	}

	// Convert to HTML color
	function ColorCode($c) {
		if ($this->IsFCFChart()) {
			$color = str_replace("#", "", $c); // Remove #
			if (strlen($color) == 3) // Convert RGB to RRGGBB
				return substr($color,0,1) . substr($color,0,1) . substr($color,1,1) . substr($color,1,1) . substr($color,2,1) . substr($color,2,1);
			else // Fill to 6 digits
				return str_pad($color, 6, "0", STR_PAD_LEFT);
		} else {
			return $c;
		}
	}

	// Output chart header
	function WriteChartHeader() {
		$cht_parms = $this->Parms;
		$chartElement = (EWR_FUSIONCHARTS_FREE && $this->ChartType == 20) ? "graph" : "chart";
		$chart = $this->XmlDoc->createElement($chartElement);
		$this->XmlRoot = &$chart;
		$this->XmlDoc->appendChild($chart);
		if (is_array($cht_parms)) {
			foreach ($cht_parms as $parm) {
				if ($parm[2])
					$this->WriteAtt($chart, $parm[0], $parm[1]);
			}
		}
	}

	// Get TrendLine XML
	// <trendlines>
	//    <line startvalue='0.8' displayValue='Good' color='FF0000' thickness='1' isTrendZone='0'/>
	//    <line startvalue='-0.4' displayValue='Bad' color='009999' thickness='1' isTrendZone='0'/>
	// </trendlines>
	function WriteChartTrendLines() {
		$cht_trends = $this->Trends;
		if (is_array($cht_trends)) {
			foreach ($cht_trends as $trend) {
				$trends = $this->XmlDoc->createElement('trendlines');
				$this->XmlRoot->appendChild($trends);
				// Get all trend lines
				$this->WriteChartTrendLine($trends, $trend);
			}
		}
	}

	// Output trend line
	function WriteChartTrendLine(&$node, $ar) {
		$line = $this->XmlDoc->createElement('line');
		@list($startval, $endval, $color, $dispval, $thickness, $trendzone, $showontop, $alpha, $tooltext, $valueonright, $dashed, $dashlen, $dashgap, $parentyaxis) = $ar;
		$this->WriteAtt($line, "startValue", $startval); // Starting y value
		if ($endval <> 0)
			$this->WriteAtt($line, "endValue", $endval); // Ending y value
		$this->WriteAtt($line, "color", $this->CheckColorCode($color)); // Color
		if ($dispval <> "")
			$this->WriteAtt($line, "displayValue", $dispval); // Display value
		if ($thickness > 0)
			$this->WriteAtt($line, "thickness", $thickness); // Thickness
		$this->WriteAtt($line, "isTrendZone", $trendzone); // Display trend as zone or line
		$this->WriteAtt($line, "showOnTop", $showontop); // Show on top
		if ($alpha > 0)
			$this->WriteAtt($line, "alpha", $alpha); // Alpha
		if ($tooltext <> "")
			$this->WriteAtt($line, "toolText", $tooltext); // Tool text
		if ($valueonright <> "0")
			$this->WriteAtt($line, "valueOnRight", $valueonright); // Value on right
		if ($dashed <> "0") {
			$this->WriteAtt($line, "dashed", $dashed); // Dashed trend line
			$this->WriteAtt($line, "dashLen", $dashlen); // Dashed trend length
			$this->WriteAtt($line, "dashGap", $dashgap); // Dashed line gap
		}
		if ($parentyaxis <> "")
			$this->WriteAtt($line, "parentYAxis", $parentyaxis); // Parent Y Axis
		$node->appendChild($line);
	}

	// Series header/footer XML (multi series)
	function WriteChartSeriesHeader(&$node, $series, $color, $alpha, $linecolor) {
		global $ReportLanguage;
		$seriesname = is_array($series) ? $series[0] : $series;
		if (is_null($seriesname)) {
			$seriesname = $ReportLanguage->Phrase("NullLabel");
		} elseif ($seriesname == "") {
			$seriesname = $ReportLanguage->Phrase("EmptyLabel");
		}
		$this->WriteAtt($node, "seriesname", $seriesname);
		if (is_array($series)) {
			if ($series[1] == "S" && $linecolor <> "")
				$this->WriteAtt($node, "color", $this->ColorCode($linecolor));
			else
				$this->WriteAtt($node, "color", $this->ColorCode($color));
		} else {
				$this->WriteAtt($node, "color", $this->ColorCode($color));
		}
		$this->WriteAtt($node, "alpha", $alpha);
		if (is_array($series))
			$this->WriteAtt($node, "parentYAxis", $series[1]);
	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Global","Chart_DataRendered")) { ##-->
		$this->Chart_DataRendered($node);
	<!--## } ##-->
	}

	// Series content XML (multi series)
	function WriteChartSeriesContent(&$node, $val, $color = "", $alpha = "", $lnk = "", $toolText = "") {
		$set = $this->XmlDoc->createElement('set');
		if ($this->IsStackedChart() && $val == 0 && !EWR_CHART_SHOW_ZERO_IN_STACK_CHART)
			$this->WriteAtt($set, "value", "");
		else
			$this->WriteAtt($set, "value", $this->ChartFormatNumber($val));
		if ($color <> "")
			$this->WriteAtt($set, "color", $this->ColorCode($color));
		if ($alpha <> "")
			$this->WriteAtt($set, "alpha", $alpha);
		if ($lnk <> "")
			$this->WriteAtt($set, "link", $lnk);
		if ($toolText <> "")
			$this->WriteAtt($set, "toolText", $toolText);
	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Global","Chart_DataRendered")) { ##-->
		$this->Chart_DataRendered($set);
	<!--## } ##-->
		$node->appendChild($set);
	}

	// Category content XML (Candlestick category)
	function WriteChartCandlestickCatContent(&$node, $xindex, $name) {
		$cat = $this->XmlDoc->createElement("category");
		$this->WriteAtt($cat, EWR_FUSIONCHARTS_FREE ? "name" : "label", $name);
		$this->WriteAtt($cat, EWR_FUSIONCHARTS_FREE ? "xindex" : "x", $xindex);
		$this->WriteAtt($cat, "showline", "1");
	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Global","Chart_DataRendered")) { ##-->
		$this->Chart_DataRendered($cat);
	<!--## } ##-->
		$node->appendChild($cat);
	}

	// Chart content XML (Candlestick)
	function WriteChartCandlestickContent(&$node, $open, $high, $low, $close, $xindex, $lnk = "") {
		$set = $this->XmlDoc->createElement("set");
		$this->WriteAtt($set, "open", $this->ChartFormatNumber($open));
		$this->WriteAtt($set, "high", $this->ChartFormatNumber($high));
		$this->WriteAtt($set, "low", $this->ChartFormatNumber($low));
		$this->WriteAtt($set, "close", $this->ChartFormatNumber($close));
		if ($xindex <> "")
			$this->WriteAtt($set, EWR_FUSIONCHARTS_FREE ? "xindex" : "x", $xindex);
		if ($lnk <> "")
			$this->WriteAtt($set, "link", $lnk);
	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Global","Chart_DataRendered")) { ##-->
		$this->Chart_DataRendered($set);
	<!--## } ##-->
		$node->appendChild($set);
	}

	// Format name for chart
	function ChartFormatName($name) {
		global $ReportLanguage;
		if (is_null($name)) {
			return $ReportLanguage->Phrase("NullLabel");
		} elseif ($name == "") {
			return $ReportLanguage->Phrase("EmptyLabel");
		} else {
			return $name;
		}
	}

	// Write attribute
	function WriteAtt(&$node, $name, $val) {
		$val = $this->CheckColorCode(strval($val));
		$val = $this->ChartEncode($val);
		if ($node->hasAttribute($name)) {
			$node->getAttributeNode($name)->value = ewr_XmlEncode(ewr_ConvertToUtf8($val));
		} else {
			$att = $this->XmlDoc->createAttribute($name);
			$att->value = ewr_XmlEncode(ewr_ConvertToUtf8($val));
			$node->appendChild($att);
		}
	}

	// Check color code
	function CheckColorCode($val) {
		if ($this->IsFCFChart() && substr($val, 0, 1) == "#" && strlen($val) == 7) {
			return substr($val, 1);
		} else {
			return $val;
		}
	}

	// Is stack chart
	function IsStackedChart() {
		return in_array($this->ChartType, array(14,15,16,17));
	}

	// FusionCharts Free type
	function IsFCFChart() {
		return EWR_FUSIONCHARTS_FREE && ($this->ChartType == 20 || $this->ChartType == 21 || $this->ChartType == 22);
	}

	// Encode "+" as "%2B" for FusionChartsFree
	function ChartEncode($val) {
		return ($this->IsFCFChart()) ? str_replace("+", "%2B", $val) : $val;
	}

	// Format number for chart
	function ChartFormatNumber($v) {
		$cht_decimalprecision = $this->LoadParm("decimals");
		if (is_null($cht_decimalprecision)) {
			if ($this->ChartDefaultDecimalPrecision >= 0)
				$cht_decimalprecision = $this->ChartDefaultDecimalPrecision; // Use default precision
			else
				$cht_decimalprecision = (($v-(int)$v) == 0) ? 0 : strlen(abs($v-(int)$v))-2; // Use original decimal precision
		}
		return number_format($v, $cht_decimalprecision, '.', '');
	}

	// Category content XML (multi series)
	function WriteChartCatContent(&$node, $name) {
		$cat = $this->XmlDoc->createElement("category");
		$this->WriteAtt($cat, "label", $name);
	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Global","Chart_DataRendered")) { ##-->
		$this->Chart_DataRendered($cat);
	<!--## } ##-->
		$node->appendChild($cat);
	}

	// Chart content XML
	function WriteChartContent(&$node, $name, $val, $color, $alpha, $lnk) {
		$cht_shownames = $this->LoadParm("shownames");
		$set = $this->XmlDoc->createElement("set");
		$this->WriteAtt($set, ($this->IsFCFChart()) ? "name" : "label", $name);
		$this->WriteAtt($set, "value", $this->ChartFormatNumber($val));
		$this->WriteAtt($set, "color", $this->ColorCode($color));
		$this->WriteAtt($set, "alpha", $alpha);
		$this->WriteAtt($set, "link", $lnk);
		if ($cht_shownames == "1")
			$this->WriteAtt($set, "showName", "1");
	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Global","Chart_DataRendered")) { ##-->
		$this->Chart_DataRendered($set);
	<!--## } ##-->
		$node->appendChild($set);
	}

	// Get chart link
	function GetChartLink($src, $row) {
		if ($src <> "" && is_array($row)) {
			$cntrow = count($row);
			$lnk = $src;
			$sdt = $this->SeriesDateType;
			$xdt = $this->XAxisDateFormat;
			$ndt = ($this->ChartType == 20) ? $this->NameDateFormat : "";
			if ($sdt <> "") $xdt = $sdt;
			if (preg_match("/&t=([^&]+)&/", $lnk, $m))
				$tblcaption = $GLOBALS["ReportLanguage"]->TablePhrase($m[1], 'TblCaption');
			else
				$tblcaption = "";
			for ($i = 0; $i < $cntrow; $i++) { // Link format: %i:Parameter:FieldType%
				if (preg_match("/%" . $i . ":([^%:]*):([\d]+)%/", $lnk, $m)) {
					$fldtype = ewr_FieldDataType($m[2]);
					if ($i == 0) { // Format X SQL
						$lnk = str_replace($m[0], ewr_Encrypt(ewr_ChartXSQL("@" . $m[1], $fldtype, $row[$i], $xdt)), $lnk);
					} elseif ($i == 1) { // Format Series SQL
						$lnk = str_replace($m[0], ewr_Encrypt(ewr_ChartSeriesSQL("@" . $m[1], $fldtype, $row[$i], $sdt)), $lnk);
					} else {
						$lnk = str_replace($m[0], ewr_Encrypt("@" . $m[1] . " = " . ewr_QuotedValue($row[$i], $fldtype)), $lnk);
					}
				}
			}
			return "javascript:" . ewr_DrillDownJs($lnk, $this->ID, $tblcaption, $this->UseDrillDownPanel, "div_" . $this->ID, FALSE);
		} else {
			return "";
		}
	}

	// Show chart (FusionCharts)
	function ShowChartFC($xml, $scroll = FALSE, $drilldown = FALSE) {
		global $ReportLanguage;
		$typ = $this->ChartType; // Chart type (1/2/3/4/...)
		$id = $this->ID; // Chart ID
		$parms = $this->Parms; // "bgcolor=FFFFFF|..."
		$trends = $this->Trends; // Trend lines
		$data = $this->Data;
		$series = $this->Series;
		$width = $this->ChartWidth;
		$height = $this->ChartHeight;
		$align = $this->ChartAlign;
		if (empty($typ))
			$typ = 1;

		// Get chart path / swf
		$fcfchart = $this->IsFCFChart();
		$showgrid = $this->UseGridComponent;
		if ($typ > 8 && $typ <> 104 && $typ <> 22 && $typ <> 101) $showgrid = FALSE;

		$charttype = "";
		switch ($typ) {

			// Single Series
			case 1:	$charttype = ($scroll) ? "scrollcolumn2d" : "column2d"; break; // Column 2D
			case 2:	$charttype = "pie2d"; break; // Pie 2D
			case 3:	$charttype = "bar2d"; break; // Bar 2D
			case 4: $charttype = ($scroll) ? "scrollline2d" : "line"; break; // Line 2D
			case 5: $charttype = "column3d"; break; // Column 3D
			case 6: $charttype = "pie3d"; break; // Pie 3D
			case 7: $charttype = ($scroll) ? "scrollarea2d" : "area2d"; break; // Area 2D
			case 8: $charttype = "doughnut2d"; break; // Doughnut 2D
			
			// Multi Series
			case 9: $charttype = ($scroll) ? "scrollcolumn2d" : "mscolumn2d"; break; // Multi-series Column 2D
			case 10: $charttype = "mscolumn3d"; break; // Multi-series Column 3D
			case 11: $charttype = ($scroll) ? "scrollline2d" : "msline"; break; // Multi-series Line 2D
			case 12: $charttype = ($scroll) ? "scrollarea2d" : "msarea"; break; // Multi-series Area 2D
			case 13: $charttype = "msbar2d"; break; // Multi-series Bar 2D
			
			// Stacked
			case 14: $charttype = ($scroll) ? "scrollstackedcolumn2d" : $charttype = "stackedcolumn2d"; break; // Stacked Column 2D
			case 15: $charttype = "stackedcolumn3d"; break; // Stacked Column 3D
			case 16: $charttype = "stackedarea2d"; break; // Stacked Area 2D
			case 17: $charttype = "stackedbar2d"; break; // Stacked Bar 2D
			
			// Combination
			case 18: $charttype = ($scroll) ? "scrollcombidy2d" : "mscombidy2d"; break; // Multi-series Column 2D Line Dual Y Chart
			case 19: $charttype = "mscolumn3dlinedy"; break; // Multi-series Column 3D Line Dual Y Chart
			
			// Financial
			case 20: $charttype = EWR_FUSIONCHARTS_FREE ? EWR_FUSIONCHARTS_FREE_CHART_PATH . "FCF_Candlestick.swf" : "candlestick"; break; // Candlestick
			
			// Other
			case 21: $charttype = EWR_FUSIONCHARTS_FREE ? EWR_FUSIONCHARTS_FREE_CHART_PATH . "FCF_Gantt.swf" : "gantt"; break; // Gantt
			case 22: $charttype = EWR_FUSIONCHARTS_FREE ? EWR_FUSIONCHARTS_FREE_CHART_PATH . "FCF_Funnel.swf" : "funnel"; break; // Funnel
			
			// Additional FusionCharts
			case 101: $charttype = "doughnut3d"; break; // Doughnut 3D
			case 102: $charttype = "msbar3d"; break; // Multi-series Bar 3D
			case 103: $charttype = "stackedbar3d"; break; // Stacked Bar 3D
			case 104: $charttype = "msbar3d"; break; // Bar 3D (using Multi-series Bar 3D for single series)
			
			// Default
			default: $charttype = "column2d"; // Default = Column 2D

		}

		// Set width, height and align
		if (is_numeric($width) && is_numeric($height)) {
			$wrkwidth = $width;
			$wrkheight = $height;
		} else { // Default
			$wrkwidth = EWR_CHART_WIDTH;
			$wrkheight = EWR_CHART_HEIGHT;
		}

		// Output JavaScript for FC
		$chartxml = $xml;
		if ($chartxml == "") $chartxml = $fcfchart ? "<graph/>" : ""; // Empty chart
		$chartid = "chart_" . $id;
		if ($drilldown) $chartid .= "_" . ewr_Random();
		if ($fcfchart && ewr_IsMobile()) {
			$wrk = "<div>" . $ReportLanguage->Phrase("BrowserNoFlashSupport") . "</div>";
		} else {
			$wrk = "<script type=\"text/javascript\">\n";
			$wrk .= "var chartoptions = { \"width\": " . $wrkwidth . ", \"height\": " . $wrkheight . ",\n" .
				"\t\"id\": \"" . $chartid . "\", \"type\": \"" . $charttype . "\" };\n";
			$wrk .= "var chartxml = \"" . ewr_EscapeJs($chartxml) . "\";\n";
			if ($fcfchart) {
				$wrk .= "var cht_$id = new FusionChartsFree(chartoptions.type, chartoptions.id, chartoptions.width, chartoptions.height);\n";
				$wrk .= "cht_$id.addParam(\"wmode\", \"transparent\");\n";
				$wrk .= "cht_$id.setDataXML(chartxml);\n";
			} else {
				$wrk .= "var cht_$id = new FusionCharts(chartoptions);\n";
				$wrk .= "cht_$id.setXMLData(chartxml);\n";
				$wrk .= ($drilldown) ? "ewrDrillCharts[ewrDrillCharts.length] = cht_$id.id;\n" :
					"ewrExportCharts[ewrExportCharts.length] = cht_$id.id;\n"; // Export chart
			}
			$wrk .= "var f = " . CurrentPage()->PageObjName . ".Chart_Rendering;\n";
			$wrk .= "if (typeof f == \"function\") f(cht_$id, 'chart_$id');\n";
			$wrk .= "cht_$id.render(\"div_" . $id . "\");\n";
			$wrk .= "f = " . CurrentPage()->PageObjName . ".Chart_Rendered;\n";
			$wrk .= "if (typeof f == \"function\") f(cht_$id, 'chart_$id');\n";

			// Grid component
			if ($showgrid && $chartxml <> "") {

				// Load Bar2D XML for Bar3D
				if ($typ == 104) {
					$this->SetChartParm("type", "3", FALSE); // Reset to 2D
					$this->XmlDoc = new DOMDocument("1.0", "utf-8");
					$chartxml = $this->ChartXml();
					$this->SetChartParm("type", "104", FALSE); // Restore chart type
				}

				// Remove clickurl first
				$doc = new DOMDocument();
				$doc->loadXML($chartxml);
				$doc->documentElement->setAttribute("clickurl", "");
				$chartgridxml = $doc->saveXML();

				$gridid = $id . "_grid";
				$chartid = "chart_" . $gridid;
				if ($drilldown) $chartid .= "_" . ewr_Random();
				$wrkgridheight = $this->ChartGridHeight;
				$wrk .= "chartxml = \"" . ewr_EscapeJs($chartgridxml) . "\";\n";
				$wrk .= "chartoptions = { \"width\": " . $wrkwidth . ", \"height\": " . $wrkgridheight . ",\n" .
					"\t\"id\": \"" . $chartid . "\", \"type\": \"ssgrid\" };\n";
				$wrk .= "var cht_$gridid = new FusionCharts(chartoptions);\n";
				$wrk .= "cht_$gridid.setXMLData(chartxml);\n";
				$wrk .= ($drilldown) ? "ewrDrillCharts[ewrDrillCharts.length] = cht_$gridid.id;\n" :
					"ewrExportCharts[ewrExportCharts.length] = cht_$gridid.id;\n"; // Export chart
				// Set Grid specific parameters
				if ($this->ChartGridConfig)
					$wrk .= "cht_$gridid.configure(" . $this->ChartGridConfig . ");\n";
				$wrk .= "cht_$gridid.render(\"div_" . $gridid . "\");\n";
			}

			// Debug mode
			if (!$fcfchart && EWR_DEBUG_ENABLED)
				$wrk .= "FusionCharts[\"debugger\"].enable(true, function(message) { console.log(message); });\n";

			$wrk .= "</script>\n";
		}

		// Show XML for debug
		if (EWR_DEBUG_ENABLED)
			$wrk .= "<p>(Chart XML): " . ewr_HtmlEncode($chartxml) . "</p>";
		return $wrk;
	}

	<!--##~SYSTEMFUNCTIONS.GetServerScript("Global","Chart_Rendering")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Global","Chart_DataRendered")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Global","Chart_Rendered")##-->

}

//
// Column class
//
class crCrosstabColumn {
	var $Caption;
	var $Value;
	var $Visible;

	function __construct($value, $caption, $visible = TRUE) {
		$this->Caption = $caption;
		$this->Value = $value;
		$this->Visible = $visible;
    }
}

//
// Advanced filter class
//
class crAdvancedFilter {
	var $ID;
	var $Name;
	var $FunctionName;
	var $Enabled = TRUE;

	function __construct($filterid, $filtername, $filterfunc) {
		$this->ID = $filterid;
		$this->Name = $filtername;
		$this->FunctionName = $filterfunc;
	}
}

<!--## if (!bGenCompatHeader) { ##-->

/**
 * Menu class
 */
class crMenu {

	var $Id;
	var $MenuBarClassName = EWR_MENUBAR_CLASSNAME;
	var $MenuClassName = EWR_MENU_CLASSNAME;
	var $SubMenuClassName = EWR_SUBMENU_CLASSNAME;
	var $SubMenuDropdownImage = EWR_SUBMENU_DROPDOWN_IMAGE;
	var $SubMenuDropdownIconClassName = EWR_SUBMENU_DROPDOWN_ICON_CLASSNAME;
	var $MenuDividerClassName = EWR_MENU_DIVIDER_CLASSNAME;
	var $MenuItemClassName = EWR_MENU_ITEM_CLASSNAME;
	var $SubMenuItemClassName = EWR_SUBMENU_ITEM_CLASSNAME;
	var $MenuActiveItemClassName = EWR_MENU_ACTIVE_ITEM_CLASS;
	var $SubMenuActiveItemClassName = EWR_SUBMENU_ACTIVE_ITEM_CLASS;
	var $MenuRootGroupTitleAsSubMenu = EWR_MENU_ROOT_GROUP_TITLE_AS_SUBMENU;
	var $ShowRightMenu = EWR_SHOW_RIGHT_MENU;
	var $MenuLinkDropdownClass = "";
	var $MenuLinkClassName = "";
	var $IsMobile = FALSE;
	var $IsRoot = FALSE;
	var $NoItem = NULL;
	var $ItemData = array();

	function __construct($id, $mobile = FALSE) {
		$this->Id = $id;
		$this->IsMobile = $mobile;
	}

	// Add a menu item
	function AddMenuItem($id, $name, $text, $url, $parentid = -1, $src = "", $allowed = TRUE, $grouptitle = FALSE, $customurl = FALSE) {
		$item = new crMenuItem($id, $name, $text, $url, $parentid, $src, $allowed, $grouptitle, $customurl);
		$item->Parent = &$this;

		// Fire MenuItem_Adding event
		if (function_exists("MenuItem_Adding") && !MenuItem_Adding($item))
			return;

		if ($item->ParentId < 0) {
			$this->AddItem($item);
		} else {
			if ($oParentMenu = &$this->FindItem($item->ParentId))
				$oParentMenu->AddItem($item, $this->IsMobile);
		}
	}

	// Add item to internal array
	function AddItem($item) {
		$this->ItemData[] = $item;
	}

	// Clear all menu items
	function Clear() {
		$this->ItemData = array();
	}

	// Find item
	function &FindItem($id) {
		$cnt = count($this->ItemData);
		for ($i = 0; $i < $cnt; $i++) {
			$item = &$this->ItemData[$i];
			if ($item->Id == $id) {
				return $item;
			} elseif (!is_null($item->SubMenu)) {
				if ($subitem = &$item->SubMenu->FindItem($id))
					return $subitem;
			}
		}
		$noitem = $this->NoItem;
		return $noitem;
	}

	// Find item by menu text
	function &FindItemByText($txt) {
		$cnt = count($this->ItemData);
		for ($i = 0; $i < $cnt; $i++) {
			$item = &$this->ItemData[$i];
			if ($item->Text == $txt) {
				return $item;
			} elseif (!is_null($item->SubMenu)) {
				if ($subitem = &$item->SubMenu->FindItemByText($txt))
					return $subitem;
			}
		}
		$noitem = $this->NoItem;
		return $noitem;
	}

	// Get menu item count
	function Count() {
		return count($this->ItemData);
	}

	// Move item to position
	function MoveItem($Text, $Pos) {
		$cnt = count($this->ItemData);
		if ($Pos < 0) {
			$Pos = 0;
		} elseif ($Pos >= $cnt) {
			$Pos = $cnt - 1;
		}
		$item = NULL;
		$cnt = count($this->ItemData);
		for ($i = 0; $i < $cnt; $i++) {
			if ($this->ItemData[$i]->Text == $Text) {
				$item = $this->ItemData[$i];
				break;
			}
		}
		if ($item) {
			unset($this->ItemData[$i]);
			$this->ItemData = array_merge(array_slice($this->ItemData, 0, $Pos),
				array($item), array_slice($this->ItemData, $Pos));
		}
	}

	// Check if sub menu should be shown
	function RenderSubMenu($item) {
		if (!is_null($item->SubMenu)) {
			foreach ($item->SubMenu->ItemData as $subitem) {
				if ($item->SubMenu->RenderItem($subitem))
					return TRUE;
			}
		}
		return FALSE;
	}

	// Check if a menu item should be shown
	function RenderItem($item) {
		if (!is_null($item->SubMenu)) {
			foreach ($item->SubMenu->ItemData as $subitem) {
				if ($item->SubMenu->RenderItem($subitem))
					return TRUE;
			}
		}
		return ($item->Allowed && $item->Url <> "");
	}
	
	// Check if this menu should be rendered
	function RenderMenu() {
		foreach ($this->ItemData as $item) {
			if ($this->RenderItem($item))
				return TRUE;
		}
		return FALSE;
	}

	// Render the menu
	function Render($ret = FALSE) {
		if (function_exists("Menu_Rendering") && $this->IsRoot) Menu_Rendering($this);
		if (!$this->RenderMenu())
			return;
		if (!$this->IsMobile) {
			if ($this->IsRoot) {
				$str = "<ul";
				if ($this->Id <> "") {
					if (is_numeric($this->Id)) {
						$str .= " id=\"menu_" . $this->Id . "\"";
					} else {
						$str .= " id=\"" . $this->Id . "\"";
					}
				}
				$str .= " class=\"" . $this->MenuClassName . "\">\n";
			} else {
				$str = "<ul class=\"" . $this->SubMenuClassName . "\" role=\"menu\">\n";
			}
		} else {
			$str = "";
		}
		$gcnt = 0; // Group count
		$gtitle = FALSE; // Last item is group title
		$i = 0; // Menu item count
		$cururl = substr(ewr_CurrentUrl(), strrpos(ewr_CurrentUrl(), "/")+1);
		foreach ($this->ItemData as $item) {
			if ($this->RenderItem($item)) {
				$i++;
				if (!$this->IsMobile && $gtitle && ($gcnt >= 1 || $this->IsRoot)) // Add divider for previous group
					$str .= "<li class=\"" . $this->MenuDividerClassName . "\"></li>\n";
				if ($item->GroupTitle && (!$this->IsRoot || !$this->MenuRootGroupTitleAsSubMenu)) { // Group title
					$gtitle = TRUE;
					$gcnt += 1;
					if (strval($item->Text) <> "") {
						if ($this->IsMobile)
							$str .= "<li data-role=\"list-divider\">" . $item->Text . "</li>\n";
						else
							$str .= "<li class=\"dropdown-header\">" . $item->Text . "</li>\n";
					}
					if (!is_null($item->SubMenu)) {
						foreach ($item->SubMenu->ItemData as $subitem) {
							$liclass = !is_null($subitem->SubMenu) && $this->RenderSubMenu($subitem) ? $this->SubMenuItemClassName : "";
							$aclass = "";
							if (!$subitem->IsCustomUrl && ewr_CurrentPage() == ewr_GetPageName($subitem->Url) || $subitem->IsCustomUrl && $cururl == $subitem->Url) {
								ewr_AppendClass($liclass, $this->MenuActiveItemClassName);
								$subitem->Url = "javascript:void(0);";
							}
							if ($this->RenderItem($subitem)) {
								if ($this->IsMobile && $item->GroupTitle)
									ewr_AppendClass($aclass, "ewIndent");
								$str .= $subitem->Render($aclass, $liclass, $this->IsMobile) . "\n"; // Create <LI>
							}
						}
					}
				} else {
					$gtitle = FALSE;
					$liclass = !is_null($item->SubMenu) && $this->RenderSubMenu($item) ? ($this->IsRoot ? $this->MenuItemClassName : $this->SubMenuItemClassName) : "";
					$aclass = "";
					if (!$item->IsCustomUrl && ewr_CurrentPage() == ewr_GetPageName($item->Url) || $item->IsCustomUrl && $cururl == $item->Url) {
						if ($this->IsRoot)
							ewr_AppendClass($liclass, $this->MenuActiveItemClassName);
						else
							ewr_AppendClass($liclass, $this->SubMenuActiveItemClassName);
						$item->Url = "javascript:void(0);";
					}
					$str .= $item->Render($aclass, $liclass, $this->IsMobile) . "\n"; // Create <LI>
				}
			}
		}
		if ($this->IsMobile) {
			$str = "<ul data-role=\"listview\" data-filter=\"true\">" . $str . "</ul>\n";
		} elseif ($this->IsRoot) {
			$str .= "</ul>\n";
			if (EWR_MENUBAR_BRAND <> "") {
				$brandhref = (EWR_MENUBAR_BRAND_HYPERLINK == "") ? "#" : EWR_MENUBAR_BRAND_HYPERLINK;
				$str = "<a class=\"navbar-brand hidden-xs\" href=\"" . ewr_HtmlEncode($brandhref) . "\">" . EWR_MENUBAR_BRAND . "</a>" . $str;
			}
			// Add right menu
			if ($this->ShowRightMenu)
				$str .= "<ul class=\"nav navbar-nav navbar-right\"></ul>";
			if ($this->MenuBarClassName <> "")
				$str = "<div class=\"" . $this->MenuBarClassName . "\">" . $str . "</div>";
		} else {
			$str .= "</ul>\n";
		}
		if ($ret) // Return as string
			return $str;
		echo $str; // Output
	}

}

// Menu item class
class crMenuItem {

	var $Id;
	var $Name;
	var $Text;
	var $Url;
	var $ParentId; 
	var $SubMenu = NULL; // Data type = crMenu
	var $Source;
	var $Allowed = TRUE;
	var $Target;
	var $GroupTitle;
	var $IsCustomUrl;
	var $Parent;

	// Constructor
	function __construct($id, $name, $text, $url, $parentid = -1, $src = "", $allowed = TRUE, $grouptitle = FALSE, $customurl = FALSE) {
		$this->Id = $id;
		$this->Name = $name;
		$this->Text = $text;
		$this->Url = $url;
		$this->ParentId = $parentid;
		$this->Source = $src;
		$this->Allowed = $allowed;
		$this->GroupTitle = $grouptitle;
		$this->IsCustomUrl = $customurl;
	}

	// Add submenu item
	function AddItem($item, $mobile = FALSE) {
		if (is_null($this->SubMenu)) {
			$this->SubMenu = new crMenu($this->Id, $mobile);
			$this->SubMenu->MenuBarClassName = $this->Parent->MenuBarClassName;
			$this->SubMenu->MenuClassName = $this->Parent->MenuClassName;
			$this->SubMenu->SubMenuClassName = $this->Parent->SubMenuClassName;
			$this->SubMenu->SubMenuDropdownImage = $this->Parent->SubMenuDropdownImage;
			$this->SubMenu->SubMenuDropdownIconClassName = $this->Parent->SubMenuDropdownIconClassName;
			$this->SubMenu->MenuDividerClassName = $this->Parent->MenuDividerClassName;
			$this->SubMenu->MenuItemClassName = $this->Parent->MenuItemClassName;
			$this->SubMenu->SubMenuItemClassName = $this->Parent->SubMenuItemClassName;
			$this->SubMenu->MenuActiveItemClassName = $this->Parent->MenuActiveItemClassName;
			$this->SubMenu->SubMenuActiveItemClassName = $this->Parent->SubMenuActiveItemClassName;
			$this->SubMenu->MenuRootGroupTitleAsSubMenu = $this->Parent->MenuRootGroupTitleAsSubMenu;
			$this->SubMenu->MenuLinkDropdownClass = $this->Parent->MenuLinkDropdownClass;
			$this->SubMenu->MenuLinkClassName = $this->Parent->MenuLinkClassName;
		}
		$this->SubMenu->AddItem($item);
	}
	
	// Render
	function Render($aclass = "", $liclass = "", $mobile = FALSE) {
		// Create <A>
		$url = ewr_GetUrl($this->Url);
		if (!is_null($this->SubMenu))
			$submenuhtml = $this->SubMenu->Render(TRUE);
		else
			$submenuhtml = "";
		if ($mobile) {
			//###$url = str_replace("#","?chart=",$url);
			if ($url == "") $url = "#";
			$attrs = array("class" => $aclass, "rel" => ($url != "#") ? "external" : "", "href" => $url, "target" => $this->Target);
		} else {
			if ($url == "") $url = "#";
			if (!is_null($this->SubMenu) && $this->SubMenu->MenuLinkDropdownClass <> "" && $submenuhtml <> "")
				ewr_PrependClass($aclass, $this->SubMenu->MenuLinkDropdownClass);
			$attrs = array("class" => $aclass, "href" => $url, "target" => $this->Target);
		}
		$text = $this->Text;
		if (!is_null($this->SubMenu) && $submenuhtml <> "") {
			if ($this->Parent->SubMenuDropdownIconClassName <> "")
				$text .= "<span class=\"" . $this->Parent->SubMenuDropdownIconClassName . "\"></span>";
			if ($this->Parent->SubMenuDropdownImage <> "" && $this->ParentId == -1)
				$text .= $this->Parent->SubMenuDropdownImage;
		}
		$innerhtml = ewr_HtmlElement("a", $attrs, $text);
		if (!is_null($this->SubMenu)) {
			if ($url <> "#" && $this->SubMenu->MenuLinkClassName <> "" && $submenuhtml <> "") { // Add click link for mobile menu
				$attrs2 = array("class" => "ewMenuLink", "href" => $url);
				$text2 = "<span class=\"" . $this->SubMenu->MenuLinkClassName . "\"></span>";
				$innerhtml = ewr_HtmlElement("a", $attrs2, $text2) . $innerhtml;
			}
			if ($mobile && $this->Url <> "#")
				$innerhtml .= $innerhtml;
			$innerhtml .= $submenuhtml;
		}
		// Create <LI>
		return ewr_HtmlElement("li", array("id" => $this->Name, "class" => $liclass), $innerhtml);
	}

}

// Menu Rendering event
<!--##~SYSTEMFUNCTIONS.GetServerScript("Global","Menu_Rendering")##-->
// MenuItem Adding event
<!--##~SYSTEMFUNCTIONS.GetServerScript("Global","MenuItem_Adding")##-->

<!--## } ##-->

/**
 * List option collection class
 */
class crListOptions {
	var $Items = array();
	var $CustomItem = "";
	var $Tag = "td";
	var $TagClassName = "";
	var $TableVar = "";
	var $RowCnt = "";
	var $ScriptType = "block";
	var $ScriptId = "";
	var $ScriptClassName = "";
	var $JavaScript = "";
	var $RowSpan = 1;
	var $UseDropDownButton = FALSE;
	var $UseButtonGroup = FALSE;
	var $ButtonClass = "";
	var $GroupOptionName = "button";
	var $DropDownButtonPhrase = "";
	var $UseImageAndText = FALSE;

	// Check visible
	function Visible() {
		foreach ($this->Items as $item) {
			if ($item->Visible)
				return TRUE;
		}
		return FALSE;
	}

	// Check group option visible
	function GroupOptionVisible() {
		$cnt = 0;
		foreach ($this->Items as $item) {
			if ($item->Name <> $this->GroupOptionName && 
				(($item->Visible && $item->ShowInDropDown && $this->UseDropDownButton) ||
				($item->Visible && $item->ShowInButtonGroup && $this->UseButtonGroup))) {
				$cnt += 1;
				if ($this->UseDropDownButton && $cnt > 1)
					return TRUE;
				elseif ($this->UseButtonGroup)
					return TRUE;
			}
		}
		return FALSE;
	}

	// Add and return a new option
	function &Add($Name) {
		$item = new crListOption($Name);
		$item->Parent = &$this;
		$this->Items[$Name] = $item;
		return $item;
	}

	// Load default settings
	function LoadDefault() {
		$this->CustomItem = "";
		foreach ($this->Items as $key => $item)
			$this->Items[$key]->Body = "";
	}

	// Hide all options
	function HideAllOptions($Lists=array()) {
		foreach ($this->Items as $key => $item)
			if (!in_array($key, $Lists))
				$this->Items[$key]->Visible = FALSE;
	}

	// Show all options
	function ShowAllOptions() {
		foreach ($this->Items as $key => $item)
			$this->Items[$key]->Visible = TRUE;
	}

	// Get item by name
	// Predefined names: view/edit/copy/delete/detail_<DetailTable>/userpermission/checkbox
	function &GetItem($Name) {
		$item = array_key_exists($Name, $this->Items) ? $this->Items[$Name] : NULL;
		return $item;
	}

	// Get item position
	function ItemPos($Name) {
		$pos = 0;
		foreach ($this->Items as $item) {
			if ($item->Name == $Name)
				return $pos;
			$pos++;
		}
		return FALSE;
	}

	// Move item to position
	function MoveItem($Name, $Pos) {
		$cnt = count($this->Items);
		if ($Pos < 0) // If negative, count from the end
			$Pos = $cnt + $Pos;
		if ($Pos < 0)
			$Pos = 0;
		if ($Pos >= $cnt)
			$Pos = $cnt - 1;
		$item = $this->GetItem($Name);
		if ($item) {
			unset($this->Items[$Name]);
			$this->Items = array_merge(array_slice($this->Items, 0, $Pos),
				array($Name => $item), array_slice($this->Items, $Pos));
		}
	}

	// Render list options
	function Render($Part, $Pos="", $RowCnt="", $ScriptType="block", $ScriptId="", $ScriptClassName="") {

		if ($this->CustomItem == "" && $groupitem = &$this->GetItem($this->GroupOptionName) && $this->ShowPos($groupitem->OnLeft, $Pos)) {
			if ($this->UseDropDownButton) { // Render dropdown
				$buttonvalue = "";
				$cnt = 0;
				foreach ($this->Items as $item) {
					if ($item->Name <> $this->GroupOptionName && $item->Visible && $item->ShowInDropDown) {
						$buttonvalue .= $item->Body;
						$cnt += 1;
					}
				}
				if ($cnt <= 1) {
					$this->UseDropDownButton = FALSE; // No need to use drop down button
				} else {
					$groupitem->Body = $this->RenderDropDownButton($buttonvalue, $Pos);
					$groupitem->Visible = TRUE;
				}
			}
			if (!$this->UseDropDownButton && $this->UseButtonGroup) { // Render button group
				$visible = FALSE;
				$buttongroups = array();
				foreach ($this->Items as $item) {
					if ($item->Name <> $this->GroupOptionName && $item->Visible && $item->ShowInButtonGroup && $item->Body <> "") {
						$visible = TRUE;
						$buttonvalue = ($this->UseImageAndText) ? $item->GetImageAndText($item->Body) : $item->Body;
						if (!array_key_exists($item->ButtonGroupName, $buttongroups)) $buttongroups[$item->ButtonGroupName] = "";
						$buttongroups[$item->ButtonGroupName] .= $buttonvalue;
					}
				}
				$groupitem->Body = "";
				foreach ($buttongroups as $buttongroup => $buttonvalue)
					$groupitem->Body .= $this->RenderButtonGroup($buttonvalue);
				if ($visible)
				$groupitem->Visible = TRUE;
			}
		}

		$this->RenderEx($Part, $Pos, $RowCnt, $ScriptType, $ScriptId, $ScriptClassName);
	}

	function RenderEx($Part, $Pos="", $RowCnt="", $ScriptType="block", $ScriptId="", $ScriptClassName="") {
		$this->RowCnt = $RowCnt;
		$this->ScriptType = $ScriptType;
		$this->ScriptId = $ScriptId;
		$this->ScriptClassName = $ScriptClassName;
		$this->JavaScript = "";
		//$this->Tag = ($Pos <> "" && $Pos <> "bottom") ? "td" : "span";
		$this->Tag = ($Pos <> "" && $Pos <> "bottom") ? "td" : "div";
		if ($this->CustomItem <> "") {
			$cnt = 0;
			$opt = NULL;
			foreach ($this->Items as &$item) {
				if ($this->ShowItem($item, $ScriptId, $Pos))
					$cnt++;
				if ($item->Name == $this->CustomItem)
					$opt = &$item;
			}
			$bUseButtonGroup = $this->UseButtonGroup; // Backup options
			$bUseImageAndText = $this->UseImageAndText;
			$this->UseButtonGroup = TRUE; // Show button group for custom item
			$this->UseImageAndText = TRUE; // Use image and text for custom item
			if (is_object($opt) && $cnt > 0) {
				if ($ScriptId <> "" || $this->ShowPos($opt->OnLeft, $Pos)) {
					echo $opt->Render($Part, $cnt);
				} else {
					echo $opt->Render("", $cnt);
				}
			}
			$this->UseButtonGroup = $bUseButtonGroup; // Restore options
			$this->UseImageAndText = $bUseImageAndText;
		} else {
			foreach ($this->Items as &$item) {
				if ($this->ShowItem($item, $ScriptId, $Pos))
					echo $item->Render($Part, 1);
			}
		}
	}

	function ShowItem($item, $ScriptId, $Pos) {
		$show = $item->Visible && ($ScriptId <> "" || $this->ShowPos($item->OnLeft, $Pos));
		if ($show)
			if ($this->UseDropDownButton)
				$show = ($item->Name == $this->GroupOptionName || !$item->ShowInDropDown);
			elseif ($this->UseButtonGroup)
				$show = ($item->Name == $this->GroupOptionName || !$item->ShowInButtonGroup);
		return $show;
	}

	function ShowPos($OnLeft, $Pos) {
		return ($OnLeft && $Pos == "left") || (!$OnLeft && $Pos == "right") || ($Pos == "") || ($Pos == "bottom");
	}

	// Concat options and return concatenated HTML
	// - pattern - regular expression pattern for matching the option names, e.g. '/^detail_/'
	function Concat($pattern, $separator = "") {
		$ar = array();
		$keys = array_keys($this->Items);
		foreach ($keys as $key) {
			if (preg_match($pattern, $key) && trim($this->Items[$key]->Body) <> "")
				$ar[] = $this->Items[$key]->Body;
		}
		return implode($separator, $ar);
	}

	// Merge options to the first option and return it
	// - pattern - regular expression pattern for matching the option names, e.g. '/^detail_/'
	function &Merge($pattern, $separator = "") {
		$keys = array_keys($this->Items);
		$first = NULL;
		foreach ($keys as $key) {
			if (preg_match($pattern, $key)) {
				if (!$first) {
					$first = $this->Items[$key];
					$first->Body = $this->Concat($pattern, $separator);
				} else {
					$this->Items[$key]->Visible = FALSE;
				}
			}
		}
		return $first;
	}

	// Get button group link
	function RenderButtonGroup($body) {
		// Get all hidden inputs
		// format: <input type="hidden" ...>
//		$inputs = array();
//		if (preg_match_all('/<input\s+([^>]*)>/i', $body, $inputmatches, PREG_SET_ORDER)) {
//			foreach ($inputmatches as $inputmatch) {
//				$body = str_replace($inputmatch[0], '', $body); 
//				if (preg_match('/\s+type\s*=\s*[\'"]hidden[\'"]/i', $inputmatch[0])) // Match type='hidden'
//					$inputs[] = $inputmatch[0];
//			}
//		}
		// Get all buttons
		// format: <div class="btn-group">...</div>
		$btns = array();
		if (preg_match_all('/<div\s+class\s*=\s*[\'"]btn-group[\'"]([^>]*)>([\s\S]*?)<\/div\s*>/i', $body, $btnmatches, PREG_SET_ORDER)) {
			foreach ($btnmatches as $btnmatch) {
				$body = str_replace($btnmatch[0], '', $body); 
				$btns[] = $btnmatch[0];
			}
		}
		$links = '';
		// Get all links/buttons
		// format: <a ...>...</a> / <button ...>...</button>
		if (preg_match_all('/<(a|button)([^>]*)>([\s\S]*?)<\/(a|button)\s*>/i', $body, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$tag = $match[1];
				if (preg_match('/\s+class\s*=\s*[\'"]([\s\S]*?)[\'"]/i', $match[2], $submatches)) { // Match class='class'
					$class = $submatches[1];
					$attrs = str_replace($submatches[0], '', $match[2]);
				} else {
					$class = '';
					$attrs = $match[2];
				}
				$caption = $match[3];
				if (strpos($class, 'btn btn-default') === FALSE) // Prepend button classes
					ewr_PrependClass($class, 'btn btn-default');
				if ($this->ButtonClass <> "")
					ewr_AppendClass($class, $this->ButtonClass);
				$attrs = ' class="' . $class . '" ' . $attrs;
 				$link ='<' . $tag . $attrs . '>' . $caption . '</' . $tag . '>';
				$links .= $link;
			}
		}
		if ($links <> "")
			$btngroup = '<div class="btn-group ewButtonGroup">' . $links . '</div>';
		else
			$btngroup = "";
		foreach ($btns as $btn)
			$btngroup .= $btn;
		//foreach ($inputs as $input)
		//	$btngroup .= $input;
		return $btngroup;
	}

	// Render drop down button
	function RenderDropDownButton($body, $pos) {

		// Get all hidden inputs
		// format: <input type="hidden" ...>

//		$inputs = array();
//		if (preg_match_all('/<input\s+([^>]*)>/i', $body, $inputmatches, PREG_SET_ORDER)) {
//			foreach ($inputmatches as $inputmatch) {
//				$body = str_replace($inputmatch[0], '', $body); 
//				if (preg_match('/\s+type\s*=\s*[\'"]hidden[\'"]/i', $inputmatch[0])) // Match type='hidden'
//					$inputs[] = $inputmatch[0];
//			}
//		}

		// Remove toggle button first <button ... data-toggle="dropdown">...</button>
		if (preg_match_all('/<button\s+([\s\S]*?)data-toggle\s*=\s*[\'"]dropdown[\'"]\s*>([\s\S]*?)<\/button\s*>/i', $body, $btnmatches, PREG_SET_ORDER)) {
			foreach ($btnmatches as $btnmatch)
				$body = str_replace($btnmatch[0], '', $body);
		}

		// Get all links/buttons <a ...>...</a> / <button ...>...</button>
		if (!preg_match_all('/<(a|button)([^>]*)>([\s\S]*?)<\/(a|button)\s*>/i', $body, $matches, PREG_SET_ORDER))
			return '';
		$links = '';
		$submenu = FALSE;
		$submenulink = "";
		$submenulinks = "";
		foreach ($matches as $match) {
			$tag = $match[1];
			if (preg_match('/\s+data-action\s*=\s*[\'"]([\s\S]*?)[\'"]/i', $match[2], $actionmatches)) { // Match data-action='action'
				$action = $actionmatches[1];
			} else {
				$action = '';
			}
			if (preg_match('/\s+class\s*=\s*[\'"]([\s\S]*?)[\'"]/i', $match[2], $submatches)) { // Match class='class'
				$class = preg_replace('/btn[\S]*\s+/i', '', $submatches[1]);
				$attrs = str_replace($submatches[0], '', $match[2]);
			} else {
				$class = '';
				$attrs = $match[2];
			}
			$attrs = preg_replace('/\s+title\s*=\s*[\'"]([\s\S]*?)[\'"]/i', '', $attrs); // Remove title='title'
			if (preg_match('/\s+data-caption\s*=\s*[\'"]([\s\S]*?)[\'"]/i', $attrs, $submatches)) // Match data-caption='caption'
				$caption = $submatches[1];
			else
				$caption = '';
			$attrs = ' class="' . $class . '" ' . $attrs;
			if (strtolower($tag) == "button") // Add href for button
				$attrs .= ' href="javascript:void(0);"';
			if ($this->UseImageAndText) { // Image and text
				if (preg_match('/<img([^>]*)>/i', $match[3], $submatch)) // <img> tag
					$caption = $submatch[0] . '&nbsp;&nbsp;' . $caption;
				elseif (preg_match('/<span([^>]*)>([\s\S]*?)<\/span\s*>/i', $match[3], $submatch)) // <span class='class'></span> tag
					if (preg_match('/\s+class\s*=\s*[\'"]([\s\S]*?)[\'"]/i', $submatch[1], $submatches)) // Match class='class'
						$caption = $submatch[0] . '&nbsp;&nbsp;' . $caption;
			}
			if ($caption == '')
				$caption = $match[3];
			$link = '<a' . $attrs . '>' . $caption . '</a>';
			if ($action == 'list') { // Start new submenu
				if ($submenu) { // End previous submenu
					if ($submenulinks <> '') { // Set up submenu
						$links .= '<li class="dropdown-submenu">' . $submenulink . '<ul class="dropdown-menu">' . $submenulinks . '</ul></li>';
					} else {
						$links .= '<li>' . $submenulink . '</li>';
					}
				}
				$submenu = TRUE;
				$submenulink = $link;
				$submenulinks = "";
			} else {
				if ($action == '' && $submenu) { // End previous submenu
					if ($submenulinks <> '') { // Set up submenu
						$links .= '<li class="dropdown-submenu">' . $submenulink . '<ul class="dropdown-menu">' . $submenulinks . '</ul></li>';
					} else {
						$links .= '<li>' . $submenulink . '</li>';
					}
					$submenu = FALSE;
				}
				if ($submenu)
					$submenulinks .= '<li>' . $link . '</li>';
				else
					$links .= '<li>' . $link . '</li>';
			}
		}
		if ($links <> "") {
			if ($submenu) { // End previous submenu
				if ($submenulinks <> '') { // Set up submenu
					$links .= '<li class="dropdown-submenu">' . $submenulink . '<ul class="dropdown-menu">' . $submenulinks . '</ul></li>';
				} else {
					$links .= '<li>' . $submenulink . '</li>';
				}
			}
			$buttonclass = "dropdown-toggle btn btn-default";
			if ($this->ButtonClass <> "")
				ewr_AppendClass($buttonclass, $this->ButtonClass);
			$buttontitle = ewr_HtmlTitle($this->DropDownButtonPhrase);
			$buttontitle = ($this->DropDownButtonPhrase <> $buttontitle) ? ' title="' . $buttontitle . '"' : '';
			$button = '<button class="' . $buttonclass . '"' . $buttontitle . ' data-toggle="dropdown">' . $this->DropDownButtonPhrase . '<span class="caret"></span></button><ul class="dropdown-menu ewMenu">' . $links . '</ul>';
			if ($pos == "bottom") // Use dropup
				$btndropdown = '<div class="btn-group dropup ewButtonDropdown">' . $button . '</div>';
			else
				$btndropdown = '<div class="btn-group ewButtonDropdown">' . $button . '</div>';
		} else {
			$btndropdown = "";
		}
		//foreach ($inputs as $input)
			//$btndropdown .= $input;
		return $btndropdown;
	}
}

/**
 * List option class
 */
class crListOption {
	var $Name;
	var $OnLeft;
	var $CssStyle;
	var $CssClass;
	var $Visible = TRUE;
	var $Header;
	var $Body;
	var $Footer;
	var $Parent;
	var $ShowInButtonGroup = TRUE;
	var $ShowInDropDown = TRUE;
	var $ButtonGroupName = "_default";

	function __construct($Name) {
		$this->Name = $Name;
	}

	function MoveTo($Pos) {
		$this->Parent->MoveItem($this->Name, $Pos);
	}

	function Render($Part, $ColSpan = 1) {
		$tagclass = $this->Parent->TagClassName;
		if ($Part == "header") {
			if ($tagclass == "") $tagclass = "ewListOptionHeader";
			$value = $this->Header;
		} elseif ($Part == "body") {
			if ($tagclass == "") $tagclass = "ewListOptionBody";
			if ($this->Parent->Tag <> "td")
				ewr_AppendClass($tagclass, "ewListOptionSeparator");
			$value = $this->Body;
		} elseif ($Part == "footer") {
			if ($tagclass == "") $tagclass = "ewListOptionFooter";
			$value = $this->Footer;
		} else {
			$value = $Part;
		}
		if (strval($value) == "" && $this->Parent->Tag == "span" && $this->Parent->ScriptId == "")
			return "";
		$res = ($value <> "") ? $value : "&nbsp;";
		ewr_AppendClass($tagclass, $this->CssClass);
		$attrs = array("class" => $tagclass,  "style" => $this->CssStyle, "data-name" => $this->Name);
		if (strtolower($this->Parent->Tag) == "td" && $this->Parent->RowSpan > 1)
			$attrs["rowspan"] = $this->Parent->RowSpan;
		if (strtolower($this->Parent->Tag) == "td" && $ColSpan > 1)
			$attrs["colspan"] = $ColSpan;
		$name = $this->Parent->TableVar . "_" . $this->Name;
		if ($this->Name <> $this->Parent->GroupOptionName) {
			if (!in_array($this->Name, array('checkbox', 'rowcnt'))) {
				if ($this->Parent->UseImageAndText)
					$res = $this->GetImageAndText($res);
				if ($this->Parent->UseButtonGroup && $this->ShowInButtonGroup) {
					$res = $this->Parent->RenderButtonGroup($res);
					if ($this->OnLeft && strtolower($this->Parent->Tag) == "td" && $ColSpan > 1)
						$res = '<div style="text-align: right">' . $res . '</div>';
				}
			}
			if ($Part == "header")
				$res = "<span id=\"elh_" . $name . "\" class=\"" . $name . "\">" . $res . "</span>";
			else if ($Part == "body")
				$res = "<span id=\"el" . $this->Parent->RowCnt . "_" . $name . "\" class=\"" . $name . "\">" . $res . "</span>";
			else if ($Part == "footer")
				$res = "<span id=\"elf_" . $name . "\" class=\"" . $name . "\">" . $res . "</span>";
		}
		$tag = ($this->Parent->Tag == "td" && $Part == "header") ? "th" : $this->Parent->Tag;
		if ($this->Parent->UseButtonGroup && $this->ShowInButtonGroup)
			$attrs["style"] .= "white-space: nowrap;";
		$res = ewr_HtmlElement($tag, $attrs, $res);
		return $res;
	}

	// Get image and text link
	function GetImageAndText($body) {
		if (!preg_match_all('/<a([^>]*)>([\s\S]*?)<\/a\s*>/i', $body, $matches, PREG_SET_ORDER))
			return $body;
		foreach ($matches as $match) {
			if (preg_match('/\s+data-caption\s*=\s*[\'"]([\s\S]*?)[\'"]/i', $match[1], $submatches)) { // Match data-caption='caption'
				$caption = $submatches[1];
				if (preg_match('/<img([^>]*)>/i', $match[2])) // Image and text
					$body = str_replace($match[2], $match[2] . '&nbsp;&nbsp;' . $caption, $body);
			}
		}
		return $body;
	}

}

/**
 * Advanced Security class
 */
class crAdvancedSecurity {

	var $UserLevel = array(); // All User Levels
	var $UserLevelPriv = array(); // All User Level permissions
	var $UserLevelID = array(); // User Level ID array
	var $UserID = array(); // User ID array
	
	var $CurrentUserLevelID;
	var $CurrentUserLevel; // Permissions
	var $CurrentUserID;
	var $CurrentParentUserID;

	// Constructor
	function __construct() {

		// Init User Level
		$this->CurrentUserLevelID = $this->SessionUserLevelID();
		if (is_numeric($this->CurrentUserLevelID) && intval($this->CurrentUserLevelID) >= -1) {
			$this->UserLevelID[] = $this->CurrentUserLevelID;
		}

		// Init User ID
		$this->CurrentUserID = $this->SessionUserID();
		$this->CurrentParentUserID = $this->SessionParentUserID();

		// Load user level
		$this->LoadUserLevel();

	}

	// Session User ID
	function SessionUserID() {
		return strval(@$_SESSION[EWR_SESSION_USER_ID]);
	}
	function setSessionUserID($v) {
		$_SESSION[EWR_SESSION_USER_ID] = trim(strval($v));
		$this->CurrentUserID = trim(strval($v));
	}

	// Session Parent User ID
	function SessionParentUserID() {
		return strval(@$_SESSION[EWR_SESSION_PARENT_USER_ID]);
	}
	function setSessionParentUserID($v) {
		$_SESSION[EWR_SESSION_PARENT_USER_ID] = trim(strval($v));
		$this->CurrentParentUserID = trim(strval($v));
	}

	// Session User Level ID
	function SessionUserLevelID() {
		return @$_SESSION[EWR_SESSION_USER_LEVEL_ID];
	}
	function setSessionUserLevelID($v) {
		$_SESSION[EWR_SESSION_USER_LEVEL_ID] = $v;
		$this->CurrentUserLevelID = $v;
		if (is_numeric($v) && $v >= -1)
			$this->UserLevelID = array($v);
	}

	// Session User Level value
	function SessionUserLevel() {
		return @$_SESSION[EWR_SESSION_USER_LEVEL];
	}
	function setSessionUserLevel($v) {
		$_SESSION[EWR_SESSION_USER_LEVEL] = $v;
		$this->CurrentUserLevel = $v;
	}

	// Current user name
	function getCurrentUserName() {
		return strval(@$_SESSION[EWR_SESSION_USER_NAME]);
	}
	function setCurrentUserName($v) {
		$_SESSION[EWR_SESSION_USER_NAME] = $v;
	}
	function CurrentUserName() {
		return $this->getCurrentUserName();
	}

	// Current User ID
	function CurrentUserID() {
		return $this->CurrentUserID;
	}

	// Current Parent User ID
	function CurrentParentUserID() {
		return $this->CurrentParentUserID;
	}

	// Current User Level ID
	function CurrentUserLevelID() {
		return $this->CurrentUserLevelID;
	}

	// Current User Level value
	function CurrentUserLevel() {
		return $this->CurrentUserLevel;
	}

	// Can list
	function CanList() {
		return (($this->CurrentUserLevel & EWR_ALLOW_LIST) == EWR_ALLOW_LIST);
	}
	function setCanList($b) {
		if ($b) {
			$this->CurrentUserLevel = ($this->CurrentUserLevel | EWR_ALLOW_LIST);
		} else {
			$this->CurrentUserLevel = ($this->CurrentUserLevel & (~ EWR_ALLOW_LIST));
		}
	}

	// Can report
	function CanReport() {
		return (($this->CurrentUserLevel & EWR_ALLOW_REPORT) == EWR_ALLOW_REPORT);
	}
	function setCanReport($b) {
		if ($b) {
			$this->CurrentUserLevel = ($this->CurrentUserLevel | EWR_ALLOW_REPORT);
		} else {
			$this->CurrentUserLevel = ($this->CurrentUserLevel & (~ EWR_ALLOW_REPORT));
		}
	}

	// Can admin
	function CanAdmin() {
		return (($this->CurrentUserLevel & EWR_ALLOW_ADMIN) == EWR_ALLOW_ADMIN);
	}
	function setCanAdmin($b) {
		if ($b) {
			$this->CurrentUserLevel = ($this->CurrentUserLevel | EWR_ALLOW_ADMIN);
		} else {
			$this->CurrentUserLevel = ($this->CurrentUserLevel & (~ EWR_ALLOW_ADMIN));
		}
	}

	// Last URL
	function LastUrl() {
		return @$_COOKIE[EWR_PROJECT_VAR]['LastUrl'];
	}

	// Save last URL
	function SaveLastUrl() {
		$s = ewr_ServerVar("SCRIPT_NAME");
		$q = ewr_ServerVar("QUERY_STRING");
		if ($q <> "") $s .= "?" . $q;
		if ($this->LastUrl() == $s) $s = "";
		@setcookie(EWR_PROJECT_VAR . '[LastUrl]', $s);
	}

	// Auto login
	function AutoLogin() {
		if (@$_COOKIE[EWR_PROJECT_VAR]['AutoLogin'] == "autologin") {
			$usr = ewr_Decrypt(@$_COOKIE[EWR_PROJECT_VAR]['Username'], EWR_RANDOM_KEY);
			$pwd = ewr_Decrypt(@$_COOKIE[EWR_PROJECT_VAR]['Password'], EWR_RANDOM_KEY);
			$AutoLogin = $this->ValidateUser($usr, $pwd, TRUE);
		} else {
			$AutoLogin = FALSE;
		}
		return $AutoLogin;
	}

	// Validate user
	function ValidateUser($usr, $pwd, $autologin) {
		global $conn, $ReportLanguage;
		$ValidateUser = FALSE;
		$CustomValidateUser = FALSE;

	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Global","User_CustomValidate")) { ##-->
		// Call User Custom Validate event
		if (EWR_USE_CUSTOM_LOGIN) {
			$CustomValidateUser = $this->User_CustomValidate($usr, $pwd);
			if ($CustomValidateUser) {
				$_SESSION[EWR_SESSION_STATUS] = "login";
				$this->setCurrentUserName($usr); // Load user name
			}
		}
	<!--## } ##-->

	<!--## if (bHardCodeAdmin) { ##-->	
		// Check hard coded admin first
		if (!$ValidateUser) {

			if (EWR_CASE_SENSITIVE_PASSWORD) {
				$ValidateUser = (!$CustomValidateUser && EWR_ADMIN_USER_NAME == $usr && EWR_ADMIN_PASSWORD == $pwd) ||
								($CustomValidateUser && EWR_ADMIN_USER_NAME == $usr);
			} else {
				$ValidateUser = (!$CustomValidateUser && strtolower(EWR_ADMIN_USER_NAME) == strtolower($usr)
								&& strtolower(EWR_ADMIN_PASSWORD) == strtolower($pwd)) ||
								($CustomValidateUser && strtolower(EWR_ADMIN_USER_NAME) == strtolower($usr));
			}
			if ($ValidateUser) {
				$_SESSION[EWR_SESSION_STATUS] = "login";
				$_SESSION[EWR_SESSION_SYSTEM_ADMIN] = 1; // System Administrator
				$this->setCurrentUserName("Administrator"); // Load user name

		<!--## if (bUserID) { ##-->	
				$this->setSessionUserID(-1); // System Administrator
		<!--## } ##-->
		<!--## if (bUserLevel) { ##-->	
				$this->setSessionUserLevelID(-1); // System Administrator
				$this->SetUpUserLevel();
		<!--## } ##-->

			}

		}
	<!--## } ##-->

	<!--## if (bUserTable) { ##-->
		// Check other users
		if (!$ValidateUser) {

		<!--##
			FIELD = SECTABLE.Fields(PROJ.SecLoginIDFld);
			bUserNameIsNumeric = (ew_GetFieldType(FIELD.FldType) == 1);
			if (bUserNameIsNumeric) { // Numeric
		##-->
			if (!is_numeric($usr)) return $CustomValidateUser;
		<!--##
			}
		##-->

			$sFilter = str_replace("%u", ewr_AdjustSql($usr), EWR_USER_NAME_FILTER);

		<!--##
			if (PROJ.SecRegisterActivate && ew_IsNotEmpty(PROJ.SecRegisterActivateFld)) {
		##-->
			$sFilter .= " AND " . EWR_USER_ACTIVATE_FILTER;
		<!--##
			}
		##-->

			$sSql = EWR_LOGIN_SELECT_SQL . " WHERE " . $sFilter;

			if ($rs = $conn->Execute($sSql)) {
				if (!$rs->EOF) {
					$ValidateUser = $CustomValidateUser || ewr_ComparePassword($rs->fields('<!--##=ew_SQuote(PROJ.SecPasswdFld)##-->'), $pwd);

					if ($ValidateUser) {
						$_SESSION[EWR_SESSION_STATUS] = "login";
		<!--##
			FIELD = SECTABLE.Fields(PROJ.SecLoginIDFld);
			sFld = "$rs->fields('" + ew_SQuote(PROJ.SecLoginIDFld) + "')";
		##-->
						$_SESSION[EWR_SESSION_SYSTEM_ADMIN] = 0; // Non System Administrator
						$this->setCurrentUserName(<!--##=GetFldVal(sFld, FIELD.FldType)##-->); // Load user name
		<!--##
			if (bUserID) {
				FIELD = SECTABLE.Fields(DB.SecuUserIDFld);
				sFld = "$rs->fields('" + ew_SQuote(DB.SecuUserIDFld) + "')";
		##-->
						$this->setSessionUserID(<!--##=GetFldVal(sFld, FIELD.FldType)##-->); // Load User ID
		<!--##
			}
			if (ew_IsNotEmpty(DB.SecuParentUserIDFld)) { // Parent User ID
				FIELD = SECTABLE.Fields(DB.SecuParentUserIDFld);
				sFld = "$rs->fields('" + ew_SQuote(DB.SecuParentUserIDFld) + "')";
		##-->
						$this->setSessionParentUserID(<!--##=GetFldVal(sFld, FIELD.FldType)##-->); // Load parent User ID
		<!--##
			}
			if (bUserLevel) { // User Level
				FIELD = SECTABLE.Fields(DB.SecUserLevelFld);
				sFld = "$rs->fields('" + ew_SQuote(DB.SecUserLevelFld) + "')";
		##-->
						if (is_null(<!--##=sFld##-->)) {
							$this->setSessionUserLevelID(0);
						} else {
							$this->setSessionUserLevelID(intval(<!--##=GetFldVal(sFld, FIELD.FldType)##-->)); // Load User Level
						}
						$this->SetUpUserLevel();
		<!--##
			}
		##-->
		<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Global","User_Validated")) { ##-->
						// Call User Validated event
						$row = $rs->fields;
						$this->User_Validated($row);
		<!--## } ##-->
					}
				}
				$rs->Close();
			}
		}
	<!--## } ##-->

		if ($CustomValidateUser)
			return $CustomValidateUser;

		if (!$ValidateUser)
			$_SESSION[EWR_SESSION_STATUS] = ""; // Clear login status
		
		return $ValidateUser;
	}

	<!--## if (bStaticUserLevel || bDynamicUserLevel) { ##-->

	// Load user level from config file
	function LoadUserLevelFromConfigFile(&$arUserLevel, &$arUserLevelPriv, &$arTable, $userpriv = FALSE) {

		// User Level definitions
		array_splice($arUserLevel, 0);
		array_splice($arUserLevelPriv, 0);
		array_splice($arTable, 0);

		// Load user level from config files
		$doc = new crXMLDocument();
		$folder = ewr_AppRoot() . EWR_CONFIG_FILE_FOLDER;

		// Load user level settings from main config file
		$ProjectID = CurrentProjectID();
		$file = $folder . EWR_PATH_DELIMITER . $ProjectID . ".xml";
		if (file_exists($file) && $doc->Load($file) && (($projnode = $doc->SelectSingleNode("//configuration/project")) != NULL)) {
			$userlevel = $doc->GetAttribute($projnode, "userlevel");
			$usergroup = explode(";", $userlevel);
			foreach ($usergroup as $group) {
				@list($id, $name, $priv) = explode(",", $group, 3);
				// Remove quotes
				if (strlen($name) >= 2 && substr($name,0,1) == "\"" && substr($name,-1) == "\"")
					$name = substr($name,1,strlen($name)-2);
				$arUserLevel[] = array($id, $name);
			}

			// Load from main config file
			$this->LoadUserLevelFromXml($folder, $doc, $arUserLevelPriv, $arTable, $userpriv);

		}

		// Warn user if user level not setup
		if (count($arUserLevel) == 0) {
			die("Unable to load user level from config file: " . $file);
		}

		// Load user priv settings from all config files
		if ($dir_handle = opendir($folder)) {
			while (FALSE !== ($file = readdir($dir_handle))) {
				if ($file == "." || $file == ".." || !is_file($folder . EWR_PATH_DELIMITER . $file))
					continue;
				$pathinfo = pathinfo($file);
				if (isset($pathinfo["extension"]) && strtolower($pathinfo["extension"]) == "xml") {
					if ($file <> $ProjectID . ".xml")
						$this->LoadUserLevelFromXml($folder, $file, $arUserLevelPriv, $arTable, $userpriv);
				}
			}
		}

	}

	function LoadUserLevelFromXml($folder, $file, &$arUserLevelPriv, &$arTable, $userpriv) {

		if (is_string($file)) {
			$file = $folder . EWR_PATH_DELIMITER . $file;
			$doc = new crXMLDocument();
			$doc->Load($file);
		} else {
			$doc = $file;
		}
		if ($doc instanceof crXMLDocument) {

			// Load project id
			$projid = "";
			$projfile = "";
			if (($projnode = $doc->SelectSingleNode("//configuration/project")) != NULL) {
				$projid = $doc->GetAttribute($projnode, "id");
				$projfile = $doc->GetAttribute($projnode, "file");
			}

			// Load user priv
			$tablelist = $doc->SelectNodes("//configuration/project/table");
			foreach ($tablelist as $table) {
				$tablevar = $doc->GetAttribute($table, "id");
				$tablename = $doc->GetAttribute($table, "name");
				$tablecaption = $doc->GetAttribute($table, "caption");
				$userlevel = $doc->GetAttribute($table, "userlevel");
				$priv = $doc->GetAttribute($table, "priv");
				if (!$userpriv || ($userpriv && $priv == "1")) {
					$usergroup = explode(";", $userlevel);
					foreach($usergroup as $group) {
						@list($id, $name, $priv) = explode(",", $group, 3);
						$arUserLevelPriv[] = array($projid . $tablename, $id, $priv);
					}
					$arTable[] = array($tablename, $tablevar, $tablecaption, $priv, $projid, $projfile);
				}
			}

		}

	}

	<!--## } ##-->

	<!--## if (bStaticUserLevel) { ##-->

	// Static User Level security
	function SetUpUserLevel() {

		// Load user level from config file
		$arTable = array();
		$this->LoadUserLevelFromConfigFile($this->UserLevel, $this->UserLevelPriv, $arTable);

	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Global","UserLevel_Loaded")) { ##-->
		// User Level loaded event
		$this->UserLevel_Loaded();
	<!--## } ##-->

		// Save the User Level to Session variable
		$this->SaveUserLevel();

	}

	// Get all User Level settings from database
	function SetUpUserLevelEx() {
		return FALSE;
	}

	<!--## } else if (bDynamicUserLevel) { ##-->

	// Dynamic User Level security
	
	// Get User Level settings from database
	function SetUpUserLevel() {

		$this->SetUpUserLevelEx(); // Load all user levels
<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Global","UserLevel_Loaded")) { ##-->
		// User Level loaded event
		$this->UserLevel_Loaded();
<!--## } ##-->
		// Save the User Level to Session variable
		$this->SaveUserLevel();
	}

	// Get all User Level settings from database
	function SetUpUserLevelEx() {
		global $conn;
		global $ReportLanguage;
		global $Page;

		// Load user level from config file first
		$arTable = array();
		$arUserLevel = array();
		$arUserLevelPriv = array();
		$this->LoadUserLevelFromConfigFile($arUserLevel, $arUserLevelPriv, $arTable);

		// Get the User Level definitions
		$sSql = "SELECT " . EWR_USER_LEVEL_ID_FIELD . ", " . EWR_USER_LEVEL_NAME_FIELD . " FROM " . EWR_USER_LEVEL_TABLE;
		if ($rs = $conn->Execute($sSql)) {
			$this->UserLevel = $rs->GetRows();
			$rs->Close();
		}
		// Get the User Level privileges
		$sSql = "SELECT " . EWR_USER_LEVEL_PRIV_TABLE_NAME_FIELD . ", " . EWR_USER_LEVEL_PRIV_USER_LEVEL_ID_FIELD . ", " . EWR_USER_LEVEL_PRIV_PRIV_FIELD . " FROM " . EWR_USER_LEVEL_PRIV_TABLE;
		if ($rs = $conn->Execute($sSql)) {
			$this->UserLevelPriv = $rs->GetRows();
			$rs->Close();
		}

		// Increase table name field size if necessary
		if (EWR_IS_MYSQL) {
			try {
				if ($rs = $conn->Execute("SHOW COLUMNS FROM " . EWR_USER_LEVEL_PRIV_TABLE . " LIKE '" . ewr_AdjustSql(EWR_USER_LEVEL_PRIV_TABLE_NAME_FIELD_2) . "'")) {
					$type = $rs->fields("Type");
					$rs->Close();
					if (preg_match('/varchar\(([\d]+)\)/i', $type, $matches)) {
						$size = intval($matches[1]);
						if ($size < EWR_USER_LEVEL_PRIV_TABLE_NAME_FIELD_SIZE)
							$conn->Execute("ALTER TABLE " . EWR_USER_LEVEL_PRIV_TABLE . " MODIFY COLUMN " . EWR_USER_LEVEL_PRIV_TABLE_NAME_FIELD . " VARCHAR(" . EWR_USER_LEVEL_PRIV_TABLE_NAME_FIELD_SIZE . ")");
					}
				}
			} catch (Exception $e) {}
		}

		// Update User Level privileges record if necessary
		$bReloadUserPriv = 0;
		$ProjectID = CurrentProjectID();
		$Sql = "SELECT COUNT(*) FROM " . EWR_USER_LEVEL_PRIV_TABLE . " WHERE EXISTS(SELECT * FROM " .
			EWR_USER_LEVEL_PRIV_TABLE . " WHERE " . EWR_USER_LEVEL_PRIV_TABLE_NAME_FIELD . " LIKE '" .
			ewr_AdjustSql(EWR_TABLE_PREFIX_OLD) . "%')";
		if (ewr_ExecuteScalar($Sql) > 0) {
			$ar = array_map(create_function('$t', 'return "\'" . ewr_AdjustSql(EWR_TABLE_PREFIX_OLD . $t[0]) . "\'";'), $arTable);
			$Sql = "UPDATE " . EWR_USER_LEVEL_PRIV_TABLE . " SET " .
				EWR_USER_LEVEL_PRIV_TABLE_NAME_FIELD . " = REPLACE(" . EWR_USER_LEVEL_PRIV_TABLE_NAME_FIELD . "," .
				"'" . ewr_AdjustSql(EWR_TABLE_PREFIX_OLD) . "','" . ewr_AdjustSql($ProjectID) . "') WHERE " .
				EWR_USER_LEVEL_PRIV_TABLE_NAME_FIELD . " IN (" . implode(",", $ar) . ")";
			if ($conn->Execute($Sql))
				$bReloadUserPriv += $conn->Affected_Rows();
		}

		// Reload the User Level privileges
		if ($bReloadUserPriv) {
			$sSql = "SELECT " . EWR_USER_LEVEL_PRIV_TABLE_NAME_FIELD . ", " . EWR_USER_LEVEL_PRIV_USER_LEVEL_ID_FIELD . ", " . EWR_USER_LEVEL_PRIV_PRIV_FIELD . " FROM " . EWR_USER_LEVEL_PRIV_TABLE;
			if ($rs = $conn->Execute($sSql)) {
				$this->UserLevelPriv = $rs->GetRows();
				$rs->Close();
			}
		}

		// Warn user if user level not setup
		if (count($this->UserLevelPriv) == 0 && $this->IsAdmin() && $Page != NULL && @$_SESSION[EWR_SESSION_USER_LEVEL_MSG] == "") {
			$Page->setFailureMessage($ReportLanguage->Phrase("NoUserLevel"));
			$_SESSION[EWR_SESSION_USER_LEVEL_MSG] = "1"; // Show only once
		}

		return TRUE;

	}

	<!--## } else { ##-->

	// No User Level security
	function SetUpUserLevel() {}

	<!--## } ##-->

	// Add user permission
	function AddUserPermission($UserLevelName, $TableName, $UserPermission) {
		// Get User Level ID from user name
		$UserLevelID = "";
		if (is_array($this->UserLevel)) {
			foreach ($this->UserLevel as $row) {
				list($levelid, $name) = $row;
				if (strval($UserLevelName) == strval($name)) {
					$UserLevelID = $levelid;
					break;
				}
			}
		}
		if (is_array($this->UserLevelPriv) && $UserLevelID <> "") {
			$cnt = count($this->UserLevelPriv);
			for ($i = 0; $i < $cnt; $i++) {
				list($table, $levelid, $priv) = $this->UserLevelPriv[$i];
				if (strtolower($table) == strtolower(EWR_TABLE_PREFIX . $TableName) && strval($levelid) == strval($UserLevelID)) {
					$this->UserLevelPriv[$i][2] = $priv | $UserPermission; // Add permission
					break;
				}
			}
		}
	}

	// Delete user permission
	function DeleteUserPermission($UserLevelName, $TableName, $UserPermission) {
		// Get User Level ID from user name
		$UserLevelID = "";
		if (is_array($this->UserLevel)) {
			foreach ($this->UserLevel as $row) {
				list($levelid, $name) = $row;
				if (strval($UserLevelName) == strval($name)) {
					$UserLevelID = $levelid;
					break;
				}
			}
		}
		if (is_array($this->UserLevelPriv) && $UserLevelID <> "") {
			$cnt = count($this->UserLevelPriv);
			for ($i = 0; $i < $cnt; $i++) {
				list($table, $levelid, $priv) = $this->UserLevelPriv[$i];
				if (strtolower($table) == strtolower(EWR_TABLE_PREFIX . $TableName) && strval($levelid) == strval($UserLevelID)) {
					$this->UserLevelPriv[$i][2] = $priv & (127 - $UserPermission); // Remove permission
					break;
				}
			}
		}
	}
	
	// Load current User Level
	function LoadCurrentUserLevel($Table) {
		$this->LoadUserLevel();
		$this->setSessionUserLevel($this->CurrentUserLevelPriv($Table));
	}

	// Get current user privilege
	function CurrentUserLevelPriv($TableName) {
		if ($this->IsLoggedIn()) {
			$Priv= 0;
			foreach ($this->UserLevelID as $UserLevelID)
				$Priv |= $this->GetUserLevelPrivEx($TableName, $UserLevelID);
			return $Priv;
		} else {
			return 0;
		}
	}
	
	// Get User Level ID by User Level name
	function GetUserLevelID($UserLevelName) {
		if (strval($UserLevelName) == "Administrator") {
			return -1;
		} elseif ($UserLevelName <> "") {
			if (is_array($this->UserLevel)) {
				foreach ($this->UserLevel as $row) {
					list($levelid, $name) = $row;
					if (strval($name) == strval($UserLevelName))
						return $levelid;
				}
			}
		}
		return -2;
	}

	// Add User Level by name
	function AddUserLevel($UserLevelName) {
		if (strval($UserLevelName) == "") return;
		$UserLevelID = $this->GetUserLevelID($UserLevelName);
		$this->AddUserLevelID($UserLevelID);
	}

	// Add User Level by ID
	function AddUserLevelID($UserLevelID) {
		if (!is_numeric($UserLevelID)) return;
		if ($UserLevelID < -1) return;
		if (!in_array($UserLevelID, $this->UserLevelID))
			$this->UserLevelID[] = $UserLevelID;
	}

	// Delete User Level by name
	function DeleteUserLevel($UserLevelName) {
		if (strval($UserLevelName) == "") return;
		$UserLevelID = $this->GetUserLevelID($UserLevelName);
		$this->DeleteUserLevelID($UserLevelID);
	}

	// Delete User Level by ID
	function DeleteUserLevelID($UserLevelID) {
		if (!is_numeric($UserLevelID)) return;
		if ($UserLevelID < -1) return;
		$cnt = count($this->UserLevelID);
		for ($i = 0; $i < $cnt; $i++) {
			if ($this->UserLevelID[$i] == $UserLevelID) {
				unset($this->UserLevelID[$i]);
				break;
			}
		}
	}

	// User Level list
	function UserLevelList() {
		return implode(", ", $this->UserLevelID);
	}

	// User Level name list
	function UserLevelNameList() {
		$list = "";
		foreach ($this->UserLevelID as $UserLevelID) {
			if ($list <> "") $lList .= ", ";
			$list .= ewr_QuotedValue($this->GetUserLevelName($UserLevelID), EWR_DATATYPE_STRING);
		}
		return $list;
	}

	// Get user privilege based on table name and User Level
	function GetUserLevelPrivEx($TableName, $UserLevelID) {
		if (strval($UserLevelID) == "-1") { // System Administrator
			return 127; // Use new User Level values (separate View/Search)
		} elseif ($UserLevelID >= 0) {
			if (is_array($this->UserLevelPriv)) {
				foreach ($this->UserLevelPriv as $row) {
					list($table, $levelid, $priv) = $row;
					if (strtolower($table) == strtolower($TableName) && strval($levelid) == strval($UserLevelID)) {
						if (is_null($priv) || !is_numeric($priv)) return 0;
						return intval($priv);
					}
				}
			}
		}
		return 0;
	}

	// Get current User Level name
	function CurrentUserLevelName() {
		return $this->GetUserLevelName($this->CurrentUserLevelID());
	}

	// Get User Level name based on User Level
	function GetUserLevelName($UserLevelID) {
		if (strval($UserLevelID) == "-1") {
			return "Administrator";
		} elseif ($UserLevelID >= 0) {
			if (is_array($this->UserLevel)) {
				foreach ($this->UserLevel as $row) {
					list($levelid, $name) = $row;
					if (strval($levelid) == strval($UserLevelID))
						return $name;
				}
			}
		}
		return "";
	}

	// Display all the User Level settings (for debug only)
	function ShowUserLevelInfo() {
		echo "<pre>";
		print_r($this->UserLevel);
		print_r($this->UserLevelPriv);
		echo "</pre>";
		echo "<p>Current User Level ID = " . $this->CurrentUserLevelID() . "</p>";
		echo "<p>Current User Level ID List = " . $this->UserLevelList() . "</p>";
	}

	// Check privilege for List page (for menu items)
	function AllowList($TableName) {
		return ($this->CurrentUserLevelPriv($TableName) & EWR_ALLOW_LIST);
	}

	// Check if user is logged in
	function IsLoggedIn() {
		return (@$_SESSION[EWR_SESSION_STATUS] == "login");
	}

	// Check if user is system administrator
	function IsSysAdmin() {
		return (@$_SESSION[EWR_SESSION_SYSTEM_ADMIN] == 1);
	}

	// Check if user is administrator
	function IsAdmin() {
		$IsAdmin = $this->IsSysAdmin();
	<!--## if (bUserLevel) { ##-->
		if (!$IsAdmin)
			$IsAdmin = $this->CurrentUserLevelID == -1 || in_array(-1, $this->UserLevelID);
	<!--## } ##-->
	<!--## if (bUserID) { ##-->
		if (!$IsAdmin)
			$IsAdmin = $this->CurrentUserID == -1 || in_array(-1, $this->UserID);
	<!--## } ##-->
		return $IsAdmin;
	}

	// Save User Level to Session
	function SaveUserLevel() {
		$_SESSION[EWR_SESSION_AR_USER_LEVEL] = $this->UserLevel;
		$_SESSION[EWR_SESSION_AR_USER_LEVEL_PRIV] = $this->UserLevelPriv;
	}

	// Load User Level from Session
	function LoadUserLevel() {
		if (!is_array(@$_SESSION[EWR_SESSION_AR_USER_LEVEL]) || !is_array(@$_SESSION[EWR_SESSION_AR_USER_LEVEL_PRIV])) {
			$this->SetupUserLevel();
			$this->SaveUserLevel();
		} else {
			$this->UserLevel = $_SESSION[EWR_SESSION_AR_USER_LEVEL];
			$this->UserLevelPriv = $_SESSION[EWR_SESSION_AR_USER_LEVEL_PRIV];
		}
	}

	// Get current user info
	function CurrentUserInfo($fldname) {
		$info = NULL;
    <!--## if (bUserID) { ##-->
		$info = $this->GetUserInfo($fldname, $this->CurrentUserID);
	<!--## } else { ##-->
		if (defined("EWR_USER_TABLE") && !$this->IsSysAdmin()) {
			$user = $this->CurrentUserName();
			if (strval($user) <> "")
				return ewr_ExecuteScalar("SELECT " . ewr_QuotedName($fldname) . " FROM " . EWR_USER_TABLE . " WHERE " .
					str_replace("%u", ewr_AdjustSql($user), EWR_USER_NAME_FILTER));
		}
	<!--## } ##-->
		return $info;
	}

	<!--##
		if (bUserID) {
			// User id field
			FIELD = SECTABLE.Fields(DB.SecuUserIDFld);
			sFld = ew_FieldSqlName(FIELD);
			sFldName = FIELD.FldName;
			sFldQuoteS = FIELD.FldQuoteS;
			sFldQuoteE = FIELD.FldQuoteE;
			sFldDataType = GetFieldTypeName(FIELD.FldType);
			bFldIsNumeric = (sFldDataType == "EWR_DATATYPE_NUMBER");
			// User name field
			FIELD = SECTABLE.Fields(PROJ.SecLoginIDFld);
			sUserNameFld = ew_FieldSqlName(FIELD);
			sUserNameFldQuoteS = FIELD.FldQuoteS;
			sUserNameFldQuoteE = FIELD.FldQuoteE;
			sUserNameFldDataType = GetFieldTypeName(FIELD.FldType);
			if (bParentUserID) {
				FIELD = SECTABLE.Fields(DB.SecuParentUserIDFld);
				sParentFld = ew_FieldSqlName(FIELD);
				sParentFldName = FIELD.FldName;
			}
	##-->

	// Get user info
	function GetUserInfo($FieldName, $UserID) {
		global $conn;
		if (strval($UserID) <> "") {
			$sFilter = str_replace("%u", ewr_AdjustSql($UserID), EWR_USER_ID_FILTER);
			$sSql = EWR_LOGIN_SELECT_SQL . " WHERE " . $sFilter;
			if (($RsUser = $conn->Execute($sSql)) && !$RsUser->EOF) {
				$info = $RsUser->fields($FieldName);
				$RsUser->Close();
				return $info;
			}
		}
		return NULL;
  }

	// Get User ID by user name
	function GetUserIDByUserName($UserName) {
		global $conn;
		if (strval($UserName) <> "") {
			$sFilter = str_replace("%u", ewr_AdjustSql($UserName), EWR_USER_NAME_FILTER);
			$sSql = EWR_LOGIN_SELECT_SQL . " WHERE " . $sFilter;
			if (($RsUser = $conn->Execute($sSql)) && !$RsUser->EOF) {
				$UserID = $RsUser->fields('<!--##=ew_SQuote(sFldName)##-->');
				$RsUser->Close();
				return $UserID;
			}
		}
		return "";
	}

	// Load User ID
	function LoadUserID() {
		global $conn;
		$this->UserID = array();
		if (strval($this->CurrentUserID) == "") {
			// Add codes to handle empty user id here
		} elseif ($this->CurrentUserID <> "-1") {
			// Get first level
			$this->AddUserID($this->CurrentUserID);
			$sFilter = str_replace("%u", ewr_AdjustSql($this->CurrentUserID), EWR_USER_ID_FILTER);
	<!--## if (bParentUserID) { ##-->
			$sParentFilter = '<!--##=ew_SQuote(sParentFld)##--> IN (' . $this->UserIDList() . ')';
			$sFilter = "($sFilter) OR ($sParentFilter)";
	<!--## } ##-->
			$sSql = EWR_LOGIN_SELECT_SQL . " WHERE " . $sFilter;
			if ($RsUser = $conn->Execute($sSql)) {
				while (!$RsUser->EOF) {
					$this->AddUserID($RsUser->fields('<!--##=ew_SQuote(sFldName)##-->'));
					$RsUser->MoveNext();
				}
				$RsUser->Close();
			}

	<!--##
			if (bParentUserID) {
	##-->
			// Recurse all levels (hierarchical User ID)
			if (EWR_USER_ID_IS_HIERARCHICAL) {
				$sCurUserIDList = $this->UserIDList();
				$sUserIDList = "";
				while ($sUserIDList <> $sCurUserIDList) {
					$sFilter = '<!--##=ew_SQuote(sParentFld)##--> IN (' . $sCurUserIDList . ')';
					$sSql = EWR_LOGIN_SELECT_SQL . " WHERE " . $sFilter;
					if ($RsUser = $conn->Execute($sSql)) {
						while (!$RsUser->EOF) {
							$this->AddUserID($RsUser->fields('<!--##=ew_SQuote(sFldName)##-->'));
							$RsUser->MoveNext();
						}
						$RsUser->Close();
					}
					$sUserIDList = $sCurUserIDList;
					$sCurUserIDList = $this->UserIDList();
				}
			}
	<!--##
			}
	##-->

		}

	}

	// Add user name
	function AddUserName($UserName) {
		$this->AddUserID($this->GetUserIDByUserName($UserName));
	}

	// Add User ID
	function AddUserID($userid) {
		if (strval($userid) == "") return;
<!--## if (bFldIsNumeric) { ##-->
		if (!is_numeric($userid)) return;
<!--## } ##-->
		if (!in_array(trim(strval($userid)), $this->UserID))
			$this->UserID[] = trim(strval($userid));
	}

	// Delete user name
	function DeleteUserName($UserName) {
		$this->DeleteUserID($this->GetUserIDByUserName($UserName));
	}

	// Delete User ID
	function DeleteUserID($userid) {
		if (strval($userid) == "") return;
<!--## if (bFldIsNumeric) { ##-->
		if (!is_numeric($userid)) return;
<!--## } ##-->
		$cnt = count($this->UserID);
		for ($i = 0; $i < $cnt; $i++) {
			if ($this->UserID[$i] == trim(strval($userid))) {
				unset($this->UserID[$i]);
				break;
			}
		}
	}

	// User ID list
	function UserIDList() {
		$ar = $this->UserID;
		$len = count($ar);
		for ($i = 0; $i < $len; $i++)
			$ar[$i] =  ewr_QuotedValue($ar[$i], <!--##=sFldDataType##-->);
		return implode(", ", $ar);
	}
	
	<!--##
		if (bParentUserID) {
	##-->

	// Parent User ID list
	function ParentUserIDList($userid) {
		$result = "";

		// Own record
		if (trim(strval($userid)) == strval(CurrentUserID())) {
			if (strval(CurrentParentUserID()) <> "")
				$result = ewr_QuotedValue(CurrentParentUserID(), <!--##=sFldDataType##-->);
			return $result;
		}

		// One level only, must be CurrentUserID
		if (!EWR_USER_ID_IS_HIERARCHICAL) {

			return ewr_QuotedValue(CurrentUserID(), <!--##=sFldDataType##-->);

		} else { // Hierarchical, all users except userid

			$ar = $this->UserID;
			$len = count($ar);
			for ($i = 0; $i < $len; $i++) {
				if (strval($ar[$i]) <> trim(strval($userid))) {
					if ($result <> "")
						$result .= ", ";
					$result .= ewr_QuotedValue($ar[$i], <!--##=sFldDataType##-->);
				}
			}
			return $result;

		}

	}

	<!--##
		}
	##-->
	
	// List of allowed User IDs for this user
	function IsValidUserID($userid) {
		return in_array(trim(strval($userid)), $this->UserID);
	}

	<!--##
		}
	##-->

	<!--##~SYSTEMFUNCTIONS.GetServerScript("Global","UserID_Loading")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Global","UserID_Loaded")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Global","UserLevel_Loaded")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Global","TablePermission_Loading")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Global","TablePermission_Loaded")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Global","User_CustomValidate")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Global","User_Validated")##-->

}


/**
 * Functions for backward compatibilty
 */

// Get current user name
function CurrentUserName() {
	global $Security;
	return (isset($Security)) ? $Security->CurrentUserName() : strval(@$_SESSION[EWR_SESSION_USER_NAME]);
}

// Get current user ID
function CurrentUserID() {
	global $Security;
	return (isset($Security)) ? $Security->CurrentUserID() : strval(@$_SESSION[EWR_SESSION_USER_ID]);
}

// Get current parent user ID
function CurrentParentUserID() {
	global $Security;
	return (isset($Security)) ? $Security->CurrentParentUserID() : strval(@$_SESSION[EWR_SESSION_PARENT_USER_ID]);
}

// Get current user level
function CurrentUserLevel() {
	global $Security;
	return (isset($Security)) ? $Security->CurrentUserLevelID() : @$_SESSION[EWR_SESSION_USER_LEVEL_ID];
}

// Get current user level list
function CurrentUserLevelList() {
	global $Security;
	return (isset($Security)) ? $Security->UserLevelList() : strval(@$_SESSION[EWR_SESSION_USER_LEVEL_ID]);
}

// Get Current user info
function CurrentUserInfo($fldname) {
	global $Security;
	if (isset($Security)) {
		return $Security->CurrentUserInfo($fldname);
	} elseif (defined("EWR_USER_TABLE") && !IsSysAdmin()) {
		$user = CurrentUserName();
		if (strval($user) <> "")
			return ewr_ExecuteScalar("SELECT " . ew_QuotedName($fldname) . " FROM " . EWR_USER_TABLE . " WHERE " .
				str_replace("%u", ew_AdjustSql($user), EWR_USER_NAME_FILTER));
	}
	return NULL;
}

// Is logged in
function IsLoggedIn() {
	global $Security;
	return (isset($Security)) ? $Security->IsLoggedIn() : (@$_SESSION[EWR_SESSION_STATUS] == "login");
}

// Check if user is system administrator
function IsSysAdmin() {
	return (@$_SESSION[EWR_SESSION_SYSTEM_ADMIN] == 1);
}

// Get current page ID
function CurrentPageID() {
	if (isset($GLOBALS["Page"])) {
		return $GLOBALS["Page"]->PageID;
	} elseif (defined("EWR_PAGE_ID")) {
		return EWR_PAGE_ID;
	}
	return "";
}

// Allow list
function AllowList($TableName) {
	global $Security;
	return $Security->AllowList($TableName);
}

// Get user IP
function ewr_CurrentUserIP() {
	return ewr_ServerVar("REMOTE_ADDR");
}

// Load recordset
function &ewr_LoadRecordset($SQL) {
	global $conn;
	$conn->raiseErrorFn = $GLOBALS["EWR_ERROR_FN"];
	$rs = $conn->Execute($SQL);
	$conn->raiseErrorFn = '';
	return $rs;
}

// Execute UPDATE, INSERT, or DELETE statements
function ewr_Execute($SQL) {
	global $conn;
	$conn->raiseErrorFn = $GLOBALS["EWR_ERROR_FN"];
	$res = $conn->Execute($SQL);
	$conn->raiseErrorFn = '';
	return $res;
}

// Executes the query, and returns the first column of the first row
function ewr_ExecuteScalar($SQL) {
	$res = FALSE;
	$rs = ewr_LoadRecordset($SQL);
	if ($rs && !$rs->EOF && $rs->FieldCount() > 0) {
		$res = $rs->fields[0];
		$rs->Close();
	}
	return $res;
}

// Executes the query, and returns the first row
function ewr_ExecuteRow($SQL) {
	$res = FALSE;
	$rs = ewr_LoadRecordset($SQL);
	if ($rs && !$rs->EOF) {
		$res = $rs->fields;
		$rs->Close();
	}
	return $res;
}

// Check if valid operator
function ewr_IsValidOpr($Opr, $FldType) {
	$valid = ($Opr == "=" || $Opr == "<" || $Opr == "<=" ||
		$Opr == ">" || $Opr == ">=" || $Opr == "<>");
	if ($FldType == EWR_DATATYPE_STRING || $FldType == EWR_DATATYPE_MEMO)
		$valid = ($valid || $Opr == "LIKE" || $Opr == "NOT LIKE" || $Opr == "STARTS WITH" || $Opr == "ENDS WITH");
	return $valid;
}

// Quote table/field name
function ewr_QuotedName($Name) {
	$Name = str_replace(EWR_DB_QUOTE_END, EWR_DB_QUOTE_END . EWR_DB_QUOTE_END, $Name);
	return EWR_DB_QUOTE_START . $Name . EWR_DB_QUOTE_END;
}

// Quote field values
function ewr_QuotedValue($Value, $FldType) {
	if (is_null($Value))
		return "NULL";
	switch ($FldType) {
	case EWR_DATATYPE_STRING:
	case EWR_DATATYPE_BLOB:
	case EWR_DATATYPE_MEMO:
	case EWR_DATATYPE_TIME:
			return "'" . ewr_AdjustSql($Value) . "'";
	case EWR_DATATYPE_DATE:
		return (EWR_IS_MSACCESS) ? "#" . ewr_AdjustSql($Value) . "#" :
			"'" . ewr_AdjustSql($Value) . "'";
//	case EWR_DATATYPE_GUID:
//		if (EWR_IS_MSACCESS) {
//			if (strlen($Value) == 38) {
//				return "{guid " . $Value . "}";
//			} elseif (strlen($Value) == 36) {
//				return "{guid {" . $Value . "}}";
//			}
//		} else {
//		  return "'" . $Value . "'";
//		}
	case EWR_DATATYPE_BOOLEAN: // ENUM('Y'/'N') Or ENUM('1'/'0')
		//return "'" . $Value . "'";
		return (EWR_IS_MSACCESS) ? $Value : "'" . ewr_AdjustSql($Value) . "'";
	default:
		return $Value;
	}
}

// Get distinct values
function ewr_GetDistinctValues($FldOpr, $sql, $dlm = "") {
	global $conn;
	$ar = array();
	if (strval($sql) == "")
		return;
	$wrkrs = $conn->Execute($sql);
	if ($wrkrs) {
		while (!$wrkrs->EOF) {
			$wrkval = ewr_ConvertValue($FldOpr, $wrkrs->fields[0]);
			if ($dlm <> "") {
				$arval = explode($dlm, $wrkval);
			} else {
				$arval = array($wrkval);
			}
			$cntar = count($arval);
			for ($i = 0; $i < $cntar; $i++) {
				$val = $arval[$i];
				if (!in_array($val,$ar))
					$ar[] = $val;
			}
			$wrkrs->MoveNext();
		}
	}
	if ($wrkrs) $wrkrs->Close();
	return $ar;
}

// Convert value
function ewr_ConvertValue($FldOpr, $val) {
	if (is_null($val)) {
		return EWR_NULL_VALUE;
	} elseif ($val == "") {
		return EWR_EMPTY_VALUE;
	}
	if (is_float($val))
		$val = (float)$val;
	if ($FldOpr == "")
		return $val;
	if ($ar = explode(" ", $val)) {
		$ar = explode("-", $ar[0]);
	} else {
		return $val;
	}
	if (!$ar || count($ar) <> 3)
		return $val;
	list($year, $month, $day) = $ar;
	switch (strtolower($FldOpr)) {
	case "year":
		return $year;
	case "quarter":
		return "$year|" . ceil(intval($month)/3);
	case "month":
		return "$year|$month";
	case "day":
		return "$year|$month|$day";
	case "date":
		return "$year-$month-$day";
	}
}

// Dropdown display values
function ewr_DropDownDisplayValue($v, $t, $fmt) {
	global $ReportLanguage;
	if ($v == EWR_NULL_VALUE) {
		return $ReportLanguage->Phrase("NullLabel");
	} elseif ($v == EWR_EMPTY_VALUE) {
		return $ReportLanguage->Phrase("EmptyLabel");
	} elseif (strtolower($t) == "boolean") {
		return ewr_BooleanName($v);
	}
	if ($t == "")
		return $v;
	$ar = explode("|", strval($v));
	switch (strtolower($t)) {
	case "year":
		return $v;
	case "quarter":
		if (count($ar) >= 2)
			return ewr_QuarterName($ar[1]) . " " . $ar[0];
	case "month":
		if (count($ar) >= 2)
			return ewr_MonthName($ar[1]) . " " . $ar[0];
	case "day":
		if (count($ar) >= 3)
			return ewr_FormatDateTime($ar[0] . "-" . $ar[1] . "-" . $ar[2], $fmt);
	case "date":
		return ewr_FormatDateTime($v, $fmt);
	}
}

// Get Boolean Name
// - Treat "T" / "True" / "Y" / "Yes" / "1" As True
function ewr_BooleanName($v) {
	global $ReportLanguage;
	if (is_null($v))
		return $ReportLanguage->Phrase("NullLabel");
	elseif (strtoupper($v) == "T" || strtoupper($v) == "TRUE" || strtoupper($v) == "Y" || strtoupper($v) == "YES" Or strval($v) == "1")
		return $ReportLanguage->Phrase("BooleanYes");
	else
		return $ReportLanguage->Phrase("BooleanNo");
}

// Quarter name
function ewr_QuarterName($q) {
	global $ReportLanguage;
	switch ($q) {
	case 1:
		return $ReportLanguage->Phrase("Qtr1");
	case 2:
		return $ReportLanguage->Phrase("Qtr2");
	case 3:
		return $ReportLanguage->Phrase("Qtr3");
	case 4:
		return $ReportLanguage->Phrase("Qtr4");
	default:
		return $q;
	}
}

// Month name
function ewr_MonthName($m) {
	global $ReportLanguage;
	switch ($m) {
	case 1:
		return $ReportLanguage->Phrase("MonthJan");
	case 2:
		return $ReportLanguage->Phrase("MonthFeb");
	case 3:
		return $ReportLanguage->Phrase("MonthMar");
	case 4:
		return $ReportLanguage->Phrase("MonthApr");
	case 5:
		return $ReportLanguage->Phrase("MonthMay");
	case 6:
		return $ReportLanguage->Phrase("MonthJun");
	case 7:
		return $ReportLanguage->Phrase("MonthJul");
	case 8:
		return $ReportLanguage->Phrase("MonthAug");
	case 9:
		return $ReportLanguage->Phrase("MonthSep");
	case 10:
		return $ReportLanguage->Phrase("MonthOct");
	case 11:
		return $ReportLanguage->Phrase("MonthNov");
	case 12:
		return $ReportLanguage->Phrase("MonthDec");
	default:
		return $m;
	}
}

// Get group count for custom template
function ewr_GrpCnt($ar, $key = array()) {
	if (is_array($ar) && is_array($key)) {
		$lvl = count($key);
		$cnt = 0;
		if ($lvl > 1) { // Get next level
			$wrkkey = array_shift($key);
			$wrkar = @$ar[$wrkkey];
			$cnt += ewr_GrpCnt($wrkar, $key);
		} else {
			$wrkar = ($lvl == 0) ? $ar : @$ar[$key[0]];
			if (is_array($wrkar)) { // Accumulate all values
				$grp = count($wrkar);
				for ($i = 1; $i < $grp; $i++)
					$cnt += ewr_GrpCnt($wrkar, array($i));
			} else {
				$cnt = $wrkar;
			}
		}
		return $cnt;
	} else {
		return 0;
	}
}

// Join array
function ewr_JoinArray($ar, $sep, $ft, $pos=0) {
	if (!is_array($ar))
		return "";
	$arwrk = array_slice($ar, $pos); // Return array from position pos
	$cntar = count($arwrk);
	for ($i = 0; $i < $cntar; $i++)
		$arwrk[$i] = ewr_QuotedValue($arwrk[$i], $ft);
	return implode($sep, $arwrk);
}

// Unformat date time based on format type
function ewr_UnFormatDateTime($dt, $namedformat) {
	$dt = trim($dt);
	while (strpos($dt, "  ") !== FALSE) $dt = str_replace("  ", " ", $dt);
	$arDateTime = explode(" ", $dt);
	if (count($arDateTime) == 0) return $dt;
	$arDatePt = explode(EWR_DATE_SEPARATOR, $arDateTime[0]);
	if ($namedformat == 0 || $namedformat == 1 || $namedformat == 2 || $namedformat == 8) {
		$arDefFmt = explode(EWR_DATE_SEPARATOR, EWR_DEFAULT_DATE_FORMAT);
		if ($arDefFmt[0] == "yyyy") {
			$namedformat = 9;
		} elseif ($arDefFmt[0] == "mm") {
			$namedformat = 10;
		} elseif ($arDefFmt[0] == "dd") {
			$namedformat = 11;
		}
	}
	if (count($arDatePt) == 3) {
		switch ($namedformat) {
		case 5:
		case 9: //yyyymmdd
			if (ewr_CheckDate($arDateTime[0])) {
				list($year, $month, $day) = $arDatePt;
				break;
			} else {
				return $dt;
			}
		case 6:
		case 10: //mmddyyyy
			if (ewr_CheckUSDate($arDateTime[0])) {
				list($month, $day, $year) = $arDatePt;
				break;
			} else {
				return $dt;
			}
		case 7:
		case 11: //ddmmyyyy
			if (ewr_CheckEuroDate($arDateTime[0])) {
				list($day, $month, $year) = $arDatePt;
				break;
			} else {
				return $dt;
			}
		case 12:
		case 15: //yymmdd
			if (ewr_CheckShortDate($arDateTime[0])) {
				list($year, $month, $day) = $arDatePt;
				$year = ewr_UnformatYear($year);
				break;
			} else {
				return $dt;
			}
		case 13:
		case 16: //mmddyy
			if (ewr_CheckShortUSDate($arDateTime[0])) {
				list($month, $day, $year) = $arDatePt;
				$year = ewr_UnformatYear($year);
				break;
			} else {
				return $dt;
			}
		case 14:
		case 17: //ddmmyy
			if (ewr_CheckShortEuroDate($arDateTime[0])) {
				list($day, $month, $year) = $arDatePt;
				$year = ewr_UnformatYear($year);
				break;
			} else {
				return $dt;
			}
		default:
			return $dt;
		}
		if (strlen($year) <= 4 && strlen($month) <= 2 && strlen($day) <= 2) {
			return $year . "-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-" .
				 str_pad($day, 2, "0", STR_PAD_LEFT) .
				((count($arDateTime) > 1) ? " " . $arDateTime[1] : "");
		} else {
			return $dt;
		}
	} else {
		return $dt;
	}
}

// ViewValue
// - return &nbsp; if empty
function ewr_ViewValue($value) {
	if ($value <> "")
		return $value;
	else
		return "&nbsp;";
}

// Get current year
function ewr_CurrentYear() {
	return intval(date('Y'));
}

// Get current quarter
function ewr_CurrentQuarter() {
	return ceil(intval(date('n'))/3);
}

// Get current month
function ewr_CurrentMonth() {
	return intval(date('n'));
}

// Get current day
function ewr_CurrentDay() {
	return intval(date('j'));
}

// FormatDateTime
// Format a timestamp, datetime, date or time field from MySQL
// $namedformat:
// 0 - General Date
// 1 - Long Date
// 2 - Short Date (Default)
// 3 - Long Time
// 4 - Short Time (hh:mm:ss)
// 5 - Short Date (yyyy/mm/dd)
// 6 - Short Date (mm/dd/yyyy)
// 7 - Short Date (dd/mm/yyyy)
// 8 - Short Date (Default) + Short Time (if not 00:00:00)
// 9 - Short Date (yyyy/mm/dd) + Short Time (hh:mm:ss)
// 10 - Short Date (mm/dd/yyyy) + Short Time (hh:mm:ss)
// 11 - Short Date (dd/mm/yyyy) + Short Time (hh:mm:ss)
// 12 - Short Date - 2 digit year (yy/mm/dd)
// 13 - Short Date - 2 digit year (mm/dd/yy)
// 14 - Short Date - 2 digit year (dd/mm/yy)
// 15 - Short Date - 2 digit year (yy/mm/dd) + Short Time (hh:mm:ss)
// 16 - Short Date (mm/dd/yyyy) + Short Time (hh:mm:ss)
// 17 - Short Date (dd/mm/yyyy) + Short Time (hh:mm:ss)
function ewr_FormatDateTime($ts, $namedformat) {
	$DefDateFormat = str_replace("yyyy", "%Y", EWR_DEFAULT_DATE_FORMAT);
	$DefDateFormat = str_replace("mm", "%m", $DefDateFormat);
	$DefDateFormat = str_replace("dd", "%d", $DefDateFormat);
	if (is_numeric($ts)) // TimeStamp
	{
		switch (strlen($ts)) {
			case 14:
				$patt = '/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/';
				break;
			case 12:
				$patt = '/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/';
				break;
			case 10:
				$patt = '/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/';
				break;
			case 8:
				$patt = '/(\d{4})(\d{2})(\d{2})/';
				break;
			case 6:
				$patt = '/(\d{2})(\d{2})(\d{2})/';
				break;
			case 4:
				$patt = '/(\d{2})(\d{2})/';
				break;
			case 2:
				$patt = '/(\d{2})/';
				break;
			default:
				return $ts;
		}
		if ((isset($patt))&&(preg_match($patt, $ts, $matches)))
		{
			$year = $matches[1];
			$month = @$matches[2];
			$day = @$matches[3];
			$hour = @$matches[4];
			$min = @$matches[5];
			$sec = @$matches[6];
		}
		if (($namedformat==0)&&(strlen($ts)<10)) $namedformat = 2;
	}
	elseif (is_string($ts))
	{
		if (preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $ts, $matches)) // DateTime
		{
			$year = $matches[1];
			$month = $matches[2];
			$day = $matches[3];
			$hour = $matches[4];
			$min = $matches[5];
			$sec = $matches[6];
		}
		elseif (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $ts, $matches)) // Date
		{
			$year = $matches[1];
			$month = $matches[2];
			$day = $matches[3];
			if ($namedformat==0) $namedformat = 2;
		}
		elseif (preg_match('/(^|\s)(\d{2}):(\d{2}):(\d{2})/', $ts, $matches)) // Time
		{
			$hour = $matches[2];
			$min = $matches[3];
			$sec = $matches[4];
			if (($namedformat==0)||($namedformat==1)) $namedformat = 3;
			if ($namedformat==2) $namedformat = 4;
		}
		else
		{
			return $ts;
		}
	}
	else
	{
		return $ts;
	}
	if (!isset($year)) $year = 0; // Dummy value for times
	if (!isset($month)) $month = 1;
	if (!isset($day)) $day = 1;
	if (!isset($hour)) $hour = 0;
	if (!isset($min)) $min = 0;
	if (!isset($sec)) $sec = 0;
	$uts = @mktime($hour, $min, $sec, $month, $day, $year);
	if ($uts < 0 || $uts == FALSE || // Failed to convert
		(intval($year) == 0 && intval($month) == 0 && intval($day) == 0)) {
		$year = substr_replace("0000", $year, -1 * strlen($year));
		$month = substr_replace("00", $month, -1 * strlen($month));
		$day = substr_replace("00", $day, -1 * strlen($day));
		$hour = substr_replace("00", $hour, -1 * strlen($hour));
		$min = substr_replace("00", $min, -1 * strlen($min));
		$sec = substr_replace("00", $sec, -1 * strlen($sec));
		$DefDateFormat = str_replace("yyyy", $year, EWR_DEFAULT_DATE_FORMAT);
		$DefDateFormat = str_replace("mm", $month, $DefDateFormat);
		$DefDateFormat = str_replace("dd", $day, $DefDateFormat);
		switch ($namedformat) {
			case 0:
				return $DefDateFormat." $hour:$min:$sec";
				break;
			case 1://unsupported, return general date
				return $DefDateFormat." $hour:$min:$sec";
				break;
			case 2:
				return $DefDateFormat;
				break;
			case 3:
				if (intval($hour)==0)
					return "12:$min:$sec AM";
				elseif (intval($hour)>0 && intval($hour)<12)
					return "$hour:$min:$sec AM";
				elseif (intval($hour)==12)
					return "$hour:$min:$sec PM";
				elseif (intval($hour)>12 && intval($hour)<=23)
					return (intval($hour)-12).":$min:$sec PM";
				else
					return "$hour:$min:$sec";
				break;
			case 4:
				return "$hour:$min:$sec";
				break;
			case 5:
				return "$year". EWR_DATE_SEPARATOR . "$month" . EWR_DATE_SEPARATOR . "$day";
				break;
			case 6:
				return "$month". EWR_DATE_SEPARATOR ."$day" . EWR_DATE_SEPARATOR . "$year";
				break;
			case 7:
				return "$day" . EWR_DATE_SEPARATOR ."$month" . EWR_DATE_SEPARATOR . "$year";
				break;
			case 8:
				return $DefDateFormat . (($hour == 0 && $min == 0 && $sec == 0) ? "" : " $hour:$min:$sec");
				break;
			case 9:
				return "$year". EWR_DATE_SEPARATOR . "$month" . EWR_DATE_SEPARATOR . "$day $hour:$min:$sec";
				break;
			case 10:
				return "$month". EWR_DATE_SEPARATOR ."$day" . EWR_DATE_SEPARATOR . "$year $hour:$min:$sec";
				break;
			case 11:
				return "$day" . EWR_DATE_SEPARATOR ."$month" . EWR_DATE_SEPARATOR . "$year $hour:$min:$sec";
				break;
			case 12:
				return substr($year,-2) . EWR_DATE_SEPARATOR . $month . EWR_DATE_SEPARATOR . $day;
				break;
			case 13:
				return substr($year,-2) . EWR_DATE_SEPARATOR . $month . EWR_DATE_SEPARATOR . $day;
				break;
			case 14:
				return substr($year,-2) . EWR_DATE_SEPARATOR . $month . EWR_DATE_SEPARATOR . $day;
				break;
			default:
				return $ts;
		}
	} else {
		switch ($namedformat) {
			case 0:
				return strftime($DefDateFormat." %H:%M:%S", $uts);
				break;
			case 1:
				return strftime("%A, %B %d, %Y", $uts);
				break;
			case 2:
				return strftime($DefDateFormat, $uts);
				break;
			case 3:
				return strftime("%I:%M:%S %p", $uts);
				break;
			case 4:
				return strftime("%H:%M:%S", $uts);
				break;
			case 5:
				return strftime("%Y" . EWR_DATE_SEPARATOR . "%m" . EWR_DATE_SEPARATOR . "%d", $uts);
				break;
			case 6:
				return strftime("%m" . EWR_DATE_SEPARATOR . "%d" . EWR_DATE_SEPARATOR . "%Y", $uts);
				break;
			case 7:
				return strftime("%d" . EWR_DATE_SEPARATOR . "%m" . EWR_DATE_SEPARATOR . "%Y", $uts);
				break;
			case 8:
				return strftime($DefDateFormat . (($hour == 0 && $min == 0 && $sec == 0) ? "" : " %H:%M:%S"), $uts);
				break;
			case 9:
				return strftime("%Y" . EWR_DATE_SEPARATOR . "%m" . EWR_DATE_SEPARATOR . "%d %H:%M:%S", $uts);
				break;
			case 10:
				return strftime("%m" . EWR_DATE_SEPARATOR . "%d" . EWR_DATE_SEPARATOR . "%Y %H:%M:%S", $uts);
				break;
			case 11:
				return strftime("%d" . EWR_DATE_SEPARATOR . "%m" . EWR_DATE_SEPARATOR . "%Y %H:%M:%S", $uts);
				break;
			case 12:
				return strftime("%y" . EWR_DATE_SEPARATOR . "%m" . EWR_DATE_SEPARATOR . "%d", $uts);
				break;
			case 13:
				return strftime("%m" . EWR_DATE_SEPARATOR . "%d" . EWR_DATE_SEPARATOR . "%y", $uts);
				break;
			case 14:
				return strftime("%d" . EWR_DATE_SEPARATOR . "%m" . EWR_DATE_SEPARATOR . "%y", $uts);
				break;
			case 15:
				return strftime("%y" . EWR_DATE_SEPARATOR . "%m" . EWR_DATE_SEPARATOR . "%d %H:%M:%S", $uts);
				break;
			case 16:
				return strftime("%m" . EWR_DATE_SEPARATOR . "%d" . EWR_DATE_SEPARATOR . "%y %H:%M:%S", $uts);
				break;
			case 17:
				return strftime("%d" . EWR_DATE_SEPARATOR . "%m" . EWR_DATE_SEPARATOR . "%y %H:%M:%S", $uts);
				break;
			default:
				return $ts;
		}
	}
}

// FormatCurrency
// FormatCurrency(Expression[,NumDigitsAfterDecimal [,IncludeLeadingDigit
//  [,UseParensForNegativeNumbers [,GroupDigits]]]])
// NumDigitsAfterDecimal is the numeric value indicating how many places to the right of the decimal are displayed
// -1 Use Default
// The IncludeLeadingDigit, UseParensForNegativeNumbers, and GroupDigits arguments have the following settings:
// -1 True
// 0 False
// -2 Use Default
function ewr_FormatCurrency($amount, $NumDigitsAfterDecimal = EWR_DEFAULT_DECIMAL_PRECISION, $IncludeLeadingDigit = -2, $UseParensForNegativeNumbers = -2, $GroupDigits = -2) {

	if (!is_numeric($amount))
		return $amount;

	extract($GLOBALS["EWR_DEFAULT_LOCALE"]);

	// Check $NumDigitsAfterDecimal
	if ($NumDigitsAfterDecimal > -1)
		$frac_digits = $NumDigitsAfterDecimal;

	// Check $UseParensForNegativeNumbers
	if ($UseParensForNegativeNumbers == -1) {
		$n_sign_posn = 0;
		if ($p_sign_posn == 0) {
			$p_sign_posn = 3;
		}
	} elseif ($UseParensForNegativeNumbers == 0) {
		if ($n_sign_posn == 0)
			$n_sign_posn = 3;
	}

	// Check $GroupDigits
	if ($GroupDigits == -1) {
	} elseif ($GroupDigits == 0) {
		$mon_thousands_sep = "";
	}

	// Start by formatting the unsigned number
	$number = number_format(abs($amount),
							$frac_digits,
							$mon_decimal_point,
							$mon_thousands_sep);

	// Check $IncludeLeadingDigit
	if ($IncludeLeadingDigit == 0) {
		if (substr($number, 0, 2) == "0.")
			$number = substr($number, 1, strlen($number)-1);
	}
	if ($amount < 0) {
		$sign = $negative_sign;

		// "extracts" the boolean value as an integer
		$n_cs_precedes  = intval($n_cs_precedes  == true);
		$n_sep_by_space = intval($n_sep_by_space == true);
		$key = $n_cs_precedes . $n_sep_by_space . $n_sign_posn;
	} else {
		$sign = $positive_sign;
		$p_cs_precedes  = intval($p_cs_precedes  == true);
		$p_sep_by_space = intval($p_sep_by_space == true);
		$key = $p_cs_precedes . $p_sep_by_space . $p_sign_posn;
	}
	$formats = array(

	  // Currency symbol is after amount

	  // No space between amount and sign
	  '000' => '(%s' . $currency_symbol . ')',
	  '001' => $sign . '%s ' . $currency_symbol,
	  '002' => '%s' . $currency_symbol . $sign,
	  '003' => '%s' . $sign . $currency_symbol,
	  '004' => '%s' . $sign . $currency_symbol,

	  // One space between amount and sign
	  '010' => '(%s ' . $currency_symbol . ')',
	  '011' => $sign . '%s ' . $currency_symbol,
	  '012' => '%s ' . $currency_symbol . $sign,
	  '013' => '%s ' . $sign . $currency_symbol,
	  '014' => '%s ' . $sign . $currency_symbol,

	  // Currency symbol is before amount

	  // No space between amount and sign
	  '100' => '(' . $currency_symbol . '%s)',
	  '101' => $sign . $currency_symbol . '%s',
	  '102' => $currency_symbol . '%s' . $sign,
	  '103' => $sign . $currency_symbol . '%s',
	  '104' => $currency_symbol . $sign . '%s',

	  // One space between amount and sign
	  '110' => '(' . $currency_symbol . ' %s)',
	  '111' => $sign . $currency_symbol . ' %s',
	  '112' => $currency_symbol . ' %s' . $sign,
	  '113' => $sign . $currency_symbol . ' %s',
	  '114' => $currency_symbol . ' ' . $sign . '%s');

  // Lookup the key in the above array
	return sprintf($formats[$key], $number);
}

// FormatNumber
// FormatNumber(Expression[,NumDigitsAfterDecimal [,IncludeLeadingDigit
// 	[,UseParensForNegativeNumbers [,GroupDigits]]]])
// NumDigitsAfterDecimal is the numeric value indicating how many places to the right of the decimal are displayed
// -1 Use Default
// The IncludeLeadingDigit, UseParensForNegativeNumbers, and GroupDigits arguments have the following settings:
// -1 True
// 0 False
// -2 Use Default
function ewr_FormatNumber($amount, $NumDigitsAfterDecimal = EWR_DEFAULT_DECIMAL_PRECISION, $IncludeLeadingDigit = -2, $UseParensForNegativeNumbers = -2, $GroupDigits = -2) {

	if (!is_numeric($amount))
		return $amount;

	extract($GLOBALS["EWR_DEFAULT_LOCALE"]);

	// Check $NumDigitsAfterDecimal
	if ($NumDigitsAfterDecimal > -1)
		$frac_digits = $NumDigitsAfterDecimal;

	// Check $UseParensForNegativeNumbers
	if ($UseParensForNegativeNumbers == -1) {
		$n_sign_posn = 0;
		if ($p_sign_posn == 0) {
			$p_sign_posn = 3;
		}
	} elseif ($UseParensForNegativeNumbers == 0) {
		if ($n_sign_posn == 0)
			$n_sign_posn = 3;
	}

	// Check $GroupDigits
	if ($GroupDigits == -1) {
	} elseif ($GroupDigits == 0) {
		$mon_thousands_sep = "";
	}

	// Start by formatting the unsigned number
	$number = number_format(abs($amount),
						  $frac_digits,
						  $mon_decimal_point,
						  $mon_thousands_sep);

	// Check $IncludeLeadingDigit
	if ($IncludeLeadingDigit == 0) {
		if (substr($number, 0, 2) == "0.")
			$number = substr($number, 1, strlen($number)-1);
	}
	if ($amount < 0) {
		$sign = $negative_sign;
		$key = $n_sign_posn;
	} else {
		$sign = $positive_sign;
		$key = $p_sign_posn;
	}
	$formats = array(
		'0' => '(%s)',
		'1' => $sign . '%s',
		'2' => $sign . '%s',
		'3' => $sign . '%s',
		'4' => $sign . '%s');

	// Lookup the key in the above array
	return sprintf($formats[$key], $number);
}

// FormatPercent
// FormatPercent(Expression[,NumDigitsAfterDecimal [,IncludeLeadingDigit
// 	[,UseParensForNegativeNumbers [,GroupDigits]]]])
// NumDigitsAfterDecimal is the numeric value indicating how many places to the right of the decimal are displayed
// -1 Use Default
// The IncludeLeadingDigit, UseParensForNegativeNumbers, and GroupDigits arguments have the following settings:
// -1 True
// 0 False
// -2 Use Default
function ewr_FormatPercent($amount, $NumDigitsAfterDecimal, $IncludeLeadingDigit = -2, $UseParensForNegativeNumbers = -2, $GroupDigits = -2) {

	if (!is_numeric($amount))
		return $amount;

	extract($GLOBALS["EWR_DEFAULT_LOCALE"]);

	// Check $NumDigitsAfterDecimal
	if ($NumDigitsAfterDecimal > -1)
		$frac_digits = $NumDigitsAfterDecimal;

	// Check $UseParensForNegativeNumbers
	if ($UseParensForNegativeNumbers == -1) {
		$n_sign_posn = 0;
		if ($p_sign_posn == 0) {
			$p_sign_posn = 3;
		}
	} elseif ($UseParensForNegativeNumbers == 0) {
		if ($n_sign_posn == 0)
			$n_sign_posn = 3;
	}

	// Check $GroupDigits
	if ($GroupDigits == -1) {
	} elseif ($GroupDigits == 0) {
		$mon_thousands_sep = "";
	}

	// Start by formatting the unsigned number
	$number = number_format(abs($amount)*100,
							$frac_digits,
							$mon_decimal_point,
							$mon_thousands_sep);

	// Check $IncludeLeadingDigit
	if ($IncludeLeadingDigit == 0) {
		if (substr($number, 0, 2) == "0.")
			$number = substr($number, 1, strlen($number)-1);
	}
	if ($amount < 0) {
		$sign = $negative_sign;
		$key = $n_sign_posn;
	} else {
		$sign = $positive_sign;
		$key = $p_sign_posn;
	}
	$formats = array(
		'0' => '(%s%%)',
		'1' => $sign . '%s%%',
		'2' => $sign . '%s%%',
		'3' => $sign . '%s%%',
		'4' => $sign . '%s%%');

	// Lookup the key in the above array
	return sprintf($formats[$key], $number);
}

// Add message
function ewr_AddMessage(&$msg, $msgtoadd, $sep = "<br>") {
	if (strval($msgtoadd) <> "") {
		if (strval($msg) <> "")
			$msg .= $sep;
		$msg .= $msgtoadd;
	}
}

// Add filter
function ewr_AddFilter(&$filter, $newfilter) {
	if (trim($newfilter) == "") return;
	if (trim($filter) <> "") {
		$filter = "(" . $filter . ") AND (" . $newfilter . ")";
	} else {
		$filter = $newfilter;
	}
}

// Add slashes for SQL
function ewr_AdjustSql($val) {
<!--## if (bDBMySql) { ##-->
	$val = addslashes(trim($val));
<!--## } else { ##-->
	$val = trim($val);
	$val = str_replace("'", "''", $val); // Adjust for single quote
<!--## } ##-->
	return $val;
}

// Build Report SQL
function ewr_BuildReportSql($sSelect, $sWhere, $sGroupBy, $sHaving, $sOrderBy, $sFilter, $sSort) {

	$sDbWhere = $sWhere;
	if ($sDbWhere <> "") $sDbWhere = "(" . $sDbWhere . ")";
	if ($sFilter <> "") {
		if ($sDbWhere <> "") $sDbWhere .= " AND ";
		$sDbWhere .= "(" . $sFilter . ")";
	}
	$sDbOrderBy = ewr_UpdateSortFields($sOrderBy, $sSort, 1);
	$sSql = $sSelect;
	if ($sDbWhere <> "") $sSql .= " WHERE " . $sDbWhere;
	if ($sGroupBy <> "") $sSql .= " GROUP BY " . $sGroupBy;
	if ($sHaving <> "") $sSql .= " HAVING " . $sHaving;
	if ($sDbOrderBy <> "") $sSql .= " ORDER BY " . $sDbOrderBy;

	return $sSql;

}

// Update sort fields
// - opt = 1, merge all sort fields
// - opt = 2, merge sOrderBy fields only
function ewr_UpdateSortFields($sOrderBy, $sSort, $opt) {
	if ($sOrderBy == "") {
		if ($opt == 1)
			return $sSort;
		else
			return "";
	} elseif ($sSort == "") {
		return $sOrderBy;
	} else {
		// Merge sort field list
		$arorderby = ewr_GetSortFlds($sOrderBy);
		$cntorderby = count($arorderby);
		$arsort = ewr_GetSortFlds($sSort);
		$cntsort = count($arsort);
		for ($i = 0; $i < $cntsort; $i++) {
			// Get sort field
			$sortfld = trim($arsort[$i]);
			if (strtoupper(substr($sortfld,-4)) == " ASC") {
				$sortfld = trim(substr($sortfld,0,-4));
			} elseif (strtoupper(substr($sortfld,-5)) == " DESC") {
				$sortfld = trim(substr($sortfld,0,-4));
			}
			for ($j = 0; $j < $cntorderby; $j++) {
				// Get orderby field
				$orderfld = trim($arorderby[$j]);
				if (strtoupper(substr($orderfld,-4)) == " ASC") {
					$orderfld = trim(substr($orderfld,0,-4));
				} elseif (strtoupper(substr($orderfld,-5)) == " DESC") {
					$orderfld = trim(substr($orderfld,0,-4));
				}
				// Replace field
				if ($orderfld == $sortfld) {
					$arorderby[$j] = $arsort[$i];
					break;
				}
			}
			// Append field
			if ($opt == 1) {
				if ($orderfld <> $sortfld)
					$arorderby[] = $arsort[$i];
			}
		}
		return implode(", ", $arorderby);
	}
}

// Get sort fields
function ewr_GetSortFlds($flds) {
	$offset = -1;
	$fldpos = 0;
	$ar = array();
	while ($offset = strpos($flds, ",", $offset + 1)) {
		$orderfld = substr($flds,$fldpos,$offset-$fldpos);
		if ((strtoupper(substr($orderfld,-4)) == " ASC") || (strtoupper(substr($orderfld,-5)) == " DESC")) {
			$fldpos = $offset+1;
			$ar[] = $orderfld;
		}
	}
	$ar[] = substr($flds,$fldpos);
	return $ar;
}

// Get reverse sort
function ewr_ReverseSort($sorttype) {
	return ($sorttype == "ASC") ? "DESC" : "ASC";
}

// Construct a crosstab field name
function ewr_CrossTabField($smrytype, $smryfld, $colfld, $datetype, $val, $qc, $alias="") {
	if ($val == EWR_NULL_VALUE) {
		$wrkval = "NULL";
		$wrkqc = "";
	} elseif ($val == EWR_EMPTY_VALUE) {
		$wrkval = "";
		$wrkqc = $qc;
	} else {
		$wrkval = $val;
		$wrkqc = $qc;
	}
	switch ($smrytype) {
	case "SUM":
		$fld = $smrytype . "(" . $smryfld . "*" . ewr_SQLDistinctFactor($colfld, $datetype, $val, $qc) . ")";
		break;
	case "COUNT":
		$fld = "SUM(" . ewr_SQLDistinctFactor($colfld, $datetype, $wrkval, $wrkqc) . ")";
		break;
	case "MIN":
	case "MAX":
		$aggwrk = ewr_SQLDistinctFactor($colfld, $datetype, $wrkval, $wrkqc);
		$fld = $smrytype . "(IF(" . $aggwrk . "=0,NULL," . $smryfld . "))";
		if (EWR_IS_MSACCESS)
			$fld = $smrytype . "(IIf(" . $aggwrk . "=0,NULL," . $smryfld . "))";
		elseif (EWR_IS_MSSQL || EWR_IS_ORACLE)
			$fld = $smrytype . "(CASE " . $aggwrk . " WHEN 0 THEN NULL ELSE " . $smryfld . " END)";
		elseif (EWR_IS_MYSQL || EWR_IS_POSTGRESQL)
			$fld = $smrytype . "(IF(" . $aggwrk . "=0,NULL," . $smryfld . "))";
		break;
	case "AVG":
		$sumwrk = "SUM(" . $smryfld . "*" . ewr_SQLDistinctFactor($colfld, $datetype, $wrkval, $wrkqc) . ")";
		if ($alias != "")
//			$sumwrk .= " AS SUM_" . $alias;
			$sumwrk .= " AS " . ewr_QuotedName("sum_" . $alias);
		$cntwrk =	"SUM(" . ewr_SQLDistinctFactor($colfld, $datetype, $wrkval, $wrkqc) . ")";
		if ($alias != "")
//			$cntwrk .= " AS CNT_" . $alias;
			$cntwrk .= " AS " . ewr_QuotedName("cnt_" . $alias);
		return $sumwrk . ", " . $cntwrk;
	}
	if ($alias != "")
		$fld .= " AS " . ewr_QuotedName($alias);
	return $fld;
}

// Construct SQL Distinct factor (MySQL)
// - ACCESS
//  y: IIf(Year(FieldName)=1996,1,0)
//  q: IIf(DatePart(""q"",FieldName,1,0)=1,1,0))
//  m: (IIf(DatePart(""m"",FieldName,1,0)=1,1,0)))
//  others: (IIf(FieldName=val,1,0)))
// - MS SQL
//  y: (1-ABS(SIGN(Year(FieldName)-1996)))
//  q: (1-ABS(SIGN(DatePart(q,FieldName)-1)))
//  m: (1-ABS(SIGN(DatePart(m,FieldName)-1)))
//  d: (CASE Convert(VarChar(10),FieldName,120) WHEN '1996-1-1' THEN 1 ELSE 0 END)
// - MySQL
//  y: IF(YEAR(FieldName)=1996,1,0))
//  q: IF(QUARTER(FieldName)=1,1,0))
//  m: IF(MONTH(FieldName)=1,1,0))
// - PostgreSql
//  y: IF(EXTRACT(YEAR FROM FieldName)=1996,1,0))
//  q: IF(EXTRACT(QUARTER FROM FieldName)=1,1,0))
//  m: IF(EXTRACT(MONTH FROM FieldName)=1,1,0))
function ewr_SQLDistinctFactor($sFld, $dateType, $val, $qc) {
	// ACCESS
	if (EWR_IS_MSACCESS) {
		if ($dateType == "y" && is_numeric($val)) {
			return "IIf(Year(" . $sFld . ")=" . $val . ",1,0)";
		} elseif (($dateType == "q" || $dateType == "m") && is_numeric($val)) {
			return "IIf(DatePart(\"" . $dateType . "\"," . $sFld . ")=" . $val . ",1,0)";
		} else {
			if ($val == "NULL")
				return "IIf(" . $sFld . " IS NULL,1,0)";
			else
				return "IIf(" . $sFld . "=" . $qc . ewr_AdjustSql($val) . $qc . ",1,0)";
		}
	// MS SQL
	} elseif (EWR_IS_MSSQL) {
		if ($dateType == "y" && is_numeric($val)) {
			return "(1-ABS(SIGN(Year(" . $sFld . ")-" . $val . ")))";
		} elseif (($dateType == "q" || $dateType == "m") && is_numeric($val)) {
			return "(1-ABS(SIGN(DatePart(" . $dateType . "," . $sFld . ")-" . $val . ")))";
		} elseif ($dateType == "d") {
			return "(CASE CONVERT(VARCHAR(10)," . $sFld . ",120) WHEN " . $qc . ewr_AdjustSql($val) . $qc . " THEN 1 ELSE 0 END)";
		} elseif ($dateType == "dt") {
			return "(CASE CONVERT(VARCHAR," . $sFld . ",120) WHEN " . $qc . ewr_AdjustSql($val) . $qc . " THEN 1 ELSE 0 END)";
		} else {
			if ($val == "NULL")
				return "(CASE WHEN " . $sFld . " IS NULL THEN 1 ELSE 0 END)";
			else
				return "(CASE " . $sFld . " WHEN " . $qc . ewr_AdjustSql($val) . $qc . " THEN 1 ELSE 0 END)";
		}
	// MySQL
	} elseif (EWR_IS_MYSQL) {
		if ($dateType == "y" && is_numeric($val)) {
			return "IF(YEAR(" . $sFld . ")=" . $val . ",1,0)";
		} elseif ($dateType == "q" && is_numeric($val)) {
			return "IF(QUARTER(" . $sFld . ")=" . $val . ",1,0)";
		} elseif ($dateType == "m" && is_numeric($val)) {
			return "IF(MONTH(" . $sFld . ")=" . $val . ",1,0)";
		} else {
			if ($val == "NULL") {
				return "IF(" . $sFld . " IS NULL,1,0)";
			} else {
				return "IF(" . $sFld . "=" . $qc . ewr_AdjustSql($val) . $qc . ",1,0)";
			}
		}
	// PostgreSql
	} elseif (EWR_IS_POSTGRESQL) {
		if ($dateType == "y" && is_numeric($val)) {
			return "CASE WHEN TO_CHAR(" . $sFld . ",'YYYY')='" . $val . "' THEN 1 ELSE 0 END";
		} elseif ($dateType == "q" && is_numeric($val)) {
			return "CASE WHEN TO_CHAR(" . $sFld . ",'Q')='" . $val . "' THEN 1 ELSE 0 END";
		} elseif ($dateType == "m" && is_numeric($val)) {
			return "CASE WHEN TO_CHAR(" . $sFld . ",'MM')=LPAD('" . $val . "',2,'0') THEN 1 ELSE 0 END";
		} else {
			if ($val == "NULL") {
				return "CASE WHEN " . $sFld . " IS NULL THEN 1 ELSE 0 END";
			} else {
				return "CASE WHEN " . $sFld . "=" . $qc . ewr_AdjustSql($val) . $qc . " THEN 1 ELSE 0 END";
			}
		}
	// Oracle
	} elseif (EWR_IS_ORACLE || EWR_IS_POSTGRESQL) {
		if ($dateType == "y" && is_numeric($val)) {
			return "DECODE(TO_CHAR(" . $sFld . ",'YYYY'),'" . $val . "',1,0)";
		} elseif ($dateType == "q" && is_numeric($val)) {
			return "DECODE(TO_CHAR(" . $sFld . ",'Q'),'" . $val . "',1,0)";
		} elseif ($dateType == "m" && is_numeric($val)) {
			return "DECODE(TO_CHAR(" . $sFld . ",'MM'),LPAD('" . $val . "',2,'0'),1,0)";
		} elseif ($dateType == "d") {
			return "DECODE(" . $sFld . ",TO_DATE(" . $qc . ewr_AdjustSql($val) . $qc . ",'YYYY/MM/DD'),1,0)";
		} elseif ($dateType == "dt") {
			return "DECODE(" . $sFld . ",TO_DATE(" . $qc . ewr_AdjustSql($val) . $qc . ",'YYYY/MM/DD HH24:MI:SS'),1,0)";
		} else {
			if ($val == "NULL") {
				return "(CASE WHEN " . $sFld . " IS NULL THEN 1 ELSE 0 END)";
			} else {
				return "DECODE(" . $sFld . "," . $qc . ewr_AdjustSql($val) . $qc . ",1,0)";
			}
		}
	}
}

// Evaluate summary value
function ewr_SummaryValue($val1, $val2, $ityp) {
	switch ($ityp) {
	case "SUM":
	case "COUNT":
	case "AVG":
		if (is_null($val2) || !is_numeric($val2)) {
			return $val1;
		} else {
			return ($val1 + $val2);
		}
	case "MIN":
		if (is_null($val2) || !is_numeric($val2)) {
			return $val1; // Skip null and non-numeric
		} elseif (is_null($val1)) {
			return $val2; // Initialize for first valid value
		} elseif ($val1 < $val2) {
			return $val1;
		} else {
			return $val2;
		}
	case "MAX":
		if (is_null($val2) || !is_numeric($val2)) {
			return $val1; // Skip null and non-numeric
		} elseif (is_null($val1)) {
			return $val2; // Initialize for first valid value
		} elseif ($val1 > $val2) {
			return $val1;
		} else {
			return $val2;
		}
	}
}

// Match filter value
function ewr_MatchedFilterValue($ar, $value) {
	if (!is_array($ar)) {
		return (strval($ar) == strval($value));
	} else {
		foreach ($ar as $val) {
			if (strval($val) == strval($value))
				return TRUE;
		}
		return FALSE;
	}
}

// Render repeat column table
// - rowcnt - zero based row count
function ewr_RepeatColumnTable($totcnt, $rowcnt, $repeatcnt, $rendertype) {
	$sWrk = "";
	if ($rendertype == 1) { // Render control start
		if ($rowcnt == 0) $sWrk .= "<table class=\"" . EWR_ITEM_TABLE_CLASSNAME . "\">";
		if ($rowcnt % $repeatcnt == 0) $sWrk .= "<tr>";
		$sWrk .= "<td>";
	} elseif ($rendertype == 2) { // Render control end
		$sWrk .= "</td>";
		if ($rowcnt % $repeatcnt == $repeatcnt - 1) {
			$sWrk .= "</tr>";
		} elseif ($rowcnt == $totcnt - 1) {
			for ($i = ($rowcnt % $repeatcnt) + 1; $i < $repeatcnt; $i++) {
				$sWrk .= "<td>&nbsp;</td>";
			}
			$sWrk .= "</tr>";
		}
		if ($rowcnt == $totcnt - 1) $sWrk .= "</table>";
	}
	return $sWrk;
}

// Check if the value is selected
function ewr_IsSelectedValue(&$ar, $value, $ft) {
	if (!is_array($ar))
		return TRUE;
	$af = (substr($value, 0, 2) == "@@");
	foreach ($ar as $val) {
		if ($af || substr($val, 0, 2) == "@@") { // Advanced filters
			if ($val == $value)
				return TRUE;
		} elseif ($value == EWR_NULL_VALUE && $value == $val) {
				return TRUE;
		} else {
			if (ewr_CompareValue($val, $value, $ft))
				return TRUE;
		}
	}
	return FALSE;
}

// Check if advanced filter value
function ewr_IsAdvancedFilterValue($v) {
	if (is_array($v) && count($v) > 0) {
		foreach ($v as $val) {
			if (substr($val,0,2) <> "@@")
				return FALSE;
		}
		return TRUE;
	} elseif (substr($v,0,2) == "@@") {
		return TRUE;
	}
	return FALSE;
}

// Set up distinct values
// - ar: array for distinct values
// - val: value
// - label: display value
// - dup: check duplicate
function ewr_SetupDistinctValues(&$ar, $val, $label, $dup, $dlm = "") {
	$isarray = is_array($ar);
	if ($dlm <> "") {
		$arval = explode($dlm, $val);
		$arlabel = explode($dlm, $label);
		if (count($arval) <> count($arlabel)) {
			$arval = array($val);
			$arlabel = array($label);
		}
	} else {
		$arval = array($val);
		$arlabel = array($label);
	}
	$cntval = count($arval);
	for ($i = 0; $i < $cntval; $i++) {
		$v = $arval[$i];
		$l = $arlabel[$i];
		if ($dup && $isarray && in_array($v, array_keys($ar)))
			continue;
		if (!$isarray) {
			$ar = array($v => $l);
		} elseif ($v == EWR_EMPTY_VALUE || $v == EWR_NULL_VALUE) { // Null/Empty
			$ar = array_reverse($ar, TRUE);
			$ar[$v] = $l; // Insert at top
			$ar = array_reverse($ar, TRUE);
		} else {
			$ar[$v] = $l; // Default insert at end
		}
	}
}

// Compare values based on field type
function ewr_CompareValue($v1, $v2, $ft) {
	switch ($ft) {
	// Case adBigInt, adInteger, adSmallInt, adTinyInt, adUnsignedTinyInt, adUnsignedSmallInt, adUnsignedInt, adUnsignedBigInt
	case 20:
	case 3:
	case 2:
	case 16:
	case 17:
	case 18:
	case 19:
	case 21:
		if (is_numeric($v1) && is_numeric($v2)) {
			return (intval($v1) == intval($v2));
		}
		break;
	// Case adSingle, adDouble, adNumeric, adCurrency
	case 4:
	case 5:
	case 131:
	case 6:
		if (is_numeric($v1) && is_numeric($v2)) {
			return ((float)$v1 == (float)$v2);
		}
		break;
	//	Case adDate, adDBDate, adDBTime, adDBTimeStamp
	case 7:
	case 133:
	case 134:
	case 135:
		if (is_numeric(strtotime($v1)) && is_numeric(strtotime($v2))) {
			return (strtotime($v1) == strtotime($v2));
		}
		break;
	default:
		return (strcmp($v1, $v2) == 0); // Treat as string
	}
}

// Register filter
function ewr_RegisterFilter(&$fld, $ID, $Name, $FunctionName = "") {
	if (!is_array($fld->AdvancedFilters))
		$fld->AdvancedFilters = array();
	$wrkid = (substr($ID,0,2) == "@@") ? $ID : "@@" . $ID;
	$key = substr($wrkid,2);
	$fld->AdvancedFilters[$key] = new crAdvancedFilter($wrkid, $Name, $FunctionName);
}

// Unregister filter
function ewr_UnregisterFilter(&$fld, $ID) {
	if (is_array($fld->AdvancedFilters)) {
		$wrkid = (substr($ID,0,2) == "@@") ? $ID : "@@" . $ID;
		$key = substr($wrkid,2);
		foreach ($fld->AdvancedFilters as $filter) {
			if ($filter->ID == $wrkid) {
				unset($fld->AdvancedFilters[$key]);
				break;
			}
		}
	}
}

// Return date value
function ewr_DateVal($FldOpr, $FldVal, $ValType) {

	// Compose date string
	switch (strtolower($FldOpr)) {
	case "year":
		if ($ValType == 1) {
			$wrkVal = "$FldVal-01-01";
		} elseif ($ValType == 2) {
			$wrkVal = "$FldVal-12-31";
		}
		break;
	case "quarter":
		list($y, $q) = explode("|", $FldVal);
		if (intval($y) == 0 || intval($q) == 0) {
			$wrkVal = "0000-00-00";
		} else {
			if ($ValType == 1) {
				$m = ($q - 1) * 3 + 1;
				$m = str_pad($m, 2, "0", STR_PAD_LEFT);
				$wrkVal = "$y-$m-01";
			} elseif ($ValType == 2) {
				$m = ($q - 1) * 3 + 3;
				$m = str_pad($m, 2, "0", STR_PAD_LEFT);
				$wrkVal = "$y-$m-" . ewr_DaysInMonth($y, $m);
			}
		}
		break;
	case "month":
		list($y, $m) = explode("|", $FldVal);
		if (intval($y) == 0 || intval($m) == 0) {
			$wrkVal = "0000-00-00";
		} else {
			if ($ValType == 1) {
				$m = str_pad($m, 2, "0", STR_PAD_LEFT);
				$wrkVal = "$y-$m-01";
			} elseif ($ValType == 2) {
				$m = str_pad($m, 2, "0", STR_PAD_LEFT);
				$wrkVal = "$y-$m-" . ewr_DaysInMonth($y, $m);
			}
		}
		break;
	case "day":
		$wrkVal = str_replace("|", "-", $FldVal);
	}

	// Add time if necessary
	if (preg_match('/(\d{4}|\d{2})-(\d{1,2})-(\d{1,2})/', $wrkVal)) { // Date without time
		if ($ValType == 1) {
			$wrkVal .= " 00:00:00";
		} elseif ($ValType == 2) {
			$wrkVal .= " 23:59:59";
		}
	}

	// Check if datetime
	if (preg_match('/(\d{4}|\d{2})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{1,2}):(\d{1,2})/', $wrkVal)) { // DateTime
		$DateVal = $wrkVal;
	} else {
		$DateVal = "";
	}

	return $DateVal;
}

// "Past"
function ewr_IsPast($FldExpression) {
	return ("($FldExpression < '" . date("Y-m-d H:i:s") . "')");
}

// "Future";
function ewr_IsFuture($FldExpression) {
	return ("($FldExpression > '" . date("Y-m-d H:i:s") . "')");
}

// "Last 30 days"
function ewr_IsLast30Days($FldExpression) {
	$dt1 = date("Y-m-d", strtotime("-29 days"));
	$dt2 = date("Y-m-d", strtotime("+1 days"));
	return ("($FldExpression >= '$dt1' AND $FldExpression < '$dt2')");
}

// "Last 14 days"
function ewr_IsLast14Days($FldExpression) {
	$dt1 = date("Y-m-d", strtotime("-13 days"));
	$dt2 = date("Y-m-d", strtotime("+1 days"));
	return ("($FldExpression >= '$dt1' AND $FldExpression < '$dt2')");
}

// "Last 7 days"
function ewr_IsLast7Days($FldExpression) {
	$dt1 = date("Y-m-d", strtotime("-6 days"));
	$dt2 = date("Y-m-d", strtotime("+1 days"));
	return ("($FldExpression >= '$dt1' AND $FldExpression < '$dt2')");
}

// "Next 30 days"
function ewr_IsNext30Days($FldExpression) {
	$dt1 = date("Y-m-d");
	$dt2 = date("Y-m-d", strtotime("+30 days"));
	return ("($FldExpression >= '$dt1' AND $FldExpression < '$dt2')");
}

// "Next 14 days"
function ewr_IsNext14Days($FldExpression) {
	$dt1 = date("Y-m-d");
	$dt2 = date("Y-m-d", strtotime("+14 days"));
	return ("($FldExpression >= '$dt1' AND $FldExpression < '$dt2')");
}

// "Next 7 days"
function ewr_IsNext7Days($FldExpression) {
	$dt1 = date("Y-m-d");
	$dt2 = date("Y-m-d", strtotime("+7 days"));
	return ("($FldExpression >= '$dt1' AND $FldExpression < '$dt2')");
}

// "Yesterday"
function ewr_IsYesterday($FldExpression) {
	$dt1 = date("Y-m-d", strtotime("-1 days"));
	$dt2 = date("Y-m-d");
	return ("($FldExpression >= '$dt1' AND $FldExpression < '$dt2')");
}

// "Today"
function ewr_IsToday($FldExpression) {
	$dt1 = date("Y-m-d");
	$dt2 = date("Y-m-d", strtotime("+1 days"));
	return ("($FldExpression >= '$dt1' AND $FldExpression < '$dt2')");
}

// "Tomorrow"
function ewr_IsTomorrow($FldExpression) {
	$dt1 = date("Y-m-d", strtotime("+1 days"));
	$dt2 = date("Y-m-d", strtotime("+2 days"));
	return ("($FldExpression >= '$dt1' AND $FldExpression < '$dt2')");
}

// "Last month"
function ewr_IsLastMonth($FldExpression) {
	$dt1 = date("Y-m", strtotime("-1 months")) . "-01";
	$dt2 = date("Y-m") . "-01";
	return ("($FldExpression >= '$dt1' AND $FldExpression < '$dt2')");
}

// "This month"
function ewr_IsThisMonth($FldExpression) {
	$dt1 = date("Y-m") . "-01";
	$dt2 = date("Y-m", strtotime("+1 months")) . "-01";
	return ("($FldExpression >= '$dt1' AND $FldExpression < '$dt2')");
}

// "Next month"
function ewr_IsNextMonth($FldExpression) {
	$dt1 = date("Y-m", strtotime("+1 months")) . "-01";
	$dt2 = date("Y-m", strtotime("+2 months")) . "-01";
	return ("($FldExpression >= '$dt1' AND $FldExpression < '$dt2')");
}

// "Last two weeks"
function ewr_IsLast2Weeks($FldExpression) {
	if (strtotime("this Sunday") == strtotime("today")) {
		$dt1 = date("Y-m-d", strtotime("-14 days this Sunday"));
		$dt2 = date("Y-m-d", strtotime("this Sunday"));
	} else {
		$dt1 = date("Y-m-d", strtotime("-14 days last Sunday"));
		$dt2 = date("Y-m-d", strtotime("last Sunday"));
	}
	return ("($FldExpression >= '$dt1' AND $FldExpression < '$dt2')");
}

// "Last week"
function ewr_IsLastWeek($FldExpression) {
	if (strtotime("this Sunday") == strtotime("today")) {
		$dt1 = date("Y-m-d", strtotime("-7 days this Sunday"));
		$dt2 = date("Y-m-d", strtotime("this Sunday"));
	} else {
		$dt1 = date("Y-m-d", strtotime("-7 days last Sunday"));
		$dt2 = date("Y-m-d", strtotime("last Sunday"));
	}
	return ("($FldExpression >= '$dt1' AND $FldExpression < '$dt2')");
}

// "This week"
function ewr_IsThisWeek($FldExpression) {
	if (strtotime("this Sunday") == strtotime("today")) {
		$dt1 = date("Y-m-d", strtotime("this Sunday"));
		$dt2 = date("Y-m-d", strtotime("+7 days this Sunday"));
	} else {
		$dt1 = date("Y-m-d", strtotime("last Sunday"));
		$dt2 = date("Y-m-d", strtotime("+7 days last Sunday"));
	}
	return ("($FldExpression >= '$dt1' AND $FldExpression < '$dt2')");
}

// "Next week"
function ewr_IsNextWeek($FldExpression) {
	if (strtotime("this Sunday") == strtotime("today")) {
		$dt1 = date("Y-m-d", strtotime("+7 days this Sunday"));
		$dt2 = date("Y-m-d", strtotime("+14 days this Sunday"));
	} else {
		$dt1 = date("Y-m-d", strtotime("+7 days last Sunday"));
		$dt2 = date("Y-m-d", strtotime("+14 days last Sunday"));
	}
	return ("($FldExpression >= '$dt1' AND $FldExpression < '$dt2')");
}

// "Next two week"
function ewr_IsNext2Weeks($FldExpression) {
	if (strtotime("this Sunday") == strtotime("today")) {
		$dt1 = date("Y-m-d", strtotime("+7 days this Sunday"));
		$dt2 = date("Y-m-d", strtotime("+21 days this Sunday"));
	} else {
		$dt1 = date("Y-m-d", strtotime("+7 days last Sunday"));
		$dt2 = date("Y-m-d", strtotime("+21 days last Sunday"));
	}
	return ("($FldExpression >= '$dt1' AND $FldExpression < '$dt2')");
}

// "Last year"
function ewr_IsLastYear($FldExpression) {
	$dt1 = date("Y", strtotime("-1 years")) . "-01-01";
	$dt2 = date("Y") . "-01-01";
	return ("($FldExpression >= '$dt1' AND $FldExpression < '$dt2')");
}

// "This year"
function ewr_IsThisYear($FldExpression) {
	$dt1 = date("Y") . "-01-01";
	$dt2 = date("Y", strtotime("+1 years")) . "-01-01";
	return ("($FldExpression >= '$dt1' AND $FldExpression < '$dt2')");
}

// "Next year"
function ewr_IsNextYear($FldExpression) {
	$dt1 = date("Y", strtotime("+1 years")) . "-01-01";
	$dt2 = date("Y", strtotime("+2 years")) . "-01-01";
	return ("($FldExpression >= '$dt1' AND $FldExpression < '$dt2')");
}

// "Next year"
function ewr_DaysInMonth($y, $m) {
	if (in_array($m, array(1, 3, 5, 7, 8, 10, 12))) {
		return 31;
	} elseif (in_array($m, array(4, 6, 9, 11))) {
		return 30;
	} elseif ($m == 2) {
		return ($y % 4 == 0) ? 29 : 28;
	}
	return 0;
}

// Function to calculate date difference
function ewr_DateDiff($dateTimeBegin, $dateTimeEnd, $interval = "d") {

	$dateTimeBegin = strtotime($dateTimeBegin);
	if ($dateTimeBegin === -1 || $dateTimeBegin === FALSE)
		return FALSE;
	
	$dateTimeEnd = strtotime($dateTimeEnd);
	if($dateTimeEnd === -1 || $dateTimeEnd === FALSE)
		return FALSE;
	
	$dif = $dateTimeEnd - $dateTimeBegin;	
	$arBegin = getdate($dateTimeBegin);
	$dateBegin = mktime(0, 0, 0, $arBegin["mon"], $arBegin["mday"], $arBegin["year"]);
	$arEnd = getdate($dateTimeEnd);
	$dateEnd = mktime(0, 0, 0, $arEnd["mon"], $arEnd["mday"], $arEnd["year"]);
	$difDate = $dateEnd - $dateBegin;
	
	switch ($interval) {
		case "s": // Seconds
			return $dif;
		case "n": // Minutes
			return ($dif > 0) ? floor($dif/60) : ceil($dif/60);
		case "h": // Hours
			return ($dif > 0) ? floor($dif/3600) : ceil($dif/3600);
		case "d": // Days
			return ($difDate > 0) ? floor($difDate/86400) : ceil($difDate/86400);
		case "w": // Weeks
			return ($difDate > 0) ? floor($difDate/604800) : ceil($difDate/604800);
		case "ww": // Calendar Weeks
			$difWeek = (($dateEnd - $arEnd["wday"]*86400) - ($dateBegin - $arBegin["wday"]*86400))/604800;
			return ($difWeek > 0) ? floor($difWeek) : ceil($difWeek);
		case "m": // Months
			return (($arEnd["year"]*12 + $arEnd["mon"]) -	($arBegin["year"]*12 + $arBegin["mon"]));
		case "yyyy": // Years
			return ($arEnd["year"] - $arBegin["year"]);
	}
}

// Set up distinct values from ext. filter
function ewr_SetupDistinctValuesFromFilter(&$ar, $af) {
	if (is_array($af)) {
		foreach ($af as $filter) {
			if ($filter->Enabled)
				ewr_SetupDistinctValues($ar, $filter->ID, $filter->Name, FALSE);
		}
	}
}

// Get group value
// - Get the group value based on field type, group type and interval
// - ft: field type
// * 1: numeric, 2: date, 3: string
// - gt: group type
// * numeric: i = interval, n = normal
// * date: d = Day, w = Week, m = Month, q = Quarter, y = Year
// * string: f = first nth character, n = normal
// - intv: interval
function ewr_GroupValue(&$fld, $val) {
	$ft = $fld->FldType;
	$grp = $fld->FldGroupByType;
	$intv = $fld->FldGroupInt;
	switch ($ft) {
	// Case adBigInt, adInteger, adSmallInt, adTinyInt, adSingle, adDouble, adNumeric, adCurrency, adUnsignedTinyInt, adUnsignedSmallInt, adUnsignedInt, adUnsignedBigInt (numeric)
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
		if (!is_numeric($val)) return $val;	
		$wrkIntv = intval($intv);
		if ($wrkIntv <= 0) $wrkIntv = 10;
		switch ($grp) {
			case "i":
				return intval($val/$wrkIntv);
			default:
				return $val;
		}
	// Case adDate, adDBDate, adDBTime, adDBTimeStamp (date)
//	case 7:
//	case 133:
//	case 134:
//	case 135:
	// Case adLongVarChar, adLongVarWChar, adChar, adWChar, adVarChar, adVarWChar (string)
	case 201: // String
	case 203:
	case 129:
	case 130:
	case 200:
	case 202:
		$wrkIntv = intval($intv);
		if ($wrkIntv <= 0) $wrkIntv = 1;
		switch ($grp) {
			case "f":
				return substr($val, 0, $wrkIntv);
			default:
				return $val;
		}
	default:
		return $val; // Ignore
	}
}

// Display group value
function ewr_DisplayGroupValue(&$fld, $val) {
	global $ReportLanguage;
	$ft = $fld->FldType;
	$grp = $fld->FldGroupByType;
	$intv = $fld->FldGroupInt;
	if (is_null($val)) return $ReportLanguage->Phrase("NullLabel");
	if ($val == "") return $ReportLanguage->Phrase("EmptyLabel");
	switch ($ft) {
	// Case adBigInt, adInteger, adSmallInt, adTinyInt, adSingle, adDouble, adNumeric, adCurrency, adUnsignedTinyInt, adUnsignedSmallInt, adUnsignedInt, adUnsignedBigInt (numeric)
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
		$wrkIntv = intval($intv);
		if ($wrkIntv <= 0) $wrkIntv = 10;
		switch ($grp) {
			case "i":
				return strval($val*$wrkIntv) . " - " . strval(($val+1)*$wrkIntv-1);
			default:
				return $val;
		}
		break;
	// Case adDate, adDBDate, adDBTime, adDBTimeStamp (date)
	case 7:
	case 133:
	case 134:
	case 135:
		$ar = explode("|", $val);
		switch ($grp) {
			Case "y":
				return $ar[0];
			Case "q":
				if (count($ar) < 2) return $val;
				return ewr_FormatQuarter($ar[0], $ar[1]);
			Case "m":
				if (count($ar) < 2) return $val;
				return ewr_FormatMonth($ar[0], $ar[1]);
			Case "w":
				if (count($ar) < 2) return $val;
				return ewr_FormatWeek($ar[0], $ar[1]);
			Case "d":
				if (count($ar) < 3) return $val;
				return ewr_FormatDay($ar[0], $ar[1], $ar[2]);
			Case "h":
				return ewr_FormatHour($ar[0]);
			Case "min":
				return ewr_FormatMinute($ar[0]);
			default:
				return $val;
		}
		break;
	default: // String and others
		return $val; // Ignore
	}
}

function ewr_FormatQuarter($y, $q) {
	return "Q" . $q . "/" . $y;
}

function ewr_FormatMonth($y, $m) {
	return $m . "/" . $y;
}

function ewr_FormatWeek($y, $w) {
	return "WK" . $w . "/" . $y;
}

function ewr_FormatDay($y, $m, $d) {
	return $y . "-" . $m . "-" . $d;
}

function ewr_FormatHour($h) {
	if (intval($h) == 0) {
		return "12 AM";
	} elseif (intval($h) < 12) {
		return $h . " AM";
	} elseif (intval($h) == 12) {
		return "12 PM";
	} else {
		return ($h-12) . " PM";
	}
}

function ewr_FormatMinute($n) {
	return $n . " MIN";
}

// Get JavaScript db in the form of:
// [{k:"key1",v:"value1",s:selected1}, {k:"key2",v:"value2",s:selected2}, ...]
function ewr_GetJsDb(&$fld, $ft) {
	$jsdb = "";
	$arv = $fld->ValueList;
	$ars = $fld->SelectionList;
	if (is_array($arv)) {
		foreach ($arv as $key => $value) {
			$jsselect = (ewr_IsSelectedValue($ars, $key, $ft)) ? "true" : "false";
			if ($jsdb <> "") $jsdb .= ",";
			$jsdb .= "{\"k\":\"" . ewr_EscapeJs($key) . "\",\"v\":\"" . ewr_EscapeJs($value) . "\",\"s\":$jsselect}";
		}
	}
	$jsdb = "[" . $jsdb . "]";
	return $jsdb;
}

// Return detail filter SQL
function ewr_DetailFilterSQL(&$fld, $fn, $val) {
	$ft = $fld->FldDataType;
	if ($fld->FldGroupSql <> "") $ft = EWR_DATATYPE_STRING;
	$sqlwrk = $fn;
	if (is_null($val)) {
		$sqlwrk .= " IS NULL";
	} else {
		$sqlwrk .= " = " . ewr_QuotedValue($val, $ft);
	}
	return $sqlwrk;
}

// Return popup filter SQL
function ewr_FilterSQL(&$fld, $fn, $ft) {
	$ar = $fld->SelectionList;
	$af = $fld->AdvancedFilters;
	$gt = $fld->FldGroupByType;
	$gi = $fld->FldGroupInt;
	$sql = $fld->FldGroupSql;
	$dlm = $fld->FldDelimiter;
	if (!is_array($ar)) {
		return TRUE;
	} else {
		$sqlwrk = "";
		$i = 0;
		foreach ($ar as $value) {
			if ($value == EWR_EMPTY_VALUE) { // Empty string
				$sqlwrk .= "$fn = '' OR ";
			} elseif ($value == EWR_NULL_VALUE) { // Null value
				$sqlwrk .= "$fn IS NULL OR ";
			} elseif (substr($value, 0, 2) == "@@") { // Advanced filter
				if (is_array($af)) {
					$afsql = ewr_AdvancedFilterSQL($af, $fn, $value); // Process popup filter
					if (!is_null($afsql))
						$sqlwrk .= $afsql . " OR ";
				}
			} elseif ($sql <> "") {
				$sqlwrk .= str_replace("%s", $fn, $sql) . " = '" . $value . "' OR ";
			} elseif ($dlm <> "") {
				$sql = ewr_GetMultiSearchSql($fn, trim($value));
				if ($sql <> "")
					$sqlwrk .= $sql . " OR ";
			} else {
				$sqlwrk .= "$fn IN (" . ewr_JoinArray($ar, ", ", $ft, $i) . ") OR ";
				break;
			}
			$i++;
		}
	}
	if ($sqlwrk != "")
		$sqlwrk = "(" . substr($sqlwrk, 0, -4) . ")";
	return $sqlwrk;
}

// Return multi-value search SQL
function ewr_GetMultiSearchSql($fn, $val) {
	if ($val == EWR_INIT_VALUE || $val == EWR_ALL_VALUE) {
		$sSql = "";
	} elseif (EWR_IS_MYSQL) {
		$sSql = "FIND_IN_SET('" . ewr_AdjustSql($val) . "', " . $fn . ")";
	} else {
		$sSql = $fn . " = '" . ewr_AdjustSql($val) . "' OR " . ewr_GetMultiSearchSqlPart($fn, $val);
	}
	return $sSql;
}

// Get multi search SQL part
function ewr_GetMultiSearchSqlPart($fn, $val) {
	global $EWR_CSV_DELIMITER;
	return $fn . ewr_Like("'" . ewr_AdjustSql($val) . $EWR_CSV_DELIMITER . "%'") . " OR " .
		$fn . ewr_Like("'%" . $EWR_CSV_DELIMITER . ewr_AdjustSql($val) . $EWR_CSV_DELIMITER . "%'") . " OR " .
		$fn . ewr_Like("'%" . $EWR_CSV_DELIMITER . ewr_AdjustSql($val) . "'");
}

// Return Advanced Filter SQL
function ewr_AdvancedFilterSQL(&$af, $fn, $val) {
	if (!is_array($af)) {
		return NULL;
	} elseif (is_null($val)) {
		return NULL;
	} else {
		foreach ($af as $filter) {
			if (strval($val) == strval($filter->ID) && $filter->Enabled) {
				$func = $filter->FunctionName;
				return $func($fn);
			}
		}
		return NULL;
	}
}

// Truncate Memo Field based on specified length, string truncated to nearest space or CrLf
function ewr_TruncateMemo($memostr, $ln, $removehtml) {
	$str = ($removehtml) ? ewr_RemoveHtml($memostr) : $memostr;
	if (strlen($str) > 0 && strlen($str) > $ln) {
		$k = 0;
		while ($k >= 0 && $k < strlen($str)) {
			$i = strpos($str, " ", $k);
			$j = strpos($str, chr(10), $k);
			if ($i === FALSE && $j === FALSE) { // Not able to truncate
				return $str;
			} else {
				// Get nearest space or CrLf
				if ($i > 0 && $j > 0) {
					if ($i < $j) {
						$k = $i;
					} else {
						$k = $j;
					}
				} elseif ($i > 0) {
					$k = $i;
				} elseif ($j > 0) {
					$k = $j;
				}
				// Get truncated text
				if ($k >= $ln) {
					return substr($str, 0, $k) . "...";
				} else {
					$k++;
				}
			}
		}
	} else {
		return $str;
	}
}

// Remove HTML tags from text
function ewr_RemoveHtml($str) {
	return preg_replace('/<[^>]*>/', '', strval($str));
}

// Escape string for JavaScript
function ewr_EscapeJs($str) {
	$str = strval($str);
	$str = str_replace("\\", "\\\\", $str);
	$str = str_replace("\"", "\\\"", $str);
    $str = str_replace("\t", "\\t", $str);
	$str = str_replace("\r", "\\r", $str);
	$str = str_replace("\n", "\\n", $str);
	return $str;
}

// Load Chart Series
function ewr_LoadChartSeries($sSql, &$cht) {
	global $conn;
	$rscht = $conn->Execute($sSql);
	$sdt = $cht->SeriesDateType;
	while ($rscht && !$rscht->EOF) {
		$cht->Series[] = ewr_ChartSeriesValue($rscht->fields[0], $sdt); // Series value
		$rscht->MoveNext();
	}
	if ($rscht) $rscht->Close();
}

// Load Chart Data
function ewr_LoadChartData($sSql, &$cht) {
	global $conn;
	$rscht = $conn->Execute($sSql);
	while ($rscht && !$rscht->EOF) {
		$temp = array();
		for ($i = 0; $i < $rscht->FieldCount(); $i++)
			$temp[$i] = $rscht->fields[$i];
		$cht->Data[] = $temp;
		$rscht->MoveNext();
	}
	if ($rscht) $rscht->Close();
}

// Get Chart X value
function ewr_ChartXValue($val, $dt) {
	if (is_numeric($dt)) {
		return ewr_FormatDateTime($val, $dt);
	} elseif ($dt == "y") {
		return $val;
	} elseif ($dt == "xyq") {
		$ar = explode("|", $val);
		if (count($ar) >= 2)
			return $ar[0] . " " . ewr_QuarterName($ar[1]);
		else
			return $val;
	} elseif ($dt == "xym") {
		$ar = explode("|", $val);
		if (count($ar) >= 2)
			return $ar[0] . " " . ewr_MonthName($ar[1]);
		else
			return $val;
	} elseif ($dt == "xq") {
		return ewr_QuarterName($val);
	}
	elseif ($dt == "xm") {
		return ewr_MonthName($val);
	} else {
		if (is_string($val))
			return trim($val);
		else
			return $val;
	}
}

// Get Chart X SQL
function ewr_ChartXSQL($fldsql, $fldtype, $val, $dt) {
	if (is_numeric($dt)) {
		return $fldsql . " = " . ewr_QuotedValue(ewr_UnFormatDateTime($val, $dt), $fldtype);
	} elseif ($dt == "y") {
		if (is_numeric($val))
			return str_replace("%s", $fldsql, EWR_YEAR_SQL) . " = " . ewr_QuotedValue($val, EWR_DATATYPE_NUMBER);
		else
			return $fldsql . " = " . ewr_QuotedValue($val, $fldtype);
	} elseif ($dt == "xyq") {
		$ar = explode("|", $val);
		if (count($ar) >= 2 && is_numeric($ar[0]) && is_numeric($ar[1]))
			return str_replace("%s", $fldsql, EWR_YEAR_SQL) . " = " . ewr_QuotedValue($ar[0], EWR_DATATYPE_NUMBER) . " AND " . str_replace("%s", $fldsql, EWR_QUARTER_SQL) . " = " . ewr_QuotedValue($ar[1], EWR_DATATYPE_NUMBER);
		else
			return $fldsql . " = " . ewr_QuotedValue($val, $fldtype);
	} elseif ($dt == "xym") {
		$ar = explode("|", $val);
		if (count($ar) >= 2 && is_numeric($ar[0]) && is_numeric($ar[1]))
			return str_replace("%s", $fldsql, EWR_YEAR_SQL) . " = " . ewr_QuotedValue($ar[0], EWR_DATATYPE_NUMBER) . " AND " . str_replace("%s", $fldsql, EWR_MONTH_SQL) . " = " . ewr_QuotedValue($ar[1], EWR_DATATYPE_NUMBER);
		else
			return $fldsql . " = " . ewr_QuotedValue($val, $fldtype);
	} elseif ($dt == "xq") {
		return str_replace("%s", $fldsql, EWR_QUARTER_SQL) . " = " . ewr_QuotedValue($val, EWR_DATATYPE_NUMBER);
	} elseif ($dt == "xm") {
		return str_replace("%s", $fldsql, EWR_MONTH_SQL) . " = " . ewr_QuotedValue($val, EWR_DATATYPE_NUMBER);
	} else {
		return $fldsql . " = " . ewr_QuotedValue($val, $fldtype);
	}
}

// Get Chart Series value
function ewr_ChartSeriesValue($val, $dt) {
	if ($dt == "syq") {
		$ar = explode("|", $val);
		if (count($ar) >= 2)
			return $ar[0] . " " . ewr_QuarterName($ar[1]);
		else
			return $val;
	} elseif ($dt == "sym") {
		$ar = explode("|", $val);
		if (count($ar) >= 2)
			return $ar[0] . " " . ewr_MonthName($ar[1]);
		else
			return $val;
	} elseif ($dt == "sq") {
		return ewr_QuarterName($val);
	} elseif ($dt == "sm") {
		return ewr_MonthName($val);
	} else {
		if (is_string($val))
			return trim($val);
		else
			return $val;
	}
}

// Get Chart Series SQL
function ewr_ChartSeriesSQL($fldsql, $fldtype, $val, $dt) {
	if ($dt == "syq") {
		$ar = explode("|", $val);
		if (count($ar) >= 2 && is_numeric($ar[0]) && is_numeric($ar[1]))
			return str_replace("%s", $fldsql, EWR_YEAR_SQL) . " = " . ewr_QuotedValue($ar[0], EWR_DATATYPE_NUMBER) . " AND " . str_replace("%s", $fldsql, EWR_QUARTER_SQL) . " = " . ewr_QuotedValue($ar[1], EWR_DATATYPE_NUMBER);
		else
			return $fldsql . " = " . ewr_QuotedValue($val, $fldtype);
	} elseif ($dt == "sym") {
		$ar = explode("|", $val);
		if (count($ar) >= 2 && is_numeric($ar[0]) && is_numeric($ar[1]))
			return str_replace("%s", $fldsql, EWR_YEAR_SQL) . " = " . ewr_QuotedValue($ar[0], EWR_DATATYPE_NUMBER) . " AND " . str_replace("%s", $fldsql, EWR_MONTH_SQL) . " = " . ewr_QuotedValue($ar[1], EWR_DATATYPE_NUMBER);
		else
			return $fldsql . " = " . ewr_QuotedValue($val, $fldtype);
	} elseif ($dt == "sq") {
		return str_replace("%s", $fldsql, EWR_QUARTER_SQL) . " = " . ewr_QuotedValue($val, EWR_DATATYPE_NUMBER);
	} elseif ($dt == "sm") {
		return str_replace("%s", $fldsql, EWR_MONTH_SQL) . " = " . ewr_QuotedValue($val, EWR_DATATYPE_NUMBER);
	} else {
		return $fldsql . " = " . ewr_QuotedValue($val, $fldtype);
	}
}

// Sort chart data
function ewr_SortChartData(&$ar, $opt, $seq="") {
	if ((($opt < 3 || $opt > 4) && $seq == "") || (($opt < 1 || $opt > 4) && $seq <> ""))
		return;
	if (is_array($ar)) {
		$cntar = count($ar);
		for ($i = 0; $i < $cntar; $i++) {
			for ($j = $i+1; $j < $cntar; $j++) {
				switch ($opt) {
					case 1: // X values ascending
						$bSwap = ewr_CompareValueCustom($ar[$i][0], $ar[$j][0], $seq);
						break;
					case 2: // X values descending
						$bSwap = ewr_CompareValueCustom($ar[$j][0], $ar[$i][0], $seq);
						break;
					case 3: // Y values ascending
						$bSwap = ewr_CompareValueCustom($ar[$i][2], $ar[$j][2], $seq);
						break;
					case 4: // Y values descending
						$bSwap = ewr_CompareValueCustom($ar[$j][2], $ar[$i][2], $seq);
				}
				if ($bSwap) {
					$tmpar = $ar[$i];
					$ar[$i] = $ar[$j];
					$ar[$j] = $tmpar;
				}
			}
		}
	}
}

// Sort chart multi series data
function ewr_SortMultiChartData(&$ar, $opt, $seq="") {
	if (!is_array($ar) || (($opt < 3 || $opt > 4) && $seq == "") || (($opt < 1 || $opt > 4) && $seq <> ""))
		return;

	// Obtain a list of columns
	foreach ($ar as $key => $row) {
		$xvalues[$key] = $row[0];
		$series[$key] = $row[1];
		$yvalues[$key] = $row[2];
		$ysums[$key] = $row[0]; // Store the x-value for the time being
		if (isset($xsums[$row[0]])) {
			$xsums[$row[0]] += $row[2];
		} else {
			$xsums[$row[0]] = $row[2];
		}
	}

	// Set up Y sum
	if ($opt == 3 || $opt == 4) {
		$cnt = count($ysums);
		for ($i=0; $i<$cnt; $i++)
			$ysums[$i] = $xsums[$ysums[$i]];
	}

	// No specific sequence, use array_multisort
	if ($seq == "") {

		switch ($opt) {
			case 1: // X values ascending
				array_multisort($xvalues, SORT_ASC, $ar);
				break;
			case 2: // X values descending
				array_multisort($xvalues, SORT_DESC, $ar);
				break;
			case 3:
			case 4: // Y values
				if ($opt == 3) { // Ascending
					array_multisort($ysums, SORT_ASC, $ar);
				} elseif ($opt == 4) { // Descending
					array_multisort($ysums, SORT_DESC, $ar);
				}
		}

	// Handle specific sequence
	} else {

		// Build key list
		if ($opt == 1 || $opt == 2)
			$vals = array_unique($xvalues);
		else
			$vals = array_unique($ysums);

		foreach ($vals as $key => $val) {
			$keys[] = array($key, $val);
		}

		// Sort key list based on specific sequence
		$cntkey = count($keys);
		for ($i = 0; $i < $cntkey; $i++) {
			for ($j = $i+1; $j < $cntkey; $j++) {
				switch ($opt) {
					// Ascending
					case 1:
					case 3:
						$bSwap = ewr_CompareValueCustom($keys[$i][1], $keys[$j][1], $seq);
						break;
					// Descending
					case 2:
					case 4:
						$bSwap = ewr_CompareValueCustom($keys[$j][1], $keys[$i][1], $seq);
						break;
				}
				if ($bSwap) {
					$tmpkey = $keys[$i];
					$keys[$i] = $keys[$j];
					$keys[$j] = $tmpkey;
				}
			}
		}
		for ($i = 0; $i < $cntkey; $i++) {
			$xsorted[] = $xvalues[$keys[$i][0]];
		}

		// Sort array based on x sequence
		$arwrk = $ar;
		$rowcnt = 0;
		$cntx = intval(count($xsorted));
		for ($i = 0; $i < $cntx; $i++) {
			foreach ($arwrk as $key => $row) {
				if ($row[0] == $xsorted[$i]) {
					$ar[$rowcnt] = $row;
					$rowcnt++;
				}
			}
		}

	}
}

// Compare values by custom sequence
function ewr_CompareValueCustom($v1, $v2, $seq) {
	if ($seq == "_number") { // Number
		if (is_numeric($v1) && is_numeric($v2)) {
			return ((float)$v1 > (float)$v2);
		}
	} else if ($seq == "_date") { // Date
		if (is_numeric(strtotime($v1)) && is_numeric(strtotime($v2))) {
			return (strtotime($v1) > strtotime($v2));
		}
	} else if ($seq <> "") { // Custom sequence
		if (is_array($seq))
			$ar = $seq;
		else
			$ar = explode(",", $seq);
		if (in_array($v1, $ar) && in_array($v2, $ar))
			return (array_search($v1, $ar) > array_search($v2, $ar));
		else
			return in_array($v2, $ar);
	}
	return ($v1 > $v2);
}

// Load array from sql
function ewr_LoadArrayFromSql($sql, &$ar) {
	global $conn;
	if (strval($sql) == "")
		return;
	$rswrk = $conn->Execute($sql);
	if ($rswrk) {
		while (!$rswrk->EOF) {
			$v = $rswrk->fields[0];
			if (is_null($v)) {
				$v = EWR_NULL_VALUE;
			} elseif ($v == "") {
				$v = EWR_EMPTY_VALUE;
			}
			if (!is_array($ar))
				$ar = array();
			$ar[] = $v;
			$rswrk->MoveNext();
		}
		$rswrk->Close();
	}
}

// Function to Match array
function ewr_MatchedArray(&$ar1, &$ar2) {
	if (!is_array($ar1) && !is_array($ar2)) {
		return TRUE;
	} elseif (is_array($ar1) && is_array($ar2)) {
		return (count(array_diff($ar1, $ar2)) == 0);
	}
	return FALSE;
}

// Write a value to file for debug
function ewr_Trace($msg) {
	$filename = "debug.txt";
	if (!$handle = fopen($filename, 'a')) exit;
	if (is_writable($filename)) fwrite($handle, $msg . "\n");
	fclose($handle);
}

// Connection/Query error handler
function ewr_ErrorFn($DbType, $ErrorType, $ErrorNo, $ErrorMsg, $Param1, $Param2, $Object) {
	if ($ErrorType == 'CONNECT') {
		<!--## if (bDBMsAccess || bDBMsSql) { ##-->
		$msg = "Failed to connect to database. Error: " . $ErrorMsg;
		<!--## } else { ##-->
		$msg = "Failed to connect to $Param2 at $Param1. Error: " . $ErrorMsg;
		<!--## } ##-->
	} elseif ($ErrorType == 'EXECUTE') {
		if (EWR_DEBUG_ENABLED) {
			$msg = "Failed to execute SQL: $Param1. Error: " . $ErrorMsg;
		} else {
			$msg = "Failed to execute SQL. Error: " . $ErrorMsg;
		}
	} 
	ewr_AddMessage($_SESSION[EWR_SESSION_FAILURE_MESSAGE], $msg);
}

// Write HTTP header
function ewr_Header($cache, $charset = EWR_CHARSET) {
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Always modified
	$export = @$_GET["export"];
	if ($cache || !$cache && ewr_IsHttps() && $export <> "" && $export <> "print") { // Allow cache
		header("Cache-Control: private, must-revalidate"); // HTTP/1.1
	} else { // No cache
		header("Cache-Control: private, no-store, no-cache, must-revalidate"); // HTTP/1.1
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache"); // HTTP/1.0
	}
	if ($charset <> "")
		header("Content-Type: text/html; charset=" . $charset); // Charset
	header("X-UA-Compatible: IE=edge");
}

// Get content file extension
function ewr_ContentExt($data) {
	$ct = ewr_ContentType(substr($data, 0, 11));
	switch ($ct) {
	case "image/gif": return ".gif"; // Return gif
	case "image/jpeg": return ".jpg"; // Return jpg
	case "image/png": return ".png"; // Return png
	case "image/bmp": return ".bmp"; // Return bmp
	case "application/pdf": return ".pdf"; // Return pdf
	default: return ""; // Unknown extension
	}
}

// Get content type
function ewr_ContentType($data, $fn = "") {
	if (substr($data, 0, 6) == "\x47\x49\x46\x38\x37\x61" || substr($data, 0, 6) == "\x47\x49\x46\x38\x39\x61") { // Check if gif
		return "image/gif";
	//} elseif (substr($data, 0, 4) == "\xFF\xD8\xFF\xE0" && substr($data, 6, 5) == "\x4A\x46\x49\x46\x00") { // Check if jpg
	} elseif (substr($data, 0, 2) == "\xFF\xD8") { // Check if jpg (SOI marker \xFF\xD8)
		return "image/jpeg";
	} elseif (substr($data, 0, 8) == "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A") { // Check if png
		return "image/png";
	} elseif (substr($data, 0, 2) == "\x42\x4D") { // Check if bmp
		return "image/bmp";
	} elseif (substr($data, 0, 4) == "\x25\x50\x44\x46") { // Check if pdf
		return "application/pdf";
	} elseif ($fn <> "") { // Use file extension to get mime type
		$extension = strtolower(substr(strrchr($fn, "."), 1));
		$ct = @$EWR_MIME_TYPES[$extension];
		if ($ct == "") {
			if (file_exists($fn) && function_exists("finfo_file")) {
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$ct = finfo_file($finfo, $fn);
				finfo_close($finfo);
			} elseif (function_exists("mime_content_type")) {
        		$ct = mime_content_type($fn);
			}
		}
		return $ct;
	} else {
		return "images";
	}
}

// Connect to database
function &ewr_Connect($info = NULL) {
	$GLOBALS["ADODB_FETCH_MODE"] = ADODB_FETCH_BOTH;
	<!--## if (bDBMySql) { ##-->
		<!--## if (UseMysqlt()) { ##-->
	$conn = ADONewConnection('mysqlt');
		<!--## } else { ##-->
	$conn = new mysqlt_driver_ADOConnection();
		<!--## } ##-->
	<!--## } else if (bDBPostgreSql) { ##-->
	$conn = ADONewConnection('postgres7');
	<!--## } else if (bDBMsSql) { ##-->
	$GLOBALS["ADODB_COUNTRECS"] = FALSE;
	$conn = ADONewConnection('ado_mssql');
	<!--## } else if (bDBMsAccess) { ##-->
	$GLOBALS["ADODB_COUNTRECS"] = FALSE;
	$conn = ADONewConnection('ado_access');
	<!--## } else if (bDBOracle) { ##-->
	$conn = ADONewConnection('oci8');
	$conn->NLS_DATE_FORMAT = 'RRRR-MM-DD HH24:MI:SS';
	<!--## } ##-->
	$conn->debug = EWR_DEBUG_ENABLED;
	$conn->debug_echo = FALSE;

	if (!$info) {
	<!--## if (bDBMySql || bDBPostgreSql || bDBOracle) { ##-->
		$info = array("host" => EWR_CONN_HOST, "port" => EWR_CONN_PORT,
			"user" => EWR_CONN_USER, "pass" => EWR_CONN_PASS, "db" => EWR_CONN_DB);
		<!--## if (bDBOracle) { ##-->
			<!--## if (PROJ.GetV("OracleCharset") != "") { ##-->
		$info["charset"] = "<!--##=ew_Quote(PROJ.GetV("OracleCharset"))##-->";
			<!--## } else { ##-->
		$info["charset"] = $conn->charSet;
			<!--## } ##-->
		$info["schema"] = EWR_CONN_SCHEMA;
		<!--## } ##-->
	<!--## } else { ##-->
		$info = <!--##=SYSTEMFUNCTIONS.ConnectionString()##-->; // ADO connection string
	<!--## } ##-->
	}

<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Global","Database_Connecting")) { ##-->
	// Database connecting event
	Database_Connecting($info);
<!--## } ##-->

	<!--## if (bDBMySql || bDBPostgreSql || bDBOracle) { ##-->
	$conn->port = intval($info["port"]);
	<!--## } ##-->
	<!--## if (bDBOracle) { ##-->
	$conn->charSet = $info["charset"];
	<!--## } ##-->
	$conn->raiseErrorFn = $GLOBALS["EWR_ERROR_FN"];
	<!--## if (bDBMySql || bDBPostgreSql || bDBOracle) { ##-->
	$conn->Connect($info["host"], $info["user"], $info["pass"], $info["db"]);
	<!--## } ##-->
	<!--## if (bDBMySql) { ##-->
	if (EWR_MYSQL_CHARSET <> "")
		$conn->Execute("SET NAMES '" . EWR_MYSQL_CHARSET . "'");
	<!--## } ##-->
	<!--## if (bDBMsAccess || bDBMsSql) { ##-->
	if (EWR_CODEPAGE > 0)
		$conn->charPage = EWR_CODEPAGE;
	$conn->Connect($info, FALSE, FALSE);
	<!--## } ##-->
	<!--## if (bDBMsSql) { ##-->
	// Set date format
	if (EWR_DEFAULT_DATE_FORMAT <> "")
		$conn->Execute("SET DATEFORMAT ymd");
	<!--## } ##-->
	<!--## if (bDBOracle) { ##-->
	// Set schema
	$conn->Execute("ALTER SESSION SET CURRENT_SCHEMA = ". ewr_QuotedName($info["schema"]));
	$conn->Execute("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'yyyy-mm-dd hh24:mi:ss'");
	$conn->Execute("ALTER SESSION SET NLS_TIMESTAMP_TZ_FORMAT = 'yyyy-mm-dd hh24:mi:ss'");
		<!--## if (PROJ.GetV("OracleCompare") != "") { ##-->
	$conn->Execute("ALTER SESSION SET NLS_COMP = <!--##=PROJ.GetV("OracleCompare")##-->");
		<!--## } ##-->
		<!--## if (PROJ.GetV("OracleSort") != "") { ##-->
	$conn->Execute("ALTER SESSION SET NLS_SORT = <!--##=PROJ.GetV("OracleSort")##-->");
		<!--## } ##-->
	<!--## } ##-->
	<!--## if (bDBPostgreSql) { ##-->
	// Set schema
	if (EWR_CONN_SCHEMA <> "public")
		$conn->Execute("SET search_path TO " . EWR_CONN_SCHEMA);
	// Set bytea_output
	$ver = explode(".", $conn->version["version"]);
	if (intval($ver[0]) >= 9) // PostgreSQL 9
		$conn->Execute("SET bytea_output = 'escape'");
	<!--## } ##-->
	$conn->raiseErrorFn = '';

<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Global","Database_Connected")) { ##-->
	// Database connected event
	Database_Connected($conn);
<!--## } ##-->

	return $conn;
}

<!--##~SYSTEMFUNCTIONS.GetServerScript("Global","Database_Connecting")##-->
<!--##~SYSTEMFUNCTIONS.GetServerScript("Global","Database_Connected")##-->

// Check if boolean value is TRUE
function ewr_ConvertToBool($value) {
	return ($value === TRUE || strval($value) == "1" ||
		strtolower(strval($value)) == "y" || strtolower(strval($value)) == "t");
}

// Check if HTTP POST
function ewr_IsHttpPost() {
	$ct = ewr_ServerVar("CONTENT_TYPE");
	if (empty($ct)) $ct = ewr_ServerVar("HTTP_CONTENT_TYPE");
	return strpos($ct, "application/x-www-form-urlencoded") !== FALSE;
}

// Strip slashes
function ewr_StripSlashes($value) {
	if (!get_magic_quotes_gpc()) return $value;
	if (is_array($value)) { 
		return array_map('ewr_StripSlashes', $value);
	} else {
		return stripslashes($value);
	}
}

// Prepend CSS class name
function ewr_PrependClass(&$attr, $classname) {
	$classname = trim($classname);
	if ($classname <> "") {
		$attr = trim($attr);
		if ($attr <> "")
			$attr = " " . $attr;
		$attr = $classname . $attr;
	}
}

// Append CSS class name
function ewr_AppendClass(&$attr, $classname) {
	$classname = trim($classname);
	if ($classname <> "") {
		$attr = trim($attr);
		if ($attr <> "")
			$attr .= " ";
		$attr .= $classname;
	}
}

// Escape chars for XML
function ewr_XmlEncode($val) {
	return htmlspecialchars(strval($val));
}

// Output SCRIPT tag
function ewr_AddClientScript($src, $attrs = NULL) {
	$atts = array("type"=>"text/javascript", "src"=>$src);
	if (is_array($attrs))
		$atts = array_merge($atts, $attrs);
	echo ewr_HtmlElement("script", $atts, "") . "\n";
}

// Output LINK tag
function ewr_AddStylesheet($href, $attrs = NULL) {
	$atts = array("rel"=>"stylesheet", "type"=>"text/css", "href"=>$href);
	if (is_array($attrs))
		$atts = array_merge($atts, $attrs);
	echo ewr_HtmlElement("link", $atts, "", FALSE) . "\n";
}

// Build HTML element
function ewr_HtmlElement($tagname, $attrs, $innerhtml = "", $endtag = TRUE) {
	$html = "<" . $tagname;
	if (is_array($attrs)) {
		foreach ($attrs as $name => $attr) {
			if (strval($attr) <> "")
				$html .= " " . $name . "=\"" . ewr_HtmlEncode($attr) . "\"";
		}
	}
	$html .= ">";
	if (strval($innerhtml) <> "")
		$html .= $innerhtml;
	if ($endtag)
		$html .= "</" . $tagname . ">";
	return $html;
}

// Encode html
function ewr_HtmlEncode($exp) {
	return @htmlspecialchars(strval($exp), ENT_COMPAT | ENT_HTML5, EWR_ENCODING);
}

// Get title
function ewr_HtmlTitle($name) {
	if (preg_match('/\s+title\s*=\s*[\'"]([\s\S]*?)[\'"]/i', $name, $matches)) { // Match title='title'
		return $matches[1];
	} elseif (preg_match('/\s+data-caption\s*=\s*[\'"]([\s\S]*?)[\'"]/i', $name, $matches)) { // Match data-caption='caption'
		return $matches[1];
	} else {
		return $name;
	}
}

// View Option Separator
function ewr_ViewOptionSeparator($rowcnt) {
	return ", ";
}

/**
 * Class for TEA encryption/decryption
 */
class crTEA {

	function long2str($v, $w) {
		$len = count($v);
		$s = array();
		for ($i = 0; $i < $len; $i++)
		{
			$s[$i] = pack("V", $v[$i]);
		}
		if ($w) {
			return substr(join('', $s), 0, $v[$len - 1]);
		}	else {
			return join('', $s);
		}
	}
	
	function str2long($s, $w) {
		$v = unpack("V*", $s. str_repeat("\0", (4 - strlen($s) % 4) & 3));
		$v = array_values($v);
		if ($w) {
			$v[count($v)] = strlen($s);
		}
		return $v;
	}
	
	// Encrypt
	public function Encrypt($str, $key = EWR_RANDOM_KEY) {
		if ($str == "") {
			return "";
		}
		$v = $this->str2long($str, true);
		$k = $this->str2long($key, false);
		$cntk = count($k);
		if ($cntk < 4) {
			for ($i = $cntk; $i < 4; $i++) {
				$k[$i] = 0;
			}
		}
		$n = count($v) - 1;
		
		$z = $v[$n];
		$y = $v[0];
		$delta = 0x9E3779B9;
		$q = floor(6 + 52 / ($n + 1));
		$sum = 0;
		while (0 < $q--) {
			$sum = $this->int32($sum + $delta);
			$e = $sum >> 2 & 3;
			for ($p = 0; $p < $n; $p++) {
				$y = $v[$p + 1];
				$mx = $this->int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ $this->int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
				$z = $v[$p] = $this->int32($v[$p] + $mx);
			}
			$y = $v[0];
			$mx = $this->int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ $this->int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
			$z = $v[$n] = $this->int32($v[$n] + $mx);
		}
		return $this->UrlEncode($this->long2str($v, false));
	}
	
	// Decrypt
	public function Decrypt($str, $key = EWR_RANDOM_KEY) {
		$str = $this->UrlDecode($str);
		if ($str == "") {
			return "";
		}
		$v = $this->str2long($str, false);
		$k = $this->str2long($key, false);
		$cntk = count($k);
		if ($cntk < 4) {
			for ($i = $cntk; $i < 4; $i++) {
				$k[$i] = 0;
			}
		}
		$n = count($v) - 1;
		
		$z = $v[$n];
		$y = $v[0];
		$delta = 0x9E3779B9;
		$q = floor(6 + 52 / ($n + 1));
		$sum = $this->int32($q * $delta);
		while ($sum != 0) {
			$e = $sum >> 2 & 3;
			for ($p = $n; $p > 0; $p--) {
				$z = $v[$p - 1];
				$mx = $this->int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ $this->int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
				$y = $v[$p] = $this->int32($v[$p] - $mx);
			}
			$z = $v[$n];
			$mx = $this->int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ $this->int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
			$y = $v[0] = $this->int32($v[0] - $mx);
			$sum = $this->int32($sum - $delta);
		}
		return $this->long2str($v, true);
	}
	
	function int32($n) {
		while ($n >= 2147483648) $n -= 4294967296;
		while ($n <= -2147483649) $n += 4294967296;
		return (int)$n;
	}
	
	function UrlEncode($string) {
		$data = base64_encode($string);
		return str_replace(array('+','/','='), array('-','_','.'), $data);
	}
	
	function UrlDecode($string) {
		$data = str_replace(array('-','_','.'), array('+','/','='), $string);
		return base64_decode($data);
	}

}

// Encrypt
function ewr_Encrypt($str, $key = EWR_RANDOM_KEY) {
	$tea = new crTEA;
	return $tea->Encrypt($str, $key);
}

// Decrypt
function ewr_Decrypt($str, $key = EWR_RANDOM_KEY) {
	$tea = new crTEA;
	return $tea->Decrypt($str, $key);
}

/**
 * Pager item class
 */
class crPagerItem {
	var $Start;
	var $Text;
	var $Enabled;
}

/**
 * Numeric pager class
 */
class crNumericPager {
	var $Items = array();
	var $Count, $FromIndex, $ToIndex, $RecordCount, $PageSize, $Range;
	var $FirstButton, $PrevButton, $NextButton, $LastButton;
	var $ButtonCount = 0;
	var $Visible = TRUE;

	function __construct($StartRec, $DisplayRecs, $TotalRecs, $RecRange) {
		$this->FirstButton = new crPagerItem;
		$this->PrevButton = new crPagerItem;
		$this->NextButton = new crPagerItem;
		$this->LastButton = new crPagerItem;
		$this->FromIndex = intval($StartRec);
		$this->PageSize = intval($DisplayRecs);
		$this->RecordCount = intval($TotalRecs);
		$this->Range = intval($RecRange);
		if ($this->PageSize == 0) return;
		if ($this->FromIndex > $this->RecordCount)
			$this->FromIndex = $this->RecordCount;
		$this->ToIndex = $this->FromIndex + $this->PageSize - 1;
		if ($this->ToIndex > $this->RecordCount)
			$this->ToIndex = $this->RecordCount;
		// Setup
		$this->SetupNumericPager();
		// Update button count
		if ($this->FirstButton->Enabled) $this->ButtonCount++;
		if ($this->PrevButton->Enabled) $this->ButtonCount++;
		if ($this->NextButton->Enabled) $this->ButtonCount++;
		if ($this->LastButton->Enabled) $this->ButtonCount++;
		$this->ButtonCount += count($this->Items);
	}

	// Add pager item
	function AddPagerItem($StartIndex, $Text, $Enabled)
	{
		$Item = new crPagerItem;
		$Item->Start = $StartIndex;
		$Item->Text = $Text;
		$Item->Enabled = $Enabled;
		$this->Items[] = $Item;
	}

	// Setup pager items
	function SetupNumericPager()
	{
		if ($this->RecordCount > $this->PageSize) {
			$Eof = ($this->RecordCount < ($this->FromIndex + $this->PageSize));
			$HasPrev = ($this->FromIndex > 1);

			// First Button
			$TempIndex = 1;
			$this->FirstButton->Start = $TempIndex;
			$this->FirstButton->Enabled = ($this->FromIndex > $TempIndex);

			// Prev Button
			$TempIndex = $this->FromIndex - $this->PageSize;
			if ($TempIndex < 1) $TempIndex = 1;
			$this->PrevButton->Start = $TempIndex;
			$this->PrevButton->Enabled = $HasPrev;

			// Page links
			if ($HasPrev || !$Eof) {
				$x = 1;
				$y = 1;
				$dx1 = intval(($this->FromIndex-1)/($this->PageSize*$this->Range))*$this->PageSize*$this->Range + 1;
				$dy1 = intval(($this->FromIndex-1)/($this->PageSize*$this->Range))*$this->Range + 1;
				if (($dx1+$this->PageSize*$this->Range-1) > $this->RecordCount) {
					$dx2 = intval($this->RecordCount/$this->PageSize)*$this->PageSize + 1;
					$dy2 = intval($this->RecordCount/$this->PageSize) + 1;
				} else {
					$dx2 = $dx1 + $this->PageSize*$this->Range - 1;
					$dy2 = $dy1 + $this->Range - 1;
				}
				while ($x <= $this->RecordCount) {
					if ($x >= $dx1 && $x <= $dx2) {
						$this->AddPagerItem($x, $y, $this->FromIndex<>$x);
						$x += $this->PageSize;
						$y++;
					} elseif ($x >= ($dx1-$this->PageSize*$this->Range) && $x <= ($dx2+$this->PageSize*$this->Range)) {
						if ($x+$this->Range*$this->PageSize < $this->RecordCount) {
							$this->AddPagerItem($x, $y . "-" . ($y+$this->Range-1), TRUE);
						} else {
							$ny = intval(($this->RecordCount-1)/$this->PageSize) + 1;
							if ($ny == $y) {
								$this->AddPagerItem($x, $y, TRUE);
							} else {
								$this->AddPagerItem($x, $y . "-" . $ny, TRUE);
							}
						}
						$x += $this->Range*$this->PageSize;
						$y += $this->Range;
					} else {
						$x += $this->Range*$this->PageSize;
						$y += $this->Range;
					}
				}
			}

			// Next Button
			$TempIndex = $this->FromIndex + $this->PageSize;
			$this->NextButton->Start = $TempIndex;
			$this->NextButton->Enabled = !$Eof;

			// Last Button
			$TempIndex = intval(($this->RecordCount-1)/$this->PageSize)*$this->PageSize + 1;
			$this->LastButton->Start = $TempIndex;
			$this->LastButton->Enabled = ($this->FromIndex < $TempIndex);
		}
	}
}

/**
 * PrevNext pager class
 */
class crPrevNextPager {
	var $FirstButton, $PrevButton, $NextButton, $LastButton;
	var $CurrentPage, $PageCount, $FromIndex, $ToIndex, $RecordCount;
	var $Visible = TRUE;

	function __construct($StartRec, $DisplayRecs, $TotalRecs) {
		$this->FirstButton = new crPagerItem;
		$this->PrevButton = new crPagerItem;
		$this->NextButton = new crPagerItem;
		$this->LastButton = new crPagerItem;
		$this->FromIndex = intval($StartRec);
		$this->PageSize = intval($DisplayRecs);
		$this->RecordCount = intval($TotalRecs);
		if ($this->PageSize == 0) return;

		$this->CurrentPage = intval(($this->FromIndex-1)/$this->PageSize) + 1;
		$this->PageCount = intval(($this->RecordCount-1)/$this->PageSize) + 1;
		if ($this->FromIndex > $this->RecordCount)
			$this->FromIndex = $this->RecordCount;
		$this->ToIndex = $this->FromIndex + $this->PageSize - 1;
		if ($this->ToIndex > $this->RecordCount)
			$this->ToIndex = $this->RecordCount;

		// First Button
		$TempIndex = 1;
		$this->FirstButton->Start = $TempIndex;
		$this->FirstButton->Enabled = ($TempIndex <> $this->FromIndex);

		// Prev Button
		$TempIndex = $this->FromIndex - $this->PageSize;
		if ($TempIndex < 1) $TempIndex = 1;
		$this->PrevButton->Start = $TempIndex;
		$this->PrevButton->Enabled = ($TempIndex <> $this->FromIndex);

		// Next Button
		$TempIndex = $this->FromIndex + $this->PageSize;
		if ($TempIndex > $this->RecordCount)
			$TempIndex = $this->FromIndex;
		$this->NextButton->Start = $TempIndex;
		$this->NextButton->Enabled = ($TempIndex <> $this->FromIndex);

		// Last Button
		$TempIndex = intval(($this->RecordCount-1)/$this->PageSize)*$this->PageSize + 1;
		$this->LastButton->Start = $TempIndex;
		$this->LastButton->Enabled = ($TempIndex <> $this->FromIndex);
  }

}

/**
 * Email class
 */
class crEmail {

	// Class properties
	var $Sender = ""; // Sender
	var $Recipient = ""; // Recipient
	var $Cc = ""; // Cc
	var $Bcc = ""; // Bcc
	var $Subject = ""; // Subject
	var $Format = ""; // Format
	var $Content = ""; // Content
	var $Attachments = array(); // Attachments
	var $EmbeddedImages = array(); // Embedded image
	var $Charset = ""; // Charset
	var $SendErrDescription; // Send error description
	var $SmtpSecure = EWR_SMTP_SECURE_OPTION; // Send secure option
	var $Mailer = NULL; // PHPMailer object

	// Method to load email from template
	function Load($fn) {
		$fn = ewr_ScriptFolder() . EWR_PATH_DELIMITER . $fn;
		$sWrk = file_get_contents($fn); // Load text file content
		if (substr($sWrk, 0, 3) == "\xEF\xBB\xBF") // UTF-8 BOM
			$sWrk = substr($sWrk, 3);
		if ($sWrk <> "") {
			// Locate Header & Mail Content
			if (EWR_IS_WINDOWS) {
				$i = strpos($sWrk, "\r\n\r\n");
			} else {
				$i = strpos($sWrk, "\n\n");
				if ($i === FALSE) $i = strpos($sWrk, "\r\n\r\n");
			}
			if ($i > 0) {
				$sHeader = substr($sWrk, 0, $i);
				$this->Content = trim(substr($sWrk, $i, strlen($sWrk)));
				if (EWR_IS_WINDOWS) {
					$arrHeader = explode("\r\n", $sHeader);
				} else {
					$arrHeader = explode("\n", $sHeader);
				}
				$cnt = count($arrHeader);
				for ($j = 0; $j < $cnt; $j++) {
					$i = strpos($arrHeader[$j], ":");
					if ($i > 0) {
						$sName = trim(substr($arrHeader[$j], 0, $i));
						$sValue = trim(substr($arrHeader[$j], $i+1, strlen($arrHeader[$j])));
						switch (strtolower($sName))
						{
							case "subject":
								$this->Subject = $sValue;
								break;
							case "from":
								$this->Sender = $sValue;
								break;
							case "to":
								$this->Recipient = $sValue;
								break;
							case "cc":
								$this->Cc = $sValue;
								break;
							case "bcc":
								$this->Bcc = $sValue;
								break;
							case "format":
								$this->Format = $sValue;
								break;
						}
					}
				}
			}
		}
	}

	// Method to replace sender
	function ReplaceSender($ASender) {
		$this->Sender = str_replace('<!--$From-->', $ASender, $this->Sender);
	}

	// Method to replace recipient
	function ReplaceRecipient($ARecipient) {
		$this->Recipient = str_replace('<!--$To-->', $ARecipient, $this->Recipient);
	}

	// Method to add Cc email
	function AddCc($ACc) {
		if ($ACc <> "") {
			if ($this->Cc <> "") $this->Cc .= ";";
			$this->Cc .= $ACc;
		}
	}

	// Method to add Bcc email
	function AddBcc($ABcc) {
		if ($ABcc <> "")  {
			if ($this->Bcc <> "") $this->Bcc .= ";";
			$this->Bcc .= $ABcc;
		}
	}

	// Method to replace subject
	function ReplaceSubject($ASubject) {
		$this->Subject = str_replace('<!--$Subject-->', $ASubject, $this->Subject);
	}

	// Method to replace content
	function ReplaceContent($Find, $ReplaceWith) {
		$this->Content = str_replace($Find, $ReplaceWith, $this->Content);
	}

	// Method to add embedded image
	function AddEmbeddedImage($image) {
		if ($image <> "")
			$this->EmbeddedImages[] = $image;
	}

	// Method to add attachment
	function AddAttachment($filename, $content = "") {
		if ($filename <> "")
			$this->Attachments[] = array("filename" => $filename, "content" => $content);
	}

	// Method to send email
	function Send() {
		global $gsEmailErrDesc;
		$result = ewr_SendEmail($this->Sender, $this->Recipient, $this->Cc, $this->Bcc,
			$this->Subject, $this->Content, $this->Format, $this->Charset, $this->SmtpSecure,
			$this->Attachments, $this->EmbeddedImages, $this->Mailer);
		$this->SendErrDescription = $gsEmailErrDesc;
		return $result;
	}

}

// Include PHPMailer class
include_once($EWR_RELATIVE_PATH . "phpmailer529/class.phpmailer.php");

// Function to send email
function ewr_SendEmail($sFrEmail, $sToEmail, $sCcEmail, $sBccEmail, $sSubject, $sMail, $sFormat, $sCharset, $sSmtpSecure = "", $arAttachments = array(), $arImages = array(), $mail = NULL) {
	global $ReportLanguage, $gsEmailErrDesc;

	$res = FALSE;

	if (is_null($mail)) {
		$mail = new PHPMailer();
		$mail->IsSMTP(); 
		$mail->Host = EWR_SMTP_SERVER;
		$mail->SMTPAuth = (EWR_SMTP_SERVER_USERNAME <> "" && EWR_SMTP_SERVER_PASSWORD <> "");
		$mail->Username = EWR_SMTP_SERVER_USERNAME;
		$mail->Password = EWR_SMTP_SERVER_PASSWORD;
		$mail->Port = EWR_SMTP_SERVER_PORT;
	}
	
	if ($sSmtpSecure <> "") $mail->SMTPSecure = $sSmtpSecure;
	if (preg_match('/^(.+)<([\w.%+-]+@[\w.-]+\.[A-Z]{2,6})>$/i', trim($sFrEmail), $m)) {
		$mail->From = $m[2];
		$mail->FromName = trim($m[1]);
	} else {
		$mail->From = $sFrEmail;
		$mail->FromName = $sFrEmail;
	}
	$mail->Subject = $sSubject;
	$mail->Body = $sMail;

	if ($sCharset <> "" && strtolower($sCharset) <> "iso-8859-1")
		$mail->CharSet = $sCharset;

	$sToEmail = str_replace(";", ",", $sToEmail);
	$arrTo = explode(",", $sToEmail);

	foreach ($arrTo as $sTo) {
		$mail->AddAddress(trim($sTo));
	}

	if ($sCcEmail <> "") {
		$sCcEmail = str_replace(";", ",", $sCcEmail);
		$arrCc = explode(",", $sCcEmail);

		foreach ($arrCc as $sCc) {
			$mail->AddCC(trim($sCc));
		}
	}

	if ($sBccEmail <> "") {
		$sBccEmail = str_replace(";", ",", $sBccEmail);
		$arrBcc = explode(",", $sBccEmail);

		foreach ($arrBcc as $sBcc) {
			$mail->AddBCC(trim($sBcc));
		}
	}

	if (strtolower($sFormat) == "html") {
		$mail->ContentType = "text/html";
	} else {
		$mail->ContentType = "text/plain";
	}

	if (is_array($arAttachments)) {
		foreach ($arAttachments as $attachment) {
			$filename = @$attachment["filename"];
			$content = @$attachment["content"];
			if ($content <> "" && $filename <> "") {
				$mail->AddStringAttachment($content, $filename);
			} else if ($filename <> "") {
				$mail->AddAttachment($filename);
			}
		}
	}

	if (is_array($arImages)) {
		foreach ($arImages as $tmpimage) {
			$file = ewr_UploadPathEx(TRUE, EWR_UPLOAD_DEST_PATH) . $tmpimage;
			$cid = ewr_TmpImageLnk($tmpimage, "cid");
			$mail->AddEmbeddedImage($file, $cid, $tmpimage);
		}
	}

	$res = $mail->Send();
	$gsEmailErrDesc = $mail->ErrorInfo;

	// Uncomment to debug
//		var_dump($mail); exit();

	return $res;
}

// Clean email content
function ewr_CleanEmailContent($Content) {
	$Content = str_replace("class=\"ewGrid\"", "", $Content);
	$Content = str_replace("class=\"table-responsive ewGridMiddlePanel\"", "", $Content);
	$Content = str_replace("table ewTable", "ewExportTable", $Content);
	return $Content;
}

// Load email count
function ewr_LoadEmailCount() {

	// Read from log
	if (EWR_EMAIL_WRITE_LOG) {

		$ip = ewr_ServerVar("REMOTE_ADDR");

		// Load from database
		if (EWR_EMAIL_WRITE_LOG_TO_DATABASE) {

			global $conn;
			$dt1 = date("Y-m-d H:i:s", strtotime("- " . EWR_MAX_EMAIL_SENT_PERIOD . "minute"));
			$dt2 = date("Y-m-d H:i:s");
			$sEmailSql = "SELECT COUNT(*) FROM " . EWR_EMAIL_LOG_TABLE_NAME .
				" WHERE " . ewr_QuotedName(EWR_EMAIL_LOG_FIELD_NAME_DATETIME) .
				" BETWEEN " . ewr_QuotedValue($dt1, EWR_DATATYPE_DATE) . " AND " . ewr_QuotedValue($dt2, EWR_DATATYPE_DATE) .
				" AND " . ewr_QuotedName(EWR_EMAIL_LOG_FIELD_NAME_IP) . 
				" = " . ewr_QuotedValue($ip, EWR_DATATYPE_STRING);
			$rscnt = $conn->Execute($sEmailSql);
			if ($rscnt) {
				$_SESSION[EWR_EXPORT_EMAIL_COUNTER] = ($rscnt->RecordCount()>1) ? $rscnt->RecordCount() : $rscnt->fields[0];
				$rscnt->Close();
			} else {
				$_SESSION[EWR_EXPORT_EMAIL_COUNTER] = 0;
			}

		// Load from log file
		} else {

			$pfx = "email";
			$sTab = "\t";
			$sFolder = EWR_UPLOAD_DEST_PATH;
			$randomkey = ewr_Encrypt(date("Ymd"), EWR_RANDOM_KEY);
			$sFn = $pfx . "_" . date("Ymd") . "_" . $randomkey . ".txt";
			$filename = ewr_UploadPathEx(TRUE, $sFolder) . $sFn;
			if (file_exists($filename)) {
				$arLines = file($filename);
				$cnt = 0;
				foreach ($arLines as $line) {
					if ($line <> "") {
						list($dtwrk, $ipwrk, $senderwrk, $recipientwrk, $subjectwrk, $messagewrk) = explode($sTab, $line);
						$timediff = intval((strtotime("now") - strtotime($dtwrk,0))/60);
						if ($ipwrk == $ip && $timediff < EWR_MAX_EMAIL_SENT_PERIOD) $cnt++;
					}
				}
				$_SESSION[EWR_EXPORT_EMAIL_COUNTER] = $cnt;
			} else {
				$_SESSION[EWR_EXPORT_EMAIL_COUNTER] = 0;
			}

		}

	}

	if (!isset($_SESSION[EWR_EXPORT_EMAIL_COUNTER]))
		$_SESSION[EWR_EXPORT_EMAIL_COUNTER] = 0;
	return intval($_SESSION[EWR_EXPORT_EMAIL_COUNTER]);
}

// Add email log
function ewr_AddEmailLog($sender, $recipient, $subject, $message) {
	$_SESSION[EWR_EXPORT_EMAIL_COUNTER]++;

	// Save to email log
	if (EWR_EMAIL_WRITE_LOG) {

		$dt = date("Y-m-d H:i:s");
		$ip = ewr_ServerVar("REMOTE_ADDR");
		$senderwrk = ewr_TruncateText($sender);
		$recipientwrk = ewr_TruncateText($recipient);
		$subjectwrk = ewr_TruncateText($subject);
		$messagewrk = ewr_TruncateText($message);

		// Save to database
		if (EWR_EMAIL_WRITE_LOG_TO_DATABASE) {

			global $conn;
			$sEmailSql = "INSERT INTO " . EWR_EMAIL_LOG_TABLE_NAME .
				" (" . ewr_QuotedName(EWR_EMAIL_LOG_FIELD_NAME_DATETIME) . ", " .
				ewr_QuotedName(EWR_EMAIL_LOG_FIELD_NAME_IP) . ", " .
				ewr_QuotedName(EWR_EMAIL_LOG_FIELD_NAME_SENDER) . ", " .
				ewr_QuotedName(EWR_EMAIL_LOG_FIELD_NAME_RECIPIENT) . ", " .
				ewr_QuotedName(EWR_EMAIL_LOG_FIELD_NAME_SUBJECT) . ", " .
				ewr_QuotedName(EWR_EMAIL_LOG_FIELD_NAME_MESSAGE) . ") VALUES (" .
				ewr_QuotedValue($dt, EWR_DATATYPE_DATE) . ", " .
				ewr_QuotedValue($ip, EWR_DATATYPE_STRING) . ", " .
				ewr_QuotedValue($senderwrk, EWR_DATATYPE_STRING) . ", " .
				ewr_QuotedValue($recipientwrk, EWR_DATATYPE_STRING) . ", " .
				ewr_QuotedValue($subjectwrk, EWR_DATATYPE_STRING) . ", " .
				ewr_QuotedValue($messagewrk, EWR_DATATYPE_STRING) . ")";
			$conn->Execute($sEmailSql);

		// Save to log file
		} else {

			$pfx = "email";
			$sTab = "\t";
			$sHeader = "date/time" . $sTab . "ip" . $sTab . "sender" . $sTab . "recipient" . $sTab . "subject" . $sTab . "message";
			$sMsg = $dt . $sTab . $ip . $sTab . $senderwrk . $sTab . $recipientwrk . $sTab . $subjectwrk . $sTab . $messagewrk;
			$sFolder = EWR_UPLOAD_DEST_PATH;
			$randomkey = ewr_Encrypt(date("Ymd"), EWR_RANDOM_KEY);
			$sFn = $pfx . "_" . date("Ymd") . "_" . $randomkey . ".txt";
			$filename = ewr_UploadPathEx(TRUE, $sFolder) . $sFn;
			if (file_exists($filename)) {
				$fileHandler = fopen($filename, "a+b");
			} else {
				$fileHandler = fopen($filename, "a+b");
				fwrite($fileHandler,$sHeader."\r\n");
			}
			fwrite($fileHandler, $sMsg."\r\n");
			fclose($fileHandler);

		}
	}
}

function ewr_TruncateText($v) {
	$maxlen = EWR_EMAIL_LOG_SIZE_LIMIT;
	$v = str_replace("\r\n", " ", $v);
	$v = str_replace("\t", " ", $v);
	if (strlen($v) > $maxlen)
		$v = substr($v, 0, $maxlen-3) . "...";
	return $v;
}

// Get global debug message
function ewr_DebugMsg() {
	global $gsDebugMsg;
	$msg = preg_replace('/^<br>\n/', "", $gsDebugMsg);
	$gsDebugMsg = "";
	return ($msg <> "") ? "<div class=\"alert alert-info ewAlert\">" . $msg . "</div>" : "";
}

// Write global debug message
function ewr_SetDebugMsg($v, $newline = TRUE) {
	global $gsDebugMsg;
	if ($newline && $gsDebugMsg <> "")
		$gsDebugMsg .= "<br>";
	$gsDebugMsg .= $v;
}

/**
 * Functions for converting encoding
 */
function ewr_ConvertToUtf8($str) {
	return ewr_Convert(EWR_ENCODING, "UTF-8", $str);
}

function ewr_ConvertFromUtf8($str) {
	return ewr_Convert("UTF-8", EWR_ENCODING, $str);
}

function ewr_Convert($from, $to, $str) {
	if ($from != "" && $to != "" && strtoupper($from) != strtoupper($to)) {
		if (function_exists("iconv")) {
			return iconv($from, $to, $str);
		} elseif (function_exists("mb_convert_encoding")) {
			return mb_convert_encoding($str, $to, $from);
		} else {
			return $str;
		}
	} else {
		return $str;
	}
}

// Encode value for single-quoted JavaScript string
function ewr_JsEncode($val) {
	$val = strval($val);
	if (EWR_IS_DOUBLE_BYTE)
		$val = ewr_ConvertToUtf8($val);
	$val = str_replace("\\", "\\\\", $val);
	$val = str_replace("'", "\\'", $val);
	$val = str_replace("\r\n", "<br>", $val);
	$val = str_replace("\r", "<br>", $val);
	$val = str_replace("\n", "<br>", $val);
	if (EWR_IS_DOUBLE_BYTE)
		$val = ewr_ConvertFromUtf8($val);
	return $val;
}

// Encode value for double-quoted Javascript string
function ewr_JsEncode2($val) {
	$val = strval($val);
	if (EWR_IS_DOUBLE_BYTE)
		$val = ewr_ConvertToUtf8($val);
	$val = str_replace("\\", "\\\\", $val);
	$val = str_replace("\"", "\\\"", $val);
	$val = str_replace("\t", "\\t", $val);
	$val = str_replace("\r", "\\r", $val);
	$val = str_replace("\n", "\\n", $val);
	if (EWR_IS_DOUBLE_BYTE)
		$val = ewr_ConvertFromUtf8($val);
	return $val;
}

// Convert a value to JSON value
// $type: string/boolean
function ewr_VarToJson($val, $type = "") {
	$type = strtolower($type);
	if (is_null($val)) {
		return "null";
	} elseif ($type == "boolean" || is_bool($val)) {
		return (ewr_ConvertToBool($val)) ? "true" : "false";
	} elseif ($type == "string" || is_string($val)) {
		return "\"" . ewr_JsEncode2($val) . "\"";
	}
	return $val;
}

// Encode json
function ewr_JsonEncode($ar) {
	if (count($ar) > 0) {
		$json = json_encode($ar);
		if ($json <> "")
			return "[" . json_encode($ar) . "]";
		else
			return "null";
	} else {
		return "null";
	}
}

// Convert rows (array) to JSON
function ewr_ArrayToJson($ar, $offset = 0) {
	$arOut = array();
	$array = FALSE;
	if (count($ar) > 0) {
		$keys = array_keys($ar[0]);
		foreach ($keys as $key) {
			if (is_int($key)) {
				$array = TRUE;
				break;
			}
		}
	}
	foreach ($ar as $row) {
		$arwrk = array();
		foreach ($row as $key => $val) {
			if (($array && is_string($key)) || (!$array && is_int($key)))
				continue;
			$key = ($array) ? "" : "\"" . ewr_JsEncode2($key) . "\":";
			$arwrk[] = $key . ewr_VarToJson($val);
		}
		if ($array) { // Array
			$arOut[] = "[" . implode(",", $arwrk) . "]";
		} else { // Object
			$arOut[] = "{" . implode(",", $arwrk) . "}";
		}
	}
	if ($offset > 0)
		$arOut = array_slice($arOut, $offset);
	return "[" . implode(",", $arOut) . "]";
}

// Executes the query, and returns the row(s) as JSON
function ewr_ExecuteJson($SQL, $FirstOnly = TRUE) {
	$rs = ewr_LoadRecordset($SQL);
	if ($rs && !$rs->EOF && $rs->FieldCount() > 0) {
		$res = ($FirstOnly) ? array($rs->fields) : $rs->GetRows();
		$rs->Close();
		return json_encode($res);
	}
	return "false";
}

// Get current page name
function ewr_CurrentPage() {
	return ewr_GetPageName(ewr_ScriptName());
}

// Get page name
function ewr_GetPageName($url) {
	$PageName = "";
	if ($url <> "") {
		$PageName = $url;
		$p = strpos($PageName, "?");
		if ($p !== FALSE)
			$PageName = substr($PageName, 0, $p); // Remove QueryString
		$p = strrpos($PageName, "/");
		if ($p !== FALSE)
			$PageName = substr($PageName, $p+1); // Remove path
	}
	return $PageName;
}

// Adjust text for caption
function ewr_BtnCaption($Caption) {
	$Min = 10;
	if (strlen($Caption) < $Min) {
		$Pad = abs(intval(($Min - strlen($Caption))/2*-1));
		$Caption = str_repeat(" ", $Pad) . $Caption . str_repeat(" ", $Pad);
	}
	return $Caption;
}


// Include mobile_detect.php
include_once("<!--##=ew_GetFileNameByCtrlID("mobiledetect")##-->");

// Check if mobile device
function ewr_IsMobile() {
	global $MobileDetect;
	if (!isset($MobileDetect))
		$MobileDetect = new Mobile_Detect;
	return $MobileDetect->isMobile();
}

// Check if responsive layout
function ewr_IsResponsiveLayout() {
	return $GLOBALS['EWR_USE_RESPONSIVE_LAYOUT'];
}

// Get server variable by name
function ewr_ServerVar($Name) {
	$str = @$_SERVER[$Name];
	if (empty($str)) $str = @$_ENV[$Name];
	return $str;
}

//###// Get jQuery host
//function ewr_jQueryHost() {
//	return "jquery/"; // Use local files
//}
//
//// Get jQuery version
//function ewr_jQueryFile($f) {
//	$v = "1.11.1"; // Get jQuery version
//	return str_replace("%v", $v, ewr_jQueryHost() . $f);
//}

// Get CSS file
function ewr_CssFile($f) {
	if (EWR_CSS_FLIP)
		return preg_replace('/(.css)$/i', "-rtl.css", $f);
	else
		return $f;
}

// Check if HTTPS
function ewr_IsHttps() {
	return (ewr_ServerVar("HTTPS") <> "" && ewr_ServerVar("HTTPS") <> "off");
}

// Encrypt password
function ewr_EncryptPassword($input, $salt = '') {
	return (strval($salt) <> "") ? md5($input . $salt) . ":" . $salt : md5($input);
}

// Compare password
// Note: If salted, password must be stored in '<hashedstring>:<salt>' or in phpass format
function ewr_ComparePassword($pwd, $input) {
	if (preg_match('/^\$[HP]\$/', $pwd)) { // phpass
		include "passwordhash.php";
		$hasher = new PasswordHash(10, TRUE);
		return $hasher->CheckPassword($input, $pwd);
	} elseif (strpos($pwd, ':') !== FALSE) { // <hashedstring>:<salt>
		@list($crypt, $salt) = explode(":", $pwd, 2);
		return ($pwd == ewr_EncryptPassword($input, $salt));
	} else {
		if (EWR_CASE_SENSITIVE_PASSWORD) {
			if (EWR_ENCRYPTED_PASSWORD) {
				return ($pwd == ewr_EncryptPassword($input));
			} else {
				return ($pwd == $input);
			}
		} else {
			if (EWR_ENCRYPTED_PASSWORD) {
				return ($pwd == ewr_EncryptPassword(strtolower($input)));
			} else {
				return (strtolower($pwd) == strtolower($input));
			}
		}
	}
}

// Get domain URL
function ewr_DomainUrl() {
	$sUrl = "http";
	$bSSL = (ewr_ServerVar("HTTPS") <> "" && ewr_ServerVar("HTTPS") <> "off");
	$sPort = strval(ewr_ServerVar("SERVER_PORT"));
	$defPort = ($bSSL) ? "443" : "80";
	$sPort = ($sPort == $defPort) ? "" : ":$sPort";
	$sUrl .= ($bSSL) ? "s" : "";
	$sUrl .= "://";
	$sUrl .= ewr_ServerVar("SERVER_NAME") . $sPort;
	return $sUrl;
}

// Get full URL
function ewr_FullUrl() {
	return ewr_DomainUrl() . ewr_ScriptName();
}

// Get current URL
function ewr_CurrentUrl() {
	$s = ewr_ScriptName();
	$q = ewr_ServerVar("QUERY_STRING");
	if ($q <> "") $s .= "?" . $q;
	return $s;
}

// Convert to full URL
function ewr_ConvertFullUrl($url) {
	if ($url == "") return "";
	if (strpos($url, "://") === FALSE && strpos($url, "\\") === FALSE) {
		$sUrl = ewr_FullUrl();
		return substr($sUrl, 0, strrpos($sUrl, "/")+1) . $url;
	} else {
		return $url;
	}
}

// Get relative url
function ewr_GetUrl($url) {
	global $EWR_RELATIVE_PATH;
	if ($url != "" && strpos($url, "://") === FALSE && strpos($url, "\\") === FALSE && strpos($url, "javascript:") === FALSE) {
		$path = "";
		if (strrpos($url, "/") !== FALSE) {
			$path = substr($url, 0, strrpos($url, "/"));
			$url = substr($url, strrpos($url, "/")+1); 
		}
		$path = ewr_PathCombine($EWR_RELATIVE_PATH, $path, FALSE);
		if ($path <> "") $path = ewr_IncludeTrailingDelimiter($path, FALSE);
		return $path . $url;
	} else {
		return $url;
	}
}

// Get script name
function ewr_ScriptName() {
	$sn = ewr_ServerVar("PHP_SELF");
	if (empty($sn)) $sn = ewr_ServerVar("SCRIPT_NAME");
	if (empty($sn)) $sn = ewr_ServerVar("ORIG_PATH_INFO");
	if (empty($sn)) $sn = ewr_ServerVar("ORIG_SCRIPT_NAME");
	if (empty($sn)) $sn = ewr_ServerVar("REQUEST_URI");
	if (empty($sn)) $sn = ewr_ServerVar("URL");
	if (empty($sn)) $sn = "UNKNOWN";
	return $sn;
}

// Remove XSS
function ewr_RemoveXSS($val) {
	// Remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
	// This prevents some character re-spacing such as <java\0script>
	// Note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
	$val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);
	
	// Straight replacements, the user should never need these since they're normal characters
	// This prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>
	$search = 'abcdefghijklmnopqrstuvwxyz';
	$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$search .= '1234567890!@#$%^&*()';
	$search .= '~`";:?+/={}[]-_|\'\\';
	for ($i = 0; $i < strlen($search); $i++) {
		// ;? matches the ;, which is optional
		// 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
	
		// &#x0040 @ search for the hex values
		$val = preg_replace('/(&#[x|X]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // With a ;
		// &#00064 @ 0{0,7} matches '0' zero to seven times
		$val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // With a ;
	}
	
	// Now the only remaining whitespace attacks are \t, \n, and \r 
	$ra = $GLOBALS["EWR_XSS_ARRAY"]; // Note: Customize $EWR_XSS_ARRAY in ewrcfg*.php
	
	$found = true; // Keep replacing as long as the previous round replaced something
	while ($found == true) {
		$val_before = $val;
		for ($i = 0; $i < sizeof($ra); $i++) {
			$pattern = '/';
			for ($j = 0; $j < strlen($ra[$i]); $j++) { 
				if ($j > 0) {
					$pattern .= '('; 
					$pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?'; 
					$pattern .= '|(&#0{0,8}([9][10][13]);?)?'; 
					$pattern .= ')?'; 
				}
				$pattern .= $ra[$i][$j];
			}
			$pattern .= '/i';
			$replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // Add in <> to nerf the tag
			$val = preg_replace($pattern, $replacement, $val); // Filter out the hex tags
			if ($val_before == $val) {
				// No replacements were made, so exit the loop
				$found = false;
			}
		}
	}
	return $val;
}

// Check token
function ewr_CheckToken($token) {
	return (time() - intval(ewr_Decrypt($token))) < intval(ini_get("session.gc_maxlifetime"));
}

// Create token
function ewr_CreateToken() {
	return ewr_Encrypt(time());
}

// Load selection from a filter clause
function ewr_LoadSelectionFromFilter(&$fld, $filter, &$sel, $af = "") {
	$sel = "";
	if ($af <> "") { // Set up advanced filter first
		$ar = is_array($af) ? $af : array($af);
		$cnt = count($ar);
		for ($i = 0; $i < $cnt; $i++) {
			if (substr($ar[$i],0,2) == "@@") {
				if (!is_array($sel))
					$sel = array();
				$sel[] = $ar[$i];
			}
		}
	}
	if ($filter <> "") {
		$sSql = ewr_BuildReportSql($fld->SqlSelect, "", "", "", $fld->SqlOrderBy, $filter, "");
		ewr_LoadArrayFromSql($sSql, $sel);
	}
}

// Load drop down list
function ewr_LoadDropDownList(&$list, $val) {
	if (is_array($val)) {
		$ar = $val;
	} elseif ($val <> EWR_INIT_VALUE && $val <> EWR_ALL_VALUE && $val <> "") {
		$ar = array($val);
	} else {
		$ar = array();
	}
	$list = array();
	foreach ($ar as $v) {
		if ($v <> EWR_INIT_VALUE && $v <> "" && substr($v,0,2) <> "@@")
			$list[] = $v;
	}
}

// Load selection list
function ewr_LoadSelectionList(&$list, $val) {
	if (is_array($val)) {
		$ar = $val;
	} elseif ($val <> EWR_INIT_VALUE && $val <> "") {
		$ar = array($val);
	} else {
		$ar = array();
	}
	$list = array();
	foreach ($ar as $v) {
		if ($v == EWR_ALL_VALUE) {
			$list = EWR_INIT_VALUE;
			return;
		} elseif ($v <> EWR_INIT_VALUE && $v <> "") {
			$list[] = $v;
		}
	}
	if (count($list) == 0)
		$list = EWR_INIT_VALUE;
}

// Get extended filter
function ewr_GetExtendedFilter(&$fld, $Default = FALSE) {
	$FldName = $fld->FldName;
	$FldExpression = $fld->FldExpression;
	$FldDataType = $fld->FldDataType;
	$FldDateTimeFormat = $fld->FldDateTimeFormat;
	$FldVal1 = ($Default) ? $fld->DefaultSearchValue : $fld->SearchValue;
	if (ewr_IsFloatFormat($fld->FldType)) $FldVal1 = ewr_StrToFloat($FldVal1);
	$FldOpr1 = ($Default) ? $fld->DefaultSearchOperator : $fld->SearchOperator;
	$FldCond = ($Default) ? $fld->DefaultSearchCondition : $fld->SearchCondition;
	$FldVal2 = ($Default) ? $fld->DefaultSearchValue2 : $fld->SearchValue2;
	if (ewr_IsFloatFormat($fld->FldType)) $FldVal2 = ewr_StrToFloat($FldVal2);
	$FldOpr2 = ($Default) ? $fld->DefaultSearchOperator2 : $fld->SearchOperator2;
	$sWrk = "";
	$FldOpr1 = strtoupper(trim($FldOpr1));
	if ($FldOpr1 == "") $FldOpr1 = "=";
	$FldOpr2 = strtoupper(trim($FldOpr2));
	if ($FldOpr2 == "") $FldOpr2 = "=";
	$wrkFldVal1 = $FldVal1;
	$wrkFldVal2 = $FldVal2;
	if ($FldDataType == EWR_DATATYPE_BOOLEAN) {
		if (EWR_IS_MSACCESS) {
			if ($wrkFldVal1 <> "") $wrkFldVal1 = ($wrkFldVal1 == "1") ? "True" : "False";
			if ($wrkFldVal2 <> "") $wrkFldVal2 = ($wrkFldVal2 == "1") ? "True" : "False";
		} else {
			//if ($wrkFldVal1 <> "") $wrkFldVal1 = ($wrkFldVal1 == "1") ? EWR_TRUE_STRING : EWR_FALSE_STRING;
			//if ($wrkFldVal2 <> "") $wrkFldVal2 = ($wrkFldVal2 == "1") ? EWR_TRUE_STRING : EWR_FALSE_STRING;
			if ($wrkFldVal1 <> "") $wrkFldVal1 = ($wrkFldVal1 == "1") ? "1" : "0";
			if ($wrkFldVal2 <> "") $wrkFldVal2 = ($wrkFldVal2 == "1") ? "1" : "0";
		}
	} elseif ($FldDataType == EWR_DATATYPE_DATE) {
		if ($wrkFldVal1 <> "") $wrkFldVal1 = ewr_UnFormatDateTime($wrkFldVal1, $FldDateTimeFormat);
		if ($wrkFldVal2 <> "") $wrkFldVal2 = ewr_UnFormatDateTime($wrkFldVal2, $FldDateTimeFormat);
	}
	if ($FldOpr1 == "BETWEEN") {
		$IsValidValue = ($FldDataType <> EWR_DATATYPE_NUMBER ||
			($FldDataType == EWR_DATATYPE_NUMBER && is_numeric($wrkFldVal1) && is_numeric($wrkFldVal2)));
		if ($wrkFldVal1 <> "" && $wrkFldVal2 <> "" && $IsValidValue)
			$sWrk = $FldExpression . " BETWEEN " . ewr_QuotedValue($wrkFldVal1, $FldDataType) .
				" AND " . ewr_QuotedValue($wrkFldVal2, $FldDataType);
	} else {
		// Handle first value
		if ($FldVal1 == EWR_NULL_VALUE || $FldOpr1 == "IS NULL") {
			$sWrk = $FldExpression . " IS NULL";
		} elseif ($FldVal1 == EWR_NOT_NULL_VALUE || $FldOpr1 == "IS NOT NULL") {
			$sWrk = $FldExpression . " IS NOT NULL";
		} else {
			$IsValidValue = ($FldDataType <> EWR_DATATYPE_NUMBER ||
				($FldDataType == EWR_DATATYPE_NUMBER && is_numeric($wrkFldVal1)));
			if ($wrkFldVal1 <> "" && $IsValidValue && ewr_IsValidOpr($FldOpr1, $FldDataType))
				$sWrk = $FldExpression . ewr_FilterString($FldOpr1, $wrkFldVal1, $FldDataType);
		}
		// Handle second value
		$sWrk2 = "";
		if ($FldVal2 == EWR_NULL_VALUE || $FldOpr2 == "IS NULL") {
			$sWrk2 = $FldExpression . " IS NULL";
		} elseif ($FldVal2 == EWR_NOT_NULL_VALUE || $FldOpr2 == "IS NOT NULL") {
			$sWrk2 = $FldExpression . " IS NOT NULL";
		} else {
			$IsValidValue = ($FldDataType <> EWR_DATATYPE_NUMBER ||
				($FldDataType == EWR_DATATYPE_NUMBER && is_numeric($wrkFldVal2)));
			if ($wrkFldVal2 <> "" && $IsValidValue && ewr_IsValidOpr($FldOpr2, $FldDataType))
				$sWrk2 = $FldExpression . ewr_FilterString($FldOpr2, $wrkFldVal2, $FldDataType);
		}
		// Combine SQL
		if ($sWrk2 <> "") {
			if ($sWrk <> "")
				$sWrk = "(" . $sWrk . " " . (($FldCond == "OR") ? "OR" : "AND") . " " . $sWrk2 . ")";
			else
				$sWrk = $sWrk2;
		}
	}
	return $sWrk;
}

// Return search string
function ewr_FilterString($FldOpr, $FldVal, $FldType) {
	if ($FldVal == EWR_NULL_VALUE || $FldOpr == "IS NULL") {
		return " IS NULL";
	} elseif ($FldVal == EWR_NOT_NULL_VALUE || $FldOpr == "IS NOT NULL") {
		return " IS NOT NULL";
	} elseif ($FldOpr == "LIKE") {
		return ewr_Like(ewr_QuotedValue("%$FldVal%", $FldType));
	} elseif ($FldOpr == "NOT LIKE") {
		return " NOT " . ewr_Like(ewr_QuotedValue("%$FldVal%", $FldType));
	} elseif ($FldOpr == "STARTS WITH") {
		return ewr_Like(ewr_QuotedValue("$FldVal%", $FldType));
	} elseif ($FldOpr == "ENDS WITH") {
		return ewr_Like(ewr_QuotedValue("%$FldVal", $FldType));
	} else {
		return " $FldOpr " . ewr_QuotedValue($FldVal, $FldType);
	}
}

// Append like operator
function ewr_Like($pat) {
<!--## if (bDBPostgreSql) { ##-->
	return ((EWR_USE_ILIKE_FOR_POSTGRESQL) ? " ILIKE " : " LIKE ") . $pat;
<!--## } else if (bDBMySql) { ##-->
	if (EWR_LIKE_COLLATION_FOR_MYSQL <> "") {
		return " LIKE " . $pat . " COLLATE " . EWR_LIKE_COLLATION_FOR_MYSQL;
	} else {
		return " LIKE " . $pat;
	}
<!--## } else if (bDBMsSql) { ##-->
	if (EWR_LIKE_COLLATION_FOR_MSSQL <> "") {
		return  " COLLATE " . EWR_LIKE_COLLATION_FOR_MSSQL . " LIKE " . $pat;
	} else {
		return " LIKE " . $pat;
	}
<!--## } else { ##-->
	return " LIKE " . $pat;
<!--## } ##-->
}

// Return date search string
function ewr_DateFilterString($FldExpr, $FldOpr, $FldVal, $FldType) {
	if ($FldOpr == "Year" && $FldVal <> "") { // Year filter
		return str_replace("%s", $FldExpr, "<!--##=ew_DbGrpSql("y",0)##-->") . " = " . $FldVal;
	} else {
		$wrkVal1 = ewr_DateVal($FldOpr, $FldVal, 1);
		$wrkVal2 = ewr_DateVal($FldOpr, $FldVal, 2);
		if ($wrkVal1 <> "" && $wrkVal2 <> "") {
			return $FldExpr . " BETWEEN " . ewr_QuotedValue($wrkVal1, $FldType) . " AND " . ewr_QuotedValue($wrkVal2, $FldType);
		} else {
			return "";
		}
	}
}

/**
 * Validation functions
 */

// Check date format
// Format: std/stdshort/us/usshort/euro/euroshort
function ewr_CheckDateEx($value, $format, $sep) {
	if (strval($value) == "") return TRUE;
	while (strpos($value, "  ") !== FALSE)
		$value = str_replace("  ", " ", $value);
	$value = trim($value);
	$arDT = explode(" ", $value);
	if (count($arDT) > 0) {
		if (preg_match('/^([0-9]{4})-([0][1-9]|[1][0-2])-([0][1-9]|[1|2][0-9]|[3][0|1])$/', $arDT[0], $matches)) { // Accept yyyy-mm-dd
			$sYear = $matches[1];
			$sMonth = $matches[2];
			$sDay = $matches[3];
		} else {
			$wrksep = "\\$sep";
			switch ($format) {
				case "std":
					$pattern = '/^([0-9]{4})' . $wrksep . '([0]?[1-9]|[1][0-2])' . $wrksep . '([0]?[1-9]|[1|2][0-9]|[3][0|1])$/';
					break;
				case "stdshort":
					$pattern = '/^([0-9]{2})' . $wrksep . '([0]?[1-9]|[1][0-2])' . $wrksep . '([0]?[1-9]|[1|2][0-9]|[3][0|1])$/';
					break;
				case "us":
					$pattern = '/^([0]?[1-9]|[1][0-2])' . $wrksep . '([0]?[1-9]|[1|2][0-9]|[3][0|1])' . $wrksep . '([0-9]{4})$/';
					break;
				case "usshort":
					$pattern = '/^([0]?[1-9]|[1][0-2])' . $wrksep . '([0]?[1-9]|[1|2][0-9]|[3][0|1])' . $wrksep . '([0-9]{2})$/';
					break;
				case "euro":
					$pattern = '/^([0]?[1-9]|[1|2][0-9]|[3][0|1])' . $wrksep . '([0]?[1-9]|[1][0-2])' . $wrksep . '([0-9]{4})$/';
					break;
				case "euroshort":
					$pattern = '/^([0]?[1-9]|[1|2][0-9]|[3][0|1])' . $wrksep . '([0]?[1-9]|[1][0-2])' . $wrksep . '([0-9]{2})$/';
					break;
			}
			if (!preg_match($pattern, $arDT[0])) return FALSE;
			$arD = explode($sep, $arDT[0]); // Change EWR_DATE_SEPARATOR to $sep
			switch ($format) {
				case "std":
				case "stdshort":
					$sYear = ewr_UnformatYear($arD[0]);
					$sMonth = $arD[1];
					$sDay = $arD[2];
					break;
				case "us":
				case "usshort":
					$sYear = ewr_UnformatYear($arD[2]);
					$sMonth = $arD[0];
					$sDay = $arD[1];
					break;
				case "euro":
				case "euroshort":
					$sYear = ewr_UnformatYear($arD[2]);
					$sMonth = $arD[1];
					$sDay = $arD[0];
					break;
			}
		}
		if (!ewr_CheckDay($sYear, $sMonth, $sDay)) return FALSE;
	}
	if (count($arDT) > 1 && !ewr_CheckTime($arDT[1])) return FALSE;
	return TRUE;
}

// Unformat 2 digit year to 4 digit year
function ewr_UnformatYear($yr) {
	if (strlen($yr) == 2) {
		if ($yr > EWR_UNFORMAT_YEAR)
			return "19" . $yr;
		else
			return "20" . $yr;
	} else {
		return $yr;
	}
}

// Check Date format (yyyy/mm/dd)
function ewr_CheckDate($value) {
	return ewr_CheckDateEx($value, "std", EWR_DATE_SEPARATOR);
}

// Check Date format (yy/mm/dd)
function ewr_CheckShortDate($value) {
	return ewr_CheckDateEx($value, "stdshort", EWR_DATE_SEPARATOR);
}

// Check US Date format (mm/dd/yyyy)
function ewr_CheckUSDate($value) {
	return ewr_CheckDateEx($value, "us", EWR_DATE_SEPARATOR);
}

// Check US Date format (mm/dd/yy)
function ewr_CheckShortUSDate($value) {
	return ewr_CheckDateEx($value, "usshort", EWR_DATE_SEPARATOR);
}

// Check Euro Date format (dd/mm/yyyy)
function ewr_CheckEuroDate($value) {
	return ewr_CheckDateEx($value, "euro", EWR_DATE_SEPARATOR);
}

// Check Euro Date format (dd/mm/yy)
function ewr_CheckShortEuroDate($value) {
	return ewr_CheckDateEx($value, "euroshort", EWR_DATE_SEPARATOR);
}

// Check day
function ewr_CheckDay($checkYear, $checkMonth, $checkDay) {
	$maxDay = 31;
	if ($checkMonth == 4 || $checkMonth == 6 ||	$checkMonth == 9 || $checkMonth == 11) {
		$maxDay = 30;
	} elseif ($checkMonth == 2)	{
		if ($checkYear % 4 > 0) {
			$maxDay = 28;
		} elseif ($checkYear % 100 == 0 && $checkYear % 400 > 0) {
			$maxDay = 28;
		} else {
			$maxDay = 29;
		}
	}
	return ewr_CheckRange($checkDay, 1, $maxDay);
}

// Check integer
function ewr_CheckInteger($value) {
	global $EWR_DEFAULT_DECIMAL_POINT;
	if (strval($value) == "") return TRUE;
	if (strpos($value, $EWR_DEFAULT_DECIMAL_POINT) !== FALSE)
		return FALSE;
	return ewr_CheckNumber($value);
}

// Check number
function ewr_CheckNumber($value) {
	global $EWR_DEFAULT_THOUSANDS_SEP, $EWR_DEFAULT_DECIMAL_POINT;
	if (strval($value) == "") return TRUE;
	$pat = '/^[+-]?(\d{1,3}(' . (($EWR_DEFAULT_THOUSANDS_SEP) ? '\\' . $EWR_DEFAULT_THOUSANDS_SEP . '?' : '') . '\d{3})*(\\' .
		$EWR_DEFAULT_DECIMAL_POINT . '\d+)?|\\' . $EWR_DEFAULT_DECIMAL_POINT . '\d+)$/';
	return preg_match($pat, $value);
}

// Check range
function ewr_CheckRange($value, $min, $max) {
	if (strval($value) == "") return TRUE;
	if (is_int($min) || is_float($min) || is_int($max) || is_float($max)) { // Number
		if (ewr_CheckNumber($value))
			$value = floatval(ewr_StrToFloat($value));
	}
	if ((!is_null($min) && $value < $min) || (!is_null($max) && $value > $max))
		return FALSE;
	return TRUE;
}

// Check time
function ewr_CheckTime($value) {
	if (strval($value) == "") return TRUE;
	return preg_match('/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $value);
}

// Check US phone number
function ewr_CheckPhone($value) {
	if (strval($value) == "") return TRUE;
	return preg_match('/^\(\d{3}\) ?\d{3}( |-)?\d{4}|^\d{3}( |-)?\d{3}( |-)?\d{4}$/', $value);
}

// Check US zip code
function ewr_CheckZip($value) {
	if (strval($value) == "") return TRUE;
	return preg_match('/^\d{5}$|^\d{5}-\d{4}$/', $value);
}

// Check credit card
function ewr_CheckCreditCard($value, $type="") {
	if (strval($value) == "") return TRUE;
	$creditcard = array("visa" => "/^4\d{3}[ -]?\d{4}[ -]?\d{4}[ -]?\d{4}$/",
		"mastercard" => "/^5[1-5]\d{2}[ -]?\d{4}[ -]?\d{4}[ -]?\d{4}$/",
		"discover" => "/^6011[ -]?\d{4}[ -]?\d{4}[ -]?\d{4}$/",
		"amex" => "/^3[4,7]\d{13}$/",
		"diners" => "/^3[0,6,8]\d{12}$/",
		"bankcard" => "/^5610[ -]?\d{4}[ -]?\d{4}[ -]?\d{4}$/",
		"jcb" => "/^[3088|3096|3112|3158|3337|3528]\d{12}$/",
		"enroute" => "/^[2014|2149]\d{11}$/",
		"switch" => "/^[4903|4911|4936|5641|6333|6759|6334|6767]\d{12}$/");
	if (empty($type))	{
		$match = FALSE;
		foreach ($creditcard as $type => $pattern) {
			if (@preg_match($pattern, $value) == 1) {
				$match = TRUE;
				break;
			}
		}
		return ($match) ? ewr_CheckSum($value) : FALSE;
	}	else {
		if (!preg_match($creditcard[strtolower(trim($type))], $value)) return FALSE;
		return ewr_CheckSum($value);
	}
}

// Check sum
function ewr_CheckSum($value) {
	$value = str_replace(array('-',' '), array('',''), $value);
	$checksum = 0;
	for ($i=(2-(strlen($value) % 2)); $i<=strlen($value); $i+=2)
		$checksum += (int)($value[$i-1]);
  for ($i=(strlen($value)%2)+1; $i <strlen($value); $i+=2) {
	  $digit = (int)($value[$i-1]) * 2;
		$checksum += ($digit < 10) ? $digit : ($digit-9);
  }
	return ($checksum % 10 == 0);
}

// Check US social security number
function ewr_CheckSSC($value) {
	if (strval($value) == "") return TRUE;
	return preg_match('/^(?!000)([0-6]\d{2}|7([0-6]\d|7[012]))([ -]?)(?!00)\d\d\3(?!0000)\d{4}$/', $value);
}

// Check emails
function ewr_CheckEmailList($value, $email_cnt) {
	if (strval($value) == "") return TRUE;
	$emailList = str_replace(",", ";", $value);
	$arEmails = explode(";", $emailList);
	$cnt = count($arEmails);
	if ($cnt > $email_cnt && $email_cnt > 0)
		return FALSE;
	foreach ($arEmails as $email) {
		if (!ewr_CheckEmail($email))
			return FALSE;
	}
	return TRUE;
}

// Check email
function ewr_CheckEmail($value) {
	if (strval($value) == "") return TRUE;
	return preg_match('/^[\w.%+-]+@[\w.-]+\.[A-Z]{2,6}$/i', trim($value));
}

// Check GUID
function ewr_CheckGUID($value) {
	if (strval($value) == "") return TRUE;
	$p1 = '/^\{\w{8}-\w{4}-\w{4}-\w{4}-\w{12}\}$/';
	$p2 = '/^\w{8}-\w{4}-\w{4}-\w{4}-\w{12}$/';
	return preg_match($p1, $value) || preg_match($p2, $value);
}

// Check by preg
function ewr_CheckByRegEx($value, $pattern) {
	if (strval($value) == "") return TRUE;
	return preg_match($pattern, $value);
}

/**
 * End Validation functions
 */

// Write the paths for config/debug only
function ewr_WritePaths() {
	global $EWR_ROOT_RELATIVE_PATH;
	echo 'DOCUMENT_ROOT=' . ewr_ServerVar("DOCUMENT_ROOT") . "<br>";
	echo 'EWR_ROOT_RELATIVE_PATH=' . $EWR_ROOT_RELATIVE_PATH . "<br>";
	echo 'ewr_AppRoot()=' . ewr_AppRoot() . "<br>";
	echo 'realpath(".")=' . realpath(".") . "<br>";
	echo '__FILE__=' . __FILE__ . "<br>";
}

// Upload path
// If PhyPath is TRUE(1), return physical path on the server
// If PhyPath is FALSE(0), return relative URL
function ewr_UploadPathEx($PhyPath, $DestPath) {
	global $EWR_ROOT_RELATIVE_PATH;
	if ($PhyPath) {
		$Path = ewr_PathCombine(ewr_AppRoot(), str_replace("/", EWR_PATH_DELIMITER, $DestPath), TRUE);
	} else {
		$Path = ewr_ScriptName();
		$Path = substr($Path, 0, strrpos($Path, "/"));
		$Path = ewr_PathCombine($Path, $EWR_ROOT_RELATIVE_PATH, FALSE);
		$Path = ewr_PathCombine(ewr_IncludeTrailingDelimiter($Path, FALSE), $DestPath, FALSE);
	}
	return ewr_IncludeTrailingDelimiter($Path, $PhyPath);
}

// Get a temp folder for temp file
function ewr_TmpFolder() {
	$tmpfolder = NULL;

	$folders = array();

	if (EWR_IS_WINDOWS) {
		$folders[] = ewr_ServerVar("TEMP");
		$folders[] = ewr_ServerVar("TMP");
	} else {
		if (EWR_UPLOAD_TMP_PATH <> "") $folders[] = ewr_AppRoot() . str_replace("/", EWR_PATH_DELIMITER, EWR_UPLOAD_TMP_PATH);
		$folders[] = '/tmp';
	}

	if (ini_get('upload_tmp_dir')) {
		$folders[] = ini_get('upload_tmp_dir');
	}

	foreach ($folders as $folder) {
		if (!$tmpfolder && is_dir($folder)) {
			$tmpfolder = $folder;
		}
	}

	//if ($tmpfolder) $tmpfolder = ewr_IncludeTrailingDelimiter($tmpfolder, TRUE);

	return $tmpfolder;
}

// Field data type
function ewr_FieldDataType($fldtype) {
	switch ($fldtype) {
		case 20:
		case 3:
		case 2:
		case 16:
		case 4:
		case 5:
		case 131:
		case 139:
		case 6:
		case 17:
		case 18:
		case 19:
		case 21: // Numeric
			return EWR_DATATYPE_NUMBER;
		case 7:
		case 133:
		case 135: // Date
		case 146: // DateTiemOffset
			return EWR_DATATYPE_DATE;
		case 134: // Time
		case 145: // Time
			return EWR_DATATYPE_TIME;
		case 201:
		case 203: // Memo
			return EWR_DATATYPE_MEMO;
		case 129:
		case 130:
		case 200:
		case 202: // String
			return EWR_DATATYPE_STRING;
		case 11: // Boolean
			return EWR_DATATYPE_BOOLEAN;
		case 72: // GUID
			return EWR_DATATYPE_GUID;
		case 128:
		case 204:
		case 205: // Binary
			return EWR_DATATYPE_BLOB;
		//case 141: // XML
		//	return EWR_DATATYPE_XML;
		default:
			return EWR_DATATYPE_OTHER;
	}
}

// Application root
function ewr_AppRoot() {
	global $EWR_ROOT_RELATIVE_PATH;

	// 1. use root relative path
	if ($EWR_ROOT_RELATIVE_PATH <> "") {
		$Path = realpath($EWR_ROOT_RELATIVE_PATH);
		$Path = str_replace("\\\\", EWR_PATH_DELIMITER, $Path);
	} else {
		$Path = realpath(".");
	}

	// 2. if empty, use the document root if available
	if (empty($Path)) $Path = ewr_ServerVar("DOCUMENT_ROOT");

	// 3. if empty, use current folder
	if (empty($Path)) $Path = realpath(".");

	// 4. use custom path, uncomment the following line and enter your path
	// E.g. $Path = 'C:\Inetpub\wwwroot\MyWebRoot'; // Windows
	//$Path = 'enter your path here';

	if (empty($Path)) die("Path of website root unknown.");

	return ewr_IncludeTrailingDelimiter($Path, TRUE);
}

// Get path relative to application root
function ewr_ServerMapPath($Path) {
	return ewr_PathCombine(ewr_AppRoot(), $Path, TRUE);
}

// Get path relative to a base path
function ewr_PathCombine($BasePath, $RelPath, $PhyPath) {
	$BasePath = ewr_RemoveTrailingDelimiter($BasePath, $PhyPath);
	if ($PhyPath) {
		$Delimiter = EWR_PATH_DELIMITER;
		$RelPath = str_replace('/', EWR_PATH_DELIMITER, $RelPath);
		$RelPath = str_replace('\\', EWR_PATH_DELIMITER, $RelPath);
	} else {
		$Delimiter = '/';
		$RelPath = str_replace('\\', '/', $RelPath);
	}
	if ($RelPath == '.' || $RelPath == '..') $RelPath .= $Delimiter;
	$p1 = strpos($RelPath, $Delimiter);
	$Path2 = "";
	while ($p1 !== FALSE) {
		$Path = substr($RelPath, 0, $p1 + 1);
		if ($Path == $Delimiter || $Path == ".$Delimiter") {
			// Skip
		} elseif ($Path == "..$Delimiter") {
			$p2 = strrpos($BasePath, $Delimiter);
			if ($p2 === 0) { // BasePath = "/xxx", cannot move up
				$BasePath = $Delimiter;
			} elseif ($p2 !== FALSE && substr($BasePath, -2) <> "..")
				$BasePath = substr($BasePath, 0, $p2);
			elseif ($BasePath <> "" && $BasePath <> "..")
				$BasePath = "";
			else
				$Path2 .= ".." . $Delimiter;
		} else {
			$Path2 .= $Path;
		}
		$RelPath = substr($RelPath, $p1+1);
		if ($RelPath === FALSE)
			$RelPath = "";
		$p1 = strpos($RelPath, $Delimiter);
	}
	return (($BasePath === "") ? "" : ewr_IncludeTrailingDelimiter($BasePath, $PhyPath)) . $Path2 . $RelPath;
}

// Remove the last delimiter for a path
function ewr_RemoveTrailingDelimiter($Path, $PhyPath) {
	$Delimiter = ($PhyPath) ? EWR_PATH_DELIMITER : '/';
	while (substr($Path, -1) == $Delimiter)
		$Path = substr($Path, 0, strlen($Path)-1);
	return $Path;
}

// Include the last delimiter for a path
function ewr_IncludeTrailingDelimiter($Path, $PhyPath) {
	$Path = ewr_RemoveTrailingDelimiter($Path, $PhyPath);
	$Delimiter = ($PhyPath) ? EWR_PATH_DELIMITER : '/';
	return $Path . $Delimiter;
}

// Create folder
function ewr_CreateFolder($dir, $mode = 0777) {
	return (is_dir($dir) || @mkdir($dir, $mode, TRUE));
}

// Save file
function ewr_SaveFile($folder, $fn, $filedata) {
	$res = FALSE;
	if (ewr_CreateFolder($folder)) {
		if ($handle = fopen($folder . $fn, 'w')) { // P6
			$res = fwrite($handle, $filedata);
    	fclose($handle);
		}
		if ($res)
			chmod($folder . $fn, EWR_UPLOADED_FILE_MODE);
	}
	return $res;
}

// Init array
function &ewr_InitArray($len, $value) {
	if ($len > 0)
		$ar = array_fill(0, $len, $value);
	else
		$ar = array();
	return $ar;
}

// Init 2D array
function &ewr_Init2DArray($len1, $len2, $value) {
	return ewr_InitArray($len1, ewr_InitArray($len2, $value));
}

// Function to generate random number
function ewr_Random() {
	return mt_rand();
}

// Check if float format
function ewr_IsFloatFormat($FldType) {
	return ($FldType == 4 || $FldType == 5 || $FldType == 131 || $FldType == 6);
}

// Convert string to float
function ewr_StrToFloat($v) {
	global $EWR_DEFAULT_THOUSANDS_SEP, $EWR_DEFAULT_DECIMAL_POINT;
	$v = str_replace(" ", "", $v);
	$v = str_replace(array($EWR_DEFAULT_THOUSANDS_SEP, $EWR_DEFAULT_DECIMAL_POINT), array("", "."), $v);
	return $v;
}

// Convert different data type value
function ewr_Conv($v, $t) {

	switch ($t) {
	case 2:
	case 3:
	case 16:
	case 17:
	case 18:
	case 19: //  adSmallInt/adInteger/adTinyInt/adUnsignedTinyInt/adUnsignedSmallInt
		return (is_null($v)) ? NULL : intval($v);
	case 4:
	Case 5:
	case 6:
	case 131:
	case 139: //  adSingle/adDouble/adCurrency/adNumeric/adVarNumeric
		return (is_null($v)) ? NULL : (float)$v;
	default:
		return (is_null($v)) ? NULL : $v;
	}

}

// Convert byte array to binary string
function ewr_BytesToStr($bytes) {
	$str = "";
	foreach ($bytes as $byte)
		$str .= chr($byte);
	return $str;
}

// Create temp image file from binary data
function ewr_TmpImage(&$filedata) {
	global $gTmpImages;
	$export = "";
	if (@$_GET["export"] <> "")
		$export = $_GET["export"];
	elseif (@$_POST["export"] <> "")
		$export = $_POST["export"];
	elseif (@$_POST["customexport"] <> "")
		$export = $_POST["customexport"];
//  $f = tempnam(ew_TmpFolder(), "tmp");
	$folder = ewr_AppRoot() . EWR_UPLOAD_DEST_PATH;
	$f = tempnam($folder, "tmp");
	$handle = fopen($f, 'w+');
	fwrite($handle, $filedata);
	fclose($handle);
	$info = getimagesize($f);
	switch ($info[2]) {
	case 1:
		rename($f, $f .= '.gif'); break;
	case 2:
		rename($f, $f .= '.jpg'); break;
	case 3:
		rename($f, $f .= '.png'); break;
	case 6:
		rename($f, $f .= '.bmp'); break;
	default:
		return "";
	}
	$tmpimage = basename($f);
	$gTmpImages[] = $tmpimage;
	//return ewr_TmpImageLnk($tmpimage);
	return ewr_TmpImageLnk($tmpimage, $export);
}

// Get temp chart image
function ewr_TmpChartImage($id, $custom = FALSE) {
	global $gTmpImages;
	$exportid = "";
	if (@$_GET["exportid"] <> "")
		$exportid = $_GET["exportid"];
	elseif (@$_POST["exportid"] <> "")
		$exportid = $_POST["exportid"];
	$export = "";
	if ($custom)
		$export = "print";
	elseif (@$_GET["export"] <> "")
		$export = $_GET["export"];
	elseif (@$_POST["export"] <> "")
		$export = $_POST["export"];
	if ($exportid <> "") {
		$file = $exportid . "_" . $id . ".png"; // v8
		$folder = ewr_AppRoot() . EWR_UPLOAD_DEST_PATH;
		$f = $folder . $file;
		if (file_exists($f)) {
			$tmpimage = basename($f);
			$gTmpImages[] = $tmpimage;
			//return ewr_TmpImageLnk($tmpimage);
			return ewr_TmpImageLnk($tmpimage, $export);
		}
		return "";
	}
}

// Delete temp images
function ewr_DeleteTmpImages($html = "") {
	global $gTmpImages;
	foreach ($gTmpImages as $tmpimage)
		@unlink(ewr_AppRoot() . EWR_UPLOAD_DEST_PATH . $tmpimage);
	// Check and remove temp images from html content (start with session id)
	if (preg_match_all('/<img([^>]*)>/i', $html, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $match) {
			if (preg_match('/\s+src\s*=\s*[\'"]([\s\S]*?)[\'"]/i', $match[1], $submatches)) { // Match src='src'
				$src = $submatches[1];
				$exportid = session_id();
				$src = basename($src);
				if (substr($src,0,strlen($exportid)) == $exportid || substr($src,0,3) == "tmp") { // Temp image
					@unlink(ewr_AppRoot() . EWR_UPLOAD_DEST_PATH . $src);
				}
			}
		}
	}
}

// Get temp image link
function ewr_TmpImageLnk($file, $lnktype = "") {
	global $EWR_ROOT_RELATIVE_PATH;
	if ($file == "") return "";
	if ($lnktype == "email" || $lnktype == "cid") {
		$ar = explode('.', $file);
		$lnk = implode(".", array_slice($ar, 0, count($ar)-1));
		if ($lnktype == "email") $lnk = "cid:" . $lnk;
		return $lnk;
	} else {
		$fn = EWR_UPLOAD_DEST_PATH . $file;
		if ($EWR_ROOT_RELATIVE_PATH <> ".") $fn = $EWR_ROOT_RELATIVE_PATH . "/" . $fn;
		return $fn;
	}
}

// Check empty string
function ewr_EmptyStr($value) {
	$str = strval($value);
	$str = str_replace("&nbsp;", "", $str);
	return (trim($str) == "");
}

// Get File View Tag
function ewr_GetFileViewTag($fld, $fn) {
	global $Page;
	if (!ewr_EmptyStr($fn)) {
		if ($Page->Export == "word" && !defined('EWR_USE_PHPWORD') || $Page->Export == "excel" && !defined('EWR_USE_PHPEXCEL')) {
			if ($fld->FldDataType == EWR_DATATYPE_BLOB) {
				$name = $fld->FldCaption();
			} else {
				$ar = parse_url($fn);
				$name = @basename($ar["path"]);
				if (@$ar["query"] <> "") {
					parse_str($ar["query"], $query);
					if (@$query["fn"] <> "")
						$name = basename($query["fn"]);
				}
			}
			$fld->HrefValue = ewr_ConvertFullUrl($fn);
			return "<div><a" . $fld->LinkAttributes() . ">" . $name . "</a></div>";
		} elseif ($fld->IsBlobImage || ewr_IsImageFile($fn)) {
			if ($fld->HrefValue == "" && $fld->DrillDownUrl == "" && !$fld->UseColorbox) {
				return "<img class=\"ewImage\" src=\"" . $fn . "\" alt=\"\"" . $fld->ViewAttributes() . ">";
			} else {
				return "<a" . $fld->LinkAttributes() . "><img class=\"ewImage\" src=\"" . $fn . "\" alt=\"\"" . $fld->ViewAttributes() . "></a>";
			}
		} else {
			if ($fld->FldDataType == EWR_DATATYPE_BLOB)
				$name = $fld->FldCaption();
			else
				$name = basename($fn);
			$fld->HrefValue = $fn;
			return "<div><a" . $fld->LinkAttributes() . ">" . $name . "</a></div>";
		}
	} else {
		return "";
	}
}

// Check if image file
function ewr_IsImageFile($fn) {
	if ($fn <> "") {
		if (substr($fn,0,4) == "cid:") // Embedded image for email
			return TRUE;
		$ar = parse_url($fn);
		if ($ar && array_key_exists('query', $ar)) { // Thumbnail url
 			if ($q = parse_str($ar['query']))
				$fn = $q['fn'];
		}
		$pathinfo = pathinfo($fn);
		$ext = strtolower(@$pathinfo["extension"]);
		return in_array($ext, explode(",", EWR_IMAGE_ALLOWED_FILE_EXT));
	} else {
		return FALSE;
	}
}

// HTTP request by cURL
// Note: cURL must be enabled in PHP
function ewr_ClientUrl($url, $postdata = "", $method = "GET") {
	global $data;
	if (!function_exists("curl_init"))
		die("cURL not installed.");
	$ch = curl_init();
	$method = strtoupper($method);
	if ($method == "POST") {
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	} elseif ($method == "GET") {
		curl_setopt($ch, CURLOPT_URL, $url . "?" . $postdata);
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$res = curl_exec($ch);
	curl_close($ch);
	return $res;
}
?>
<!--##/session##-->


<!--##session thumbnailfunction##-->
<?php
/**
 * Functions for image resize
 */
 
// Resize binary to thumbnail
function ewr_ResizeBinary($filedata, &$width, &$height, $quality) {
	return TRUE; // No resize
}

// Resize file to thumbnail file
function ewr_ResizeFile($fn, $tn, &$width, &$height, $quality) {
	if (file_exists($fn)) { // Copy only
		return ($fn <> $tn) ? copy($fn, $tn) : TRUE;
	} else {
		return FALSE;
	}
}

// Resize file to binary
function ewr_ResizeFileToBinary($fn, &$width, &$height, $quality) {
	return file_get_contents($fn); // Return original file content only
}
?>
<!--##/session##-->