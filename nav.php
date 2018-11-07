<!-- ######################     Main Navigation   ########################## -->
<nav class="navbar navbar-default">
    <ul class="nav navbar-nav">
        <?php
        // This sets a class for current page so you can style it differently
        $username = htmlentities($_SERVER["REMOTE_USER"], ENT_QUOTES, "UTF-8");
        $user = array('anoor','iverma','zkong1','rerickso', 'ylin19');

        print '<li ';
        if ($PATH_PARTS['filename'] == 'index') {
            print ' class="active" ';
        }
        print '><a href="index.php">Home</a></li>';


        print '<li ';
        if ($PATH_PARTS['filename'] == 'order') {
            print ' class="active" ';
        }
        print '><a href="order.php">Order</a></li>';

       print '<li ';
        if ($PATH_PARTS['filename'] == 'status') {
            print ' class="active" ';
        }
        print '><a href="status.php">Status</a></li>';

       print '<li ';
        if ($PATH_PARTS['filename'] == 'about') {
            print ' class="active" ';
        }
        print '><a href="about.php">About</a></li>';

        print '<li ';

         if ($PATH_PARTS['filename'] == 'menu') {
             print ' class="active" ';
         }
         print '><a href="menu.php">Menu</a></li>';

if(in_array($username, $user)){
         print '<li ';
         if ($PATH_PARTS['filename'] == 'edit_menu') {
             print ' class="active" ';
         }
         print '><a href="edit_menu.php"> EditMenu </a></li>';
}


if(in_array($username, $user)){
         print '<li ';
         if ($PATH_PARTS['filename'] == 'tables') {
             print ' class="active" ';
         }
         print '><a href="tables.php"> tables </a></li>';
}

        ?>
    </ul>
</nav>

<!-- #################### Ends Main Navigation    ########################## -->
