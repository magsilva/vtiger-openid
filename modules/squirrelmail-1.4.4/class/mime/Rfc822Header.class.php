<?php

/**
 * Rfc822Header.class.php
 *
 * Copyright (c) 2003-2005 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This contains functions needed to handle mime messages.
 *
 * $Id: Rfc822Header.class.php,v 1.1 2005/06/14 13:42:16 indigoleopard Exp $
 */

/*
 * rdc822_header class
 * input: header_string or array
 */
class Rfc822Header {
    var $date = -1,
        $subject = '',
        $from = array(),
        $sender = '',
        $reply_to = array(),
        $to = array(),
        $cc = array(),
        $bcc = array(),
        $in_reply_to = '',
        $message_id = '',
        $references = '',
        $mime = false,
        $content_type = '',
        $disposition = '',
        $xmailer = '',
        $priority = 3,
        $dnt = '',
        $encoding = '',
        $mlist = array(),
        $more_headers = array(); /* only needed for constructing headers
                                    in smtp.php */
    function parseHeader($hdr) {
        if (is_array($hdr)) {
            $hdr = implode('', $hdr);
        }
        /* First we replace \r\n by \n and unfold the header */
        $hdr = trim(str_replace(array("\r\n", "\n\t", "\n "),array("\n", ' ', ' '), $hdr));

        /* Now we can make a new header array with */
        /* each element representing a headerline  */
        $hdr = explode("\n" , $hdr);
        foreach ($hdr as $line) {
            $pos = strpos($line, ':');
            if ($pos > 0) {
                $field = substr($line, 0, $pos);
                if (!strstr($field,' ')) { /* valid field */
                        $value = trim(substr($line, $pos+1));
                        $this->parseField($field, $value);
                }
            }
        }
        if ($this->content_type == '') {
            $this->parseContentType('text/plain; charset=us-ascii');
        }
    }

    function stripComments($value) {
        $result = '';
        $cnt = strlen($value);
        for ($i = 0; $i < $cnt; ++$i) {
            switch ($value{$i}) {
                case '"':
                    $result .= '"';
                    while ((++$i < $cnt) && ($value{$i} != '"')) {
                        if ($value{$i} == '\\') {
                            $result .= '\\';
                            ++$i;
                        }
                        $result .= $value{$i};
                    }
                    $result .= $value{$i};
                    break;
                case '(':
                    $depth = 1;
                    while (($depth > 0) && (++$i < $cnt)) {
                        switch($value{$i}) {
                            case '\\':
                                ++$i;
                                break;
                            case '(':
                                ++$depth;
                                break;
                            case ')':
                                --$depth;
                                break;
                            default:
                                break;
                        }
                    }
                    break;
                default:
                    $result .= $value{$i};
                    break;
            }
        }
        return $result;
    }

    function parseField($field, $value) {
        $field = strtolower($field);
        switch($field) {
            case 'date':
                $value = $this->stripComments($value);
                $d = strtr($value, array('  ' => ' '));
                $d = explode(' ', $d);
                $this->date = getTimeStamp($d);
                break;
            case 'subject':
                $this->subject = $value;
                break;
            case 'from':
                $this->from = $this->parseAddress($value,true);
                break;
            case 'sender':
                $this->sender = $this->parseAddress($value);
                break;
            case 'reply-to':
                $this->reply_to = $this->parseAddress($value, true);
                break;
            case 'to':
                $this->to = $this->parseAddress($value, true);
                break;
            case 'cc':
                $this->cc = $this->parseAddress($value, true);
                break;
            case 'bcc':
                $this->bcc = $this->parseAddress($value, true);
                break;
            case 'in-reply-to':
                $this->in_reply_to = $value;
                break;
            case 'message-id':
                $value = $this->stripComments($value);
                $this->message_id = $value;
                break;
            case 'references':
                $value = $this->stripComments($value);
                $this->references = $value;
                break;
            case 'x-confirm-reading-to':
            case 'return-receipt-to':
            case 'disposition-notification-to':
                $value = $this->stripComments($value);
                $this->dnt = $this->parseAddress($value);
                break;
            case 'mime-version':
                $value = $this->stripComments($value);
                $value = str_replace(' ', '', $value);
                $this->mime = ($value == '1.0' ? true : $this->mime);
                break;
            case 'content-type':
                $value = $this->stripComments($value);
                $this->parseContentType($value);
                break;
            case 'content-disposition':
                $value = $this->stripComments($value);
                $this->parseDisposition($value);
                break;
            case 'user-agent':
            case 'x-mailer':
                $this->xmailer = $value;
                break;
            case 'x-priority':
                $this->priority = $value;
                break;
            case 'list-post':
                $value = $this->stripComments($value);
                $this->mlist('post', $value);
                break;
            case 'list-reply':
                $value = $this->stripComments($value);            
                $this->mlist('reply', $value);
                break;
            case 'list-subscribe':
                $value = $this->stripComments($value);            
                $this->mlist('subscribe', $value);
                break;
            case 'list-unsubscribe':
                $value = $this->stripComments($value);
                $this->mlist('unsubscribe', $value);
                break;
            case 'list-archive':
                $value = $this->stripComments($value);
                $this->mlist('archive', $value);
                break;
            case 'list-owner':
                $value = $this->stripComments($value);
                $this->mlist('owner', $value);
                break;
            case 'list-help':
                $value = $this->stripComments($value);
                $this->mlist('help', $value);
                break;
            case 'list-id':
                $value = $this->stripComments($value);
                $this->mlist('id', $value);
                break;
            default:
                break;
        }
    }

