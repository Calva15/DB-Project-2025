<?php
require_once 'includes/db.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Fetch types
$type_result = $conn->query("SELECT id, type_name FROM card_types");

$allowed_rarities = ['Common', 'Uncommon', 'Rare', 'Ultra Rare', 'Secret Rare'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $card_type_id = $_POST['card_type_id'];
    $rarity = $_POST['rarity'];
    $set_name = trim($_POST['set_name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $source = trim($_POST['source']);
    $date_checked = $_POST['date_checked'];

    $errors = [];

    // Validate
    if ($name === '') $errors[] = "Card name required.";
    if (!ctype_digit($card_type_id)) $errors[] = "Card type required.";
    if (!in_array($rarity, $allowed_rarities)) $errors[] = "Invalid rarity.";
    if ($set_name === '') $errors[] = "Set name required.";
    if ($description === '') $errors[] = "Description required.";
    if (!is_numeric($price)) $errors[] = "Price must be a number.";
    if ($source === '') $errors[] = "Price source required.";
    if (!$date_checked) $errors[] = "Price check date required.";

    if (empty($errors)) {
        $stmt = $conn->prepare("CALL add_card_with_price(?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisssdss", $name, $card_type_id, $rarity, $set_name, $description, $price, $source, $date_checked);
        if ($stmt->execute()) {
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
    <title>Add Card (with Price)</title>
    <style>
        body { font-family: Arial; padding: 30px; background: #f4f4f4; }
        form { max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; }
        input, select, textarea { width: 100%; padding: 8px; margin-bottom: 10px; }
        button { width: 100%; padding: 10px; background: #007BFF; color: white; border: none; }
        .message { text-align: center; color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Add New Card (With Market Price)</h2>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $e): ?><p><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
        </div>
    <?php elseif (isset($success)): ?>
        <p class="message"><?= $success ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Card Name:</label>
        <input type="text" name="name" required>

        <label>Card Type:</label>
        <select name="card_type_id" required>
            <option value="">-- Select Type --</option>
            <?php while ($type = $type_result->fetch_assoc()): ?>
                <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['type_name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label>Rarity:</label>
        <select name="rarity" required>
            <?php foreach ($allowed_rarities as $r): ?>
                <option value="<?= $r ?>"><?= $r ?></option>
            <?php endforeach; ?>
        </select>

        <label>Set Name:</label>
        <input type="text" name="set_name" required>

        <label>Description:</label>
        <textarea name="description" rows="3" required></textarea>

        <hr>

        <label>Price (USD):</label>
        <input type="text" name="price" required>

        <label>Source (e.g. TCGPlayer):</label>
        <input type="text" name="source" required>

        <label>Date Checked:</label>
        <input type="date" name="date_checked" required>

        <button type="submit">Add Card + Price</button>
    </form>
</body>
</html>
