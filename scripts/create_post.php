<?php
	error_reporting(0);

	session_start();
	
	if (isset($_POST['post']) && !empty($_POST['post']))
	{
		$post=$_POST['post'];
		$u_id=$_SESSION['u_id'];
		date_default_timezone_set('America/New_York');
		$post_time=date('Y-m-d H:i:s');
		$repost_id=-1;

		//Connect To Database
		require_once __dir__ . '/db_connect.php';
		$mysqli = connect();
		
		//sanitize input
		$post = $mysqli->real_escape_string(trim($post));
		
		$query ="INSERT INTO post(u_id, repost_id, message, post_time) VALUES('$u_id', '$repost_id', '$post', '$post_time')";
		$result=$mysqli->query($query);
		$mysqli->close();
		if($result === true)
		{
			header('location:../home.php?success=1');
		}
		else 
		{
			header('location:../home.php?error=query_fail');
		}
	}
	else 
	{
		header('location:../home.php?error=empty_fields');
	}






?>