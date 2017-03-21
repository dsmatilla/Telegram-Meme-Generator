<?php
    require_once 'lib/memegenerator.php';

    $image = $_GET['img'];
    $text = $_GET['text'];

    $aux = explode("/", $text);
    if(count($aux) <= 1) {
        $aux = explode(" ", $text);
        $words = count($aux);
        $chunk = ceil($words / 2);
        $meme = array_chunk($aux, $chunk);
        $textup = implode(" ", $meme[0]);
        $textdown = implode(" ", $meme[1]);
    } else {
        $textup = trim($aux[0]);
        $textdown = trim($aux[1]);
    }

    $obj = new MemeGenerator($image);
	$obj->setUpperText($textup);
	$obj->setLowerText($textdown);
	$obj->outputImg();