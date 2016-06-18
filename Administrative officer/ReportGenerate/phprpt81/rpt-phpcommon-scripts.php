<!--##session phpload##-->
<!--## if (!PROJ.Cache) { ##-->
<?php ewr_Header(FALSE) ?>
<!--## } else { ##-->
<?php ewr_Header(TRUE) ?>
<!--## } ##-->
<?php
// Create page object
if (!isset($<!--##=sPageObj##-->)) $<!--##=sPageObj##--> = new cr<!--##=sPageObj##-->();
if (isset($<!--##=gsPageObj##-->)) $OldPage = $<!--##=gsPageObj##-->;
$<!--##=gsPageObj##--> = &$<!--##=sPageObj##-->;

// Page init
$<!--##=gsPageObj##-->->Page_Init();

// Page main
$<!--##=gsPageObj##-->->Page_Main();

<!--##
	if (CTRL.CtrlType.toLowerCase() == "table" || CTRL.CtrlType.toLowerCase() == "report" || CTRL.CtrlType.toLowerCase() == "other" && CTRL.CtrlID.toLowerCase() != "logout") {
		if (SYSTEMFUNCTIONS.ServerScriptExist("Global","Page_Rendering")) {
##-->
// Global Page Rendering event (in ewrusrfn*.php)
Page_Rendering();
<!--##
		}

		if (SYSTEMFUNCTIONS.ServerScriptExist(sCtrlType,"Page_Render") && CTRL.CtrlID != "custom") {
##-->
// Page Rendering event
$<!--##=gsPageObj##-->->Page_Render();
<!--##
		}
	}
##-->
?>
<!--##/session##-->

<!--##session phpunload##-->
<?php
$<!--##=gsPageObj##-->->Page_Terminate();
if (isset($OldPage)) $<!--##=gsPageObj##--> = $OldPage;
?>
<!--##/session##-->

<!--##session phppageclassbegin##-->
<!--##
	bExtendPageClass = (CTRL.CtrlType.toLowerCase() == "table" || CTRL.CtrlType.toLowerCase() == "report" && CTRL.CtrlID.toLowerCase() != "custom" || CTRL.CtrlType.toLowerCase() == "field" || (bUserTable && CTRL.CtrlID.toLowerCase() == "login"));
##-->
<?php
//
// Page class
//
$<!--##=sPageObj##--> = NULL; // Initialize page object first
<!--## if (bExtendPageClass) { ##-->
class cr<!--##=sPageObj##--> extends cr<!--##=gsTblVar##--> {
<!--## } else { ##-->
class cr<!--##=sPageObj##--> {
<!--## } ##-->

	// Page ID
	var $PageID = '<!--##=CTRL.CtrlID##-->';

	// Project ID
	var $ProjectID = "<!--##=PROJ.ProjID##-->";

	// Page object name
	var $PageObjName = '<!--##=sPageObj##-->';

	// Page name
	function PageName() {
		return ewr_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ewr_CurrentPage() . "?";
<!--## if (CTRL.CtrlType.toLowerCase() == "table" || CTRL.CtrlType.toLowerCase() == "report" && CTRL.CtrlID.toLowerCase() != "custom") { ##-->
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
<!--## } ##-->
		return $PageUrl;
	}

<!--## if (CTRL.CtrlType.toLowerCase() == "table" || CTRL.CtrlType.toLowerCase() == "report" && CTRL.CtrlID.toLowerCase() != "custom") { ##-->
	// Export URLs
	var $ExportPrintUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportPdfUrl;
	var $ReportTableClass;
	var $ReportTableStyle = "";

	// Custom export
	var $ExportPrintCustom = <!--##=ew_Val(bUseCustomTemplate)##-->;
	var $ExportExcelCustom = <!--##=ew_Val(bUseCustomTemplate)##-->;
	var $ExportWordCustom = <!--##=ew_Val(bUseCustomTemplate)##-->;
	var $ExportPdfCustom = <!--##=ew_Val(bUseCustomTemplate)##-->;
	var $ExportEmailCustom = <!--##=ew_Val(bUseCustomTemplate)##-->;

<!--## } ##-->

	// Message
	function getMessage() {
		return @$_SESSION[EWR_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EWR_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EWR_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EWR_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_WARNING_MESSAGE], $v);
	}
	
		// Show message
	function ShowMessage() {
		$hidden = <!--##=ew_Val(bUseJavaScriptMessage)##-->;
		$html = "";
		// Message
		$sMessage = $this->getMessage();
	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist(sCtrlType,"Message_Showing")) { ##-->
		$this->Message_Showing($sMessage, "");
	<!--## } ##-->
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EWR_SESSION_MESSAGE] = ""; // Clear message in Session
		}
		// Warning message
		$sWarningMessage = $this->getWarningMessage();
	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist(sCtrlType,"Message_Showing")) { ##-->
		$this->Message_Showing($sWarningMessage, "warning");
	<!--## } ##-->
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EWR_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}
		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist(sCtrlType,"Message_Showing")) { ##-->
		$this->Message_Showing($sSuccessMessage, "success");
	<!--## } ##-->
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EWR_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}
		// Failure message
		$sErrorMessage = $this->getFailureMessage();
	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist(sCtrlType,"Message_Showing")) { ##-->
		$this->Message_Showing($sErrorMessage, "failure");
	<!--## } ##-->
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EWR_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog ewDisplayTable\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	
<!--## if (CTRL.CtrlType.toLowerCase() == "table" || CTRL.CtrlType.toLowerCase() == "report" && CTRL.CtrlID.toLowerCase() != "custom" || CTRL.CtrlType.toLowerCase() == "other") { ##-->
	var $PageHeader;
	var $PageFooter;
	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist(sCtrlType,"Page_DataRendering")) { ##-->
		$this->Page_DataRendering($sHeader);
	<!--## } ##-->
		if ($sHeader <> "") // Header exists, display
			echo $sHeader;
	}
	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist(sCtrlType,"Page_DataRendered")) { ##-->
		$this->Page_DataRendered($sFooter);
	<!--## } ##-->
		if ($sFooter <> "") // Fotoer exists, display
			echo $sFooter;
	}
