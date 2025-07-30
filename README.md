# GachaBlog

A modern blog platform for gacha gaming enthusiasts. Share your pulls, discuss strategies, and connect with fellow gamers across popular gacha titles like Genshin Impact, Honkai: Star Rail, Blue Archive, and more.

## 🎮 Features

- **Modern UI** with dark/light mode toggle
- **Game-specific tags** for organizing content
- **User authentication** and profiles
- **Post creation** and management
- **Like/Comment system** for engagement
- **Follow system** to connect with other users
- **Responsive design** for mobile and desktop
- **Real-time notifications**

## 🛠️ Tech Stack

- **Backend:** Laravel 12 (PHP 8.4+)
- **Frontend:** Blade templates with custom CSS
- **Database:** SQLite (for development)
- **Build Tool:** Vite
- **Styling:** Custom CSS with CSS variables

## 📋 Prerequisites

Before running this project, make sure you have:

- **PHP 8.4 or higher**
- **Composer** (PHP package manager)
- **Node.js 18+** and **npm**
- **Git**

## 🚀 Installation & Setup

### 1. Clone the Repository

```bash
git clone <repository-url>
cd gacha-blog-finals
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node.js Dependencies

```bash
npm install
```

### 4. Environment Setup

Copy the environment file:
```bash
cp .env.example .env
```

Generate application key:
```bash
php artisan key:generate
```

### 5. Database Setup

Create the SQLite database:
```bash
# For Windows Command Prompt/PowerShell:
type nul > database/database.sqlite

# For Unix/Linux/Mac:
touch database/database.sqlite
```

Run migrations and seed the database:
```bash
php artisan migrate:fresh --seed
```

This will create:
- All necessary database tables
- Sample users and posts
- Game tags (Genshin Impact, Honkai: Star Rail, etc.)
- Sample relationships between posts and tags

### 6. Build Frontend Assets

For development (with hot reload):
```bash
npm run dev
```

For production:
```bash
npm run build
```

### 7. Start the Development Server

```bash
php artisan serve
```

Your application will be available at: **http://localhost:8000**

## 🎯 Available Routes

- **Home:** `/` - Welcome page
- **Posts:** `/posts` - View all posts
- **Users:** `/users` - View all users

## 🎨 UI Features

### Dark/Light Mode
- Toggle between themes using the moon/sun button in the navbar
- Theme preference is saved in localStorage

### Responsive Design
- Mobile-friendly sidebar that slides in/out
- Optimized layouts for different screen sizes

### Game Tags
The platform includes tags for popular gacha games:
- Genshin Impact
- Honkai: Star Rail
- Blue Archive
- Nikke: Goddess of Victory
- Arknights
- Fate/Grand Order
- Azur Lane
- Epic Seven
- Punishing: Gray Raven
- Reverse: 1999

## 🗄️ Database Structure

### Models & Relationships
- **Users** - can create posts, comments, likes, follow others
- **Posts** - belong to users, have comments, likes, and game tags
- **Comments** - belong to users and posts
- **Likes** - connect users to posts
- **Follows** - user-to-user relationships
- **GameTags** - many-to-many with posts
- **Notifications** - belong to users

## 🔧 Development

### Adding New Features
1. Create migrations: `php artisan make:migration create_table_name`
2. Update models with relationships
3. Create controllers: `php artisan make:controller ControllerName`
4. Add routes in `routes/web.php`
5. Create Blade views in `resources/views/`

### Styling
- Main styles: `resources/css/app.css`
- Uses CSS variables for theming
- Responsive design with mobile-first approach

### Database Changes
```bash
# Create new migration
php artisan make:migration add_column_to_table

# Run migrations
php artisan migrate

# Reset and reseed
php artisan migrate:fresh --seed
```

## 🐛 Troubleshooting

### Common Issues

**"No application encryption key"**
```bash
php artisan key:generate
```

**"Database file does not exist"**
```bash
# Create SQLite database file
type nul > database/database.sqlite  # Windows
touch database/database.sqlite       # Unix/Linux/Mac
php artisan migrate
```

**"Vite manifest not found"**
```bash
npm run dev    # For development
npm run build  # For production
```

**"No such table: sessions"**
```bash
php artisan migrate:fresh --seed
```

## 📝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🤝 Support

If you encounter any issues or have questions:
1. Check the troubleshooting section above
2. Review Laravel documentation: https://laravel.com/docs
3. Create an issue in the repository

---

**Happy gaming and blogging! 🎮✨**
