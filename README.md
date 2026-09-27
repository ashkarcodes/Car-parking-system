# Online Car Parking System (PHP + MySQL)

A role-based parking reservation platform with **User** and **Admin** modules, built with plain PHP, MySQL (mysqli), HTML, CSS, and JavaScript — no frameworks required.

## Features

**User**
- Register and log in
- View live list of available parking spots (location, floor, vehicle type, hourly rate)
- Book a spot instantly — it's immediately marked unavailable to other users
- Mark vehicle as "Parked" on arrival
- Mark "Exit" when leaving (spot stays occupied until admin confirms release)
- Cancel a booking before parking
- View personal parking history with estimated cost per session

**Admin**
- Secure login
- Add / view / force-reset parking spots
- Monitor all bookings (active, awaiting release, or all)
- **Release a spot** after confirming a user has exited — this is the only way a spot becomes available again after use
- Manage registered users (block/unblock)
- View platform-wide parking history

## Parking Spot Lifecycle

```
available → (user books) → booked → (user parks) → occupied
   → (user exits) → occupied, awaiting release → (admin releases) → available
```

This mirrors the spec: the admin does not allocate spots to users — users self-serve — but the admin is the sole authority that confirms a vacated spot is truly free and safe to book again.

## File Structure

```
car-parking-system/
├── config/
│   └── db.php                  # Database connection
├── includes/
│   ├── header.php               # Shared page header/nav
│   ├── footer.php                # Shared page footer
│   └── functions.php            # Helpers: auth guards, flash messages, status badges
├── assets/
│   ├── css/style.css            # All styling
│   └── js/script.js             # Confirm dialogs, alert auto-hide
├── admin/
│   ├── login.php
│   ├── dashboard.php
│   ├── add_spot.php
│   ├── manage_spots.php
│   ├── monitor_bookings.php     # Includes the "Release Spot" action
│   ├── manage_users.php
│   ├── parking_history.php
│   └── logout.php
├── user/
│   ├── register.php
│   ├── login.php
│   ├── dashboard.php
│   ├── available_spots.php
│   ├── book_spot.php
│   ├── my_bookings.php          # Park / Exit / Cancel actions
│   ├── parking_history.php
│   └── logout.php
├── index.php                    # Public landing page
├── database.sql                 # Full schema + seed admin + sample spots
└── README.md
```

## Setup Instructions

1. Install a local server stack such as XAMPP, WAMP, or MAMP (PHP 7.4+ and MySQL/MariaDB).
2. Copy the `car-parking-system` folder into your server's web root:
   - XAMPP: `C:\xampp\htdocs\car-parking-system`
   - MAMP: `/Applications/MAMP/htdocs/car-parking-system`
3. **Create the database**: open phpMyAdmin (or the MySQL CLI) and import `database.sql`. This creates the `parking_db` database, all tables, one default admin, and 6 sample parking spots.
4. Configure the connection in `config/db.php` if your MySQL username/password differ from the defaults (`root` / empty password).
5. Visit `http://localhost/car-parking-system/` in your browser.

## Default Admin Login

```
Email:    admin@parking.com
Password: admin123
```

## Notes on Security

- Passwords are hashed with `password_hash()` / verified with `password_verify()`.
- All queries touching user input use **prepared statements**.
- Output is escaped with `htmlspecialchars()`.
- Booking a spot uses a `SELECT ... FOR UPDATE` inside a transaction to avoid two users grabbing the same spot at the same moment (race condition guard).
- A user is limited to **one active booking at a time**, matching the described workflow.
- For production use, also add HTTPS, CSRF tokens, and rate-limiting on login attempts.

## Extending the Project

- Add time-slot based reservations (book a spot for a future date/time window).
- Add real payment integration and generate invoices from `parking_history`.
- Add email/SMS notification when a booked spot is about to be auto-released for no-shows.
- Add a live map/grid view of spots color-coded by status.
- Add pagination for large user/booking tables.
