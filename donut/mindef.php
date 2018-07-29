
<?php

$HTMLpage = new DOMDocument();
$pageStart = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<style>
	body {
    font: normal 10px "Trebuchet MS";
	}
	</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>MINDEF FILE UPLOAD</title>
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.17/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.17/jquery-ui.min.js"></script>

</head>
<body>
HELLO!
<div class="container">  
<table class="table table-hover" >
  <thead>
    <tr>
      <th scope="col">Description</th>
    </tr>
  </thead>
  <tbody id="loglist">

  </tbody>
</table>
</div>

</body>
</html>';

//$(document).ready(function(e) {
	
	$HTMLpage->loadHTML("<html><body><i>Test</i><br><div>Text</div></body></html>");
	
	
	$HTMLpage->loadHTML($pageStart); //run this first to initiate the jQuery libs
	$HTMLpage->saveHTML();
	
	set_include_path('lib/phpseclib1.0.11');
	include('Net/SFTP.php');


	
	//NOTE : Please change the default timezone to date.timezone = "Asia/Kuala_Lumpur" in php.ini
    $uploadDirectory = "uploads/" . date('Ymd'); // 'Do not include a trailing slash  "/" at the end of the folder name' 
    $errors = []; // Store all foreseen and unforseen errors here
    $fileExtensions = ['jpeg','jpg','png','txt','GFM']; // Get all the file extensions

    $fileName = $_FILES['myfile']['name'];
    $fileSize = $_FILES['myfile']['size'];
    $fileTmpName  = $_FILES['myfile']['tmp_name'];
    $fileType = $_FILES['myfile']['type'];
    $fileExtension = strtolower(end(explode('.',$fileName)));

    $uploadPath =  $uploadDirectory . "/" . basename($fileName); //$currentDir .

    if (isset($_POST['submit'])) {
        if (! in_array($fileExtension,$fileExtensions)) {
            $errors[] = "This file extension is not allowed. Sorry, I can't do that <br>";
        }

        if ($fileSize > 20000000) {
            $errors[] = "This file is more than 20MB. Sorry, it has to be less than or equal to 20MB";
        }

        if (empty($errors)) {
			
		if (!file_exists($uploadDirectory)){
			addLog("'Directory  ' . $uploadDirectory .' not found. Auto-creating now");
			echo 'Directory  ' . $uploadDirectory .' not found. Auto-creating now <br><br> ';
			mkdir($uploadDirectory, 0777, true); //mkdir cannot have a trailing slash "/"
		}
            $didUpload = move_uploaded_file($fileTmpName, $uploadPath);

            if ($didUpload) {
                addLog("The file " . basename($fileName) . " has been uploaded <br>");
				
				//Check any existing file within the same folder and get the file count to get the Sequence Number
				
				$GFMfilecount = 1;
				
				$files = scandir($uploadDirectory);
				foreach($files as $file) {
					$file_parts = pathinfo($file);
					switch($file_parts['extension'])
					{
						case "GFM":
						++$GFMfilecount;
						break;

						//case "GPG":
						//break;

						case "": // Handle file extension for files ending in '.'
						case NULL: // Handle no file extension
						break;
					}
				}
				
				$GFMsequence = str_pad($GFMfilecount, 3, '0', STR_PAD_LEFT);
				$GFMfilename = "1106AP504200004" . date("Ymd") . $GFMsequence . ".GFM";
				$GPGfilename = "1106AP504200004" . date("Ymd") . $GFMsequence . ".GPG";
				$filepathGFM = $uploadDirectory . "/" . $GFMfilename;
				$filepathGPG = $uploadDirectory . "/" . $GPGfilename;
				
				echo "<br>GFM Filename that will be generated : " . $filepathGFM . "<br><br>";
				
				//Creating the new blank file in GFM file extenstion
				$writeFile = fopen($filepathGFM,"wb",1) or die ('unable to open file!'); //"uploads/1106AP504200004" . date('Ymd') . "001.txt" ,"wb",1
				echo "AP504 GFM File created <br><br>";
				
				//Read the file line-by-line in memory.
				$isFirst = true;
				foreach (file($uploadPath) as $line) {
					//Header has different file format
					if ($isFirst) {
						$isFirst = false;

						$firstline = substr($line, 0, 1)
						. "20"
						. substr($line, 1, 4)
						. "1106"
						. substr($line, 8, 8)
						. substr($line, 16, 15) //need to add replacement function
						. substr($line, 31, 6)
						. substr($line, 37, 6)
						. substr($line, 44, 13)
						. substr($line, 57, 8)
						. "                "
						. PHP_EOL;
						
						fwrite($writeFile, $firstline);
						//$(".accordion").append("<h2> MINDEF FILES " . date('Ymd') . " </h2> <p> "  . $firstline . "<br>"); //echo $firstline . '<br>';
						continue;
					} 
					
					$pukalstring = substr ($line,0,1) 
					. "20" 
					. substr($line, 1, 4)
					. "1106"
					. substr($line, 8, 8)
					. substr($line, 16, 12)
					. "     "
					. substr($line, 28, 3)
					. substr($line, 31, 15)
					. "00"
					. substr($line, 61, 11)
					. "B02"
					. substr($line, 72, 5)
					. PHP_EOL;
					
					fwrite($writeFile, $pukalstring);
					//$(".accordion").append($pukalstring . "<br>");//echo $firstline . '<br>';
					//echo $pukalstring . "<br>";
				}
				//$(".accordion").append("</p>"); //To close of the jquery HTML Tag for accordion
				fclose($writeFile);
				
				//Auto-encyrpting the file into GPG format
				$strCommand = "gpg -r prd1gfmas -o " . $filepathGPG ." --always-trust -e " . $filepathGFM;
				exec($strCommand);
				echo ' <br> Encryption Successful <br>' . $strCommand . '<br><br>';
				
			/* 	//Auto-upload the file into 1GFMAS SFTP Server (PETRONAS Public IP need to be whitelisted)
				$sftp = new Net_SFTP("10.14.19.240","22");
				if (!$sftp->login('lmsftpbtd', 'lmsftpbtd@1')){
					exit(' <br> Login Failed  <br>');
				} else {
					echo ' <br> SFTP Login Success! <br>';
					
					$SFTPuploadpath = "/SFSFOLDER/" . $GPGfilename;
					echo $SFTPuploadpath;
					
					if (!$sftp->put($SFTPuploadpath, $filepathGPG, NET_SFTP_LOCAL_FILE)){
						exit(' <br> Upload Failed  <br>');
					} else {
						//Double check at SFTP folder to confirm whether the file is uploaded or not
						
						echo ' <br> Upload Success <br>';
						print_r($sftp->size($SFTPuploadpath));
					}
					echo '<br>';
				} */
            } else {
                echo "An error occurred somewhere... I cannot find it. Can you?";
            }
        } else {
            foreach ($errors as $error) {
                echo $error . "These are the errors:" . "\n";
            }
        }
    }
	
function addLog($desc){
	exit();
	//HTML ui hacking
	$dom = new DOMDocument(); 
	$contentHTMLString = file_get_contents("mindef.php");
	$dom->loadHTML($contentHTMLString);
	
	//get the element you want to append to
	$rowList = $HTMLpage->getElementById("loglist"); 
	
	echo $rowList;

	$tr = $dom->createElement('tr');
	$rowList->appendChild($tr);
	//$th = $dom->createElement('th','Cake');
	//$rowList->appendChild($th);
	$td1 = $dom->createElement('td',$desc);
	$rowList->appendChild($td1);
	//$td2 = $dom->createElement('td',$td2);
	//$rowList->appendChild($td2);
	
	echo $dom->saveHTML();
}

?>