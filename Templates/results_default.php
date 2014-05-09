<div class="eg-results">
	<div style="display:none;">
		<?php echo var_dump( $eg_search_results ); ?>
	</div>
	<p class="eg-header">
	<?php 	
		$documentCount = count( $eg_search_results['docs'] );
		$totalCount = intval( $eg_search_results['numFound'] );
		
		$outOf = ($documentCount < $totalCount) ? $documentCount.' out of '.$totalCount : $documentCount;
		
		echo 'Showing '.$outOf.' results for \''.$eg_search_results['prettySearchTerm'].'\':';
	?>
	</p>
	<?php 
		$docs = $eg_search_results['docs'];
		$count = 0;
		foreach ($docs as &$doc) {
			$count = $count + 1;
			include( $template_result_item );
		}
	?>
</div>