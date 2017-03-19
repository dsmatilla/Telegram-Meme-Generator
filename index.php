<?php
    require_once 'lib/memegenerator.php';

    $image = $_GET['img'];
    $text = $_GET['text'];

	$aux = explode(" ", $text);

	$words = count($aux);
	$chunk = ceil($words / 2);
	$meme = array_chunk($aux, $chunk);

    $obj = new MemeGenerator($image);
	$obj->setUpperText(implode(" ", $meme[0]));
	$obj->setLowerText(implode(" ", $meme[1]));
	$obj->outputImg();