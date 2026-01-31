School Management System (PHP + MySQL)

This project matches the provided `school_management_system` SQL schema.

Quick setup (XAMPP on Windows):

1) Copy the `school_system` folder into `C:\xampp\htdocs\section06` (already in workspace).
2) Import the provided SQL dump (`school_management_system.sql`) via phpMyAdmin or CLI:

```powershell
mysql -u root < "c:/xampp/htdocs/section06/school_management_system.sql"
```

3) Ensure the `roles` table contains the roles you need (e.g., admin, teacher, student). If empty, insert basic roles:

```sql
INSERT INTO roles (role_name) VALUES ('admin'),('teacher'),('student');
```

4) Update DB credentials in `db.php` if necessary.
5) Run the seeder to create default roles and an admin user:

```powershell
php "c:/xampp/htdocs/section06/school_system/seed.php"
```

6) Open: http://localhost/section06/school_system/ and login with `admin@example.com` / `Admin@123` (change after first login).

Notes:
- Passwords use PHP `password_hash` and `password_verify`.
- The scaffold includes basic admin CRUD for students/faculty/courses/sections/enrollments/grades.
- This is a starting point — add validation, CSRF protection, and harden for production before use.
