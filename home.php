<?php
date_default_timezone_set("Africa/Lagos");
include('includes/header.php');
if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == 1) {

?>

<script>
  $(function() {
    $('.knob').knob({
      /*change : function (value) {
      //console.log("change : " + value);
      },
      release : function (value) {
      console.log("release : " + value);
      },
      cancel : function () {
      console.log("cancel : " + this.value);
      },*/
      draw: function() {

        // "tron" case
        if (this.$.data('skin') == 'tron') {

          var a = this.angle(this.cv) // Angle
            ,
            sa = this.startAngle // Previous start angle
            ,
            sat = this.startAngle // Start angle
            ,
            ea // Previous end angle
            ,
            eat = sat + a // End angle
            ,
            r = true

          this.g.lineWidth = this.lineWidth

          this.o.cursor &&
            (sat = eat - 0.3) &&
            (eat = eat + 0.3)

          if (this.o.displayPrevious) {
            ea = this.startAngle + this.angle(this.value)
            this.o.cursor &&
              (sa = ea - 0.3) &&
              (ea = ea + 0.3)
            this.g.beginPath()
            this.g.strokeStyle = this.previousColor
            this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false)
            this.g.stroke()
          }

          this.g.beginPath()
          this.g.strokeStyle = r ? this.o.fgColor : this.fgColor
          this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false)
          this.g.stroke()

          this.g.lineWidth = 2
          this.g.beginPath()
          this.g.strokeStyle = this.o.fgColor
          this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false)
          this.g.stroke()

          return false
        }
      }
    })
  });

  $(function() {
    $("#grids").DataTable({
      "paging": true,
      "lengthChange": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      //"buttons": ["excel", "pdf", "colvis"]
    }).buttons().container().appendTo('#guests_wrapper .col-md-6:eq(0)');;
  });

  $('#msgBody').keyup(function() {

    var characterCount = $(this).val().length,
      current = $('#current'),
      maximum = $('#maximum'),
      theCount = $('#count');

    current.text(characterCount);

    /*This isn't entirely necessary, just playin around*/
    if (characterCount < 70) {
      current.css('color', '#666');
    }
    if (characterCount > 70 && characterCount < 90) {
      current.css('color', '#6d5555');
    }
    if (characterCount > 90 && characterCount < 100) {
      current.css('color', '#793535');
    }
    if (characterCount > 100 && characterCount < 120) {
      current.css('color', '#841c1c');
    }

    if (characterCount >= 120) {
      maximum.css('color', '#ff0001');
      current.css('color', '#ff0001');
      theCount.css('font-weight', 'bold');
    } else {
      maximum.css('color', '#666');
      theCount.css('font-weight', 'normal');
    }
  });