    function getAddressTokens($address) {
        $aTokens = array();
        $aAddress = array();
        $aSpecials = array('(' ,'<' ,',' ,';' ,':');
        $aReplace =  array(' (',' <',' ,',' ;',' :');
        $address = str_replace($aSpecials,$aReplace,$address);
        $iCnt = strlen($address);
        $i = 0;
        while ($i < $iCnt) {
            $cChar = $address{$i};
            switch($cChar)
            {
            case '<':
                $iEnd = strpos($address,'>',$i+1);
                if (!$iEnd) {
                   $sToken = substr($address,$i);
                   $i = $iCnt;
                } else {
                   $sToken = substr($address,$i,$iEnd - $i +1);
                   $i = $iEnd;
                }
                $sToken = str_replace($aReplace, $aSpecials,$sToken);
                if ($sToken) $aTokens[] = $sToken;
                break;
            case '"':
                $iEnd = strpos($address,$cChar,$i+1);
                if ($iEnd) {
                   // skip escaped quotes
                   $prev_char = $address{$iEnd-1};
                   while ($prev_char === '\\' && substr($address,$iEnd-2,2) !== '\\\\') {
                       $iEnd = strpos($address,$cChar,$iEnd+1);
                       if ($iEnd) {
                          $prev_char = $address{$iEnd-1};
                       } else {
                          $prev_char = false;
                       }
                   }
                }
                if (!$iEnd) {
                    $sToken = substr($address,$i);
                    $i = $iCnt;
                } else {
                    // also remove the surrounding quotes
                    $sToken = substr($address,$i+1,$iEnd - $i -1);
                    $i = $iEnd;
                }
                $sToken = str_replace($aReplace, $aSpecials,$sToken);
                if ($sToken) $aTokens[] = $sToken;
                break;
            case '(':
                array_pop($aTokens); //remove inserted space
                $iEnd = strpos($address,')',$i);
                if (!$iEnd) {
                    $sToken = substr($address,$i);
                    $i = $iCnt;
                } else {
                    $iDepth = 1;
                    $iComment = $i;
                    while (($iDepth > 0) && (++$iComment < $iCnt)) {
                        $cCharComment = $address{$iComment};
                        switch($cCharComment) {
                            case '\\':
                                ++$iComment;
                                break;
                            case '(':
                                ++$iDepth;
                                break;
                            case ')':
                                --$iDepth;
                                break;
                            default:
                                break;
                        }
                    }
                    if ($iDepth == 0) {
                        $sToken = substr($address,$i,$iComment - $i +1);
                        $i = $iComment;
                    } else {
                        $sToken = substr($address,$i,$iEnd - $i + 1);
                        $i = $iEnd;
                    }
                }
                // check the next token in case comments appear in the middle of email addresses
                $prevToken = end($aTokens);
                if (!in_array($prevToken,$aSpecials,true)) {
                    if ($i+1<strlen($address) && !in_array($address{$i+1},$aSpecials,true)) {
                        $iEnd = strpos($address,' ',$i+1);
                        if ($iEnd) {
                            $sNextToken = trim(substr($address,$i+1,$iEnd - $i -1));
                            $i = $iEnd-1;
                        } else {
                            $sNextToken = trim(substr($address,$i+1));
                            $i = $iCnt;
                        }
                        // remove the token
                        array_pop($aTokens);
                        // create token and add it again
                        $sNewToken = $prevToken . $sNextToken;
                        if($sNewToken) $aTokens[] = $sNewToken;
                    }
                }
                $sToken = str_replace($aReplace, $aSpecials,$sToken);
                if ($sToken) $aTokens[] = $sToken;
                break;
            case ',':
            case ':':
            case ';':
            case ' ':
                $aTokens[] = $cChar;
                break;
            default:
                $iEnd = strpos($address,' ',$i+1);
                if ($iEnd) {
                    $sToken = trim(substr($address,$i,$iEnd - $i));
                    $i = $iEnd-1;
                } else {
                    $sToken = trim(substr($address,$i));
                    $i = $iCnt;
                }
                if ($sToken) $aTokens[] = $sToken;
            }
            ++$i;
        }
        return $aTokens;
    }
    function createAddressObject(&$aStack,&$aComment,&$sEmail,$sGroup='') {
        //$aStack=explode(' ',implode('',$aStack));
        if (!$sEmail) {
            while (count($aStack) && !$sEmail) {
                $sEmail = trim(array_pop($aStack));
            }
        }
        if (count($aStack)) {
            $sPersonal = trim(implode('',$aStack));
        } else { 
            $sPersonal = '';
        }
        if (!$sPersonal && count($aComment)) {
            $sComment = trim(implode(' ',$aComment));
            $sPersonal .= $sComment;
        }
        $oAddr =& new AddressStructure();
        if ($sPersonal && substr($sPersonal,0,2) == '=?') {
            $oAddr->personal = encodeHeader($sPersonal);
        } else {
            $oAddr->personal = $sPersonal;
        }
 //       $oAddr->group = $sGroup;
        $iPosAt = strpos($sEmail,'@');
        if ($iPosAt) {
           $oAddr->mailbox = substr($sEmail, 0, $iPosAt);
           $oAddr->host = substr($sEmail, $iPosAt+1);
        } else {
           $oAddr->mailbox = $sEmail;
           $oAddr->host = false;
        }
        $sEmail = '';
        $aStack = $aComment = array();
        return $oAddr;
    }

