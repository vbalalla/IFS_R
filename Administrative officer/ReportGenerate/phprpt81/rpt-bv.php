<!--##session ewbv_script##-->
<?php
// Get resize parameters
$resize = (@$_GET["resize"] <> "");
$width = (@$_GET["width"] <> "") ? $_GET["width"] : 0;
$height = (@$_GET["height"] <> "") ? $_GET["height"] : 0;
if (@$_GET["width"] == "" && @$_GET["height"] == "") {
	$width = EWR_THUMBNAIL_DEFAULT_WIDTH;
	$height = EWR_THUMBNAIL_DEFAULT_HEIGHT;
}
$quality = (@$_GET["quality"] <> "") ? $_GET["quality"] : EWR_THUMBNAIL_DEFAULT_QUALITY;

// Resize image from physical file
if (@$_GET["fn"] <> "") {
	$fn = ewr_StripSlashes($_GET["fn"]);
	$fn = str_replace("\0", "", $fn);
	$fn = ewr_PathCombine(ewr_AppRoot(), $fn, TRUE);
	if (file_exists($fn)) {
		$pathinfo = pathinfo($fn);
		$ext = strtolower($pathinfo['extension']);
		$size = getimagesize($fn);
		if ($size)
			header("Content-type: {$size['mime']}");
		echo ewr_ResizeFileToBinary($fn, $width, $height, $quality);
	}
	exit();
}?>
<!--##/session##-->