</script>

  <style>
    #grids.tbody th,
    #grids tbody td {
      height: 5px;
    }
  </style>


  <body class="hold-transition layout-top-nav">
    <div id="app">
      <div class="wrapper">
        <?php include('includes/top_menu.php'); ?>
        <div class="content-wrapper">
          <div class="content">
            <div class="container">
              <div class="content-header">
                <div class="container">
                  <div class="row mb-2">
                    <div class="col-sm-6">
                      <h1 class="m-0">OSHO for Senate FCT</h1>
                      <!-- <h3 class="card-title" style="color:cadetblue;"><?php //echo userDetails(); ?></h3> -->
                    </div>
                    <div class="col-sm-6">
                      <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item acive"> <?php echo namePage(); ?> </li>
                      </ol>
                    </div>
                  </div>
                </div>
              </div>

              <?php if (isset($_REQUEST['p'])) {
                grantAccess();
              } else { ?>
                <div class="content">
                  <div class="container-fluid">
                    <div class="row">
                      <div class="col-md-4 col-sm-6 col-12">
                        <div class="small-box bg-info">
                          <div class="inner">
                            <h3><?php echo getWeekCount(); ?></h3>
                            <p>No. of Contacts reached (This Week)</p>
                          </div>
                          <div class="icon"><i class="fas fa-user-secret"></i></div>
                        </div>
                      </div>
                      <div class="col-md-4 col-sm-6 col-12">
                        <div class="small-box bg-secondary">
                          <div class="inner">
                            <h3><?php echo getMonthCount(); ?></h3>
                            <p>No. of Contacts reached (This Month)</p>
                          </div>
                          <div class="icon"><i class="fas fa-user-shield"></i></div>
                        </div>
                      </div>
                      <div class="col-md-4 col-sm-6 col-12">
                        <div class="small-box bg-success">
                          <div class="inner">
                            <h3><?php echo getQuarterCount(); ?></h3>
                            <p>Total No. of Contacts reached (Quarter)</p>
                          </div>
                          <div class="icon"><i class="fas fa-user-tie"></i></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>


      <footer class="main-footer">
        <div class="float-right d-none d-sm-inline">
          Powered by <strong>Osho for FCT Senate</strong> | Social Media Team
        </div>
        Copyright &copy <span id="copy"><?php echo date('Y'); ?></span>
      </footer>
    </div>

  <?php } else {
  die('<head><script LANGUAGE="JavaScript">window.location="index";</script></head>');
} ?>

  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/adminlte.min.js"></script>
  <script src="assets/js/jquery.knob.min.js"></script>

  <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
  <script src="assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
  <script src="assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
  <script src="assets/plugins/jszip/jszip.min.js"></script>
  <script src="assets/plugins/pdfmake/pdfmake.min.js"></script>
  <script src="assets/plugins/pdfmake/vfs_fonts.js"></script>
  <script src="assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
  <script src="assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
  <script src="assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
  <script src="assets/plugins/flot/jquery.flot.js"></script>
  <script src="assets/plugins/flot/plugins/jquery.flot.resize.js"></script>
  <script src="assets/plugins/flot/plugins/jquery.flot.pie.js"></script>

  <script>
    $(function() {
      $('.knob').knob({
        /*change : function (value) {
        //console.log("change : " + value);
        },
        release : function (value) {
        console.log("release : " + value);
        },
        cancel : function () {
        console.log("cancel : " + this.value);
        },*/
        draw: function() {

          // "tron" case
          if (this.$.data('skin') == 'tron') {

            var a = this.angle(this.cv) // Angle
              ,
              sa = this.startAngle // Previous start angle
              ,
              sat = this.startAngle // Start angle
              ,
              ea // Previous end angle
              ,
              eat = sat + a // End angle
              ,
              r = true

            this.g.lineWidth = this.lineWidth

            this.o.cursor &&
              (sat = eat - 0.3) &&
              (eat = eat + 0.3)

            if (this.o.displayPrevious) {
              ea = this.startAngle + this.angle(this.value)
              this.o.cursor &&
                (sa = ea - 0.3) &&
                (ea = ea + 0.3)
              this.g.beginPath()
              this.g.strokeStyle = this.previousColor
              this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false)
              this.g.stroke()
            }

            this.g.beginPath()
            this.g.strokeStyle = r ? this.o.fgColor : this.fgColor
            this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false)
            this.g.stroke()

            this.g.lineWidth = 2
            this.g.beginPath()
            this.g.strokeStyle = this.o.fgColor
            this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false)
            this.g.stroke()

            return false
          }
        }
      })
    });

    $("#upload").click(function() {
        document.getElementById('loadarea').src = 'progressbar.php';
    });
  </script>

  </body>

</html>


