<?php

require_once 'SolrPhpClient/Apache/Solr/Service_custom.php';

class EG_WP
{
	// The SOLR arguments for the plugin
	const SOLR_HOST = 'solr.host.qia.oxi.net';
	const SOLR_PORT = '8080';
	const SOLR_PATH = 'solr';
	
	const SOLR_URI = 'http://egwsv1.demoiis.oxfordcc.co.uk/resources?mode=solr';
	
	// Class variables:
	// - args is an array to pass to the service's
	// - results_pagesize is an int to set the size of a page
	// - pageNumber is an int to set the page number of results
	protected $args, $type;
	
	// Main construct, pass the arguments to correct construct.
	function __construct() 
    { 
        $a = func_get_args(); 
        $i = func_num_args(); 
        if (method_exists($this,$f='__construct'.$i))
		{ 
            call_user_func_array(array($this,$f),$a); 
        }
		else
		{
			throw new Exception('EG_WP: There is no contruct supporting '.$i.' variables, please consider passing an array object as the second argument.');
		}
    } 
	
	function __construct0()
	{
		// No arguments, then return this class object.
		return $this;
	}
	
	function __construct1($args)
    {
        $this->args = $args;
		return $this;
    }
	
	function __construct2($type, $args)
    {
		$this->type = $type;
        $this->args = $args;
		return $this;
    }
	
	private $singleArguments;
	public function SingleArguments()
	{
		if (empty($singleArguments))
		{
			$pid = "";
			$_pid = str_replace( '"', '', trim($this->args['pid']) );
			if (!empty($_pid)) {
				$pid = $_pid;
			}
			
			$_return_url = str_replace( '"', '', trim($this->args['return_url']) );
			$return_url = empty($_return_url) ? '/excellence-gateway-search' : $_return_url;
			
			$http_method = "";
			$_http_method = str_replace( '"', '', trim($this->args['http_method']) );
			if (!empty($_http_method)) {
				$http_method = $_http_method;
			}
			$singleArguments = array( 
				'pid' => $pid,
				'return_url' => $return_url,
				'http_method' => $http_method,
			);
		}
		// return array with values (default at the least, or class arguments)
		return $singleArguments;
	}
	
	private $searchArguments;
	public function SearchArguments()
	{ 
		if (empty($searchArguments))
		{
			// default search term is *:* - no class argument matching search_term
			$search_term = "*:*";
			$_term = str_replace( '"', '', trim($this->args['search_term']) );
			if (!empty($_term)) {
				$search_term = $_term;
			}
			$paging_enabled = false;
			if (!empty($this->args['paging_enabled'])) {
				$paging_enabled = $this->args['paging_enabled'];
			}
			// default results_offset is 0 - no class argument matching results_offset
			$results_offset = "0";
			if (!empty($this->args['results_offset'])) {
				$results_offset = $this->args['results_offset'];
			}
			// default results_count is -1 - no class argument matching results_count
			// -1 results_count returns the count of matching search_term
			$results_count = "10";
			if (!empty($this->args['results_count'])) {
				$results_count = $this->args['results_count'];
			}
			$additionalParameters = array( 'fl' => '*,score' );
			if (!empty($this->args['additionalParameters'])) {
				$additionalParameters = $this->args['additionalParameters'];
			}
			$paging_size = "2";
			if (!empty($this->args['paging_size'])) {
				$paging_size = $this->args['paging_size'];
			}
			$results_pagesize = "10";
			if (!empty($this->args['results_pagesize'])) {
				$results_pagesize = $this->args['results_pagesize'];
			}
			$requestedPage = "0";
			if (!empty($this->args['requestedPage'])) {
				$requestedPage = $this->args['requestedPage'];
			}
			$content_url = "/excellence-gateway-content";
			if (!empty($this->args['content_url'])) {
				$content_url = $this->args['content_url'];
			}
			$return_url = "/excellence-gateway-search";
			if (!empty($this->args['return_url'])) {
				$return_url = $this->args['return_url'];
			}
			$var_pagenumber = "pg";
			if (!empty($this->args['var_pagenumber'])) {
				$var_pagenumber = $this->args['var_pagenumber'];
			}
			$var_searchterm = "qq";
			if (!empty($this->args['var_searchterm'])) {
				$var_searchterm = $this->args['var_searchterm'];
			}
			
			$searchArguments = array( 
				'paging_enabled' => $paging_enabled,
				'results_offset' => $results_offset,
				'results_count' => $results_count,
				'search_term' => $search_term,
				'prettySearchTerm' => ($_term == "*:*" || $_term == "*") ? "Any" : $_term,
				'additionalParameters' => $additionalParameters,
				'results_pagesize' => $results_pagesize,
				'requestedPage' => ($requestedPage == 0 ? 0 : ($requestedPage - 1)),
				'paging_size' => $paging_size,
				'content_url' => $content_url,
				'return_url' => $return_url,
				'var_pagenumber' => $var_pagenumber,
				'var_searchterm' => $var_searchterm,
			);
		}
		// return array with values (default at the least, or class arguments)
		return $searchArguments;
	}
	
