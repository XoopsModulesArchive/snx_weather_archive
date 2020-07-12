<? 
if(!class_exists("http_class")):
require_once(XOOPS_ROOT_PATH."/modules/snx_weather/http/http.php");
$cookies=array();

function posting($url,$referer,$args,$okstr,$cookie_header="", $followRedir=1) {
	global $session_id;
	global $debug;
	global $cookies;
        global $lasturl;
		$OK=false;
        set_time_limit(0);
        $http=new http_class;
        $http->timeout=0;
        $http->data_timeout=0;
        $http->debug=0;
        $http->html_debug=1;
		$http->follow_redirect=$followRedir;
		$http->user_agent="Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7) Gecko/20040626 Firefox/0.8";
		$error=$http->GetRequestArguments($url,$arguments);
		
		if($cookie_header) {
			$arguments["Headers"]["Cookie"]=$cookie_header;
		}
        $arguments["RequestMethod"]="POST";
        $arguments["PostValues"]=$args["PostValues"];
		while(list($key,$val)=each($args)) {
			$arguments[$key]=$args[$key];
		}
        $arguments["Headers"]["Referer"]=$referer;
	$error=$http->Open($arguments);

	if($error=="")
	{
		$error=$http->SendRequest($arguments);
		if($error=="")
		{
			$headers=array();
			$error=$http->ReadReplyHeaders($headers);
			if($error=="")
			{
				for(Reset($headers),$header=0;$header<count($headers);Next($headers),$header++)
				{
					$header_name=Key($headers);
					if(GetType($headers[$header_name])=="array")
					{
						for($header_value=0;$header_value<count($headers[$header_name]);$header_value++) {
							if($header_name=="set-cookie") {
                                                 	       $cookies["set-cookie"]=$headers[$header_name][$header_value];
                                                	}
						}
					}
					else {
						if($header_name=="set-cookie") {
                                         	       $cookies["set-cookie"]=$headers[$header_name];
                                        	}
					}
				}


				for(;;)
				{
					$error=$http->ReadReplyBody($body,2048);
					if($error!=""
					|| strlen($body)==0)
						break;
					$OK.=$body;
				}
			}
		}
		$http->Close();
	}
/*	if(strlen($error))
		 echo "<CENTER><H2>Error: ",$error,"</H2><CENTER>\n"; */
	return $OK;
}

function getting($url,$referer,$okstr,$cookie_header="", $followRedir=1,$args=array()) {
	global $session_id;
	global $lasturl;
	global $cookies;
	$debug=0;
	$OK=false;
	set_time_limit(0);
	$http=new http_class;

	/* Connection timeout */
	$http->timeout=10;
	$http->user_agent="Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7) Gecko/20040626 Firefox/0.8";
	$http->user_agent="WEATHER SHOULD BE FREE AND AVAILABLE FOR EVERYONE";
	/* Data transfer timeout */
	$http->data_timeout=10;

	/* Output debugging information about the progress of the connection */
	$http->debug=0;

	/* Format dubug output to display with HTML pages */
	$http->html_debug=1;

	/*
	 *  If you want to the class to follow the URL of redirect responses
	 *  set this variable to 1.
	 */
	$http->follow_redirect=$followRedir;

	/*
	 *  How many consecutive redirected requests the class should follow.
	 */
	$http->redirection_limit=5;

	/*
	 *  If your DNS always resolves non-existing domains to a default IP
	 *  address to force the redirection to a given page, specify the
	 *  default IP address in this variable to make the class handle it
	 *  as when domain resolution fails.
	 */
	$http->exclude_address="";

	/*
	 *  If basic authentication is required, specify the user name and
	 *  password in these variables.
	 */
	$user="";
	$password="";
	$authentication=(strlen($user) ? UrlEncode($user).":".UrlEncode($password)."@" : "");

/*
	Do you want to access a page via SSL?
	Just specify the https:// URL.
	$url="https://www.openssl.org/";
*/

	/*
	 *  Generate a list of arguments for opening a connection and make an
	 *  HTTP request from a given URL.
	 */
	$error=$http->GetRequestArguments($url,$arguments);
	
	if($cookie_header) {
			$arguments["Headers"]["Cookie"]=$cookie_header;
	}

	/* Set additional request headers */
	$arguments["Headers"]["Referer"]=$referer;
	while(list($key,$val)=each($args)) {
		$arguments[$key]=$args[$key];
	}
/*
	Is it necessary to specify a certificate to access a page via SSL?
	Specify the certificate file this way.
	$arguments["SSLCertificateFile"]="my_certificate_file.pem";
	$arguments["SSLCertificatePassword"]="some certificate password";
*/

	if($debug) echo "<H2><LI>Opening connection to:</H2>\n<PRE>",HtmlEntities($arguments["HostName"]),"</PRE>\n";
	$error=$http->Open($arguments);

	if($error=="")
	{
		if($debug) echo "<H2><LI>Sending request for page:</H2>\n<PRE>";
		if($debug) echo HtmlEntities($arguments["RequestURI"]),"\n";
		if(strlen($user))
		if($debug) echo "\nLogin:    ",$user,"\nPassword: ",str_repeat("*",strlen($password));
		if($debug) echo "</PRE>\n";
		flush();
		$error=$http->SendRequest($arguments);

		if($error=="")
		{
			if($debug) echo "<H2><LI>Request:</LI</H2>\n<PRE>\n".HtmlEntities($http->request)."</PRE>\n";
			if($debug) echo "<H2><LI>Request headers:</LI</H2>\n<PRE>\n";
			$headers=array();
			$error=$http->ReadReplyHeaders($headers);
			if($error=="")
			{
				if($debug) echo "<H2><LI>Response headers:</LI</H2>\n<PRE>\n";
				for(Reset($headers),$header=0;$header<count($headers);Next($headers),$header++)
				{
					$header_name=Key($headers);
					if(GetType($headers[$header_name])=="array")
					{
						for($header_value=0;$header_value<count($headers[$header_name]);$header_value++) {
							if($header_name=="set-cookie") $cookies["set-cookie"]=$headers[$header_name][$header_value];
							if($debug) echo $header_name.": ".$http->request_headers[$header_name][$header_value],"\r\n";
						}
					}
				else {
						if($header_name=="set-cookie") $cookies["set-cookie"]=$headers[$header_name];
						if($debug) echo $header_name.": ".$http->request_headers[$header_name],"\r\n";
					}
				}
				if($debug) echo "</PRE>\n";

				if($debug) echo "<H2><LI>Response body:</LI</H2>\n<PRE>\n";
				for(;;)
				{
					$error=$http->ReadReplyBody($body,1000);
					if($error!=""
					|| strlen($body)==0)
						break;
					if($debug) echo HtmlSpecialChars($body);
					$OK.=$body;
				}
				if($debug) echo "</PRE>\n";
				flush();				
			}
		}
		$http->Close();
	}
/*	if(strlen($error))
		 echo "<CENTER><H2>Error: ",$error,"</H2><CENTER>\n";*/
	return $OK;
}
/* ***************************************************** */
/*                  	MAIN				 */
/* ***************************************************** */
endif;
?>
