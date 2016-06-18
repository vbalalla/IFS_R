<!--##session report_menu##-->
<!--##
	bGenReportMenu = false;
	sMenuIdPrefix = "mi_";
	sReportMenuIdPrefix = "mri_";
	sCustomMenuIdPrefix = "mci_";
##-->
<!--## if (!bGenCompatHeader) { ##-->

<!-- Begin Main Menu -->
<div class="ewMenu">
<?php $RootMenu = new crMenu(EWR_MENUBAR_ID); ?>
<!--##include rpt-phpcommon.php/render-menu##-->
</div>
<!-- End Main Menu -->

<!--## } ##-->
<!--##/session##-->


<!--##session report_mobilemenu##-->
<!--##
	bGenReportMenu = false;
	sMenuIdPrefix = "mmi_";
	sReportMenuIdPrefix = "mmri_";
	sCustomMenuIdPrefix = "mmci_";
##-->
<!--## if (!bGenCompatHeader) { ##-->

<!-- Begin Main Menu -->
<!--##include rpt-phpcommon.php/render-menu##-->
<!-- End Main Menu -->

<!--## } ##-->
<!--##/session##-->