<?php
//session_start();
$_SESSION['msgErr'] = '';
$errMsg = '';
if (isset($_REQUEST['saveRec'])) {
  // if (canSave()) {
    $errMsg = createNewMsg();
    $_REQUEST['v'] = "update";
  // } else {
  //   $errMsg = $_SESSION['msgErr'];
  //   $_REQUEST['v'] = "new";
  // }
}

if (isset($_POST['updateRec'])) {
  if (canSaveEdit()) {
    $errMsg = UpdateMsg();
    $_REQUEST['v'] = "update";
  } else {
    $errMsg = $_SESSION['msgErr'];
    $_REQUEST['v'] = "edit";
  }
}

if (isset($_POST['deleteRec'])) {
  // if (canSaveEdit()) {
  //   $errMsg = UpdateMsg();
  //   $_REQUEST['v'] = "update";
  // }else {
  //   $errMsg=$_SESSION['msgErr'];
  //   $_REQUEST['v'] = "edit";
  // }  
}
?>


<div class="content">
  <div class="container-fluid" style="width:70%;">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-8">
            <h5>Messages</h5>
            <?php if ($errMsg != '') {
              echo '<span style="color:red;font-size:15px;">' . $errMsg . '</span>';
            } ?>
          </div>
          <div class="col-sm-4">
            <?php if (isset($_REQUEST['v']) && ($_REQUEST['v'] == 'new' || $_REQUEST['v'] == 'edit' || $_REQUEST['v'] == 'disable')) { ?>
              <a href="home?p=messages" class="btn btn-danger float-right">Back</a>
            <?php } else { ?>
              <a href="home?p=messages&v=new" class="btn btn-secondary float-right">Create New Message</a>
            <?php } ?>
          </div>
        </div>
      </div>
      <?php if (isset($_REQUEST['v']) && $_REQUEST['v'] == 'new') { ?>
        <div class="row">
          <div class="card-body card-secondary">
            <div class="card-header">
              <h3 class="card-title">Create New Message</h3>
            </div>
            <form method="post" target="">
              <?php echo buildNewForm(); ?>
            </form>
          </div>
        </div>
      <?php } else if (isset($_REQUEST['v']) && $_REQUEST['v'] == 'disable') { ?>
        <div class="row">
          <div class="card-body card-secondary">
            <div class="card-header">
              <h3 class="card-title">Disable Message</h3>
            </div>
            <form method="post" target="">
              <?php echo buildDisableForm($_REQUEST['rid']) ?>
            </form>
          </div>
        </div>
      <?php } else if (isset($_REQUEST['v']) && $_REQUEST['v'] == 'edit') { ?>
        <div class="row">
          <div class="card-body card-secondary">
            <div class="card-header">
              <h3 class="card-title">Edit Message</h3>
            </div>
            <form method="post" target="">
              <?php echo buildEditForm($_REQUEST['rid']); ?>
            </form>
          </div>
        </div>
      <?php } else { ?>
        <div class="row">
          <div class="card-body">
            <table id="grids" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th width="130px">Message Type</th>
                  <th width="350px">Message</th>
                  <th width="50px">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php echo getAccountRecords(); ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
</div>

<?php
///--------------------------------------------------
///------------- Geenral DML functions --------------
///--------------------------------------------------

function createNewMsg()
{
  $rtn = '';
  try {
    $db = new connectDatabase();
    if ($db->isLastQuerySuccessful()) {
      $con = $db->connect();
      
      $sql = "INSERT INTO message_template (msgBody,msgCategory,msgSpecialDate) VALUES (:msgBd,:msgCat,:msgSD)";

      $stmt = $con->prepare($sql);
      $stmt->bindparam(":msgBd", $_REQUEST['msgBody'], PDO::PARAM_STR);
      $stmt->bindparam(":msgCat", $_REQUEST['msgCategory'], PDO::PARAM_STR);
      $stmt->bindparam(":msgSD", $_REQUEST['msgSpecialDate'], PDO::PARAM_STR);     
      
      $row = $stmt->execute();

      if ($row) {
        $rtn = "The Message Template <b>[" . substr($_REQUEST['msgCategory'],0,25) . "...]</b> has been created!";
        //trigger_error($msg, E_USER_NOTICE);
      }
    } else {
      trigger_error($db->connectionError());
    }
    $db->closeConnection();
  } catch (PDOException $e) {
    trigger_error($e->getMessage());
  }

  return ($rtn == '') ? 'No Message Data' : $rtn;
}

function UpdateMsg()
{
  $rtn = '';
  try {
    $db = new connectDatabase();
    if ($db->isLastQuerySuccessful()) {
      $con = $db->connect();
      $sql = "UPDATE message_template SET msgBody= :msgB, msgCategory=:msgC, msgSpecialDate=:msgSD WHERE msgID=:recID";

      $stmt = $con->prepare($sql);
      $stmt->bindparam(":recID", $_REQUEST['rid'], PDO::PARAM_INT);
      $stmt->bindparam(":msgB", $_REQUEST['msgBody'], PDO::PARAM_STR);
      $stmt->bindparam(":msgC", $_REQUEST['msgCategory'], PDO::PARAM_STR);
      $stmt->bindparam(":msgSD", $_REQUEST['msgSpecialDate'], PDO::PARAM_STR);
      $row = $stmt->execute();

      if ($row) {
        $rtn = "The SMS Message <b>[" . substr($_REQUEST['msgBody'],0,25). "...]</b> has been updated";
        //trigger_error($msg, E_USER_NOTICE);
      }
    } else {
      trigger_error($db->connectionError());
    }
    $db->closeConnection();
  } catch (PDOException $e) {
    trigger_error($e->getMessage());
  }

  return ($rtn == '') ? 'No Message Data' : $rtn;
}

function getAccountRecords()
{
  $rtn = '';
  try {
    $db = new connectDatabase();
    if ($db->isLastQuerySuccessful()) {
      $con = $db->connect();

      $sql = "SELECT msgID,msgCategory,msgBody FROM message_template ORDER BY msgID ASC";
      $stmt = $con->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
      $stmt->execute();
      $stmt->setFetchMode(PDO::FETCH_ASSOC);

      foreach ($stmt->fetchAll() as $row) {
        $r1 = $row['msgCategory'];
        $r2 = substr($row['msgBody'],0,40);
        $rID = $row['msgID'];
        
        $rtn .= '<tr><td>' . $r1 . '</td><td>' . $r2 . '...</td>'
          . '<td><span class="badge badge-complete"><a href="home?p=messages&v=disable&rid=' . $rID . '">'
          . '<i class="nav-icon fas fa-user-lock" title="Disable Message" style="color:red;"></i>'
          . '</a></span><span class="badge badge-edit"><a href="home?p=messages&v=edit&rid=' . $rID . '">'
          . '<i class="nav-icon fas fa-edit" title="Edit Message" style="color:blue;"></i></a></span></td></tr>';
      }
    } else {
      trigger_error($db->connectionError());
    }
    $db->closeConnection();
  } catch (Exception $e) {
    trigger_error($db->connectionError());
  }
  return ($rtn == '') ? '<tr><td colspan="4" style="color:red;text-align:center;"><b>No Message</b></td></tr>' : $rtn;
}

function getSpecificGuest($rec)
{
  $rtn = array();
  try {
    $db = new connectDatabase();
    if ($db->isLastQuerySuccessful()) {
      $con = $db->connect();

      $sql = "SELECT msgID,msgBody,msgCategory,msgSpecialDate FROM message_template WHERE msgID = :id";
      $stmt = $con->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
      $stmt->bindparam(":id", $rec, PDO::PARAM_INT);
      $stmt->execute();
      $stmt->setFetchMode(PDO::FETCH_ASSOC);

      foreach ($stmt->fetchAll() as $row) {
        $rtn = $row;
      }
    } else {
      trigger_error($db->connectionError());
    }
    $db->closeConnection();
  } catch (PDOException $e) {
    trigger_error($db->connectionError());
  }
  return $rtn;
}

///--------------------------------------------------
///-------------- Build Form functions --------------
///--------------------------------------------------
function buildEditForm($id)
{
  $rtn = '';
  $msg = array();
  $msg = getSpecificGuest($id);
  if (is_array($msg) && count($msg) >= 1) {
    $dat=new DateTime($msg['msgSpecialDate']);
    $rtn = '<div class="row"><div class="col-sm-6"><label for="msgCategory">Message Category</label><div class="form-group">';
    $rtn .= '<select class="form-control" id="msgCategory" name="msgCategory" required>';
    $rtn.=($msg['msgCategory'] == "send")? '<option value="send" selected>Ready to Send</option>': '<option value="send">Ready to Send</option>';
    $rtn.=($msg['msgCategory'] == "do not send")? '<option value="do not send" selected>Not Ready to be Sent</option>': '<option value="do not send">Not Ready to be Sent</option>';
    $rtn .= '</select></div>';

    $rtn .= '<div class="form-group"><div class="form-group"><label for="msgScheduleDate">Scheduled SMS Date</label>';
    $rtn .= '<input type="date" class="form-control" name="msgScheduleDate" id="msgScheduleDate" value="'.$dat->format('D d F, Y').'"></div></div></div>';

    $rtn .= '<div class="col-sm-6"><div class="form-group"><label for="msgBody">SMS Template</label>';
    $rtn .= '<textarea class="form-control" rows="6" name="msgBody" id="msgBody" spellcheck="true" required>'.$msg['msgBody'].'</textarea></div>';

    $rtn .= '<div class="form-group"><div id="count" class="float-left"><span id="current">0</span><span id="maximum">/120</span></div>';
    $rtn .= '<button type="submit" id="updateRec" name="updateRec" class="btn btn-success float-right">Update Message</button></div></div></div>';
  }

  // die('the value is '.$rtn);
  $_SESSION['oldRec'] = $msg;
  return $rtn;
}

function buildNewForm()
{
  $rtn = '<div class="row"><div class="col-sm-6"><label for="msgCategory">Message Category</label><div class="form-group">';
  $rtn .= '<select class="form-control" id="msgCategory" name="msgCategory" required>';
  $rtn .='<option value="send" selected>Ready to Send</option><option value="do not send">Not Ready to be Sent</option></select></div>';

  $rtn .= '<div class="form-group"><div class="form-group"><label for="msgScheduleDate">Scheduled SMS Date</label>';
  $rtn .= '<input type="date" class="form-control" name="msgScheduleDate" id="msgScheduleDate"></div></div></div>';

  $rtn .= '<div class="col-sm-6"><div class="form-group"><label for="msgBody">SMS Template</label>';
  $rtn .= '<textarea class="form-control" rows="6" name="msgBody" id="msgBody" spellcheck="true" required>This is the Osho4FCT message template ...</textarea></div>';

  $rtn .= '<div class="form-group"><div id="count" class="float-left"><span id="current">0</span><span id="maximum">/120</span></div>';
  $rtn .= '<button type="submit" id="saveRec" name="saveRec" class="btn btn-success float-right">Create Message</button></div></div></div>';

  return $rtn;
}

function buildDisableForm($id)
{
  $rtn = '';
  $gst = array();
  $msg = getSpecificGuest($id);
  // die('the value is '.$gst['guestVisitDate']);
  if (is_array($msg) && count($msg) >= 1) {
    $rtn = '<div class="row"><div class="col-sm-6"><label for="msgCategory">Message Category</label><div class="form-group">';
    $rtn .= '<select class="form-control" id="msgCategory" name="msgCategory" readonly>';
    $rtn.=($msg['msgCategory'] == "send")? '<option value="send" selected>Ready to Send</option>': '<option value="send">Ready to Send</option>';
    $rtn.=($msg['msgCategory'] == "do not send")? '<option value="do not send" selected>Not Ready to be Sent</option>': '<option value="do not send">Not Ready to be Sent</option>';
    $rtn .= '</select></div>';

    $rtn .= '<div class="form-group"><div class="form-group"><label for="msgSpecialDate">Scheduled SMS Date</label>';
    $rtn .= '<input type="date" readonly class="form-control" name="msgSpecialDate" id="msgSpecialDate" value="'.$msg['msgSpecialDate'].'"></div></div></div>';

    $rtn .= '<div class="col-sm-6"><div class="form-group"><label for="msgBody">SMS Template</label>';
    $rtn .= '<textarea class="form-control" rows="6" name="msgBody" id="msgBody" spellcheck="true" readonly>'.$msg['msgBody'].'</textarea></div>';

    $rtn .= '<div class="form-group"><div id="count" class="float-left"><span id="current">0</span><span id="maximum">/120</span></div>';
    $rtn .= '<button type="submit" id="disableRec" name="disableRec" class="btn btn-success float-right">Disable Message</button></div></div></div>';
    
  }

  // die('the value is '.$rtn);
  $_SESSION['oldRec'] = $gst;
  return $rtn;
}


///--------------------------------------------------
///---------- Data Verification functions -----------
///--------------------------------------------------
function canSave()
{
  $rtn = true;
  try {
    $msgC = $_REQUEST['msgCategory'];
    $db = new connectDatabase();
    if ($db->isLastQuerySuccessful()) {
      $con = $db->connect();
      $sql = "SELECT * FROM message_template WHERE msgCategory = '$msgC'";
      $stmt = $con->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
      $stmt->execute();
      $stmt->setFetchMode(PDO::FETCH_ASSOC);

      foreach ($stmt->fetchAll() as $row) {
        $rtn = false;
        trigger_error("This Message already exist in the Database!", E_USER_NOTICE);
      }
    } else {
      trigger_error($db->connectionError());
    }
    $db->closeConnection();
  } catch (PDOException $e) {
    trigger_error($db->connectionError());
  }

  return $rtn;
}

function canSaveEdit()
{
  $rtn = false;
  $cse = array();
  $oldRec = $_SESSION['oldRec'];

  $cse['msgBody'] = $_REQUEST['msgBody'];
  $cse['msgScheduleDate'] = $_REQUEST['msgScheduleDate'];
  $cse['msgCategory'] = $_REQUEST['msgCategory'];

  if (count(array_diff($oldRec, $cse)) >= 1) {
    $rtn = true;
  } else {
    $_SESSION['msgErr'] = 'No new data to update!';
    $rtn = false;
  }
  return $rtn;
}

///--------------------------------------------------
///------------ general-purpose functions -----------
///--------------------------------------------------

function getToday()
{
  $dt = new DateTime('now');
  return $dt->format('Y-m-d');
}

?>