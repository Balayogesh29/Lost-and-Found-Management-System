# Lost & Found Management System

## Objective

Develop a web-based system that helps a community report, browse, claim, and return lost or found items safely.

## Technology

- PHP 8.2
- MariaDB/MySQL
- HTML5, CSS3, and JavaScript
- XAMPP (Apache and MySQL)

## Main Features

1. User registration, sign-in, and sign-out.
2. Lost/found item reports with category, location, date, description, and optional image upload.
3. Keyword, category, item-type, location, and date filtering.
4. Item detail pages with related-report suggestions.
5. Protected claim requests instead of exposing contact information publicly.
6. User dashboards for My Reports and My Claims.
7. Administrator dashboard for reviewing claims, viewing statistics, and moderating reports.
8. Report editing, deletion, and returned-item status management.
9. Responsive layout, hover animations, and reduced-motion support.
10. CSRF tokens on every state-changing form.

## Database Tables

| Table | Purpose |
| --- | --- |
| `users` | Stores account information and user role. |
| `items` | Stores lost/found item reports and their status. |
| `claims` | Stores ownership requests, review status, and administrator notes. |

## Setup

1. Start Apache and MySQL in XAMPP.
2. Put the project in `C:\xampp\htdocs\lost-and-found`.
3. Import `database/lost_and_found.sql`, `database/upgrade.sql`, then `database/upgrade_v2.sql` in phpMyAdmin.
4. Open `http://localhost/lost-and-found/`.

## Testing Summary

- PHP syntax checks pass for every PHP page.
- The local Apache site was checked after each release.
- Admin authentication, dashboard display, protected claim flow, and filter queries were tested.

## Future Scope

- Email notifications for claim decisions.
- Password reset workflow.
- Pagination for very large item lists.
- Deployment with production database credentials and HTTPS.
