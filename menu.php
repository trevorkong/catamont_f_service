<?php
include 'top.php';
//##############################################################################
//
// This page lists the records based on the query given
//
//##############################################################################
$records = '';

$query = 'SELECT pmkFdId, fldProduct, fldPrice FROM tblMenu';

if ($thisDatabaseReader->querySecurityOk($query, 0)) {
    $query = $thisDatabaseReader->sanitizeQuery($query);
    $records = $thisDatabaseReader->select($query, '');

}
print '<div class="container " >';
print '<h1>OUR MENU :</h2>';

print '<table class="table table-hover">';
print '<thead>
      <tr>
        <th>Food</th>
        <th>Price</th>';
        if(in_array($username, $user)){
          print   '<th>Edit</th>';
}
print   '</tr></thead><tbody>';
if (is_array($records)) {
    foreach ($records as $record) {
        print '<tr><td>' . $record['fldProduct'] .'</td><td>'.$record['fldPrice'];
        print '</td>';
        if(in_array($username, $user)){
        print'<td><a href="https://iverma.w3.uvm.edu/cs148/catamount_food_service/edit_menu.php?id='.$record['pmkFdId'].'">EDIT</a></td>';
      }
        print '</tr>';

        }
}
print '</tbody></table>';
print '</div>';

?>
<?php
include 'footer.php';
?>
