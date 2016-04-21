<!DOCTYPE html>
<html>
<head>
<title>chat</title>
<link type="text/css" rel="stylesheet" href="style.css" />
</head>

<?php

//starting the session by providing info about the username, database name and the server name
$username= "root";
$password = "pooja";
$database = "chatsystem";
$server = "127.0.0.1";

//Connecting to mysql database using the above defined parameters
$db_handle = mysql_connect($server, $username, $password);
$db_found = mysql_select_db($database , $db_handle);
if ($db_found) 
{
	echo 'connected';
}
else
{
	echo'not connected';
}

//When the database is connected the session starts
session_start();

if(isset($_GET['logout']))
{
	$fp = fopen("log.html" , 'a');
    fwrite($fp, "<div class='msgln'><i>User ". $_SESSION['name'] ." has left the chat session.</i><br></div>");
    fclose($fp);

	//when user logged out, database changes the session column to "no" for that user
	$dbname=$_SESSION['name'];
	$no="no";
	$close="UPDATE info SET
			session = 'no'
			WHERE name = '".$_SESSION['name']."'";
			
	$close1=mysql_query($close);
					
	mysql_close($db_handle);

	session_destroy();

	header("Location: index.php"); //Redirect the user
}


//login form to enter username and password
function loginForm(){
echo'
<div id="loginform">
<form action="index.php" method="post">
<label for="name">Name:</label>
<input type="text" name="name" id="name" placeholder="Enter Name" />
<label for="password">Password:</label>
<input type="password" name="password" id="password" placeholder="Enter Password" />
<input type="submit" name="enter" id="enter" value="Enter" />
</form>
</div>
';
}

//if username and password not empty, then remove special characters, else enter the name.
if(isset($_POST['enter']))
{
	if($_POST['name']!= "" && $_POST['password']!="")
	{
		$_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));
		$_SESSION['password'] = stripslashes(htmlspecialchars($_POST['password']));
		$checkname=$_SESSION['name'];
		$checkpass=$_SESSION['password'];
		$i=0;
		
		//Authentication:checking if the user entered password is same as the one in the database, If not error message is displayed
		$query1 = mysql_query("SELECT name,password FROM info");
		
			
		while ($row1= mysql_fetch_assoc($query1))
		{
			$databasename = $row1['name'];
			$databasepassword =$row1['password'];
			
			if($databasename==$checkname && $databasepassword!=$checkpass)
			{
				loginform();
				echo' <span class ="error">WRONG PASSWORD, PLEASE ENTER CORRECT PASSWORD AGAIN</span>';
				return 0;
			}
			if($databasename==$checkname)
			{
				break;
			}
			
			$i++;
		}
		$numrows1 = mysql_num_rows($query1);
		
		//error condition if user does not exist in the database
		if($i==$numrows1)
		{
			echo '<span class ="error">user does not exist in database</span>';
			loginform();
			return;
		}
		
	}
	else
	{
		echo '<span class="error">Please enter both name and password</span>';
	}
}
?>


