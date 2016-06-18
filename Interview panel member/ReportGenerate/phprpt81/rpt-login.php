<!--##session currenttable##-->
<!--##
	// Set security table current
	if (ew_IsNotEmpty(PROJ.SecTbl)) {
		TABLE = DB.Tables(PROJ.SecTbl);
		goFlds = goTblFlds.Fields;
		gsTblVar = TABLE.TblVar;
		sTblObj = gsTblVar;
	}
	
	sLoginOption = PROJ.LoginOption;
	if (sLoginOption == "") sLoginOption = "AUTO,USER,ASK";
	arLoginOption = sLoginOption.split(",");
	dLoginOption = {};
	lLoginOptionCount = 0;
	for (var i = 0; i < arLoginOption.length; i++) {
		sOption = arLoginOption[i].trim();
		if (sOption == "AUTO" || sOption == "USER" || sOption == "ASK") {
			if (!(sOption in dLoginOption)) {
				dLoginOption[sOption] = sOption;
				lLoginOptionCount += 1;
			}
		}
	}
	
	sExpStart = "";
	sExpEnd = "";
	sBreadcrumbCheckStart = "";
	sBreadcrumbCheckEnd = "";
##-->
<!--##/session##-->


<?php
<!--##session phpmain##-->

<!--##include rpt-captcha.php/phpcaptcha_var##-->

	var $Username;
	var $LoginType;

	//
	// Page main
	//
	function Page_Main() {
		global $Security, $ReportLanguage, $gsFormError, $ReportBreadcrumb;
		
		$url = substr(ewr_CurrentUrl(), strrpos(ewr_CurrentUrl(), "/")+1);
		$ReportBreadcrumb = new crBreadcrumb;
		$ReportBreadcrumb->Add("<!--##=CTRL.CtrlID##-->", "LoginPage", $url, "", "", TRUE);
		
		$sPassword = "";
		$sLastUrl = $Security->LastUrl(); // Get last URL
		if ($sLastUrl == "")
			$sLastUrl = "<!--##=sFnDefault##-->";

		if (!$Security->IsLoggedIn())
			$Security->AutoLogin();

	<!--## if (bUserLevel) { ##-->
		$Security->LoadUserLevel(); // Load user level
	<!--## } ##-->

		$this->Username = ""; // Initialize
		if (@$_POST["username"] <> "") {
			// Setup variables
			$this->Username = ewr_RemoveXSS(ewr_StripSlashes(@$_POST["username"]));
			$sPassword = ewr_RemoveXSS(ewr_StripSlashes(@$_POST["password"]));
			$this->LoginType = strtolower(ewr_RemoveXSS(@$_POST["type"]));
<!--## if (PROJ.GetV("AllowLoginByUrl")) { ##-->
		} else if (@$_GET["username"] <> "") {
			// Setup variables
			$this->Username = ewr_RemoveXSS(ewr_StripSlashes(@$_GET["username"]));
			$sPassword = ewr_RemoveXSS(ewr_StripSlashes(@$_GET["password"]));
			$this->LoginType = strtolower(ewr_RemoveXSS(@$_GET["type"]));
<!--## } ##-->
		}

		if ($this->Username <> "") {

			$bValidate = $this->ValidateForm($this->Username, $sPassword);
			if (!$bValidate)
				$this->setFailureMessage($gsFormError);

		} else {

			if ($Security->IsLoggedIn()) {
				if ($this->getFailureMessage() == "")
					$this->Page_Terminate($sLastUrl); // Return to last accessed page
			}

			$bValidate = FALSE;

			// Restore settings
			if (@$_COOKIE[EWR_PROJECT_NAME]['Checksum'] == strval(crc32(md5(EWR_RANDOM_KEY))))
				$this->Username = ewr_Decrypt(@$_COOKIE[EWR_PROJECT_NAME]['Username'], EWR_RANDOM_KEY);
			if (@$_COOKIE[EWR_PROJECT_NAME]['AutoLogin'] == "autologin") {
				$this->LoginType = "a";
			} elseif (@$_COOKIE[EWR_PROJECT_NAME]['AutoLogin'] == "rememberusername") {
				$this->LoginType = "u";
			} else {
				$this->LoginType = "";
			}

		}

		$bValidPwd = FALSE;

		<!--##include rpt-captcha.php/phpcaptcha_php##-->

		if ($bValidate) {

		<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Other","User_LoggingIn")) { ##-->
			// Call Logging In event
			$bValidate = $this->User_LoggingIn($this->Username, $sPassword);
		<!--## } else { ##-->
			$bValidate = TRUE;
		<!--## } ##-->
		
			if ($bValidate) {
				$bValidPwd = $Security->ValidateUser($this->Username, $sPassword, FALSE); // Manual login
				if (!$bValidPwd) {
					if ($this->getFailureMessage() == "")
						$this->setFailureMessage($ReportLanguage->Phrase("InvalidUidPwd")); // Invalid user id/password
				}
			} else {
				if ($this->getFailureMessage() == "")
					$this->setFailureMessage($ReportLanguage->Phrase("LoginCancelled")); // Login cancelled
			}
		}

		if ($bValidPwd) {
			// Write cookies
			if ($this->LoginType == "a") { // Auto login
				setcookie(EWR_PROJECT_VAR . '[AutoLogin]',  "autologin", EWR_COOKIE_EXPIRY_TIME); // Set autologin cookie
				setcookie(EWR_PROJECT_VAR . '[Username]', ewr_Encrypt($this->Username, EWR_RANDOM_KEY), EWR_COOKIE_EXPIRY_TIME); // Set user name cookie
				setcookie(EWR_PROJECT_VAR . '[Password]', ewr_Encrypt($sPassword, EWR_RANDOM_KEY), EWR_COOKIE_EXPIRY_TIME); // Set password cookie
				setcookie(EWR_PROJECT_VAR . '[Checksum]', crc32(md5(EWR_RANDOM_KEY)), EWR_COOKIE_EXPIRY_TIME);
			} elseif ($this->LoginType == "u") { // Remember user name
				setcookie(EWR_PROJECT_VAR . '[AutoLogin]', "rememberusername", EWR_COOKIE_EXPIRY_TIME); // Set remember user name cookie
				setcookie(EWR_PROJECT_VAR . '[Username]', ewr_Encrypt($this->Username, EWR_RANDOM_KEY), EWR_COOKIE_EXPIRY_TIME); // Set user name cookie
				setcookie(EWR_PROJECT_VAR . '[Checksum]', crc32(md5(EWR_RANDOM_KEY)), EWR_COOKIE_EXPIRY_TIME);
			} else {
				setcookie(EWR_PROJECT_VAR . '[AutoLogin]', "", EWR_COOKIE_EXPIRY_TIME); // Clear auto login cookie
			}

		<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Other","User_LoggedIn")) { ##-->
			// Call loggedin event
			$this->User_LoggedIn($this->Username);
		<!--## } ##-->

			$this->Page_Terminate($sLastUrl); // Return to last accessed URL

		} elseif ($this->Username <> "" && $sPassword <> "") {

<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Other","User_LoginError")) { ##-->
			// Call user login error event
			$this->User_LoginError($this->Username, $sPassword);
<!--## } ##-->

		}

	}

