<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Trading Card Library</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <script>
    window.addEventListener('load', async () => {
      console.log("Checking for background image...");

      try {
        const res = await fetch('uploads/background.jpg', { method: 'HEAD' });

        if (res.ok) {
          const timestamp = new Date().getTime();
          const bgUrl = `url('uploads/background.jpg?cache=${timestamp}')`;
          document.body.style.backgroundImage = bgUrl;
          document.body.style.backgroundSize = "cover";
          document.body.style.backgroundPosition = "center";
          document.body.style.backgroundRepeat = "no-repeat";
          document.body.style.backgroundAttachment = "fixed";
          console.log("✅ Background applied:", bgUrl);
        } else {
          console.log("⚠️ Background image not found. Using fallback.");
          document.body.style.background = "#1e1e1e";
        }
      } catch (err) {
        console.error("❌ Error checking background image:", err);
      }
    });
  </script>
