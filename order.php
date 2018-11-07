<?php
include 'top.php';
$foods = '';

$query1 = 'SELECT pmkFdId, fldProduct, fldPrice FROM tblMenu';

if ($thisDatabaseReader->querySecurityOk($query1, 0)) {
    $query1 = $thisDatabaseReader->sanitizeQuery($query1);
    $foods = $thisDatabaseReader->select($query1, '');

}

$emailOI='';


$update = false;

// SECTION: 1a.
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1b Security
//
// define security variable to be used in SECTION 2a.

$yourURL = DOMAIN . PHP_SELF;
$pmkOI = -1;
$email = '';
$name = '';
$pickuptime = '';
$instruction = '';

$tip = 0;



$activityERROR = false;
if (isset($_GET["id"])) {
    $pmkOI = (int) htmlentities($_GET["id"], ENT_QUOTES, "UTF-8");

    $query = 'SELECT fldEmail, fldName, fldTime, fldInstruction, fldTips ';
    $query .= 'FROM tblOrder WHERE pmkOI = ?';

    $dataRecord = array($pmkOI);

    if ($thisDatabaseReader->querySecurityOk($query, 1)) {
        $query = $thisDatabaseReader->sanitizeQuery($query);
        $order = $thisDatabaseReader->select($query, $dataRecord);
    }

    $email = $order[0]["fldEmail"];
    $name = $order[0]["fldName"];
    $pickuptime = $order[0]["fldTime"];
    $instruction = $order[0]["fldInstruction"];

    $tip = $order[0]["fldTips"];
}

$emailERROR = false;
$nameERROR = false;
$pickuptimeERROR = false;
$instructionERROR = false;
$tipERROR = false;




$mailed = false;



$errorMsg = array();

// array used to hold form values that will be written to a CSV file
$dataRecord = array();



