<?php
/**
 * Pachube API class
 * Version 0.3 (June 2011)
 * Requirements: PHP5, cURL, API v.2.0
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 */
class PachubeAPI
{
	private $Api;
	private $Pachube;
	private $Pachube_headers;
	
	/**
	 * Constructor
	 */
	function __construct($api) 
	{
		$this->Api = $api;
		$this->Pachube = "api.pachube.com/v2";
		$this->Pachube_headers  = array("X-PachubeApiKey: $this->Api");
	}
	
	/**
	 * Get list of feeds
	 * @param string format of output ("json", "xml", "csv")
	 * @param int page number
	 * @param int feeds per page
	 * @param string content type ("full", "summary")
	 * @param string query for full text search
	 * @param string tag
	 * @param string user name
	 * @param string units
	 * @param string status ("live", "frozen", "all")
	 * @param string order ("created_at", "retrieved_at", "relevance")
	 * @param array location (lat, lon, distance, distance_units)
	 * @return string
	 */
	public function getFeedsList($format=false, $page=false, $per_page=false, $content=false, $query=false, $tag=false, $user=false, $units=false, $status=false, $order=false, $location=false)
	{
		if($format && ($format == "json" || $format == "csv" || $format == "xml")) $f = ".". $format;
		$url = "http://$this->Pachube/feeds$f?";
		if($page) $url .= "page=" . $page . "&";
		if($per_page) $url .= "per_page=" . $per_page . "&";
		if($content) $url .= "content=" . $content . "&";
		if($query) $url .= "q=" . $query . "&";
		if($tag) $url .= "tag=" . $tag . "&";
		if($user) $url .= "user=" . $user . "&";
		if($units) $url .= "units=" . $units . "&";
		if($status) $url .= "status=" . $status . "&";
		if($order) $url .= "order=" . $order . "&";
		if($location) 
		{
			$url .= "lat=" . $location['lat'] . "&" . "lon=" . $location['lon'] . "&";
			if(isset($location['distance'])) $url .= "distance=" . $location['distance'] . "&";
			if(isset($location['distance_units'])) $url .= "distance_units=" . $location['distance_units'];
		}
		return $this->_getRequest($url);
	}
	
	/**
	 * Get feed information
	 * @param string format of output ("json", "xml", "csv")
	 * @param int feed ID
	 * @param string/array datastreams
	 * @return string
	 */
	public function getFeed($format=false, $feed, $datastreams=false)
	{
		if($format && ($format == "json" || $format == "csv" || $format == "xml")) $feed .= ".". $format;
		$url = "http://$this->Pachube/feeds/$feed?";
		if($datastreams)
		{
			if(is_array($datastreams))
			{
				$url .= "datastreams=" . implode(",", $datastreams);
			}
			else
			{
				$url .= "datastreams=" . $datastreams;
			}
		}
		return $this->_getRequest($url);
	}
	
	/**
	 * Update feed with data
	 * @param int feed ID
	 * @param string data to update
	 * @return http response headers
	 */
	public function updateFeed($format=false, $feed, $data)
	{
		if($format && ($format == "json" || $format == "csv" || $format == "xml")) $feed .= ".". $format;
		$url = "http://$this->Pachube/feeds/$feed";
		return $this->_putRequest($url, $data);
	}
	
	/**
	 * Delete feed from Pachube
	 * @param int feed ID
	 * @return http response headers
	 */
	public function deleteFeed($feed)
	{
		$url = "http://$this->Pachube/feeds/$feed";
		return $this->_deleteRequest($url);
	}
	
	/**
	 * Get feed information
	 * @param int feed ID
	 * @return array of objects
	 */
	public function getDatastreamsList($feed)
	{
		$feed .= ".json";
		$url = "http://$this->Pachube/feeds/$feed";
		$data = json_decode($this->_getRequest($url));
		return $data->datastreams;
	}
	
	/**
	 * Create datastream
	 * @param int feed ID
	 * @param string datastream name
	 * @param int datastream ID
	 * @return http response headers
	 */
	public function createDatastream($format=false, $feed, $data)
	{
		$url = "http://$this->Pachube/feeds/$feed/datastreams";
		if($format && ($format == "json" || $format == "csv" || $format == "xml")) $url .= ".". $format;
		return $this->_postRequest($url, $data);
	}
	
	/**
	 * Get datastream
	 * @param string format of output ("json", "xml", "csv")
	 * @param int feed ID
	 * @param string datastream ID
	 * @return string
	 */
	public function getDatastream($format=false, $feed, $datastream)
	{
		if($format && ($format == "json" || $format == "csv" || $format == "xml")) $datastream .= ".". $format;
		$url = "http://$this->Pachube/feeds/$feed/datastreams/$datastream";
		return $this->_getRequest($url);
	}
	
