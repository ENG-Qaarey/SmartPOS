# SmartPOS - Point of Sale System

A complete web-based Point of Sale (POS) application built with PHP and MySQL, designed for small-to-medium businesses to manage sales, products, inventory, and customers.

## 🚀 Features

### 🔐 Authentication & User Management
- **Admin and Cashier Login**: Role-based access control
- **Session-based Security**: Secure session management
- **Password Hashing**: Bcrypt encryption for user passwords
- **User Management**: Admins can create, edit, and manage user accounts

### 🛒 Sales Management
- **Invoice-style Interface**: Professional sales interface
- **Auto Calculation**: Automatic totals and change calculation
- **Product Selection**: Easy product search and quantity selection
- **Discount Support**: Apply discounts to sales
- **Payment Methods**: Support for cash and card payments
- **Receipt Generation**: Print-friendly sales receipts

### 📦 Product & Inventory Management
- **Product CRUD**: Add, edit, delete products
- **Category Management**: Organize products by categories
- **Stock Management**: Track inventory levels
- **Low Stock Alerts**: Automatic notifications for low stock items
- **Stock Thresholds**: Configurable low stock warnings

### 👥 Customer Management
- **Customer CRUD**: Add, edit, delete customer information
- **Purchase History**: View complete customer transaction history
- **Customer Analytics**: Track customer spending patterns

### 📊 Reporting & Analytics
- **Sales Reports**: Daily, weekly, monthly sales reports
- **Date Range Filtering**: Customizable report periods
- **CSV Export**: Export sales data to CSV format
- **Visual Charts**: Interactive sales charts using Chart.js
- **Sales Analytics**: Total sales, transactions, discounts, and averages

### 🧑‍💼 User Roles
- **Admin**: Full access to all features including reports and user management
- **Cashier**: Limited access to sales, products, and customers

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **UI Framework**: Bootstrap 5.1.3
- **Icons**: Font Awesome 6.0.0
- **Charts**: Chart.js

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Modern web browser

## 🚀 Installation

1. **Clone or Download** the project to your web server directory
2. **Configure Database**:
   - Create a MySQL database named `smartpos`
   - Update database credentials in `config/database.php` if needed
3. **Set Permissions**:
   - Ensure web server has read/write permissions
4. **Access the Application**:
   - Navigate to `http://your-domain/smartpos/`
   - Login with default admin credentials

## 🔑 Default Login
**admin**
- **Username**: `admin`
- **Password**: `admin123`

     create your Own **Cashiers** like on **below**
| --------------------------------------------------
| **Cashier1**                                     |
| - **Username**: `Cashier`                        |
| - **Password**: `123456789`                      |
| --------------------------------------------------
| **Cashier2**                                     |
| - **Username**: `abdi`                           |
| - **Password**: `1234567890`                     |
| --------------------------------------------------

**⚠️ Important**: Change the default password after first login!

## 📁 Project Structure

```
smartpos/
├── config/
│   └── database.php          # Database configuration
├── includes/
│   ├── functions.php         # Utility functions
│   ├── header.php           # Header template
│   └── footer.php           # Footer template
├── pages/
│   ├── dashboard.php        # Dashboard page
│   ├── sales.php           # Sales management
│   ├── products.php        # Product management
│   ├── customers.php       # Customer management
│   ├── reports.php         # Sales reports
│   └── users.php           # User management
├── index.php               # Main application entry
├── login.php              # Login page
├── logout.php             # Logout script
└── README.md              # This file
```

## 🗄️ Database Schema

The application automatically creates the following tables:

- **users**: User accounts and authentication
- **categories**: Product categories
- **products**: Product inventory
- **customers**: Customer information
- **sales**: Sales transactions
- **sale_items**: Individual sale line items

## 🎯 Key Features Explained

### Sales Process
1. **New Sale**: Click "New Sale" from dashboard
2. **Add Products**: Select products and quantities
3. **Apply Discounts**: Optional discount application
4. **Complete Sale**: Choose payment method and finalize
5. **Print Receipt**: Generate and print sales receipt

### Inventory Management
- **Add Products**: Create new products with categories
- **Stock Tracking**: Monitor inventory levels
- **Low Stock Alerts**: Automatic warnings for low stock
- **Stock Updates**: Automatic stock reduction on sales

### Customer Management
- **Customer Profiles**: Store customer information
- **Purchase History**: Track all customer transactions
- **Customer Analytics**: View spending patterns

### Reporting
- **Sales Reports**: Filter by date ranges
- **Export Data**: Download reports as CSV
- **Visual Analytics**: Interactive charts and graphs
- **Summary Statistics**: Key performance indicators

## 🔒 Security Features

- **Password Hashing**: Bcrypt encryption
- **Session Management**: Secure session handling
- **SQL Injection Prevention**: Prepared statements
- **XSS Protection**: Input sanitization
- **Role-based Access**: Admin/Cashier permissions

## 🎨 User Interface

- **Responsive Design**: Works on desktop and mobile
- **Modern UI**: Clean, professional interface
- **Intuitive Navigation**: Easy-to-use menu system
- **Real-time Updates**: Dynamic calculations and updates

## 📈 Business Benefits

- **Streamlined Sales**: Faster transaction processing
- **Inventory Control**: Better stock management
- **Customer Insights**: Track customer behavior
- **Financial Reporting**: Comprehensive sales analytics
- **Multi-user Support**: Multiple cashiers and admins

## 🛠️ Customization

The application is designed to be easily customizable:

- **Styling**: Modify CSS in header.php
- **Features**: Add new functionality to existing pages
- **Database**: Extend database schema for additional features
- **Reports**: Create custom report templates

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**:
   - Check database credentials in `config/database.php`
   - Ensure MySQL service is running

2. **Permission Errors**:
   - Ensure web server has read/write permissions
   - Check file ownership

3. **Login Issues**:
   - Verify default credentials: admin/admin123
   - Check session configuration

### Support

For issues or questions:
1. Check the error logs
2. Verify database connectivity
3. Ensure all requirements are met

## 📝 License

This project is open source and available under the MIT License.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit pull requests or open issues for bugs and feature requests.

---

**SmartPOS** - Empowering businesses with efficient point of sale management. 


<img width="536" height="362" alt="image" src="https://github.com/user-attachments/assets/6b6ed2fa-6ba2-4f4f-b37f-bb07782f6897" />