    /*
     * parseAddress: recursive function for parsing address strings and store 
     *               them in an address stucture object.
     *               input: $address = string
     *                      $ar      = boolean (return array instead of only the
     *                                 first element)
     *                      $addr_ar = array with parsed addresses // obsolete
     *                      $group   = string // obsolete
     *                      $host    = string (default domainname in case of 
     *                                 addresses without a domainname)
     *                      $lookup  = callback function (for lookup address
     *                                 strings which are probably nicks
     *                                 (without @ ) ) 
     *               output: array with addressstructure objects or only one
     *                       address_structure object.
     *  personal name: encoded: =?charset?Q|B?string?=
     *                 quoted:  "string"
     *                 normal:  string
     *  email        : <mailbox@host>
     *               : mailbox@host
     *  This function is also used for validating addresses returned from compose
     *  That's also the reason that the function became a little bit huge
     */

    function parseAddress($address,$ar=false,$aAddress=array(),$sGroup='',$sHost='',$lookup=false) {
        $aTokens = $this->getAddressTokens($address);
        $sPersonal = $sEmail = $sComment = $sGroup = '';
        $aStack = $aComment = array();
        foreach ($aTokens as $sToken) {
            $cChar = $sToken{0};
            switch ($cChar)
            {
            case '=':
            case '"':
            case ' ':
                $aStack[] = $sToken; 
                break;
            case '(':
                $aComment[] = substr($sToken,1,-1);
                break;
            case ';':
                if ($sGroup) {
                    $aAddress[] = $this->createAddressObject($aStack,$aComment,$sEmail,$sGroup);
                    $oAddr = end($aAddress);
                    if(!$oAddr || ((isset($oAddr)) && !$oAddr->mailbox && !$oAddr->personal)) {
                        $sEmail = $sGroup . ':;';
                    } 
                    $aAddress[] = $this->createAddressObject($aStack,$aComment,$sEmail,$sGroup);
                    $sGroup = '';
                    $aStack = $aComment = array();
                    break;
                }
            case ',':
                $aAddress[] = $this->createAddressObject($aStack,$aComment,$sEmail,$sGroup);
                break;
            case ':': 
                $sGroup = trim(implode(' ',$aStack));
                $sGroup = preg_replace('/\s+/',' ',$sGroup);
                $aStack = array();
                break;
            case '<':
               $sEmail = trim(substr($sToken,1,-1));
               break;
            case '>':
               /* skip */
               break; 
            default: $aStack[] = $sToken; break;
            }
        }
        /* now do the action again for the last address */
        $aAddress[] = $this->createAddressObject($aStack,$aComment,$sEmail);
        /* try to lookup the addresses in case of invalid email addresses */
        $aProcessedAddress = array();
        foreach ($aAddress as $oAddr) {
          $aAddrBookAddress = array();
          if (!$oAddr->host) {
            $grouplookup = false;
            if ($lookup) {
                 $aAddr = call_user_func_array($lookup,array($oAddr->mailbox));
                 if (isset($aAddr['email'])) {
                     if (strpos($aAddr['email'],',')) {
                         $grouplookup = true;
                         $aAddrBookAddress = $this->parseAddress($aAddr['email'],true);
                     } else {
                         $iPosAt = strpos($aAddr['email'], '@');
                         $oAddr->mailbox = substr($aAddr['email'], 0, $iPosAt);
                         $oAddr->host = substr($aAddr['email'], $iPosAt+1);
                         if (isset($aAddr['name'])) {
                             $oAddr->personal = $aAddr['name'];
                         } else {
                             $oAddr->personal = encodeHeader($sPersonal);
                         }
                     }
                 }
            }
            if (!$grouplookup && !$oAddr->mailbox) {
                $oAddr->mailbox = trim($sEmail);
                if ($sHost && $oAddr->mailbox) {
                    $oAddr->host = $sHost;
                }
            } else if (!$grouplookup && !$oAddr->host) {
                if ($sHost && $oAddr->mailbox) {
                    $oAddr->host = $sHost;
                }
	    }
          }
          if (!$aAddrBookAddress && $oAddr->mailbox) {
              $aProcessedAddress[] = $oAddr;
          } else {
              $aProcessedAddress = array_merge($aProcessedAddress,$aAddrBookAddress); 
          }
        }
        if ($ar) { 
            return $aProcessedAddress;
        } else {
            return $aProcessedAddress[0];
        }
    } 

