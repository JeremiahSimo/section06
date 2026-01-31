<?php
require_once __DIR__ . '/db.php';
// seed roles
$roles = ['admin','teacher','student','applicant','staff'];
foreach($roles as $r){
    $stmt=$pdo->prepare('SELECT role_id FROM roles WHERE role_name=?'); $stmt->execute([$r]); if(!$stmt->fetch()){ $ins=$pdo->prepare('INSERT INTO roles (role_name) VALUES (?)'); $ins->execute([$r]); }
}
// create admin user if none
$stmt=$pdo->prepare('SELECT u.user_id FROM users u JOIN roles r ON u.role_id=r.role_id WHERE r.role_name="admin" LIMIT 1'); $stmt->execute();
if(!$stmt->fetch()){
    $role_id = null; $s=$pdo->prepare('SELECT role_id FROM roles WHERE role_name="admin" LIMIT 1'); $s->execute(); $r=$s->fetch(); if($r) $role_id=$r['role_id'];
    if($role_id){
        $email = 'admin@example.com';
        $password = 'Admin@123';
        $hash = password_hash($password,PASSWORD_DEFAULT);
        $pdo->prepare('INSERT INTO users (role_id,email,password_hash,status) VALUES (?,?,?,"active")')->execute([$role_id,$email,$hash]);
        $user_id = $pdo->lastInsertId();
        $pdo->prepare('INSERT INTO faculty (user_id,first_name,last_name,employment_status) VALUES (?,?,?,"active")')->execute([$user_id,'Site','Admin']);
        echo "Created admin user: $email with password $password\n";
    }
} else echo "Admin user exists\n";

// begin seeding new data
try {
    // college and department
    $pdo->prepare('INSERT INTO colleges (name) VALUES (?) ON DUPLICATE KEY UPDATE name=name')->execute(['College of Science']);
    $college_id = $pdo->lastInsertId();
    if(!$college_id) {
        $college_id = $pdo->query('SELECT college_id FROM colleges WHERE name = "College of Science"')->fetchColumn();
    }
    
    $pdo->prepare('INSERT INTO departments (college_id, name) VALUES (?,?) ON DUPLICATE KEY UPDATE name=name')->execute([$college_id, 'Computer Science']);
    $dept_id = $pdo->lastInsertId();
    if(!$dept_id) {
        $dept_id = $pdo->query('SELECT department_id FROM departments WHERE name = "Computer Science"')->fetchColumn();
    }
    
    // program
    $pdo->prepare('INSERT INTO programs (department_id, program_code, name, degree_type) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE name=name')->execute([$dept_id, 'BSCS', 'Bachelor of Science in Computer Science', 'Undergraduate']);
    $prog_id = $pdo->lastInsertId();
    if(!$prog_id) {
        $prog_id = $pdo->query('SELECT program_id FROM programs WHERE program_code = "BSCS"')->fetchColumn();
    }

    // teacher
    $stmt=$pdo->prepare('SELECT u.user_id FROM users u JOIN roles r ON u.role_id=r.role_id WHERE r.role_name="teacher" LIMIT 1'); $stmt->execute();
    if(!$stmt->fetch()){
        $role_id = null; $s=$pdo->prepare('SELECT role_id FROM roles WHERE role_name="teacher" LIMIT 1'); $s->execute(); $r=$s->fetch(); if($r) $role_id=$r['role_id'];
        if($role_id){
            $email = 'teacher@example.com';
            $password = 'teacher123';
            $hash = password_hash($password,PASSWORD_DEFAULT);
            $pdo->prepare('INSERT INTO users (role_id,email,password_hash,status) VALUES (?,?,?,"active")')->execute([$role_id,$email,$hash]);
            $user_id = $pdo->lastInsertId();
            $pdo->prepare('INSERT INTO faculty (user_id,department_id,first_name,last_name,employment_status) VALUES (?,?,?,?,?)')->execute([$user_id,$dept_id,'John','Doe','active']);
            echo "Created teacher user: $email with password $password\n";
        }
    } else echo "Teacher user exists\n";
    $teacher_id = $pdo->query('SELECT faculty_id FROM faculty ORDER BY faculty_id DESC LIMIT 1')->fetchColumn();

    // course
    $pdo->prepare('INSERT INTO courses (course_code, title, units) VALUES (?,?,?) ON DUPLICATE KEY UPDATE title=title')->execute(['CS101', 'Introduction to Computer Science', 3]);
    $course_id = $pdo->lastInsertId();
    if(!$course_id) {
        $course_id = $pdo->query('SELECT course_id FROM courses WHERE course_code = "CS101"')->fetchColumn();
    }

    // student
    $stmt=$pdo->prepare('SELECT u.user_id FROM users u JOIN roles r ON u.role_id=r.role_id WHERE r.role_name="student" LIMIT 1'); $stmt->execute();
    if(!$stmt->fetch()){
        $role_id = null; $s=$pdo->prepare('SELECT role_id FROM roles WHERE role_name="student" LIMIT 1'); $s->execute(); $r=$s->fetch(); if($r) $role_id=$r['role_id'];
        if($role_id){
            $email = 'student@example.com';
            $password = 'student123';
            $hash = password_hash($password,PASSWORD_DEFAULT);
            $pdo->prepare('INSERT INTO users (role_id,email,password_hash,status) VALUES (?,?,?,"active")')->execute([$role_id,$email,$hash]);
            $user_id = $pdo->lastInsertId();
            $pdo->prepare('INSERT INTO students (user_id,student_number,first_name,last_name,program_id,year_level,status) VALUES (?,?,?,?,?,?,?)')->execute([$user_id,'2024-0001','Jane','Doe',$prog_id,1,'enrolled']);
            echo "Created student user: $email with password $password\n";
        }
    } else echo "Student user exists\n";
    $student_id = $pdo->query('SELECT student_id FROM students ORDER BY student_id DESC LIMIT 1')->fetchColumn();

    // room
    $pdo->prepare('INSERT INTO rooms (room_name, capacity) VALUES (?,?) ON DUPLICATE KEY UPDATE room_name=room_name')->execute(['Room 101', 30]);
    $room_id = $pdo->lastInsertId();
    if(!$room_id) {
        $room_id = $pdo->query('SELECT room_id FROM rooms WHERE room_name = "Room 101"')->fetchColumn();
    }

    // section
    $pdo->prepare('INSERT INTO sections (course_id, faculty_id, room_id, schedule, semester, academic_year) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE schedule=schedule')->execute([$course_id, $teacher_id, $room_id, 'MWF 9-10AM', '1st', '2024-2025']);
    $section_id = $pdo->lastInsertId();
    if(!$section_id) {
        $section_id = $pdo->query('SELECT section_id FROM sections ORDER BY section_id DESC LIMIT 1')->fetchColumn();
    }

    // enrollment
    $pdo->prepare('INSERT INTO enrollments (student_id, section_id, enrollment_status) VALUES (?,?,?) ON DUPLICATE KEY UPDATE enrollment_status=enrollment_status')->execute([$student_id, $section_id, 'enrolled']);

    echo "Added more data to the database.\n";
} catch(PDOException $e) {
    echo "Error seeding data: " . $e->getMessage() . "\n";
}

echo "Seeding complete\n";