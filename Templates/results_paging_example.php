<?php 
	$rp = $eg_search_results['requestedPage'];
	$count = ($rp * $eg_search_results['results_pagesize']);
?>
<div style="display:none;">
	<?php echo var_dump( $eg_search_results ); ?>
</div>
<div class="eg-results">
	<p class="eg-header">
	<?php 
		$documentCount = count( $eg_search_results['docs'] );
		$maxDocument = $count + $documentCount;
		$totalCount = intval( $eg_search_results['numFound'] );
		
		if ($documentCount > 0) {
			echo 'Showing from result '.($count + 1).' to '.$maxDocument.($maxDocument < $totalCount ? ' of '.$totalCount.' results' : '').' for \''.$eg_search_results['prettySearchTerm'].'\':';
		} else {
			echo 'Showing 0 results for \''.$eg_search_results['prettySearchTerm'].'\'.';
		}
	?>
	</p>
	<?php 
		echo $eg_search_results['pagingElement'];
	?>
	</br>
	<?php
		$docs = $eg_search_results['docs'];
		foreach ($docs as &$doc) {
			$count = $count + 1;
			include( $template_result_item );
		}
		
		echo $eg_search_results['pagingElement'];
	?>
</div>