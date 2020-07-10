<?php
   header ("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT");
   include '../../connect/connect.php'; 
   $connectionInfo = array("Database"=>$dbName, "UID"=>$userName, "PWD"=> $userPassword, "MultipleActiveResultSets"=>true,"CharacterSet" => 'UTF-8');
   $conn = sqlsrv_connect( $serverName, $connectionInfo);
   $options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET);
   $sql = "SELECT * FROM CALENDAR_EVENT";
   $query=sqlsrv_query($conn,$sql);
   $datas = [];
   while($result=sqlsrv_fetch_array($query))									
   {
    $datefinish = $result['PLANFINISH'];
    $dateworkday = $result['PLANWORKDAY'];
    $datestart = $result['PLANSTART'];
    $id = $result['ID'];
    $datatitle = $result['TITLE'];
    //echo "PLANSTART==>" . $datestart;
    if(isset($datestart)){
        //echo "HAVE---";
        $datalast  =  array(
            "id"=> $id,
            "title"=>$datatitle,
            "start"=>$datestart,
            "end"=> $datefinish,
            "workday" => $dateworkday,
        );
          array_push($datas,$datalast);
    }

     //$workday = "-".$dateworkday ." " . "day";  //WORKDATE ไว้ลบตามจำนวนวัน
     $dateworkdayint = intval($dateworkday); //แปลง WORKDAY TO INTERGER
     $datedb = date_format($datefinish , "Y-m-d H:i:s");//FROM database TO timestamp
     $datetimestamp = strtotime($datedb);
     //echo " datedb" .  $datetimestamp . '</br>';
     $countfinishdate = count($datefinish);
     $sqlholiday = "SELECT * FROM CALENDAR WHERE DATE <= '$datedb' AND FLAG IS NULL ORDER BY DATE DESC";
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
            $datedb = date_format($datefinish , "Y-m-d H:i:s");//FROM database TO timestamp
            $datetimestampfinish = strtotime($datedb);
            $finishlastdate = strtotime("+1 day", $datetimestampfinish);   //+1 เพื่อแสดงใน ปฎิทิน 
            $lastdate = date("Y-m-d", $finishlastdate);
            //echo "END DATE ===>" . $lastdate . '</br>';

            $datalast  =  array(
                "id"=> $id,
                "title"=>$datatitle,
                "start"=>$firstdate,
                "end"=> $lastdate,
                "workday" => $dateworkday,
            );
     
        array_push($datas,$datalast);
   }  
   echo json_encode($datas);

?>

