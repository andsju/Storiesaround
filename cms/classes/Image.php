<?php

/**
 * Class Image
 *
 * Based on documentation from http://php.net/manual/en/function.imagecopyresampled.php
 */
class Image
{

    /**
     * Image constructor
     */
    public function __construct()
    {

    }

    /**
     * @param $imagefile
     * @param $value
     * @return mixed
     */
    public function image_info($imagefile, $value)
    {
        // returns an array with 7 element, 0:height, 1:width, 2:IMAGETYPE_XXX constants, 3:height="yyy" width="xxx"  ...
        $size = getimagesize($imagefile);

        switch ($value) {
            case 'width':
                $v = $size[0];
                break;
            case 'height':
                $v = $size[1];
                break;
            case 'type':
                $types = array(
                    1 => 'GIF', 2 => 'JPG', 3 => 'PNG', 4 => 'SWF', 5 => 'PSD', 6 => 'BMP', 7 => 'TIFF', 8 => 'TIFF',
                    9 => 'JPC', 10 => 'JP2', 11 => 'JPX', 12 => 'JB2', 13 => 'SWC', 14 => 'IFF', 15 => 'WBMP', 16 => 'XBM');
                $v = $types[$size[2]];
                return $v;
                break;
            case 'attr':
                $v = $size[3];
                break;
            case 'bits':
                $v = $size['bits'];
                break;
            case 'channels':
                $v = $size['channels'];
                break;
            case 'mime':
                $v = $size['mime'];
                break;
            default:
                $v = false;
                break;
        }
        return $v;
    }


    /**
     * @param $image
     * @return float|null
     */
    public function image_ratio($image)
    {
        if (list($w, $h) = getimagesize($image)) {
            $ratio = round($h / $w, 3);
            return $ratio;
        } else {
            return null;
        }
    }


    /**
     * @return array
     */
    public function get_image_sizes()
    {
        return array(100, 222, 474, 726, 1024, 1366, 1920);
    }

    
    /**
     * @param $image
     * @param $dst
     * @param $width
     * @return bool|string
     */
    public function image_resize($image, $dst, $width)
    {

        if (!list($w, $h) = getimagesize($image)) return "Unsupported picture type!";

        if (!$type = $this->image_get_type($image)) return "Cant get filetype";
        switch ($type) {
            case 'bmp':
                $img = imagecreatefromwbmp($image);
                break;
            case 'gif':
                $img = imagecreatefromgif($image);
                break;
            case 'jpg':
                $img = imagecreatefromjpeg($image);
                break;
            case 'png':
                $img = imagecreatefrompng($image);
                break;
            default :
                return "Unsupported picture type!";
        }


        if ($w < $width) return "Picture is too small";

        $ratio = $width / $w;
        $height = $h * $ratio;

        $new = imagecreatetruecolor($width, $height);

        // preserve transparency
        if ($type == "gif" or $type == "png") {
            imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
            imagealphablending($new, false);
            imagesavealpha($new, true);
        }

        imagecopyresampled($new, $img, 0, 0, 0, 0, $width, $height, $w, $h);

        switch ($type) {
            case 'bmp':
                imagewbmp($new, $dst);
                break;
            case 'gif':
                imagegif($new, $dst);
                break;
            case 'jpg':
                imagejpeg($new, $dst, 90);
                break;
            case 'png':
                imagepng($new, $dst);
                break;
        }
        return true;
    }

    /**
     * @param $image
     * @return bool|string
     */
    private function image_get_type($image)
    {
        $type = strtolower(substr(strrchr($image, "."), 1));
        switch ($type) {
            case 'bmp':
                $t = 'bmp';
                break;
            case 'gif':
                $t = 'gif';
                break;
            case 'jpg':
                $t = 'jpg';
                break;
            case 'jpeg':
                $t = 'jpg';
                break;
            case 'png':
                $t = 'png';
                break;
            default :
                return false;
                break;
        }
        return $t;
    }

