<?php
/**
 * @name functions.php
 * @author lancelot <lancelot1215@gmail.com>
 * Date 2013-6-9 
 * Encoding UTF-8
 * 
 * you can access all the global functions in your application
 */
	
	function app(){
	    return Yii::app();
	}
	
	function clientScript(){
		return Yii::app()->getClientScript();
	}
	
	function getComponent($componentId = null){
		if ( $componentId === null )
			return Yii::app()->getComponents();
		else
			return Yii::app()->getComponent($componentId);
	}
	
	function user(){
		return Yii::app()->getUser();
	}
	
	function createUrl($route,$params=array(),$ampersand='&'){
		return Yii::app()->createUrl($route,$params,$ampersand);
	}
	
	function html($text){
		return CHtml::encode($text);
	}
	
	/**
	 * create a link
	 * @param string $text
	 * @param string $url
	 * @param array $htmlOptions
	 * @return string
	 */
	function l($text, $url = '#', $htmlOptions = array()){
		return CHtml::link($text, $url, $htmlOptions);
	}
	
	function session(){
		return Yii::app()->getSession();
	}

	/**
	 * Set the key, value in Session
	 * @return boolean
	 */
	function addSession($key,$value){
		return Yii::app()->getSession()->add($key, $value);
	}
	
	/**
	 * Get the value from key in Session
	 * @return array | NULL
	 */
	function getSession($key){
		return Yii::app()->getSession()->get($key);
	}
	
	function settings(){
		return Yii::app()->settings;
	}
	
	/**
	 * shortcut to Yii::t() with default category = 'stay'
	 */
	function t($category = 'xcms', $message, $params = array(), $source = null, $language = null){
		return Yii::t($category, $message, $params, $source, $language);
	}
	
	/**
	 * shortcut to Yii::app()->request->baseUrl
	 * If the parameter is given,
	 * it will be returned and prefixed with the app baseUrl.
	 */
	function baseUrl($url=null)
	{
		static $baseUrl;
		if ($baseUrl===null)
			$baseUrl=Yii::app()->getRequest()->getBaseUrl();
		return $url===null ? $baseUrl : $baseUrl.'/'.ltrim($url,'/');
	}
	
	/**
	 * @return array | NULL
	 */
	function params(){
		return Yii::app()->params;
	}
	
	/**
	 * @param unknown_type $name
	 * @return mixed | NULL
	 */
	function param($name){
		return isset(Yii::app()->params[$name]) ? Yii::app()->params[$name] : NULL;
	}
	
	/**
	 * @param mixed $val
	 * @param boolean $useSystemDump
	 * @param boolean $exit
	 */
	function dump($val,$useSystemDump = false,$exit = true){
		if ( $useSystemDump === true )
			var_dump($val);
		else
			CVarDumper::dump($val);
		if ( $exit === true )
			exit();
	}
	
	/**
	 * Convert local timestamp to GMT
	 *
	 */
	function local_to_gmt($time = ''){
		if ($time == '')
			$time = time();
		return mktime( gmdate("H", $time), gmdate("i", $time), gmdate("s", $time), gmdate("m", $time), gmdate("d", $time), gmdate("Y", $time));
	}
	
	/**
	 * Get the current IP of the connection
	 *
	 */
	function ip() {
		$ip = null;
		$ipType = array(
					'HTTP_CLIENT_IP',
					'HTTP_FORWARDED_FOR',
					'HTTP_X_FORWARDED_FOR',
					'REMOTE_ADDR'
				);
		if (isset($_SERVER)) {
			foreach ( $ipType as $value ){
				if ( isset($_SERVER[$value]) ){
					$ip = $_SERVER[$value];
					break;
				}
			}
		}else{
			foreach ( $ipType as $value ){
				if ( ($ip = getenv($value)) != false){
					break;
				}
			}
		}
		
		return $ip;
	}
	
	/**
	 * Generate Unique string
	 */
	function genUniqueString($len=8) {
		$hex = md5(param('salt-file') . uniqid("", true));
	
		$pack = pack('H*', $hex);
		$tmp =  base64_encode($pack);
	
		$uid = preg_replace("/[^A-Za-z0-9]/", "", $tmp);
	
		$len = max(4, min(128, $len));
	
		while (strlen($uid) < $len)
			$uid .= gen_uuid(22);
	
		$res = substr($uid, 0, $len);
		return $res;
	}
	
	/**
	 * Get array of subfolders' name
	 */
	function get_subfolders_name($path,$file=false){
		$list=array();
		$results = scandir($path);
		foreach ($results as $result) {
			if ($result === '.' or $result === '..' or $result === '.svn')
				continue;
			if(!$file) {
				if (is_dir($path . '/' . $result)) {
					$list[]=trim($result);
				}
			}
			else {
				if (is_file($path . '/' . $result)) {
					$list[]=trim($result);
				}
			}
		}
		return $list;
	}
	
	/**
	 * Check current app is console or not
	 */
	function isConsoleApp() {
		return get_class(Yii::app())=='CConsoleApplication';
	}
	
	/**
	 * Replace Tags
	 */
	function replaceTags($startPoint, $endPoint, $newText, $source) {
		return preg_replace('#('.preg_quote($startPoint).')(.*)('.preg_quote($endPoint).')#si', '${1}'.$newText.'${3}', $source);
	}
	
	/**
	 * Encode the text into a string which all white spaces will be replaced by $rplChar
	 * @param string $text  text to be encoded
	 * @param Char $rplChar character to replace all the white spaces
	 * @param boolean upWords   set True to uppercase the first character of each word, set False otherwise
	 */
	function encode($text, $rplChar='', $upWords=true)
	{
		$encodedText = null;
		if($upWords)
		{
			$encodedText = ucwords($text);
		}
		else
		{
			$encodedText = strtolower($text);
		}
	
		if($rplChar=='')
		{
			$encodedText = preg_replace('/\s[\s]+/','',$encodedText);    // Strip off multiple spaces
			$encodedText = preg_replace('/[\s\W]+/','',$encodedText);    // Strip off spaces and non-alpha-numeric
		}
		else
		{
			$encodedText = preg_replace('/\s[\s]+/',$rplChar, $encodedText);    // Strip off multiple spaces
			$encodedText = preg_replace('/[\s\W]+/',$rplChar, $encodedText);    // Strip off spaces and non-alpha-numeric
			$encodedText = preg_replace('/^[\\'.$rplChar.']+/','', $encodedText); // Strip off the starting $rplChar
			$encodedText = preg_replace('/[\\'.$rplChar.']+$/','',$encodedText); // // Strip off the ending $rplChar
		}
		return $encodedText;
	
	}
	
	// Query Filter String from Litpi.com
	function query_clean($str)
	{
		//Use RegEx for complex pattern
		$filterPattern = array(
				'/select.*(from|if|into)/i',// select table query,
				'/0x[0-9a-f]*/i',			// hexa character
				'/\(.*\)/',					// call a sql function
				'/union.*select/i',			// UNION query
				'/insert.*values/i',		// INSERT query
				'/order.*by/i'				// ORDER BY injection
		);
		$str = preg_replace($filterPattern, '', $str);
	
		//Use normal replace for simple replacement
		$filterHaystack = array(
				'--',   // query comment
				'||',   // OR operator
				'\*',   // OR operator
		);
	
		$str = str_replace($filterHaystack, '', $str);
		return $str;
	}
	
	//XSS Clean Data Input from Litpi.com
	function xss_clean($data)
	{
		return $data;
		// Fix &entity\n;
		$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
		$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
		$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
		$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
	
		// Remove any attribute starting with "on" or xmlns
		$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);
	
		// Remove javascript: and vbscript: protocols
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);
	
		// Only works in IE: <span style="width: expression(alert('cms','Ping!'));"></span>
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);
	
		// Remove namespaced elements (we do not need them)
		$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);
	
		do
		{
			// Remove really unwanted tags
			$old_data = $data;
			$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
		}
		while ($old_data !== $data);
	
		// we are done...
		return $data;
	}
	
	function plaintext($s)
	{
		$s = strip_tags($s);
		$s = xss_clean($s);
		return $s;
	}
	
	/**
	 * create a request
	 * @param string $requestType
	 * 		POST or GET
	 * @param string $url
	 * @param array $params
	 */
	function curl_async($requestType,$url,$params){
		foreach ($params as $key => $val) {
			if (is_array($val))
				$val = implode(',', $val);
			$post_params[] = $key.'='.urlencode($val);
		}
		$post_string = implode('&', $post_params);
		
		$parts=parse_url($url);
		
		$fp = fsockopen($parts['host'],
				isset($parts['port'])?$parts['port']:80,
				$errno, $errstr, 30);
		
		$out = "{$requestType} ".$parts['path']." HTTP/1.1\r\n";
		$out.= "Host: ".$parts['host']."\r\n";
		$out.= "Content-Type: application/x-www-form-urlencoded\r\n";
		$out.= "Content-Length: ".strlen($post_string)."\r\n";
		$out.= "Connection: Close\r\n\r\n";
		if (isset($post_string)) $out.= $post_string;
		
		fwrite($fp, $out);
		fclose($fp);
	}
	
	/**
	 * create a post request
	 * @param string $url
	 * @param array $params
	 */
	function curl_post_async($url, $params){
		curl_async('POST', $url, $params);
	}
	
	/**
	 * create a get request
	 * @param string $url
	 * @param array $params
	 */
	function curl_get_async($url, $params){
		curl_async('GET', $url, $params);
	}
	
	/**
	 * Format string of filesize
	 *
	 * @param string $s
	 * @return string
	 */
	function formatFileSize($s)
	{
		if($s >= "1073741824")
		{
			$s = number_format($s / 1073741824, 2) . " GB";
		}
		elseif($s >= "1048576")
		{
			$s  = number_format($s / 1048576, 2) . " MB";
		}
		elseif($s >= "1024")
		{
			$s = number_format($s / 1024, 2) . " KB";
		}
		elseif($s >= "1")
		{
			$s = $s . " bytes";
		}
		else
		{
			$s = "-";
		}
	
		return $s;
	}
	
	/**
	 * Fix back button on IE6 (stupid) browser
	 * @author khanhdn
	 */
	function fixBackButtonOnIE()
	{
		//drupal_set_header("Expires: Sat, 27 Oct 1984 08:52:00 GMT GMT");  // Always expired (1)
		//drupal_set_header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified (2)
		header("Cache-Control: no-store, no-cache, must-revalidate");   // HTTP/1.1 (3)
		header("Cache-Control: public");    //(4)
		header("Pragma: no-cache"); // HTTP/1.0   (5)
		//ini_set('cms','session.cache_limiter', 'private');   // (6)
	}

	function base64_serialize($data){
		return base64_encode(serialize($data));
	}
	
	function base64_unserialize($data){
		return unserialize(base64_decode($data));
	}


?>