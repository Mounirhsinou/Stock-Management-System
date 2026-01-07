# Stock Management System

A production-ready inventory management system built with pure PHP (MVC architecture), MySQL, HTML5, CSS3, and Vanilla JavaScript. Designed for small businesses with role-based access control and comprehensive stock tracking.

## 🚀 Features

- **Authentication System**: Secure login/logout with password hashing
- **Role-Based Access Control**: Admin, Staff, and Viewer roles
- **Product Management**: Complete CRUD operations with SKU tracking
- **Stock Movements**: IN/OUT transactions with atomic updates
- **Low Stock Alerts**: Automatic notifications for products below minimum quantity
- **Dashboard**: Real-time statistics and recent activity
- **CSV Export**: Export product data for external use
- **Responsive Design**: Mobile-friendly interface
- **Security**: PDO prepared statements, input validation, CSRF protection

## 📋 Requirements

- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Apache**: with mod_rewrite enabled
- **Extensions**: PDO, PDO_MySQL

## 🛠️ Installation

### 1. Clone or Download

```bash
git clone <repository-url>
# or download and extract the ZIP file
```

### 2. Database Setup

1. Create a new MySQL database:
```sql
CREATE DATABASE stock_management_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Import the schema:
```bash
mysql -u root -p stock_management_system < database/schema.sql
```

Or use phpMyAdmin to import `database/schema.sql`

### 3. Configuration

1. Copy the configuration template:
```bash
cp config/config.example.php config/config.php
```

2. Edit `config/config.php` with your database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'stock_management_system');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

3. Update `APP_URL` to match your installation:
```php
define('APP_URL', 'http://localhost/Stock%20Management%20System');
```

### 4. Set Permissions

Ensure the web server has read access to all files:
```bash
chmod -R 755 .
```

### 5. Access the Application

Navigate to: `http://localhost/Stock%20Management%20System`

## 👥 Default User Accounts

| Role | Username | Password |
|------|----------|----------|
| Admin | admin | admin123 |
| Staff | staff | staff123 |
| Viewer | viewer | viewer123 |

**⚠️ IMPORTANT**: Change these passwords immediately after deployment!

## 🔐 User Roles & Permissions

### Admin
- Full access to all features
- Create, edit, delete products
- Manage stock IN/OUT
- View all reports and statistics
- Export data

### Staff
- View products
- Manage stock IN/OUT operations
- View dashboard and reports
- Cannot create/edit/delete products

### Viewer
- Read-only access
- View products and stock levels
- View dashboard statistics
- Cannot perform any modifications

## 📁 Project Structure

```
Stock Management System/
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   └── js/
│       └── main.js            # Client-side JavaScript
├── config/
│   ├── config.php             # Configuration (git-ignored)
│   ├── config.example.php     # Configuration template
│   └── Database.php           # PDO database connection
├── controllers/
│   ├── AuthController.php     # Authentication logic
│   ├── DashboardController.php
│   ├── ProductController.php
│   └── StockController.php
├── models/
│   ├── User.php               # User model
│   ├── Product.php            # Product model
│   └── StockMovement.php      # Stock movement model
├── views/
│   ├── auth/
│   │   └── login.php
│   ├── dashboard/
│   │   └── index.php
│   ├── products/
│   │   ├── index.php
│   │   ├── form.php
│   │   └── view.php
│   └── stock/
│       ├── index.php
│       └── form.php
├── includes/
│   ├── header.php             # Common header
│   ├── footer.php             # Common footer
│   └── helpers.php            # Utility functions
├── database/
│   └── schema.sql             # Database schema
├── index.php                  # Main entry point
├── .htaccess                  # Apache configuration
├── .gitignore
└── README.md
```

## 🔧 Configuration Options

Edit `config/config.php` to customize:

- **Database settings**: Host, name, user, password
- **Application settings**: Name, URL, environment
- **Session settings**: Lifetime, name
- **Pagination**: Items per page
- **Security**: Password minimum length

## 🚀 Deployment

### Shared Hosting

1. Upload all files to your hosting directory
2. Create MySQL database via cPanel
3. Import `database/schema.sql`
4. Update `config/config.php` with production credentials
5. Set `APP_ENV` to `'production'`
6. Ensure `.htaccess` is uploaded and working

### Production Checklist

- [ ] Change all default passwords
- [ ] Set `APP_ENV` to `'production'`
- [ ] Update database credentials
- [ ] Enable HTTPS (uncomment in `.htaccess`)
- [ ] Set proper file permissions
- [ ] Test all functionality
- [ ] Set up regular database backups
- [ ] Monitor error logs

## 🔒 Security Features

- **Password Hashing**: bcrypt algorithm
- **Prepared Statements**: All SQL queries use PDO prepared statements
- **Input Validation**: Server-side validation for all inputs
- **CSRF Protection**: Token-based protection for forms
- **Session Security**: Secure session management with timeout
- **Role-Based Access**: Granular permission control
- **SQL Injection Prevention**: PDO with bound parameters
- **XSS Prevention**: Output escaping with htmlspecialchars

## 📊 Database Schema

### Tables

1. **users**: User accounts with roles
2. **products**: Product catalog with pricing and stock
3. **stock_movements**: Transaction history for stock changes

### Relationships

- `products.created_by` → `users.id`
- `stock_movements.product_id` → `products.id`
- `stock_movements.created_by` → `users.id`

## 🎯 Future Enhancements

This system is designed to be easily extended into a mini ERP:

- **Suppliers Management**: Track product suppliers
- **Purchase Orders**: Create and manage orders
- **Sales Module**: Invoice generation and tracking
- **Reporting**: Advanced analytics and charts
- **Multi-warehouse**: Support for multiple locations
- **Barcode Scanning**: Quick product lookup
- **API**: RESTful API for integrations
- **Notifications**: Email/SMS alerts for low stock

## 🐛 Troubleshooting

### Database Connection Error
- Verify database credentials in `config/config.php`
- Ensure MySQL service is running
- Check database exists and user has permissions

### Page Not Found (404)
- Ensure `.htaccess` is uploaded
- Verify `mod_rewrite` is enabled in Apache
- Check file permissions

### Session Issues
- Verify session directory is writable
- Check PHP session configuration
- Clear browser cookies

### Blank Page
- Enable error reporting in `config/config.php`
- Check PHP error logs
- Verify all required PHP extensions are installed

## 📝 License

This project is open-source and available for commercial and personal use.

## 👨‍💻 Support

For issues, questions, or contributions, please contact your development team.

---

**Built with ❤️ for small businesses**