    /**
     * @param $image
     * @param $dst
     * @param $thumb_width
     * @param $thumb_height
     * @return string
     */
    public function image_resize_crop($image, $dst, $thumb_width, $thumb_height)
    {

        if (!list($width, $height) = getimagesize($image)) return "Unsupported picture type!";

        if (!$type = $this->image_get_type($image)) return "Cant get filetype";

        switch ($type) {
            case 'bmp':
                $img = imagecreatefromwbmp($image);
                break;
            case 'gif':
                $img = imagecreatefromgif($image);
                break;
            case 'jpg':
                $img = imagecreatefromjpeg($image);
                break;
            case 'png':
                $img = imagecreatefrompng($image);
                break;
            default :
                return "Unsupported picture type!";
        }

        $original_aspect = $width / $height;
        $thumb_aspect = $thumb_width / $thumb_height;

        if ($original_aspect >= $thumb_aspect) {
            $new_height = $thumb_height;
            $new_width = $width / ($height / $thumb_height);
        } else {
            $new_width = $thumb_width;
            $new_height = $height / ($width / $thumb_width);
        }

        $thumb = imagecreatetruecolor($thumb_width, $thumb_height);

        // resize and crop
        imagecopyresampled($thumb,
            $img,
            0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
            0 - ($new_height - $thumb_height) / 2, // Center the image vertically
            0, 0,
            $new_width, $new_height,
            $width, $height);

        switch ($type) {
            case 'bmp':
                imagewbmp($thumb, $dst);
                break;
            case 'gif':
                imagegif($thumb, $dst);
                break;
            case 'jpg':
                imagejpeg($thumb, $dst, 90);
                break;
            case 'png':
                imagepng($thumb, $dst);
                break;
        }


    }

    /**
     * rotate image
     */
    public function image_rotate($image, $degrees)
    {
        if (!list($w, $h) = getimagesize($image)) {
            return die("Unsupported picture type!");
        }

        // header('Content-type: image/jpeg');
        if (!$type = $this->image_get_type($image)) {
            return die("Missing file extension");
        }
        if ($type == 'gif') {
            if ($this->image_is_animated($image)) {
                return die("Animated gifs can not be rotated");
            }
        }

        switch ($type) {
            case 'bmp':
                $image_original = imagecreatefromwbmp($image);
                break;
            case 'gif':
                $image_original = imagecreatefromgif($image);
                break;
            case 'jpg':
                $image_original = imagecreatefromjpeg($image);
                break;
            case 'png':
                $image_original = imagecreatefrompng($image);
                break;
            default :
                return false;
        }

        // rotate image
        $image_new = imagerotate($image_original, $degrees, 0);

        // preserve alpha
        imagealphablending($image_new, true);
        imagesavealpha($image_new, true);

        // save file
        switch ($type) {
            case 'bmp':
                image2wbmp($image_new, $image);
                break;
            case 'gif':
                imagegif($image_new, $image);
                break;
            case 'jpg':
                imagejpeg($image_new, $image);
                break;
            case 'png':
                imagepng($image_new, $image);
                break;
            default :
                return false;
        }

        // clean up
        imagedestroy($image_original);
        imagedestroy($image_new);
    }

    /**
     * @param $image
     * @return bool
     */
    private function image_is_animated($image)
    {
        return (bool)preg_match('#(\x00\x21\xF9\x04.{4}\x00\x2C.*){2,}#s', file_get_contents($image));
    }

