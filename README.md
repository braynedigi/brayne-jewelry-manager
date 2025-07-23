# 🏪 Brayne Jewelry Manager

A comprehensive Laravel-based jewelry management system with role-based access control for distributors, factory workers, and administrators. This system streamlines the entire jewelry manufacturing process from order creation to delivery.

## ✨ Features

### 🎭 User Roles & Access Control
- **👑 Admin**: Full system management and oversight
- **🏪 Distributor**: Customer and order management
- **🏭 Factory**: Production workflow management

### 🏪 Distributor Dashboard
- ✅ Add/view/update/delete customers
- ✅ Place new orders with product selection
- ✅ View order history and real-time status
- ✅ Update payment status (50%/fully paid)
- ✅ Manage distributor profile and settings
- ✅ Order templates for quick reordering
- ✅ Advanced search and filtering

### 🏭 Factory Dashboard
- ✅ View all orders (without financial information)
- ✅ Update order status through production stages
- ✅ Track production progress and timelines
- ✅ Manage workload and priorities
- ✅ Real-time status updates

### 👑 Admin Dashboard
- ✅ Manage all users, products, and couriers
- ✅ View comprehensive system statistics
- ✅ Order approval and management
- ✅ System settings and configuration
- ✅ Real-time monitoring and analytics

## 🚀 Key Features

### 📱 Real-Time Updates
- **WebSocket Integration**: Live notifications for order status changes
- **Live Dashboard**: Updates without page refresh
- **Status Change Alerts**: Immediate popup notifications
- **Role-based Channels**: Targeted notifications for different user types

### 📋 Order Management
- **Order Templates**: Save common order configurations
- **Quick Reorder**: One-click reorder from previous orders
- **Advanced Search**: Filter by status, date, customer, amount, etc.
- **Order History**: Complete audit trail with timestamps
- **Production Tracking**: Detailed timeline management

### 🎨 Modern UI/UX
- **Responsive Design**: Works on all devices
- **Bootstrap 5**: Modern, clean interface
- **Custom Branding**: Configurable logos and colors
- **Enhanced Notifications**: Multiple notification styles
- **Accessibility**: WCAG compliant design

## 🛠️ Technology Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Bootstrap 5, JavaScript, AJAX
- **Database**: MySQL/PostgreSQL/SQLite
- **Real-time**: Pusher WebSocket integration
- **Authentication**: Laravel's built-in auth system
- **File Storage**: Laravel Storage with public disk

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- Node.js & NPM (for frontend assets)
- MySQL/PostgreSQL/SQLite
- Web server (Apache/Nginx)

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/jewelry-manager.git
cd jewelry-manager
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database
Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jewelry_manager
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Run Migrations and Seeders
```bash
php artisan migrate:fresh --seed
```

### 6. Create Storage Link
```bash
php artisan storage:link
```

### 7. Build Frontend Assets
```bash
npm run build
```

### 8. Start Development Server
```bash
php artisan serve
```

## 🔐 Default Login Credentials

### Admin
- **Email**: `admin@jewelry.com`
- **Password**: `password`

### Distributor 1
- **Email**: `distributor1@jewelry.com`
- **Password**: `password`

### Distributor 2
- **Email**: `distributor2@jewelry.com`
- **Password**: `password`

### Factory
- **Email**: `factory@jewelry.com`
- **Password**: `password`

## 📊 Order Status Flow

1. **Pending Payment** - Order created by distributor
2. **Approved** - Order confirmed by admin
3. **In Production** - Order being manufactured
4. **Finishing** - Final touches and quality check
5. **Ready for Delivery** - Order completed and ready
6. **Delivered to Brayne** - Order received by Brayne Jewelry
7. **Delivered to Client** - Order delivered to customer
8. **Cancelled** - Order cancelled

## 💳 Payment Status

- **Unpaid** - No payment received
- **Partially Paid** - 50% payment received
- **Fully Paid** - Complete payment received

## 🔧 Configuration

### Real-Time Features
For real-time notifications, configure Pusher in your `.env`:
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
```

### File Storage
Configure file storage for logos and images:
```env
FILESYSTEM_DISK=public
```

## 📁 Project Structure

```
jewelry-manager/
├── app/
│   ├── Console/Commands/     # Artisan commands
│   ├── Http/Controllers/     # Application controllers
│   ├── Models/              # Eloquent models
│   ├── Services/            # Business logic services
│   └── Providers/           # Service providers
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/            # Database seeders
├── resources/
│   ├── views/              # Blade templates
│   ├── css/               # Stylesheets
│   └── js/                # JavaScript files
├── routes/                 # Application routes
├── storage/               # File storage
└── public/                # Public assets
```

## 🧪 Testing

Run the test suite:
```bash
php artisan test
```

## 📝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🤝 Support

For support and questions:
- Create an issue on GitHub
- Email: support@braynejewelry.com
- Documentation: [Wiki](https://github.com/yourusername/jewelry-manager/wiki)

## 🔄 Changelog

### Version 1.0.0
- Initial release
- Role-based access control
- Order management system
- Real-time notifications
- Modern UI/UX design

## 🙏 Acknowledgments

- Laravel team for the amazing framework
- Bootstrap team for the UI components
- Pusher for real-time functionality
- All contributors and testers

---

**Made with ❤️ for Brayne Jewelry**
