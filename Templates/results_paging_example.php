<?php 
	$rp = $eg_result['requestedPage'];
	$count = ($rp * $eg_result['results_pagesize']);
?>
<div class="eg-results">
	<p class="eg-header">
	<?php 
		$documentCount = count( $eg_result['docs'] );
		$maxDocument = $count + $documentCount;
		$totalCount = intval( $eg_result['numFound'] );
		
		if ($documentCount > 0) {
			echo 'Showing from result '.($count + 1).' to '.$maxDocument.($maxDocument < $totalCount ? ' of '.$totalCount.' results' : '').' for \''.$eg_result['prettySearchTerm'].'\':';
		} else {
			echo 'Showing 0 results for \''.$eg_result['prettySearchTerm'].'\'.';
		}
	?>
	</p>
	<?php 
		echo $eg_result['pagingElement'];
	?>
	</br>
	<?php
		$docs = $eg_result['docs'];
		$page_url = $eg_result['page_url'];
		
		foreach ($docs as &$doc) {
			$count = $count + 1;
			include( $template_result_item );
		}
		
		echo $eg_result['pagingElement'];
	?>
</div>