<?php
<!--##session report_pdf_function##-->
<!--##
	if (bExportPdf) {
##-->

	// Export to PDF
	function ExportPdf($html) {
		ob_end_clean();
		echo($html);
		ewr_DeleteTmpImages($html);
		exit();
	}

<!--##
	};
##-->
<!--##/session##-->
?>
