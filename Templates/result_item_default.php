<?php
$partReturnUrl = "&".$var_returnurl."=".urlencode($return_url);
if (strpos($return_url,'?') === false) {
	$partReturnUrl .= urlencode("?".$var_searchterm."=".$search_term."&".$var_pagenumber."=".$requestedPage);
}
?>

<div class="eg-res eg-resno-<?php echo $count ?>">
	<p class="title"><?php echo $count.') <a href="'.$content_url.'?pid='.$doc->pid.$partReturnUrl.'">'.$doc->title.'</a>' ?></p>
	<p class="description"><?php echo $doc->description ?></p>
	<?php if (isset($vocabularies) && is_array($vocabularies)) { ?>
	<p class="vocabularies">
	<?php
		foreach ($vocabularies as $vocab) {
			try {
				$value = $doc->$vocab;
				if (!empty($doc->$vocab))
				{
					if (is_array($value))
					{
						$value = implode(", ", $value);
					}
					echo "<div class='vocab'>".$eg_search->GetPrettyVocabName($vocab).": ".$value."</div>";
				}
			} catch (Exception $ex) { }
		}
	?>
	</p>
	<?php } ?>
</div>