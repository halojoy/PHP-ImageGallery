<?php

/////////////////////////////////////////////////////////////////////

class ImageTool extends Imagick
{
    public $width  = 500;
    public $height = 100;

    public function __construct($image = '')
    {
        if ($image) {
            $image = realpath($image);
            parent::__construct($image);
            if ($this->getImageFormat() == 'PSD')
                $this->setIteratorIndex(0);
        }else{
            parent::__construct();
        }
    }

    public function read($fname)
    {
        $frompath = realpath($fname);
        $this->readImage($frompath);
        if ($this->getImageFormat() == 'PSD')
            $this->setIteratorIndex(0);
    }

    public function resize($width='', $height='')
    {
        if ($width && $height) {
            $this->width  = $width;
            $this->height = $height;
        }elseif ($width) {
            $this->width  = $width;
            $this->height = 5*$width;
        }elseif ($height) {
            $this->height = $height;
            $this->width  = 5*$height;
        }
        $this->resizeImage($this->width, $this->height, 
                    imagick::FILTER_LANCZOS, 1, true);
    }

    public function convert($format)
    {
        $this->setImageFormat($format);
    }

    public function write($path)
    {
        $this->writeImage(getcwd().'/'.$path);
    }
}

/////////////////////////////////////////////////////////////////////

function gd_imagecreate($image)
{
    $typ = exif_imagetype($image);
    if     ($typ ==  1) $im = imagecreatefromgif($image);
    elseif ($typ ==  2) $im = imagecreatefromjpeg($image);
    elseif ($typ ==  3) $im = imagecreatefrompng($image);
    elseif ($typ ==  6) $im = imagecreatefrombmp($image);
    elseif ($typ == 18) $im = imagecreatefromwebp($image);
    else exit('Image type not supported: '.$typ);
    return $im;
}

function gd_resize($im, $newwidth, $newheight, $width, $height)
{
    $dest = imagecreatetruecolor($newwidth, $newheight);
    imagecopyresampled($dest, $im, 0, 0, 0, 0, 
        $newwidth, $newheight, $width, $height);
    return $dest;
}

/////////////////////////////////////////////////////////////////////
