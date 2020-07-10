<?php 
	/*------------------------------------function post----------------------------------------*/
	if ($checkfuction == 'manualCheckitem') 
	{
		manualCheckitem($_POST);
	}
	if ($checkfuction == 'manualCheckDoc') 
	{
		manualCheckDoc($_POST);
	}
	if ($checkfuction == 'manualCheckSendMail') 
	{
		manualCheckSendMail($_POST);
	}
	if ($checkfuction == 'manualInsertDetail') 
	{
		manualInsertDetail($_POST);
	}
	if ($checkfuction == 'manualSelectDetail') 
	{
		manualSelectDetail($_POST);
	}
	if ($checkfuction == 'manualUpdateDetail') 
	{
		manualUpdateDetail($_POST);
	}
	if ($checkfuction == 'VOID') 
	{
		VOID($_POST);
	}
	if ($checkfuction == 'manualCheckMailENG') 
	{
		manualCheckMailENG($_POST);
	}
	if ($checkfuction == 'manualCheckWorkMail') 
	{
		manualCheckWorkMail($_POST);
	}
	if ($checkfuction == 'manualCheckWorkMailPNCheck') 
	{
		manualCheckWorkMailPNCheck($_POST);
	}
	if ($checkfuction == 'manualCheckMailFinished') 
	{
		manualCheckMailFinished($_POST);
	}
	if ($checkfuction == 'Pn_work') 
	{
		Pn_work($_POST);
	}
	if ($checkfuction == 'RETURN_ENG')
	{
		RETURN_ENG($_POST);
	}
	if ($checkfuction == 'RETURN_WORK')
	{
		RETURN_WORK($_POST);
	}
	if ($checkfuction == 'PN_CHECK')
		{
			PN_CHECK($_POST);
		}
	if ($checkfuction =='ECN_Finished')
		{
			ECN_Finished($_POST);
		}
	/*------------------------------------function post----------------------------------------*/

	/*------------------------------------function get----------------------------------------*/

	
	if(isset($_GET['temp']) || isset($_GET['mr']) || isset($_GET['mc'])){
		if ($_GET['temp'] != 'scrap' || $_GET['temp'] != 'ma') {
			$datatemp = manualnotemplate();
		}
		if ($_GET['temp'] == 'scrap') {
			$datatemp = manualScrap();
		}
		if ($_GET['temp'] == 'ma') {
			$datatemp = manualMa();
		}
		$datatemp['temp'] = $_GET['temp'];
		$datatemp['mr'] = isset($_GET['mr'])?$_GET['mr']:'';
		$datatemp['mc'] = isset($_GET['mc'])?$_GET['mc']:'';
		$datatemp['sl'] = isset($_GET['sl'])?$_GET['sl']:'';
	}


	/*------------------------------------function get----------------------------------------*/


	/*------------------------------------index----------------------------------------*/
	function deltemp($param){	
		$sql = "DELETE FROM SDO_TEMPLATE
				WHERE TemplateID = '".$param['TemplateID']."'";
		$checkerr = ConnectManage($sql);
		if ($checkerr == 'error') {
			$data['check'] = 'Error';
		}else{
			$data['check'] = 'SaveOk';
		}
		
		echo json_encode($data);
	}


	function manualCheckSendMail($param)	//Mail only PN + PROD
	{	
		include '../../connect/connect.php'; 
		$connectionInfo = array("Database"=>$dbName, "UID"=>$userName, "PWD"=> $userPassword, "MultipleActiveResultSets"=>true,"CharacterSet" => 'UTF-8');
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		$params = array();
		$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET);
		$sqlemail = "SELECT PNWORK_MAIL,PROD_MAIL FROM EMAIL_MRP WHERE MRP = '".$param['MRPC']."'";
		$queryemail=sqlsrv_query($conn,$sqlemail);
		$queryemail_array=sqlsrv_fetch_array($queryemail);
		$emailsend =  $queryemail_array['PNWORK_MAIL']. " " .$queryemail_array['PROD_MAIL'];

		if ($emailsend) 
		{
			$data =  $emailsend;
			// echo json_encode(array("mail"=>$data));
		}else{

			$data = 'NO DATA';
			
		    // echo json_encode($data);
		}
		echo json_encode($data);
		
	}

	function manualCheckMailENG($param){
		include '../../connect/connect.php'; 
		$connectionInfo = array("Database"=>$dbName, "UID"=>$userName, "PWD"=> $userPassword, "MultipleActiveResultSets"=>true,"CharacterSet" => 'UTF-8');
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		$params = array();
		$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET);
		$sqlemail = "SELECT ENG_MAIL FROM EMAIL_MRP WHERE MRP = '".$param['MRPC']."'";
		$queryemail=sqlsrv_query($conn,$sqlemail);
		$queryemail_array=sqlsrv_fetch_array($queryemail);
		$emailsend =  $queryemail_array['ENG_MAIL'];
		if ($emailsend) 
		{
			$data =  $emailsend;
			// echo json_encode(array("mail"=>$data));
		}else{

			$data = 'NO DATA';
			
		    // echo json_encode($data);
		}
		echo json_encode($data);

	}

	function manualCheckWorkMailPNCheck($param){  //ONLY PN_CHECKMAIL
		include '../../connect/connect.php'; 
		$connectionInfo = array("Database"=>$dbName, "UID"=>$userName, "PWD"=> $userPassword, "MultipleActiveResultSets"=>true,"CharacterSet" => 'UTF-8');
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		$params = array();
		$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET);
		$sqlemail = "SELECT PNCHECK_MAIL FROM EMAIL_MRP WHERE MRP = '".$param['MRPC']."'";
		$queryemail=sqlsrv_query($conn,$sqlemail);
		$queryemail_array=sqlsrv_fetch_array($queryemail);
		$emailpncheck =  $queryemail_array['PNCHECK_MAIL'];
		if ($emailpncheck) 
		{
			$data =  $emailpncheck;
			// echo json_encode(array("mail"=>$data));
		}else{
			$data = 'NO DATA';
		    // echo json_encode($data);
		}
		echo json_encode($data);

	}

	
	function manualCheckMailFinished($param){  //ONLY Finished ENGMAIL+WORKMAIL+PROD
		include '../../connect/connect.php'; 
		$connectionInfo = array("Database"=>$dbName, "UID"=>$userName, "PWD"=> $userPassword, "MultipleActiveResultSets"=>true,"CharacterSet" => 'UTF-8');
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		$params = array();
		$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET);
		$sqlemail = "SELECT ENG_MAIL,PNWORK_MAIL,PROD_MAIL FROM EMAIL_MRP WHERE MRP = '".$param['MRPC']."'";
		$queryemail=sqlsrv_query($conn,$sqlemail);
		$queryemail_array=sqlsrv_fetch_array($queryemail);
		$emailfinished = $queryemail_array['ENG_MAIL'] . $queryemail_array['PNWORK_MAIL'] . $queryemail_array['PROD_MAIL'];
		if ($emailfinished) 
		{
			$data =  $emailfinished;
			// echo json_encode(array("mail"=>$data));
		}else{

			$data = 'NO DATA';
		    // echo json_encode($data);
		}
		echo json_encode($data);

	}

	



	function manualCheckWorkMail($param){ //ONLY PNWORK_MAIL
		include '../../connect/connect.php'; 
		$connectionInfo = array("Database"=>$dbName, "UID"=>$userName, "PWD"=> $userPassword, "MultipleActiveResultSets"=>true,"CharacterSet" => 'UTF-8');
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		$params = array();
		$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET);
		$sqlemail = "SELECT PNWORK_MAIL FROM EMAIL_MRP WHERE MRP = '".$param['MRPC']."'";
		$queryemail=sqlsrv_query($conn,$sqlemail);
		$queryemail_array=sqlsrv_fetch_array($queryemail);
		$emailsend =  $queryemail_array['PNWORK_MAIL'];
		if ($emailsend) 
		{
			$data =  $emailsend;
		}else{

			$data = 'NO DATA';
			
		}
		echo json_encode($data);
	}


	function deldetail($param)
	{	
		$sql = "DELETE FROM REQ_DRAW_HEADER
				WHERE REQNO = '".$param['REFNO']."'";
		$checkerr = ConnectManage($sql);
		
		/*$sql = "DELETE FROM REQ_DRAW_DETAIL
				WHERE REQNO = '".$param['REFNO']."'
				AND DRAWNO = '".$param['DRAWNO']."'";
		$checkerr = ConnectManage($sql);	
		$sqlh = "UPDATE REQ_DRAW_DETAIL SET SUBLINE = 0 WHERE REQNO = '".$param['REFNO']."'"; 
		ConnectManage($sqlh);
		 $countitem = count($param['DRAWNO']);		
		$Num = $param['REFNO'];
		for ($i=0; $i < $countitem; $i++) 			
			{
				$total=$param['SUBID'][$i]+1;
				$sqlt = "UPDATE REQ_DRAW_DETAIL SET SUBLINE = '$total' WHERE DRAWNO = '".$param['DRAWNO1'][$i]."'";
				ConnectManage($sqlt);
				echo $sqlt;
			} */
		
		if ($checkerr == 'error') 
		{
			
			$data['check'] = 'Error';
		}
		else
		{
			$data['check'] = 'SaveOk';
		}
		
		echo json_encode($data);
	}
	// function sqlEscape($sql1)
	// { 
	// 			$fix_str    = stripslashes($sql1); 
	// 			$fix_str    = str_replace("'","''",$sql1);  
	// 			$fix_str    = str_replace("\0","[NULL]",$fix_str); 
	// 			return $fix_str; 
	// }	
	function manualInsertDetail($Param)
	{

		$checkbox = $Param['CHECKBOX'];  //check or un check
		$data = $Param['DATA'];


		include '../../connect/connect.php'; 
		$connectionInfo = array("Database"=>$dbName, "UID"=>$userName, "PWD"=> $userPassword, "MultipleActiveResultSets"=>true,"CharacterSet" => 'UTF-8');
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		// $Param = array();
		$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET);	
		$sqlno = "SELECT IDNO  FROM ECN_HEADER ORDER BY IDNO DESC";
		$queryno=sqlsrv_query($conn,$sqlno);
		$queryidno_array =sqlsrv_fetch_array($queryno);
		$idno = $queryidno_array['IDNO']+1;
		$countcheckbox = count($Param['CHECKBOX']);
		$countdata = count($Param['DATA']);
		//$Param['WORKMAILPN'] = str_replace("'","&#39;",$Param['WORKMAIL']);
		//$Param['REMARK'] = str_replace("'","&#39;",$Param['REMARK']);
		for ($i=0; $i < $countcheckbox; $i++)
		{
			// $no =  $idno++;
			//echo $no;
			if ($Param['CHECKBOX'][$i]=='check') 
			{
					$no =  $idno++;
					$ecnno = $data[$i]['ecnno'];
					$ecnDesc = $data[$i]['ecnDesc'];
					$cdate = $data[$i]['cdate'];
					$mrpc = $data[$i]['mrpc'];
					$savemailpn = $data[$i]['savemailpn'];
					$urgent = $data[$i]['selectUrgent'];
					$remark = str_replace("'","&#39;",$data[$i]['remark']);
					$workmailsave;
					$workmailsend;
					$engmail;
					$sqlmail = "SELECT PNWORK_MAIL,PROD_MAIL,ENG_MAIL FROM EMAIL_MRP WHERE MRP = '".$mrpc."'";            //MAIL PN_WORK SAVE WORKMAIL SEND PN+PROD
					$querymail=sqlsrv_query($conn,$sqlmail);
					$querymail_array =sqlsrv_fetch_array($querymail);
					$engmail = $querymail_array['ENG_MAIL'];     // ENGMAIL
					if($savemailpn === 'Send'){
						$workmailsave = $querymail_array['PNWORK_MAIL'];   //SAVEMAIL
						$workmailsend = $querymail_array['PNWORK_MAIL']." ".$querymail_array['PROD_MAIL']; //SENDMAIL
						//echo "Send---------------------------". $workmailsave;
					}else{
						$workmailsave = NULL;
						$workmailsend = NULL;
						//echo "UNSend---------------------------". $workmailsave;
					}
					$sql="INSERT ECN_HEADER (IDNO,ECNNO,ECNDESC,ECNDATE,MRPC,CBY,CDATE,ENGMAIL,REMARK,WORKMAIL,URGENT,STATUS";
					$sql.=") VALUES ('$no','".$ecnno."',
					'".$ecnDesc."',
					'".$cdate."',
					'".$mrpc."',	
					'".$_SESSION['user']."',
					GETDATE(),";
			        if($engmail != NULL) $sql.="'".$engmail."'";
					else $sql.="NULL";
					$remark = str_replace("'","&#39;",$data[$i]['remark']);
			        if($remark!= NULL) $sql.=",'$remark'";
					else $sql.=",NULL";
			        if($workmailsave!=NULL) $sql.=",'".$workmailsave."'";
					else $sql.=",NULL";
					$urgent = $data[$i]['selectUrgent'];
			        if($urgent=='Y') $sql.=",'$urgent'";
					else $sql.=",'N'";
					$sql.=",'Engineer Create'";
					$sql.=")";

					$checkerr = ConnectManage($sql);
					if ($checkerr == 'error') 
					{
						$datas['check'] = 'Error';
					}
					else
					{
						$datas['check'] = 'SaveOk';
					}
	


					$send =  explode(" ",$workmailsend);    //EMAIL TO SEND PN+PROD
					//send mail
					if($workmailsend !== NULL)  
					 {
					  //echo "ARRAY1===>" . json_encode($data[$i]['sendmail'][$i]);
					  require_once('../mail/class.phpmailer.php');
					  //$Param['WORKMAIL'][] = str_replace("''","&#39;",$Param['WORKMAIL'][0]);
					  $mail = new PHPMailer();
					  $mail->IsHTML(true);
					  $mail->IsSMTP();
					  $mail->SMTPAuth = true; // enable SMTP authentication
					  $mail->SMTPSecure = ""; // sets the prefix to the servier
					  $mail->Host = "172.18.65.1"; // sets GMAIL as the SMTP server
					  $mail->Port = 25; // set the SMTP port for the GMAIL server
					  $mail->Username = "webmaster@thai.sodick"; // GMAIL username
					  $mail->Password = "ST.2016."; // GMAIL password
					  $mail->From = "webmaster@thai.sodick"; // "name@yourdomain.com";
					  $mail->AddReplyTo = "support@thaicreate.com"; // Reply
					  $mail->FromName = "WEBMASTER@THAI.SODICK";  // set from Name
					  $mail->Subject = "Engineering Create ECN ".$data[$i]['ecnno'];  
					  $mail->Body = "Engineering Create ECN no. ".$data[$i]['ecnno']." will has accepted by Planning Work Please CLICK link for detail --> http://stsvr3.thai.sodick/CENTER/ECN/search.php?per_search=50&v1=".$data[$i]['ecnno']."&v2=Engineer%20Create&v3=&v4=&v5=&vmailpn=&vmailck=  <br><br><br>Best regards,<br>".$_SESSION['user'];"</b>";	
					  
					   //SENDMAIL TEST EDP
					    //  $mailtestEDP = ['varoon@thai.sodick','piyawat@thai.sodick','pissanu@thai.sodick','KUNSIYA@THAI.SODICK'];
					    //  $countitems = count($mailtestEDP);
					    // for ($j=0; $j < $countitems; $j++)
					    // {
						//   $recipients = array($mailtestEDP[$j]); 
					    //   foreach($recipients as $email)
						//   {
						// 	 $mail->AddAddress($email);
						//   }
						// }
						



					//   SEND MAIL
					  $countitems = count($send);
					  for ($j=0; $j < $countitems; $j++)
					  {
						  $recipients = array($send[$j]); 
					  foreach($recipients as $email)
						  {
							 $mail->AddAddress($email);
						  }
					  }
					  //$mail->AddAddress ("KUNSIYA@THAI.SODICK");	
					  $mail->addCC ("WEBMASTER@THAI.SODICK");	
					  //$mail->addCC ("piyawat@thai.sodick");	
					  /*$mail->AddAttachment("E:/WEB/upload/PUR_DRAW/".$param['MATERIAL'].'.pdf');*/
					  $mail->set('X-Priority', '1'); //Priority 1 = High, 3 = Normal, 5 = low
					  $mail->Send();
					  //echo var_dump ($mail);
					 }			 
			}
		}

	 echo json_encode($datas);
	}
	/*------------------------------------index----------------------------------------*/
	
	function manualSelectDetail($Param){
		include '../../connect/connect.php'; 
		$connectionInfo = array("Database"=>$dbName, "UID"=>$userName, "PWD"=> $userPassword, "MultipleActiveResultSets"=>true,"CharacterSet" => 'UTF-8');
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		$params = array();
		$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET);
		$strSQL = "SELECT PARENT,PDESC,TYPE,OLDQTY,NEWQTY,OLDITEM,OLDDESC,NEWITEM,NEWDESC,OLDSORT,NEWSORT,OLDMRP,NEWMRP,OLDSUP,NEWSUP,VALIDFROM 
					 FROM VW_ECN_DETAIL
					 WHERE ECN = '".$Param['ECNNO']."'
					 AND MRPC = '".$Param['MRPC']."'";
		$objQuery = sqlsrv_query($conn,$strSQL);	
		$datadetail = array();

		while($objResult1=sqlsrv_fetch_array($objQuery))									
			{
			$ww = $objResult1['PARENT'];
			$rr = $objResult1['PDESC'];
			$type = $objResult1['TYPE'];
			$oldqty = $objResult1['OLDQTY'];
			$newqty = $objResult1['NEWQTY'];
			$olditem = $objResult1['OLDITEM'];
			$olddesc = $objResult1['OLDDESC'];
			$newitem = $objResult1['NEWITEM'];
			$newdesc = $objResult1['NEWDESC']; 
			$oldsort = $objResult1['OLDSORT'];
			$newsort = $objResult1['NEWSORT'];
			$oldmrpc = $objResult1['OLDMRP'];
			$newmrpc = $objResult1['NEWMRP'];
			$oldsup = $objResult1['OLDSUP'];
			$newsup = $objResult1['NEWSUP'];
			$valid = $objResult1['VALIDFROM'];
            $datevalid =  date_format($valid, 'd/m/Y');
			array_push($datadetail,$ww,$rr,$type,$oldqty,$newqty,$olditem,$olddesc,$newitem,$newdesc,$oldsort,$newsort,$oldmrpc,$newmrpc,$oldsup,$newsup,$datevalid );
				}
			echo json_encode($datadetail);

	}
	function manualUpdateDetail($Param)	
	{ 

		$data = $Param['DATA'];
		include '../../connect/connect.php'; 
		$connectionInfo = array("Database"=>$dbName, "UID"=>$userName, "PWD"=> $userPassword, "MultipleActiveResultSets"=>true,"CharacterSet" => 'UTF-8');
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		// $Param = array();
		$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET);
		$countdata = count($Param['DATA']);

		for ($i=0; $i < $countdata; $i++)
		{
			$idno = $data[$i]['idno'];
			$ecnno = $data[$i]['ecnno'];
			$mrpc = $data[$i]['mrpc'];
			$re;
			$remark = str_replace("'","&#39;",$data[$i]['remark']);
			if($remark ==NULL){
				$re = $remark;
			}else{
				$re = $remark;
			}

            $a;
			$selectUrgent = $data[$i]['selectUrgent'];
			if($selectUrgent == 'Y'){
				$a = $selectUrgent;
			}else{
				$a = 'N';
			}
			$savemailpn = $data[$i]['savemailpn'];
			$workmailsave;
			$workmailsend;
			$sqlmail = "SELECT PNWORK_MAIL,PROD_MAIL FROM EMAIL_MRP WHERE MRP = '".$mrpc."'"; //MAIL PN_WORK SAVE WORKMAIL SEND PN+PROD
			$querymail=sqlsrv_query($conn,$sqlmail);
			$querymail_array =sqlsrv_fetch_array($querymail);
			if($savemailpn === 'Send'){
				$workmailsave = $querymail_array['PNWORK_MAIL'];        //SAVEMAIL
				$workmailsend = $querymail_array['PNWORK_MAIL']." ".$querymail_array['PROD_MAIL']; //SENDMAIL
			}else{
				$workmailsave = null;
			    $workmailsend= null;
			}
			


			if($workmailsave!=NULL)$sql = "UPDATE ECN_HEADER SET WORKMAIL='$workmailsave',REMARK = '$re',URGENT ='$a',CBY ='".$_SESSION['user']."',CDATE = GETDATE() WHERE IDNO = '$idno'";
			else $sql = "UPDATE ECN_HEADER SET WORKMAIL=NULL ,REMARK = '$re',URGENT ='$a',CBY ='".$_SESSION['user']."',CDATE = GETDATE() WHERE IDNO = '$idno'";

			$checkerr = ConnectManage($sql);
			if ($checkerr == 'error') 
			{
				$data['check'] = 'Error';
			}
			else
			{
				$data['check'] = 'SaveOk';
			}

			$send =  explode(" ",$workmailsend);    //EMAIL TO SEND PN+PROD
			// echo  "SENDMAIL++++". $workmailsend;
            //SEND MAIL
			if($workmailsend !== NULL)
			{
			require_once('../mail/class.phpmailer.php');
		  	$mail = new PHPMailer();
			$mail->IsHTML(true);
			$mail->IsSMTP();
			$mail->SMTPAuth = true; // enable SMTP authentication
			$mail->SMTPSecure = ""; // sets the prefix to the servier
		 	$mail->Host = "172.18.65.1"; // sets GMAIL as the SMTP server
			$mail->Port = 25; // set the SMTP port for the GMAIL server
			$mail->Username = "webmaster@thai.sodick"; // GMAIL username
			$mail->Password = "ST.2016."; // GMAIL password
			$mail->From = "webmaster@thai.sodick"; // "name@yourdomain.com";
			$mail->AddReplyTo = "support@thaicreate.com"; // Reply
		 	$mail->FromName = "WEBMASTER@THAI.SODICK";  // set from Name
			$mail->Subject = "Engineering Create ECN ".$ecnno;  
			$mail->Body = "Engineering Create ECN no. ".$ecnno." will has accepted by Planning Work  Please CLICK link for detail --> http://stsvr3.thai.sodick/CENTER/ECN/search.php?per_search=50&v1=".$ecnno."&v2=Engineer%20Create&v3=&v4=&v5=&vmailpn=&vmailck=  <br><br><br>Best regards,<br>".$_SESSION['user'];"</b>";	
			//$mail->Body = "Originator Create Request Drawing Copy number '$total' http://stsvr3.thai.sodick/CENTER/ENG_REQ_DRAW/edit_req_drawing.php?EDIT='$total' <br>Best regards,".$_SESSION['user'];"</b>";	
			
			//SENDMAIL TEST EDP
			$mailtestEDP = ['varoon@thai.sodick','piyawat@thai.sodick','pissanu@thai.sodick','KUNSIYA@THAI.SODICK'];
			$countitems = count($mailtestEDP);
			for ($j=0; $j < $countitems; $j++)
				{
					$recipients = array($mailtestEDP[$j]); 
						foreach($recipients as $email)
							{
								$mail->AddAddress($email);
							}
			    }
			
					// //   SEND MAIL
					// //   $countitems = count($send);
					// //   for ($j=0; $j < $countitems; $j++)
					// //   {
					// // 	  $recipients = array($send[$j]); 
					// //   foreach($recipients as $email)
					// // 	  {
					// // 		 $mail->AddAddress($email);
					// // 	  }
					// //   }


			//$mail->AddAddress("pissanu@thai.sodick");
			//$mail->AddAddress("Phisarn@thai.sodick");	
			$mail->addCC ("WEBMASTER@THAI.SODICK");	
			//$mail->addCC ("piyawat@thai.sodick");	
			/*$mail->AddAttachment("E:/WEB/upload/PUR_DRAW/".$param['MATERIAL'].'.pdf');*/
			$mail->set('X-Priority', '1'); //Priority 1 = High, 3 = Normal, 5 = low
			$mail->Send();
			//echo var_dump ($mail);
			
			}  

		}

		echo json_encode($data);			
	} 




?>