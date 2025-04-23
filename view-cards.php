<?php
require_once 'includes/db.php';

$sql = "SELECT 
            cards.id,
            cards.name,
            card_types.type_name,
            cards.rarity,
            cards.set_name,
            cards.description,
            cards.image_url,
            market_prices.price,
            market_prices.source,
            market_prices.date_checked
        FROM cards
        LEFT JOIN card_types ON cards.card_type_id = card_types.id
        LEFT JOIN market_prices ON cards.id = market_prices.card_id
        ORDER BY cards.id";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Cards</title>
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


  <h1 class="page-title">All Trading Cards</h1>
  <a class="back" href="index.html">← Back to Home</a>

  <?php if ($result->num_rows > 0): ?>
    <table>
      <tr>
        <th>Name</th>
        <th>Type</th>
        <th>Rarity</th>
        <th>Set</th>
        <th>Description</th>
        <th>Price (USD)</th>
        <th>Source</th>
        <th>Checked On</th>
        <th>Image</th>
        <th>Actions</th>
      </tr>
      <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['type_name']) ?></td>
        <td><?= htmlspecialchars($row['rarity']) ?></td>
        <td><?= htmlspecialchars($row['set_name']) ?></td>
        <td><?= htmlspecialchars($row['description']) ?></td>
        <td><?= $row['price'] !== null ? '$' . number_format($row['price'], 2) : 'N/A' ?></td>
        <td><?= htmlspecialchars($row['source']) ?></td>
        <td><?= htmlspecialchars($row['date_checked']) ?></td>
        <td>
          <?php if (!empty($row['image_url'])): ?>
            <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="Card Image" class="zoom-image">
          <?php else: ?>
            <span style="color: #aaa;">No image</span>
          <?php endif; ?>
        </td>
        <td>
          <a href="edit-card.php?id=<?= $row['id'] ?>">✏️ Edit</a> |
          <a href="delete-card.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this card?');">🗑️ Delete</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <p style="text-align: center;">No cards found.</p>
  <?php endif; ?>

</body>
</html>
