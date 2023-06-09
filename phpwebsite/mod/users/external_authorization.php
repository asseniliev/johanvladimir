<?php
/*
 * Function to use the VMS authorization technique in
 * lue of an honest LDAP connection
 *
 * @author Mike Wilson <mike@NOSPAM.tux.appstate.edu>
 * @modified Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @param username : string with username
 * @param password : string with password
 * @return String with authorized user type FACULTY/STAFF as example
 *         OR returns false if error or unauthorized.
 *
 * This is an example of external authorization. Basically you just
 * need an 'authorize' function that accepts an username and password.
 * It will return TRUE or FALSE.
 *
 * You can also add a processUser function. It accepts a pointer
 * to the user object. It will only be called if an externally authorized
 * user is not in the local system.
 */

function authorize($username, $password) {
  $address = null;
  $port    = null;
  $data    = null;
  
  if(empty($password) || (strlen($password) == 0)) {
    return FALSE;
  }

  if(!($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP))) {
    return FALSE;
  }

  if(!socket_connect($socket, $address, $port)) {
    return FALSE;
  }

  if(!socket_write($socket, $username."/".$password."\r\n")) {
    return FALSE;
  }

  while(($buf = socket_read($socket, 512)) !== false && ($buf!="")) {
    $data .= $buf;
  }


  if ($data == "INVALID")
    return FALSE;
  elseif (preg_match("/ok/i", $data))
    return TRUE;

  return FALSE;
}

function processUser(&$user){
  $user->email = $user->username . "@emaildomain.com";
}

?>