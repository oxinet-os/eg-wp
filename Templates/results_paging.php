<?php 
	$rp = $eg_search_results['requestedPage'];
	$count = ($rp * $eg_search_results['pageSize']);
?>
<div class="eg-results">
	<p class="eg-header">
	<?php 
		$documentCount = count( $eg_search_results['docs'] );
		$totalCount = intval( $eg_search_results['numFound'] );
		
		$outOf = ($documentCount < $totalCount) ? $documentCount.' out of '.$totalCount : $documentCount;
		
		echo 'Showing '.$outOf.' results for \''.$eg_search_results['prettySearchTerm'].'\':';
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
			include( $result_item_template );
		}
		
		echo $eg_search_results['pagingElement'];
	?>
</div>