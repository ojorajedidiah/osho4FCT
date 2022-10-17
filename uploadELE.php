<?php

set_time_limit(0);



if (isset($_POST["upload"])) {
  $msg = '';
  $prg = 0;
  $cnt = 0;
  try {
    $db = new connectDatabase();
    $filename = $_FILES["file"]["tmp_name"];
    if ($_FILES["file"]["size"] > 0) {
      $tot = count(file($filename, FILE_SKIP_EMPTY_LINES));
      $file = fopen($filename, "r");

      while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE) {
        //var_dump($emapData);
        foreach ($emapData as $ky => $phn) {
          echo($phn.'<br>');
          $cCrt = canCreate($phn, $db);
          if (!$cCrt) {
            $msg .= 'Duplicate Phonenumber (<b>' . $phn . '</b>)!<br>';
          } else {
            if ($db->isLastQuerySuccessful()) {
              $prg++;
              $cnt++;
              $percent = intval($prg / $tot * 100) . "%";              
              echo '<script>parent.document.getElementById("progressbar").innerHTML="<div style=\"width:' . $percent
                . ';background:linear-gradient(to bottom, rgb(6, 80, 6) 0%,rgb(6, 80, 6) 100%);\">&nbsp;</div>";parent.document.getElementById("information").innerHTML="<div style=\"text-align:center; font-weight:bold\">'
                . $percent . ' is processed.</div>";</script>';

              ob_flush();
              // flush();

              $con = $db->connect();
              $sql = "INSERT INTO ele_details_temp (ele_Number) VALUES (:eleM)";
              
              $stmt = $con->prepare($sql);              
              $stmt->bindparam(":eleM", $phn, PDO::PARAM_STR);
              $row = $stmt->execute();

              if ($row) {
                $msg .= "The Contact <b>(" . $phn . ")</b> has been created!<br>";
              }
            }
          }
        }
      }
    } else {
      trigger_error('Invalid File:Please Upload CSV File', E_USER_NOTICE);
    }
    $db->closeConnection();
  } catch (Exception $e) {
    trigger_error($db->connectionError());
  }
}

function getFirstName($str)
{
  $rtn='';
  $rtn=explode(' ',trim($str));
  return ucwords($rtn[0]);
}

function canCreate($ky, $obj)
{
  $rtn = true;
  try {
    if ($obj->isLastQuerySuccessful()) {
      $con = $obj->connect();
      $sql = "SELECT * FROM ele_details_temp WHERE ele_Number LIKE :eleM";

      $stmt = $con->prepare($sql);
      $stmt->bindparam(":eleM", $ky, PDO::PARAM_STR);
      $stmt->execute();
      $stmt->setFetchMode(PDO::FETCH_ASSOC);
      $row = $stmt->fetch();
      if ($row) {
        $rtn = false;
      }
    } else {
      $rtn = false;
    }
  } catch (Exception $e) {
    trigger_error($obj->connectionError());
  }
  
  return $rtn;
}

?>



<div class="content" style="align-items:center;">
  <div class="container-fluid" style="width:60%;">
    <div class="form-group-upload col-sm-12">
      <form enctype="multipart/form-data" method="post" role="form">
        <span style="font: size 11px;" width="450px">Choose the list of recipients then click 'Upload' to upload
          <br /><br />Ensure CSV is in this format below. <br /><b>Column A</b>-Name <b>Column B</b>-phonenumber <b>Column C</b>-Ward</span><br />

        <span for="file">Select CSV File for Recipient List <input type="file" name="file" id="file" size="150">
          <button class="btn btn-success" type="submit" id="upload" name="upload">Upload</button><br><br></span>
      </form>
      <div id="progressbar" style="border:1px solid #ccc; border-radius: 5px; text-align:left; height:35px;"></div>
      <div id="information" styel="text-align:center; font-weight:bold;"><?php if (isset($msg) && strlen($msg)>1){ echo $msg; }?></div>
      <iframe id="loadarea" style="display:none;"></iframe><br />
    </div>
  </div>
</div>