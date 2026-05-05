# auth_system
this is full competely local authenicattion system 

# Setup Notes

## Database Connection

This project uses the following MySQL database connection:

```php
new mysqli("localhost", "root", "", "auth_system");
```

Please make sure to run this project on your **local server environment** (WAMP/XAMPP/Laragon) using:

```txt
http://localhost/
```

---

## Image Upload

Please upload a profile image **smaller than 1 MB** for smooth processing and database storage.

---

## OTP Information

Currently, OTP delivery is configured for **local development/testing only**.

The OTPs generated in this project are **not being sent to real email addresses or mobile numbers at this stage.**

### Mobile OTP

For real SMS OTP delivery, a third-party SMS service/API is required.
The service I checked requires a paid plan (approximately **₹6000**), so live mobile OTP integration has not been enabled yet.

### Email OTP

For real email OTP delivery, I initially implemented **PHPMailer / SendMail** integration for sending live emails.

However, that setup includes mail configuration files and sensitive credentials that are not suitable for pushing publicly to GitHub.

Because of that, the live email sending setup was removed from the public repository, and the current GitHub version uses **local/testing OTP generation only.**

---

## Current Project Status

This repository contains the complete authentication flow:

* User Registration
* OTP Generation
* OTP Verification & Login
* JWT Authentication
* Password Change
* Password Verification

All features are fully functional in a **local development environment.**
