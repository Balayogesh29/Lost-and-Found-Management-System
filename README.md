# Lost & Found Management System

A PHP and MySQL application to report, search, view, and manage lost/found item reports.

## Run locally with XAMPP

1. Copy this folder into `C:\xampp\htdocs\lost-and-found`.
2. Ensure Apache and MySQL are running in XAMPP.
3. Open `http://localhost/phpmyadmin`, select **Import**, and import `database/lost_and_found.sql`.
4. Import `database/upgrade.sql` to enable accounts, image uploads, My Reports, and the admin dashboard.
5. Import `database/upgrade_v2.sql` to enable claims and moderation controls.
6. Visit `http://localhost/lost-and-found/`.

The default XAMPP MySQL account is used: `root` with a blank password. If yours differs, update `config.php`.

## Default administrator

- Email: `admin@lostfound.local`
- Password: `admin123`

Change this password before using the site beyond local development.
