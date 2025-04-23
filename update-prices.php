<?php
require_once 'includes/db.php';
date_default_timezone_set('America/Chicago');

ini_set('display_errors', 1);
error_reporting(E_ALL);

// PriceCharting scraper
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

// TCGPlayer scraper (regex fallback)
function getPriceFromTCGPlayer($url) {
    $html = @file_get_contents($url);
    if (!$html) return null;
    if (preg_match('/Market Price.*?\\$(\d+\.\d+)/', $html, $matches)) {
        return floatval($matches[1]);
    }
    return null;
}

// Troll and Toad scraper (regex fallback)
function getPriceFromTrollAndToad($url) {
    $html = @file_get_contents($url);
    if (!$html) return null;
    if (preg_match('/Our Price.*?\\$(\d+\.\d+)/', $html, $matches)) {
        return floatval($matches[1]);
    }
    return null;
}

$today = date('Y-m-d');
$query = "SELECT id, pricing_url FROM cards WHERE pricing_url IS NOT NULL AND pricing_url != ''";
$result = $conn->query($query);

$updated = [
    'PriceCharting' => 0,
    'TCGPlayer' => 0,
    'Troll and Toad' => 0
];

while ($card = $result->fetch_assoc()) {
    $cardId = $card['id'];
    $url = $card['pricing_url'];
    $price = null;
    $source = '';

    // Multi-site detection
    if (strpos($url, 'pricecharting.com') !== false) {
        $price = getPriceFromPriceCharting($url);
        $source = 'PriceCharting';
    } elseif (strpos($url, 'tcgplayer.com') !== false) {
        $price = getPriceFromTCGPlayer($url);
        $source = 'TCGPlayer';
    } elseif (strpos($url, 'trollandtoad.com') !== false) {
        $price = getPriceFromTrollAndToad($url);
        $source = 'Troll and Toad';
    } else {
        continue; // Skip unsupported sources
    }

    if ($price !== null) {
        $check = $conn->prepare("SELECT id FROM market_prices WHERE card_id = ? AND date_checked = ?");
        $check->bind_param("is", $cardId, $today);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();

        if ($exists) {
            $stmt = $conn->prepare("UPDATE market_prices SET price = ?, source = ?, date_checked = ? WHERE card_id = ? AND date_checked = ?");
            $stmt->bind_param("dssis", $price, $source, $today, $cardId, $today);
        } else {
            $stmt = $conn->prepare("INSERT INTO market_prices (card_id, price, source, date_checked) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("idss", $cardId, $price, $source, $today);
        }

        if ($stmt->execute()) {
            $updated[$source]++;
        }

        $stmt->close();
    }
}

echo "✅ Price updates complete:\n";
foreach ($updated as $site => $count) {
    echo "- $site: $count card(s) updated\n";
}
?>
