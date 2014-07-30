<?php
$partReturnUrl = "&".$var_returnUrl."=".urlencode($return_url);
if (strpos($return_url,'?') === false) {
	$partReturnUrl .= urlencode("?".$var_searchterm."=".$search_term."&".$var_pagenumber."=".$requestedPage);
}
?>

<div class="eg-res eg-resno-<?php echo $count ?>">
	<p class="title"><?php echo $count.') <a href="'.$content_url.'?pid='.$doc->pid.$partReturnUrl.'">'.$doc->title.'</a>' ?></p>
	<p class="description"><?php echo $doc->description ?></p>
</div>