    function parseContentType($value) {
        $pos = strpos($value, ';');
        $props = '';
        if ($pos > 0) {
           $type = trim(substr($value, 0, $pos));
           $props = trim(substr($value, $pos+1));
        } else {
           $type = $value;
        }
        $content_type = new ContentType($type);
        if ($props) {
            $properties = $this->parseProperties($props);
            if (!isset($properties['charset'])) {
                $properties['charset'] = 'us-ascii';
            }
            $content_type->properties = $this->parseProperties($props);
        }
        $this->content_type = $content_type;
    }
    
    /* RFC2184 */
    function processParameters($aParameters) { 
        $aResults = array();
	$aCharset = array();
	// handle multiline parameters
        foreach($aParameters as $key => $value) {
	    if ($iPos = strpos($key,'*')) {
	        $sKey = substr($key,0,$iPos);
		if (!isset($aResults[$sKey])) {
		    $aResults[$sKey] = $value;
		    if (substr($key,-1) == '*') { // parameter contains language/charset info
		        $aCharset[] = $sKey;
		    }
	        } else {
		    $aResults[$sKey] .= $value;
		}
	    }
        }
	foreach ($aCharset as $key) {
	    $value = $aResults[$key];
	    // extract the charset & language
	    $charset = substr($value,0,strpos($value,"'"));
	    $value = substr($value,strlen($charset)+1);
	    $language = substr($value,0,strpos($value,"'"));
	    $value = substr($value,strlen($charset)+1);
	    // FIX ME What's the status of charset decode with language information ????
	    $value = charset_decode($charset,$value);
	    $aResults[$key] = $value;
	}
	return $aResults;    
    }

    function parseProperties($value) {
        $propArray = explode(';', $value);
        $propResultArray = array();
        foreach ($propArray as $prop) {
            $prop = trim($prop);
            $pos = strpos($prop, '=');
            if ($pos > 0)  {
                $key = trim(substr($prop, 0, $pos));
                $val = trim(substr($prop, $pos+1));
                if (strlen($val) > 0 && $val{0} == '"') {
                    $val = substr($val, 1, -1);
                }
                $propResultArray[$key] = $val;
            }
        }
        return $this->processParameters($propResultArray);
    }

    function parseDisposition($value) {
        $pos = strpos($value, ';');
        $props = '';
        if ($pos > 0) {
            $name = trim(substr($value, 0, $pos));
            $props = trim(substr($value, $pos+1));
        } else {
            $name = $value;
        }
        $props_a = $this->parseProperties($props);
        $disp = new Disposition($name);
        $disp->properties = $props_a;
        $this->disposition = $disp;
    }