<?php

  function getBarChartData()
  {
    // $rtn='[[1,10], [2,8], [3,4], [4,13]]';
    // $rtnTitle='[[1,'January'], [2,'February'], [3,'March'], [4,'April']]';
    $rtn='[';$cnt=1;$rtnTitle='[';
    try {
      $db = new connectDatabase();
      if ($db->isLastQuerySuccessful()) {
        $con = $db->connect();

        $sql = "SELECT COUNT(*) FROM bar_chart";
        $res = $con->query($sql);
        $r_count = $res->fetchColumn();
  
        $sql = "SELECT chtTitle,chtValue FROM bar_chart ORDER BY chtTitle DESC";
        $stmt = $con->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        
        foreach ($stmt->fetchAll() as $row) 
        {
          $stmt->rowCount();
          if ($cnt>1 && $cnt<=$r_count){$rtn.=', ';$rtnTitle.=', ';}
          $val=$row['chtValue'];
          $tit=$row['chtTitle'];
          $rtn.='['.$cnt.','.$val.']';
          $rtnTitle.="[".$cnt.",'".date('j M Y',strtotime($tit))."']";
          $cnt++;
        }
        $rtn.=']';$rtnTitle.="]";
      } else {
        trigger_error($db->connectionError());
      }
      $db->closeConnection();
    } catch (Exception $e) {
      trigger_error($db->connectionError());
    }
    $_SESSION['titles']=$rtnTitle;
    return $rtn;
  }

  function namePage()
  {
    $rtn = '';
    if (isset($_REQUEST['p']) && $_REQUEST['p'] != '') {
      $rtn = ucwords($_REQUEST['p']);
    } else {
      $rtn = 'Dashboard';
    }
    return $rtn;
  }

  function getWeekCount()
  {
    $cnt = 0;
    // try {
    //   $db = new connectDatabase();
    //   if ($db->isLastQuerySuccessful()) {
    //     $con = $db->connect();
  
    //     $sql = "SELECT COUNT(guestID) as cnt FROM guests WHERE guestStatus = 'guest' AND guestVisitDate BETWEEN ".getDateRange('W');
    //     $stmt = $con->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        
    //     $stmt->execute();
    //     $stmt->setFetchMode(PDO::FETCH_ASSOC);
    //     $tmp=$stmt->fetch();
    //     $cnt=(int) $tmp['cnt'];

    //   } else {
    //     trigger_error($db->connectionError());
    //   }
    //   $db->closeConnection();
    // } catch (Exception $e) {
    //   trigger_error($db->connectionError());
    // }
    return $cnt;
  }

  function getMonthCount()
  {
    $cnt = 0;
    // try {
    //   $db = new connectDatabase();
    //   if ($db->isLastQuerySuccessful()) {
    //     $con = $db->connect();
  
    //     $sql = "SELECT COUNT(guestID) as cnt FROM guests WHERE guestStatus = 'guest' AND guestVisitDate BETWEEN ".getDateRange('M');
    //     $stmt = $con->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        
    //     $stmt->execute();
    //     $stmt->setFetchMode(PDO::FETCH_ASSOC);
    //     $tmp=$stmt->fetch();
    //     $cnt=(int) $tmp['cnt'];

    //   } else {
    //     trigger_error($db->connectionError());
    //   }
    //   $db->closeConnection();
    // } catch (Exception $e) {
    //   trigger_error($db->connectionError());
    // }
    return $cnt;
  }

  function getQuarterCount()
  {
    $cnt = 0;
    // try {
    //   $db = new connectDatabase();
    //   if ($db->isLastQuerySuccessful()) {
    //     $con = $db->connect();
  
    //     $sql = "SELECT COUNT(guestID) as cnt FROM guests WHERE guestStatus = 'guest' AND guestVisitDate BETWEEN ".getDateRange('Q');
    //     $stmt = $con->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        
    //     $stmt->execute();
    //     $stmt->setFetchMode(PDO::FETCH_ASSOC);
    //     $tmp=$stmt->fetch();
    //     $cnt=(int) $tmp['cnt'];

    //   } else {
    //     trigger_error($db->connectionError());
    //   }
    //   $db->closeConnection();
    // } catch (Exception $e) {
    //   trigger_error($db->connectionError());
    // }
    return $cnt;
  }

  function getTotalNumTPM()
  {
    return 20;
  }

  function userDetails()
  {
    $rtn = '';
    if (isset($_SESSION['fullname'])) {
      $rtn = $_SESSION['fullname'] . " (" . $_SESSION['role'] . ")";
    } else {
      $rtn = 'No User Details';
    }

    return $rtn;
  }

  function grantAccess()
  {
    $errormsg = ''; ///, $usersOnline, $staffOnline, $allowed
    global $pagePriviledge;

    if (isset($_GET["p"])) {
      $pageRequested = $_GET["p"];
      //die('<br><br><br><br>');var_dump($_SESSION);
      include($pageRequested . '.php');

      // if (isset($_SESSION[$pageRequested])) {
      //     $pagePriviledge = $_SESSION[$pageRequested];

      //     // $fname = @file_get_contents($pagePriviledge . '/' . $pageRequested . '.php');
      //     $fname=@file_get_contents($pageRequested . '.php');         

      //     if ($fname === FALSE) {
      //         $errormsg = 'PLEASE CONTACT YOUR ADMINISTRATOR TO ACCESS THIS PAGE!<br>...ACCESS DENIED...';
      //     } else {
      //         //die('<HEAD><SCRIPT lang="javascript">window.location="'.$pagePriviledge . '/' . $pageRequested .'.php";</SCRIPT></HEAD>');
      //         // include($pagePriviledge . '/' . $pageRequested .'.php');
      //         include($pageRequested .'.php');
      //     }
      // } else {
      //     //$tmp=print_r(array_values($_SESSION));
      //     $errormsg = 'YOU ARE NOT AUTHORISED TO VIEW THIS PAGE!<br>...ACCESS DENIED...';
      // }
    }
    if ($errormsg != '') {
      echo '<center><br><span style="color:red; font-size:20pt;">' . $errormsg . '</span></center>';
    }
  }

  function getDateRange($intv)
  {
    $rg='';
    $rge=new DateTime();
    if ($intv == 'W'){
      $rgs=new DateTime();      
      $rgs->modify('-6 days');
      $rg=" '".$rgs->format('Y-m-d')."' AND '".$rge->format('Y-m-d')."' ";
    } else if ($intv=='M'){
      $rg=" '".$rge->format('Y-m')."-01' AND '".$rge->format('Y-m-d')."' ";
    } else if ($intv=='Q'){
       $m=$rge->format('m');
       switch($m)
       {
        case 1: case 2: case 3:
          $rg=" '".$rge->format('Y')."-01-01' AND '".$rge->format('Y')."-03-31' ";
          break;
        case 4: case 5: case 6:
          $rg=" '".$rge->format('Y')."-04-01' AND '".$rge->format('Y')."-06-30' ";
          break;
        case 7: case 8: case 9:
          $rg=" '".$rge->format('Y')."-07-01' AND '".$rge->format('Y')."-09-30' ";
          break;
        case 10: case 11: case 12:
          $rg=" '".$rge->format('Y')."-10-01' AND '".$rge->format('Y')."-12-31' ";
          break;
       }
    }    
    return $rg;
  }

?>