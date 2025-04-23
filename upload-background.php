<?php
$uploadDir = 'uploads/';
$targetFile = $uploadDir . 'background.jpg';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (!isset($_FILES['background_image'])) {
    echo "❌ No file field found in form.";
    exit;
}

$errorCode = $_FILES['background_image']['error'];
$tmp = $_FILES['background_image']['tmp_name'];

if ($errorCode !== UPLOAD_ERR_OK) {
    echo "❌ Upload error code: $errorCode";
    exit;
}

$check = getimagesize($tmp);
if ($check === false) {
    echo "❌ File is not a valid image.";
    exit;
}

if (move_uploaded_file($tmp, $targetFile)) {
    echo "✅ Background uploaded!";
} else {
    echo "❌ move_uploaded_file() failed. Check permissions for '$uploadDir'.";
}
?>