    function mlist($field, $value) {
        $res_a = array();
        $value_a = explode(',', $value);
        foreach ($value_a as $val) {
            $val = trim($val);
            if ($val{0} == '<') {
                $val = substr($val, 1, -1);
            }
            if (substr($val, 0, 7) == 'mailto:') {
                $res_a['mailto'] = substr($val, 7);
            } else {
                $res_a['href'] = $val;
            }
        }
        $this->mlist[$field] = $res_a;
    }

    /*
     * function to get the addres strings out of the header.
     * Arguments: string or array of strings !
     * example1: header->getAddr_s('to').
     * example2: header->getAddr_s(array('to', 'cc', 'bcc'))
     */
    function getAddr_s($arr, $separator = ',',$encoded=false) {
        $s = '';

        if (is_array($arr)) {
            foreach($arr as $arg) {
                if ($this->getAddr_s($arg, $separator, $encoded)) {
                    $s .= $separator;
                }
            }
            $s = ($s ? substr($s, 2) : $s);
        } else {
            $addr = $this->{$arr};
            if (is_array($addr)) {
                foreach ($addr as $addr_o) {
                    if (is_object($addr_o)) {
                        if ($encoded) {
                            $s .= $addr_o->getEncodedAddress() . $separator;
                        } else {
                            $s .= $addr_o->getAddress() . $separator;
                        }
                    }
                }
                $s = substr($s, 0, -strlen($separator));
            } else {
                if (is_object($addr)) {
                    if ($encoded) {
                        $s .= $addr->getEncodedAddress();
                    } else {
                        $s .= $addr->getAddress();
                    }
                }
            }
        }
        return $s;
    }

    function getAddr_a($arg, $excl_arr = array(), $arr = array()) {
        if (is_array($arg)) {
            foreach($arg as $argument) {
                $arr = $this->getAddr_a($argument, $excl_arr, $arr);
            }
        } else {
            $addr = $this->{$arg};
            if (is_array($addr)) {
                foreach ($addr as $next_addr) {
                    if (is_object($next_addr)) {
                        if (isset($next_addr->host) && ($next_addr->host != '')) {
                            $email = $next_addr->mailbox . '@' . $next_addr->host;
                        } else {
                            $email = $next_addr->mailbox;
                        }
                        $email = strtolower($email);
                        if ($email && !isset($arr[$email]) && !isset($excl_arr[$email])) {
                            $arr[$email] = $next_addr->personal;
                        }
                    }
                }
            } else {
                if (is_object($addr)) {
                    $email  = $addr->mailbox;
                    $email .= (isset($addr->host) ? '@' . $addr->host : '');
                    $email  = strtolower($email);
                    if ($email && !isset($arr[$email]) && !isset($excl_arr[$email])) {
                        $arr[$email] = $addr->personal;
                    }
                }
            }
        }
        return $arr;
    }
    
    function findAddress($address, $recurs = false) {
        $result = false;
        if (is_array($address)) {
            $i=0;
            foreach($address as $argument) {
                $match = $this->findAddress($argument, true);
                $last = end($match);
                if ($match[1]) {
                    return $i;
                } else {
                    if (count($match[0]) && !$result) {
                        $result = $i;
                    }
                }
                ++$i;        
            }
        } else {
            if (!is_array($this->cc)) $this->cc = array();
            $srch_addr = $this->parseAddress($address);
            $results = array();
            foreach ($this->to as $to) {
                if ($to->host == $srch_addr->host) {
                    if ($to->mailbox == $srch_addr->mailbox) {
                        $results[] = $srch_addr;
                        if ($to->personal == $srch_addr->personal) {
                            if ($recurs) {
                                return array($results, true);
                            } else {
                                return true;
                            }
                        }
                    }
                }
            }
             foreach ($this->cc as $cc) {
                if ($cc->host == $srch_addr->host) {
                    if ($cc->mailbox == $srch_addr->mailbox) {
                        $results[] = $srch_addr;
                        if ($cc->personal == $srch_addr->personal) {
                            if ($recurs) {
                                return array($results, true);
                            } else {
                                return true;
                            }
                        }
                    }
                }
            }
            if ($recurs) {
                return array($results, false);
            } elseif (count($result)) {
                return true;
            } else {
                return false;
            }        
        }
        //exit;
        return $result;
    }

    function getContentType($type0, $type1) {
        $type0 = $this->content_type->type0;
        $type1 = $this->content_type->type1;
        return $this->content_type->properties;
    }
}

?>