	private $solrInstance;
	protected function SolrInstance($asUri)
	{
		if (empty($solrInstance)) {
			if ( $asUri ) {
				//echo 'SolrInstance useAPI = '.$asUri.'.<br/>';
				// new SOLR class object using above const's
				$solr = new Apache_Solr_Service( EG_WP::SOLR_URI );
				$solr->setQueryDelimiter("&");
				$solrInstance = $solr;
			} else {
				//echo 'SolrInstance useAPI = '.$asUri.'.<br/>';
				// new SOLR class object using above const's
				$solr = new Apache_Solr_Service( '', EG_WP::SOLR_HOST, EG_WP::SOLR_PORT, EG_WP::SOLR_PATH );
				// test SOLR service
				if (!$solr->ping()) {
					throw new Exception('EG_WP: Solr service not responding.');
				} else {
					$solrInstance = $solr;
				}
			}
		}
		return $solrInstance;
	}
	
	//protected function 
	
	public function ResultCount() 
	{
		$use_api = true;
		if (!empty($this->args['use_api'])) {
			$use_api = $this->args['use_api'];
		}
		
		// get the SOLR instance
		$solr = $this->SolrInstance($use_api);
		// get the search arguments
		$search = $this->SearchArguments();
		$query = '"'.$search['search_term'].'"';
		$additionalParameters = $search['additionalParameters'];
		// execute search
		$res = $solr->search($query, 0, -1, $additionalParameters);
		
		return $res->response->numFound;
	}
	
	public function GetSingle() 
	{
		$use_api = true;
		if (!empty($this->args['use_api'])) {
			$use_api = $this->args['use_api'];
		}
		
		// get the SOLR instance
		$solr = $this->SolrInstance($use_api);
		$solr->setQueryStringEncapse(false);
		// get the single arguments
		$single = $this->SingleArguments();
		
		$res = false;
		
		// if $single['pid'] has value
		if (!empty($single['pid'])) {
		
			$query = 'pid:"' . $single['pid'] . '"';
			// execute single
			$searchRes = $solr->search($query, 0, 1, NULL);
			$response = get_object_vars($searchRes->response);
			
			if ($response && $response['docs'][0]) {
				$firstDoc = (array)$response['docs'][0];
				foreach ($firstDoc as $key => $value) {
					if (strpos($key,'_fields') !== false) {
						$res = $value;
					}
				}
			}
			
			// if there is a result and that result has a Metadata-url
			// then fetch and add to return array
			if ($res && !empty($res['Metadata-url'])) {
				$metaData = file_get_contents($res['Metadata-url']);
				$metaXml = simplexml_load_string($metaData);
				$metaJson = json_encode($metaXml);
				$metaArray = json_decode($metaJson, TRUE);
				if (!empty($metaArray)) {
					$res = array_merge( $res, $metaArray );
					
					$firstMetaContribution = array( "firstMetaContribution" => false );
					$lastMetaContribution = array( "lastMetaContribution" => false );
					
					if (!empty($metaArray['meta-metadata']) && !empty($metaArray['meta-metadata']['contributions']) && !empty($metaArray['meta-metadata']['contributions']['contribution'])) {
						$metaContribs = $metaArray['meta-metadata']['contributions']['contribution'];
						if (!empty($metaContribs[0])) {
							$zeroIndexCount = count($metaContribs)-1;
							$firstMetaContribution = array( "firstMetaContribution" => $metaContribs[0] );
							if ($zeroIndexCount >= 1) {
								$lastMetaContribution = array( "lastMetaContribution" => $metaContribs[$zeroIndexCount] );
							}
						}
					}
					
					$res = array_merge( $res, $firstMetaContribution );
					$res = array_merge( $res, $lastMetaContribution );
				}
				
				$res = array_merge( $res, $single );
			}
			
		} else {
			$res = false;
		}
		return $res;
	}
	
