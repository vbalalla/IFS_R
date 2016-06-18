<!--##session phppager##-->
<form action="<?php echo ewr_CurrentPage() ?>" name="ewPagerForm" class="ewForm form-horizontal">
<!--##
	if (TABLE.TblType != "REPORT") {
		sItem = "<?php echo $ReportLanguage->Phrase(\"Record\") ?>";
		sItemsPerPage = "<?php echo $ReportLanguage->Phrase(\"RecordsPerPage\") ?>";
	} else {
		sItem = "<?php echo $ReportLanguage->Phrase(\"Group\") ?>";
		sItemsPerPage = "<?php echo $ReportLanguage->Phrase(\"GroupsPerPage\") ?>";
	}
	sImageFolder = ew_FolderPath("_images") + "/";
	iAnonymous = TABLE.TblAnonymous;
	bAnonymous = ((iAnonymous & 8) == 8);

	switch (iPagerStyle) {
		case 1: // Pager Style 1
##-->
<?php if (!isset($Pager)) $Pager = new crNumericPager($<!--##=gsPageObj##-->->StartGrp, $<!--##=gsPageObj##-->->DisplayGrps, $<!--##=gsPageObj##-->->TotalGrps, $<!--##=gsPageObj##-->->GrpRange) ?>
<?php if ($Pager->RecordCount > 0) { ?>
<div class="ewPager">
<div class="ewNumericPage"><ul class="pagination">
	<?php if ($Pager->FirstButton->Enabled) { ?>
	<li><a href="<?php echo ewr_CurrentPage() ?>?start=<?php echo $Pager->FirstButton->Start ?>"><!--##@PagerFirst##--></a></li>
	<?php } ?>
	<?php if ($Pager->PrevButton->Enabled) { ?>
	<li><a href="<?php echo ewr_CurrentPage() ?>?start=<?php echo $Pager->PrevButton->Start ?>"><!--##@PagerPrevious##--></a></li>
	<?php } ?>
	<?php foreach ($Pager->Items as $PagerItem) { ?>
		<li<?php if (!$PagerItem->Enabled) { echo " class=\" active\""; } ?>><a href="<?php echo ewr_CurrentPage() ?>?start=<?php echo $PagerItem->Start ?>"><?php echo $PagerItem->Text ?></a></li>
	<?php } ?>
	<?php if ($Pager->NextButton->Enabled) { ?>
	<li><a href="<?php echo ewr_CurrentPage() ?>?start=<?php echo $Pager->NextButton->Start ?>"><!--##@PagerNext##--></a></li>
	<?php } ?>
	<?php if ($Pager->LastButton->Enabled) { ?>
	<li><a href="<?php echo ewr_CurrentPage() ?>?start=<?php echo $Pager->LastButton->Start ?>"><!--##@PagerLast##--></a></li>
	<?php } ?>
</ul></div>
</div>
<div class="ewPager ewRec">
	<span><!--##@Record##--> <?php echo $Pager->FromIndex ?> <!--##@To##--> <?php echo $Pager->ToIndex ?> <!--##@Of##--> <?php echo $Pager->RecordCount ?></span>
</div>
<?php } ?>
<!--##
			break;
		case 2: // Pager Style 2
##-->
<?php if (!isset($Pager)) $Pager = new crPrevNextPager($<!--##=gsPageObj##-->->StartGrp, $<!--##=gsPageObj##-->->DisplayGrps, $<!--##=gsPageObj##-->->TotalGrps) ?>
<?php if ($Pager->RecordCount > 0) { ?>
<div class="ewPager">
<span><!--##@Page##-->&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $ReportLanguage->Phrase("PagerFirst") ?>" href="<?php echo ewr_CurrentPage() ?>?start=<?php echo $Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $ReportLanguage->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $ReportLanguage->Phrase("PagerPrevious") ?>" href="<?php echo ewr_CurrentPage() ?>?start=<?php echo $Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $ReportLanguage->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EWR_TABLE_PAGE_NO ?>" value="<?php echo $Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $ReportLanguage->Phrase("PagerNext") ?>" href="<?php echo ewr_CurrentPage() ?>?start=<?php echo $Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $ReportLanguage->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $ReportLanguage->Phrase("PagerLast") ?>" href="<?php echo ewr_CurrentPage() ?>?start=<?php echo $Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $ReportLanguage->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<!--##@of##-->&nbsp;<?php echo $Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
<span><!--##@Record##--> <?php echo $Pager->FromIndex ?> <!--##@To##--> <?php echo $Pager->ToIndex ?> <!--##@Of##--> <?php echo $Pager->RecordCount ?></span>
</div>
<?php } ?>
<!--##
		break;
	}
##-->
<!--##
	if (ew_IsNotEmpty(sGrpPerPageList)) {
		arrGrpPerPage = sGrpPerPageList.split(",");
##-->
<?php if ($<!--##=gsPageObj##-->->TotalGrps > 0) { ?>
<div class="ewPager">
<input type="hidden" name="t" value="<!--##=gsTblVar##-->">
<select name="<?php echo EWR_TABLE_GROUP_PER_PAGE; ?>" class="form-control input-sm" onchange="this.form.submit();">
	<!--##
		for (var i = 0; i < arrGrpPerPage.length; i++) {
			thisDisplayGrps = arrGrpPerPage[i];
			if (parseInt(thisDisplayGrps) > 0) {
				thisValue = parseInt(thisDisplayGrps);
	##-->
<option value="<!--##=thisDisplayGrps##-->"<?php if ($<!--##=gsPageObj##-->->DisplayGrps == <!--##=thisValue##-->) echo " selected=\"selected\"" ?>><!--##=thisDisplayGrps##--></option>
	<!--##
			} else {
	##-->
<option value="ALL"<?php if ($<!--##=gsPageObj##-->->getGroupPerPage() == -1) echo " selected=\"selected\"" ?>><!--##@AllRecords##--></option>
	<!--##
			}
		}
	##-->
</select>
</div>
<?php } ?>
<!--##
	}
##-->
</form>
<!--##/session##-->

<?php
<!--##session setupdisplaygrps##-->
	// Set up number of groups displayed per page
	function SetUpDisplayGrps() {

		$sWrk = @$_GET[EWR_TABLE_GROUP_PER_PAGE];
		if ($sWrk <> "") {
			if (is_numeric($sWrk)) {
				$this->DisplayGrps = intval($sWrk);
			} else {
				if (strtoupper($sWrk) == "ALL") { // Display all groups
					$this->DisplayGrps = -1;
				} else {
					$this->DisplayGrps = <!--##=iGrpPerPage##-->; // Non-numeric, load default
				}
			}
			$this->setGroupPerPage($this->DisplayGrps); // Save to session

			// Reset start position (reset command)
			$this->StartGrp = 1;
			$this->setStartGroup($this->StartGrp);
		} else {
			if ($this->getGroupPerPage() <> "") {
				$this->DisplayGrps = $this->getGroupPerPage(); // Restore from session
			} else {
				$this->DisplayGrps = <!--##=iGrpPerPage##-->; // Load default
			}
		}
	}
<!--##/session##-->
?>
