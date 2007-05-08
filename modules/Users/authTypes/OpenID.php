<?php
/**
 * Library to authenticate to an OpenID server.
 * 
 * Copyright (C) 2007 Marco Aurélio Graciotto Silva <magsilva@gmail.com>
 */

require_once('Auth/OpenID/Consumer.php');
require_once('Auth/OpenID/FileStore.php');
require_once('Auth/OpenID/SReg.php');
 
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

	function OpenIdAuthenticator()
	{
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
		
		$returnto_url = sprintf("$scheme://%s:%s%s?module=Users&action=Authenticate&return_module=Users&return_action=Login&phase=2&user_name=$openid_url",
			$_SERVER['SERVER_NAME'],
			$_SERVER['SERVER_PORT'],
			$_SERVER['PHP_SELF']);
		
		$trusted_root = sprintf("$scheme://%s:%s%s",
			$_SERVER['SERVER_NAME'],
			$_SERVER['SERVER_PORT'],
			dirname($_SERVER['PHP_SELF']));
		
		// Begin the OpenID authentication process.
		$auth_request = $this->consumer->begin($openid_url);

		// Handle failure status return values.
		if (! $auth_request) {
			return false;
		}

		$sreg_request = Auth_OpenID_SRegRequest::build(
			// Required
			array('nickname'),
			// Optional
			array('fullname', 'email'));

		if ($sreg_request) {
			$auth_request->addExtension($sreg_request);
		}
		
		// Redirect the user to the OpenID server for authentication.
		// Store the token for this authentication so we can verify the
		// response.
		
		// For OpenID 1, send a redirect.  For OpenID 2, use a Javascript
		// form to send a POST request to the server.
		if ($auth_request->shouldSendRedirect()) {
			$redirect_url = $auth_request->redirectURL($trusted_root, $returnto_url);

			// If the redirect URL can't be built, display an error message.
			if (Auth_OpenID::isFailure($redirect_url)) {
				return false;
			} else {
				// Send redirect.
				header("Location: ".$redirect_url);
				exit();
			}
		} else {
			// Generate form markup and render it.
			$form_id = 'openid_message';
			$form_html = $auth_request->formMarkup($trusted_root, $returnto_url,
				false, array('id' => $form_id));

			// Display an error if the form markup couldn't be generated;
			// otherwise, render the HTML.
			if (Auth_OpenID::isFailure($form_html)) {
				return false;
			} else {
				$page_contents = array(
					'<html><head><title>',
					'OpenID transaction in progress',
					'</title></head>',
					'<body onload="document.getElementById(' . $form_id . ').submit()">',
					$form_html,
					'</body></html>');
				print implode("\n", $page_contents);
			}
		}
	}

	function authenticate_phase2($username)
	{
		$response = $this->consumer->complete();
		
		
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

	    	/*	
        	$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
			$sreg = $sreg_resp->contents();
			if (@$sreg['email']) {
            	$success .= "  You also returned '".$sreg['email']."' as your email.";
	        }
			if (@$sreg['nickname']) {
        	    $success .= "  Your nickname is '".$sreg['nickname']."'.";
        	    }
			if (@$sreg['fullname']) {
            	$success .= "  Your fullname is '".$sreg['fullname']."'.";
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