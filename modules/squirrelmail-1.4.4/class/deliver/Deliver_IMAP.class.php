<?php
/**
 * Deliver_IMAP.class.php
 *
 * Copyright (c) 1999-2005 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * Delivery backend for the Deliver class.
 *
 * $Id: Deliver_IMAP.class.php,v 1.1 2005/06/14 13:40:04 indigoleopard Exp $
 */

require_once(SM_PATH . 'class/deliver/Deliver.class.php');

class Deliver_IMAP extends Deliver {

    function getBcc() {
       return true;
    }
    
    /* to do: finishing the imap-class so the initStream function can call the 
       imap-class */
}


?>
