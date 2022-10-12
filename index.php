<?php
#   Author of the script
#   Name: Adeleke Ojora
#   Email : ojorajedidiah@gmail.com
#	  Modified by: Adeleke Ojora

session_start();
date_default_timezone_set("Africa/Lagos");
include('models/databaseConnection.class.php');
$ip = $_SERVER['REMOTE_ADDR'];


$msg = '';
//die();
if (isset($_REQUEST['submit']) && (isset($_REQUEST['vw']) && $_REQUEST['vw']=='psswd')) {
  if (!(empty($_SESSION['un'])) && !(empty($_REQUEST['password']))) {
    
    $db = new connectDatabase(); //    
    if ($db->isLastQuerySuccessful()) {
      $con = $db->connect();

      try {
        $dn = $_SESSION['un'];
        $pwd = md5($_REQUEST['password']);

        $sql = "SELECT shID,sh_userName,sh_fullName,canSendSMS,shStatus 
        FROM sh_sec WHERE sh_userName = '$dn' AND sh_password = '$pwd'";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $row=$stmt->fetch();

        if ($row) {
          if ($row['shStatus'] !== 'active') {
            $msg = 'Account deactivated, please contact your Admin';
            $_REQUEST['vw']='error';
          } else {
            $_SESSION['fullname'] = $row['sh_fullName'];
            $_SESSION['expiryTime'] = time() + (3 * 60); //set up session to expire within 1 min
            $_SESSION['username'] = $dn;
            $_SESSION['canSendSMS'] = $row['canSendSMS'];
            $_SESSION['loggedIn'] = 1;

            //// Perform insert for login action and insert into logs table
            $action = $_SESSION['fullname'] . ' Logged into OSHO for FCT Application';
            $data = [
              'logIP' => $ip,
              'logDate' => date('Y-m-d'),
              'logDescription' => $action,
            ];
            $sql = "INSERT INTO logs (logIP,logDate,logDescription) VALUES (:logIP, :logDate, :logDescription)";
            $stmt = $con->prepare($sql);
            $stmt->execute($data);
            $db->closeConnection();
            //die('i enter here');
            die('<head><script language="javascript">window.location="home.php";</script></head>');
          }
        } else {
          $msg='Wrong username and password combination!';
          $_REQUEST['vw']='error';
        }
      } catch (PDOException $er) {
        $msg = $er->getMessage() . '<br>Please contact OSHO for FCT Media Team!';
        $_REQUEST['vw']='error';
      }
    } else {
      $msg=$db->getErrorMessage();
      $_REQUEST['vw']='error';
    }
  }
} else { ///if (isset($_REQUEST['vw']) && $_REQUEST['vw']=='uname')
  $_SESSION['un']=(isset($_REQUEST['username']))?$_REQUEST['username']:'';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OSHO for Senate FCT | Log in</title>

 <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/adminlte.min.css">
</head>

<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <a class="navbar-brand" href=""><img src="assets/img/logo.gif" alt="Logo" width="105" height="105" /></a>
      <a href="">
        <h5><b>OSHO for Senate FCT | Log in</b></h5>
      </a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Sign in to start your session</p>
        <span class="badge badge-danger"><?php echo $msg; ?></span>

        <form action="" method="post">
          <?php if (!isset($_REQUEST['vw']) || strlen($msg)>1) { ?>
            <div class="input-group mb-3">
              <input type="hidden" name="vw" id="vw" value="uname">
              <input type="username" name="username" class="form-control" required placeholder="Enter username">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-user-check"></span>
                </div>
              </div>
            </div>
          <?php } else if (isset($_REQUEST['vw']) && $_REQUEST['vw'] =='uname') { ?>
            <div class="input-group mb-3">
              <input type="hidden" name="vw" id="vw" value="psswd">
              <input type="password" name="password" class="form-control" required placeholder="Password">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
          <?php } //else { var_dump($_REQUEST); }?>
          <div class="row">
            <div class="col-8">
              <!-- <div class="icheck-primary">
                <input type="checkbox" id="remember">
                <label for="remember">
                  Remember Me
                </label> 
              </div>-->
            </div>
            <?php if (!isset($_REQUEST['vw']) || strlen($msg)>1) { ?>
              <div class="col-4">
                <button type="submit" name="submit" class="btn btn-danger btn-block">Next</button>
              </div>
            <?php } elseif (isset($_REQUEST['vw']) && isset($_REQUEST['vw'])=='uname') { ?>
              <div class="col-4">
                <button type="submit" name="submit" class="btn btn-danger btn-block">Sign In</button>
              </div>
            <?php } elseif (strlen($msg) > 1) { ?>
              <div class="col-4">
                <button type="submit" name="submit" class="btn btn-danger btn-block">Try Again</button>
              </div>
            <?php } ?>
          </div>
        </form>

        <div class="lockscreen-footer text-center">
          <span style="font-size: 8pt;">Powered by <b>Osho for FCT Senate</b> | Social Media Team </span>
        </div>
      </div>
    </div>
  </div>


  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/adminlte.min.js"></script>
</body>

</html>