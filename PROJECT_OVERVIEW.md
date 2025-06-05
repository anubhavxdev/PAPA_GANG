# AquaSave Project Overview

## 1. Project Purpose and Goals

AquaSave is a web application designed to help users monitor, track, and optimize their water usage. The primary goal is to promote water conservation by providing users with tools to understand their consumption patterns, set conservation goals, and access relevant tips and information.

**Target Users:**
- **Residential Users:** Homeowners or tenants who want to reduce water consumption and save on utility bills.
- **Environmentally Conscious Individuals:** People looking for ways to minimize their environmental footprint.
- **Service Providers/Experts (Future Scope):** While bestowal focuses on end-users, the project description in README.md mentions administrators validating key participants like service providers and experts, suggesting a potential future direction.

**Problems Solved:**
- **Lack of Awareness:** Many people are unaware of their actual water consumption and areas where they can save. AquaSave provides data and insights to address this.
- **Difficulty in Tracking Usage:** Manual tracking is cumbersome. AquaSave automates this process (conceptually, by allowing users to register and connect devices - though actual device integration is not implemented).
- **Motivation for Conservation:** By allowing users to set goals and see their progress, AquaSave aims to motivate users to adopt water-saving habits.
- **Access to Information:** Provides users with actionable tips for water conservation.

## 2. System Architecture

AquaSave is a web application built with a traditional client-server architecture.

