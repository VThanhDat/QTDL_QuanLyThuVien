<?php
require_once("includes/config.php");
if (!empty($_POST["bookid"])) {
    $bookid = $_POST["bookid"];

    // Modify the query to use LIKE for both ISBNNumber and BookName
    $sql = "SELECT BookName, id FROM sach WHERE ISBNNumber LIKE :bookid OR BookName LIKE :bookidlike";
    $query = $dbh->prepare($sql);

    // Use LIKE for both ISBNNumber and BookName
    $bookidlike = "%" . $bookid . "%";
    $query->bindParam(':bookid', $bookidlike, PDO::PARAM_STR); // Use LIKE for ISBNNumber
    $query->bindParam(':bookidlike', $bookidlike, PDO::PARAM_STR); // Use LIKE for BookName

    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() > 0) {
        foreach ($results as $result) { ?>
            <option value="<?php echo htmlentities($result->id); ?>"><?php echo htmlentities($result->BookName); ?></option>
            <b>Tên Sách :</b>
        <?php
            echo htmlentities($result->BookName);
            echo "<script>$('#submit').prop('disabled',false);</script>";
        }
    } else { ?>
        <option class="others"> Không tìm thấy Sách với Số ISBN hoặc Tên Sách</option>
<?php
        echo "<script>$('#submit').prop('disabled',true);</script>";
    }
}
?>