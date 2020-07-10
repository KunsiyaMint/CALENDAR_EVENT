<?php
   header ("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT");
   include '../../connect/connect.php'; 
   $connectionInfo = array("Database"=>$dbName, "UID"=>$userName, "PWD"=> $userPassword, "MultipleActiveResultSets"=>true,"CharacterSet" => 'UTF-8');
   $conn = sqlsrv_connect( $serverName, $connectionInfo);
   $options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET);
   $sql = "SELECT * FROM MPS A, MS_GANTT B WHERE A.MATERIAL = B.MATERIAL";
   $query=sqlsrv_query($conn,$sql);
   $datas = [];
   while($result=sqlsrv_fetch_array($query))									
   {  
       $datefnfinish = $result['FFINISH'];
       $workdayfinal = $result['F1'];
       $id = $result['IDNO'];
       $datatitle = $result['WBSFROM'];
   
       //echo "DATA==>" . date_format($result['FFINISH'], "Y-m-d H:i:s").'</br>';  
        $datestart = $result['FSTART'];
         if(isset($datestart)){
             //echo "HAVE---";
           $datalast  =  array(
                    "id"=> $id,
                    "title"=>$result['WBSFROM'],
                    "start"=>$result['BSTART'],
                    "end"=> $result['FFINISH'],
                    "f1"=> $result['F1'],
                    "m1"=> $result['M1'],
                    "b1"=> $result['B1'],
           );
            array_push($datas,$datalast);
         }

   $intfndate = intval($workdayfinal);
   $intfndateloopholi = intval($workdayfinal)+10;

   $datefn = date_format($datefnfinish, "Y-m-d H:i:s");  //find holiday & Save to Database

   $tmpdatefn = strtotime($datefn);          //timestampfinalfinish
   $tmpdatefinish = strtotime("+1 day", $tmpdatefn); //+1 เพื่อเพิ่มวันไปข้างหน้าแล้วทำการนับถอยหลังวันที่ในปฏิทิน

   //find Holiday
   $sqlholiday = "SELECT * FROM CALENDAR WHERE DATE <= '$datefn' AND FLAG IS NULL ORDER BY DATE DESC";
   $queryholiday=sqlsrv_query($conn,$sqlholiday);
   $result_list = array();
   while($row=sqlsrv_fetch_array($queryholiday)) 
     {
        $result_list[] = $row;
     }
   $countholiday = count($result_list);
   $holiday = [];
       for ($j = 0; $j < $countholiday; $j++) 
       {
            if($j != $intfndateloopholi){
               $datedbholi = date_format($result_list[$j]['DATE'] , "Y-m-d H:i:s");
               $holidaytimestamp = strtotime($datedbholi);
               array_push ($holiday,$holidaytimestamp);
           }else{
              break; 
           } 
       }
       //echo "HOLIDAY====>" . json_encode($holiday) .'</br>';

      //WORKDATE LOOP FIND START DATE
       $startdate = [];
       for ($i = 0; $i <= count($startdate); $i++) 
           {
               $tmpdatefinish = strtotime("-1 day", $tmpdatefinish); //converts to date("Y-m-d", $datetimestamp) AND show in Calendar
               $datetodate = date("Y-m-d", $tmpdatefinish);
               $workdatetimestamp = strtotime($datetodate);
               //echo "newDate" .  $datetodate . '</br>';
               $filterrunday = $workdatetimestamp; 
               $data = array_filter($startdate, function ($var) use ($filterrunday) {
                return $var != "HOLIDAY";
               });
               $runworkday =  count($data);
               // echo "DATA====>" . $runworkday . '</br>';

              if($runworkday < $intfndate){
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
        
            //CROSS holiday
           $firstdatearray = [];
           for($m=0; $m < count($startdate); $m++ ){
               //echo "startdate LOOP===>" . $startdate[$m] . '</br>';              
               if($startdate[$m] != 'HOLIDAY'){
                   array_push($firstdatearray,$startdate[$m]);
               }
           }
           //echo "STARTDATE FINAL DATE ====>" . json_encode($firstdatearray) .'</br>';
           $lastdata = array_values(array_slice($firstdatearray, -1));  //ตัวสุดท้ายของ Array เป็นวันที่ StartDate
           $stringtodata = intval(implode("",$lastdata));
           $firstdate = date("Y-m-d", $stringtodata);

           //UPDATE START FINAL DATE
           $sqlfn = "UPDATE MPS  SET FSTART = '$firstdate'  WHERE IDNO = '$id'";
           sqlsrv_query($conn,$sqlfn);

// //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
    //MACHINE WORKDAY
           $workdaymachine = $result['M1'];
           $finishdatemcs = strtotime($firstdate); 
           $finishdatemc = strtotime("-1 day", $finishdatemcs); //นับถอยหลังมา 1 วันจาก วัน start ของ FN_START
           $datemc = date("Y-m-d", $finishdatemc);  //MC_FINISH find database
           $intmcdate = $workdaymachine; //ดึงมาจาก database Master

           $intmcdateloop = intval($workdaymachine)+10;
           $datafinishmc = [];
           for($t = 0; $t <= $intmcdateloop; $t++){
               $finishdatemcs = strtotime("-1 day", $finishdatemcs);  //ถอยหลังมา 1 วัน  finishdate ของ mc
               array_push($datafinishmc,$finishdatemcs);
           }
        
           //find Holiday
           $sqlholidaymc = "SELECT * FROM CALENDAR WHERE DATE <= '$datemc' AND FLAG IS NULL ORDER BY DATE DESC";
           $queryholidaymc=sqlsrv_query($conn,$sqlholidaymc);
           $result_listmc = array();
           while($row=sqlsrv_fetch_array($queryholidaymc)) 
           {
               $result_listmc[] = $row;
           }
           $countholidaymc = count($result_listmc);
           $holidaymc = [];
           for ($k = 0; $k < $countholidaymc; $k++) 
           {
               if($k != $intmcdateloop)
               {
               $datedbholimc = date_format($result_listmc[$k]['DATE'] , "Y-m-d H:i:s");
               $holidaytimestampmc = strtotime($datedbholimc);
               array_push ($holidaymc,$holidaytimestampmc);
               }
               else
               {
                   break; 
               } 
           }
           $lastmc = [];
           foreach($datafinishmc as $item) {
               $filterBy = $item; 
               $newmc = array_filter($holidaymc, function ($var) use ($filterBy) {
                   return $var == $filterBy;
              });
              if($newmc){
               array_push($lastmc,"HOLIDAY");
             }else{
               array_push($lastmc,$item);
              }
           }

           //ตัด HOLIDAY
           $firstfinishmc = [];
           for($a=0; $a < count($lastmc); $a++ ){
               //echo "startdate LOOP===>" . $startdate[$m] . '</br>';              
               if($lastmc[$a] != 'HOLIDAY'){
                   array_push($firstfinishmc,$lastmc[$a]);
               }
           }
           $finishdatemachinesave = date("Y-m-d",$firstfinishmc[0]);   //ยังไม่บวกให้เซฟลง database
           //echo "finishdatemachinesave==>" .   $finishdatemachinesave .'</br>'; 

           $finishdatemachine = strtotime("+ 1 day", $firstfinishmc[0]); //บวกแล้วเตรียมนับถอยหลังปฎิทิน
           //echo "finishdatemachine==>" . $finishdatemachine .'</br>'; 

           // //WORKDATE LOOP FIND START DATE
           $startdatemc = [];
           for ($l = 0; $l <= count($startdatemc); $l++) 
           {
               $finishdatemachine = strtotime("-1 day", $finishdatemachine); //converts to date("Y-m-d", $datetimestamp) AND show in Calendar
               $datetodatemc = date("Y-m-d", $finishdatemachine);
               $workdatetimestampmc = strtotime($datetodatemc);
           
               $filterrundaymc = $workdatetimestampmc; 
               $data = array_filter($startdatemc, function ($var) use ($filterrundaymc) {
                return $var != "HOLIDAY";
               });
               $runworkdaymc =  count($data);
               // echo "DATA====>" . $runworkday . '</br>';

              if($runworkdaymc < $intmcdate){
                   $filterBy = $workdatetimestampmc; 
                   $newmc = array_filter($holidaymc, function ($var) use ($filterBy) {
                   return $var == $filterBy;
              });
              //echo  "NEW==>" . json_encode($new) . '</br>';
              if($newmc){
                array_push($startdatemc,"HOLIDAY");
               //$startdate[] = array("holiday" => "Y","startday" => NULL);  
              }else{
               //$startdate[] = array("holiday" => NULL,"startday" => $workdatetimestamp );
                array_push($startdatemc,$workdatetimestampmc);
               }
              }
              else{
                   break; 
              }           
           } 
            //echo " startdate" .  json_encode($startdate);

           //CROSS holiday
           $firstdatearraymc = [];
           for($n=0; $n < count($startdatemc); $n++ ){
               //echo "startdate LOOP===>" . $startdate[$m] . '</br>';              
               if($startdatemc[$n] != 'HOLIDAY'){
                   array_push($firstdatearraymc,$startdatemc[$n]);
               }
           }
           $lastdatamc = array_values(array_slice($firstdatearraymc, -1));  //ตัวสุดท้ายของ Array เป็นวันที่ StartDate
           $stringtodatamc = intval(implode("",$lastdatamc));
           $firstdatemc = date("Y-m-d", $stringtodatamc);
           // echo "ID ===>" . $id . '</br>'; 
           //echo "START DATE ===>" . $firstdatemc . '</br>'; 
           //UPDATE MC FINAL DATE
           $sqlmc = "UPDATE MPS  SET MSTART = '$firstdatemc' ,MFINISH = '$finishdatemachinesave'  WHERE IDNO = '$id'";
           sqlsrv_query($conn,$sqlmc);
        
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          
           //GROUPBODY WORKDAY
           $workdaygroupbody = $result['B1'];

           $finishdategbs = strtotime($firstdatemc);
           //echo "START DATE ===>" . $finishdategbs . '</br>'; 

           $finishdategb = strtotime("-1 day", $finishdategbs); //นับถอยหลังมา 1 วันจาก วัน start ของ FN_START
           $dategb = date("Y-m-d", $finishdategb);  //MC_FINISH find database
           $intgbdate = $workdaygroupbody; //ดึงมาจาก database Master

           $intgbdateloop = $intgbdate+10;  //บวกลบวันหยุดทดแทน 10 วัน
           $datafinishgb = [];
           for($b = 0; $b <= $intgbdateloop; $b++){
               $finishdategbs = strtotime("-1 day", $finishdategbs);  //ถอยหลังมา 1 วัน  finishdate ของ mc
               array_push($datafinishgb,$finishdategbs);
           }

           //echo "datafinishgb ==> " . json_encode($datafinishgb) .'</br>';
           //find Holiday
           $sqlholidaygb = "SELECT * FROM CALENDAR WHERE DATE <= '$dategb' AND FLAG IS NULL ORDER BY DATE DESC";
           $queryholidaygb=sqlsrv_query($conn,$sqlholidaygb);
           $result_listgb = array();
           while($row=sqlsrv_fetch_array($queryholidaygb)) 
           {
               $result_listgb[] = $row;
           }
           $countholidaygb = count($result_listgb);
           $holidaygb = [];
           for ($p = 0; $p < $countholidaygb; $p++) 
           {
               if($p != $intgbdateloop)
               {
               $datedbholigb = date_format($result_listgb[$p]['DATE'] , "Y-m-d H:i:s");
               $holidaytimestampgb = strtotime($datedbholigb);
               array_push ($holidaygb,$holidaytimestampgb);
               }
               else
               {
                   break; 
               } 
           }
           //echo "HOLIDAY ==>" .   json_encode($holidaygb) .'</br>'; 

           $lastgb = [];
           foreach($datafinishgb as $item) {
               $filterBy = $item; 
               $newgb = array_filter($holidaygb, function ($var) use ($filterBy) {
                   return $var == $filterBy;
              });
              if($newgb){
               array_push($lastgb,"HOLIDAY");
             }else{
               array_push($lastgb,$item);
              }
           }
           //echo "firstfinishgb 1 ==>" .   json_encode($lastgb) .'</br>'; 

           //ตัด HOLIDAY
           $firstfinishgb = [];
           for($q=0; $q < count($lastgb); $q++ ){
               //echo "startdate LOOP===>" . $startdate[$m] . '</br>';              
               if($lastgb[$q] != 'HOLIDAY'){
                   array_push($firstfinishgb,$lastgb[$q]);
               }
           }
           //echo "firstfinishgb ==>" .   json_encode($firstfinishgb) .'</br>'; 


           $finishdategroupbodysave = date("Y-m-d",$firstfinishgb[0]);   //ยังไม่บวกให้เซฟลง database
           //echo "finishdatemachinesave==>" .   $finishdatemachinesave .'</br>'; 
           $finishdategroupbody = strtotime("+ 1 day", $firstfinishgb[0]); //บวกแล้วเตรียมนับถอยหลังปฎิทิน
           //echo "finishdatemachine==>" . $finishdatemachine .'</br>'; 

           // //WORKDATE LOOP FIND START DATE
           $startdategb = [];
           for ($r = 0; $r <= count($startdategb); $r++) 
           {
               $finishdategroupbody = strtotime("-1 day", $finishdategroupbody); //converts to date("Y-m-d", $datetimestamp) AND show in Calendar
               $datetodategb = date("Y-m-d", $finishdategroupbody);
               $workdatetimestampgb = strtotime($datetodategb);
           
               $filterrundaygb = $workdatetimestampgb; 
               $data = array_filter($startdategb, function ($var) use ($filterrundaygb) {
                return $var != "HOLIDAY";
               });
               $runworkdaygb =  count($data);
               // echo "DATA====>" . $runworkday . '</br>';

              if($runworkdaygb < $intgbdate){
                   $filterBy = $workdatetimestampgb; 
                   $newgb = array_filter($holidaygb, function ($var) use ($filterBy) {
                   return $var == $filterBy;
              });
              //echo  "NEW==>" . json_encode($new) . '</br>';
              if($newgb){
                array_push($startdategb,"HOLIDAY");
               //$startdate[] = array("holiday" => "Y","startday" => NULL);  
              }else{
               //$startdate[] = array("holiday" => NULL,"startday" => $workdatetimestamp );
                array_push($startdategb,$workdatetimestampgb);
               }
              }
              else{
                   break; 
              }           
           } 
            //echo " startdate" .  json_encode($startdate);
            //START DATE
           $firstdatearraygb = [];
           for($s=0; $s < count($startdategb); $s++ ){
               //echo "startdate LOOP===>" . $startdate[$m] . '</br>';              
               if($startdategb[$s] != 'HOLIDAY'){
                   array_push($firstdatearraygb,$startdategb[$s]);
               }
           }
           $lastdatagb = array_values(array_slice($firstdatearraygb, -1));  //ตัวสุดท้ายของ Array เป็นวันที่ StartDate
           $stringtodatagb = intval(implode("",$lastdatagb));
           $firstdategb = date("Y-m-d", $stringtodatagb);
           // echo "ID ===>" . $id . '</br>'; 
           //echo "START DATE ===>" . $firstdatemc . '</br>'; 
           
           //UPDATE MC GROUPBODY DATE
           $sqlgb = "UPDATE MPS  SET BSTART = '$firstdategb' ,BFINISH = '$finishdategroupbodysave'  WHERE IDNO = '$id'";
           sqlsrv_query($conn,$sqlgb);

        
     
// // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// //PLAN TO DISPLAY CALENDAR
          $dateend = strtotime($datefn);                //แปลงเป็น timestamp
          $enddate = strtotime("+1 day", $tmpdatefn);   //+1 เพื่อแสดงใน ปฎิทิน 
          $lastdate = date("Y-m-d", $enddate);          //แสดงวันสุดท้ายใน ปฎิทิน 
          $datalast  =  array(
               "id"=> $id,
               "title"=>$datatitle,
               "start"=>$firstdategb,
               "end"=> $lastdate,
               "f1"=> $workdayfinal,
               "m1"=> $workdaymachine,
               "b1"=> $workdaygroupbody,
           );
           array_push($datas,$datalast);
        }
          echo json_encode($datas);  //ต้องอยู่นอกลูป
?>

