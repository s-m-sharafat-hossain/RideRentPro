# 🚗 RideRent Pro - Vehicle Rental Management System

A comprehensive PHP-based vehicle rental management system with multi-role functionality, payment processing, and real-time tracking. Built for vehicle rental businesses to manage fleets, bookings, customers, drivers, and payments efficiently.

![PHP Version](https://img.shields.io/badge/PHP-8.0+-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)

## ✨ Features

### 🎯 Core Functionality

- **Multi-Role System**: Admin, Vehicle Owner, Driver, and Customer dashboards
- **Vehicle Management**: Add, edit, delete vehicles with image uploads
- **Booking System**: Complete booking lifecycle with status tracking
- **Payment Processing**: Multiple payment methods (bKash, Nagad, Card, Cash)
- **Payment Slips**: Professional receipt generation and printing
- **Rating System**: Customer reviews with real-time rating updates
- **Driver Assignment**: Admin-controlled driver assignment to bookings
- **Vehicle Approvals**: Admin approval workflow for new vehicle listings
- **Performance Analytics**: Track vehicle performance, earnings, and ratings
- **Search & Filter**: Advanced vehicle search and filtering
- **Responsive Design**: Mobile-friendly interface with dark mode support

### 💳 Payment System

- **Multiple Payment Methods**: bKash, Nagad, Credit/Debit Card, Cash/Manual
- **Flexible Payment Options**: Pay now or pay later during booking
- **Professional Receipts**: Thermal receipt format with print functionality
- **Real-time Updates**: Payment status reflects across all dashboards
- **Transaction Tracking**: Complete payment history and transaction IDs

### 📊 Analytics & Reporting

- **Vehicle Performance**: Individual vehicle ratings, bookings, and earnings
- **Owner Dashboard**: Fleet overview with total earnings and statistics
- **Driver Performance**: Track driver ratings and booking history
- **Admin Reports**: System-wide statistics and vehicle performance metrics
- **Rating Overview**: Comprehensive rating management for vehicles and drivers

## 🏗️ Project Structure

```text
RideRentPro/
├── config/                    # Configuration files
│   ├── database.php          # Database connection
│   └── database/
│       └── riderent_prodb.sql # Database schema
├── includes/                  # Shared components
│   ├── header.php            # Page header
│   ├── footer.php            # Page footer
│   ├── sidebar.php           # Dynamic sidebar
│   └── functions.php         # Helper functions
├── assets/                    # Static assets
│   ├── css/                  # Stylesheets
│   ├── js/                   # JavaScript files
│   ├── images/               # Image assets
│   └── uploads/              # User uploads
├── admin/                     # Admin panel
│   ├── dashboard.php         # Admin dashboard
│   ├── users.php             # User management
│   ├── drivers.php           # Driver management
│   ├── bookings.php          # Booking management
│   ├── reviews.php           # Review management
│   ├── ratings.php           # Ratings overview
│   ├── reports.php           # Reports page
│   ├── vehicle_approvals.php # Vehicle approval system
│   └── driver_assignment.php # Driver assignment
├── owner/                     # Vehicle owner panel
│   ├── dashboard.php         # Owner dashboard
│   ├── vehicles.php          # Vehicle management
│   ├── bookings.php          # Owner's bookings
│   ├── drivers.php           # Driver management
│   ├── vehicle_performance.php # Performance analytics
│   └── vehicles/             # Vehicle CRUD operations
│       ├── vehicle_list.php
│       ├── add_vehicle.php
│       ├── edit_vehicle.php
│       ├── delete_vehicle.php
│       └── vehicle_details.php
├── driver/                    # Driver panel
│   ├── dashboard.php         # Driver dashboard
│   ├── bookings.php          # Driver's bookings
│   ├── booking_details.php   # Booking details
│   ├── profile.php           # Driver profile
│   ├── availability.php      # Availability management
│   ├── earnings.php          # Earnings tracking
│   └── performance.php       # Performance metrics
├── customer/                  # Customer panel
│   ├── dashboard.php         # Customer dashboard
│   ├── vehicles.php          # Browse vehicles
│   ├── book_vehicle.php      # Book a vehicle
│   ├── bookings.php          # Customer's bookings
│   ├── booking_history.php   # Booking history
│   ├── payment.php           # Payment processing
│   ├── add_review.php        # Submit reviews
│   ├── compare.php           # Vehicle comparison
│   └── profile.php           # Customer profile
├── auth/                      # Authentication
│   ├── login.php             # Login handler
│   ├── register.php          # Registration handler
│   └── logout.php            # Logout handler
└── index.php                  # Landing page
```

## 🛠️ Technology Stack

- **Backend**: PHP 8.0+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Icons**: Font Awesome 6.4.0
- **Styling**: Custom CSS with responsive design
- **Server**: Apache or Nginx

## 📋 Requirements

- **PHP**: 8.0 or higher
- **MySQL**: 5.7 or higher
- **Web Server**: Apache or Nginx
- **PHP Extensions**: mysqli, gd (for image processing)
- **Browser**: Modern browser (Chrome, Firefox, Safari, Edge)

## 🚀 Installation

### Option 1: XAMPP (Recommended for Windows)

1. **Install XAMPP** from https://www.apachefriends.org/

2. **Start Apache and MySQL** services in XAMPP Control Panel

3. **Clone or download** the project

4. **Copy project folder** to `C:\xampp\htdocs\RideRentPro`

5. **Import database**:

   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Create database: `riderent_prodb`
   - Import `config/database/riderent_prodb.sql`

6. **Configure database** in `config/database.php`:

   ```php
   $servername = "localhost";
   $username = "root";
   $password = ""; // Your MySQL password
   $dbname = "riderent_prodb";
   ```

7. **Access application**: `http://localhost/RideRentPro`

### Option 2: Local PHP Server

1. **Install PHP and MySQL** on your system

2. **Start MySQL server**

3. **Import database**:

   ```bash
   mysql -u root -p riderent_prodb < config/database/riderent_prodb.sql
   ```

4. **Configure database** in `config/database.php`

5. **Start PHP server**:

   ```bash
   cd RideRentPro
   php -S localhost:8000
   ```

6. **Access application**: `http://localhost:8000`

### Option 3: Docker

```dockerfile
# Create a Dockerfile for containerized deployment
FROM php:8.0-apache
RUN docker-php-ext-install mysqli gd
COPY . /var/www/html/
EXPOSE 80
```

## 🔑 Default Credentials

### Admin

- **Email**: ornima5170@gmail.com
- **Password**: ornima123

### Vehicle Owner

- **Email**: masud@gmail.com
- **Password**: masud123

### Driver

- **Email**: rahim.driver@gmail.com
- **Password**: rahim123

### Customer

- **Email**: mahmud@gmail.com
- **Password**: mahmud123

## 👥 User Roles & Permissions

### 🛡️ Admin

- Full system management
- User management (create, edit, delete users)
- Vehicle approval and deletion
- Driver assignment to bookings
- Review moderation
- System-wide analytics and reports
- Payment status management

### 🚗 Vehicle Owner

- Add, edit, delete own vehicles
- View and manage bookings
- Track earnings and performance
- View vehicle ratings and reviews
- Manage driver availability
- Access performance analytics

### 👨‍✈️ Driver

- View assigned bookings
- Manage availability status
- Track earnings and performance
- View customer contact information
- Complete booking assignments
- Manage profile

### 👤 Customer

- Browse and search vehicles
- Book vehicles with driver options
- Process payments (multiple methods)
- View booking history
- Submit reviews and ratings
- Print payment receipts
- Compare vehicles

## 💳 Payment System Details

### Supported Payment Methods

- **bKash**: Popular mobile financial service
- **Nagad**: Alternative mobile financial service
- **Credit/Debit Card**: Traditional card payments
- **Cash/Manual**: In-person payments

### Payment Flow

1. Customer selects vehicle and dates
2. Chooses "Pay Now" or "Pay Later"
3. If "Pay Now": Redirected to payment page
4. Selects payment method and enters transaction ID
5. Payment status updates immediately across all dashboards
6. Professional receipt generated for printing

### Receipt Features

- Thermal receipt format (80mm width)
- Professional branding and layout
- Complete booking and payment details
- Print-optimized styling
- Transaction ID tracking

## 📊 Database Schema

### Key Tables

- **admin**: Administrator accounts
- **customer**: Customer accounts and profiles
- **vehicle_owner**: Vehicle owner accounts
- **driver**: Driver accounts and profiles
- **vehicle**: Vehicle inventory with ratings
- **booking**: Booking records with payment tracking
- **reviews**: Customer reviews and ratings

### Payment Fields

- `payment_status`: Pending, Paid, Refunded
- `payment_type`: Manual, Digital
- `payment_method`: bKash, Nagad, Card, Cash
- `transaction_id`: Payment transaction reference

## 🎨 Features Overview

### Vehicle Management

- Image uploads with validation
- Vehicle specifications (type, fuel, transmission, seats)
- Pricing management (per day rates)
- Location-based availability
- Approval workflow for new listings
- Rating and review integration

### Booking System

- Real-time availability checking
- Driver assignment options
- Flexible date ranges
- Special requests handling
- Booking status tracking
- Payment integration

### Rating System

- 5-star rating system
- Real-time rating updates
- Review moderation
- Performance tracking
- Customer feedback collection

## 🔒 Security Considerations

**Note**: This is a development/educational project. For production deployment, implement:

- Password hashing (use `password_hash()`)
- Prepared statements for all SQL queries
- CSRF protection
- Input validation and sanitization
- SSL/HTTPS encryption
- Rate limiting
- File upload validation
- Session security enhancements
- SQL injection prevention

## 🧪 Testing

### Database Connection Test

```bash
php -r "require 'config/database.php'; echo 'Connection successful!';"
```

### Payment System Test

1. Login as customer
2. Book a vehicle
3. Choose "Pay Now"
4. Complete payment with test transaction ID
5. Verify receipt generation
6. Check payment status across dashboards

## 📈 Performance Optimization

- Database indexing on frequently queried columns
- Image optimization for uploads
- CSS/JS minification for production
- Caching strategies for static assets
- Query optimization for large datasets

## 🚧 Future Enhancements

- [ ] Email notifications for bookings and payments
- [ ] SMS integration for booking confirmations
- [ ] Advanced analytics dashboard
- [ ] Mobile API endpoints
- [ ] Multi-language support
- [ ] Advanced filtering and search
- [ ] Real-time chat support
- [ ] Invoice generation (PDF)
- [ ] Multi-currency support
- [ ] Vehicle maintenance tracking

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License.

## 📞 Support

For support, email support@riderentpro.com or create an issue in the GitHub repository.

## 🙏 Acknowledgments

- Built with PHP and MySQL
- Icons by Font Awesome
- Inspired by modern vehicle rental platforms

## 📸 Screenshots

*Add screenshots of the application here to showcase the UI*

### Admin Dashboard

*Admin dashboard showing system overview*

### Customer Vehicle Browsing

*Vehicle listing with ratings and search*

### Payment Processing

*Payment page with multiple payment methods*

### Owner Performance

*Vehicle performance analytics dashboard*

---

**Built with ❤️ for the vehicle rental industry**
