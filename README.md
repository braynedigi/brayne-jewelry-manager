# 🏪 Brayne Jewelry Manager

A comprehensive, production-ready jewelry management system built with Laravel, featuring advanced order management, real-time updates, and dynamic color customization.

## ✨ Features

### 🎨 **Dynamic Color Customization**
- **Admin-controlled theming** with live preview
- **Color presets**: Default, Green, Orange, Red, Purple, Dark themes
- **Comprehensive UI theming**: Buttons, sidebar, cards, badges, links
- **Real-time color updates** across the entire application

### 👥 **Multi-Role User System**
- **Admin Users**: Full system access, color customization, order management
- **Factory Users**: Production workflow, status updates, timeline management
- **Distributor Users**: Order creation, customer management, status tracking

### 📋 **Order Management**
- **Complete order lifecycle** from creation to delivery
- **Real-time status updates** with notifications
- **Priority management** (Low, Normal, Urgent)
- **Timeline tracking** with estimated completion dates
- **Production workflow** with detailed status transitions

### 🏭 **Factory Dashboard**
- **Real-time production queue** with drag-and-drop functionality
- **Workload management** with capacity planning
- **Status tracking** for all production stages
- **Priority-based ordering** system

### 📊 **Advanced Features**
- **Real-time notifications** using Laravel Broadcasting
- **Email notifications** with customizable templates
- **Customer management** with detailed profiles
- **Product catalog** with categories and pricing
- **Order templates** for quick order creation
- **Import/Export** functionality for data management

## 🚀 Quick Start

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL 5.7 or higher
- Node.js & NPM (for asset compilation)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/braynedigi/brayne-jewelry-manager.git
   cd brayne-jewelry-manager
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   php artisan db:seed --class=ColorSettingsSeeder
   ```

5. **Asset compilation**
   ```bash
   npm run dev
   ```

6. **Storage setup**
   ```bash
   php artisan storage:link
   ```

7. **Start the server**
   ```bash
   php artisan serve
   ```

## 🎨 Color Customization

### Admin Access
Navigate to **Settings → Appearance** to customize the application theme.

### Available Customizations
- **Button Colors**: Primary, Secondary, Success, Warning, Danger, Info
- **Sidebar Colors**: Background, text, active states
- **Card Colors**: Background, headers, borders
- **Status Badges**: Pending, approved, production, completed
- **UI Elements**: Links, borders, shadows, form controls

### Color Presets
- **Default Blue**: Professional blue theme
- **Green Theme**: Nature-inspired green palette
- **Orange Theme**: Warm orange accents
- **Red Theme**: Bold red highlights
- **Purple Theme**: Elegant purple tones
- **Dark Theme**: Modern dark interface

## 📱 User Roles & Permissions

### Admin Users
- Full system access and configuration
- Color customization and theming
- User management and role assignment
- System settings and email configuration
- Order approval and management

### Factory Users
- Production workflow management
- Order status updates (In Production → Finishing → Ready for Delivery → Delivered to Brayne)
- Timeline and priority management
- Production notes and tracking
- Real-time dashboard updates

### Distributor Users
- Customer management
- Order creation and templates
- Order status tracking
- Customer communication
- Profile management

## 🔧 Technical Stack

- **Backend**: Laravel 10.x
- **Frontend**: Bootstrap 5, jQuery, Alpine.js
- **Database**: MySQL
- **Real-time**: Laravel Broadcasting (Pusher)
- **Email**: Laravel Mail with customizable templates
- **Caching**: Laravel Cache with Redis support
- **File Storage**: Laravel Storage with local/cloud support

## 📁 Project Structure

```
jewelry-manager/
├── app/
│   ├── Console/Commands/     # Custom Artisan commands
│   ├── Events/              # Event classes for real-time updates
│   ├── Http/Controllers/    # Application controllers
│   ├── Mail/               # Email templates and classes
│   ├── Models/             # Eloquent models
│   └── Services/           # Business logic services
├── database/
│   ├── migrations/         # Database migrations
│   └── seeders/           # Database seeders
├── public/
│   └── css/               # Generated CSS files
├── resources/
│   └── views/             # Blade templates
└── routes/                # Application routes
```

## 🛠️ Development

### Key Commands
```bash
# Generate CSS from settings
php artisan tinker --execute="app('App\Http\Controllers\SettingsController')->generateDynamicCSS();"

# Clear all caches
php artisan optimize:clear

# Run tests
php artisan test

# Export database
php artisan export:database
```

### Environment Variables
```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jewelry_manager
DB_USERNAME=root
DB_PASSWORD=

# Broadcasting (for real-time features)
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@jewelrymanager.com
MAIL_FROM_NAME="Jewelry Manager"
```

## 📊 Database Schema

### Core Tables
- **users**: User accounts and authentication
- **orders**: Order management and tracking
- **customers**: Customer information and profiles
- **products**: Product catalog and pricing
- **settings**: Application configuration and theming
- **notifications**: Real-time notification system

### Relationships
- Orders belong to Distributors and Customers
- Orders have many Products (many-to-many)
- Users have specific roles (Admin, Factory, Distributor)
- Settings control application appearance and behavior

## 🔒 Security Features

- **Role-based access control** (RBAC)
- **CSRF protection** on all forms
- **Input validation** and sanitization
- **SQL injection prevention** with Eloquent ORM
- **XSS protection** with Blade templating
- **Secure file uploads** with validation

## 🚀 Deployment

### Production Checklist
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Configure production database
- [ ] Set up SSL certificate
- [ ] Configure email settings
- [ ] Set up file storage (local or cloud)
- [ ] Configure caching (Redis recommended)
- [ ] Set up monitoring and logging
- [ ] Configure backups

### Performance Optimization
```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is proprietary software developed for Brayne Digital. All rights reserved.

## 🆘 Support

For support and questions:
- **Email**: support@braynedigital.com
- **Documentation**: [Internal Wiki]
- **Issues**: [GitHub Issues](https://github.com/braynedigi/brayne-jewelry-manager/issues)

---

**Built with ❤️ by Brayne Digital Team**
