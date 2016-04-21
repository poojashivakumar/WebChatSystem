<?php
session_start();
if(isset($_SESSION['name']))
{
	$text = $_POST['text'];    //getting the test entered by the user
	$username=$_POST['username'];//getting the name of the friend that is being clicked
	$newusername= trim($username);
	$user=$_SESSION['name'];// assigning the session name  of the person logged in
	
    //Assigning each pair a filename to write logs
	if(($newusername=="pooja" && $user=="rohith") || ($newusername=="rohith" && $user=="pooja"))
	{
		$filename="pooja-rohith";
	}
   
	if(($newusername=="pooja" && $user=="ravils") || ($newusername=="ravils" && $user=="pooja"))
	{
		$filename="pooja-ravils";
	}
   
	if(($newusername=="pooja" && $user=="getha") || ($newusername=="getha" && $user=="pooja"))
	{
		$filename="pooja-getha";
	}
   
	if(($newusername=="rohith" && $user=="ravils") || ($newusername=="ravils" && $user=="rohith"))
	{
		$filename="rohith-ravils";
	}
   
	if(($newusername=="rohith" && $user=="getha") || ($newusername=="getha" && $user=="rohith"))
	{
		$filename="rohith-getha";
	}
   
    if(($newusername=="ravils" && $user=="getha") || ($newusername=="getha" && $user=="ravils"))
	{
		$filename="ravils-getha";
	}
   
	//opening the respective pair's filename and appending the logs
	$fp = fopen($filename. '.html', 'a');
	fwrite($fp, "<div class='msgln'>(".date("g:i A").") <b>".$_SESSION['name']."</b>: ".stripslashes(htmlspecialchars($text))."<br></div>");
	fclose($fp);
}
?>