<?php
/*
 * Copyright 1999/2000 by Gero Kohnert
 *
 * CVS Info:  $Id: logout.php 85 2005-01-17 05:11:31Z saraj $
 * $Author: saraj $
 */

 global $calpath;
 $calpath = "modules/Calendar/";
 include_once $calpath .'webelements.p3';
 include_once $calpath .'permission.p3';

 check_user();

 $auth = array();
 $al = split(" ",$tutos[authtype]);
 $cnt = 0;
 foreach ( $al as $a ) {
   require_once $calpath .'auth/auth_'. $a .'.pinc';
   $x = "auth_".$tutos[authtype];
   $auth[$cnt++] = new $x();
 }


 $gotourl = $auth[0]->logout();

 if ( isset($_GET['db']) ) {
   $gotourl= addUrlParameter($gotourl,"db=". $_GET['db']);
   $gotourl= addUrlParameter($gotourl,"msg=RELOGIN");
 } else {
   $gotourl= addUrlParameter($gotourl,"msg=goodbye");
 }
 
 //Header("Status: 302 Moved Temporarily");
 //Header("Location: ". getBaseUrl(). $gotourl );
 $dbconn->Close();
?>
