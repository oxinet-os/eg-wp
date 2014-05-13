<div class="eg-results">
	<div style="display:none;">
		<?php echo var_dump( $eg_result ); ?>
	</div>
	<p class="eg-header">
	<?php 	
		$documentCount = count( $eg_result['docs'] );
		$totalCount = intval( $eg_result['numFound'] );
		
		$outOf = ($documentCount < $totalCount) ? $documentCount.' out of '.$totalCount : $documentCount;
		
		echo 'Showing '.$outOf.' results for \''.$eg_result['prettySearchTerm'].'\':';
	?>
	</p>
	<?php 
		$docs = $eg_result['docs'];
		$count = 0;
		foreach ($docs as &$doc) {
			$count = $count + 1;
			include( $template_result_item );
		}
	?>
</div>