<?php
require_once 'includes/db.php';
include 'includes/header.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

$id = $_GET['id'] ?? '';
if (!ctype_digit($id)) {
    die("Invalid card ID.");
}

// Fetch card data
$query = $conn->prepare("SELECT * FROM cards WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
if ($result->num_rows !== 1) {
    die("Card not found.");
}
$card = $result->fetch_assoc();

// Fetch market price
$priceResult = $conn->prepare("SELECT * FROM market_prices WHERE card_id = ? ORDER BY date_checked DESC LIMIT 1");
$priceResult->bind_param("i", $id);
$priceResult->execute();
$priceData = $priceResult->get_result()->fetch_assoc();

// Fetch card types grouped by TCG
$typesByTCG = [];
$type_result = $conn->query("SELECT id, type_name, tcg FROM card_types ORDER BY tcg, type_name");
while ($type = $type_result->fetch_assoc()) {
    $typesByTCG[$type['tcg']][] = $type;
}

// Allowed rarities
$allowed_rarities = [
  'Common', 'Uncommon', 'Rare', 'Ultra Rare', 'Secret Rare', 'Hyper Rare',
  'Double Rare', 'Promo', 'Reverse Holofoil', 'Super Rare', 'Ghost Rare',
  'Ultimate Rare', 'Platinum Secret Rare', 'Ultra Secret Rare', 'Secret Ultra Rare',
  'Prismatic Secret Rare', 'Extra Secret Rare', 'Quarter Century Secret Rare',
  'Collector’s Rare', 'Mythic Rare', 'Legendary', 'Enchanted'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $card_type_id = $_POST['card_type_id'];
    $rarity = $_POST['rarity'];
    $set_name = trim($_POST['set_name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $source = trim($_POST['source']);
    $date_checked = $_POST['date_checked'];
    $pricing_url = trim($_POST['pricing_url']);
    $image_url = trim($_POST['image_url']);

    $errors = [];

    if ($name === '') $errors[] = "Name is required.";
    if (!ctype_digit($card_type_id)) $errors[] = "Card type is required.";
    if (!in_array($rarity, $allowed_rarities)) $errors[] = "Invalid rarity.";
    if ($set_name === '') $errors[] = "Set name is required.";
    if ($description === '') $errors[] = "Description is required.";
    if (!is_numeric($price)) $errors[] = "Price must be numeric.";
    if ($source === '') $errors[] = "Price source is required.";
    if (!$date_checked) $errors[] = "Date checked is required.";
    if ($pricing_url !== '' && !filter_var($pricing_url, FILTER_VALIDATE_URL)) $errors[] = "Invalid pricing URL.";
    if ($image_url !== '' && !filter_var($image_url, FILTER_VALIDATE_URL)) $errors[] = "Invalid image URL.";

    if (empty($errors)) {
        $update = $conn->prepare("UPDATE cards SET name=?, card_type_id=?, rarity=?, set_name=?, description=?, pricing_url=?, image_url=? WHERE id=?");
        $update->bind_param("sisssssi", $name, $card_type_id, $rarity, $set_name, $description, $pricing_url, $image_url, $id);
        $update->execute();
        $update->close();

        if ($priceData) {
            $updatePrice = $conn->prepare("UPDATE market_prices SET price=?, source=?, date_checked=? WHERE card_id=?");
            $updatePrice->bind_param("dssi", $price, $source, $date_checked, $id);
            $updatePrice->execute();
            $updatePrice->close();
        } else {
            $insertPrice = $conn->prepare("INSERT INTO market_prices (card_id, price, source, date_checked) VALUES (?, ?, ?, ?)");
            $insertPrice->bind_param("idss", $id, $price, $source, $date_checked);
            $insertPrice->execute();
            $insertPrice->close();
        }

        $success = "Card updated successfully!";
        $card['name'] = $name;
        $card['card_type_id'] = $card_type_id;
        $card['rarity'] = $rarity;
        $card['set_name'] = $set_name;
        $card['description'] = $description;
        $card['pricing_url'] = $pricing_url;
        $card['image_url'] = $image_url;
        $priceData['price'] = $price;
        $priceData['source'] = $source;
        $priceData['date_checked'] = $date_checked;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Card</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h2 style="text-align: center;">Edit Card</h2>

<?php if (!empty($errors)): ?>
    <div class="error">
        <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php elseif (!empty($success)): ?>
    <p class="message"><?= $success ?></p>
<?php endif; ?>

<form method="POST">
    <label>Card Name:</label>
    <input type="text" name="name" required value="<?= htmlspecialchars($card['name']) ?>">

    <label>Card Type:</label>
    <select name="card_type_id" required>
      <option value="">-- Select Type --</option>
      <?php foreach ($typesByTCG as $tcg => $types): ?>
        <optgroup label="<?= htmlspecialchars($tcg) ?>">
          <?php foreach ($types as $type): ?>
            <option value="<?= $type['id'] ?>" <?= ($card['card_type_id'] ?? '') == $type['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($type['type_name']) ?>
            </option>
          <?php endforeach; ?>
        </optgroup>
      <?php endforeach; ?>
    </select>

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
    <input type="text" name="set_name" required value="<?= htmlspecialchars($card['set_name']) ?>">

    <label>Description:</label>
    <textarea name="description" rows="3"><?= htmlspecialchars($card['description']) ?></textarea>

    <label>Price (USD):</label>
    <input type="text" name="price" required value="<?= htmlspecialchars($priceData['price'] ?? '') ?>">

    <label>Price Source:</label>
    <input type="text" name="source" required value="<?= htmlspecialchars($priceData['source'] ?? '') ?>">

    <label>Date Price Checked:</label>
    <input type="date" name="date_checked" required value="<?= htmlspecialchars($priceData['date_checked'] ?? '') ?>">

    <label>Pricing Link:</label>
    <input type="url" name="pricing_url" value="<?= htmlspecialchars($card['pricing_url']) ?>">

    <label>Image URL:</label>
    <input type="url" name="image_url" value="<?= htmlspecialchars($card['image_url']) ?>">

    <button type="submit">Update Card</button>
</form>

<div style="text-align: center; margin-top: 20px;">
    <a href="view-cards.php">← Back to Cards</a>
</div>

</body>
</html>
