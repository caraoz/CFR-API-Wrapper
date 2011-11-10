<?php
/**
 * PHP Wrapper to query CFR API
 * @author Benjamin J. Balter
 * @version 1.0
 *
 * Usage:
 *
 * $cfr = new CFR_API();
 *
 * $cfr->get_vol( 41, 1 ); //returns 41 CFR §§ 1-100 
 *
 * $cfr->get_title( 41 ); //returns all of title 41
 *
 * Example endpoint URL: http://www.gpo.gov/fdsys/bulkdata/CFR/2011/title-41/CFR-2011-title41-vol1.xml
 *
 */

class CFR_API {

	public $url_base = 'http://www.gpo.gov/fdsys/bulkdata/CFR/';
	public $ttl = 3600; //3600 = 1HR
	
	/**
	 * Given a title and volume, returns the URL to the volume
	 *
	 * Note: if no year is passed, defaults to current year
	 * @param int $title the title
	 * @param int $vol the volume number
	 * @param int $year the year in YYYY format
	 * @return string the URL to the XML file
	 * @todo verify year has been published... will this work on 1/1/12?
	 */
	function build_url( $title, $vol, $year = null ) {
		
		if ( !$year ) 
			$year = date('Y');
		
		return $this->url_base . (int) $year . '/title-' . (int) $title . '/CFR-' . (int) $year . '-title' . (int) $title . '-vol' . (int) $vol . '.xml'; 
	}
	
	/**
	 * Retrieves a specific volume as an XML Object
	 * @param int $title the title
	 * @param int vol the volume number
	 * @param int year the year in YYYY Format
	 * @return obj the simple XML object of the volume 
	 */
	function get_vol( $title, $vol, $year = null ) {
		$url = $this->build_url( $title, $vol, $year );
		$data = $this->fetch( $url );
		return $this->parse( $data );
	}
	
	/**
	 * Retrives and merges all volumes within a title
	 * @param int $title the title
	 * @param int $year the publication year in YYYY format 
	 * @return obj the merged simple XML object
	 * @todo the actual merge -- is this possible?
	 */
	function get_title( $title, $year = null ) {
		$vol = 1;
		while ( $data = $this->get_vol( $title, $year, $vol ) ) {
			//do something here to merge
			
			$vol ++;
		}
	} 
	
	/**
	 * Given an XML string, parses into a simplexml object
	 * @param string $data the raw xml data
	 * @return obj the simple xml object
	 * @todo suuppoer other methods of parsing
	 */
	function parse( $data ) {
		return simplexml_load_string( $data );
	}
	
	/**
	 * Checks cache, otherwise fetches a given URL
	 * Note:  you can't cache an XML object
	 * @param string the URL to fetch
	 * @return string the raw XML data
	 */
	function fetch( $url ) {

		if ( $cache = $this->get_cache( $url ) )
			return $cache;
		
		//because the fdsys system returns a 200 status code, even on 404s, 
		//we have to sniff the content type to prevent errors
		$headers = get_headers( $url, true );
		if ( $headers['Content-Type'] != 'text/xml' )
			return false;
		
		@$data = file_get_contents( $url);
		
		if ( !$data )
			return false;
		
		$this->set_cache( $url, $data );
		
		return $data;
	}
	
	/**
	 * Retrieves a value from internal cache
	 * @todo support other caches
	 * @param string $key a unique identifier
	 * @return mixed the cached value
	 */
	function get_cache( $key ) {
		
		if ( !function_exists( 'apc_fetch' ) )
			return false;
			
		return apc_fetch( $key );
	}
	
	/**
	 * Stores a value in cache
	 * @param string $key a unique identifier
	 * @param mixed the value to store
	 * @todo support other caches
	 */
	function set_cache( $key, $value ) {
	
		if ( !function_exists( 'apc_fetch' ) )
			return false;
	
		return apc_store( $key, $value, $this->ttl );
	
	}
	
}