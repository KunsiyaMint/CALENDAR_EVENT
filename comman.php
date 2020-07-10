<?php 
	require('../comman.php');
	// require('search_comman.php');
	require('manual_comman.php');

	
	/*------------------------------------standartGetdata----------------------------------------*/

	$link = $_SERVER['PHP_SELF'];
    $link_array = explode('/',$link);
    $page = end($link_array);

	function getWorkcenter()
	{
		$sql = "SELECT *
  				FROM WORKCENTER";
		$data = ConnectQuery($sql);
		return $data;
	}

	function GetStatusSDO($userID)
	{
		$sql = "SELECT *
  				FROM CENTER_HEAD
  				WHERE userID = '".$userID."'
  				AND menuID = '1'";
		$data = ConnectQuery($sql);
		return $data;
	}

	function GetVender()
	{
		$sql = "SELECT *
  				FROM SAP_VENDOR";
		$data = ConnectQuery($sql);
		return $data;
	}

	function Getcarrier($wc)
	{
		
		$sql = "SELECT us.*,wc.DESCRIPTION
  				FROM CENTER_USER AS us
  				LEFT JOIN WORKCENTER AS wc ON wc.WCCODE = us.MRPC
  				WHERE us.Userlevel = 'carrier'
  				AND us.MRPC = '".$wc."'";
		$data = ConnectQuery($sql);
		return $data;
	}

	function GetuserBywc($wc)
	{
		
		$sql = "SELECT us.*,wc.DESCRIPTION
  				FROM CENTER_USER AS us
  				LEFT JOIN WORKCENTER AS wc ON wc.WCCODE = us.MRPC
  				WHERE us.MRPC = '".$wc."'
  				AND us.ACTIVE = '1'";
		$data = ConnectQuery($sql);
		return $data;
	}

	function GetManager($wct)
	{
		$sql = "SELECT *
  				FROM CENTER_USER
  				WHERE  MRPC = '".$wct."'
  				AND UserLevel = 'manager'
  				OR MRPC = '".$wct."'
  				AND UserLevel = 'admin'";
		$data = ConnectQuery($sql);
		return $data;
	}	
	function GetManagerByMail($mail)
	{

		$sql = "SELECT MRPC
  				FROM CENTER_USER
  				WHERE MAIL = '".$mail."'";
		$data = ConnectQuery($sql);

		$sql1 = "SELECT *
  				FROM CENTER_USER
  				WHERE MRPC = '".$data[0]['MRPC']."'
  				AND UserLevel = 'manager'
  				OR MRPC = '".$data[0]['MRPC']."'
  				AND UserLevel = 'admin'";
		$data1 = ConnectQuery($sql1);
		return $data1;
	}

	function GetManagerJson($wct)
	{
		$sql = "SELECT *
  				FROM CENTER_USER
  				WHERE MRPC = '".$wct."'
  				AND UserLevel = 'manager'
  				OR MRPC = '".$wct."'
  				AND UserLevel = 'admin'";
		$data = ConnectQuery($sql);
		echo json_encode($data);
	}

	function getwctfrommail($mail)
	{
		$sql = "SELECT *
  				FROM CENTER_USER
  				WHERE MAIL = '".$mail."'
  				AND UserLevel = 'manager'
  				OR MAIL = '".$mail."'
  				AND UserLevel = 'admin'";
		$data = ConnectQuery($sql);
		return $data;
	}

	function getTime()
	{
		$sql = "SELECT TOP (1) CONVERT(VARCHAR(10), GETDATE(), 108) AS timenow
				FROM SDO_TEMPLATE";
		$data = ConnectQuery($sql);
		return $data;
	}

	function getEndloop($item,$user,$status)
	{
		$sql = "SELECT SDO_HEADER.SDONO
				FROM SDO_HEADER
				LEFT JOIN SDO_DETAIL ON SDO_DETAIL.SDONO = SDO_HEADER.SDONO
				WHERE SDO_DETAIL.ITEM = '".$item."'
				AND SDO_HEADER.CARRYBY = '".$user."'
				AND SDO_HEADER.HSTATUS = '".$status."'";
		$data = ConnectQuery($sql);
		return $data;
	}

	function checkstring($string)
	{
		if (strpos($string, "'") !== false) {
		    $check = 'yes';
		}else{
			$check = 'no';
		}
		return $check;
	}

	/*------------------------------------standartGetdata----------------------------------------*/

	/*------------------------------------function post----------------------------------------*/

	$checkfuction = "";

	if(isset($_POST['checkfuction'])){
		$checkfuction = $_POST['checkfuction'];
	}

	if ($checkfuction == 'CheckLogin') {
		$Param['Username'] = $_POST['username'];
		$Param['Password'] = $_POST['password'];
		CheckLogin($Param);
	}

	if ($checkfuction == 'Logout') {
		session_destroy();
		header("Location: /SDO/login_1.php");
	}

	if ($checkfuction == 'Getvendorlist') {
		Getvendorlist($_POST);
	}

	/*------------------------------------function post----------------------------------------*/

	
?>