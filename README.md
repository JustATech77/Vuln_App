# PHP Social Media Web Application

A simple social media web application built with PHP and MySQL, featuring user authentication, profile management, posts system, and admin panel.

## Features

- **User Authentication**: Register, login, and logout functionality
- **User Profiles**: Update profile information and upload profile images
- **Posts System**: Create, edit, and delete posts
- **Admin Panel**: Manage users, assign admin roles, and view all users
- **User Management**: View all users, edit user information
- **Responsive Design**: Modern UI with CSS styling

## Database Structure

The application uses **2 main tables**:

### 1. `users` Table
### 2. `posts` Table

## Installation & Setup

### Prerequisites

- **XAMPP** (or similar local server with Apache, MySQL, and PHP)
- **PHP 7.0+**
- **MySQL 5.7+**

### Step 1: Download and Setup

1. Clone or download this project to your XAMPP `htdocs` folder
2. Start XAMPP Control Panel
3. Start **Apache** and **MySQL** services

### Step 2: Database Setup

1. Open **phpMyAdmin** (http://localhost/phpmyadmin)
2. Create a new database named `null` (or change the database name in `config.php`)
3. Import the database structure by running these SQL commands:

```sql
-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    profile_image VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create posts table
CREATE TABLE posts (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create a default admin user (optional)
INSERT INTO users (username, email, password, is_admin)
VALUES ('admin', 'admin@example.com', 'admin123', 1);
```

### Step 3: Configuration

1. Open `config.php` file
2. Update database connection settings if needed:
   ```php
   $host = 'localhost';
   $dbname = 'null';  // Your database name
   $username = 'root';
   $password = '';    // Your MySQL password
   ```

### Step 4: Access the Application

1. Open your web browser
2. Navigate to: `http://localhost/PHP_Task/`
3. You should see the login page

## Usage

### For Regular Users:

1. **Register**: Create a new account at the registration page
2. **Login**: Use your email and password to log in
3. **Profile**: Update your profile information and upload a profile image
4. **Posts**: Create, edit, and delete your posts
5. **Users List**: View all registered users

### For Admins:

1. **Admin Panel**: Access admin features at `/admin/admin_panal.php`
2. **User Management**: Add, edit, delete users, and manage admin roles
3. **All Features**: Admins have access to all regular user features

## File Structure

```
PHP_Task/
├── admin/                 # Admin panel files
│   ├── admin_panal.php   # Main admin panel
│   ├── add_user.php      # Add new users
│   ├── edit_user.php     # Edit user information
│   └── Profile_image/    # Admin profile images
├── user/                  # User area files
│   ├── profile.php       # User profile page
│   ├── user_posts.php    # User posts page
│   ├── users_list.php    # All users list
│   ├── POSTS/           # Post management
│   └── Profile_image/   # User profile images
├── css/                  # Stylesheet files
├── image/               # Application images
├── config.php           # Database configuration
├── login.php            # Login page
├── register.php         # Registration page
└── index.php            # Main entry point
```
**Happy Testing! 🐛**
