# Practical 7 XAMPP setup guide

This practical sends the StudentHub registration form to PHP using POST. PHP checks the values and appends registration records to a CSV file. Passwords are stored as one-way hashes. The CSV folder is outside XAMPP's public `htdocs` directory.

## 1. Copy Practical 7 into XAMPP

1. Open your XAMPP installation folder, usually `C:\xampp`.
2. Open `C:\xampp\htdocs`.
3. Copy the whole `Practical7` folder from this project into `htdocs`.

The resulting files should include:

```text
C:\xampp\htdocs\Practical7\register.html
C:\xampp\htdocs\Practical7\register.php
```

Keep the folder name `Practical7`; the URL below uses that name. This copy is what Apache serves. If you edit the original workspace copy later, copy those changes to the XAMPP copy too.

## 2. Start Apache

1. Open **XAMPP Control Panel** (usually `C:\xampp\xampp-control.exe`).
2. Click **Start** in the Apache row. Wait for the row to show it is running.
3. MySQL is not needed for this practical because registrations are saved to a CSV file.

XAMPP uses its Control Panel to start and stop Apache. The [official Windows FAQ](https://www.apachefriends.org/faq_windows.html) documents `htdocs` as the web directory and maps files there to `http://localhost/` URLs.

## 3. Open the registration form

Go to:

```text
http://localhost/Practical7/register.html
```

Enter a name with at least 3 letters/spaces, an email, a 10-digit mobile number starting with 6, 7, 8 or 9, and a password with at least 6 characters including a letter and a number. The confirmation must match. These are the same rules in `register.js` and `register.php`.

The form uses `novalidate` so `register.js` can show simple validation alerts. When the checks pass, JavaScript sends the form to PHP using POST. PHP checks the fields again and saves them to the CSV. PHP sends a small JSON reply back, and JavaScript shows the success box on the same registration page. Login remains a demonstration and does not authenticate against the CSV.

On the first successful submission, PHP creates:

```text
C:\xampp\Practical7_storage\registrations.csv
```

This location is outside `htdocs`, so Apache does not serve the registration records as a public file. The first row contains column names. Each later row contains the name, email, phone, and password hash. Open the CSV in Notepad to view the saved values. If importing into Excel, import the phone column as Text to keep leading zeros.

Each valid submission adds a new row. Invalid submissions add nothing. Previously saved JSON files are not converted or deleted. The events page still uses its existing `data/events.json` feed.

## How the PHP works

- `$_POST` reads the submitted form fields.
- `trim` removes spaces from the ends of the name, email and phone.
- `preg_match` checks each value against the same pattern used in JavaScript. The name pattern rejects HTML tags.
- `password_hash` protects the password before saving it.
- `fopen` with `a` appends; `fputcsv` writes a CSV row with the correct quoting.
- `fclose` closes the file after writing.

PHP sends its result as JSON. JavaScript shows PHP's error message if PHP rejects the form or cannot save the record.

Try submitting blank fields, an invalid email, a phone with letters, or different passwords. Each should show an error without adding a CSV row. Then submit two valid registrations and check that the CSV has one header row and two data rows.

## 4. Stop Apache

When finished, click **Stop** in the Apache row of XAMPP Control Panel.

## Common problems

- **Apache will not start:** another program may already be using port 80 (often IIS). Close or reconfigure that program, then try Apache again. Check the XAMPP Control Panel's Apache logs for the specific error.
- **The browser shows the wrong page or 404:** check that the folder is exactly `C:\xampp\htdocs\Practical7` and use `http://localhost/Practical7/register.html`.
- **The browser displays PHP source:** confirm Apache is running from XAMPP and that you opened the `localhost` URL, not the file directly in Explorer.
- **PHP says it could not save registration:** check that the account running Apache can write to `C:\xampp\Practical7_storage`. Use your actual XAMPP installation path if it differs from `C:\xampp`.

XAMPP is for local development and demonstrations. Do not expose it as a public production server.
