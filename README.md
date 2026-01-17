# Web-Based Inventory Management System

This project is a simple, file-based inventory management system created with PHP, HTML, CSS, and JavaScript. It allows you to add, view, edit, and delete inventory items.

## Project Files

- `index.html`: The main entry point and user interface.
- `style.css`: Styles for the user interface.
- `script.js`: Frontend logic for interacting with the backend.
- `api.php`: The backend API that handles all CRUD (Create, Read, Update, Delete) operations.
- `db_config.php`: The database connection configuration.
- `database.sql`: The SQL script to create the necessary database and table.
- `README.md`: This setup guide.

## Prerequisites

You need a local web server environment with PHP and MySQL. [XAMPP](https://www.apachefriends.org/index.html) is a popular choice that provides everything you need.

- **XAMPP** (with Apache and MySQL started)
- A web browser

## Setup and Installation

Follow these steps to get the application running.

### 1. Place Files in `htdocs`

- Make sure all the project files (`index.html`, `style.css`, etc.) are located in a folder inside your XAMPP's `htdocs` directory.
- For example: `C:/xampp/htdocs/inventory-system/`

### 2. Create the Database

You need to create the MySQL database and the `products` table. You can do this easily using `phpMyAdmin`, which comes with XAMPP.

1.  **Open phpMyAdmin**: Open your web browser and go to `http://localhost/phpmyadmin/`.
2.  **Create the Database**:
    - Click on the **Databases** tab.
    - Under "Create database", enter the name `inventory_db` and click **Create**.
3.  **Import the SQL File**:
    - Select the `inventory_db` database you just created from the left-hand menu.
    - Click on the **Import** tab at the top.
    - Click on "Choose File" and select the `database.sql` file from this project folder.
    - Scroll down and click the **Go** button.

This will create the `products` table and populate it with some sample data.

### 3. Access the Application

- Open your web browser and navigate to the project folder.
- For example, if your folder is named `inventory-system`, go to: `http://localhost/inventory-system/`

You should now see the Inventory Management System interface and be able to add, edit, and delete items.

## How It Works

- The `index.html` page is loaded, which in turn loads the `script.js`.
- The `script.js` file immediately sends an AJAX request to `api.php?action=get_items`.
- The `api.php` script queries the MySQL database and returns all inventory items as a JSON object.
- The `script.js` receives the JSON data and dynamically builds the HTML table to display the items.
- All subsequent actions (adding, updating, deleting) are handled via AJAX calls to the `api.php` script, which prevents the need for page reloads.