    /**
     * @param $image
     */
    public function image_greyscale($image)
    {
        $im = imagecreatefromjpeg($image);
        $size = getimagesize($image);
        $L = $size[0];
        $H = $size[1];

        for ($j = 0; $j < $H; $j++) {
            for ($i = 0; $i < $L; $i++) {
                // get the pixel color at i,j
                $rgb = imagecolorat($im, $i, $j);

                // get the individual color values
                $r = $rgb & 0x00FF0000;
                $r = $r >> 16;
                $g = $rgb & 0x0000FF00;
                $g = $g >> 8;
                $b = $rgb & 0x0000FF;

                // calculate the grayscale
                $bw = ($r + $g + $b) / 3;
                $result = (0x000000FF << 24) | ($bw << 16) | ($bw << 8) | $bw;

                // create the new color values
                $new_r = ($result >> 16) & 0xFF;
                $new_g = ($result >> 8) & 0xFF;
                $new_b = $result & 0xFF;

                // assign the grayscale color
                $new_color = imagecolorallocate($im, $new_r, $new_g, $new_b);
                imagesetpixel($im, $i, $j, $new_color);
            }
        }
        header("Content-type: image/jpeg");
        imagejpeg($im);
    }

    /**
     * @param $path
     * @param $filename
     * @param $new_filename_prefix
     * @param $filter
     * @return string
     */
    function image_filter($path, $filename, $new_filename_prefix, $filter)
    {

        if (!$type = $this->image_get_type($path . $filename)) return "Cant get filetype";

        switch ($type) {
            case 'bmp':
                $im = imagecreatefromwbmp($path . $filename);
                break;
            case 'gif':
                $im = imagecreatefromgif($path . $filename);
                break;
            case 'jpg':
                $im = imagecreatefromjpeg($path . $filename);
                break;
            case 'png':
                $im = imagecreatefrompng($path . $filename);
                break;
            default :
                return "Unsupported picture type!";
        }

        // apply filter
        switch ($filter) {

            case "IMG_FILTER_GRAYSCALE" :
                if ($im && imagefilter($im, IMG_FILTER_GRAYSCALE)) {
                    $this->output_image($im, $path, $filename, $new_filename_prefix, $type);
                }
                break;

            case "IMG_FILTER_EDGEDETECT" :
                if ($im && imagefilter($im, IMG_FILTER_EDGEDETECT)) {
                    $this->output_image($im, $path, $filename, $new_filename_prefix, $type);
                }
                break;

            case "IMG_FILTER_EMBOSS" :
                if ($im && imagefilter($im, IMG_FILTER_EMBOSS)) {
                    $this->output_image($im, $path, $filename, $new_filename_prefix, $type);
                }
                break;
            case "IMG_FILTER_NEGATE" :
                if ($im && imagefilter($im, IMG_FILTER_NEGATE)) {
                    $this->output_image($im, $path, $filename, $new_filename_prefix, $type);
                }
                break;
            case "IMG_FILTER_GAUSSIAN_BLUR" :
                if ($im && imagefilter($im, IMG_FILTER_GAUSSIAN_BLUR)) {
                    $this->output_image($im, $path, $filename, $new_filename_prefix, $type);
                }
                break;
            case "IMG_FILTER_SELECTIVE_BLUR" :
                if ($im && imagefilter($im, IMG_FILTER_SELECTIVE_BLUR)) {
                    $this->output_image($im, $path, $filename, $new_filename_prefix, $type);
                }
                break;
            case "IMG_FILTER_MEAN_REMOVAL" :
                if ($im && imagefilter($im, IMG_FILTER_MEAN_REMOVAL)) {
                    $this->output_image($im, $path, $filename, $new_filename_prefix, $type);
                }
                break;
            case "IMG_FILTER_BRIGHTNESS_PLUS" :
                if ($im && imagefilter($im, IMG_FILTER_BRIGHTNESS, 20)) {
                    $this->output_image($im, $path, $filename, $new_filename_prefix, $type);
                }
                break;
            case "IMG_FILTER_BRIGHTNESS_MINUS" :
                if ($im && imagefilter($im, IMG_FILTER_BRIGHTNESS, -20)) {
                    $this->output_image($im, $path, $filename, $new_filename_prefix, $type);
                }
                break;
            case "IMG_FILTER_CONTRAST_PLUS" :
                if ($im && imagefilter($im, IMG_FILTER_CONTRAST, -5)) {
                    $this->output_image($im, $path, $filename, $new_filename_prefix, $type);
                }
                break;
            case "IMG_FILTER_CONTRAST_MINUS" :
                if ($im && imagefilter($im, IMG_FILTER_CONTRAST, 5)) {
                    $this->output_image($im, $path, $filename, $new_filename_prefix, $type);
                }
                break;
            case "IMG_FILTER_SMOOTH" :
                if ($im && imagefilter($im, IMG_FILTER_SMOOTH, 1)) {
                    $this->output_image($im, $path, $filename, $new_filename_prefix, $type);
                }
                break;
            case "IMG_FILTER_PIXELATE" :
                if ($im && imagefilter($im, IMG_FILTER_PIXELATE, 5, false)) {
                    $this->output_image($im, $path, $filename, $new_filename_prefix, $type);
                }
                break;

        }

        imagedestroy($im);
    }