<!--##/session##-->
?>


<!--##session login_htm##-->
<script type="text/javascript">

var <!--##=sFormName##--> = new ewr_Form("<!--##=sFormName##-->");

// Validate method
<!--##=sFormName##-->.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	if (!ewr_HasValue(fobj.username))
		return this.OnError(fobj.username, ewLanguage.Phrase("EnterUid"));
	if (!ewr_HasValue(fobj.password))
		return this.OnError(fobj.password, ewLanguage.Phrase("EnterPwd"));

<!--##include rpt-captcha.php/phpcaptcha_js##-->

<!--## if (SYSTEMFUNCTIONS.ClientScriptExist("Other","Form_CustomValidate")) { ##-->
	// Call Form Custom Validate event
	if (!this.Form_CustomValidate(fobj)) return false;
<!--## } ##-->

	return true;
}

<!--## if (SYSTEMFUNCTIONS.ClientScriptExist("Other","Form_CustomValidate")) { ##-->
// Form_CustomValidate method
<!--##=sFormName##-->.Form_CustomValidate = <!--##~SYSTEMFUNCTIONS.GetClientScript("Other","Form_CustomValidate")##-->
<!--## } ##-->

// Requires js validation
<?php if (EWR_CLIENT_VALIDATE) { ?>
<!--##=sFormName##-->.ValidateRequired = true;
<?php } else { ?>
<!--##=sFormName##-->.ValidateRequired = false;
<?php } ?>

</script>

<div class="ewToolbar">
<!--##include rpt-phpcommon.php/breadcrumb##-->
<!--##include rpt-phpcommon.php/language##-->
<div class="clearfix"></div>
</div>

<!--##include rpt-phpcommon.php/header-message##-->
<!--##include rpt-phpcommon.php/common-message##-->

<form name="<!--##=sFormName##-->" id="<!--##=sFormName##-->" class="form-horizontal ewForm ewLoginForm" action="<?php echo ewr_CurrentPage() ?>" method="post">
<?php if ($Page->CheckToken) { ?>
<input type="hidden" name="<?php echo EWR_TOKEN_NAME ?>" value="<?php echo $Page->Token ?>">
<?php } ?>
<!--##
	sPlaceHolder = (sUsePlaceHolder == "Caption") ? " placeholder=\"<?php echo ewr_HtmlEncode($ReportLanguage->Phrase(\"Username\")) ?>\"" : "";
