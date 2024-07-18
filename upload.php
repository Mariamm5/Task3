<?php
$uploadDir = 'uploads/';
$error = false;
$message = null;

if (isset($_POST['submit']) && isset($_FILES['image'])) {
    $uploadFile = $uploadDir . basename($_FILES['image']['name']);
    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES['image']['tmp_name']);
//
//
//    echo "<pre>";
//    var_dump($check);
//    echo "</pre>";

    if ($check === false) {
        $error = true;
        $message = 'File is not an image.';
    } elseif ($_FILES['image']['size'] > 500000) {
        $error = true;
        $message = 'Sorry, your file is too large';
    } elseif ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'gif') {
        $error = true;
        $message = 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
    } else {
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
                    break;
            }
            if (!$error) {
                $thumbnail = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);
                imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $width, $height);
                $thumbnailFileName = $uploadDir . "thumb_" . basename($_FILES['image']['name']);
                imagejpeg($thumbnail, $thumbnailFileName);
                imagedestroy($image);
                imagedestroy($thumbnail);
                $thumbnail = $thumbnailFileName;
                $message = 'File uploaded successfully.';
            }

        } else {
            $message = 'Sorry, there was an error uploading your file.';
        }
    }
} else {
    $message = 'Select a img to upload.';
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Upload Image</title>
</head>
<body>
<h2>Upload Image</h2>
<form method="post" enctype="multipart/form-data">

    <?php if (isset($message)): ?>
        <p style="color:black">
            <?= htmlspecialchars($message);
            unset($message); ?>
        </p>
    <?php endif; ?>


    <input type="file" name="image" accept="image/*">
    <button type="submit" name="submit">Upload Image</button>
</form>

<?php if (!empty($uploadFile) && !empty($thumbnail)): ?>
    <h2>Original Image</h2>
    <img src="<?= $uploadFile ?>" alt="Upload image">
    <h2>Thumbnail</h2>
    <img src="<?= $thumbnail ?>" alt="Edited image">
<?php endif; ?>

</body>
</html>
