
<!doctype html>
<html>
<body>



<?php

require_once("Sql.php");

class Search{
    private $connectValue;
    function __construct(){
        $s = new Sql();
        $this->connectValue = $s->connectToDatabase('recruit');
    }

    function fullSearch($word){
        $result1 = mysqli_query($this->connectValue, "SELECT * FROM candidate WHERE email LIKE '%$word%' OR LastName LIKE '%$word%' OR FirstName LIKE '%$word%'");
        $result2 = mysqli_query($this->connectValue, "SELECT * FROM recruitmentsession WHERE name LIKE '%$word%'");
        $result3 = mysqli_query($this->connectValue, "SELECT * FROM interview WHERE Name LIKE '%$word%'");



        if ($result1->num_rows > 0) {
            echo "<br><h3 style='padding-left: 100px'>Candidates</h3>";
            // output data of each row
            ?> <ul class="navDownList" style="list-style-type:none"> <?php
            while($row1=mysqli_fetch_array($result1,MYSQLI_NUM)) {
                ?>
                <li><a href="enterCandiateDetails.php?id=<?php echo $row1[0]?>&rsid=<?php echo $row1[1]?>&rname=<?php echo $row1[2]?>&rjob=<?php echo $row1[3]?>&rdate=<?php echo $row1[4]?>&rstatus=<?php echo $row1[5]?>">
                <?php
                echo  $row1[0]." - ".$row1[1]." - ".$row1[5];
                ?></a></li>
<?php
            }
    ?> </ul> <?php
        } else {
            echo "";
        }



        if ($result2->num_rows > 0) {
            echo "<br><h3 style='padding-left: 100px'>Sessions</h3>";
            ?> <ul class="navDownList" style="list-style-type:none"> <?php
            while($row2=mysqli_fetch_array($result2,MYSQLI_NUM)) {
            ?>
            <li><a href="candidateList.php?rsid=<?php echo $row2[0]?>&rname=<?php echo $row2[1]?>&rjob=<?php echo $row2[2]?>&rdate=<?php echo $row2[3]?>&rstatus=<?php echo $row2[4]?>">
            <?php
                echo  $row2[0]." - ".$row2[1]." - ".$row2[4];
            ?></a></li>
            <?php
            }
            ?> </ul> <?php
        } else {
            echo "";
        }



        if ($result3->num_rows > 0) {
            echo "<br><h3 style='padding-left: 100px'>Interviews</h3>";
            ?> <ul class="navDownList" style="list-style-type:none"> <?php
                while($row2=mysqli_fetch_array($result3,MYSQLI_NUM)) {
                    ?>
                    <li><a href="#">
                            <?php
                            echo  $row2[0]." - ".$row2[1]." - ".$row2[4];
                            ?></a></li>
                <?php
                }
                ?> </ul> <?php
        } else {
            echo "";
        }
    }

}
?>
</body>
</html>