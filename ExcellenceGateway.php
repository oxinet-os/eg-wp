<?php
/**
 * @package ExcellenceGateway
 * @version 0.2.2
 */
/*
	Plugin Name: EG WordPress API
	Description: 
	Author: Oliver Chalk - Oxinet
	Version: 0.2.1
	Author URI: http://oxi.net
	Licence: GNU General Public License, version 3 (GPL-3.0)

	Copyright 2014 Oliver Chalk - Oxinet (email : ollie@oxi.net)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 3, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once 'EG_Functions.php';

class EG_ContentPageTemplate {
	//A Unique Identifier
	 protected $plugin_slug;

	//A reference to an instance of this class.
	private static $instance;

	//The array of templates that this plugin tracks.
	protected $templates;
	
	//Returns an instance of this class. 
	public static function get_instance() {
			if( null == self::$instance ) {
					self::$instance = new EG_ContentPageTemplate();
			}
			return self::$instance;
	} 

	// Initializes the plugin by setting filters and administration functions.
	private function __construct() {
			$this->templates = array();
			// Add a filter to the attributes metabox to inject template into the cache.
			add_filter(
				'page_attributes_dropdown_pages_args',
				 array( $this, 'register_project_templates' ) 
			);
			// Add a filter to the save post to inject out template into the page cache
			add_filter(
				'wp_insert_post_data', 
				array( $this, 'register_project_templates' ) 
			);
			// Add a filter to the template include to determine if the page has our 
			// template assigned and return it's path
			add_filter(
				'template_include', 
				array( $this, 'view_project_template') 
			);
			// Add your templates to this array.
			$this->templates = array(
					'Templates/eg-content-template.php'     => 'Excellence Gateway Page',
					'Templates/eg-search-template.php'     => 'Excellence Gateway Search',
			);
	}

	/**
	 * Adds our template to the pages cache in order to trick WordPress
	 * into thinking the template file exists where it doesn't really exist.
	 */
	public function register_project_templates( $atts ) {
			// Create the key used for the themes cache
			$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

			// Retrieve the cache list. 
			// If it doesn't exist, or it's empty prepare an array
			$templates = wp_get_theme()->get_page_templates();
			if ( empty( $templates ) ) {
					$templates = array();
			} 

			// New cache, therefore remove the old one
			wp_cache_delete( $cache_key , 'themes');

			// Now add our template to the list of templates by merging our templates
			// with the existing templates array from the cache.
			$templates = array_merge( $templates, $this->templates );

			// Add the modified cache to allow WordPress to pick it up for listing
			// available templates
			wp_cache_add( $cache_key, $templates, 'themes', 1800 );

			return $atts;
	} 

	// Checks if the template is assigned to the page
	public function view_project_template( $template ) {
			global $post;

			if (!isset($this->templates[get_post_meta( 
				$post->ID, '_wp_page_template', true 
			)] ) ) {
					return $template;
			}

			$file = plugin_dir_path(__FILE__).get_post_meta( 
				$post->ID, '_wp_page_template', true 
			);
			
			// Just to be safe, we check if the file exist first
			if( file_exists( $file ) ) {
					return $file;
			}
			else { echo $file; }

			return $template;
	}
}
add_action( 'plugins_loaded', array( 'EG_ContentPageTemplate', 'get_instance' ) );

