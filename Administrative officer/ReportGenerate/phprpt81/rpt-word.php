<?php
<!--##session report_word_function##-->
<!--##
	if (bExportWord) {
##-->

	// Export to WORD
	function ExportWord($html) {
		global $gsExportFile;
		header('Content-Type: application/vnd.ms-word' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
		header('Content-Disposition: attachment; filename=' . $gsExportFile . '.doc');

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