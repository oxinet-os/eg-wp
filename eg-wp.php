<?php
/**
 * @package EG_WP
 * @version 0.8
 */
/*
	Plugin Name: EG WordPress API
	Plugin URI: http://wordpress.org/extend/plugins/eg-wp/
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

require_once 'functions.php';

function wrap_element( $element, $type ) {
	if (empty($element)) { $type = ''; }
	
	switch( $type ) {
		case 'title':
			$res = '<p class="eg-wp-title">'.$element.'</p>';
			break;
		case 'description':
			$res = '<p class="eg-wp-description">'.$element.'</p>';
			break;
		case 'header':
			$res = '<p class="eg-wp-header">'.$element.'</p>';
			break;
		default:
			$res = '';
			break;
	}
	
	return $res;
}

function generate_documents( $searchArray, $includeNoOntoTitle ) {
	$res = '';
	$docs = $searchArray['docs'];
	$count = 0;
	foreach ($docs as &$doc) {
		$count ++;
		$res .= '<div class="eg-wp-result eg-wp-resultno-'.$count.'">';
		$title = filter_var($includeNoOntoTitle, FILTER_VALIDATE_BOOLEAN) ? $count.') '.$doc->title : $doc->title;
		$res .= wrap_element( $title, 'title' );
		$res .= wrap_element( $doc->description, 'description' );
		$res .= '</div>';
	}
	return $res;
}

function generate_header( $searchArray, $visible ) {
	$res = '';
	
	if ( filter_var($visible, FILTER_VALIDATE_BOOLEAN) ) {
		$documentCount = count( $searchArray['docs'] );
		$totalCount = intval($searchArray['numFound']);
		
		$outOf = ($documentCount < $totalCount) ? $documentCount.' out of '.$totalCount : $documentCount;
		
		$text = 'Showing '.$outOf.' results for \''.$searchArray['prettySearchTerm'].'\':';
		$res = wrap_element( $text, 'header' );
	}
	
	return $res;
}

function ExcellenceGateway_func( $atts ) {
	$varRes = ""; 
	
	try {
		extract( shortcode_atts( array(
			'searchterm' => '*:*',
			'pagesize' => intval(0),
			'header' => false,
			'paging' => false,
			'documents_includeno' => false,
		), $atts ) );

		$eg_wp_search = new EG_WP(array( 'searchTerm' => $atts['searchterm'], 'pageSize' => $atts['pagesize'], ));
		$eg_wp_search_result = $eg_wp_search->ExecuteSearch();
		
		$varRes = '<div class="eg-wp-results">';
		$varRes .= generate_header( $eg_wp_search_result, $atts['header'] );
		$varRes .= generate_documents( $eg_wp_search_result, $atts['documents_includeno'] );
		$varRes .= '</div>';
		
	} catch (Exception $e) {
	}
	
	return $varRes;
}

add_shortcode( 'ExcellenceGateway', 'ExcellenceGateway_func' );

?>