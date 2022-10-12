<?php

#**** SMS NBS Class 
#***Author of the script
#***Name: Adeleke Ojora
#***Email : ojorajedidiah@gmail.com
#***Date created: 08/10/2022
#***Date modified: 


#   This class is an extension of the Nigeria Bulk SMS API. 
#   It was recoded to suit the need.

class sms_nbs
{
  //declaring SMS variables
  public $sms_status;
  public $sms_result = '';
  public $sms_balance=0;

  protected $url;
  protected $username;
  protected $apikey;

  //activating SMS
  function __construct($set_conf)
  {
    $country_code = '234';
    $this->url = $set_conf['url'];
    $this->username = $set_conf['uname'];
    $this->apikey = $set_conf['apikey'];
  }



  //Function to connect to SMS sending server using HTTP POST
  public function sendMessage($sender, $message, $numbers)
  {
    $sdr = $sender;
    $msg = $message;
    $num = $numbers;

    $data = array('username' => $this->username, 'password' => $this->apikey, 'sender' => $sdr, 'message' => $msg, 'mobiles' => $num);
    $data = http_build_query($data);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $this->url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    $result = curl_exec($curl);
    if ($result !== false) {
      $rst = json_decode($result);
      $this->sms_status = $this->getResult($rst);
      $this->sms_balance=$this->getBalance();
    }
    curl_close($curl);
  }

  private function getResult($res)
  {
    $rtn = '';
    if (isset($res->status) && strtoupper($res->status) == 'OK') {
      $rtn = 'success';
      $this->sms_result = 'Message sent at N' . $res->price;
    } else if (isset($res->error)) {
      $rtn = 'error';
      $this->sms_result = 'Message failed - error: ' . $res->error;
    } else {
      $rtn = 'error';
      $this->sms_result = 'Unable to process request';
    }
    return $rtn;
  }

  private function getBalance()
  {
    $rtn=0;
    $data = array('username' => $this->username, 'password' => $this->apikey, 'action' => 'balance');
    $data = http_build_query($data);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $this->url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    $result = curl_exec($curl);
    if ($result !== false) {
      $rst = json_decode($result);
      $rtn = $rst->balance;
    }
    curl_close($curl);
    return $rtn;
  }
  
}
