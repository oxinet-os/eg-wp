<?php
/**
 * Template Name: Excellence Gateway Content
 * @package ExcellenceGateway
 */

get_header(); ?>

<div id="main-content" class="main-content">
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<article class="page type-page status-publish hentry">
				<header class="entry-header">
					<h1 class="eg-title entry-title">Search</h1>
				</header>
				<div class="entry-content">
					<?php if (have_posts()) : while (have_posts()) : the_post();?>
					<?php the_content(); ?>
					<?php endwhile; endif; ?>
				</div><!-- #entry-content -->
			</article>
		</div><!-- #content -->
	</div><!-- #primary -->
</div><!-- #main-content -->
	
<?php
get_sidebar();
get_footer();
