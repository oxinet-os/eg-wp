<?php
/* 	if $eg_result isn't false (has data) and has a document then
	display the variables formatted in div/p. Use <?php var_dump($doc) ?>
	to see the available data */
	
// If there is a result then do page.
if ($eg_result) {
	$doc = $eg_result;
	// Wrap in try, incase any unknown issues occur.
	try {
?>

<div id="main-content" class="main-content">
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<article class="page type-page status-publish hentry">
				<?php 
				// If 'title' field exists, then display in entry-header div, h1:
				echo !empty($doc['title']) ? '<header class="entry-header"><h1 class="eg-title entry-title">'.$doc['title'].'</h1></header>' : '';
				?>
				<div class="entry-content">
					<div class="eg-res eg-single">
						<?php
							// If 'description' field exists, then display:
							echo !empty($doc['description']) ? '<p class="eg-desc">'.$doc['description'].'</p>' : '';
						?>
						<div class="eg-cont">
							<span style="font-weight:bold;">Content</span><br />
							<?php
								// Variable for checking updated and create datetimes, set to now.
								$cd = new DateTime; $cd = $cd->format( 'd-m-Y h:i A' );
								// If 'dsid' field exists, then display:
								if (!empty($doc['dsid'])) { echo 'Document type: '.$doc['dsid'].'<br/>'; }
								// If 'create-date' field exists, then display and set the $cd var:
								if (!empty($doc['create-date'])) { $cd = new DateTime($doc['create-date']); $cd = $cd->format( 'd-m-Y h:i A' );echo 'Created: '.$cd.'<br/>'; }
								// If 'moddate' field exists, then check is different from $cd var and then display:
								if (!empty($doc['moddate'])) {
									$ud = new DateTime($doc['moddate']); $ud = $ud->format( 'd-m-Y h:i A' );
									echo ($cd != $ud ? 'Last updated: '.$ud.'<br/>' : '');
								}
								// If 'url' field exists, then display:
								if (!empty($doc['url'])) { echo '<a target="_blank" href="'.$doc['url'].'">Open</a>'; }
							?>
						</div>
						<?php // If 'return_url' field exists then display:
						if (!empty($return_url)) { echo '<br/><div class="eg-returnurl"><a href="'.urldecode($return_url).'">Go back to search...</a></div>'; } ?>
					</div>
				</div><!-- #entry-content -->
			</article>
		</div><!-- #content -->
	</div><!-- #primary -->
</div><!-- #main-content -->

<?php
	} catch (Exception $e) {
		if (isset($e)) { echo '<div style="display:none;">'.$e->getMessage().'</div>'; }
	}
} else {
// if $eg_result is false or there was an exception, display warning 
?>

<div id="main-content" class="main-content">
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<article class="page type-page status-publish hentry">
				<div class="entry-content">
					<p>No content found.</p>
				</div><!-- #entry-content -->
			</article>
		</div><!-- #content -->
	</div><!-- #primary -->
</div><!-- #main-content -->

<?php
}
?>