<!--## } ##-->

	// Validate page request
	function IsPageRequest() {
<!--## if (CTRL.CtrlType.toLowerCase() == "table" || CTRL.CtrlType.toLowerCase() == "report" && CTRL.CtrlID.toLowerCase() != "custom") { ##-->
		if ($this->UseTokenInUrl) {
			if (ewr_IsHttpPost())
				return ($this->TableVar == @$_POST("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == @$_GET["t"]);
		} else {
			return TRUE;
		}
<!--## } else { ##-->
		return TRUE;
<!--## } ##-->
	}
	
	var $Token = "";
	var $CheckToken = EWR_CHECK_TOKEN;
	var $CheckTokenFn = "ewr_CheckToken";
	var $CreateTokenFn = "ewr_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ewr_IsHttpPost())
			return TRUE;
		if (!isset($_POST[EWR_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EWR_TOKEN_NAME]);
		return FALSE;
	}

	// Create Token
	function CreateToken() {
		global $gsToken;
		if ($this->CheckToken) {
			$fn = $this->CreateTokenFn;
			if ($this->Token == "" && is_callable($fn)) // Create token
				$this->Token = $fn();
			$gsToken = $this->Token; // Save to global variable
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $ReportLanguage;
		
		// Language object
		$ReportLanguage = new crLanguage();

<!--## if (bExtendPageClass) { ##-->
		// Parent constuctor
		parent::__construct();

		// Table object (<!--##=gsTblVar##-->)
		if (!isset($GLOBALS["<!--##=gsTblVar##-->"])) {

			$GLOBALS["<!--##=gsTblVar##-->"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["<!--##=gsTblVar##-->"];

		}

<!--## } ##-->

<!--## if (CTRL.CtrlType.toLowerCase() == "table" || CTRL.CtrlType.toLowerCase() == "report" && CTRL.CtrlID.toLowerCase() != "custom") { ##-->

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";

	<!--##
		// Initialize other table objects
		for (var tmpTblVar in dIncludeTable) {
	##-->
		// Table object (<!--##=tmpTblVar##-->)
		if (!isset($GLOBALS["<!--##=tmpTblVar##-->"])) $GLOBALS["<!--##=tmpTblVar##-->"] = new cr<!--##=tmpTblVar##-->();
	<!--##
		}
	##-->

<!--## } ##-->

		// Page ID
		if (!defined("EWR_PAGE_ID"))
			define("EWR_PAGE_ID", '<!--##=CTRL.CtrlID##-->', TRUE);

<!--## if (CTRL.CtrlType.toLowerCase() == "table" || CTRL.CtrlType.toLowerCase() == "report" || CTRL.CtrlType.toLowerCase() == "field") { ##-->
		// Table name (for backward compatibility)
		if (!defined("EWR_TABLE_NAME"))
			define("EWR_TABLE_NAME", '<!--##=ew_SQuote(TABLE.TblName)##-->', TRUE);
<!--## } ##-->

		// Start timer
		$GLOBALS["gsTimer"] = new crTimer();

		// Open connection
		$conn = ewr_Connect();

<!--## if (CTRL.CtrlType.toLowerCase() == "table" || CTRL.CtrlType.toLowerCase() == "report" && CTRL.CtrlID.toLowerCase() != "custom") { ##-->
		// Export options
		$this->ExportOptions = new crListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Search options
		$this->SearchOptions = new crListOptions();
		$this->SearchOptions->Tag = "div";
		$this->SearchOptions->TagClassName = "ewSearchOption";
		
		// Filter options
		$this->FilterOptions = new crListOptions();
		$this->FilterOptions->Tag = "div";
		$this->FilterOptions->TagClassName = "ewFilterOption <!--##=sFormName##-->";
<!--## } ##-->

	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $gsEmailContentType, $ReportLanguage, $Security;
		global $gsCustomExport;

<!--## if (bSecurityEnabled) { ##-->
		// Security
		$Security = new crAdvancedSecurity();
		<!--##~SYSTEMFUNCTIONS.Security()##-->
<!--## } ##-->

<!--##
	if (CTRL.CtrlType.toLowerCase() == "table" || CTRL.CtrlType.toLowerCase() == "report" && CTRL.CtrlID.toLowerCase() != "custom") {
##-->

		// Get export parameters
		if (@$_GET["export"] <> "")
			$this->Export = strtolower($_GET["export"]);
		elseif (@$_POST["export"] <> "")
			$this->Export = strtolower($_POST["export"]);

	<!--## if (bUseCustomTemplate) { ##-->

		// Get custom export parameters
		if ($this->Export <> "" && @$_GET["custom"] <> "") {
			$this->CustomExport = $this->Export;
			$this->Export = "print";
		}
		$gsCustomExport = $this->CustomExport;

		// Custom export (post back from ewr_ApplyTemplate), export and terminate page
		if (@$_POST["customexport"] <> "") {
			$this->CustomExport = $_POST["customexport"];
			$this->Export = $this->CustomExport;
			$this->Page_Terminate();
			exit();
		}

	<!--## } ##-->

		$gsExport = $this->Export; // Get export parameter, used in header
		$gsExportFile = $this->TableVar; // Get export file, used in header
		$gsEmailContentType = @$_POST["contenttype"]; // Get email content type

	<!--## if (sUsePlaceHolder != "" && sUsePlaceHolder != "None") { ##-->
		// Setup placeholder
	<!--##
		for (var i = 0; i < nFldCount; i++) {
			if (GetFldObj(arFlds[i])) {
				// Text filters
				if (IsExtendedFilter(goFld) && IsTextFilter(goFld)) {
	##-->
		$this-><!--##=gsFldParm##-->->PlaceHolder = $this-><!--##=gsFldParm##-->->FldCaption();
	<!--##
				}
			} // End text filters
		}
	##-->
	<!--## } ##-->

<!--##
	}
##-->

<!--## if (CTRL.CtrlType.toLowerCase() == "table" || CTRL.CtrlType.toLowerCase() == "report" && CTRL.CtrlID.toLowerCase() != "custom" && TABLE.TblReportType != "dashboard") { ##-->

		// Setup export options
		$this->SetupExportOptions();

<!--## } ##-->

<!--## if (CTRL.CtrlSkipHeaderFooter) { ##-->
		global $gbOldSkipHeaderFooter, $gbSkipHeaderFooter;
		$gbOldSkipHeaderFooter = $gbSkipHeaderFooter;
		$gbSkipHeaderFooter = TRUE;
<!--## } ##-->

<!--##
	if (CTRL.CtrlType.toLowerCase() != "field") {
		if (SYSTEMFUNCTIONS.ServerScriptExist("Global","Page_Loading")) {
##-->
		// Global Page Loading event (in userfn*.php)
		Page_Loading();
<!--##
		}
	}
##-->

<!--##
	if (SYSTEMFUNCTIONS.ServerScriptExist(sCtrlType,"Page_Load") && CTRL.CtrlID != "custom") {
##-->
		// Page Load event
		$this->Page_Load();
<!--##
	}
##-->

		// Check token
		if (!$this->ValidPost()) {
			echo $ReportLanguage->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Create Token
		$this->CreateToken();

	}

<!--##
	if (CTRL.CtrlType.toLowerCase() == "table" || CTRL.CtrlType.toLowerCase() == "report" && CTRL.CtrlID.toLowerCase() != "custom" && TABLE.TblReportType != "dashboard") {
##-->
	// Set up export options
	function SetupExportOptions() {
		global $ReportLanguage;

		$exportid = session_id();

	<!--## if (bUseCustomTemplate) { ##-->
		// Update Export URLs
		if ($this->ExportPrintCustom)
			$this->ExportPrintUrl .= "&amp;custom=1";
		if (defined("EWR_USE_PHPEXCEL"))
			$this->ExportExcelCustom = FALSE;
		if ($this->ExportExcelCustom)
			$this->ExportExcelUrl .= "&amp;custom=1";
		if (defined("EWR_USE_PHPWORD"))
			$this->ExportWordCustom = FALSE;
		if ($this->ExportWordCustom)
			$this->ExportWordUrl .= "&amp;custom=1";
		if ($this->ExportPdfCustom)
			$this->ExportPdfUrl .= "&amp;custom=1";
	<!--## } ##-->

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");

	<!--## if (bUseCustomTemplate) { ##-->
		if ($this->ExportPrintCustom)
			$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly", TRUE)) . "\" href=\"javascript:void(0);\" onclick=\"ewr_ExportCharts(this, '" . $this->ExportPrintUrl . "', '" . $exportid . "');\">" . <!--##=sPrinterFriendlyCaption##--> . "</a>";
		else
			$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly"), TRUE) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly", TRUE)) . "\" href=\"" . $this->ExportPrintUrl . "\">" . <!--##=sPrinterFriendlyCaption##--> . "</a>";
	<!--## } else { ##-->
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly", TRUE)) . "\" href=\"" . $this->ExportPrintUrl . "\">" . <!--##=sPrinterFriendlyCaption##--> . "</a>";
	<!--## } ##-->

		$item->Visible = <!--##=ew_Val(bPrinterFriendly && (bShowReport || bShowChart))##-->;

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");

	<!--## if (bUseCustomTemplate) { ##-->
		if ($this->ExportExcelCustom)
			$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" href=\"javascript:void(0);\" onclick=\"ewr_ExportCharts(this, '" . $this->ExportExcelUrl . "', '" . $exportid . "');\">" . <!--##=sExportToExcelCaption##--> . "</a>";
		else
			$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" href=\"" . $this->ExportExcelUrl . "\">" . <!--##=sExportToExcelCaption##--> . "</a>";
	<!--## } else { ##-->
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" href=\"" . $this->ExportExcelUrl . "\">" . <!--##=sExportToExcelCaption##--> . "</a>";
	<!--## } ##-->

		$item->Visible = <!--##=ew_Val(bExportExcel && (bShowReport || bExportChart && UsePhpExcel() && CTRL.CtrlID != "gantt"))##-->;

		// Export to Word
		$item = &$this->ExportOptions->Add("word");

	<!--## if (bUseCustomTemplate) { ##-->
		if ($this->ExportWordCustom)
			$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" href=\"javascript:void(0);\" onclick=\"ewr_ExportCharts(this, '" . $this->ExportWordUrl . "', '" . $exportid . "');\">" . <!--##=sExportToWordCaption##--> . "</a>";
		else
			$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" href=\"" . $this->ExportWordUrl . "\">" . <!--##=sExportToWordCaption##--> . "</a>";
	<!--## } else { ##-->
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" href=\"" . $this->ExportWordUrl . "\">" . <!--##=sExportToWordCaption##--> . "</a>";
	<!--## } ##-->

		//$item->Visible = <!--##=ew_Val(bExportWord && bShowReport)##-->;
		$item->Visible = <!--##=ew_Val(bExportWord && (bShowReport || bExportChart && UsePhpWord() && CTRL.CtrlID != "gantt"))##-->;


		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" href=\"" . $this->ExportPdfUrl . "\">" . <!--##=sExportToPdfCaption##--> . "</a>";
		$item->Visible = FALSE;
		// Uncomment codes below to show export to Pdf link
//		$item->Visible = <!--##=ew_Val(bExportPdf && (bShowReport || bExportChart))##-->;

		// Export to Email
		$item = &$this->ExportOptions->Add("email");
		$url = $this->PageUrl() . "export=email";
	<!--## if (bUseCustomTemplate) { ##-->
		if ($this->ExportEmailCustom)
			$url .= "&amp;custom=1";
	<!--## } ##-->
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" id=\"emf_<!--##=gsTblVar##-->\" href=\"javascript:void(0);\" onclick=\"ewr_EmailDialogShow({lnk:'emf_<!--##=gsTblVar##-->',hdr:ewLanguage.Phrase('ExportToEmail'),url:'$url',exportid:'$exportid',el:this});\">" . <!--##=sExportToEmailCaption##--> . "</a>";
		$item->Visible = <!--##=ew_Val(bExportEmail && (bShowReport || bExportChart))##-->;

		// Drop down button for export
		$this->ExportOptions->UseDropDownButton = <!--##=ew_Val(bUseDropDownForExport)##-->;
		$this->ExportOptions->UseButtonGroup = TRUE;
		$this->ExportOptions->UseImageAndText = $this->ExportOptions->UseDropDownButton;
		$this->ExportOptions->DropDownButtonPhrase = $ReportLanguage->Phrase("ButtonExport");

		// Add group option item
		$item = &$this->ExportOptions->Add($this->ExportOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
		
	<!--##
		var sSearchToggleClass = PROJ.GetV("SearchPanelCollapsed") ? "" : " active";
	##-->
		
		// Filter panel button
		$item = &$this->SearchOptions->Add("searchtoggle");
		$SearchToggleClass = "<!--##=sSearchToggleClass##-->";
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-caption=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-toggle=\"button\" data-form=\"<!--##=sFormName##-->\">" . $ReportLanguage->Phrase("SearchBtn") . "</button>";
		$item->Visible = <!--##=ew_Val(bShowYearSelection || nExtFilterFlds > 0)##-->;

		// Reset filter
		$item = &$this->SearchOptions->Add("resetfilter");
		$item->Body = "<button type=\"button\" class=\"btn btn-default\" title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ResetAllFilter", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ResetAllFilter", TRUE)) . "\" onclick=\"location='" . ewr_CurrentPage() . "?cmd=reset'\">" . <!--##=sResetAllFilterCaption##--> . "</button>";
		$item->Visible = <!--##=ew_Val(bShowYearSelection || nSearchFlds > 0 || nExtFilterFlds > 0)##-->;

		// Button group for reset filter
		$this->SearchOptions->UseButtonGroup = TRUE;

		// Add group option item
		$item = &$this->SearchOptions->Add($this->SearchOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
		
		// Filter button
		$item = &$this->FilterOptions->Add("savecurrentfilter");
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"<!--##=sFormName##-->\" href=\"#\">" . $ReportLanguage->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"<!--##=sFormName##-->\" href=\"#\">" . $ReportLanguage->Phrase("DeleteFilter") . "</a>";
		$item->Visible = TRUE;
		$this->FilterOptions->UseDropDownButton = TRUE;
		$this->FilterOptions->UseButtonGroup = !$this->FilterOptions->UseDropDownButton; // v8
		$this->FilterOptions->DropDownButtonPhrase = $ReportLanguage->Phrase("Filters");
		
		// Add group option item
		$item = &$this->FilterOptions->Add($this->FilterOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Set up options (extended)
		$this->SetupExportOptionsExt();

		// Hide options for export
		if ($this->Export <> "") {
			$this->ExportOptions->HideAllOptions();
			$this->SearchOptions->HideAllOptions();
			$this->FilterOptions->HideAllOptions();
		}

		// Set up table class
		if ($this->Export == "word" || $this->Export == "excel" || $this->Export == "pdf")
			$this->ReportTableClass = "ewTable";
		else
			$this->ReportTableClass = "table ewTable";

	<!--## if (bUseCustomTemplate) { ##-->
		// Hide main table for custom layout
		if ($this->Export <> "")
			$this->ReportTableStyle = " style=\"display: none;\"";
	<!--## } ##-->

	}
<!--##
	}
##-->

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn, $ReportLanguage, $EWR_EXPORT, $gsExportFile;

<!--## if (CTRL.CtrlSkipHeaderFooter) { ##-->
		global $gbOldSkipHeaderFooter, $gbSkipHeaderFooter;
		$gbSkipHeaderFooter = $gbOldSkipHeaderFooter;
<!--## } ##-->

	<!--## if (bUseCustomTemplate) { ##-->
		if (@$_POST["customexport"] == "") {
	<!--## } ##-->

	<!--##
		if (SYSTEMFUNCTIONS.ServerScriptExist(sCtrlType,"Page_Unload") && CTRL.CtrlID != "custom") {
	##-->
		// Page Unload event
		$this->Page_Unload();
	<!--##
		}
	##-->
	<!--##
		if (CTRL.CtrlType.toLowerCase() != "field") {
			if (SYSTEMFUNCTIONS.ServerScriptExist("Global","Page_Unloaded")) {
	##-->
		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();
	<!--##
			}
		}
	##-->

	<!--## if (bUseCustomTemplate) { ##-->
		}
	<!--## } ##-->

	<!--##
		if (CTRL.CtrlType.toLowerCase() == "table" || CTRL.CtrlType.toLowerCase() == "report" && CTRL.CtrlID.toLowerCase() != "custom") {
	##-->
		// Export
		if ($this->Export <> "" && array_key_exists($this->Export, $EWR_EXPORT)) {
	<!--## if (bUseCustomTemplate) { ##-->
			if (@$_POST["data"] <> "") {
				$sContent = $_POST["data"];
				$gsExportFile = @$_POST["filename"];
				if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			} else {
				$sContent = ob_get_contents();
			}
	<!--## } else { ##-->
			$sContent = ob_get_contents();
	<!--## } ##-->
			// Remove all <div data-tagid="..." id="orig..." class="hide">...</div> (for customviewtag export, except "googlemaps")
			if (preg_match_all('/<div\s+data-tagid=[\'"]([\s\S]*?)[\'"]\s+id=[\'"]orig([\s\S]*?)[\'"]\s+class\s*=\s*[\'"]hide[\'"]>([\s\S]*?)<\/div\s*>/i', $sContent, $divmatches, PREG_SET_ORDER)) {
				foreach ($divmatches as $divmatch) {
					if ($divmatch[1] <> "googlemaps")
						$sContent = str_replace($divmatch[0], '', $sContent);
				}
			}
			$fn = $EWR_EXPORT[$this->Export];
			if ($this->Export == "email") { // Email
				ob_end_clean();
				echo $this->$fn($sContent);
				$conn->Close(); // Close connection
				exit();
			} else {
				$this->$fn($sContent);
			}
		}
	<!--##
		}
	##-->

		 // Close connection
		$conn->Close();

		// Go to URL if specified
		if ($url <> "") {
			if (!EWR_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}

<!--## if (!CTRL.CtrlSkipHeaderFooter) { ##-->
		exit();
<!--## } ##-->

	}
<!--##/session##-->
?>


<?php
<!--##session setupexportoptionsext-start##-->
	function SetupExportOptionsExt() {
		global $ReportLanguage;
<!--##/session##-->

<!--##session setupexportoptionsext-end##-->
	}
<!--##/session##-->
?>


<?php
<!--##session report_export_functions##-->

<!--##include rpt-email.php/report_email_function##-->
<!--##include rpt-html.php/report_html_function##-->
<!--##include rpt-word.php/report_word_function##-->
<!--##include rpt-excel.php/report_excel_function##-->
<!--##include rpt-pdf.php/report_pdf_function##-->


<!--##/session##-->
?>


<?php
<!--##session phpevents##-->
<!--## if (CTRL.CtrlID != "custom") { ##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript(sCtrlType,"Page_Load")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript(sCtrlType,"Page_Unload")##-->
<!--## } ##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript(sCtrlType,"Message_Showing")##-->

<!--## if (CTRL.CtrlType.toLowerCase() == "table" || CTRL.CtrlType.toLowerCase() == "report" && CTRL.CtrlID.toLowerCase() != "custom" || CTRL.CtrlType.toLowerCase() == "other" && CTRL.CtrlID.toLowerCase() != "logout") { ##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript(sCtrlType,"Page_Render")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript(sCtrlType,"Page_DataRendering")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript(sCtrlType,"Page_DataRendered")##-->
<!--## } ##-->

<!--##/session##-->
?>

<?php
<!--##session phppageclassend##-->
}
?>
<!--##/session##-->

<!--##session clientscript##-->
<!--##
	switch (CTRL.CtrlType.toLowerCase()) {
		case "table":
			sCtrlType = "Table"; break;
		case "report":
			sCtrlType = "Table"; break;
		case "other":
			sCtrlType = "Other"; break;
		default:
			sCtrlType = ""; break;
	}
##-->
<!--##~SYSTEMFUNCTIONS.GetClientScript(sCtrlType,"Client Script")##-->
<!--##/session##-->
<!--##session clientstartupscript##-->
<!--##~SYSTEMFUNCTIONS.GetClientScript(sCtrlType,"Startup Script")##-->
<!--##/session##-->