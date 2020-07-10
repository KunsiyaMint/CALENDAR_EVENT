<?php
   header ("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT");
   include '../../connect/connect.php'; 
   $connectionInfo = array("Database"=>$dbName, "UID"=>$userName, "PWD"=> $userPassword, "MultipleActiveResultSets"=>true,"CharacterSet" => 'UTF-8');
   $conn = sqlsrv_connect( $serverName, $connectionInfo);
   $options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET);

//    $sql = "SELECT * FROM CALENDAR_EVENT";
//    $query=sqlsrv_query($conn,$sql);



$id = $_POST['id'];
$title = $_POST['title'];
$start = $_POST['start'];
$end = $_POST['end'];   //แปลงเป็น timestamp ก่อนแล้วทำการ -1 เพื่อให้ได้จำนวนวันที่ถูกต้อง
$workdate = $_POST['workdate'];


$dateworkdayint = intval($workdate); //แปลง WORKDAY TO INTERGER
$datetmp = strtotime($end);
$datetimestamp = strtotime("-1 day", $datetmp);
$dateformatfinish = date("Y-m-d", $datetimestamp); //FIND to database CALENDAR

$sqlholiday = "SELECT * FROM CALENDAR WHERE DATE <= '$dateformatfinish' AND FLAG IS NULL ORDER BY DATE DESC";
$queryholiday=sqlsrv_query($conn,$sqlholiday);
   //FIND TO HOLIDAY
   $result_list = array();
   while($row=sqlsrv_fetch_array($queryholiday)) 
   {
       $result_list[] = $row;
   }
   $countholiday = count($result_list);
   //$countworkdate = count($workdate);
   $holiday = [];
   for ($j = 0; $j < $countholiday; $j++) 
   {
       if($j != $dateworkdayint){
           $datedbholi = date_format($result_list[$j]['DATE'] , "Y-m-d H:i:s");
           $holidaytimestamp = strtotime($datedbholi);

           array_push ($holiday,$holidaytimestamp);
       }else{
          break; 
       } 
   }
   //echo "HOLIDAY==>" . json_encode($holiday) . '</br>';

  //WORKDATE LOOP FIND FINISH DATE
  $startdate = [];
   for ($i = 0; $i <= count($startdate); $i++) 
       {
           $datetimestamp = strtotime("-1 day", $datetimestamp); //converts to date("Y-m-d", $datetimestamp) AND show in Calendar
           $datetodate = date("Y-m-d", $datetimestamp);
           $workdatetimestamp = strtotime($datetodate);
           //echo "workdatetimestamp" .  $workdatetimestamp . '</br>';
           $filterrunday = $workdatetimestamp; 
           $data = array_filter($startdate, function ($var) use ($filterrunday) {
            return $var != "HOLIDAY";
           });
           $runworkday =  count($data);
           // echo "DATA====>" . $runworkday . '</br>';

          if($runworkday < $dateworkdayint){
               $filterBy = $workdatetimestamp; 
               $new = array_filter($holiday, function ($var) use ($filterBy) {
               return $var == $filterBy;
          });
          //echo  "NEW==>" . json_encode($new) . '</br>';
          if($new){
            array_push($startdate,"HOLIDAY");
           //$startdate[] = array("holiday" => "Y","startday" => NULL);  
          }else{
           //$startdate[] = array("holiday" => NULL,"startday" => $workdatetimestamp );
            array_push($startdate,$workdatetimestamp);
           }
          }
          else{
               break; 
          }           
       } 
       //echo " startdate" .  json_encode($startdate);
       //START DATE
       $firstdatearray = [];
       for($m=0; $m < count($startdate); $m++ ){
           //echo "startdate LOOP===>" . $startdate[$m] . '</br>';              
           if($startdate[$m] != 'HOLIDAY'){
               array_push($firstdatearray,$startdate[$m]);
           }
         
       }
       $lastdata = array_values(array_slice($firstdatearray, -1));  //ตัวสุดท้ายของ Array เป็นวันที่ StartDate
       $stringtodata = intval(implode("",$lastdata));
       $firstdate = date("Y-m-d", $stringtodata);
       //echo "START DATE ===>" . $firstdate . '</br>'; 
       //END DATE 
       $datetmps = strtotime($end);
       $datetimestamps = strtotime("-1 day", $datetmp);
       //$finishlastdate = strtotime("+1 day", $datetimestamps);   //+1 เพื่อแสดงใน ปฎิทิน 
       $lastdate = date("Y-m-d", $datetimestamps);

       $sqlUpdate = "UPDATE CALENDAR_EVENT SET TITLE='" . $title . "',PLANSTART='" . $firstdate . "',PLANFINISH='" . $lastdate . "',PLANWORKDAY='" . $workdate . "' WHERE id=" . $id;
       $query=sqlsrv_query($conn,$sqlUpdate);
       $datalast  =  array(
            "id"=> $id,
            "title"=>$title,
            "start"=>$firstdate,
            "end"=> $lastdate,
            "workday" => $workdate,
       );

       $datas = [];
       array_push($datas,$datalast);
       echo json_encode($datas);
       sqlsrv_close($conn);

// }



?>