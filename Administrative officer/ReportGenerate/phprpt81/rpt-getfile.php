<!--##session phpconfig##-->
<!--##
	BLOBFIELD = FIELD; // Save field object
##-->
<!--##/session##-->

<?php
<!--##session phpmain##-->
<!--##
	FIELD = BLOBFIELD; // Restore field object
##-->

	//
	// Page main
	//
	function Page_Main() {
		global $conn;
		
		$sSqlFrom = "<!--##=ew_Quote(sFrom)##-->";
		$sSqlSelect = "SELECT <!--##=ew_Quote(sSelect)##--> FROM " . $sSqlFrom;
	<!--## if (ew_IsEmpty(sWhere)) { ##-->
		$sSqlWhere = "";
	<!--## } else { ##-->
		$sSqlWhere = <!--##=sWhere##-->;
	<!--## } ##-->
		$sSqlGroupBy = "<!--##=ew_Quote(sGroupBy)##-->";
		$sSqlHaving = "";
		$sSqlOrderBy = "<!--##=ew_Quote(sOrderBy)##-->";

		// Get key
		$sFilter = "";
		<!--##
			ORIFIELD = FIELD;
			for (var i = 0, len = arKeyFlds.length; i < len; i++) {
				if (GetFldObj(arKeyFlds[i])) {
		##-->
		if (@$_GET["<!--##=gsFldParm##-->"] <> "") {
			$<!--##=gsFldParm##--> = ewr_StripSlashes($_GET["<!--##=gsFldParm##-->"]);
			if ($sFilter <> "") $sFilter .= " AND ";
			$sFilter .= "<!--##=ew_Quote(gsFld)##--> = " . ewr_QuotedValue($<!--##=gsFldParm##-->, <!--##=GetFieldTypeName(goFld.FldType)##-->);
		} else {
			$this->Page_Terminate(); // Exit
			exit();
		}
		<!--##
				}
			}
			FIELD = ORIFIELD;
			goFld = goTblFlds.Fields[FIELD.FldName];
		##-->

		<!--##
			thumbnailwidth = FIELD.FldTagImgWidth; // Default width
			thumbnailheight = FIELD.FldTagImgHeight; // Default height
			if (thumbnailwidth <= 0 && thumbnailheight <= 0) {
				thumbnailwidth = "EWR_THUMBNAIL_DEFAULT_WIDTH";
			 	thumbnailheight = "EWR_THUMBNAIL_DEFAULT_HEIGHT";
			}
			quality = FIELD.FldResizeQuality;
			if (quality == "" || isNaN(quality)) {
				quality = "EWR_THUMBNAIL_DEFAULT_QUALITY"; // Default quality
			} else if (parseInt(quality) <= 0) {
				quality = "EWR_THUMBNAIL_DEFAULT_QUALITY"; // Default quality
			}
		##-->

		// Show thumbnail
		$bShowThumbnail = (@$_GET["showthumbnail"] == "1");

		if (@$_GET["thumbnailwidth"] == "" && @$_GET["thumbnailheight"] == "") {
			$iThumbnailWidth = <!--##=thumbnailwidth##-->; // Set default width
			$iThumbnailHeight = <!--##=thumbnailheight##-->; // Set default height
		} else {
			if (@$_GET["thumbnailwidth"] <> "") {
				$iThumbnailWidth = $_GET["thumbnailwidth"];
				if (!is_numeric($iThumbnailWidth) || $iThumbnailWidth < 0) $iThumbnailWidth = 0;
			}
			if (@$_GET["thumbnailheight"] <> "") {
				$iThumbnailHeight = $_GET["thumbnailheight"];
				if (!is_numeric($iThumbnailHeight) || $iThumbnailHeight < 0) $iThumbnailHeight = 0;
			}
		}

		if (@$_GET["quality"] <> "") {
			$quality = $_GET["quality"];
			if (!is_numeric($quality)) $quality = <!--##=quality##-->; // Set Default
		} else {
			$quality = <!--##=quality##-->;
		}

		$sSql = ewr_BuildReportSql($sSqlSelect, $sSqlWhere, $sSqlGroupBy, $sSqlHaving, $sSqlOrderBy, $sFilter, "");

		if ($rs = $conn->Execute($sSql)) {

			if (!$rs->EOF) {
				if (!EWR_DEBUG_ENABLED && ob_get_length())
					ob_end_clean();

				$data = $rs->fields('<!--##=ew_SQuote(FIELD.FldName)##-->');

			<!--## if (!bDBMySql) { ##-->

				if (is_array($data) || is_object($data)) // Byte array
					$data = ewr_BytesToStr($data);

			<!--## } else {	##-->

				//$data = $data;

			<!--## } ##-->

				if ($bShowThumbnail) {
					ewr_ResizeBinary($data, $iThumbnailWidth, $iThumbnailHeight, $quality);
				}

			<!--## 
				var sFileName = ew_IsNotEmpty(FIELD.FileNameFld) ? ", $rs->fields('" + ew_SQuote(FIELD.FileNameFld) + "')" : "";
				if (ew_IsNotEmpty(FIELD.FileTypeFld)) {
			##-->

				if (trim(strval($rs->fields('<!--##=ew_SQuote(FIELD.FileTypeFld)##-->'))) <> "") {
					header("Content-type: ". $rs->fields('<!--##=ew_SQuote(FIELD.FileTypeFld)##-->'));
				} else {
					if (strpos(ewr_ServerVar("HTTP_USER_AGENT"), "MSIE") === FALSE)
					 header("Content-type: " . ewr_ContentType(substr($data, 0, 11)<!--##=sFileName##-->));
				}

			<!--##
				} else {
			##-->

				if (strpos(ewr_ServerVar("HTTP_USER_AGENT"), "MSIE") === FALSE)
					header("Content-type: " . ewr_ContentType(substr($data, 0, 11)<!--##=sFileName##-->));

			<!--##
				}
			##-->

			<!--## if (ew_IsNotEmpty(FIELD.FileNameFld)) { ##-->

				if (trim(strval($rs->fields('<!--##=ew_SQuote(FIELD.FileNameFld)##-->'))) <> "") {
					header("Content-Disposition: attachment; filename=" . $rs->fields('<!--##=ew_SQuote(FIELD.FileNameFld)##-->'));
				}

			<!--## } ##-->

				if (substr($data, 0, 2) == "PK" && strpos($data, "[Content_Types].xml") > 0 &&
					strpos($data, "_rels") > 0 && strpos($data, "docProps") > 0) { // Fix Office 2007 documents
					if (substr($data, -4) <> "\0\0\0\0")
						$data .= "\0\0\0\0";
				}

				ob_clean();
				echo $data;

			}

			$rs->Close();
		}

	}
<!--##/session##-->
?>