<?php

	if(!isset($_SESSION['name'] ) || !isset($_SESSION['password']))
	{
		loginForm();
	}
	else 
	{
		//check if the user has already logged in, If yes, then display error message that user already exists
		$i=0;
		$no="no";
		$yes="yes";
		$uname=$_SESSION['name'];
		$pword=$_SESSION['password'];
		$query = mysql_query("SELECT name,password,session FROM info");
		
		$numrows = mysql_num_rows($query);
		
		if($db_found)
		{
			while ($row = mysql_fetch_assoc($query))
			{
				$dbname = $row['name'];
				$dbpassword =$row['password'];
				$dbsession=$row['session'];
				
				if($uname==$dbname && $yes==$dbsession )
				{
					loginform();
					echo ' already exists';
					return 0;
				}
				//Set the session column in the database to "yes"
				else 
				{
					$change="UPDATE info SET
					session = 'yes' 
					WHERE name = '".$_SESSION['name']."'";
					mysql_query($change);
					
				}	
			}
		}
	?>
<br><br>	

		<div id ="list">
		<ul id = "friendlist">
		<li id="pooja">pooja</li>
		<li id="rohith">rohith</li>
		<li id="ravi">ravils</li>
		<li id="geetha">getha</li>
		</ul>
		</div>
	
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js">
</script>

<script type="text/javascript">

$(document).ready(function()
{
	setInterval (loadLog, 2500);
	
	//When clicked on a friend creating the wrapper dynamically and appending all the elements to it
	$("li").click(function() 
	{ 
		//if friend selected is same as the logged in person, alerts the user.
		var name="<?php echo $_SESSION['name'];?>";
		username= $(this).text();
		if(username==name)
		{
			alert("cannot chat with same person");
			return;
		}
	
		$('#wrapper').remove();
		var whole = document.createElement("div");
		whole.id="wrapper";
		document.getElementById("friendlist").appendChild(whole);
	
		$('#wrapper').append('<p class="welcome"><b>WELCOME</b></p>');
		$('#wrapper').append('<input type="text" name="login" id="login" value="<?php echo $_SESSION['name'];?>" />');
		$('#wrapper').append('<p class="logout"><a id="exit" href="#">Exit Chat</a></p>');
		$('#wrapper').append('<div style="clear:both></div>');
		$('#wrapper').append('<div id="chatbox"></div>');
		$('#wrapper').append('<form name="message" action="">');
		$('#wrapper').append('<input name="usermsg" type="text" id="usermsg" size="63" />');
		$('#wrapper').append('<input name="submitmsg" type="submit"  id="submitmsg" value="Send" />');
	
		
		$('#talkingto').remove();
		$('#wrapper').append('<li id =talkingto>Chatting with '+username+'</li>');
		
		//Confirmation box when user clicks exit chat
		$("#exit").click(function()
		{
			var exit = confirm("Are you sure you want to end the session?");
			if(exit==true)
			{	
				window.location = 'index.php?logout=true';
			}
		});
	 
		//On click of send button, the text and the username is sent to post.php file
		$("#submitmsg").click(function(e)
		{
			var clientmsg = $("#usermsg").val();
			$.post("post.php", {text:clientmsg,username:username});
			$("#usermsg").attr("value", "");
			return false;
		});
	});
   
  
	function loadLog()
	{
		var oldscrollHeight = $("#chatbox").attr("scrollHeight") - 20;
		var loggedinuser = document.getElementById('login').value;
	
		loggedinuser.trim();
		username.trim();
		
		//creating log files for each pair of friends
		if((username=="rohith"  && loggedinuser=="pooja") || (username=="pooja" && loggedinuser=="rohith"))
		{
			filename="pooja-rohith";
		}
				
		if((username=="ravils"  && loggedinuser=="pooja") || (username=="pooja" && loggedinuser=="ravils"))
		{
			filename="pooja-ravils";
		}
	
		if((username=="getha"  && loggedinuser=="pooja") || (username=="pooja" && loggedinuser=="getha"))
		{
			filename="pooja-getha";
		}
				
		if((username=="ravils"  && loggedinuser=="rohith") || (username=="rohith" && loggedinuser=="ravils"))
		{
			filename="rohith-ravils";
		}
				
		if((username=="getha"  && loggedinuser=="rohith") || (username=="rohith" && loggedinuser=="getha"))
		{
			filename="rohith-getha";
		}

		if((username=="getha"  && loggedinuser=="ravils") || (username==="ravils" && loggedinuser=="getha"))
		{
			filename="ravils-getha";
		}
		
		//writes the text entered by the user to the chat box
		$.ajax({url:filename + ".html",
				cache: false,
				success: function(html)
				{
					$("#chatbox").html(html);
					var newscrollHeight = $("#chatbox").attr("scrollHeight") - 20;
					if(newscrollHeight > oldscrollHeight){
                    $("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); 
                }
            },
		});
	}
});
</script>
<?php
}
?>
</body>
</html>