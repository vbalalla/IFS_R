<?php
<!--##session phpmain##-->

	//
	// Page main
	//
	function Page_Main() {
		global $ReportLanguage;
		global $Security;

		$sUsername = $Security->CurrentUserName();

		<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Other","User_LoggingOut")) { ##-->
		// Call User LoggingOut event
		$bValidate = $this->User_LoggingOut($sUsername);
		<!--## } else { ##-->
		$bValidate = TRUE;
		<!--## } ##-->

		if (!$bValidate) {
		
			$sLastUrl = $Security->LastUrl();
			if ($sLastUrl == "")
				$sLastUrl = "<!--##=sFnDefault##-->";
			$this->Page_Terminate($sLastUrl); // Go to last accessed URL
		
		} else {

			if (@$_COOKIE[EWR_PROJECT_VAR]['AutoLogin'] == "") // Not autologin
				setcookie(EWR_PROJECT_VAR . '[Username]', ""); // Clear user name cookie
			setcookie(EWR_PROJECT_VAR . '[Password]', ""); // Clear password cookie
			setcookie(EWR_PROJECT_VAR . '[LastUrl]', ""); // Clear last url

			<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Other","User_LoggedOut")) { ##-->
			// Call User LoggedOut event
			$this->User_LoggedOut($sUsername);
			<!--## } ##-->

			// Unset all of the session variables
			$_SESSION = array();

			// Delete the session cookie and kill the session
			if (isset($_COOKIE[session_name()]))
				setcookie(session_name(), '', time()-42000, '/');

			// Finally, destroy the session
			@session_destroy();

			$this->Page_Terminate("<!--##=sFnLogin##-->");
		}

	}

<!--##/session##-->
?>

<?php
<!--##session phpevents##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Other","User_LoggingOut")##-->
	<!--##~SYSTEMFUNCTIONS.GetServerScript("Other","User_LoggedOut")##-->
<!--##/session##-->
?>