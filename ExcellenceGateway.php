<?php
/**
 * @package ExcellenceGateway
 * @version 0.8
 */
/*
	Plugin Name: EG WordPress API
	Description: 
	Author: Oliver Chalk - Oxinet
	Version: 0.8
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

function ExcellenceGateway_Search_func( $atts ) {		
	extract( shortcode_atts( array(
		'search_term' => '*:*',
		'search_enabled' => false,
		'results_count' => intval(10),
		'results_offset' => intval(0),
		'results_pagesize' => intval(0),
		'paging_size' => intval(2),
		'paging_enabled' => false,
		'template_results' => 'results_default',
		'template_result_item' => 'result_item_default',
		'var_pagenumber' => 'pg',
		'var_searchterm' => 'qq',
		'http_method' => 'GET',
	), $atts, 'ExcellenceGateway_Search' ) );
	
	if ( !isset($atts['template_result_item']) || $atts['template_result_item'] == 'result_item_default' ) {
		$template_result_item = 'result_item_default.php';
	} else {
		$template_result_item = $template_result_item.'.php';
	}
	
	$pg = $var_pagenumber;
	$qq = $var_searchterm;
	
	$requestedPage = 0;
	$httpMeth = $http_method == 'GET' ? $_GET : ($http_method == 'POST' ? $_POST : null);
	if ($httpMeth != null) {
		foreach ($_GET as $key => $value) {
			if ( $key === $pg ) {
				$requestedPage = intval($value);
			}
			if ( $key === $qq ) {
				$search_term = $value;
			}
		}
	}
	
	$eg_search = new EG_WP(
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
			'var_pagenumber' => $pg,
			'var_searchterm' => $qq,
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

add_shortcode( 'ExcellenceGateway_Search', 'ExcellenceGateway_Search_func' );

function ExcellenceGateway_Single_func( $atts ) {
	
}

add_shortcode( 'ExcellenceGateway_Single', 'ExcellenceGateway_Single_func' );

?>