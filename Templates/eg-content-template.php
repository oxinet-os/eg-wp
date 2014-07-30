<?php
/**
 * Template Name: Excellence Gateway Content
 * @package ExcellenceGateway
 */
 
// Global $excellenceGatewayTitle variable, default is 'Excellence Gateway Content'.
global $excellenceGatewayTitle;
$excellenceGatewayTitle = 'Excellence Gateway Content';

// Get the plugin directory and require the EG_Functions.php file
$eg_template_dir = dirname(__FILE__);
$eg_plugin_dir = rtrim($eg_template_dir, "Templates");
require_once $eg_plugin_dir.'/EG_Functions.php';

$pid = '';
$returnUrl = '';
$var_pid = 'pid';
$var_returnUrl = 'ret';
$http_method = 'GET';

$pid = isset($_REQUEST[$var_pid]) ? $_REQUEST[$var_pid] : $pid;
$returnUrl = isset($_REQUEST[$var_returnUrl]) ? $_REQUEST[$var_returnUrl] : $returnUrl;

$eg_single = new EG_WP(
	'single',
	array
	(
		'pid' => $pid,
		'returnUrl' => $returnUrl,
		'http_method' => $http_method,
	)
);

$eg_result = $eg_single->GetSingle();

// Set the title $excellenceGatewayTitle variable which is set
// in ExcellenceGateway.php ExcellenceGateway_assignPageTitle function.
if ($eg_result) {
	if (!empty($eg_result['title'])) {
		$excellenceGatewayTitle = $eg_result['title'];
	}
}
// Get the header, ExcellenceGateway_assignPageTitle is called here.
get_header();

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
						<?php // If 'returnUrl' field exists then display:
						if (!empty($returnUrl)) { echo '<br/><div class="eg-returnurl"><a href="'.urldecode($returnUrl).'">Go back to search...</a></div>'; } ?>
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

<?php
get_sidebar();
get_footer();
