<?php
// include_once('models/sms_ebs.class.php');
include_once('models/sms_nbs.class.php');
// include('models/databaseConnection.class.php');

// $sndSMS=new SMS($sendername,$messagetext, $recipients);
// configure sms class 
// get message 
// select recipients records from db
// attempt sending sms
// report (message_report scheme)
set_time_limit(12000);

if (isset($_SESSION['canSendSMS']) && $_SESSION['canSendSMS'] == 1) {
 

  $bal = 0;
  $report = '';

  $sms_msg = getSMSMessage();
  $sms_cfg = getConfigSettings('nbs');
  $sms_rpt = getRecipients();

  try {
    
    $sms = new sms_nbs($sms_cfg);
    foreach ($sms_rpt as $rec) {
      $msg = 'Hello ' . $rec['ele_Name'] . ', ' . $sms_msg . getTYurl();
      $rec['message'] = $msg;
      // die('i enter here now '.$msg);
      $sms->sendMessage('Osho4FCT', $msg, $rec['ele_Number']);
      //$bal = $sms->sms_balance;
      if ($sms->sms_status == 'success') {
        $report .= saveSuccess($rec, $sms->sms_result) . '<br>';
      } else {
        $report .= handleSMSError($rec, $sms->sms_result) . '<br>';
      }
    }
  } catch (PDOException $e) {
    trigger_error($e->getMessage());
  }

  echo $report;
  //trigger_error('<b>Balance : </b>' . $bal);
} else {
  trigger_error('<b>You are not authorised to send SMS</b>');
}
///--------------------------------------------------
///--------------- General functions ----------------
///--------------------------------------------------

function getConfigSettings($smsType)
{
  $rtn=array();
  try {
    $db = new connectDatabase();
    if ($db->isLastQuerySuccessful()) {
      $con = $db->connect();

      $sql = "SELECT ipa_unique_code,ipa_name,ipa_url,ipa_entry,ipa_code FROM sh_ipa WHERE ipa_unique_code=:code";
      $stmt = $con->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
      $stmt->bindparam(":code", $smsType, PDO::PARAM_STR);
      $stmt->execute();
      $stmt->setFetchMode(PDO::FETCH_ASSOC);

      foreach ($stmt->fetchAll() as $row) {
        $rtn['url']=$row['ipa_url'];
        $rtn['uname']=$row['ipa_entry'];
        $rtn['apikey']=$row['ipa_code'];
      }
    } else {
      trigger_error($db->connectionError());
    }
    $db->closeConnection();
  }  catch (Exception $e) {
    trigger_error($db->connectionError());
  }
  return $rtn;
}

function getSMSMessage()
{
  $rtn = '';
  try {
    $db = new connectDatabase();
    if ($db->isLastQuerySuccessful()) {
      $con = $db->connect();

      /// get number of SMS messages set to be ready to be sent
      $sql = "SELECT COUNT(*) FROM message_template WHERE msgCategory = 'send'";
      $count = $con->query($sql)->fetchColumn();

      if ($count > 1 || $count == 0){
        trigger_error('There is either more than one message ready to be sent or no message to send!',E_USER_ERROR);
      } else {
        // now get the SMS message to send
        $sql = "SELECT msgID,msgBody,msgCategory,msgSpecialDate FROM message_template WHERE msgCategory = 'send'";
        $stmt = $con->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        foreach ($stmt->fetchAll() as $row) {
          $rtn=$row['msgBody'];  
        }
      }      
    } else {
      trigger_error($db->connectionError());
    }
    $db->closeConnection();
  } catch (Exception $e) {
    trigger_error($e->getMessage());
  }
  return $rtn;
}

function getRecipients()
{
  $rtn = array();$ctn=0;
  try {
    $db = new connectDatabase();
    if ($db->isLastQuerySuccessful()) {
      $con = $db->connect();

      $sql = "SELECT eleID,ele_Name,ele_Number FROM ele_details LIMIT 1";
      $stmt = $con->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
      // $stmt->bindparam(":lk", $nam, PDO::PARAM_STR);
      $stmt->execute();
      $stmt->setFetchMode(PDO::FETCH_ASSOC);

      foreach ($stmt->fetchAll() as $row) {
        $ctn++;
        $rtn[$ctn]['eleID']=$row['eleID'];
        $rtn[$ctn]['ele_Name']=$row['ele_Name']; 
        $rtn[$ctn]['ele_Number']=$row['ele_Number'];     
      }
    } else {
      trigger_error($db->connectionError());
    }
    $db->closeConnection();
  } catch (Exception $e) {
    trigger_error($e->getMessage());
  }
  return $rtn;
}

function saveSuccess($arr,$smsResult)
{
  $rtn='';
  try {
    $db = new connectDatabase();
    $dat=new DateTime();
    $dt=$dat->format('Y-m-d');
    if ($db->isLastQuerySuccessful()) {
      $con = $db->connect();
      $sql = "INSERT INTO ele_sms (ele_dID,ele_msg,ele_sent_date,ele_sent_status) VALUES (:msgID,:msgM,:msgSD,:msgSS)";

      $stmt = $con->prepare($sql);
      $stmt->bindparam(":msgID", $arr['eleID'], PDO::PARAM_INT);
      $stmt->bindparam(":msgM", $arr['message'], PDO::PARAM_STR);
      $stmt->bindparam(":msgSD", $dt, PDO::PARAM_STR);
      $stmt->bindparam(":msgSS", $smsResult, PDO::PARAM_STR);
      $row = $stmt->execute();

      if ($row) {
        $sql="UPDATE ele_details SET ele_Send_Count=ele_Send_Count+1 WHERE eleID = :id";
        $stmt = $con->prepare($sql);
        
        $stmt->bindparam(":id", $arr['eleID'], PDO::PARAM_INT);
        $rww = $stmt->execute();

        if ($rww) {
          $rtn = "Message Successfully sent to <b>(" . $arr['ele_Number'] .")</b>! ".$smsResult;
        }       
      }
    } else {
      trigger_error($db->connectionError());
    }
    $db->closeConnection();
  } catch (PDOException $e) {
    trigger_error($e->getMessage());
  }
  return $rtn;
}

function handleSMSError($arr,$smsResult)
{
  $rtn=$smsResult;  
  return $rtn;
}

function getTYurl()
{
  $rtn='';
  $nt=rand(10,100);
  ////https://bit.ly/3Mp40qK Twitter
  ///https://bit.ly/3CSlKrn Facebook
  ///https://bit.ly/3MWEE3E website

  if (($nt % 3) == 1) {
    $rtn = 'https://bit.ly/3MWEE3E';
  } elseif (($nt % 3) == 2) {
    $rtn = 'https://bit.ly/3Mp40qK';
  } else {
    $rtn = 'https://bit.ly/3CSlKrn';
  }
  return $rtn;
}

?>