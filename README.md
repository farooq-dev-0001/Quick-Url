# Quick URL - TinyURL Clone

A modern URL shortener application built with Laravel, featuring user authentication, analytics, and an attractive admin dashboard.

## Features

### 🔗 URL Shortening
- **Instant URL Shortening**: Transform long URLs into short, shareable links
- **Custom Titles**: Add custom titles to your shortened URLs
- **Expiration Dates**: Set expiration dates for temporary links
- **Auto Title Extraction**: Automatically extracts page titles from URLs

### 👤 User Authentication
- **Registration & Login**: Secure user registration and login system
- **Password Security**: Encrypted password storage
- **Remember Me**: Option to stay logged in
- **Session Management**: Secure session handling

### 📊 Analytics & Dashboard
- **Click Tracking**: Real-time click counting for all URLs
- **User Dashboard**: Comprehensive dashboard for managing URLs
- **Statistics**: View detailed statistics including:
  - Total URLs created
  - Total clicks received
  - URLs created today
  - Average clicks per URL
  - Top performing URLs

### 🎨 Modern UI/UX
- **Responsive Design**: Works perfectly on desktop, tablet, and mobile
- **Beautiful Animations**: Smooth transitions and hover effects
- **SweetAlert2 Integration**: Beautiful alert dialogs
- **jQuery AJAX**: Seamless user experience without page reloads
- **Bootstrap 5**: Modern and responsive UI components

### 🛡️ Security Features
- **CSRF Protection**: Built-in CSRF token protection
- **Input Validation**: Comprehensive input validation
- **SQL Injection Protection**: Laravel's Eloquent ORM prevents SQL injection
- **XSS Protection**: Output escaping and sanitization

## Technology Stack

- **Backend**: Laravel 10
- **Frontend**: Bootstrap 5, jQuery, SweetAlert2
- **Database**: MySQL
- **Build Tool**: Vite
- **Authentication**: Laravel's built-in authentication

## Installation & Setup

1. **Install PHP dependencies**
   ```bash
   composer install
   ```

2. **Install NPM dependencies**
   ```bash
   npm install
   ```

3. **Environment Configuration**
   - Configure your database settings in `.env`:
     ```
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=quick_url_db
     DB_USERNAME=root
     DB_PASSWORD=
     ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Run Database Migrations**
   ```bash
   php artisan migrate
   ```

6. **Seed Test Users (Optional)**
   ```bash
   php artisan db:seed --class=UserSeeder
   ```

7. **Build Assets**
   ```bash
   npm run build
   ```

8. **Start the Development Server**
   ```bash
   php artisan serve
   ```

9. **Access the Application**
    - Visit: `http://localhost:8000`

## Test Credentials

After running the seeder, you can use these test accounts:

- **Admin User**
  - Email: `admin@quickurl.com`
  - Password: `password123`

- **Test User**
  - Email: `test@quickurl.com`
  - Password: `password123`

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
