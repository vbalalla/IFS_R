<?php
echo "OPOPOP";


$rSessionID="";
		if(isset($_GET["rsid"])){
				$rSessionID=$_GET["rsid"];
		}	
		
		
		
		$intID="";
		if(isset($_GET["intID"])){
				$intID=$_GET["intID"];
		}
		
		
		$criteriaID="";
		if(isset($_GET["criteriaID"])){
			
				$criteriaID=$_GET["criteriaID"];
		}
		
echo $intID;
echo $criteriaID;

$connect = mysqli_connect('localhost','root',"") or die("Unable to connect");
$select_db = mysqli_select_db($connect,'recruit') or die("Unable to connect to database");
$idRow=mysqli_query($connect,"update interview set EvaCriID='$criteriaID' where IntID='$intID'");

$criteria = mysqli_query($connect,"SELECT * FROM criteria WHERE criteriaID=$criteriaID");
$row = $criteria->fetch_row();

$evaluation = mysqli_query($connect,"SELECT * FROM evaluation WHERE criteriaID=$criteriaID");
$candidates = mysqli_query($connect,"SELECT DISTINCT CandID FROM candidate,cv,interview WHERE candidate.cvID=cv.cvID AND cv.RSID=interview.RSID AND interview.IntID=$intID AND (candidate.candStatusID='CS001' OR candidate.candStatusID='CS005')");

echo "SSSS";
echo "$criteriaID\n";
echo "QQQQ";
echo "$intID\n";

$createTable = "CREATE TABLE IF NOT EXISTS criteria".$criteriaID."_".$intID."(MarkID int AUTO_INCREMENT,CandID VARCHAR(8),panelMember VARCHAR(20),comment text(50)";

//echo "CREATE TABLE criteria".$criteriaID."_".$intID."(MarkID int AUTO_INCREMENT,CandID VARCHAR(8)";

$i=0;
while($col = $evaluation->fetch_row()){
	
	$str = $col[1];
	$strlen = strlen( $col[1] );
	$id = "";
	for( $i = 0; $i <= $strlen; $i++ ) {
    $char = substr( $str, $i, 1 );
    if( $char == " " ){
		$char = '_'; 
	}
    $id .= $char;
	
}
$createTable = ($createTable).",$id varchar(20)";
	//echo $id;
}
$createTable = $createTable.",CONSTRAINT PRIMARY KEY (MarkID), CONSTRAINT FOREIGN KEY(CandID)REFERENCES candidate(CandID) ON DELETE CASCADE ON UPDATE CASCADE)";

//echo $createTable;

mysqli_query($connect,$createTable);

$queryToGetPanel="SELECT IntPanID FROM interview WHERE IntID=$intID";
echo $queryToGetPanel;
$result=mysqli_query($connect,$queryToGetPanel);
while($row1=$result->fetch_row()){
		$panelID=$row1[0];
	
	}

$queryToCountPanelMembers="SELECT COUNT(*) FROM interviewpanelmemberdetails WHERE IntPanID=$panelID";

$queryToGetPanelMembers=mysqli_query($connect,"SELECT EmpID FROM interviewpanelmemberdetails WHERE IntPanID=$panelID");

$result1=mysqli_query($connect,$queryToCountPanelMembers);
while($row1=$result1->fetch_row()){
		$NumOfPanelMembers=$row1[0];
	
	}

echo "iuiuiu";
echo $NumOfPanelMembers;


while($n1 = $candidates->fetch_row()){
$n[]=$n1;	
}
while($panelmember1=$queryToGetPanelMembers->fetch_row()){
$panelmember[]=$panelmember1;	
}

$length=sizeof($n);
$length1=sizeof($panelmember);
echo $length;
echo "iiiii";
echo "SELECT DISTINCT CandID FROM candidate,cv,interview WHERE candidate.cvID=cv.cvID AND cv.RSID=interview.RSID AND interview.IntID=$intID AND (candidate.candStatusID='CS001' OR candidate.candStatusID='CS005')";
for($i=0;$i<$length;$i++){
echo $n[$i][0];	
}


	
	echo "uuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuu";
	//echo $n[0];
	echo $n[0][0];
	echo $n[1][0];
	echo "uuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuu";
for($i=0;$i<$length;$i++){
	echo "what the hell";
	echo $n[$i][0];	
for($j=0;$j<$length1;$j++){
	$value=$n[$i][0];
	$value1=$panelmember[$j][0];
	echo "INSERT INTO criteria".$criteriaID."_".$intID."(CandID,panelMember) VALUES ('$value','$value1')";
//	$candidates = mysqli_query($connect,"SELECT DISTINCT CandID FROM candidate,cv,interview WHERE candidate.cvID=cv.cvID AND cv.RSID=interview.RSID AND interview.IntID=$intID AND (candidate.candStatusID='CS001' OR candidate.candStatusID='CS005')");
//echo "INSERT INTO criteria".$criteriaID."_".$intID."(CandID,panelMember) VALUES ('$n[$i][0]',$panelmember[0])";
//echo ""
//echo "$n[$i][0]";
	mysqli_query($connect,"INSERT INTO criteria".$criteriaID."_".$intID."(CandID,panelMember) VALUES ('$value','$value1')");
}
}


header("Location: a_interviews.php?rsid=$rSessionID&rname=$rName&rjob=$rJob&rdate=$rDate&rstatus=$rStatus");
?>