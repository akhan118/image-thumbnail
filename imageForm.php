


<form action="" enctype="multipart/form-data" method="post">


    <input type="file" name="datafile" size="40">

    <input type="submit" value="upload">

</form>






<?php
include '../../../private/Config.php';
$image_obj = new image_upload();
var_dump($_GET);


$image_obj->main();


class image_upload {

    public $post_file_name = "datafile";
    public $image_name;
    public $image_type;
    public $image_temp_dir;
    public $image_upload_error;
    public $image_size;
    public $destination_dir;
    public $destination_file_name = 'test';
    public $thumbnail_directory;

    function __construct() {
        header('Content-Type: image/jpeg');


        $this->extract_Post_File();
        $this->destination_dir = $_SERVER['DOCUMENT_ROOT'] . "/testing/images/";
    }

    function main() {

        if ($this->check_error() && $this->check_file_type()) {
            $this->upload();



            $this->generate_image_thumbnail($this->destination_dir . $this->image_name, '/Applications/MAMP/htdocs/testing/images/000.png');

        }
    }


    /*
     * Extract the file uploaded information, such as size, type,name,directory, errors
     * This function is initated in the constructer and populate the class variables above.
     * 
     */

    function extract_Post_File() {
        if ($_FILES[$this->post_file_name]) {
            $this->image_name = $_FILES[$this->post_file_name]["name"];
            $this->image_type = $_FILES[$this->post_file_name]["type"];
            $this->image_temp_dir = $_FILES[$this->post_file_name]["tmp_name"];
            $this->image_upload_error = $_FILES[$this->post_file_name]["error"];
            $this->image_size = $_FILES[$this->post_file_name]["size"];
        }
    }

    /*
     * check if the file upload is a jpeg or png
     * Return True if the file is jpeg or png
     * Return False otherwise
     * 
     */

    function check_file_type() {

        if ($this->image_type != "image/jpeg" && $this->image_type != "image/png") {
            return false;
        } else {

            return true;
        }
    }

    /*
     * check if the uploaded file has any errors
     * Return True if there is no errors
     * Return False if there is an error
     */

    function check_error() {
        if ($this->image_upload_error == UPLOAD_ERR_OK) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * upload the file
     * 
     */

    function upload() {
        if (move_uploaded_file($this->image_temp_dir, $this->destination_dir . $this->image_name)) {
            echo "The file  has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
            //var_dump($this->image_upload_error);
        }
    }
    
    
        function generate_image_thumbnail($src, $dst) {



        $type = strtolower(substr(strrchr($src, "."), 1));
        if ($type == 'jpeg')
            $type = 'jpg';
        switch ($type) {
            case 'jpg': $image = imagecreatefromjpeg($src);
                break;
            case 'png': $image = imagecreatefrompng($src);
                break;
            default : return "Unsupported picture type!";
        }
        $thumb_width = 120;
        $thumb_height = 120;

        $width = imagesx($image);
        $height = imagesy($image);

        $original_aspect = $width / $height;
        $thumb_aspect = $thumb_width / $thumb_height;

        if ($original_aspect >= $thumb_aspect) {
            // If image is wider than thumbnail (in aspect ratio sense)
            $new_height = $thumb_height;
            $new_width = $width / ($height / $thumb_height);
        } else {
            // If the thumbnail is wider than the image
            $new_width = $thumb_width;
            $new_height = $height / ($width / $thumb_width);
        }

        $thumb = imagecreatetruecolor($thumb_width, $thumb_height);


        // preserve transparency
        if ($type == "png") {

            imagecolortransparent($thumb, imagecolorallocatealpha($thumb, 0, 0, 0, 127));
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
        }


        if ($type == "jpg") {

            imagealphablending($thumb, true);
            $transparentcolour = imagecolorallocate($thumb, 255, 255, 255);
            imagecolortransparent($thumb, $transparentcolour);
        }

// Resize and crop
        imagecopyresampled($thumb, $image, 0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
                0, // Center the image vertically
                0, 0, $new_width, $new_height, $width, $height);


        switch ($type) {
            case 'jpg': imagepng($thumb, $dst, 1);
                break;
            case 'png': imagepng($thumb, $dst);
                break;
        }


        imagedestroy($thumb);
    }


}
?>