$dataEntered = false;
$output = array();
if (isset($_POST["btnSubmit"])) {

    //print '<pre>Form has been submitted</pre>';
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2a Security
    //
    if (!securityCheck($yourURL)) {
        $msg = "<p>Sorry you cannot access this page. ";
        $msg.= "Security breach detected and reported.</p>";
        die($msg);
    }
     $pmkOI = (int) htmlentities($_POST["hidOI"], ENT_QUOTES, "UTF-8");
    if ($pmkOI > 0) {
        $update = true;
    }




        $email = filter_var($_POST["txtemail"], FILTER_SANITIZE_EMAIL);
        $dataRecord[] = $email;



        $name = htmlentities($_POST["txtname"], ENT_QUOTES, "UTF-8");
        $dataRecord[] = $name;


        $pickuptime = htmlentities($_POST["lstpickuptime"], ENT_QUOTES, "UTF-8");
        $dataRecord[] = $pickuptime;


        $instruction = htmlentities($_POST["txtinstruction"], ENT_QUOTES, "UTF-8");
        $dataRecord[] = $instruction;

        $varfood="";

        $varprice="";


///*
if(!empty($_POST["checkarray"])){
if (is_array($foods)) {
  foreach ($foods as $food) {
foreach($_POST['checkarray'] as $interest) {
  if($interest==$food['pmkFdId']){
    $varfood .= $food['fldProduct']. ',';
    $varprice=$varprice+$food['fldPrice'];
  }
}
}
}
}


        $dataRecord[]=$varfood;

        $tip = htmlentities($_POST["radtip"], ENT_QUOTES, "UTF-8");
        $dataRecord[] = $tip;

        $varprice = $varprice + $tip;

        $dataRecord[]=$varprice;








    //2c validation
    if ($name == "") {
        $errorMsg[] = "Please enter the name <br>";
        $nameERROR = true;
    } elseif (!verifyAlphaNum($name)) {
        $errorMsg[] = "Enter the name in a correct format <br>";
        $nameERROR = true;
    }

    if ($pickuptime == "Closed") {
        $errorMsg[] = "We are closed right now <br>";
        $pickuptimeERROR = true;
    }




    if ($email == "") {
        $errorMsg[] = "Please enter your email address";
        $emailERROR = true;
    } elseif (!verifyEmail($email)) {
        $errorMsg[] = "Your email address appears to be incorrect.";
        $emailERROR = true;
    }

   if(empty($_POST["checkarray"])){
    $errorMsg[] = "Please select a food item to order";
    }
//*/

    if (!$errorMsg) {
        if (DEBUG) {
            print "<p>Form is valid</p>";
        }
        // SECTION: 2e Save Data
//

        $dataEntered = false;

        try {
            $thisDatabaseWriter->db->beginTransaction();

            if ($update) {
                $query = 'UPDATE tblOrder SET ';
            } else {
                $query = 'INSERT INTO tblOrder SET ';
            }

            $query = 'INSERT INTO tblOrder SET ';

            $query .= 'fldEmail = ?, ';
            $query .= 'fldName = ?, ';
            $query .= 'fldTime = ?, ';
            $query .= 'fldInstruction = ?, ';
            $query .= 'fnkFoods = ?, ';
            $query .= 'fldTips = ?, ';
            $query .= 'fnkPrice = ? ';

            if ($update) {
                $query .= 'WHERE pmkOI = ?';
                $dataRecord[] = $pmkOI;


                if ($thisDatabaseReader->querySecurityOk($query, 1)) {
                    $query = $thisDatabaseWriter->sanitizeQuery($query);
                    $results = $thisDatabaseWriter->update($query, $dataRecord);
                }
            } else {
                if (DEBUG) {
                    $thisDatabaseWriter->TestSecurityQuery($query, 0);
                    print_r($dataRecord);
                }

                if ($thisDatabaseWriter->querySecurityOk($query, 0)) {
                    $query = $thisDatabaseWriter->sanitizeQuery($query);
                    $results = $thisDatabaseWriter->insert($query, $dataRecord);
                    $primaryKey = $thisDatabaseWriter->lastInsert();
                }

                if (DEBUG) {
                    print "<p>pmk= " . $primaryKey;
                }
            }
            $dataEntered = $thisDatabaseWriter->db->commit();


            if (DEBUG)
                print "<p>transaction completed ";
        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();
            if (DEBUG)
                print "Error!: " . $e->getMessage() . "</br>";
            $errorMsg[] = "There was a problem with accepting your data please contact us directly.";
        }

        //*
        $query2 = 'SELECT pmkOI FROM tblOrder ORDER BY pmkOI DESC LIMIT 1';
        $emailOID="";
        if ($thisDatabaseReader->querySecurityOk($query2, 0,1)) {
            $query2 = $thisDatabaseReader->sanitizeQuery($query2);
            $emailOID = $thisDatabaseReader->select($query2, '');

        }
        if (is_array($emailOID)) {
            foreach ($emailOID as $x) {
              $emailOI = $x["pmkOI"];
            }
          }

        $datastatus=array();

        $fdstatus="Order Placed";
        $datastatus[]=$fdstatus;
        $datastatus[]=$emailOI;

        $querystatus = 'INSERT INTO tblStatus ';

        $querystatus .= 'SET fldStatus = ?, pfkOI = ? ';

        $results = $thisDatabaseWriter->insert($querystatus, $datastatus);

        $message = '<h2>Your Order</h2>';


        $message  .= "<p> Order ID : ".$emailOI."</p>";
        $message  .= "<p> Name : ".$name."</p>";
        $message  .= "<p> Total: ".$varprice."</p>";





        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        // SECTION: 2g Mail to user
        //
        // Process for mailing a message which contains the forms data
        // the message was built in section 2f.
        $to = $email; // the person who filled out the form
        $cc = "";
        $bcc = "";

        $from = "Catamount Food Service <customer.service@yoursite.com>";

        // subject of mail should make sense to your form
        $todaysDate = strftime("%x");
        $subject = "Order : " . $todaysDate;

        $mailed = sendMail($to, $cc, $bcc, $from, $subject, $message);
    }
}
?>
<div class="container " >
<?php
if ($dataEntered) { // closing of if marked with: end body submit
    print "<h1>Order Placed</h1> ";
    print "<h3>Your Order ID : ".$emailOI ."</h1> ";
    // Display the message you created in in SECTION: 2f
} else {
//####################################
//
// SECTION 3b Error Messages
//
// display any error messages before we print out the form
    if ($errorMsg) {
        print '<div id="errors">';
        print '<h1>Your form has the following mistakes</h1>';
        print "<ol>\n";
        foreach ($errorMsg as $err) {
            print "<li>" . $err . "</li>\n";
        }
        print "</ol>\n";
        print '</div>';
    }
}
?>
<?php
if (!$dataEntered){

 ?>
    <h2>Order</h2>
    <form action="<?php print PHP_SELF; ?>"
          method="post"
          id="frmRegister">

        <input type="hidden" id="hidOI" name="hidOI"
               value="<?php print $pmkOI; ?>"
               >
        <div class="form-group">
            <label for="txtemail" >Email</label>
                <input class="form-control" type="text" id="txtemail" name="txtemail"
                       value="<?php print $email; ?>"
                        placeholder="Enter the email"
    <?php if ($emailERROR) print 'class="form-control has-error"'; ?>
                       onfocus="this.select()"
                       autofocus>

          </div>
        <div class="form-group">


            <label for="txtname" >Name</label>
                <input class="form-control" type="text" id="txtname" name="txtname"
                       value="<?php print $name; ?>"
                        placeholder="Enter the name"
<?php if ($nameERROR) print 'class="form-control has-error "'; ?>
                       onfocus="this.select()"
                       autofocus>

        </div>



 <div class="form-group">

         <?php

      $timenow = time();
      $opentime = strtotime('10:00:00');
      $closetime = strtotime('20:50:00');

      print '<label for="lstpickuptime"';

      print '>Pick up time </label>';
      print '<select class="form-control" id="lstpickuptime"';
      print '        name="lstpickuptime"';
      print '            >';

      if($timenow > $closetime || $timenow <= $opentime){
      print '<option value="Closed" selected>CLOSED</option>';
      print "</select>";
      }
      else{

      $deliverytime = strtotime('+15 minutes', $timenow);

      $deliverytime = ceil($deliverytime / (15*60)) * (15*60);
      print '<option selected value="';
      print date('H:i:s',$timenow);
      print '">Now</option>';
      while($deliverytime <= $closetime && $deliverytime >= $opentime) {
         print '<option ';

      //   print " selected='selected' ";
         print 'value="'. date('H:i:s', $deliverytime) .'">' . date('H:i', $deliverytime) . '</option>'."\n";
         $deliverytime = strtotime('+15 minutes', $deliverytime);
      }
      print "</select>";
         }

      ?>
   </div>
<?php


?>



   <div class="form-group">

<?php
if (is_array($foods)) {
    foreach ($foods as $food) {
      print '<div class="form-group">';
      print '<label  for="'.$food['fldProduct'].'"> ';
      print '<input  type="checkbox" name="checkarray[]" id="checkarray[]" value="'.$food['pmkFdId'].'"> ';
      print $food['fldProduct']." - $".$food['fldPrice'].' </label></div>';
    }
  }




?>



   </div>


            <div class="form-group">
                <label for="txtinstruction" >Special Instruction</label>
                    <input class="form-control" rows="5" type="text" id="txtinstruction" name="txtinstruction"
                           value="<?php print $instruction; ?>"
                            placeholder="Enter your special instruction"
            <?php if ($instructionERROR) print 'class="form-control has-error"'; ?>
                           onfocus="this.select()"
                           autofocus>

            </div>

            <div class="form-group">


<div class=form-group>
                    <label>
                        <input  type="radio"
                               id="radtip"
                               name="radtip"
                               value="0"

                           <?php if ($tip == "0") echo ' checked="checked" '; ?>>
$0 tip</label>
</div>




<div class=form-group>

                    <label >
                        <input  type="radio"
                               id="radtip"
                               name="radtip"
                               value="1"

                               <?php if ($tip == "1") echo ' checked="checked" '; ?>>

$1 tip</label>
</div>


<div class=form-group>

                    <label >
                        <input  type="radio"
                               id="radtip"
                               name="radtip"
                               value="2"

            <?php if ($tip == "2") echo ' checked="checked" '; ?>>

$2 tip
</label></div>

          </div>

                <input type="submit" id="btnSubmit" name="btnSubmit" value="Place Order" class="btn btn-default">


    </form>


<?php
}
// end body submit
?>
</div>
<?php
include "footer.php";
if (DEBUG)
    print "<p>END OF PROCESSING</p>";
?>

</body>
</html>
