# Laravel API for Technical Assessment

This Laravel backend API provides authentication and IP geolocation functionality for the technical assessment challenge.

## Features

- **User Authentication** with Laravel Sanctum tokens
- **IP Geolocation** using ipinfo.io API with SSL verification disabled for development
- **Search History** tracking and management with bulk delete functionality
- **CORS Support** for React frontend integration
- **Database Seeding** with test user credentials

## API Endpoints

### Authentication
- `POST /api/login` - User login with email/password
- `POST /api/logout` - User logout (requires authentication)
- `GET /api/user` - Get current authenticated user (requires authentication)

### Geolocation
- `GET /api/geo` - Get geolocation data
  - **Without parameters**: Returns current user's IP geolocation
  - **With `?ip={address}`**: Returns geolocation for specified IP address
  - Automatically saves IP searches to history

### Search History
- `GET /api/history` - Get user's search history (requires authentication)
- `POST /api/history/delete` - Delete multiple history entries (requires authentication)

## Setup Instructions

### Prerequisites
- PHP 8.2+
- Composer
- **Laragon** (recommended) or XAMPP/WAMP
- MySQL database (comes with Laragon)

### Installation Steps

1. **Clone and install dependencies**
```bash
git clone https://github.com/HarleyGotardo/backend-api.git
cd backend-api
composer install
```

2. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Configure database (Laragon setup)**
Edit `.env` file with your Laragon database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jlabs_exam
DB_USERNAME=root
DB_PASSWORD=
```

**Database Setup in Laragon:**
- Open Laragon
- Click "Menu > MySQL > Create database"
- Enter database name: `jlabs_exam`
- Click "Create"

4. **Run migrations and seed database**
```bash
php artisan migrate:fresh --seed
```

5. **Start the development server**
```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## Test Credentials

After running the seeder, you can use these credentials for testing:

- **Email**: `admin@jlabs.com`
- **Password**: `password123`

## Usage Examples

### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@jlabs.com", "password": "password123"}'
```

### Get Current IP Geolocation
```bash
curl -X GET http://localhost:8000/api/geo \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Get Specific IP Geolocation
```bash
curl -X GET "http://localhost:8000/api/geo?ip=8.8.8.8" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Get Search History
```bash
curl -X GET http://localhost:8000/api/history \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Delete Search History
```bash
curl -X POST http://localhost:8000/api/history/delete \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{"ids": [2, 3]}'
```

## Project Structure

- `app/Http/Controllers/Api/` - API controllers
- `app/Models/` - Eloquent models (User, SearchHistory)
- `database/seeders/` - Database seeders
- `routes/api.php` - API routes definition

## Dependencies

All dependencies are listed in `composer.json`. Key packages include:
- `laravel/framework` - Laravel framework
- `laravel/sanctum` - API authentication
- `illuminate/http` - HTTP client for external API calls

## Notes

- CORS is configured for frontend integration
- All API endpoints (except login) require authentication via Bearer tokens
- IP geolocation searches are automatically saved to user history
- External API calls to ipinfo.io include proper error handling and 10-second timeouts
- SSL verification is disabled for development environments (Windows/Laragon compatibility)
- Database seeder creates test user: `admin@jlabs.com` / `password123`
- User model includes `HasApiTokens` trait for Laravel Sanctum functionality

## Troubleshooting

### SSL Certificate Error
If you encounter "SSL peer certificate" errors, the SSL verification is already disabled in the SearchController for development purposes.

### Database Connection
Ensure Laragon is running and the `jlabs_exam` database exists before running migrations.

### Authentication Issues
Make sure the User model has the `HasApiTokens` trait (included in this setup).

## License

MIT License
#
