# PHP E-Commerce System

A web-based e-commerce and online sales information system built with **Native PHP** and **MySQL**.  
This project was developed as a final assignment for the **Web Programming Practice** course.

It includes secure authentication, session and cookie-based access handling, product management, shopping cart flow, and order processing.

> Status: 🚧 In Development (Academic Project)

---

## Project Overview

This application is designed to simulate a simple real-world online store where:

- **Customers** can register, log in, browse products, add items to cart, and place orders.
- **Admins** can manage products, categories, and monitor transactions from an admin panel.

The system emphasizes practical implementation of core web development concepts such as:

- Native PHP architecture
- Relational database modeling
- Authentication & authorization
- CRUD operations
- Session & cookies management
- Form validation and secure input handling

---

## Features

### Customer Features

- User registration and login
- Logout and session handling
- Remember Me (cookie-based)
- Product catalog browsing
- Product detail view
- Add to cart / manage cart items
- Checkout process
- Order history

### Admin Features

- Admin authentication
- Admin dashboard
- Product CRUD
- Category CRUD
- Order management
- Transaction monitoring

### Security Features

- Session-based authentication
- Role-based authorization
- Input validation
- Cookie handling for persistent login
- Basic session protection

---

## Tech Stack

- **Backend:** Native PHP
- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap 5
- **Database:** MySQL / MariaDB
- **Development Tools:** VS Code, Git, GitHub, XAMPP/Laragon, phpMyAdmin

---

## Setup & Installation

### 1) Clone the repository

```bash
git clone https://github.com/Shevanka/php-ecommerce-system.git
cd php-ecommerce-system
```

### 2) Start local server

Use one of the following:

- XAMPP
- Laragon

Make sure:

- Apache is running
- MySQL is running

### 3) Create database

```sql
CREATE DATABASE penjualan_online;
```

### 4) Import database schema

Import this file into your MySQL server:

```text
database/penjualan_online.sql
```

### 5) Configure database connection

Edit the database configuration file if needed:

```text
config/database.php
```

Typical local values:

```php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "penjualan_online";
```

### 6) Run the app

Open in browser:

```text
http://localhost/php-ecommerce-system
```

> If your local folder name is different, adjust the URL path accordingly.

---

## Usage

### As Customer

1. Register a new account
2. Log in
3. Browse available products
4. Add products to cart
5. Complete checkout
6. View order history

### As Admin

1. Log in with admin account
2. Manage categories
3. Manage products
4. Manage incoming orders
5. Monitor transaction data

---

## Screenshots

Screenshots are not yet available in this repository. Recommended sections to document:

- Login page
- Register page
- Product listing page
- Product detail page
- Shopping cart page
- Checkout page
- Admin dashboard
- Product/category management pages

> You can add screenshots later in a `docs/screenshots/` folder and link them here.

---

## Contribution

Contributions are welcome for educational improvements.

1. Fork this repository
2. Create a feature branch (`feature/your-feature-name`)
3. Commit your changes with clear messages
4. Push to your branch
5. Open a Pull Request

### Suggested Commit Convention

```text
type: short description
```

Examples:

- `feat: add checkout validation`
- `fix: resolve session timeout bug`
- `docs: improve installation guide`
- `refactor: simplify database connection`

---

## License

This project is intended for **academic and learning purposes**.

If you plan to reuse this code in another project, please provide proper attribution to the original repository.

---

## Author

Developed by the project team:

- **Shevanka Bagus D. K**
- **Kristian Utama Putra**
