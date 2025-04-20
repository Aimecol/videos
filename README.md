# VideoStream - Video Learning Platform

VideoStream is a modern web-based video learning platform that allows users to watch educational content while tracking their progress. Built with PHP, MySQL, and modern frontend technologies.

## Screenshots

### Homepage

![VideoStream Homepage](screenshots/home.png)
Modern landing page with featured videos and category browsing

### Video Player

![Video Player](screenshots/video.png)
Feature-rich video player with progress tracking

### User Dashboard

![User Dashboard](screenshots/dashboard.png)
Personal dashboard showing watch history and progress

### User Profile

![User Profile](screenshots/profile.png)
Detailed user profile with earnings and completed videos

### Admin Dashboard

![Admin Dashboard](screenshots/admin.png)
Comprehensive admin interface for content management

## Features

### For Users

- 🎥 Watch high-quality educational videos
- 📊 Track watching progress and completion status
- 💰 Earn rewards for completing videos
- 🔍 Search and filter videos by category
- 📱 Responsive design for all devices
- 👤 User profiles with watch history
- 🔒 Secure authentication system

### For Administrators

- 📊 Comprehensive admin dashboard
- 📹 Video management system
- 🗂️ Category management
- 👥 User management
- 📈 View statistics and analytics

## Tech Stack

- **Backend:** PHP 8.2
- **Database:** MySQL (MariaDB 10.4)
- **Frontend:** HTML5, CSS3, JavaScript
- **Icons:** Font Awesome 6
- **Fonts:** Poppins (Google Fonts)
- **Server:** Apache (XAMPP)

## Installation

1. Install XAMPP with PHP 8.2+ and MySQL
2. Clone this repository to your XAMPP htdocs folder:

   ```bash
   cd /xampp/htdocs
   git clone [repository-url] videos
   ```

3. Create a MySQL database named 'video_platform'

4. Import the database schema:

   ```bash
   mysql -u root -p video_platform < video_platform.sql
   ```

5. Configure your database connection in `includes/db.php`:

   ```php
   $host = 'localhost';
   $dbname = 'video_platform';
   $username = 'root';
   $password = '';
   ```

6. Set up your site URL in `includes/config.php`:

   ```php
   define('SITE_URL', 'http://localhost/videos');
   ```

7. Ensure the following directories are writable:
   - assets/videos/
   - assets/images/thumbnails/
   - assets/images/profiles/

## Default Users

After installation, you can log in with these default credentials:

### Admin User

- Email: admin@gmail.com
- Password: admin123

## Project Structure

```
videos/
├── admin/              # Admin dashboard files
├── api/               # API endpoints
├── assets/            # Static assets
│   ├── css/
│   ├── js/
│   ├── images/
│   └── videos/
├── auth/              # Authentication pages
├── includes/          # Core PHP files
└── ...
```

## Features in Detail

### User Features

1. **Video Watching**

   - Resume from last watched position
   - Track watching progress
   - Mark videos as completed
   - Earn rewards upon completion

2. **User Dashboard**

   - Watch history
   - Progress tracking
   - Completed videos
   - Earnings summary

3. **Video Navigation**
   - Category-based filtering
   - Search functionality
   - Related videos suggestions
   - Featured videos section

### Admin Features

1. **Video Management**

   - Upload new videos
   - Edit video details
   - Delete videos
   - Set video categories and prices

2. **Category Management**

   - Create categories
   - Edit category details
   - View videos per category
   - Delete unused categories

3. **User Management**

   - View all users
   - Manage admin privileges
   - View user statistics
   - Delete user accounts

4. **Dashboard Analytics**
   - Total videos count
   - Total users count
   - Video views statistics
   - Recent activity logs

## Security Features

- Password hashing using PHP's password_hash()
- Session management
- CSRF protection
- SQL injection prevention using PDO
- XSS protection
- Admin authentication system

## Contributing

1. Fork the repository
2. Create a new branch
3. Make your changes
4. Submit a pull request

## License

[Your License Here]

## Support

For support, email [your-email] or create an issue in the repository.
