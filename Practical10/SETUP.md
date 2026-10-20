# Practical 10: PHP sessions

Serve this folder through PHP (for example, XAMPP Apache), then open `index.php`.
Use the database connection settings in `db.php`.

## Database

- For a fresh database, create `studenthub` and import `database.sql`.
- If using existing tables without a role column, back up the database and add
  `role ENUM('student', 'admin') NOT NULL DEFAULT 'student'` to `students` in
  phpMyAdmin. Importing `database.sql` alone will not change an existing table.
- Registration continues to hash passwords and creates a `student` by default.
- To demonstrate the admin role, register a separate test account, then manually
  change only that account's `role` to `admin` in phpMyAdmin. The existing
  Admin / Teacher dashboard uses this role. The login form cannot choose a role.

## Log in as an admin

1. Register a separate account using the existing registration form.
2. In phpMyAdmin, select the database configured in `db.php`, then open `students`.
3. Find that account by its email, click **Edit**, change `role` from `student`
   to `admin`, and save. Keep its existing password hash unchanged.
4. Log out if already logged in, then use the normal login form with that
   account's email and password. It opens `admin-dashboard.php`.

No separate admin registration page or default admin password is needed.

## How it works

1. `Js/login.js` submits the form to `login.php`, which looks up the email with a
   prepared statement and checks the stored hash using `password_verify()`.
2. After a successful login, PHP regenerates the session ID and stores the
   student ID, database role, and last activity time in the session.
3. Students go to `dashboard.php`; admins go to `admin-dashboard.php`.
   Both include `auth.php` before sending HTML. Guests go to login, and users
   opening a page restricted to the other role return to their own dashboard.
4. Home and all protected pages check the same 15-minute inactivity
   timeout. The timeout is checked when requesting a page, not by a browser timer.
   Change `$timeout` in `session.php` to demonstrate a shorter delay.
5. `logout.php` clears session data, deletes the session cookie, destroys the
   server session, and redirects to login. Protected pages use `no-store` headers.

`index.php` shows Register and Login to guests, and Dashboard, Events, Profile,
and Logout to logged-in users. Its Dashboard link follows the user's role.
`events.php` and `profile.php` include `auth.php`, so guests are redirected to
login even when opening their URLs directly. Both roles can access these pages.
All existing Logout links call `logout.php`.

## Page permissions

| Access | Pages |
| --- | --- |
| Public | `index.php`, `login.html`, `register.html` |
| Any logged-in user | `events.php`, `profile.php` |
| Student only | `dashboard.php`, `fee-payment.php`, `attendance.php`, `timetable.php`, `results.php` |
| Admin only | `admin-dashboard.php`, `admin-attendance.php`, `admin-timetable.php` |

Protected pages include `auth.php` before any HTML. Role-specific pages set
`$requiredRole` to `student` or `admin` first. Guests go to login, and users with
the wrong role go to their own dashboard. Timeout and logout apply to every
protected page, including direct URL access.

Home and protected pages were renamed from `.html` to `.php` so PHP can check
the session before showing the page. Existing navigation links were updated;
the pages still use their existing CSS and JavaScript files. Their content and
demonstration features are unchanged.
