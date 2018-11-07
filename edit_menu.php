<?php
include 'top.php';

if(in_array($username, $user)){
$update = false;

// SECTION: 1a.
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1b Security
//
// define security variable to be used in SECTION 2a.
$pmkFdId = -1;
$yourURL = DOMAIN . PHP_SELF;

$product="";
$price="";


if (isset($_GET["id"])) {
  $pmkFdId = (int) htmlentities($_GET["id"], ENT_QUOTES, "UTF-8");

  $query = 'SELECT fldProduct, fldPrice ';
  $query .= 'FROM tblMenu WHERE pmkFdId = ?';

  $data = array($pmkFdId);

  if ($thisDatabaseReader->querySecurityOk($query, 1)) {
    $query = $thisDatabaseReader->sanitizeQuery($query);
    $poet = $thisDatabaseReader->select($query, $data);
  }

  $product = $poet[0]["fldProduct"];
  $price = $poet[0]["fldPrice"];

}

$productERROR="";
$priceERROR="";

$errorMsg = array();
$data = array();
$dataEntered = false;

if (isset($_POST["btnD"])) {

  if (!securityCheck($yourURL)) {
         $msg = "<p>Sorry you cannot access this page. ";
         $msg.= "Security breach detected and reported</p>";
         die($msg);
     }

  $pmkFdId = (int) htmlentities($_POST["hidFdId"], ENT_QUOTES, "UTF-8");
   $product = htmlentities($_POST["txtProduct"], ENT_QUOTES, "UTF-8");
   $price = htmlentities($_POST["txtPrice"], ENT_QUOTES, "UTF-8");

   $dataDeleteted = false;

   try {
        $thisDatabaseWriter->db->beginTransaction();

        $query = "DELETE ";
        $query .= "FROM tblMenu ";
        $query .= "WHERE pmkFdId = ?";

        $data[] = $pmkFdId;
        if ($thisDatabaseReader->querySecurityOk($query, 1)) {
            $query = $thisDatabaseWriter->sanitizeQuery($query);
            $results = $thisDatabaseWriter->delete($query, $data);
        }
        $dataDeleteted = $thisDatabaseWriter->db->commit();
        if (DEBUG)
            print "<p>transaction complete ";
    } catch (PDOExecption $e) {
        $thisDatabaseWriter->db->rollback();
        if (DEBUG)
            print "Error!: " . $e->getMessage() . "</br>";
        $errorMsg[] = "There was a problem with accepting your data please contact us directly.";
    }

    if ($dataDeleteted) {
       $dataEntered=true;
        
    } else {
        print '<div class="container"><h1>Food Item was not Deleted. Sorry!</h1></div>';
    }








}
if (isset($_POST["btnSubmit"])) {

  if (!securityCheck($yourURL)) {
    $msg = "<p>Sorry you cannot access this page. ";
    $msg.= "Security breach detected and reported</p>";
    die($msg);
  }

  $pmkFdId = (int) htmlentities($_POST["hidFdId"], ENT_QUOTES, "UTF-8");
  if ($pmkFdId > 0) {
    $update = true;
  }
  $product = htmlentities($_POST["txtProduct"], ENT_QUOTES, "UTF-8");
  $data[] = $product;

  $price = htmlentities($_POST["txtPrice"], ENT_QUOTES, "UTF-8");
  $data[] = $price;


  if ($product == "") {

    $errorMsg[] = "Please enter food";
    $productERROR = true;
  } elseif (!verifyAlphaNum($product)) {

    $errorMsg[] = "Your food appears to have extra character.";
    $productERROR = true;

  }

  if ($price == "") {
    $errorMsg[] = "Please enter Price";
    $priceERROR = true;
  } elseif (!verifyNumeric($price)) {
    $errorMsg[] = "Your Price appears to have extra character.";
    $priceERROR = true;
  }

  if (!$errorMsg) {
    if (DEBUG) {
      print "<p>Changes Recorded </p>";
    }
    $dataEntered = false;
    try {
      $thisDatabaseWriter->db->beginTransaction();
      if($update){
        $query = 'UPDATE tblMenu SET ';
      }else {
        $query = 'INSERT INTO tblMenu SET ';
      }
      // *****  this part is the same as an insert

      $query .= 'fldProduct = ?, ';
      $query .= 'fldPrice = ? ';
      // *****

      // adding a where clause so we need to add the id to the array
      if($update){
        $query .= 'WHERE pmkFdId = ?';
        $data[] = $pmkFdId;


        if ($thisDatabaseReader->querySecurityOk($query, 1)) {
          $query = $thisDatabaseWriter->sanitizeQuery($query);
          $results = $thisDatabaseWriter->update($query, $data);
        }
      }else{
        if (DEBUG) {
          $thisDatabaseWriter->TestSecurityQuery($query, 0);
          print_r($data);
        }

        if ($thisDatabaseWriter->querySecurityOk($query, 0)) {
          $query = $thisDatabaseWriter->sanitizeQuery($query);
          $results = $thisDatabaseWriter->insert($query, $data);
          $primaryKey = $thisDatabaseWriter->lastInsert();
        }

        if (DEBUG) {
          print "<p>pmk= " . $primaryKey;
        }
      }
      $dataEntered = $thisDatabaseWriter->db->commit();

      if (DEBUG)
      print "<p>transaction complete ";
    } catch (PDOExecption $e) {
      $thisDatabase->db->rollback();
      if (DEBUG)
      print "Error!: " . $e->getMessage() . "</br>";
      $errorMsg[] = "There was a problem with accepting your data please contact us directly.";
    }
  }
}



?>
<div id="main" class="container " >
    <?php
    if ($dataEntered) { // closing of if marked with: end body submit
        print "<h1>Record Saved</h1> ";

        // Display the message you created in in SECTION: 2f

    } else {
      if ($errorMsg) {
            print '<div id="errors" class="container ">';
            print '<h1>Your form has the following mistakes</h1>';
            print "<ol>\n";
            foreach ($errorMsg as $err) {
                print "<li>" . $err . "</li>\n";
            }
            print "</ol>\n";
            print '</div>';
        }
      ?>

<form  action="<?php print $phpSelf; ?>"
  method="post"
  id="frmRegister">
  <input class="form-control" type="hidden" id="hidFdId" name="hidFdId"
                   value="<?php print $pmkFdId; ?>"
                   >
  <div class="form-group">
    <label for="txtProduct" >Product</label>

      <input class="form-control" type="text" id="txtProduct" name="txtProduct"
      value="<?php print $product; ?>"
       placeholder="Enter your product"
      <?php if ($productERROR) print 'class="form-control "'; ?>
      onfocus="this.select()"
      autofocus>


  </div>

  <div class="form-group">
    <label  for="txtPrice" >Price</label>

      <input class="form-control" type="text" id="txtPrice" name="txtPrice"
      value="<?php print $price; ?>"
       placeholder="Enter price"
      <?php if ($priceERROR) print 'class="form-control"'; ?>
      onfocus="this.select()"
      >


  </div>

    <input type="submit" id="btnSubmit" name="btnSubmit" value="Save"  class="btn btn-success">
    <input type="submit" id="btnD" name="btnD" value="Delete"  class="btn btn-danger">
</form>
   <!-- ends buttons -->
</div>


<?php
    } // end body submit
    ?>
</div>
<?php
}
include 'footer.php';
?>
