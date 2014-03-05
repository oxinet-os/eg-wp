<?php

require_once 'SolrPhpClient/Apache/Solr/Service.php';

class EG_WP
{
	// The SOLR arguments for the plugin
	const SOLR_HOST = 'solr.host.qia.oxi.net';
	const SOLR_PORT = '8080';
	const SOLR_PATH = 'solr';
	
	// Class variables:
	// - args is an array to pass to the service's
	// - pageSize is an int to set the size of a page
	// - pageNumber is an int to set the page number of results
	protected $args, $pageSize, $pageNumber;
	
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
		// Construct with no array argument, just the web service.
        $this->args = $args;
		return $this;
    }
	
	private $searchArguments;
	public function SearchArguments()
	{
		if (empty($searchArguments))
		{
			// default search term is *:* - no class argument matching searchTerm
			$searchTerm = "*:*";
			if (!empty($this->args['searchTerm'])) {
				$searchTerm = $this->args['searchTerm'];
			}
			// default offset is 0 - no class argument matching offset
			$offset = "0";
			if (!empty($this->args['offset'])) {
				$offset = $this->args['offset'];
			}
			// default results is -1 - no class argument matching results
			// -1 results returns the count of matching searchTerm
			$results = "-1";
			if (!empty($this->args['results'])) {
				$results = $this->args['results'];
			}
			$additionalParameters = array( 'fl' => '*,score' );
			if (!empty($this->args['additionalParameters'])) {
				$additionalParameters = $this->args['additionalParameters'];
			}
			$pageSize = "10";
			if (!empty($this->args['pageSize'])) {
				$pageSize = $this->args['pageSize'];
			}
			$requestedPage = "0";
			if (!empty($this->args['requestedPage'])) {
				$requestedPage = $this->args['requestedPage'];
			}
			
			$searchArguments = array( 
				'offset' => $offset,
				'results' => $results,
				'searchTerm' => $searchTerm,
				'additionalParameters' => $additionalParameters,
				'pageSize' => $pageSize,
				'requestedPage' => $requestedPage,
			);
		}
		// return array with values (default at the least, or class arguments)
		return $searchArguments;
	}
	
	private $solrInstance;
	protected function SolrInstance()
	{
		if (empty($solrInstance)) {
			// new SOLR class object using above const's
			$solr = new Apache_Solr_Service( EG_WP::SOLR_HOST, EG_WP::SOLR_PORT, EG_WP::SOLR_PATH );
			// test SOLR service
			if (!$solr->ping()) {
				throw new Exception('EG_WP: Solr service not responding.');
			} else {
				$solrInstance = $solr;
			}
		}
		return $solrInstance;
	}
	
	public function ResultCount() 
	{
		// get the SOLR instance
		$solr = $this->SolrInstance();
		// get the search arguments
		$search = $this->SearchArguments();
		$query = $search['searchTerm'];
		$additionalParameters = $search['additionalParameters'];
		// execute search
		$res = $solr->search($query, 0, -1, $additionalParameters);
		
		return $res->response->numFound;
	}
	
	public function PagingResult()
	{
		// get the search arguments
		$search = $this->SearchArguments();
		
		$count = intval($this->ResultCount()) - 1;
		
		$pageSize = intval($search['pageSize']);
		$requestedPage = intval($search['requestedPage']);
		$offsetSize = $requestedPage * $pageSize;
		
		$hasPreviousBtn = true;
		if ($requestedPage == 0) {
			$hasPreviousBtn = false;
		}
		$hasNextBtn = true; 
		if ($offsetSize >= $count) {
			$hasNextBtn = false; 
		}
		
		$varDivided = ($count / $pageSize);
		$totalPages = ceil($varDivided);
		
		return array( 
			'offsetSize' => $offsetSize,
			'pageSize' => $pageSize,
			'requestedPage' => $requestedPage,
			'previousBtn' => $hasPreviousBtn,
			'nextBtn' => $hasNextBtn,
			'totalPages' => $totalPages,
			'searchTerm' => $search['searchTerm'],
			'prettySearchTerm' => $search['searchTerm'] == "*:*" ? "Any" : $search['searchTerm'],
			'additionalParameters' => $search['additionalParameters'],
		);
	}
		
	public function ExecuteSearch()
	{
		// get the SOLR instance
		$solr = $this->SolrInstance();
		// get the search arguments
		$paging = $this->PagingResult();
		$offset = $paging['offsetSize'];
		$limit = $paging['pageSize'];
		$query = $paging['searchTerm'];
		$additionalParameters = $paging['additionalParameters'];
		// execute search
		$res = $solr->search($query, $offset, $limit, $additionalParameters);

		// response
		$responseArray = get_object_vars($res->response);
		// prepend paging settings to response
		$mergedArray = array_merge( $paging, $responseArray );
		
		return $mergedArray;
	}
}

//$testWS = new EG_WP(array( 'searchTerm' => $_GET['q'], ));
//echo "Count: " . $testWS->ResultCount() . "<br/><br/>";
//
//$testWS = new EG_WP(array( 'searchTerm' => $_GET['q'], 'pageSize' => $_GET['ps'], 'requestedPage' => $_GET['rp'], ));
//echo var_dump($testWS->ExecuteSearch()) . "<br/><br/>";
//
//$testWS = new EG_WP(array( 'searchTerm' => $_GET['q'], 'pageSize' => $_GET['ps'], 'requestedPage' => $_GET['rp'], ));
//echo var_dump($testWS->PagingResult()) . "<br/><br/>";

//$testWS = new EG_WP();
//echo var_dump($testWS->AvailableWebServices()) . "<br/><br/>";

?>