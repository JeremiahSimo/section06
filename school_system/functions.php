<?php
require_once __DIR__ . '/db.php';
if(session_status()===PHP_SESSION_NONE) session_start();

function is_logged_in(){ return isset($_SESSION['user']); }
function require_login(){ if(!is_logged_in()){ header('Location: /section06/school_system/login.php'); exit; }}
function require_role($role_name){ require_login(); if(strtolower($_SESSION['user']['role_name'])!==strtolower($role_name)){ http_response_code(403); exit('Access denied'); }}

function get_roles(){ global $pdo; $stmt=$pdo->query('SELECT * FROM roles ORDER BY role_name'); return $stmt->fetchAll(); }

// CSRF helpers
function csrf_token(){ if(empty($_SESSION['_csrf'])) $_SESSION['_csrf']=bin2hex(random_bytes(16)); return $_SESSION['_csrf']; }
function csrf_field(){ $t=csrf_token(); return '<input type="hidden" name="_csrf" value="'.htmlspecialchars($t).'">'; }
function verify_csrf($token){ return isset($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'],$token); }

function e($v){ return htmlspecialchars($v,ENT_QUOTES,'UTF-8'); }

function find_role_id($role_name){ global $pdo; $stmt=$pdo->prepare('SELECT role_id FROM roles WHERE role_name=? LIMIT 1'); $stmt->execute([$role_name]); $r=$stmt->fetch(); return $r?$r['role_id']:null; }

function get_user_by_email($email){ global $pdo; $stmt=$pdo->prepare('SELECT u.user_id,u.email,u.password_hash,u.role_id,r.role_name,u.status FROM users u JOIN roles r ON u.role_id=r.role_id WHERE u.email=? LIMIT 1'); $stmt->execute([$email]); return $stmt->fetch(); }

function get_user_by_id($user_id){ global $pdo; $stmt=$pdo->prepare('SELECT u.user_id,u.email,u.role_id,r.role_name,u.status FROM users u JOIN roles r ON u.role_id=r.role_id WHERE u.user_id=? LIMIT 1'); $stmt->execute([$user_id]); $user=$stmt->fetch(); if(!$user) return null; // attach name if student or faculty
    if(strtolower($user['role_name'])==='student'){
        $s=$pdo->prepare('SELECT student_number,first_name,last_name FROM students WHERE user_id=? LIMIT 1'); $s->execute([$user_id]); $sd=$s->fetch(); if($sd) $user['name']=$sd['first_name'].' '.$sd['last_name'];
    } elseif(strtolower($user['role_name'])==='teacher' || strtolower($user['role_name'])==='faculty'){
        $f=$pdo->prepare('SELECT first_name,last_name FROM faculty WHERE user_id=? LIMIT 1'); $f->execute([$user_id]); $fd=$f->fetch(); if($fd) $user['name']=$fd['first_name'].' '.$fd['last_name'];
    }
    return $user; }

function create_user_with_profile($email,$password,$role_name,$first_name,$last_name){ global $pdo; // determine role id
    $role_id=find_role_id($role_name);
    if(!$role_id) throw new Exception('Invalid role');
    $hash=password_hash($password,PASSWORD_DEFAULT);
    $stmt=$pdo->prepare('INSERT INTO users (role_id,email,password_hash,status) VALUES (?,?,?,"active")');
    $stmt->execute([$role_id,$email,$hash]);
    $user_id=$pdo->lastInsertId();
    if(strtolower($role_name)==='student'){
        $student_number = 'S'.time();
        $ins=$pdo->prepare('INSERT INTO students (user_id,student_number,first_name,last_name,year_level,status) VALUES (?,?,?,?,1,"active")');
        $ins->execute([$user_id,$student_number,$first_name,$last_name]);
    } elseif(strtolower($role_name)==='teacher' || strtolower($role_name)==='faculty'){
        $ins=$pdo->prepare('INSERT INTO faculty (user_id,first_name,last_name,employment_status) VALUES (?,?,?,"active")');
        $ins->execute([$user_id,$first_name,$last_name]);
    }
    return $user_id;
}
