<?php
if (isset($_POST['submit']) && $_FILES['image']) {
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($_FILES['image']['name']);
    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES['image']['tmp_name']);
    if ($check !== false) {
        if ($_FILES['image']['size'] > 500000) {
            echo 'Sorry, your file is too large.';
            exit;
        }
        if ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'gif') {
            echo 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
            exit;
        }
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $thumbnailWidth = 150;
            $thumbnailHeight = 150;

            list($width, $height) = getimagesize($uploadFile);

            switch ($imageFileType) {
                case 'jpg':
                case 'jpeg':
                    $image = imagecreatefromjpeg($uploadFile);
                    break;
                case 'png':
                    $image = imagecreatefrompng($uploadFile);
                    break;
                case 'gif':
                    $image = imagecreatefromgif($uploadFile);
                    break;
                default:
                    echo 'Unsupported file type.';
                    exit;
            }
            $thumbnail = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);
            imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $width, $height);
            echo '<h2>Original Image</h2>';
            echo '<img src="' . $uploadFile . '" alt="Original Image"><br>';

            echo '<h2>Thumbnail</h2>';
            $thumbnailFileName = $uploadDir . "thumb_" . basename($_FILES["image"]["name"]);
            imagejpeg($thumbnail, $thumbnailFileName);
            echo '<img src="' . $thumbnailFileName . '" alt="Thumbnail">';
            imagedestroy($image);
            imagedestroy($thumbnail);

        } else {
            echo 'Sorry, there was an error uploading your file.';
        }
    } else {
        echo 'File is not an image.';
    }
}

