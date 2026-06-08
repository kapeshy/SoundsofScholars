<?php
include("../config/db.php");

$result = mysqli_query($conn,
"SELECT applications.id, users.full_name, scholarships.title, applications.status
 FROM applications
 JOIN users ON users.id = applications.user_id
 JOIN scholarships ON scholarships.id = applications.scholarship_id");

while ($row = mysqli_fetch_assoc($result)) {
    echo $row['full_name']." - ".$row['title']." - ".$row['status'];
    echo " <a href='update.php?id=".$row['id']."&s=Approved'>Approve</a>";
    echo " <a href='update.php?id=".$row['id']."&s=Rejected'>Reject</a><br>";
}
?>
