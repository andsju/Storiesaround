<?php

include_once 'inc.core.php';
include_once 'inc.functions.php';
include_once 'inc.file-uploader.php';

if (!get_role_CMS('administrator') == 1) {
    die;
}

if (isset($_REQUEST['token'])) {

    function convert_to_filename($string)
    {
        // replace spaces
        $string = preg_replace("/ +/", "-", $string);
        $string = preg_replace("/^ +/", "", $string);
        $string = preg_replace("/ +$/", "", $string);

        // replace characters
        $characters = array(
            'Å' => 'A',
            'Ä' => 'A',
            'Ö' => 'O',
            'Ø' => 'O',
            'É' => 'E',
            'Á' => 'A',
            'Æ' => 'A',
            'å' => 'a',
            'ä' => 'a',
            'ö' => 'o',
            'ø' => 'o',
            'é' => 'e',
            'á' => 'a',
            'æ' => 'a');

        $string = str_replace(flatt_array($characters), flatt_array($characters), $string);

        // allow characters
        $string = preg_replace("/[^a-zA-Z0-9-_]/", "", $string);
        return $string;
    }

    // list of valid extensions, ex. array("jpeg", "xml", "bmp")
    $allowedExtensions = array();

    // max file size in bytes
    $sizeLimit = 10 * 1024 * 1024;

    $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
    $uploadDirectory = CMS_ABSPATH . '/content/uploads/ads/';
    $result = $uploader->handleUpload($uploadDirectory);

    if ($result) {
        $file = $result['newFilename'];
        $details = getimagesize($uploadDirectory . $file);
        $width = $details[0];
        $height = $details[1];

        $banners = new Banners();
        $banners_id = $banners->setBannersNew($file, $file, $width, $height, $active = 0);
        if ($banners_id) {
            $utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), 'Europe/Stockholm', 'Y-m-d H:i:s');
            $history = new History();
            $history->setHistory($banners_id, 'banners_id', 'INSERT', 'file', $_SESSION['users_id'], $_SESSION['token'], $utc_modified);
        }
        $array_to_return = array('success' => true, 'dir' => $uploadDirectory, 'filename' => $file, 'width' => $width, 'height' => $height, 'banners_id' => $banners_id);

        // pass encode data
        echo htmlspecialchars(json_encode($array_to_return), ENT_NOQUOTES);
    }

}