	public function PagingResult()
	{
		$search = $this->SearchArguments();
		$count = intval($this->ResultCount()) - 1;
		
		$var_searchterm = $search['var_searchterm'];
		$encoded_search_term = urlencode($search['search_term']);
		
		$var_pagenumber = $search['var_pagenumber'];
		$paging_size = intval($search['paging_size']);
		$results_pagesize = intval($search['results_pagesize']);
		$requestedPage = intval($search['requestedPage']);
		$computedPage = intval($search['requestedPage']) + 1;
		$offsetSize = $requestedPage * $results_pagesize;
		
		$varDivided = ($count / $results_pagesize);
		$totalPages = ceil($varDivided);
		
		$lowerBound = max( 1, $computedPage - $paging_size);
		$upperBound = min( $totalPages, $computedPage + $paging_size);
		
		if ($computedPage == 1) {
			$previousPage = 1;
			$hasPreviousBtn = false;
		} else {
			$previousPage = $computedPage - 1;
			$hasPreviousBtn = true;
		}
		if ($computedPage == $totalPages || $totalPages == 0) {
			$nextPage = 0;
			$hasNextBtn = false; 
		} else {
			$nextPage = $computedPage + 1;
			$hasNextBtn = true; 
		}
		
		$lb = $lowerBound;
		$ub = $upperBound;
		$tp = $totalPages;
		$cp = $computedPage;
		
		$pagingDiv = '<div class="eg-paging">';
		if ( $tp != 1 ) {
			if ( $hasPreviousBtn ) {
				$pagingDiv .= '<a href="?'.$var_searchterm.'='.$encoded_search_term.'&'.$var_pagenumber.'='.$previousPage.'" class="eg-paging-prev"><< Previous</a>&nbsp;&nbsp;';
			}
			if ( $lb > 1 ) {
				$pagingDiv .= '<a href="?'.$var_searchterm.'='.$encoded_search_term.'&'.$var_pagenumber.'=1">1</a>&nbsp;&nbsp;...&nbsp;&nbsp;';
			}
			while($lb <= $ub) {
				if ($lb == $cp) {
					$pagingDiv .= '<span class="eg-cur-page">'.$lb.'</span>&nbsp;&nbsp;';
				} else {
					$pagingDiv .= '<a href="?'.$var_searchterm.'='.$encoded_search_term.'&'.$var_pagenumber.'='.$lb.'">'.$lb.'</a>&nbsp;&nbsp;';
				}
				$lb ++;
			}
			if ( $ub <= ($tp - 1) ) {
				$pagingDiv .= '...&nbsp;&nbsp;<a href="?'.$var_searchterm.'='.$encoded_search_term.'&'.$var_pagenumber.'='.$tp.'">'.$tp.'</a>';
			}
			if ( $hasNextBtn ) {
				$pagingDiv .= '&nbsp;&nbsp;<a href="?'.$var_searchterm.'='.$encoded_search_term.'&'.$var_pagenumber.'='.$nextPage.'" class="eg-paging-next">Next >></a>';
			}
		}
		$pagingDiv .= '</div>';
		
		return array( 
			'offsetSize' => $offsetSize,
			'results_pagesize' => $results_pagesize,
			'requestedPage' => $requestedPage,
			'computedPage' => $computedPage,
			'previousBtn' => $hasPreviousBtn,
			'previousPage' => $previousPage,
			'nextBtn' => $hasNextBtn,
			'nextPage' => $nextPage,
			'lowerBound' => $lowerBound,
			'upperBound' => $upperBound,
			'pagingElement' => $pagingDiv,
			'totalPages' => $totalPages,
			'content_url' => $search['content_url'],
			'return_url' => $search['return_url'],
			'search_term' => $search['search_term'],
			'prettySearchTerm' => $search['prettySearchTerm'],
			'additionalParameters' => $search['additionalParameters'],
		);
	}
			
	public function ExecuteSearch()
	{
		$use_api = true;
		if (!empty($this->args['use_api'])) {
			$use_api = $this->args['use_api'];
		}
		
		// get the SOLR instance
		$solr = $this->SolrInstance($use_api);
		
		// get the search arguments
		$search = $this->SearchArguments();
		$pagingEnabled = $search['paging_enabled'];
		
		// is paging enabled, if so get search options from that
		if ( $pagingEnabled ) {
			$paging = $this->PagingResult();
			$offset = $paging['offsetSize'];
			$limit = $paging['results_pagesize'];
			$query = $paging['search_term'];
			$additionalParameters = $paging['additionalParameters'];
		// else get the default (or set) results_count sizes and parameters
		} else {
			$offset = $search['results_offset'];
			$limit = $search['results_count'];
			$query = $search['search_term'];
			$additionalParameters = $search['additionalParameters'];
		}

		// execute search
		$res = $solr->search('"'.$query.'"', $offset, $limit, $additionalParameters);

		// response
		$responseArray = get_object_vars($res->response);
		
		// if paging is enabled, merge the paging result array onto the front of the response array
		if ( $pagingEnabled ) {
			// prepend paging settings to response
			$mergedArray = array_merge( $paging, $responseArray );
		} else {
			// prepend search settings to response
			$mergedArray = array_merge( $search, $responseArray );
		}
		
		return $mergedArray;
	}
}

?>