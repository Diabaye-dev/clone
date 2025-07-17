# ONG Membership Form

This project provides a simple membership form for an NGO using PHP with a MySQL database. The HTML interfaces rely on Bootstrap 5.
The phone field uses the intl-tel-input plugin so users can search all international dialing codes with flags before entering their number. All inputs are mandatory.
Server-side validation in `submit.php` displays clear error messages when fields are missing.

## Usage

1. Ensure PHP and MySQL are installed.
2. Create a MySQL database named `membership` and update the credentials at the top of `submit.php` if necessary.
3. Start the PHP built-in server from this directory:
   ```bash
   php -S localhost:8000
   ```
4. Open your browser at `http://localhost:8000/membership_form.html` to access the form.
5. To view and manage registrations, go to `http://localhost:8000/admin.php`.
6. Members can log in at `http://localhost:8000/login.php` once their account has been activated.

Uploaded files are stored in the `uploads` directory and submissions are saved in the `members` table. New accounts are created blocked by default so an administrator must activate them and assign a username and password from the admin page. Members can then connect at `login.php` and manage their credentials from `member.php`.
