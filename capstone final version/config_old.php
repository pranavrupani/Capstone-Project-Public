
<?php /*
// DATABASE CONNECTION DETAILS
$host = '127.0.0.1';
$db_user = 'root';
$db_pass = 'root';
$db_name = 'cosmic_hinduism';
$db_port = 3306;

// If true, use the SQL dump files in the repo as the data source
// (uses users.sql and deities.sql in the project root). Set to true to
// run without a MySQL server. NOTE: file mode is simple and intended for
// development only.
$use_sql_files = false;
$users_sql_path = __DIR__ . '/users.sql';
$deities_sql_path = __DIR__ . '/deities.sql';

if (!$use_sql_files) {
    $conn = new mysqli($host, $db_user, $db_pass, $db_name, $db_port);
} else {
    $conn = null; // we will use the SQL files instead
}
// Small helper to handle DB errors gracefully in dev
function handle_db_error($msg) {
    error_log('[app/db] ' . $msg);
}

if ($conn && $conn->connect_error) {
    handle_db_error('Database connection failed: ' . $conn->connect_error);
    // mark connection as unavailable for callers to handle
    $conn = null;
} elseif ($conn) {
    $conn->set_charset("utf8");
}

// --- File-based loaders for users/deities (when $use_sql_files = true) ---
function parse_insert_rows($sqlText, $tableName) {
    // Find INSERT statement for table and capture columns and values block
    $pattern = '/INSERT INTO `'.preg_quote($tableName,'/').'`\s*\(([^)]+)\)\s*VALUES\s*(.*?);/is';
    if (!preg_match($pattern, $sqlText, $m)) return [[],[]];
    $cols = array_map(function($c){ return trim(trim($c), "` "); }, explode(',', $m[1]));
    $valuesBlock = $m[2];
    // extract tuples
    preg_match_all('/\(([^)]*)\)/', $valuesBlock, $tuples);
    $rows = [];
    foreach ($tuples[1] as $t) {
        // parse CSV with single-quote enclosure
        $fields = str_getcsv($t, ',', "'");
        $fields = array_map(function($f){ $f = trim($f); if ($f === 'NULL') return null; return $f; }, $fields);
        $row = [];
        for ($i=0; $i<count($cols); $i++) {
            $row[$cols[$i]] = isset($fields[$i]) ? $fields[$i] : null;
        }
        $rows[] = $row;
    }
    return [$cols, $rows];
}

function load_users_from_file() {
    global $users_sql_path;
    if (!file_exists($users_sql_path)) return [];
    $txt = file_get_contents($users_sql_path);
    list($cols, $rows) = parse_insert_rows($txt, 'users');
    // normalize to app fields: id,name,email,password,role,created_at
    $out = [];
    foreach ($rows as $r) {
        $out[] = [
            'id' => isset($r['user_id']) ? (int)$r['user_id'] : null,
            'name' => $r['name'] ?? '',
            'email' => $r['email'] ?? '',
            'password' => $r['password'] ?? '',
            'role' => $r['role'] ?? 'learner',
            'created_at' => $r['created_at'] ?? null,
        ];
    }
    return $out;
}

function write_users_to_file($users) {
    global $users_sql_path;
    if (!file_exists($users_sql_path)) return false;
    $txt = file_get_contents($users_sql_path);
    // build values text
    $lines = [];
    foreach ($users as $u) {
        $id = (int)($u['id'] ?? 0);
        $name = str_replace("'", "''", $u['name']);
        $email = str_replace("'", "''", $u['email']);
        $pw = str_replace("'", "''", $u['password']);
        $role = str_replace("'", "''", $u['role']);
        $created = $u['created_at'] ?? date('Y-m-d H:i:s');
        $lines[] = "($id, '$name', '$email', '$pw', '$role', '$created')";
    }
    $valuesText = implode(",\n", $lines) . ";";
    // replace existing INSERT block
    $pattern = '/INSERT INTO `users`\s*\(([^)]+)\)\s*VALUES\s*(.*?);/is';
    $replacement = "INSERT INTO `users` ($1) VALUES\n" . $valuesText;
    $new = preg_replace($pattern, $replacement, $txt, 1);
    if ($new === null) return false;
    return file_put_contents($users_sql_path, $new) !== false;
}

function load_deities_from_file() {
    global $deities_sql_path;
    if (!file_exists($deities_sql_path)) return [];
    $txt = file_get_contents($deities_sql_path);
    list($cols, $rows) = parse_insert_rows($txt, 'deities');
    $out = [];
    foreach ($rows as $r) {
        $out[] = [
            'deity_id' => isset($r['deity_id']) ? (int)$r['deity_id'] : null,
            'name' => $r['name'] ?? '',
            'description' => $r['description'] ?? '',
            'mythology' => $r['mythology'] ?? '',
            'image_name' => $r['image_name'] ?? '',
        ];
    }
    return $out;
}

function write_deities_to_file($deities) {
    global $deities_sql_path;
    if (!file_exists($deities_sql_path)) return false;
    $txt = file_get_contents($deities_sql_path);
    $lines = [];
    foreach ($deities as $d) {
        $id = (int)($d['deity_id'] ?? 0);
        $name = str_replace("'", "''", $d['name']);
        $desc = str_replace("'", "''", $d['description']);
        $myth = str_replace("'", "''", $d['mythology']);
        $img = str_replace("'", "''", $d['image_name']);
        $lines[] = "($id, '$name', '$desc', '$myth', '$img')";
    }
    $valuesText = implode(",\n", $lines) . ";";
    $pattern = '/INSERT INTO `deities`\s*\(([^)]+)\)\s*VALUES\s*(.*?);/is';
    $replacement = "INSERT INTO `deities` ($1) VALUES\n" . $valuesText;
    $new = preg_replace($pattern, $replacement, $txt, 1);
    if ($new === null) return false;
    return file_put_contents($deities_sql_path, $new) !== false;
}
//  Get all users from database
function getAllUsers() {
    global $conn;
    global $use_sql_files;
    if (!empty($use_sql_files)) {
        $rows = load_users_from_file();
        // return newest first to match previous behavior
        return array_reverse($rows);
    }
    if (!$conn) {
        handle_db_error('getAllUsers: no DB connection');
        return [];
    }

    // Select all users from the users table
    $sql = "SELECT id, name, email, password, role, created_at FROM users ORDER BY id DESC";
    $result = $conn->query($sql);

    // Check if query failed
    if (!$result) {
        handle_db_error("getAllUsers query failed: " . $conn->error);
        return [];
    }

    // Convert results to array
    $users = [];
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    return $users;
}

// Get user by email 
function getUserByEmail($email) {
    global $conn;
    global $conn, $use_sql_files;
    if (!empty($use_sql_files)) {
        $rows = load_users_from_file();
        foreach ($rows as $r) {
            if (strcasecmp($r['email'], $email) === 0) return $r;
        }
        return null;
    }

    if (!$conn) {
        handle_db_error('getUserByEmail: no DB connection');
        return null;
    }

    // Escape email to prevent SQL injection
    $email = $conn->real_escape_string($email);

    // Query to find user by email
    $sql = "SELECT id, name, email, password, role FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    // Check if query failed
    if (!$result) {
        handle_db_error("getUserByEmail query failed: " . $conn->error);
        return null;
    }

    // Check if user was found
    if ($result->num_rows > 0) {
        return $result->fetch_assoc(); // Return the user
    }

    return null; // User not found
}

//  Add new user to the database

function addNewUser($name, $email, $password, $role = 'learner') {
    global $conn;
    global $conn, $use_sql_files;
    if (!empty($use_sql_files)) {
        // file mode: append new user into users.sql by rewriting the INSERT block
        $users = load_users_from_file();
        foreach ($users as $u) if (strcasecmp($u['email'], $email) === 0) return false;
        $max = 0; foreach ($users as $u) if (!empty($u['id'])) $max = max($max, (int)$u['id']);
        $new = [
            'id' => $max + 1,
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role' => $role,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $users[] = $new;
        return write_users_to_file($users);
    }
    if (!$conn) {
        handle_db_error('addNewUser: no DB connection');
        return false;
    }

    // Check if email already exists
    if (getUserByEmail($email)) {
        return false; // Email already in use
    }

    // Escape inputs to prevent SQL injection
    $name = $conn->real_escape_string($name);
    $email = $conn->real_escape_string($email);

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $hashed_password = $conn->real_escape_string($hashed_password);

    // Insert new user into database
    $role = $conn->real_escape_string($role);
    $sql = "INSERT INTO users (name, email, password, role) 
        VALUES ('$name', '$email', '$hashed_password', '$role')";

    // Execute the query
    if ($conn->query($sql)) {
        return true; // Success
    } else {
        handle_db_error("Error adding user: " . $conn->error);
        return false;
    }
}

// Get a single user by ID
function getUserById($id) {
    global $conn;
    global $use_sql_files;
    if (!empty($use_sql_files)) {
        $rows = load_users_from_file();
        foreach ($rows as $r) if ((int)$r['id'] === (int)$id) return $r;
        return null;
    }
    $id = (int)$id;
    $sql = "SELECT id, name, email, password, role FROM users WHERE id = $id";
    $result = $conn->query($sql);
    if (!$result || $result->num_rows == 0) {
        return null;
    }
    return $result->fetch_assoc();
}

// Update a user's information. If $password is null, do not change the password.
function updateUser($id, $name, $email, $role, $password = null) {
    global $conn, $use_sql_files;
    if (!empty($use_sql_files)) {
        $rows = load_users_from_file();
        $changed = false;
        foreach ($rows as &$r) {
            if ((int)$r['id'] === (int)$id) {
                $r['name'] = $name;
                $r['email'] = $email;
                $r['role'] = $role;
                if ($password !== null && $password !== '') $r['password'] = password_hash($password, PASSWORD_BCRYPT);
                $changed = true;
                break;
            }
        }
        if ($changed) return write_users_to_file($rows);
        return false;
    }
    $id = (int)$id;
    $name = $conn->real_escape_string($name);
    $email = $conn->real_escape_string($email);
    $role = $conn->real_escape_string($role);

    if ($password !== null && $password !== '') {
        $hashed = $conn->real_escape_string(password_hash($password, PASSWORD_BCRYPT));
        $sql = "UPDATE users SET name = '$name', email = '$email', role = '$role', password = '$hashed' WHERE id = $id";
    } else {
        $sql = "UPDATE users SET name = '$name', email = '$email', role = '$role' WHERE id = $id";
    }

    return $conn->query($sql) ? true : false;
}

// Delete a user by ID
function deleteUser($id) {
    global $conn, $use_sql_files;
    if (!empty($use_sql_files)) {
        $rows = load_users_from_file();
        $new = [];
        $found = false;
        foreach ($rows as $r) {
            if ((int)$r['id'] === (int)$id) { $found = true; continue; }
            $new[] = $r;
        }
        if ($found) return write_users_to_file($new);
        return false;
    }
    $id = (int)$id;
    $sql = "DELETE FROM users WHERE id = $id";
    return $conn->query($sql) ? true : false;
}

// --- Deities helpers ---
function getAllDeities() {
    global $conn, $use_sql_files;
    if (!empty($use_sql_files)) {
        return load_deities_from_file();
    }
    if (!$conn) {
        handle_db_error('getAllDeities: no DB connection');
        return [];
    }
    $sql = "SELECT deity_id, name, description, mythology, image_name FROM deities ORDER BY deity_id ASC";
    $res = $conn->query($sql);
    if (!$res) {
        handle_db_error('getAllDeities query failed: ' . $conn->error);
        return [];
    }
    $out = [];
    while ($row = $res->fetch_assoc()) $out[] = $row;
    return $out;
}

function getDeityById($id) {
    global $conn, $use_sql_files;
    if (!empty($use_sql_files)) {
        $rows = load_deities_from_file();
        foreach ($rows as $r) if ((int)$r['deity_id'] === (int)$id) return $r;
        return null;
    }
    if (!$conn) {
        handle_db_error('getDeityById: no DB connection');
        return null;
    }
    $id = (int)$id;
    $sql = "SELECT deity_id, name, description, mythology, image_name FROM deities WHERE deity_id = $id LIMIT 1";
    $res = $conn->query($sql);
    if (!$res || $res->num_rows == 0) return null;
    return $res->fetch_assoc();
}

function findDeityImagePath($image_name, $deity_name = '') {
    // Try a list of candidate filenames in assets/images
    $candidates = [];
    if ($image_name) $candidates[] = $image_name;
    if ($image_name) $candidates[] = $image_name . '.jpeg';
    if ($image_name) $candidates[] = $image_name . '.jpg';
    if ($image_name) $candidates[] = $image_name . '.png';
    if ($deity_name) {
        $candidates[] = $deity_name . '.jpg';
        $candidates[] = $deity_name . '.jpeg';
        $candidates[] = $deity_name . '.png';
    }
    foreach ($candidates as $f) {
        $f = trim($f);
        if ($f === '') continue;
        $path = __DIR__ . '/assets/images/' . $f;
        if (file_exists($path)) {
            return 'assets/images/' . $f;
        }
    }
    // fallback placeholder
    return 'assets/images/placeholder.png';
}

// Deity CRUD helpers for admin
function createDeity($name, $description, $mythology, $image_name) {
    global $conn, $use_sql_files;
    if (!empty($use_sql_files)) {
        $all = load_deities_from_file();
        $max = 0; foreach ($all as $d) if (!empty($d['deity_id'])) $max = max($max, (int)$d['deity_id']);
        $new = [
            'deity_id' => $max + 1,
            'name' => $name,
            'description' => $description,
            'mythology' => $mythology,
            'image_name' => $image_name,
        ];
        $all[] = $new;
        return write_deities_to_file($all);
    }
    if (!$conn) return false;
    $name = $conn->real_escape_string($name);
    $description = $conn->real_escape_string($description);
    $mythology = $conn->real_escape_string($mythology);
    $image_name = $conn->real_escape_string($image_name);
    $sql = "INSERT INTO deities (name, description, mythology, image_name) VALUES ('$name','$description','$mythology','$image_name')";
    return $conn->query($sql) ? true : false;
}

function updateDeity($id, $name, $description, $mythology, $image_name) {
    global $conn, $use_sql_files;
    if (!empty($use_sql_files)) {
        $all = load_deities_from_file();
        $changed = false;
        foreach ($all as &$d) {
            if ((int)$d['deity_id'] === (int)$id) {
                $d['name'] = $name;
                $d['description'] = $description;
                $d['mythology'] = $mythology;
                $d['image_name'] = $image_name;
                $changed = true; break;
            }
        }
        if ($changed) return write_deities_to_file($all);
        return false;
    }
    if (!$conn) return false;
    $id = (int)$id;
    $name = $conn->real_escape_string($name);
    $description = $conn->real_escape_string($description);
    $mythology = $conn->real_escape_string($mythology);
    $image_name = $conn->real_escape_string($image_name);
    $sql = "UPDATE deities SET name='$name', description='$description', mythology='$mythology', image_name='$image_name' WHERE deity_id=$id";
    return $conn->query($sql) ? true : false;
}

function deleteDeity($id) {
    global $conn, $use_sql_files;
    if (!empty($use_sql_files)) {
        $all = load_deities_from_file();
        $new = []; $found = false;
        foreach ($all as $d) {
            if ((int)$d['deity_id'] === (int)$id) { $found = true; continue; }
            $new[] = $d;
        }
        if ($found) return write_deities_to_file($new);
        return false;
    }
    if (!$conn) return false;
    $id = (int)$id;
    $sql = "DELETE FROM deities WHERE deity_id=$id";
    return $conn->query($sql) ? true : false;
}

*/ ?>