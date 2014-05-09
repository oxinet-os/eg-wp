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
	try {
	
		extract( shortcode_atts( array(
			'search_term' => '*:*',
			'search_enabled' => false,
			'results_count' => intval(10),
			'results_offset' => intval(0),
			'results_pagesize' => intval(0),
			'paging_size' => intval(2),
			'paging_enabled' => false,
			'template_results' => '',
			'template_result_item' => '',
			'qs_pagenumber' => '',
			'qs_searchterm' => '',
		), $atts ) );
		
		$template_result_item = '';
		
		if ( $atts['template_result_item'] == '' || $atts['template_result_item'] == 'result_item_default' ) {
			$template_result_item = 'result_item_default.php';
		} else {
			$template_result_item = $atts['template_result_item'].'.php';
		}
		
		$pg = $atts['qs_pagenumber'];
		if (empty( $pg )) { $pg = 'pg'; }
		$qq = $atts['qs_searchterm'];
		if (empty( $qq )) { $qq = 'qq'; }
		
		$requestedPage = 0;
		foreach ($_GET as $key => $value) {
			if ( $key === $pg ) {
				$requestedPage = intval($value);
			}
			if ( $key === $qq ) {
				$atts['search_term'] = $value;
			}
		}
		
		$eg_search = new EG_WP(
			array
			(
				'results_count' => $atts['results_count'],
				'results_offset' => $atts['results_offset'],
				'search_term' => $atts['search_term'],
				'search_enabled' => $atts['search_enabled'],
				'results_pagesize' => $atts['results_pagesize'],
				'paging_enabled' => $atts['paging_enabled'],
				'paging_size' => $atts['paging_size'],
				'requestedPage' => $requestedPage,
				'qs_pagenumber' => $pg,
				'qs_searchterm' => $qq,
			)
		);
		
		$eg_search_results = $eg_search->ExecuteSearch();
		
		if ( $atts['template_results'] == '' || $atts['template_results'] == 'results_default' ) {
			include 'Templates/results_default.php';
		} else {
			include 'Templates/'.$atts['template_results'].'.php';
		}
		
	} catch (Exception $e) {
	}
}

add_shortcode( 'ExcellenceGateway_Search', 'ExcellenceGateway_Search_func' );

function ExcellenceGateway_Single_func( $atts ) {
	
}

add_shortcode( 'ExcellenceGateway_Single', 'ExcellenceGateway_Single_func' );

?>