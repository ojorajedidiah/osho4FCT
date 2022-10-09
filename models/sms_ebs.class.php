<?php

#**** SMS Class 
#***Author of the script
#***Name: Adeleke Ojora
#***Email : ojorajedidiah@gmail.com
#***Date created: 29/09/2021
#***Date modified: 


#   This class is an extension of the eBulkSMS SMS class. 
#   It was recoded to suit the need.

class SMS
{
  //declaring SMS variables
  protected $json_data;
  public $sms_status;
  protected $sms_error = '';

  //activating SMS
  function __construct($sendername,$messagetext, $recipients)
  {
    $gsm = array();

    $country_code = '234';
    
    $url='https://api.ebulksms.com:4433/sendsms.json';
    $flash=0;
    $username='ojorajedidiah@gmail.com';
    $apikey='536b397715dd2823fa9b1be75d78831af32a7f4e';

    $arr_recipient = explode(',', $recipients);
    foreach ($arr_recipient as $recipient) {
      $mobilenumber = trim($recipient);
      if (substr($mobilenumber, 0, 1) == '0') {
        $mobilenumber = $country_code . substr($mobilenumber, 1);
      } elseif (substr($mobilenumber, 0, 1) == '+') {
        $mobilenumber = substr($mobilenumber, 1);
      }
      $generated_id = uniqid('int_', false);
      $generated_id = substr($generated_id, 0, 30);
      $gsm['gsm'][] = array('msidn' => $mobilenumber, 'msgid' => $generated_id);
    }
    $message = array(
      'sender' => $sendername,
      'messagetext' => $messagetext,
      'flash' => "{$flash}",
    );

    $request = array('SMS' => array(
      'auth' => array(
        'username' => $username,
        'apikey' => $apikey
      ),
      'message' => $message,
      'recipients' => $gsm
    ));
    $this->json_data = json_encode($request);
    if ($this->json_data) {
      $response = $this->doPostRequest($url, $this->json_data, array('Content-Type: application/json'));
      $this->result = json_decode($response);
      $this->sms_status=$this->result->response->status;
      return true;
    } else {
      $this->sms_error=$this->result->response->status;
      return false;
    }
  }

  //Function to connect to SMS sending server using HTTP POST
  private function doPostRequest($url, $arr_params, $headers = array('Content-Type: application/x-www-form-urlencoded'))
  {
    $response = array('code' => '', 'body' => '');
    $final_url_data = $arr_params;
    if (is_array($arr_params)) {
      $final_url_data = http_build_query($arr_params, '', '&');
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $final_url_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    try {
      $response['body'] = curl_exec($ch);
      $response['code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if ($response['code'] != '200') {
        throw new Exception("Problem reading data from $url");
      }
      curl_close($ch);
    } catch (Exception $e) {
      echo 'cURL error: ' . $e->getMessage();
    }
    return $response['body'];
  }


}