##-->
	<div class="form-group">
		<label class="<!--##=ewBootstrapLabelClass##-->" for="username"><!--##@Username##--></label>
		<div class="<!--##=ewBootstrapRightColumnClass##-->"><input type="text" name="username" id="username" class="<!--##=ewBootstrapInputClass##-->" value="<?php echo ewr_HtmlEncode($<!--##=sPageObj##-->->Username) ?>"<!--##=sPlaceHolder##-->></div>
	</div>
<!--##
	sPlaceHolder = (sUsePlaceHolder == "Caption") ? " placeholder=\"<?php echo ewr_HtmlEncode($ReportLanguage->Phrase(\"Password\")) ?>\"" : "";
##-->
	<div class="form-group">
		<label class="<!--##=ewBootstrapLabelClass##-->" for="password"><!--##@Password##--></label>
		<div class="<!--##=ewBootstrapRightColumnClass##-->"><input type="password" name="password" id="password" class="<!--##=ewBootstrapInputClass##-->"<!--##=sPlaceHolder##-->></div>
	</div>
<!--## if (lLoginOptionCount > 1) { ##--> 
	<div class="form-group">
		<div class="<!--##=ewBootstrapOffsetClass##-->">
			<a id="ewLoginOptions" class="collapsed" data-toggle="collapse" data-target="#<!--##=sFormName##-->_options"><?php echo $ReportLanguage->Phrase("LoginOptions") ?> <span class="icon-arrow"></span></a>
			<div id="<!--##=sFormName##-->_options" class="collapse">
		<!--## if (String("AUTO") in dLoginOption) { ##-->
					<div class="radio ewRadio">
					<label for="type1"><input type="radio" name="type" id="type1" value="a"<?php if ($<!--##=sPageObj##-->->LoginType == "a") { ?> checked="checked"<?php } ?>><!--##@AutoLogin##--></label>
					</div>
		<!--## } ##-->
		<!--## if (String("USER") in dLoginOption) { ##-->
					<div class="radio ewRadio">
					<label for="type2"><input type="radio" name="type" id="type2" value="u"<?php if ($<!--##=sPageObj##-->->LoginType == "u") { ?>  checked="checked"<?php } ?>><!--##@SaveUserName##--></label>
					</div>
		<!--## } ##-->
		<!--## if (String("ASK") in dLoginOption) { ##-->
					<div class="radio ewRadio">
					<label for="type3"><input type="radio" name="type" id="type3" value=""<?php if ($<!--##=sPageObj##-->->LoginType == "") { ?> checked="checked"<?php } ?>><!--##@AlwaysAsk##--></label>
					</div>
		<!--## } ##-->
		</div>
	</div>
	</div>
<!--## } ##-->

<!--##include rpt-captcha.php/phpcaptcha_htm##-->

	<div class="form-group">
		<div class="<!--##=ewBootstrapOffsetClass##-->">
			<button class="<!--##=sSubmitButtonClass##-->" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $ReportLanguage->Phrase("Login") ?></button>
		</div>
	</div>

<!--## if (lLoginOptionCount == 1) { ##-->
	<!--## if (String("AUTO") in dLoginOption) { ##-->
		<input type="hidden" name="type" value="a">
	<!--## } else if (String("USER") in dLoginOption) { ##-->
		<input type="hidden" name="type" value="u">
	<!--## } else if (String("ASK") in dLoginOption) { ##-->
		<input type="hidden" name="type" value="">
	<!--## } ##-->
<!--## } ##-->

</form>

<script type="text/javascript">
<!--##=sFormName##-->.Init();
</script>

<!--##include rpt-phpcommon.php/footer-message##-->
<!--##/session##-->


<?php
<!--##session phpfunction##-->

	//
	// Validate form
	//
	function ValidateForm($usr, $pwd) {
		global $ReportLanguage, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EWR_SERVER_VALIDATE)
			return TRUE;

		if (trim($usr) == "") {
			ew_AddMessage($gsFormError, $ReportLanguage->Phrase("EnterUid"));
		}

		if (trim($pwd) == "") {
			ew_AddMessage($gsFormError, $ReportLanguage->Phrase("EnterPwd"));
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

	<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Other","Form_CustomValidate")) { ##-->
		// Call Form Custom Validate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
	<!--## } ##-->

		return $ValidateForm;

	}

<!--##/session##-->
?>


<?php
<!--##session phpevents##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Other","User_LoggingIn")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Other","User_LoggedIn")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Other","User_LoginError")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Other","Form_CustomValidate")##-->
<!--##/session##-->
?>