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

if($photoset_id === NULL)
{
	$data = $f->photosets_getList($user_id);
	foreach($data['photoset'] as $photoset)
	{
		echo "<a href=\"?photoset_id=".$photoset['id']."\">".$photoset['title']."</a><br/>";
	}
}
else
{
	$data = $f->photosets_getPhotos($photoset_id);
	if($data === false) exit("Error getting photoset");

	$zip = new ZipArchive();
	//create the file and throw the error if unsuccessful
	$archFina = $photoset_id.".zip";
	if(!file_exists($archFina))
	{
	if ($zip->open($archFina, ZIPARCHIVE::CREATE )!==TRUE)
	{
		exit("cannot open <$archFina>\n");
	}

	foreach($data['photoset']['photo'] as $photo)
	{
		print_r(utf8_decode($photo['title']));
		echo '<br/>';

		$sizes = $f->photos_getSizes($photo['id']);

		$url = NULL;
		foreach($sizes as $size)
		{
			if($size['label'] == "Original") $url = $size['source'];			
		}
		if($url!==NULL)
		{
			$ext = pathinfo($url, PATHINFO_EXTENSION);

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

			echo "Add result:".$zip->addFromString(utf8_decode($photo['title']).".".$ext, $output)."<br/>";
		}

		echo '<br/>';
		flush();
	}

	echo "Closing archive:".$zip->close()."<br/>";
	
	echo "All done!<br/>";
	}
	echo "<a href=\"".$archFina."\">Download</a>";
	flush();
}



?>
