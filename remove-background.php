<?php
$backgroundPath = 'uploads/background.jpg';

if (file_exists($backgroundPath)) {
    if (unlink($backgroundPath)) {
        echo "✅ Background removed!";
    } else {
        echo "❌ Failed to remove background.";
    }
} else {
    echo "⚠️ No background image found.";
}
?>
