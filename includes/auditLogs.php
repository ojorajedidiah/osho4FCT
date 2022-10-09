<?php 
$qry='';$yyy='';
$ip = getIPAddress();
// $userN = ucwords(strtolower($_SESSION['fullname']));

// if (isset($_REQUEST)) {
//   $usrn=$_SESSION['username'];
//   $qry = basename($_SERVER['REQUEST_URI']);
//   $strW = explode("?", $qry);
//   //die('the querystring is '. $strW[0]);


// }



function getIPAddress()
{
  $ipadd = '';

  try {
    //die('testing the login 2');
    if (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'] != '' && $_SERVER['HTTP_CLIENT_IP'] != '127.0.0.1') {
      $ipadd = $_SERVER['HTTP_CLIENT_IP'];
    } else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] != '' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
      $ipadd = $_SERVER['REMOTE_ADDR'];
    } else if (isset($_SERVER['REMOTE_HOST']) && $_SERVER['REMOTE_HOST'] != '' && $_SERVER['REMOTE_HOST'] != '127.0.0.1') {
      $ipadd = $_SERVER['REMOTE_HOST'];
    }
  } catch (Exception $ex) {
    die('Error getIPAddress ' . $ex->getMessage());
  }

  return $ipadd; 
}


?>