    /**
     * @param $im
     * @param $path
     * @param $filename
     * @param $new_filename_prefix
     * @param $type
     * @return bool|string
     */
    private function output_image($im, $path, $filename, $new_filename_prefix, $type)
    {

        switch ($type) {
            case 'bmp':
                return image2wbmp($im, $path . $new_filename_prefix . $filename);
                break;
            case 'gif':
                return imagegif($im, $path . $new_filename_prefix . $filename);
                break;
            case 'jpg':
                return imagejpeg($im, $path . $new_filename_prefix . $filename, 90);
                break;
            case 'png':
                return imagepng($im, $path . $new_filename_prefix . $filename);
                break;
            default :
                return "Unsupported picture type!";
        }
    }

    /**
     * @param $image_file
     * @param $dst_x
     * @param $dst_y
     * @param $src_x
     * @param $src_y
     * @param $dst_w
     * @param $dst_h
     * @param $src_w
     * @param $src_h
     */
    public function crop($image_file, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
    {
        $dst_image = imagecreatetruecolor($dst_w, $dst_h);
        $src_image = imagecreatefromjpeg($image_file);
        imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        imagejpeg($dst_image, 'new.jpg');
    }


    /**
     * @param $path
     * @param $filename
     * @param $new_filename_prefix
     * @param $dst_x
     * @param $dst_y
     * @param $src_x
     * @param $src_y
     * @param $dst_w
     * @param $dst_h
     * @param $src_w
     * @param $src_h
     * @return bool|string
     */
    public function image_crop($path, $filename, $new_filename_prefix, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
    {
        if (!$type = $this->image_get_type($path . $filename)) return "Cant get filetype";

        $new_image = imagecreatetruecolor($dst_w, $dst_h);


        switch ($type) {
            case 'bmp':
                $src_image = imagecreatefromwbmp($path . $filename);
                break;
            case 'gif':
                $src_image = imagecreatefromgif($path . $filename);
                break;
            case 'jpg':
                $src_image = imagecreatefromjpeg($path . $filename);
                break;
            case 'png':
                $src_image = imagecreatefrompng($path . $filename);
                break;
            default :
                return "Unsupported picture type!";
        }
        imagecopyresampled($new_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

        return $this->output_image($new_image, $path, $filename, $new_filename_prefix, $type);
    }

    /**
     * @param $path_and_filename
     * @return string
     */
    public function get_max_image($path_and_filename)
    {

        if (is_file($_SERVER['DOCUMENT_ROOT'] . $path_and_filename)) {

            $extension = pathinfo($_SERVER['DOCUMENT_ROOT'] . $path_and_filename, PATHINFO_EXTENSION);
            $ext = strlen($extension);
            
            $pos_underscore = strrpos($path_and_filename, '_') + 1;
            $pos_dot = strrpos($path_and_filename, '.') + 1;
            $pre = substr($path_and_filename, 0, $pos_underscore);
            $sizes = $this->get_image_sizes();
            rsort($sizes);

            foreach($sizes as $size) {
                // biggest possible
                $img = $pre . $size . '.'. $extension;
                if (is_file($_SERVER['DOCUMENT_ROOT'] . $img)) {
                    return $img;
                }            
            }
        }

    }

    /**
     * @param $path_and_filename
     * @param $return
     * @return string
     */
    public function get_max_image2($path_and_filename, $return)
    {

        if (is_file($_SERVER['DOCUMENT_ROOT'] . $path_and_filename)) {
            
            $extension = pathinfo($_SERVER['DOCUMENT_ROOT'] . $path_and_filename, PATHINFO_EXTENSION);
            $ext = strlen($extension);
            
            $pos_underscore = strrpos($path_and_filename, '_') + 1;
            $pos_dot = strrpos($path_and_filename, '.') + 1;
            $pre = substr($path_and_filename, 0, $pos_underscore);
            $sizes = $this->get_image_sizes();
            rsort($sizes);

            foreach($sizes as $size) {
                // biggest possible
                $f = $pre . $size .'.'. $extension;
                if (is_file($_SERVER['DOCUMENT_ROOT'] . $f)) {
                    $s = $return == 'filename' ? substr($f, strrpos($f, '/') + 1) : $f;
                    return $s;
                }                
            } 
            return null;
        }
    }

/**
     * @param string path_and_filename
     * @param int column_width
     * @return string
     */
    public function get_optimzed_image($path_and_filename, $column_width)
    {
        if (is_file($_SERVER['DOCUMENT_ROOT'] . $path_and_filename)) {

            $extension = pathinfo($_SERVER['DOCUMENT_ROOT'] . $path_and_filename, PATHINFO_EXTENSION);
            $ext = strlen($extension);            
            $pos_underscore = strrpos($path_and_filename, '_') + 1;
            $pos_dot = strrpos($path_and_filename, '.') + 1;
            $pre = substr($path_and_filename, 0, $pos_underscore);
            $sizes = $this->get_image_sizes();
            rsort($sizes);
            for ($i = 0; $i < count($sizes); $i++) 
            {
                if ($sizes[$i] < $column_width) {
                    $f = $pre . $sizes[$i-1] .'.'. $extension;
                    
                    if (is_file($_SERVER['DOCUMENT_ROOT'] . $f)) {
                        return $f;                            
                    } 
                }
            }
            return $path_and_filename;
        }
    }


    /**
     * @param $filename
     * @return array
     */
    function image_extract_xmpdata($filename)
    {

        ob_start();
        readfile($filename);
        $source = ob_get_contents();
        ob_end_clean();

        $xmpdata_start = strpos($source, "<x:xmpmeta");
        $xmpdata_end = strpos($source, "</x:xmpmeta>");
        $xmplenght = $xmpdata_end - $xmpdata_start;
        $xmpdata = substr($source, $xmpdata_start, $xmplenght + 12);

        $xmp_parsed = array();

        $regexps = array(
            array("name" => "DC creator", "regexp" => "/<dc:creator>\s*<rdf:Seq>.+<\/rdf:Seq>\s*<\/dc:creator>/"),
            array("name" => "DC rights", "regexp" => "/<dc:rights>\s*<rdf:Alt>.+<\/rdf:Alt>\s*<\/dc:rights>/"),
            array("name" => "DC description", "regexp" => "/<dc:description>\s*<rdf:Alt>.+<\/rdf:Alt>\s*<\/dc:description>/"),
            array("name" => "XMP CreateDate", "regexp" => "/xmp:CreateDate=\"(.[^\"]+)\"/"),
        );

        foreach ($regexps as $key => $k) {
            $name = $k["name"];
            $regexp = $k["regexp"];
            unset($matches);
            preg_match($regexp, $xmpdata, $matches);
            $xmp_item = isset($matches[1]) ? strip_tags($matches[1]) : null;
            if ($xmp_item === null) {
                $xmp_item = isset($matches[0]) ? strip_tags($matches[0]) : '';
            }
            array_push($xmp_parsed, array("item" => $name, "value" => $xmp_item));
        }

        return ($xmp_parsed);
    }

}

?>