<!--##session exportemail_htm##-->
<form id="ewrEmailForm" class="form-horizontal ewForm ewEmailForm" action="<?php echo ewr_CurrentPage() ?>">
<?php if ($Page->CheckToken) { ?>
<input type="hidden" name="<?php echo EWR_TOKEN_NAME ?>" value="<?php echo $Page->Token ?>">
<?php } ?>
<input type="hidden" name="export" value="email">
	<div class="form-group">
		<label class="<!--##=ewBootstrapLabelClass##-->" for="ewrSender"><!--##@EmailFormSender##--></label>
		<div class="<!--##=ewBootstrapRightColumnClass##-->"><input type="text" class="<!--##=ewBootstrapInputClass##-->" name="sender" id="ewrSender"></div>
	</div>
	<div class="form-group">
		<label class="<!--##=ewBootstrapLabelClass##-->" for="ewrRecipient"><!--##@EmailFormRecipient##--></label>
		<div class="<!--##=ewBootstrapRightColumnClass##-->"><input type="text" class="<!--##=ewBootstrapInputClass##-->" name="recipient" id="ewrRecipient"></div>
	</div>
	<div class="form-group">
		<label class="<!--##=ewBootstrapLabelClass##-->" for="ewrCc"><!--##@EmailFormCc##--></label>
		<div class="<!--##=ewBootstrapRightColumnClass##-->"><input type="text" class="<!--##=ewBootstrapInputClass##-->" name="cc" id="ewrCc"></div>
	</div>
	<div class="form-group">
		<label class="<!--##=ewBootstrapLabelClass##-->" for="ewrBcc"><!--##@EmailFormBcc##--></label>
		<div class="<!--##=ewBootstrapRightColumnClass##-->"><input type="text" class="<!--##=ewBootstrapInputClass##-->" name="bcc" id="ewrBcc"></div>
	</div>
	<div class="form-group">
		<label class="<!--##=ewBootstrapLabelClass##-->" for="ewrSubject"><!--##@EmailFormSubject##--></label>
		<div class="<!--##=ewBootstrapRightColumnClass##-->"><input type="text" class="<!--##=ewBootstrapInputClass##-->" name="subject" id="ewrSubject"></div>
	</div>
	<div class="form-group">
		<label class="<!--##=ewBootstrapLabelClass##-->" for="ewrMessage"><!--##@EmailFormMessage##--></label>
		<div class="<!--##=ewBootstrapRightColumnClass##-->"><textarea class="<!--##=ewBootstrapInputClass##-->" rows="6" name="message" id="ewrMessage"></textarea></div>
		</div>
<!--
	<div class="form-group">
		<label class="<!--##=ewBootstrapLabelClass##-->"><!--##@EmailFormContentType##--></label>
		<div class="<!--##=ewBootstrapRightColumnClass##-->">
		<label class="radio-inline ewRadio" style="white-space: nowrap;"><input type="radio" name="contenttype" value="html" checked="checked"><!--##@EmailFormContentTypeHtml##--></label>
		<label class="radio-inline ewRadio" style="white-space: nowrap;"><input type="radio" name="contenttype" value="url"><!--##@EmailFormContentTypeUrl##--></label>
	</div>
-->
	<input type="hidden" name="contenttype" value="html">
</form>
<!--##/session##-->