**Frontend (Client-Side):**
- **HTML5:** Structures the web pages.
- **CSS3:** Styles the application, with [Tailwind CSS](https://tailwindcss.com/) used as the primary CSS framework for rapid UI development. Custom styles are located in `Frontend/css/styles.css`.
- **JavaScript (ES6):** Handles client-side interactivity, form validation, and dynamic content updates (e.g., charts). Key scripts include:
    - `Frontend/js/main.js`: Core interactions and event handling.
    - `Frontend/js/forms.js`: Form validation and submission logic.
    - `Frontend/js/charts.js`: Rendering charts (likely using a library like Chart.js, though not explicitly specified in dependencies).
- **Pages:** The frontend consists of several HTML and PHP pages located in `Frontend/` and `Frontend/pages/`. `index.php` serves as the main landing page.

**Backend (Server-Side):**
- **PHP:** Powers the server-side logic, including user authentication, data processing, and interaction with the database. PHP scripts are found throughout the `Frontend/pages/` directory (e.g., `login.php`, `register.php`, `admin.php`) and the root `Frontend/index.php`.
- **Session Management:** PHP sessions are used to manage user login states.

**Database:**
- **MySQL:** Used as the relational database to store user data, device information (conceptual), goals, etc.
- **Schema:** The database schema is defined and initialized in `Frontend/pages/config.php`. This script also creates an initial `users` table and an admin account if they don't exist. The `aquasave_db.sql` file might contain a full schema dump or backup.

**Interaction Flow:**
1. The user interacts with the frontend in their browser.
2. Frontend JavaScript enhances user experience with client-side validation and dynamic updates.
3. For actions requiring data or authentication (e.g., login, registration, fetching dashboard data), the frontend makes requests to PHP scripts on the server.
4. PHP scripts process these requests, interact with the MySQL database (via `config.php` for connection), and manage user sessions.
5. PHP scripts then typically render HTML pages (often mixing PHP with HTML) or return data (e.g., JSON for AJAX calls, though specific AJAX usage isn't detailed yet) to the frontend.

## 3. Key Features

AquaSave offers a range of features to help users manage and conserve water:

- **User Authentication:**
    - **Registration (`Frontend/pages/register.php`):** New users can create an account by providing their first name, last name, email, password, location, and user type.
    - **Login (`Frontend/pages/login.php`):** Registered users can log in to access their personalized dashboard and features.
    - **Logout (`Frontend/pages/logout.php`):** Users can securely log out of their accounts.
    - **Session Management:** PHP sessions maintain user login status across the application.

- **Dashboard (`Frontend/pages/dashboard.php`):**
    - Displays an overview of the user's water usage (conceptually).
    - Likely place for charts and statistics once data is available (integrates with `Frontend/js/charts.js`).
    - Provides navigation to other sections of the application.

- **Device Management (`Frontend/pages/devices.html`):**
    - Placeholder for users to list and manage their water-consuming devices. (Note: The backend logic for actual device data storage and management beyond user accounts is not explicitly detailed in the provided PHP scripts but is a conceptual feature).

- **Goal Setting (`Frontend/pages/goals.html`):**
    - Allows users to set water conservation goals.
    - Placeholder for tracking progress towards these goals.

- **Water-Saving Tips (`Frontend/pages/tips.html`):**
    - Provides users with useful tips and suggestions for reducing water consumption.

- **User Profile Management (`Frontend/pages/profile.php`):**
    - Allows users to view and potentially update their profile information.

- **Admin Panel (`Frontend/pages/admin.php`):**
    - Accessible to users with admin privileges (`is_admin` flag in the `users` table).
    - **User Management:**
        - View list of users (`get_users.php`).
        - Edit user details (`edit_user.php`).
        - Delete users (`delete_user.php`).
        - View individual user details (`view_user.php`).
    - **Statistics:** View overall platform statistics (`get_statistics.php`).
    - **Water Usage Monitoring:** Potentially view aggregated water usage data (`get_water_usage.php`).
    - Admin users are created via `config.php` (default admin) or can be set in the database.

## 4. Database Schema

The primary database schema is defined and initialized in `Frontend/pages/config.php`. The system uses MySQL.

**Key Tables:**

- **`users` Table:** Stores information about registered users.
    - `id`: INT, NOT NULL, PRIMARY KEY, AUTO_INCREMENT - Unique identifier for the user.
    - `first_name`: VARCHAR(50), NOT NULL - User's first name.
    - `last_name`: VARCHAR(50), NOT NULL - User's last name.
    - `email`: VARCHAR(100), NOT NULL, UNIQUE - User's email address (used for login).
    - `password`: VARCHAR(255), NOT NULL - Hashed password for the user.
    - `location`: VARCHAR(50), NOT NULL - User's location (e.g., for localized tips).
    - `user_type`: VARCHAR(20), NOT NULL, DEFAULT 'residential' - Type of user (e.g., residential, commercial).
    - `is_admin`: TINYINT(1), NOT NULL, DEFAULT 0 - Flag indicating if the user has administrative privileges (1 for admin, 0 for regular user).
    - `created_at`: DATETIME, DEFAULT CURRENT_TIMESTAMP - Timestamp of when the user account was created.

**Database Initialization:**
- The `Frontend/pages/config.php` script attempts to create the database (`aquasave`) and the `users` table if they do not already exist.
- It also creates a default admin user (`admin@aquasave.com` with password `admin123`) if no admin user is found.

**SQL Dump File:**
- The `aquasave_db.sql` file in the root directory may contain a more complete database schema, including other tables related to devices, goals, or water usage data, or it might be a backup of the database. This file should be consulted for a full understanding of the database structure if it contains more than the `users` table.

## 5. Setup and Installation Instructions

To set up and run the AquaSave project locally, you will need a web server environment that supports PHP and MySQL (e.g., XAMPP, WAMP, MAMP, or a custom LEMP/LAMP stack).

**Prerequisites:**
- A web server (Apache, Nginx, etc.)
- PHP (ensure the version is compatible with the project's code, likely PHP 7.x or higher)
- MySQL (or MariaDB) database server
- A web browser

**Steps:**

1.  **Clone the Repository:**
    ```bash
    git clone https://github.com/anubhavxdev/PAPA_GANG.git
    cd PAPA_GANG
    ```
    (Assuming `PAPA_GANG` is the repository name as per the `README.md`. Adjust if the actual repository name is different.)

2.  **Place Project Files in Web Server Directory:**
    - Move the entire project folder (or the contents of the `Frontend` directory if you only want to serve that part directly, though the PHP files are inside `Frontend`) into your web server's document root (e.g., `htdocs` for XAMPP, `www` for WAMP/MAMP).
    - For example, you might have `C:/xampp/htdocs/AquaSaveProject/` with the project files inside.

3.  **Database Setup:**
    -   **Start your MySQL server.**
    -   **Option A (Automatic Setup via `config.php`):**
        1.  Ensure the MySQL credentials in `Frontend/pages/config.php` match your MySQL setup:
            ```php
            define('DB_SERVER', 'localhost'); // Usually correct
            define('DB_USERNAME', 'root');    // Default for XAMPP, adjust if needed
            define('DB_PASSWORD', '');        // Default for XAMPP, adjust if needed
            define('DB_NAME', 'aquasave');
            ```
        2.  When you first access the application through your web browser (e.g., `http://localhost/AquaSaveProject/Frontend/`), the `config.php` script (included by `index.php` and other pages) will attempt to:
            - Create the `aquasave` database if it doesn't exist.
            - Create the `users` table if it doesn't exist.
            - Create a default admin user (`admin@aquasave.com`, password: `admin123`) if no admin exists.
    -   **Option B (Manual Setup using `aquasave_db.sql`):**
        1.  If `aquasave_db.sql` contains the full database schema and potentially data, you can import it using a MySQL administration tool like phpMyAdmin or the MySQL command line.
        2.  Open phpMyAdmin (usually accessible via `http://localhost/phpmyadmin`).
        3.  Create a new database named `aquasave`.
        4.  Select the `aquasave` database, then go to the "Import" tab.
        5.  Choose the `aquasave_db.sql` file from the project's root directory and click "Go" or "Import".
        6.  If you use this method, verify that the credentials in `Frontend/pages/config.php` still correctly point to this database. You might not need the auto-creation parts of `config.php` if the SQL file sets everything up.

4.  **Access the Application:**
    - Open your web browser and navigate to the project's `Frontend` directory on your local server. For example:
        - `http://localhost/PAPA_GANG/Frontend/`
        - `http://localhost/AquaSaveProject/Frontend/` (if you renamed the project folder)

5.  **Testing:**
    - Try registering a new user.
    - Log in with the default admin credentials (`admin@aquasave.com` / `admin123`) or your newly registered user.
    - Navigate through the different pages to ensure they load correctly.

**Troubleshooting:**
- **PHP Errors:** Check your PHP error logs if pages don't load correctly. Ensure all required PHP extensions are enabled (e.g., `mysqli`).
- **Database Connection Errors:** Double-check the database credentials in `Frontend/pages/config.php`. Ensure your MySQL server is running and accessible.
- **CSS/JS Issues:** Use your browser's developer tools (F12) to check for console errors or issues with loading static assets. Ensure paths are correct.
