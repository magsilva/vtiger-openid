<?php
/**
 * Library to authenticate to an OpenID server.
 * 
 * Copyright (C) 2007 Marco Aurélio Graciotto Silva <magsilva@gmail.com>
 */

require_once('openid.php');
 
/*
 * Authenticator class
 */
class OpenIdAuthenticator
{
	var $openid_client;

	function OpenIdAuthenticator()
	{
		$this->openid_client = new OpenIDClient();
		if ($this->openid_client == null) {
			return null;
		}
	}
	
	function authenticate_phase1($openid_url, $redirect_url)
	{
		$scheme = 'http';
		if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {
			$scheme .= 's';
		}
		
		$returnto_url = sprintf("$scheme://%s:%s%s?module=Users&action=Authenticate&return_module=Users&return_action=Login&user_name=$openid_url",
			$_SERVER['SERVER_NAME'],
			$_SERVER['SERVER_PORT'],
			$_SERVER['PHP_SELF']);
		
		return $this->openid_client->doAuthRequest($openid_url, $returnto_url);
	}

	function authenticate_phase2()
	{
		return $this->openid_client->handleAuthResponse();
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
	// What a shame, vTiger does not redirect after login?
	$redirect_url = null;
	$openid_handler = new OpenIdAuthenticator();
	
	if ($openid_handler->openid_client->isAuthResponseConditionOk()) {
		$result = $openid_handler->authenticate_phase2();
	} else {
		$result = $openid_handler->authenticate_phase1($user_name, $redirect_url);
	}
	if ($result == false) {
		return NULL;
	}

	return $result;
}
?>