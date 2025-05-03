# InvoicePro

**InvoicePro** is a web-based invoicing system built with PHP and MySQL. It allows businesses and freelancers to manage clients, generate professional invoices in PDF format, and handle related data efficiently through a clean and responsive dashboard.

## Features

* **User Authentication** (Register/Login/Logout)
* **Client Management** (Add, Edit, Delete Clients)
* **Invoice Management** (Create, Edit, Delete Invoices)
* **PDF Invoice Generation** using TCPDF
* **Profile Management** with logo upload
* **Secure File Upload Handling**
* **Responsive Dashboard** for an organized view of all data

## Technologies Used

* **Backend**: PHP
* **Database**: MySQL
* **Frontend**: HTML, CSS, JavaScript
* **PDF Generation**: TCPDF

## Folder Structure

* `clients/`: Manages client-related functionality (add/edit/delete clients)
* `invoices/`: Manages invoice-related operations
* `services/`: Handles backend logic for clients and invoices (edit, delete, etc.)
* `includes/`: Contains database connection (`config.php`) and common UI components like the sidebar
* `uploads/`: Stores user-uploaded files (e.g., profile images, company logos)
* `tcpdf/`: External library used for generating PDF invoices

## Key Files

* `index.php`: Home page, primarily focused on client management
* `dashboard.php`: Displays an overview of clients and invoices
* `register.php`, `login.php`, `logout.php`: Handle user authentication
* `profile.php`: Allows users to update their profile information and logo

## Setup Instructions

1. **Clone the Repository**
   ```bash
   git clone https://github.com/sayan365/invoicepro.git
   ```

2. **Configure the Database**
   * Create a MySQL database named `invoicepro`
   * Import any `.sql` file provided (or set up the schema manually if not present)

3. **Update Database Credentials**
   * In `includes/config.php`, update the following:
   ```php
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'your_db_username');
   define('DB_PASSWORD', 'your_db_password');
   define('DB_NAME', 'invoicepro');
   ```

4. **Run the Application**
   * Host the project on a local server (e.g., XAMPP, WAMP)
   * Visit `http://localhost/invoicepro` in your browser

## Usage

1. **Register** as a new user
2. **Login** to access your dashboard
3. **Add Clients** via the index page
4. **Create Invoices** linked to clients
5. **Download Invoices** as PDF files
6. **Update Your Profile** and upload your company logo

## Screenshots

(You can add screenshots of the dashboard, client page, invoice generation, etc.)

## License

This project is licensed under the MIT License.

## Author

* [@sayan365](https://github.com/sayan365)
