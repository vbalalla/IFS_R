<!-- Begin Main Menu -->
<div class="ewMenu">
<?php $RootMenu = new crMenu(EWR_MENUBAR_ID); ?>
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(26, "mi_Job_Offered_Candidates_Vs_Job_Positions", $ReportLanguage->Phrase("CrosstabReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("26", "MenuText") . $ReportLanguage->Phrase("CrosstabReportMenuItemSuffix"), "Job_Offered_Candidates_Vs_Job_Positionsctb.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(27, "mi_Job_Offered_Candidates_Vs_Job_Positions_Job_Offered_Candidates_Vs_Job_Positions", $ReportLanguage->Phrase("ChartReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("27", "MenuText") . $ReportLanguage->Phrase("ChartReportMenuItemSuffix"), "Job_Offered_Candidates_Vs_Job_Positionsctb.php#cht_Job_Offered_Candidates_Vs_Job_Positions_Job_Offered_Candidates_Vs_Job_Positions", 26, "", TRUE, FALSE);
$RootMenu->AddMenuItem(28, "mi_Rejected_Candidates_Vs_Job_Positions", $ReportLanguage->Phrase("CrosstabReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("28", "MenuText") . $ReportLanguage->Phrase("CrosstabReportMenuItemSuffix"), "Rejected_Candidates_Vs_Job_Positionsctb.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(29, "mi_Rejected_Candidates_Vs_Job_Positions_Rejected_Candidates_Vs_Job_Positions", $ReportLanguage->Phrase("ChartReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("29", "MenuText") . $ReportLanguage->Phrase("ChartReportMenuItemSuffix"), "Rejected_Candidates_Vs_Job_Positionsctb.php#cht_Rejected_Candidates_Vs_Job_Positions_Rejected_Candidates_Vs_Job_Positions", 28, "", TRUE, FALSE);
$RootMenu->AddMenuItem(30, "mi_Job_Offered_Candidates_Vs_University", $ReportLanguage->Phrase("CrosstabReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("30", "MenuText") . $ReportLanguage->Phrase("CrosstabReportMenuItemSuffix"), "Job_Offered_Candidates_Vs_Universityctb.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(31, "mi_Job_Offered_Candidates_Vs_University_Job_Offered_Candidates_Vs_University", $ReportLanguage->Phrase("ChartReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("31", "MenuText") . $ReportLanguage->Phrase("ChartReportMenuItemSuffix"), "Job_Offered_Candidates_Vs_Universityctb.php#cht_Job_Offered_Candidates_Vs_University_Job_Offered_Candidates_Vs_University", 30, "", TRUE, FALSE);
$RootMenu->AddMenuItem(32, "mi_Rejected_Candidates_Vs_University", $ReportLanguage->Phrase("CrosstabReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("32", "MenuText") . $ReportLanguage->Phrase("CrosstabReportMenuItemSuffix"), "Rejected_Candidates_Vs_Universityctb.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(33, "mi_Rejected_Candidates_Vs_University_Rejected_Candidates_Vs_University", $ReportLanguage->Phrase("ChartReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("33", "MenuText") . $ReportLanguage->Phrase("ChartReportMenuItemSuffix"), "Rejected_Candidates_Vs_Universityctb.php#cht_Rejected_Candidates_Vs_University_Rejected_Candidates_Vs_University", 32, "", TRUE, FALSE);
$RootMenu->AddMenuItem(34, "mi_Number_of_Interviews_Vs_Job_Positions", $ReportLanguage->Phrase("CrosstabReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("34", "MenuText") . $ReportLanguage->Phrase("CrosstabReportMenuItemSuffix"), "Number_of_Interviews_Vs_Job_Positionsctb.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(35, "mi_Number_of_Interviews_Vs_Job_Positions_Number_of_Interviews_Vs_Job_Positions", $ReportLanguage->Phrase("ChartReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("35", "MenuText") . $ReportLanguage->Phrase("ChartReportMenuItemSuffix"), "Number_of_Interviews_Vs_Job_Positionsctb.php#cht_Number_of_Interviews_Vs_Job_Positions_Number_of_Interviews_Vs_Job_Positions", 34, "", TRUE, FALSE);
$RootMenu->Render();
?>
</div>
<!-- End Main Menu -->
