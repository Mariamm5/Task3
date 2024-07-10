<?php
$uploadDir = 'uploads/';
if (isset($_POST['submit']) && isset($_FILES['image'])) {
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
            $thumbnailFileName = $uploadDir . "thumb_" . basename($_FILES['image']['name']);
            imagejpeg($thumbnail, $thumbnailFileName);
            imagedestroy($image);
            imagedestroy($thumbnail);
            $thumbnail = $thumbnailFileName;
        } else {
            echo 'Sorry, there was an error uploading your file.';
        }
    } else {
        echo 'File is not an image.';
    }
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
