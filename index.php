<?php

require_once("phpflickr/phpFlickr.php");
$f = new phpFlickr("c3a0649ccb823d20f687513db8c186e1");

if(isset($_GET['user_id']))
	$user_id = $_GET['user_id'];
else
	$user_id = "68932647@N00";

if(isset($_GET['photoset_id']))
	$photoset_id = $_GET['photoset_id'];
else
	$photoset_id = NULL;

$targetSize = "Large";
echo "<h1>".$user_id."</h1>\n";

if($photoset_id === NULL)
{
	$data = $f->photosets_getList($user_id);
	foreach($data['photoset'] as $photoset)
	{
		echo "<a href=\"?photoset_id=".$photoset['id']."\">".$photoset['title']."</a><br/>";
	}
?>

<form method="GET">
Flickr NSID: <input type="text" name="user_id"><br/>
<input type="submit" value="Get Photosets">
</form> 

<?
	
}
else
{
	$data = $f->photosets_getPhotos($photoset_id);
	if($data === false) exit("Error getting photoset");

	$zip = new ZipArchive();
	//create the file and throw the error if unsuccessful
	$archFina = $photoset_id.$targetSize.".zip";
	if(!file_exists($archFina))
	{
	if ($zip->open($archFina, ZIPARCHIVE::CREATE )!==TRUE)
	{
		exit("cannot open <$archFina>\n");
	}
	$tmpFinas = array();
	$count = 0;

	foreach($data['photoset']['photo'] as $photo)
	{
		print_r(utf8_decode($photo['title']));
		echo '<br/>';

		$sizes = $f->photos_getSizes($photo['id']);

		$url = NULL;
		$foundSize = NULL;

		foreach($sizes as $size)
		{
			if($size['label'] == "Original" and $url === NULL)
			{
				$url = $size['source'];
				$foundSize = $size['label'];
			}
			if($size['label'] == $targetSize)
			{
				$url = $size['source'];
				$foundSize = $size['label'];
			}
		}

		if($url!==NULL)
		{
			$ext = pathinfo($url, PATHINFO_EXTENSION);

			if(1)
			{
				// create curl resource
				$ch = curl_init();

				// set url
				curl_setopt($ch, CURLOPT_URL, $url);

				//return the transfer as a string
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

				// $output contains the output string
				$output = curl_exec($ch);

				// close curl resource to free up system resources
				curl_close($ch);
			}
			else
				$output = "testmode";

			$tmpFina = sprintf("%05d",$count)."-".$photo['id'].".".$ext;
			$tmpFi = fopen($tmpFina,"wb");
			fwrite($tmpFi, $output);
			fclose($tmpFi);

			$archImgName = sprintf("%05d",$count)."-".utf8_decode($photo['title']).".".$ext;
			echo "Add result:".$zip->addFile($tmpFina, $archImgName)."<br/>";

			array_push($tmpFinas, $tmpFina);
			$count ++;
		}

		echo '<br/>';
		flush();
	}

	echo "Closing archive:".$zip->close()."<br/>";
	
	foreach($tmpFinas as $tmpFina)
	{
		unlink($tmpFina);
	}

	echo "All done!<br/>";
	}
	echo "<a href=\"".$archFina."\">Download</a>";
	flush();
}



?>
