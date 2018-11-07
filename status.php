<?php
include 'top.php';
$yourURL = DOMAIN . PHP_SELF;

$status='';

$email = '';
$name = '';
$pickuptime = '';
$instruction = '';
$orderList='';
$tip = '';
$price='';

$statusERROR=false;
$errorMsg = array();

$records='';

$dataEntered = false;

$output = array();

if (isset($_POST["btnSubmit"])) {
    if (!securityCheck($yourURL)) {

        $msg = "<p>Sorry you cannot access this page. ";
        $msg.= "Security breach detected and reported.</p>";
        die($msg);
    }

    $status = htmlentities($_POST["txtstatus"], ENT_QUOTES, "UTF-8");
  /*
    if ($status == "") {

        $errorMsg[] = "Please enter the status <br>";
        $statusERROR = true;

    } elseif (!verifyNumeric($status)) {

        $errorMsg[] = "Enter the status in a correct format <br>";
        $statusERROR = true;
    }
    */
    $chkid='';
    $querychk='SELECT pmkOI from tblOrder';
    if ($thisDatabaseReader->querySecurityOk($querychk, 0)) {
        $querychk = $thisDatabaseReader->sanitizeQuery($querychk);
        $chkid = $thisDatabaseReader->select($querychk, '');


    }
    $true_status=false;
    if (is_array($chkid)) {

        foreach ($chkid as $id) {
          if($id["pmkOI"]==$status){
            $true_status=true;
          }
        }
    }
    if ($true_status==false){

      $errorMsg[] = "No such Order ID exist <br>";
    }



    if ((!$errorMsg) && ($true_status) ) {
        $dataEntered=true;
        if (DEBUG) {
            print "<p>Form is valid</p>";
        }
        // fldName fldTime fldInstruction fnkFoods fldTips fnkPrice
        $query = 'SELECT pmkOI, fldEmail, fldName, fldTime, fldInstruction, fnkFoods, fldTips, fnkPrice FROM tblOrder ';
        $query .= 'WHERE pmkOI = ? ';

        $arr=array($status);

        if ($thisDatabaseReader->querySecurityOk($query, 1)) {
            $query = $thisDatabaseReader->sanitizeQuery($query);
            $records = $thisDatabaseReader->select($query, $arr);


        }


      if (is_array($records)) {

          foreach ($records as $record) {
            print '<div class="container text-center" >';
            print "<h3> Order ID </h3>";
            print "<p>".$record["pmkOI"]."</p>";
            print "<h3> Email </h3>";
            print "<p>".$record["fldEmail"]."</p>";
            print "<h3> Name </h3>";
            print "<p>".$record["fldName"]."</p>";
            print "<h3> Time </h3>";
            print "<p>".$record["fldTime"]."</p>";
            print "<h3> Items Ordered  </h3>";
            print "<p>".$record["fnkFoods"]."</p>";
            print "<h3> Tip</h3>";
            print "<p>".$record["fldTips"]."</p>";
            print "<h3> Total Price   </h3>";
            print "<p>".$record["fnkPrice"]."</p>";

            print "<h3> Status   </h3>";
            print "<p>";
            $somearr=array();
            $somearr[]=$record["pmkOI"];
            $queryforthis = 'SELECT fldStatus FROM tblStatus WHERE pfkOI = ? ';

            if ($thisDatabaseReader->querySecurityOk($queryforthis, 1)) {
                $queryforthis = $thisDatabaseReader->sanitizeQuery($queryforthis);
                $recordds = $thisDatabaseReader->select($queryforthis, $somearr);


            }
            if (is_array($recordds)) {

                foreach ($recordds as $recordd) {
                  print $recordd["fldStatus"];
                }
              }


            print "</p>";

            print "</div>";
      }



    }
  }if ($errorMsg) {
      print '<div class="container">';

      foreach ($errorMsg as $err) {
          print "<h1>" . $err . "</h1>\n";
      }
      print '</div >';
  }
}



?>


<?php if(!$dataEntered) {?>

<div class="container " >
<?php
if(!$errorMsg){
print "<h1>Check Your Order Status Here</h1>";
}
?>

<form action="<?php print PHP_SELF; ?>" method="post" id="frmStatus">
  <div class="form-group">
    <label for="txtstatus" >Order No:</label>
    <input type="text" id="txtstatus" class="form-control" name="txtstatus">
  </div>
  <input type="submit" id="btnSubmit" name="btnSubmit" value="Check Status" class="btn btn-default">
</form>


<?php
}
print '</div>';
include 'footer.php';
?>
