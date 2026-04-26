<?php

	include('Config.php');
	
	$books = mysqli_query($con,'select * from Book');
	echo '<table border=1>';
	echo '<tr><th>ISBN</th><th>Title</th></tr>';
	while($res = mysqli_fetch_array($books))
	{
		echo '<tr>';
		//echo $res['ISBN'].' '.$res['Title'].'<br/>';
		echo '<td>'.$res['ISBN'].'</td><td>'.$res['Title'].'</td></tr>';
	}
	echo '</table>';

?>