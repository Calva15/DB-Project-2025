<?php
require_once 'includes/db.php';
include 'includes/header.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Chicago');

// Fetch card types grouped by TCG
$typesByTCG = [];
$type_result = $conn->query("SELECT id, type_name, tcg FROM card_types ORDER BY tcg, type_name");
while ($type = $type_result->fetch_assoc()) {
    $typesByTCG[$type['tcg']][] = $type;
}

// Allowed rarities (previously defined)
$allowed_rarities = [
  'Common', 'Uncommon', 'Rare', 'Ultra Rare', 'Secret Rare', 'Hyper Rare',
  'Double Rare', 'Promo', 'Reverse Holofoil', 'Super Rare', 'Ghost Rare',
  'Ultimate Rare', 'Platinum Secret Rare', 'Ultra Secret Rare', 'Secret Ultra Rare',
  'Prismatic Secret Rare', 'Extra Secret Rare', 'Quarter Century Secret Rare',
  'Collector’s Rare', 'Mythic Rare', 'Legendary', 'Enchanted'
];

function getPriceFromPriceCharting($url) {
    $html = @file_get_contents($url);
    if (!$html) return null;
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $priceNode = $xpath->query("//span[contains(@class, 'price')]")->item(0);
    if ($priceNode) {
        $priceText = trim($priceNode->nodeValue);
        return floatval(str_replace(['$', ','], '', $priceText));
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $card_type_id = $_POST['card_type_id'];
    $rarity = $_POST['rarity'];
    $set_name = trim($_POST['set_name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'] ?? '';
    $source = trim($_POST['source'] ?? '');
    $date_checked = $_POST['date_checked'] ?? '';
    $pricing_url = trim($_POST['pricing_url'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
    $errors = [];

    if ($name === '') $errors[] = "Card name is required.";
    if (!ctype_digit($card_type_id)) $errors[] = "Card type must be selected.";
    if (!in_array($rarity, $allowed_rarities)) $errors[] = "Invalid rarity.";
    if ($set_name === '') $errors[] = "Set name is required.";
    if ($description === '') $errors[] = "Description is required.";

    if ($pricing_url !== '' && filter_var($pricing_url, FILTER_VALIDATE_URL)) {
        if (strpos($pricing_url, 'pricecharting.com') !== false) {
            $autoPrice = getPriceFromPriceCharting($pricing_url);
            if ($autoPrice !== null) {
                $price = $autoPrice;
                $source = "PriceCharting";
                $date_checked = date('Y-m-d');
            } else {
                $errors[] = "Failed to fetch price from PriceCharting.";
            }
        } else {
            $source = "Other";
        }
    }

    if ($price === '' || !is_numeric($price)) $errors[] = "Price is required (auto or manual).";
    if ($date_checked === '') $date_checked = date('Y-m-d');
    if ($image_url !== '' && !filter_var($image_url, FILTER_VALIDATE_URL)) {
        $errors[] = "Invalid image URL.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("CALL add_card_with_price(?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisssdsss", $name, $card_type_id, $rarity, $set_name, $description, $price, $source, $date_checked, $image_url);
        if ($stmt->execute()) {
            $last_id = $conn->insert_id;
            if ($pricing_url !== '') {
                $updateURL = $conn->prepare("UPDATE cards SET pricing_url = ? WHERE id = ?");
                $updateURL->bind_param("si", $pricing_url, $last_id);
                $updateURL->execute();
                $updateURL->close();
            }
            $success = "Card and price added successfully!";
        } else {
            $errors[] = "Database error: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Card</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h2 style="text-align:center;">Add New Trading Card</h2>

<?php if (!empty($errors)): ?>
    <div class="error"><?php foreach ($errors as $e): ?><p><?= htmlspecialchars($e) ?></p><?php endforeach; ?></div>
<?php elseif (isset($success)): ?>
    <p class="message"><?= $success ?></p>
<?php endif; ?>

<form method="POST">
    <label>Card Name:</label>
    <input type="text" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">

    <label>Card Type:</label>
    <select name="card_type_id" required>
      <option value="">-- Select Type --</option>
      <?php foreach ($typesByTCG as $tcg => $types): ?>
        <optgroup label="<?= htmlspecialchars($tcg) ?>">
          <?php foreach ($types as $type): ?>
            <option value="<?= $type['id'] ?>"
              <?= ($_POST['card_type_id'] ?? '') == $type['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($type['type_name']) ?>
            </option>
          <?php endforeach; ?>
        </optgroup>
      <?php endforeach; ?>
    </select>

    <label>Rarity:</label>
    <label>Rarity:</label>
<select name="rarity" required>
  <optgroup label="Pokémon">
    <option value="Common">Common</option>
    <option value="Uncommon">Uncommon</option>
    <option value="Rare">Rare</option>
    <option value="Ultra Rare">Ultra Rare</option>
    <option value="Secret Rare">Secret Rare</option>
    <option value="Hyper Rare">Hyper Rare</option>
    <option value="Double Rare">Double Rare</option>
    <option value="Promo">Promo</option>
    <option value="Reverse Holofoil">Reverse Holofoil</option>
  </optgroup>
  <optgroup label="Yu-Gi-Oh!">
    <option value="Common">Common</option>
    <option value="Rare">Rare</option>
    <option value="Super Rare">Super Rare</option>
    <option value="Ultra Rare">Ultra Rare</option>
    <option value="Secret Rare">Secret Rare</option>
    <option value="Ghost Rare">Ghost Rare</option>
    <option value="Ultimate Rare">Ultimate Rare</option>
    <option value="Platinum Secret Rare">Platinum Secret Rare</option>
    <option value="Ultra Secret Rare">Ultra Secret Rare</option>
    <option value="Secret Ultra Rare">Secret Ultra Rare</option>
    <option value="Prismatic Secret Rare">Prismatic Secret Rare</option>
    <option value="Extra Secret Rare">Extra Secret Rare</option>
    <option value="Quarter Century Secret Rare">Quarter Century Secret Rare</option>
    <option value="Collector’s Rare">Collector’s Rare</option>
  </optgroup>
  <optgroup label="Magic: The Gathering">
    <option value="Common">Common</option>
    <option value="Uncommon">Uncommon</option>
    <option value="Rare">Rare</option>
    <option value="Mythic Rare">Mythic Rare</option>
  </optgroup>
  <optgroup label="Lorcana">
    <option value="Common">Common</option>
    <option value="Uncommon">Uncommon</option>
    <option value="Rare">Rare</option>
    <option value="Super Rare">Super Rare</option>
    <option value="Legendary">Legendary</option>
    <option value="Enchanted">Enchanted</option>
  </optgroup>
</select>

    <label>Set Name:</label>
    <input type="text" name="set_name" required value="<?= htmlspecialchars($_POST['set_name'] ?? '') ?>">

    <label>Description:</label>
    <textarea name="description" rows="3" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

    <label>Price (USD):</label>
    <input type="text" name="price" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">

    <label>Price Source:</label>
    <input type="text" name="source" value="<?= htmlspecialchars($_POST['source'] ?? '') ?>">

    <label>Date Price Checked:</label>
    <input type="date" name="date_checked" value="<?= htmlspecialchars($_POST['date_checked'] ?? '') ?>">

    <label>Pricing Link:</label>
    <input type="url" name="pricing_url" value="<?= htmlspecialchars($_POST['pricing_url'] ?? '') ?>">

    <label>Image URL:</label>
    <input type="url" name="image_url" value="<?= htmlspecialchars($_POST['image_url'] ?? '') ?>">

    <button type="submit">Add Card</button>
</form>

<div style="text-align: center; margin-top: 20px;">
    <a href="index.html">← Back to Home</a>
</div>

</body>
</html>
