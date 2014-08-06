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
			echo 'Showing from result '.($count + 1).' to '.$maxDocument.($maxDocument < $totalCount ? ' of '.$totalCount.' results' : '').':';
		} else {
			echo 'Showing 0 results.';
		}
	?>
	</p>
	<?php 
		echo $eg_result['pagingElement'];
	?>
	</br>
	<?php
		$docs = $eg_result['docs'];
		$content_url = $eg_result['content_url'];
		$return_url = $eg_result['return_url'];
		foreach ($docs as &$doc) {
			$count = $count + 1;
			include( $template_result_item );
		}
		
		echo $eg_result['pagingElement'];
	?>
</div>