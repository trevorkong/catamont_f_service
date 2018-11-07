<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Order Food </title>
        <meta charset="utf-8">
        <meta name="author" content="Ishan, Kong, Ahmed">
        <meta name="description" content="Catamount Food Ordering System">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!--[if lt IE 9]>
        <script src="//html5shim.googlecode.com/sin/trunk/html5.js"></script>
        <![endif]-->

       <link rel="stylesheet" href="morecss.css" type="text/css" media="screen">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <style>

        html {
          position: relative;
          min-height: 100%;
        }
        body {
          /* Margin bottom by footer height */
          margin-bottom: 60px;
        }
        .footer {
          position: absolute;
          bottom: 0;
          width: 100%;
          /* Set the fixed height of the footer here */
          height: 60px;
          background-color: #f5f5f5;
        }

</style>

        <?php

        $debug = false;

        // This if statement allows us in the classroom to see what our variables are
        // This is NEVER done on a live site
        if (isset($_GET["debug"])) {
            $debug = true;
        }

// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// PATH SETUP
//

        $domain = "//";

        $server = htmlentities($_SERVER['SERVER_NAME'], ENT_QUOTES, "UTF-8");

        $domain .= $server;

        $phpSelf = htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, "UTF-8");

        $path_parts = pathinfo($phpSelf);

        if ($debug) {
            print "<p>Domain: " . $domain;
            print '<p>php Self: ' . $phpSelf;
            print '<p>Path Parts<pre>';
            print_r($path_parts);
            print '</pre></p>';
        }

        // %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
        //
        // inlcude all libraries.
        //
        // %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
        print '<!-- begin including libraries -->';

        include 'lib/constants.php';

        include LIB_PATH . '/Connect-With-Database.php';

        print '<!-- libraries complete-->';

        print  "\n". '<!-- include libraries -->' . "\n";

        require_once('lib/security.php');

        // notice this if statemtent only includes the functions if it is
        // form page. A common mistake is to make a form and call the page
        // join.php which means you need to change it below (or delete the if)
        //if ($path_parts['filename'] == "form") {
            print "\n<!-- include form libraries -->\n";
            include "lib/validation-functions.php";
            include "lib/mail-message.php";
    //    }

        print  "\n" . '<!-- finished including libraries -->' . "\n";

        ?>

    </head>

    <!-- **********************     Body section      ********************** -->
    <?php
    include 'header.php';
    include 'nav.php';

    print '<body id="' . $PATH_PARTS['filename'] . '">';

    ?>
