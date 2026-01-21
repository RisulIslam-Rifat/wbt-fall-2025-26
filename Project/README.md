# BloodLink - Blood Donation Management System

BloodLink is a web-based platform dedicated to connecting blood donors with those in critical need. It streamlines the donation process, facilitates real-time matching, and helps manage blood requests efficiently.

## 🚀 Features

-   **User Roles**: Distinct flows for **Donors**, **Finders** (Recipients), and **Admins**.
-   **Registration & Login**: Secure account creation and authentication.
-   **Blood Requests**: Finders can post blood requests with hospital and contact details.
-   **Donor Matching**: Efficient system to find compatible donors.
-   **Admin Dashboard**: Manage users, requests, and platform activity.
-   **Responsive Design**: Built with Tailwind CSS for a seamless experience on all devices.
-   **Secure**: Basic security implementations for user data.

## 🛠️ Tech Stack

-   **Frontend**: HTML5, Tailwind CSS
-   **Backend**: PHP
-   **Database**: MySQL
-   **Server**: Apache (via XAMPP/WAMP)

## 📋 Prerequisites

Before you begin, ensure you have the following installed:
-   **XAMPP** or **WAMP** server (includes Apache and MySQL).
-   A web browser.

## ⚙️ Installation & Setup

1.  **Clone/Download the Repository**
    -   Place the project folder inside your web server's root directory (e.g., `htdocs` for XAMPP).
    -   Example path: `C:/xampp/htdocs/webtech project/`

2.  **Database Configuration**
    -   Open **phpMyAdmin** (usually at `http://localhost/phpmyadmin`).
    -   Create a new database named `bloodlink_db`.
    -   Import the `sql/database.sql` file provided in the project folder into this new database.
        -   Go to the "Import" tab in phpMyAdmin.
        -   Choose the `database.sql` file.
        -   Click "Import".

3.  **Verify Connection Settings**
    -   Open `php/db_connect.php`.
    -   Ensure the credentials match your local MySQL setup:
        ```php
        $servername = "localhost";
        $username   = "root";      // Default XAMPP user
        $password   = "";          // Default XAMPP password is empty
        $dbname     = "bloodlink_db";
        ```

4.  **Run the Application**
    -   Start **Apache** and **MySQL** from your XAMPP Control Panel.
    -   Open your browser and navigate to:
        `http://localhost/webtech project/index.html`

## 📖 Usage

### For Donors
1.  **Sign Up**: Create an account via `Sign Up` > `Donor`.
2.  **Profile**: Fill in health details (Blood Group, Last Donation, etc.).
3.  **Donate**: Wait for requests or browse active needs.

### For Finders (Recipients)
1.  **Sign Up**: Create an account via `Sign Up` > `Finder`.
2.  **Request Blood**: detailed form with patient location and blood group needed.
3.  **Connect**: Get matched with available donors.

### For Admins
-   **Login**: Use the admin credentials to access the dashboard.
-   **Manage**: Approve/Decline user registrations and monitor requests.

## 🗄️ Database Schema

The system uses `bloodlink_db` with the following key tables:
-   `users`: Stores all user data (Admin, Donor, Finder) with role-specific fields.
-   `blood_requests`: Stores details of blood needs (group, hospital, contacts).

## 🧪 Default Login Credentials (for Testing)

You can use the sample data from `database.sql` to test:

-   **Admin**:
    -   Email: `admin@bloodlink.com`
    -   Password: `admin123`

-   **Donor**:
    -   Email: `john@example.com`
    -   Password: `donor123`

-   **Finder**:
    -   Email: `sarah@example.com`
    -   Password: `finder123`

---
© 2026 BloodLink System.
