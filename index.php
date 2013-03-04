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
		//print_r($photoset);
		echo "<a href=\"?photoset_id=".$photoset['id']."\">".$photoset['title']."</a><br/>";


	}
}
else
{
	$data = $f->photosets_getPhotos($photoset_id);
	//print_r($data['photoset']['photo']);
	foreach($data['photoset']['photo'] as $photo)
	{
	print_r(utf8_decode($photo['title']));
	echo '<br/>';

	$sizes = $f->photos_getSizes($photo['id']);

	foreach($sizes as $size)
	{
		if($size['label'] == "Original") echo $size['source'];
	}

	echo '<br/>';
	}

}





/*foreach ($recent['photos'] as $photo) {
    $owner = $f->people_getInfo($photo['owner']);
    echo "<a href='http://www.flickr.com/photos/" . $photo['owner'] . "/" . $photo['id'] . "/'>";
    echo $photo['title'];
    echo "</a> Owner: ";
    echo "<a href='http://www.flickr.com/people/" . $photo['owner'] . "/'>";
    echo $owner['username'];
    echo "</a><br>";
}*/


?>
