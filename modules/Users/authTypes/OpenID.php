<?php
/**
 * Library to authenticate to an OpenID server.
 * 
 * Copyright (C) 2007 Marco Aurélio Graciotto Silva <magsilva@gmail.com>
 */

/**
 * Required by OpenID checkup.
 */
require_once('Auth/OpenID.php');
require_once('Services/Yadis/Yadis.php');

/**
 * Require the OpenID consumer code.
 */
require_once('Auth/OpenID/Consumer.php');
require_once('Auth/OpenID/FileStore.php');
 
/*
 * Authenticator class
 */
class OpenIdAuthenticator
{
	/**
	 * This is where the OpenID information will be stored.
	 */
	var $store_path = "/tmp/_php_consumer_test";
	
	var $store;
	
	var $consumer;

	var $user_id;
	var $username;
	
	function checkMath()
	{
		global $_Auth_OpenID_math_extensions;
		$ext = Auth_OpenID_detectMathLibrary($_Auth_OpenID_math_extensions);
		if (! isset($ext['extension']) || !isset($ext['class'])) {
			// Your PHP installation does not include big integer math
			// support. This support is required if you wish to run a
			// secure OpenID server without using SSL.
			return false;
		} else {
			switch ($ext['extension']) {
				case 'bcmath':
					break;
				case 'gmp':
					break;
				default:
					$class = $ext['class'];
					$lib = new $class();
					$one = $lib->init(1);
					$two = $lib->add($one, $one);
					$t = $lib->toString($two);
					if ($t != '2') {
						return false;
					}
			}
		}
		return true;
	}
		
	function checkRandom()
	{
		if (Auth_OpenID_RAND_SOURCE === null) {
			// Using (insecure) pseudorandom number source, because
			// Auth_OpenID_RAND_SOURCE has been defined as null.
			return false;
		}
		
		$numbytes = 6;
		$f = @fopen(Auth_OpenID_RAND_SOURCE, 'r');
		if ($f !== false) {
			$data = fread($f, $numbytes);
			$stat = fstat($f);
			$size = $stat['size'];
			fclose($f);
		} else {
			$data = null;
			$size = true;
		}
		
		if ($f !== false) {
			$dataok = (strlen($data) == $numbytes);
			$ok = $dataok && ! $size;
		} else {
	        $ok = false;
    	}
    	
    	return $ok;		
	}
	
	
	function checkStores()
	{
		foreach (array('sqlite', 'mysql', 'pgsql') as $dbext) {
			if (extension_loaded($dbext) || @dl($dbext . '.' . PHP_SHLIB_SUFFIX)) {
				$found[] = $dbext;
			}
		}
		
		if (count($found) == 0) {
			return false;
		}

		return true;
	}
	
	function checkfetcher()
	{
		$ok = true;
		$fetcher = Services_Yadis_Yadis::getHTTPFetcher();
		$fetch_url = 'http://www.openidenabled.com/resources/php-fetch-test';
		$expected_url = $fetch_url . '.txt';
		$result = $fetcher->get($fetch_url);
		
		if (isset($result)) {
			// list ($code, $url, $data) = $result;
			if ($result->status != '200') {
				$ok = false;
			}
			$url = $result->final_url;
			if ($url != $expected_url) {
				$ok = false;
			}
		} else {
			$ok = false;
		}
		
		return $ok;
	}
	
	
	function checkXml()
	{
		global $__Services_Yadis_xml_extensions;
		
		// Try to get an XML extension.
		$ext = Services_Yadis_getXMLParser();
		
		if ($ext !== null) {
			return true;
		} else {
			return false;
		}
	}

	function checkDependencies()
	{
		$result = true;
	
		$result &= $this->checkMath();
		$result &= $this->checkRandom();
		$result &= $this->checkStores();
		$result &= $this->checkFetcher();
		$result &= $this->checkXml();
	
		return $result;
	}
	
	function OpenIdAuthenticator()
	{
		$checklist = $this->checkDependencies();
		if ($checklist == false) {
			return null;
		}

		if (!file_exists($this->store_path) && ! mkdir($this->store_path)) {
			return null;
		}

		$this->store = new Auth_OpenID_FileStore($this->store_path);
		$this->consumer = new Auth_OpenID_Consumer($this->store);
	}
	
	function authenticate_phase1($openid_url, $redirect_url)
	{
		if (empty($openid_url)) {
			return false;
		}
			
		$scheme = 'http';
		if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {
			$scheme .= 's';
		}
		
		$process_url = sprintf("$scheme://%s:%s%s?module=Users&action=Authenticate&return_module=Users&return_action=Login&phase=2&user_name=$openid_url",
			$_SERVER['SERVER_NAME'],
			$_SERVER['SERVER_PORT'],
			$_SERVER['PHP_SELF']);
		
		$trust_root = sprintf("$scheme://%s:%s%s",
			$_SERVER['SERVER_NAME'],
			$_SERVER['SERVER_PORT'],
			dirname($_SERVER['PHP_SELF']));
		
		// Begin the OpenID authentication process.
		$auth_request = $this->consumer->begin($openid_url);

		// Handle failure status return values.
		if (! $auth_request) {
			return false;
		}
		

		$auth_request->addExtensionArg('sreg', 'optional', 'email');
		
		// Redirect the user to the OpenID server for authentication.  Store
		// the token for this authentication so we can verify the response.
		$redirect_url = $auth_request->redirectURL($trust_root, $process_url);
		header('Location: ' . $redirect_url);
	}

	function authenticate_phase2($username)
	{
		session_start();
		
		$response = $this->consumer->complete($_GET);
		if ($response->status == Auth_OpenID_CANCEL) {
	    	// This means the authentication was cancelled.
	    	$msg = 'Verification cancelled.';
	    	return false;
		} else if ($response->status == Auth_OpenID_FAILURE) {
	    	$msg = "OpenID authentication failed: " . $response->message;
	    	return false;
		} else if ($response->status == Auth_OpenID_SUCCESS) {
	    	// This means the authentication succeeded.
	    	$openid = $response->identity_url;
	    	$esc_identity = htmlspecialchars($openid, ENT_QUOTES);
	    	if ($response->endpoint->canonicalID) {
	        	$success .= '  (XRI CanonicalID: '.$response->endpoint->canonicalID.') ';
	    	}
	    	$sreg = $response->extensionResponse('sreg');

			/*
			$this->username = $username;	    	
			$this->user_id = $this->userExists($username); 
	    	if (! $this->user_id) {
				$this->createsqluser($username, '', '');
				$this->user_id = $this->userExists($username);  
			}
			*/
		
			return true;
		} else {
			return false;
		}
	}
}
 

/**
 * Function to authenticate against an OpenID server.
 *
 * @param string $authUser - Username to authenticate
 * @return NULL on failure, user's info (in an array) on bind
 */
function openidAuthenticate($user_name)
{
	global $AUTHCFG;

	$result = null;
	$phase = $_REQUEST['phase'];
	// What a shame, vTiger does not redirect after login?
	$redirect_url = null;
	$openid_handler = new OpenIdAuthenticator();
	
	if ($phase == null) {
		$phase = 1;
	}

	switch ($phase) {
		case 1:
			$result = $openid_handler->authenticate_phase1($user_name, $redirect_url);
			break;
		case 2:
			$result = $openid_handler->authenticate_phase2($user_name);
			break;
	}
	if ($result == false) {
		return NULL;
	}
	return $result;
}
?>