<?php

	include('Config.php');
	
	$sql = "insert into Student (name,email,password,university,major,dateOfBirth) values ('$_POST[name]','$_POST[email]','$_POST[password]','$_POST[university]','$_POST[major]','$_POST[dateOfBirth]')";
	
	if (mysqli_query($con,$sql))
	{
		header("Location: student-login.php");
	}
	else
		echo mysqli_error($con);
	

?>