	/**
	 * Update datastream
	 * @param string format of output ("json", "xml", "csv")
	 * @param int feed ID
	 * @param string datastream ID
     * @param string data
	 * @return http response headers
	 */
	public function updateDatastream($format=false, $feed, $datastream, $data)
	{
		if($format && ($format == "json" || $format == "csv" || $format == "xml")) $datastream .= ".". $format;
		$url = "http://$this->Pachube/feeds/$feed/datastreams/$datastream";
		return $this->_putRequest($url, $data);
	}
	
	/**
	 * Delete datastream
	 * @param int feed ID
	 * @param string datastream ID
	 * @return http response headers
	 */
	public function deleteDatastream($feed, $datastream)
	{
		$url = "http://$this->Pachube/feeds/$feed/datastreams/$datastream";
		return $this->_deleteRequest($url);
	}
	
	// ToDo: DataPoints
	// ToDo: Triggers
	// ToDo: Users: List, Create, Update, Delete
	// ToDo: API keys
	
	/**
	 * Get user information
	 * @param string format of output ("json", "xml")
	 * @param string user login
	 * @return string
	 */
	public function getUser($format=false, $user)
	{
		if($format && ($format == "json" || $format == "xml")) $user .= ".". $format;
		$url = "http://$this->Pachube/users/$user";
		return $this->_getRequest($url);
	}
	
	/**
	 * Get feed history
	 * @param string format of output ("json", "xml", "csv")
	 * @param int feed ID
	 * @param string start point
	 * @param string end point
	 * @param string duration
	 * @param int page
	 * @param int per_page
	 * @param string time
	 * @param bool find_previous
	 * @param string interval_type ("discrete", false)
	 * @param int interval (in seconds: 0, 30, 60, 300, 900, 3600, 10800, 21600, 43200, 86400)
	 * @return string
	 */
	public function getFeedHistory($format, $feed, $start=false, $end=false, $duration=false, $page=false, $per_page=false, $time=false, $find_previous=false, $interval_type=false, $interval=false)
	{
		if($format && ($format == "json" || $format == "csv" || $format == "xml")) $feed .= ".". $format;
		$url = "http://$this->Pachube/feeds/$feed?";
		if($start) $url .= "start=" . $start . "&";
		if($end) $url .= "content=" . $content . "&";
		if($duration) $url .= "duration=" . $duration . "&";
		if($page) $url .= "page=" . $page . "&";
		if($per_page) $url .= "end=" . $end . "&";
		if($time) $url .= "time=" . $time . "&";
		if($find_previous) $url .= "find_previous=" . $find_previous . "&";
		if($interval_type) $url .= "interval_type=" . $interval_type . "&";
		if($interval) $url .= "interval=" . $interval;
		
		return $this->_getRequest($url);
	}
	
	/**
	 * Get datastream history
	 * @param string format of output ("json", "xml", "csv")
	 * @param int feed ID
	 * @param string datastream ID
	 * @param string start point
	 * @param string end point
	 * @param string duration
	 * @param int page
	 * @param int per_page
	 * @param string time
	 * @param bool find_previous
	 * @param string interval_type ("discrete", false)
	 * @param int interval (in seconds: 0, 30, 60, 300, 900, 3600, 10800, 21600, 43200, 86400)
	 * @return string
	 */
	public function getDatastreamHistory($format, $feed, $datastream, $start=false, $end=false, $duration=false, $page=false, $per_page=false, $time=false, $find_previous=false, $interval_type=false, $interval=false)
	{
		if($format && ($format == "json" || $format == "csv" || $format == "xml")) $datastream .= ".". $format;
		$url = "http://$this->Pachube/feeds/$feed/datastreams/$datastream?";
		if($start) $url .= "start=" . $start . "&";
		if($end) $url .= "content=" . $content . "&";
		if($duration) $url .= "duration=" . $duration . "&";
		if($page) $url .= "page=" . $page . "&";
		if($per_page) $url .= "end=" . $end . "&";
		if($time) $url .= "time=" . $time . "&";
		if($find_previous) $url .= "find_previous=" . $find_previous . "&";
		if($interval_type) $url .= "interval_type=" . $interval_type . "&";
		if($interval) $url .= "interval=" . $interval;
		
		return $this->_getRequest($url);
	}
	
	/**
	 * Create GET request to Pachube (wrapper)
	 * @param string url
	 * @return http code response
	 */
	private function _getRequest($url)
	{		
		if(function_exists('curl_init'))
		{
			return $this->_curl($url, true);
		}
		elseif(function_exists('file_get_contents') && ini_get('allow_url_fopen'))
		{
			return $this->_get($url);		
		}
		else
		{
			return 500;
		}
	}
	
