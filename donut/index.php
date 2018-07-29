<?php
set_include_path('lib/phpseclib1.0.11');

include('Math/BigInteger.php');
include('Crypt/Random.php');
include('Crypt/Hash.php');
include('Crypt/Base.php');
//include('GPG.php');
  if(!empty($_FILES['uploaded_file'])){
    $folder = "uploads/" . date('Ymd') . '/';
	
	if (!file_exists($folder)){
		mkdir ($folder);
	}
	
    $path = $folder . basename( $_FILES['uploaded_file']['name']);
	
	
	
 echo $path;
	
    if(move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $path)) {
      echo "The file has been uploaded : ". $path . '<br><br>' ;
		header("Location: index.php");
		exit();
    } else{
        echo "There was an error uploading the file, please try again!";
    }
  }
  
  
	echo ($_FILES['uploaded_file']['tmp_name']);
	echo 'Yalla.php';

$filenameGFM = $folder . "1106AP504200004" . date("Ymd001") . ".GFM";
$filenameGPG = $folder . "1106AP504200004" . date("Ymd001") . ".GPG";
	
/* echo file_exists($path);
$filedata = function() {
    $file = fopen($path, 'wb',1); //__DIR__ . 

    if (!$file)
        die('file does not exist or cannot be opened');

    while (($line = fgets($file)) !== false) {
        yield $line;
    }
    fclose($file);
};
 */
/* $writeFile = fopen('uploads/Hahahaha.txt' ,"wb",1) or die ('unable to open file!');

$isFirst = true;
echo $path;
foreach (file($path) as $line) {
	
	 if ($isFirst) {
        $isFirst = false;

		$firstoline = substr($line, 0, 1)
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
		
		fwrite($writeFile, $firstoline);
		
		echo $firstoline . '<br>';
		
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
	
	echo $pukalstring . "<br>";
}

	fclose($writeFile);

	//$strCommand = "C:\\GnuPG\\gpg.exe -r prd1gfmas -o " . $filenameGPG ." --always-trust -e " . $filenameGFM;

	echo '<br>' . $strCommand . '<br>';

	//exec($strCommand);

   */
  
/* $sftp = new Net_SFTP('10.14.19.136',22);
if (!$sftp->login('ftpcmsrpt', 'cms@Rep0rt')){
	exit('Login Failed');
} else {
	echo 'Success!';
	
	print_r($sftp->nlist());
	
	echo '<br>'; */
	
/* 	$handle = fopen("C:\pukal.txt", "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        echo $line;
		//echo  '\n';// process the line read.
    }

    fclose($handle);
} else {
    // error opening the file.
}  */


	
//	if (!$sftp->get('/SFS/main.js','C:\kuku.ks')){
//		exit('Download Failed');
//	}else{
//		echo 'Done';
//	}
	
//	if (!$sftp->put('/SFS/mainsss.js','C:\kuku.ks')){
//		exit('Upload Failed');
//	}else{
//		echo 'Upload Success';
//	}
	
	
//}


?>