<?php
<!--##session report_email_function##-->
<!--##
	if (bExportEmail) {
##-->

	// Export email
	function ExportEmail($EmailContent) {
		global $gTmpImages, $ReportLanguage;

		$sContentType = @$_POST["contenttype"];
		$sSender = @$_POST["sender"];
		$sRecipient = @$_POST["recipient"];
		$sCc = @$_POST["cc"];
		$sBcc = @$_POST["bcc"];
		
		// Subject
		$sSubject = ewr_StripSlashes(@$_POST["subject"]);
		$sEmailSubject = $sSubject;
		
		// Message
		$sContent = ewr_StripSlashes(@$_POST["message"]);
		$sEmailMessage = $sContent;

		// Check sender
		if ($sSender == "")
			return "<p class=\"text-error\">" . $ReportLanguage->Phrase("EnterSenderEmail") . "</p>";

		if (!ewr_CheckEmail($sSender))
			return "<p class=\"text-error\">" . $ReportLanguage->Phrase("EnterProperSenderEmail") . "</p>";
	
		// Check recipient
		if (!ewr_CheckEmailList($sRecipient, EWR_MAX_EMAIL_RECIPIENT))
			return "<p class=\"text-error\">" . $ReportLanguage->Phrase("EnterProperRecipientEmail") . "</p>";

		// Check cc
		if (!ewr_CheckEmailList($sCc, EWR_MAX_EMAIL_RECIPIENT))
			return "<p class=\"text-error\">" . $ReportLanguage->Phrase("EnterProperCcEmail") . "</p>";

		// Check bcc
		if (!ewr_CheckEmailList($sBcc, EWR_MAX_EMAIL_RECIPIENT))
			return "<p class=\"text-error\">" . $ReportLanguage->Phrase("EnterProperBccEmail") . "</p>";

		// Check email sent count
		$emailcount = ewr_LoadEmailCount();
		if (intval($emailcount) >= EWR_MAX_EMAIL_SENT_COUNT)
			return "<p class=\"text-error\">" . $ReportLanguage->Phrase("ExceedMaxEmailExport") . "</p>";

		if ($sEmailMessage <> "") {
			if (EWR_REMOVE_XSS) $sEmailMessage = ewr_RemoveXSS($sEmailMessage);
			$sEmailMessage .= ($sContentType == "url") ? "\r\n\r\n" : "<br><br>";
		}
		$sAttachmentContent = ewr_CleanEmailContent($EmailContent);
		$sAppPath = ewr_FullUrl();
		$sAppPath = substr($sAppPath, 0, strrpos($sAppPath, "/")+1);
		if (strpos($sAttachmentContent, "<head>") !== FALSE)
			$sAttachmentContent = str_replace("<head>", "<head><base href=\"" . $sAppPath . "\">", $sAttachmentContent); // Add <base href> statement inside the header
		else
			$sAttachmentContent = "<base href=\"" . $sAppPath . "\">" . $sAttachmentContent; // Add <base href> statement as the first statement

		//$sAttachmentFile = $this->TableVar . "_" . Date("YmdHis") . ".html";
		$sAttachmentFile = $this->TableVar . "_" . Date("YmdHis") . "_" . ewr_Random() . ".html";
		if ($sContentType == "url") {
			ewr_SaveFile(EWR_UPLOAD_DEST_PATH, $sAttachmentFile, $sAttachmentContent);
			$sAttachmentFile = EWR_UPLOAD_DEST_PATH . $sAttachmentFile;
			$sUrl = $sAppPath . $sAttachmentFile;
			$sEmailMessage .= $sUrl; // Send URL only
			$sAttachmentFile = "";
			$sAttachmentContent = "";
		} else {
			$sEmailMessage .= $sAttachmentContent;
		<!--## if (bUseCustomTemplate) { ##-->
			// Replace images in custom template
			if (preg_match_all('/<img([^>]*)>/i', $sEmailMessage, $matches, PREG_SET_ORDER)) {
				foreach ($matches as $match) {
					if (preg_match('/\s+src\s*=\s*[\'"]([\s\S]*?)[\'"]/i', $match[1], $submatches)) { // Match src='src'
						$src = $submatches[1];
						// Add embedded temp image if not in gTmpImages
						if (substr($src,0,4) == "cid:") {
							$tmpimage = substr($src,4);
							if (substr($tmpimage,0,3) == "tmp") {
								// Add file extension
								$addimage = FALSE;
								if (file_exists(ewr_AppRoot() . EWR_UPLOAD_DEST_PATH . $tmpimage . ".gif")) {
									$tmpimage .= ".gif";
									$addimage = TRUE;
								} elseif (file_exists(ewr_AppRoot() . EWR_UPLOAD_DEST_PATH . $tmpimage . ".jpg")) {
									$tmpimage .= ".jpg";
									$addimage = TRUE;
								} elseif (file_exists(ewr_AppRoot() . EWR_UPLOAD_DEST_PATH . $tmpimage . ".png")) {
									$tmpimage .= ".png";
									$addimage = TRUE;
								}
								// Add to gTmpImages
								if ($addimage) {
									foreach ($gTmpImages as $tmpimage2)
										if ($tmpimage == $tmpimage2)
											$addimage = FALSE;
									if ($addimage)
										$gTmpImages[] = $tmpimage;
								}
							}
						// Not embedded image, create temp image
						} else {
							$data = @file_get_contents($src);
							if ($data <> "")
								$sEmailMessage = str_replace($match[0], "<img src=\"" . ewr_TmpImage($data) . "\">", $sEmailMessage);
						}
					}
				}
			}
		<!--## } ##-->
			$sAttachmentFile = "";
			$sAttachmentContent = "";
		}

		// Send email
		$Email = new crEmail();
		$Email->Sender = $sSender; // Sender
		$Email->Recipient = $sRecipient; // Recipient
		$Email->Cc = $sCc; // Cc
		$Email->Bcc = $sBcc; // Bcc
		$Email->Subject = $sEmailSubject; // Subject
		$Email->Content = $sEmailMessage; // Content
		if ($sAttachmentFile <> "")
			$Email->AddAttachment($sAttachmentFile, $sAttachmentContent);
		if ($sContentType <> "url") {
			foreach ($gTmpImages as $tmpimage)
				$Email->AddEmbeddedImage($tmpimage);
		}
		$Email->Format = ($sContentType == "url") ? "text" : "html";
		$Email->Charset = EWR_EMAIL_CHARSET;

<!--## if (SYSTEMFUNCTIONS.ServerScriptExist("Table","Email_Sending")) { ##-->
		$EventArgs = array();
		$bEmailSent = FALSE;
		if ($this->Email_Sending($Email, $EventArgs))
			$bEmailSent = $Email->Send();
<!--## } else { ##-->
		$bEmailSent = $Email->Send();
<!--## } ##-->

		ewr_DeleteTmpImages($EmailContent);

		// Check email sent status
		if ($bEmailSent) {
			// Update email sent count and write log
			ewr_AddEmailLog($sSender, $sRecipient, $sEmailSubject, $sEmailMessage);
			// Sent email success
			return "<p class=\"text-success\">" . $ReportLanguage->Phrase("SendEmailSuccess") . "</p>"; // Set up success message
		} else {
			// Sent email failure
			return "<p class=\"text-error\">" . $Email->SendErrDescription . "</p>";
		}

	}

<!--##
	};
##-->
<!--##/session##-->
?>
