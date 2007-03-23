<?php

/*********************************************
 * Library to authenticate to LDAP servers for
 * vTiger CRM.  Written by
 *
 * Daniel Jabbour
 * iWebPress Incorporated, www.iwebpress.com
 * djabbour - a t - iwebpress - d o t - com
 *********************************************/

/**
 * Function to authenticate users to LDAP
 *
 * @param string $authUser -  Username to authenticate
 * @param string $authPW - Cleartext password
 * @return NULL on failure, user's info (in an array) on bind
 */
function ldapAuthenticate($authUser, $authPW) {
	global $AUTHCFG;

	if ($authUser != "" && $authPW != "") {
		$ds=@ldap_connect($AUTHCFG['ldap_host'],$AUTHCFG['ldap_port']);
		@ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3); //Try version 3.  Will fail and default to v2.
		
		$bind = false;
		
		if (!empty($AUTHCFG['ldap_username'])) {
			$bind = @ldap_bind($ds, $AUTHCFG['ldap_username'], $AUTHCFG['ldap_pass']);
		} else {
			$bind = @ldap_bind($ds); //attempt an anonymous bind if no user/pass specified in config.php
		}
		
		if (!$bind) {
			return NULL; //bind failed
		}
		
		$r = @ldap_search( $ds, $AUTHCFG['ldap_basedn'], $AUTHCFG['ldap_uid'] . '=' . $authUser);
		if ($r) {
			$result = @ldap_get_entries( $ds, $r);
			if ($result[0]) {
				if (@ldap_bind( $ds, $result[0]['dn'], $authPW) ) {
					return $result[0];
				}
			}
		}
	}
	return NULL;
}
?>