	/**
	 * Create POST request to Pachube (wrapper)
	 * @param string url
	 * @param array data
	 * @return http code response
	 */
	private function _postRequest($url, $data)
	{		
		if(function_exists('curl_init'))
		{
			return $this->_curl($url, true, true, $data);
		}
		elseif(function_exists('file_post_contents') && ini_get('allow_url_fopen'))
		{
			return $this->_post($url, $data);		
		}
		else
		{
			return 500;
		}
	}

	/**
	 * Create PUT request to Pachube (wrapper)
	 * @param string url
	 * @param string data
	 * @return http code response
	 */
	private function _putRequest($url, $data)
	{	
		if(function_exists('curl_init'))
		{
			$putData = tmpfile();
			fwrite($putData, $data);
			fseek($putData, 0);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->Pachube_headers);
			curl_setopt($ch, CURLOPT_INFILE, $putData);
			curl_setopt($ch, CURLOPT_INFILESIZE, strlen($data));
			curl_setopt($ch, CURLOPT_PUT, true);
			curl_exec($ch);
			$headers = curl_getinfo($ch);
			fclose($putData);
			curl_close($ch);

			return $headers['http_code'];
		}
		elseif(function_exists('file_put_contents') && ini_get('allow_url_fopen'))
		{
			return $this->_put($url,$data);
		}
		else
		{
			return 500;
		}
	}
	
	/**
	 * Create DELETE request to Pachube
	 * @param string url
	 * @return http code response
	 */
	private function _deleteRequest($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->Pachube_headers);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_exec($ch);
		$headers = curl_getinfo($ch);
		curl_close($ch);
		return $headers['http_code'];
	}
	
	/**
	 * GET requests to Pachube
	 * @param string url
	 * @return response
	 */
	private function _get($url)
	{
		// Create a stream
		$opts['http']['method'] = "GET";
		$opts['http']['header'] = "X-PachubeApiKey: ".$this->Api."\r\n";
		$context = stream_context_create($opts);
		// Open the file using the HTTP headers set above
		return file_get_contents($url, false, $context);
	}
	
	/**
	 * POST requests to Pachube
	 * @param string url
	 * @param array data
	 * @return response
	 */
	private function _post($url, $data)
	{
		$postfields = http_build_query($data);  
		$opts = array('http' =>  
		   array(  
		      'method'  => 'POST',  
		      'header'  => 'Content-type: application/x-www-form-urlencoded',  
		      'content' => $postfields,  
		   )  
		);  
		$context  = stream_context_create($opts);  
		return file_get_contents($url, false, $context);
	}


	/**
	 * PUT requests to Pachube
	 * @param string url
	 * @param string data
	 * @return response
	 */
	private function _put($url,$data)
	{	
		// Create a stream
		$opts['http']['method'] = "PUT";
		$opts['http']['header'] = "X-PachubeApiKey: ".$this->Api."\r\n";
		$opts['http']['header'] .= "Content-Length: " . strlen($data) . "\r\n";
		$opts['http']['content'] = $data;
		$context = stream_context_create($opts);
		// Open the file using the HTTP headers set above
		return file_get_contents($url, false, $context);
	}

	/**
	 * cURL main function
	 * @param string url
	 * @param bool authentication
	 * @return response
	 */
	private function _curl($url, $auth=false, $post=false, $post_data=false)
	{
		if(function_exists('curl_init'))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			if($auth) curl_setopt($ch, CURLOPT_HTTPHEADER, $this->Pachube_headers);
			if($post)
			{
				curl_setopt($ch, CURLOPT_POST, 1);
				if($post_data) curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			}
			
			$data = curl_exec($ch);
			curl_close($ch);
			return $data;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Print debug status of error
	 * @param int status code
	 */
	public function _debugStatus($status_code)
	{
		switch ($status_code)
		{			
			case 200:
				$msg = "Pachube feed successfully updated";	
				break;
			case 401:
				$msg = "Pachube API key was incorrect";
				break;
			case 403:
				$msg = "Access forbidden!";
				break;
			case 404:
				$msg = "Feed ID or some other parameter does not exist";
				break;
			case 422:
				$msg = "Unprocessable Entity, semantic errors (CSV instead of XML?)";
				break;
			case 418:
				$msg = "Error in feed ID, data type or some other data";
				break;
			case 500:
				$msg = "cURL library not installed or some other internal error occured";
				break;	
			default:
				$msg = "Status code not recognised: ".$status_code;
				break;
		}
		echo $msg;		
	}
}
?>