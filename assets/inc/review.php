<?php
	require_once "./config.php";
	session_start();
	if(!empty($_POST))
	{
		// del
		if($_POST["yn"] == "-1")
		{
			mysqli_query($link, "delete from ".$review_table." where stu_num = '".$_SESSION["user"]."' and res = '".$_POST["res"]."'");
			if(mysqli_affected_rows($link) > 0)	echo "s";
			else echo "n";
		}
		else
		{
			$text = mysqli_real_escape_string($link, $_POST["text"]);
			
			mysqli_query($link, "update ".$review_table." set comment = '".$text."', ".$review_table." = '".$_POST["yn"]."' where stu_num = '".$_SESSION["user"]."' and res = '".$_POST["res"]."'");
			if(mysqli_affected_rows($link) > 0)	echo "s";
			else
			{
				mysqli_query($link, "insert into ".$review_table." values(null, '".$_SESSION["user"]."', '".$_POST["res"]."', '".$_POST["yn"]."', '".$text."')");
				if(mysqli_insert_id($link) > -1)	echo "s";
				else echo "error";
			}
		}
	}
	mysqli_close($link);
?>