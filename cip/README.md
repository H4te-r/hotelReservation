# Hotel Reservation System

A complete hotel and reservation management system built with PHP and MySQL, featuring both guest and admin interfaces.

## Features

### Guest Features
- Browse available hotels with ratings and descriptions
- View room details and pricing
- Make reservations with date selection
- Real-time availability checking
- Responsive design for mobile devices

### Admin Features
- Secure admin login system
- Dashboard with system statistics
- Hotel management (add, edit, delete)
- Room management (add, edit, delete)
- Reservation management (view, update status, delete)
- User management

## System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Modern web browser

## Installation

1. **Clone or download the project files** to your web server directory.

2. **Set up the database:**
   - Create a MySQL database
   - Import the `database.sql` file to create all tables and sample data

3. **Configure database connection:**
   - Edit `config/database.php`
   - Update the database credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'hotel_reservation_system');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     ```

4. **Set up your web server:**
   - Point your web server to the project directory
   - Ensure PHP has write permissions for the directory

## Default Admin Credentials

- **Username:** `admin`
- **Password:** `password`

**Important:** Change these credentials after first login for security!

## File Structure

```
hotel-reservation-system/
├── admin/
│   ├── auth.php              # Authentication system
│   ├── login.php             # Admin login page
│   ├── dashboard.php         # Admin dashboard
│   ├── hotels.php            # Hotel management
│   ├── rooms.php             # Room management
│   └── reservations.php      # Reservation management
├── config/
│   └── database.php          # Database configuration
├── index.php                 # Guest homepage
├── get_rooms.php             # AJAX endpoint for rooms
├── process_reservation.php   # Reservation processing
├── database.sql              # Database schema and sample data
└── README.md                 # This file
```

## How to Use

### For Guests
1. Visit the homepage (`index.php`)
2. Browse available hotels
3. Click "Book Now" on any hotel
4. Select a room and click "Book"
5. Fill in your details and confirm reservation
6. Receive confirmation with reservation ID

### For Admins
1. Navigate to `admin/login.php`
2. Login with admin credentials
3. Access the dashboard to view system overview
4. Manage hotels, rooms, and reservations
5. Update reservation statuses as needed

## Database Schema

### Tables
- **hotels**: Hotel information (name, address, rating, etc.)
- **rooms**: Room details (type, capacity, price, availability)
- **reservations**: Guest reservations with dates and status
- **admin_users**: Admin user accounts

### Key Features
- Foreign key relationships for data integrity
- Timestamp tracking for all records
- Status management for reservations
- Availability tracking for rooms

## Security Features

- Password hashing for admin accounts
- SQL injection prevention with prepared statements
- Input validation and sanitization
- Session-based authentication
- CSRF protection through form tokens

## Customization

### Adding New Hotels
1. Login to admin panel
2. Go to Hotels section
3. Click "Add Hotel"
4. Fill in hotel details
5. Save

### Adding New Rooms
1. Login to admin panel
2. Go to Rooms section
3. Click "Add Room"
4. Select hotel and fill room details
5. Save

### Modifying Styles
- Edit CSS in the respective PHP files
- Bootstrap 5 is used for responsive design
- Font Awesome icons are included

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check database credentials in `config/database.php`
   - Ensure MySQL service is running
   - Verify database exists

2. **Permission Errors**
   - Ensure web server has read/write permissions
   - Check file ownership

3. **Reservation Not Working**
   - Verify all required fields are filled
   - Check date format (YYYY-MM-DD)
   - Ensure room is available for selected dates

### Support
For issues or questions, check the error logs or contact your system administrator.

## License

This project is open source and available under the MIT License.

## Contributing

Feel free to submit issues, feature requests, or pull requests to improve the system.

---

**Note:** This is a demonstration system. For production use, implement additional security measures, error handling, and backup systems. 