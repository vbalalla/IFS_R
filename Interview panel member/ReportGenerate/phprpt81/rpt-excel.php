<?php
<!--##session report_excel_function##-->
<!--##
	if (bExportExcel) {
##-->

	// Export to EXCEL
	function ExportExcel($html) {
		global $gsExportFile;
		header('Content-Type: application/vnd.ms-excel' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
		header('Content-Disposition: attachment; filename=' . $gsExportFile . '.xls');

	<!--## if (bUseCustomTemplate) { ##-->
		// Replace images in custom template to hyperlinks
		if (preg_match_all('/<img([^>]*)>/i', $html, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				if (preg_match('/\s+src\s*=\s*[\'"]([\s\S]*?)[\'"]/i', $match[1], $submatches)) { // Match src='src'
					$src = $submatches[1];
					$html = str_replace($match[0], "<a class=\"ewExportLink\" href=\"" . ewr_ConvertFullUrl($src) . "\">" . $src . "</a>", $html);
				}
			}
		}
	<!--## } ##-->

		echo $html;
	}

<!--##
	};
##-->
<!--##/session##-->
?>