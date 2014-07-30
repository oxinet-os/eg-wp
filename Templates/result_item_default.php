<?php
$partReturnUrl = "&".$var_returnUrl."=".urlencode($returnUrl."?".$var_searchterm."=".$search_term."&".$var_pagenumber."=".$requestedPage);
?>

<div class="eg-res eg-resno-<?php echo $count ?>">
	<p class="title"><?php echo $count.') <a href="'.$page_url.'?pid='.$doc->pid.$partReturnUrl.'">'.$doc->title.'</a>' ?></p>
	<p class="description"><?php echo $doc->description ?></p>
</div>