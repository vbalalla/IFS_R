<!--##session chart_content##-->
<!-- Chart Content (Start) -->
<?php
$id = "<!--##=gsTblVar##-->";
$chartxml = $<!--##=sPageObj##-->->Gantt->Xml();
$wrkwidth = <!--##=iChartWidth##-->;
$wrkheight = <!--##=iChartHeight##-->;
?>
<div id="div_<?php echo $id; ?>"></div>
<script type="text/javascript">
	var chartwidth = "<?php echo $wrkwidth ?>", chartheight = "<?php echo $wrkheight ?>",
		chartid = "chart_<?php echo $id ?>", chartdivid = "div_<?php echo $id ?>";
	var chartxml = "<?php echo ewr_EscapeJs($chartxml) ?>";
<?php if (EWR_FUSIONCHARTS_FREE) { ?>
	var chartswf = "<?php echo EWR_FUSIONCHARTS_FREE_CHART_PATH ?>FCF_Gantt.swf";
	var cht_<?php echo $id ?> = new FusionChartsFree(chartswf, chartid, chartwidth, chartheight);
	cht_<?php echo $id ?>.setDataXML(chartxml);
	cht_<?php echo $id ?>.addParam("wmode", "transparent");
<?php } else { ?>
	var cht_<?php echo $id ?> = new FusionCharts({ "type": "gantt", "id": chartid, "width": chartwidth, "height": chartheight });
	cht_<?php echo $id ?>.setXMLData(chartxml);
<?php } ?>
	var f = <?php echo $<!--##=sPageObj##-->->PageObjName ?>.Chart_Rendering;
	if (typeof f == "function") f(cht_<?php echo $id ?>, 'chart_<?php echo $id ?>');
	cht_<?php echo $id ?>.render(chartdivid);
	f = <?php echo $<!--##=sPageObj##-->->PageObjName ?>.Chart_Rendered;
	if (typeof f == "function") f(cht_<?php echo $id ?>, 'chart_<?php echo $id ?>');
</script>
<?php
// Add debug XML
if (EWR_DEBUG_ENABLED)
	echo "<p>(Chart XML): " . ewr_HtmlEncode($chartxml) . "</p>";
?>
<!-- Chart Content (End) -->
<!--##/session##-->