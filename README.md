
# 📚 Trading Card Library

A lightweight web-based library system to manage, view, and track trading cards, complete with live market price updates.

---

## 📋 Features

- Add, edit, and delete cards
- Upload and remove background images dynamically
- Grouped types and rarities by TCG (Pokémon, Yu-Gi-Oh!, Magic, Lorcana)
- Scrape and update live market prices from PriceCharting
- Fully responsive design with dark mode
- Floating, collapsible background settings widget
- AJAX price updater (no page reload)

---

## 🛠 Installation

1. **Download or Clone the Repository**
   ```bash
   git clone https://github.com/yourusername/trading-card-library.git
   ```

2. **Setup XAMPP or Similar Local Server**
   - Install [XAMPP](https://www.apachefriends.org/).
   - Move project files into `/htdocs/tradingcards/`.

3. **Import the Database**
   - Create a new database (example: `trading_card_library`).
   - Import the provided `schema.sql` file (if available) or manually create:
     - `cards` table
     - `market_prices` table
     - `card_types` table

4. **Folder Permissions**
   - Ensure `uploads/` directory exists and is writable:
     ```bash
     chmod 775 uploads
     sudo chown daemon:daemon uploads
     ```

---

## ⚙️ Project Structure

```
/tradingcards/
├── add-card.php
├── edit-card.php
├── view-cards.php
├── update-prices.php
├── delete-card.php
├── index.html
├── includes/
│   └── db.php
│   └── header.php (optional shared header)
├── uploads/ (background images)
├── styles.css
├── README.md
```

---

## 🚀 Usage

- Visit `http://localhost/tradingcards/index.html` to get started.
- Manage your trading card collection.
- Upload a background image to personalize your site.
- Update live prices using the **🔄 Update Prices** button inside View Cards.

---

## 🔥 Planned Features

- Background themes based on TCG type
- Price history graphs
- Export collection to CSV or PDF
- Search, filter, and sort functionality

---

## 👨‍💻 Developer Notes

- Built with PHP + MySQL
- Uses pure HTML, CSS, and vanilla JavaScript (no frameworks)
- Designed to be modular and expandable

---

## 📜 License

This project is open-source under the MIT License.  
Feel free to use, expand, and customize it!

---