function ExcellenceGateway_Search_func( $atts ) {		
	extract( shortcode_atts( array(
		'search_on_empty' => false,
		'search_term' => '',
		'search_enabled' => false,
		'results_count' => intval(10),
		'results_offset' => intval(0),
		'results_pagesize' => intval(0),
		'paging_size' => intval(2),
		'paging_enabled' => false,
		'template_results' => 'results_default',
		'template_result_item' => 'result_item_default',
		'content_url' => '/excellence-gateway-content',
		'return_url' => $_SERVER['REQUEST_URI'],
		'var_returnUrl' => 'ret',
		'var_pagenumber' => 'pg',
		'var_searchterm' => 'qq',
		'http_method' => 'GET',
	), $atts, 'ExcellenceGateway_Search' ) );
	
	if ( !isset($atts['template_result_item']) || $atts['template_result_item'] == 'result_item_default' ) {
		$template_result_item = 'result_item_default.php';
	} else {
		$template_result_item = $template_result_item.'.php';
	}
	
	$requestedPage = isset($_REQUEST[$var_pagenumber]) ? intval($_REQUEST[$var_pagenumber]) : 1;
	$search_term = isset($_REQUEST[$var_searchterm]) ? $_REQUEST[$var_searchterm] : $search_term;
		
	if (!empty($search_term) || (empty($search_term) && $search_on_empty)) {
	
		$search_term = urldecode($search_term);
		
		$eg_search = new EG_WP(
			'search',
			array
			(
				'results_count' => $results_count,
				'results_offset' => $results_offset,
				'search_term' => $search_term,
				'search_enabled' => $search_enabled,
				'results_pagesize' => $results_pagesize,
				'paging_enabled' => $paging_enabled,
				'paging_size' => $paging_size,
				'requestedPage' => $requestedPage,
				'content_url' => $content_url,
				'return_url' => $return_url,
				'var_pagenumber' => $var_pagenumber,
				'var_searchterm' => $var_searchterm,
				'http_method' => $http_method,
			)
		);
		
		$eg_result = $eg_search->ExecuteSearch();
		
		if ( empty($template_results) || $template_results == 'results_default' ) {
			include 'Templates/results_default.php';
		} else {
			include 'Templates/'.$template_results.'.php';
		}
	}	
}
add_shortcode( 'ExcellenceGateway_Search', 'ExcellenceGateway_Search_func' );

function ExcellenceGateway_SearchForm_func( $atts ) {		
	extract( shortcode_atts( array(
		'placeholder' => 'Search term...',
		'var_searchterm' => 'qq',
		'http_method' => 'GET',
	), $atts, 'ExcellenceGateway_SearchForm' ) );
	
	$inputSearchTerm = isset($_REQUEST[$var_searchterm]) ? $_REQUEST[$var_searchterm] : "";
	
	$content = '
	<div class="eg-search-form">
		<form method="'.$http_method.'">
			<input type="text" name="'.$var_searchterm.'" placeholder="'.$placeholder.'" value="'.$inputSearchTerm.'" />
			<input type="submit" /> 
		</form>
	</div>';
	
	echo $content;
}
add_shortcode( 'ExcellenceGateway_SearchForm', 'ExcellenceGateway_SearchForm_func' );

function ExcellenceGateway_Single_func( $atts ) {	
	extract( shortcode_atts( array(
		'template_single' => 'single_default',
		'pid' => '',
		'return_url' => '/excellence-gateway-search',
		'var_pid' => 'pid',
		'var_returnUrl' => 'ret',
		'http_method' => 'GET',
	), $atts, 'ExcellenceGateway_Single' ) );
	
	$pid = isset($_REQUEST[$var_pid]) ? $_REQUEST[$var_pid] : $pid;
	$return_url = isset($_REQUEST[$var_returnUrl]) ? $_REQUEST[$var_returnUrl] : $return_url;
	
	$eg_single = new EG_WP(
		'single',
		array
		(
			'pid' => $pid,
			'return_url' => $return_url,
			'http_method' => $http_method,
		)
	);
	
	$eg_result = $eg_single->GetSingle();
	
	if ( empty($template_single) || $template_single == 'single_default' ) {
		include 'Templates/single_default.php';
	} else {
		include 'Templates/'.$template_single.'.php';
	}
}
add_shortcode( 'ExcellenceGateway_Single', 'ExcellenceGateway_Single_func' );

function ExcellenceGateway_assignPageTitle($title, $sep = '|'){
	global $excellenceGatewayTitle;
	if ($excellenceGatewayTitle) {
		return $excellenceGatewayTitle.' '.$sep.' ';
	}
	return $title;
}
add_filter('wp_title', 'ExcellenceGateway_assignPageTitle');

?>