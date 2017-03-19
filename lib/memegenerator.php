<?php

class MemeGenerator {

    private $upperText;
    private $lowerText;
    private $alignment;
    private $background;
    private $font = './impact.ttf';
    private $im;
    private $imgSize;

    public function __construct($path) {
        $this->im = $this->ReturnImageFromPath($path);
        $this->imgSize = getimagesize($path);

        $this->background = imagecolorallocate($this->im, 255, 255, 255);
        imagecolortransparent($this->im, $this->background);
    }

    public function setUpperText($txt) {
        $this->upperText = strtoupper($txt);
    }

    public function setLowerText($txt) {
        $this->lowerText = strtoupper($txt);
    }

    private function getHorizontalTextAlignment($imgWidth, $topRightPixelOfText) {
        return ceil(($imgWidth - $topRightPixelOfText) / 2);
    }

    private function CheckTextWidthExceedImage($imgWidth, $fontWidth) {
        if ($imgWidth < $fontWidth + 20)
            return true;
        else
            return false;
    }

    private function GetFontPlacementCoordinates($text, $fontSize) {
        return imagettfbbox($fontSize, 0, $this->font, $text);
    }

    private function ReturnImageFromPath($path) {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if ($ext == 'jpg' || $ext == 'jpeg')
            return imagecreatefromjpeg($path);
        else if ($ext == 'png')
            return imagecreatefrompng($path);
        else if ($ext == 'gif')
            return imagecreatefromgif($path);
    }

    private function WorkOnImage($text, $size, $type) {
        if ($type == "upper")
            $TextHeight = 35;
        else
            $TextHeight = $this->imgSize[1] - 20;
        while (1) {
            $coords = $this->GetFontPlacementCoordinates($text, $size);
            if ($type == "upper")
                $UpperTextX = $this->getHorizontalTextAlignment($this->imgSize[0], $coords[4]);
            else
                $LowerTextX = $this->getHorizontalTextAlignment($this->imgSize[0], $coords[4]);
            if ($this->CheckTextWidthExceedImage($this->imgSize[0], $coords[2] - $coords[0])) {
                if ($type == "upper")
                    $TextHeight = $TextHeight - 1;        //if it is top text take it up as font size decreases
                else
                    $TextHeight = $TextHeight + 1;        //if it is bottom text take it down as font size decreases
                if ($size == 10) {
                    if ($type == "upper") {
                        $this->upperText = $this->ReturnMultipleLinesText($text, $type, 16);
                        $text = $this->upperText;
                        return;
                    } else {
                        $this->lowerText = $this->ReturnMultipleLinesText($text, $type, $this->imgSize[1] - 20);
                        $text = $this->lowerText;
                        return;
                    }
                } else
                    $size -= 1;
            } else
                break;
        }
        if ($type == "upper")
            $this->PlaceTextOnImage($this->im, $size, $UpperTextX, $TextHeight + 20, $this->font, $this->upperText);
        else
            $this->PlaceTextOnImage($this->im, $size, $LowerTextX, $TextHeight, $this->font, $this->lowerText);
    }

    private function PlaceTextOnImage($img, $fontsize, $Xlocation, $Textheight, $font, $text) {
        $black = imagecolorallocate($this->im, 0, 0, 0);
        $px = 2;

        for ($c1 = ($Xlocation - abs($px)); $c1 <= ($Xlocation + abs($px)); $c1++)
            for ($c2 = ($Textheight - abs($px)); $c2 <= ($Textheight + abs($px)); $c2++)
                imagettftext($this->im, $fontsize, 0, $c1, $c2, $black, $font, $text);

        imagettftext($this->im, $fontsize, 0, $Xlocation, $Textheight, (int)$this->background, $font, $text);
    }

    private function ReturnMultipleLinesText($text, $type, $textHeight) {
        $brokenText = explode(" ", $text);
        $finalOutput = "";

        if ($type != "upper")
            $textHeight = $this->imgSize[1] - ((count($brokenText) / 2) * 3);

        for ($i = 0; $i < count($brokenText); $i++) {
            $temp = $finalOutput;
            $finalOutput .= $brokenText[$i] . " ";
            $dimensions = $this->GetFontPlacementCoordinates($finalOutput, 10);
            if ($this->CheckTextWidthExceedImage($this->imgSize[0], $dimensions[2] - $dimensions[0])) {
                $dimensions = $this->GetFontPlacementCoordinates($temp, 10);
                $locx = $this->getHorizontalTextAlignment($this->imgSize[0], $dimensions[4]);
                $this->PlaceTextOnImage($this->im, 10, $locx, $textHeight, $this->font, $temp);
                $finalOutput = $brokenText[$i];
                $textHeight += 13;
            }
            if ($i == count($brokenText) - 1) {
                $dimensions = $this->GetFontPlacementCoordinates($finalOutput, 10);
                $locx = $this->getHorizontalTextAlignment($this->imgSize[0], $dimensions[4]);
                $this->PlaceTextOnImage($this->im, 10, $locx, $textHeight, $this->font, $finalOutput);
            }
        }
        return $finalOutput;
    }

    public function processImg($file) {
        if ($this->lowerText != "") {
            $this->WorkOnImage($this->lowerText, 30, "lower");
        }
        if ($this->upperText != "") {
            $this->WorkOnImage($this->upperText, 30, "upper");
        }
        imagejpeg($this->im, $file);
        imagedestroy($this->im);
    }

    public function outputImg() {
        if ($this->lowerText != "") {
            $this->WorkOnImage($this->lowerText, 30, "lower");
        }
        if ($this->upperText != "") {
            $this->WorkOnImage($this->upperText, 30, "upper");
        }

        header('Content-Type: image/jpeg');
        imagejpeg($this->im);
        imagedestroy($this->im);
    }
}