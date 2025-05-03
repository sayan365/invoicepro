# InvoicePro

**InvoicePro** is a PHP/MySQL web application for managing clients and generating PDF invoices. It features a clean, responsive dashboard and full CRUD support for both clients and invoices.

---

## 🔑 Key Features

- **User Authentication**  

  - Register, Login, Logout  

- **Client Management** (`clients/` + root `index.php`)  

  - Add, edit & delete clients  

  - View client list on `index.php`  

- **Invoice Management** (`invoices/`)  

  - Create, edit & delete invoices  

  - Generate professional PDF invoices via TCPDF  

- **Backend Services** (`services/`)  

  - Centralized PHP scripts for handling all CRUD operations on clients & invoices  

- **Profile & Logo Uploads** (`uploads/`)  

  - Users can update their profile & company logo; files are stored in `uploads/`  

- **Dashboard** (`dashboard.php`)  

  - At-a-glance view of total clients, outstanding invoices, recent activity, etc.

---

## 🗂️ Project Structure

├── clients/ # Client-side pages for managing clients

│ ├── add-client.php

│ ├── edit-client.php

│ └── delete-client.php

│

├── invoices/ # Pages for invoice workflows

│ ├── add-invoice.php

│ ├── edit-invoice.php

│ └── delete-invoice.php

│

├── services/ # PHP service scripts (CRUD logic)

│ ├── client-service.php

│ ├── invoice-service.php

│ └── ...

│

├── includes/ # Shared components

│ ├── config.php # Database connection

│ └── sidebar.php # Dashboard sidebar UI

│

├── uploads/ # User-uploaded files (profile pics, logos)

│ └── [all uploaded images]

│

├── tcpdf/ # TCPDF library for PDF generation

│ └── ...

│

├── index.php # Entry: redirects/logins & lists clients

├── register.php # User registration

├── login.php # User login

├── logout.php # Session logout

├── profile.php # Profile edit + logo upload

├── dashboard.php # Main dashboard view

└── README.md # ← you are here!

yaml

Copy

Edit

---

## 🚀 Getting Started

1\. **Clone the repo**  

   ```bash

   git clone https://github.com/sayan365/invoicepro.git

   cd invoicepro

Create & import database

Create a MySQL database named invoicepro.

Import any provided schema (e.g. schema.sql) or run the CREATE TABLE scripts in the clients/ and invoices/ folders.

Configure DB connection

Edit includes/config.php with your credentials:

php

Copy

Edit

define('DB_SERVER',   'localhost');

define('DB_USERNAME', 'your_username');

define('DB_PASSWORD', 'your_password');

define('DB_NAME',     'invoicepro');

Install TCPDF

The TCPDF library is already included in tcpdf/. No extra setup needed.

Set permissions

Ensure uploads/ is writable by your web server:

bash

Copy

Edit

chmod -R 755 uploads/

Run on your local server

Place the project folder under your web root (e.g. htdocs or www) and browse to:

arduino

Copy

Edit

http://localhost/invoicepro/

🎯 Usage Workflow

Register a new account

Login to access the dashboard

Manage Clients

On index.php, view all clients

Use "Add Client" / "Edit" / "Delete" actions

Generate Invoices

Go to the Invoices section

Create an invoice linked to a client, then download the PDF

Profile Settings

Update your personal/company info & upload a logo on profile.php

🤝 Contributing

Fork the repository

Create a feature branch (git checkout -b feature/my-feature)

Commit your changes (git commit -m "Add awesome feature")

Push to your branch (git push origin feature/my-feature)

Open a pull request

📄 License

This project is licensed under the MIT License.

Built with ❤️ by @sayan365
