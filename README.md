# 🏎️ F1 Runner - Web Browser Game

![Project Status](https://img.shields.io/badge/status-completed-brightgreen)
![Tech Stack](https://img.shields.io/badge/php-mysql-blue)
![Tech Stack](https://img.shields.io/badge/javascript-vanilla-yellow)

**F1 Runner** is a vertical scrolling endless runner game developed as a final project for the **Web Programming** course at University.
The project combines a dynamic **Vanilla JavaScript** frontend with a robust **PHP/MySQL** backend to handle user sessions, score persistence, and game logic security.

---

## 🎮 Game Features

* **Endless Gameplay:** Dodge obstacles and survive as long as possible. Speed increases over time.
* **Shop:** Buy cars and drivers and build your team. 
* **Garage System:** Manage cars and drivers, select them or **Upgrade** the different stats of your car (Speed, Reliability, Pit Crew).
* **Score & Currency:** Earn coins based on performance to upgrade your garage.
* **Audio Manager:** Dynamic sound effects and background music management.
* **Responsive Controls:** Support for keyboard (Arrow Keys) to change lane during the game and UI buttons.

## ⚙️ Technical Highlights

This isn't just a game; it's a full-stack web application. Key engineering features include:

### 🛡️ Security & Anti-Cheat
* **Server-Side Validation:** The game implements a strict **Time-Based Anti-Cheat system**. When a score is submitted, the server calculates the maximum possible score based on the match duration. If the submitted score exceeds this limit (e.g., via memory manipulation), the request is rejected.
* **SQL Injection Protection:** All database interactions use **PDO Prepared Statements** to prevent injection attacks.
* **Session Security:** User authentication is managed via PHP Sessions.

### ⚡ Performance & Async
* **Rendering:** The game loop uses `requestAnimationFrame` for smooth 60fps rendering, decoupled from the logic loop.
* **Asynchronous Saving:** Game data is saved using the **Fetch API** (AJAX), allowing for seamless "Save & Retry" or "Save & Exit" functionality without blocking the UI.
* **Dynamic Hitboxes:** Collision detection calculates hitboxes dynamically based on the current CSS transformations.

---

## 🛠️ Tech Stack

* **Frontend:** HTML5, CSS3 (Animations), JavaScript (ES6+).
* **Backend:** PHP (7.4+).
* **Database:** MySQL / MariaDB.
* **Server:** Apache (XAMPP/WAMP environment).

---

## 🚀 Installation & Setup

To run this project locally, you need a PHP environment (like XAMPP, MAMP, or Docker).

### 1. Clone the Repository
Download the project files into your server's root directory (e.g., `htdocs` for XAMPP).

### 2. Database Setup
1.  Open **phpMyAdmin** (or your SQL client).
2.  Create a new database named `f1_runner`.
3.  Import the `f1_runner.sql` file provided in the db folder of this repository.

### 3. Configuration
Open `api/connessione.php` (or `db.php`) and configure your database credentials if necessary:

```php
$host = "localhost";
$username = "root";
$password = ""; // Your DB password (usually empty on XAMPP)
$dbname = "f1_runner_db";
```
## 🖼️ Screenshots 
### 1. Login/Sign up: 
<img width="1920" height="1140" alt="Screenshot 2026-02-03 201116" src="https://github.com/user-attachments/assets/143cabe9-e090-49c4-839d-ece0a3003750" />

### 2. Home page/Dashboard: 
<img width="1920" height="1140" alt="Screenshot 2026-02-03 201134" src="https://github.com/user-attachments/assets/bb9f6a37-2ec1-4ac0-94fd-fbb0ef85590f" />

### 3. Shop:
<img width="1920" height="1140" alt="Screenshot 2026-02-03 201219" src="https://github.com/user-attachments/assets/dd96124a-b9a9-468a-af9d-f3b1b536c664" />

### 4. Garage:
<img width="1920" height="1140" alt="Screenshot 2026-02-03 201150" src="https://github.com/user-attachments/assets/2011c08c-cbb3-4d13-b838-9d2745151d57" />

### 5. Upgrade: 
<img width="1920" height="1140" alt="Screenshot 2026-02-03 201227" src="https://github.com/user-attachments/assets/89e890b1-e8bc-4061-94bb-15c0fc57b036" />

### 6. Game: 
<img width="1920" height="1140" alt="Screenshot 2026-02-03 201249" src="https://github.com/user-attachments/assets/91a1e79e-66cd-4d63-a760-db6a4b958fff" />

## 👤 Author
Developed by **dlcbeatrix**. Computer Engineering Student.
