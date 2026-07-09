<?php
// This file acts as the app's data layer without a live database server.
$use_sql_files = true;
$users_sql_path = __DIR__ . '/users.sql';
$deities_sql_path = __DIR__ . '/deities.sql';
$lessons_sql_path = __DIR__ . '/lessons.sql';
$completions_path = __DIR__ . '/progress_data.php';

// Simple error logging helper for database-like operations.
function handle_db_error(string $msg): void {
    error_log('[app/db] ' . $msg);
}

// Parse SQL INSERT statements from a dump file and return rows as PHP arrays.
// This allows the app to treat the static SQL files like a simple data source.
function parse_insert_rows(string $sql, string $table): array {
    if (!preg_match('/INSERT INTO `'.preg_quote($table, '/').'`\s*\(([^)]+)\)\s*VALUES\s*(.*?);/is', $sql, $m)) {
        return [];
    }

    $cols = array_map(fn($c) => trim(trim($c), "` "), explode(',', $m[1]));
    preg_match_all('/\(([^)]*)\)/', $m[2], $tuples);

    $rows = [];
    foreach ($tuples[1] as $t) {
        $fields = str_getcsv($t, ',', "'");
        $row = [];
        foreach ($cols as $i => $col) {
            $row[$col] = isset($fields[$i]) ? trim($fields[$i]) : null;
        }
        $rows[] = $row;
    }

    return $rows;
}

// User data helpers load and write the users SQL file, mapping each row into a PHP-friendly structure.
function load_users_from_file(): array {
    global $users_sql_path;
    if (!file_exists($users_sql_path)) {
        return [];
    }

    $rows = parse_insert_rows(file_get_contents($users_sql_path), 'users');
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

// Writing the full users array back to users.sql, rebuilding an INSERT statement.
function write_users_to_file(array $users): bool {
    global $users_sql_path;
    $lines = [];

    foreach ($users as $u) {
        $id = (int)($u['id'] ?? 0);
        $name = str_replace("'", "''", $u['name'] ?? '');
        $email = str_replace("'", "''", $u['email'] ?? '');
        $pw = str_replace("'", "''", $u['password'] ?? '');
        $role = str_replace("'", "''", $u['role'] ?? 'learner');
        $created = $u['created_at'] ?? date('Y-m-d H:i:s');
        $lines[] = "($id, '$name', '$email', '$pw', '$role', '$created')";
    }

    $txt = "-- users.sql (generated)\n\nINSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`, `created_at`) VALUES\n" . implode(",\n", $lines) . ";\n";
    return file_put_contents($users_sql_path, $txt) !== false;
}

// Deity data helpers load and write the deities SQL file in the same way as users.
function load_deities_from_file(): array {
    global $deities_sql_path;
    if (!file_exists($deities_sql_path)) {
        return [];
    }

    $rows = parse_insert_rows(file_get_contents($deities_sql_path), 'deities');
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

function write_deities_to_file(array $deities): bool {
    global $deities_sql_path;
    $lines = [];

    foreach ($deities as $d) {
        $id = (int)($d['deity_id'] ?? 0);
        $name = str_replace("'", "''", $d['name'] ?? '');
        $desc = str_replace("'", "''", $d['description'] ?? '');
        $myth = str_replace("'", "''", $d['mythology'] ?? '');
        $img = str_replace("'", "''", $d['image_name'] ?? '');
        $lines[] = "($id, '$name', '$desc', '$myth', '$img')";
    }

    $txt = "-- deities.sql (generated)\n\nINSERT INTO `deities` (`deity_id`, `name`, `description`, `mythology`, `image_name`) VALUES\n" . implode(",\n", $lines) . ";\n";
    return file_put_contents($deities_sql_path, $txt) !== false;
}

// User lookup and helper functions.
function getAllUsers(): array {
    return array_reverse(load_users_from_file());
}

function getUserByEmail($email) {
    foreach (load_users_from_file() as $u) {
        if (strcasecmp($u['email'], $email) === 0) {
            return $u;
        }
    }
    return null;
}

function addNewUser($name, $email, $password, $role = 'learner') {
    $users = load_users_from_file();
    foreach ($users as $u) {
        if (strcasecmp($u['email'], $email) === 0) {
            return false;
        }
    }

    $max = 0;
    foreach ($users as $u) {
        if (!empty($u['id'])) {
            $max = max($max, (int)$u['id']);
        }
    }

    $users[] = [
        'id' => $max + 1,
        'name' => $name,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_BCRYPT),
        'role' => $role,
        'created_at' => date('Y-m-d H:i:s'),
    ];

    return write_users_to_file($users);
}

// Lookup helpers for user records.
function getUserById($id) {
    foreach (load_users_from_file() as $u) {
        if ((int)$u['id'] === (int)$id) {
            return $u;
        }
    }
    return null;
}

function updateUser($id, $name, $email, $role, $password = null) {
    $rows = load_users_from_file();
    $changed = false;

    foreach ($rows as &$r) {
        if ((int)$r['id'] === (int)$id) {
            $r['name'] = $name;
            $r['email'] = $email;
            $r['role'] = $role;
            if ($password !== null && $password !== '') {
                $r['password'] = password_hash($password, PASSWORD_BCRYPT);
            }
            $changed = true;
            break;
        }
    }

    return $changed ? write_users_to_file($rows) : false;
}

function deleteUser($id) {
    $rows = load_users_from_file();
    $out = [];
    foreach ($rows as $r) {
        if ((int)$r['id'] !== (int)$id) {
            $out[] = $r;
        }
    }
    return write_users_to_file($out);
}

function getAllDeities(): array {
    return load_deities_from_file();
}

function getDeityById($id) {
    foreach (load_deities_from_file() as $d) {
        if ((int)$d['deity_id'] === (int)$id) {
            return $d;
        }
    }
    return null;
}

// Find a deity image file, trying the configured name and common extensions.
// Falls back to a placeholder image when no match is found.
function createDeity($name, $description, $mythology, $image_name) {
    $all = load_deities_from_file();
    $max = 0;
    foreach ($all as $d) {
        if (!empty($d['deity_id'])) {
            $max = max($max, (int)$d['deity_id']);
        }
    }

    $all[] = [
        'deity_id' => $max + 1,
        'name' => $name,
        'description' => $description,
        'mythology' => $mythology,
        'image_name' => $image_name,
    ];

    return write_deities_to_file($all);
}

function updateDeity($id, $name, $description, $mythology, $image_name) {
    $rows = load_deities_from_file();
    $changed = false;

    foreach ($rows as &$r) {
        if ((int)$r['deity_id'] === (int)$id) {
            $r['name'] = $name;
            $r['description'] = $description;
            $r['mythology'] = $mythology;
            $r['image_name'] = $image_name;
            $changed = true;
            break;
        }
    }

    return $changed ? write_deities_to_file($rows) : false;
}

function deleteDeity($id) {
    $rows = load_deities_from_file();
    $out = [];
    foreach ($rows as $r) {
        if ((int)$r['deity_id'] !== (int)$id) {
            $out[] = $r;
        }
    }
    return write_deities_to_file($out);
}

// Find a deity image file in assets/images, trying the configured name and common extensions.
// Falls back to a placeholder image when no file exists.
function findDeityImagePath($image_name, $deity_name = '') {
    $candidates = [];
    if ($image_name) {
        $candidates[] = $image_name;
    }

    foreach (['.jpg', '.jpeg', '.png'] as $ext) {
        if ($image_name && !str_ends_with($image_name, $ext)) {
            $candidates[] = $image_name . $ext;
        }
    }

    if ($deity_name) {
        foreach (['.jpg', '.jpeg', '.png'] as $ext) {
            $candidates[] = $deity_name . $ext;
        }
    }

    foreach ($candidates as $f) {
        $f = trim($f);
        if ($f === '') {
            continue;
        }

        $p = __DIR__ . '/assets/images/' . $f;
        if (file_exists($p)) {
            return 'assets/images/' . $f;
        }
    }

    return 'assets/images/placeholder.png';
}

// Lesson data comes from lessons.sql, parsed into PHP arrays for course and lesson lookup.
function load_lessons_from_file(): array {
    global $lessons_sql_path;
    if (!file_exists($lessons_sql_path)) {
        return [];
    }

    $rows = parse_insert_rows(file_get_contents($lessons_sql_path), 'lessons');
    $out = [];
    foreach ($rows as $r) {
        $out[] = [
            'lesson_id' => isset($r['lesson_id']) ? (int)$r['lesson_id'] : null,
            'course_id' => isset($r['course_id']) ? (int)$r['course_id'] : null,
            'title' => $r['title'] ?? '',
            'content' => $r['content'] ?? '',
            'position' => isset($r['position']) ? (int)$r['position'] : 0,
        ];
    }
    return $out;
}

function getLessonsByCourse(int $course_id): array {
    $all = load_lessons_from_file();
    $out = array_filter($all, fn($l) => (int)$l['course_id'] === (int)$course_id);
    usort($out, fn($a, $b) => $a['position'] <=> $b['position']);
    return array_values($out);
}

function getLessonById(int $id) {
    foreach (load_lessons_from_file() as $l) {
        if ((int)$l['lesson_id'] === (int)$id) {
            return $l;
        }
    }
    return null;
}

// Progress storage is saved in a PHP file that returns an array of completed lesson IDs per user.
function ensure_progress_storage(): string {
    global $completions_path;
    if (!file_exists($completions_path)) {
        $content = "<?php\n\n// Local lesson progress storage.\nreturn [];\n";
        file_put_contents($completions_path, $content);
    }
    return $completions_path;
}

function load_completions(): array {
    global $completions_path;
    ensure_progress_storage();
    $data = include $completions_path;
    return is_array($data) ? $data : [];
}

function save_completions(array $data): bool {
    global $completions_path;
    ensure_progress_storage();

    $normalized = [];
    foreach ($data as $uid => $lessons) {
        $ids = [];
        foreach ((array)$lessons as $lid) {
            $id = (int)$lid;
            if ($id > 0 && !in_array($id, $ids, true)) {
                $ids[] = $id;
            }
        }
        if ($ids !== []) {
            $normalized[(string)$uid] = $ids;
        }
    }

    $content = "<?php\n\n// Local lesson progress storage.\nreturn " . var_export($normalized, true) . ";\n";
    return file_put_contents($completions_path, $content) !== false;
}

function markLessonComplete(int $user_id, int $lesson_id): bool {
    $data = load_completions();
    $uid = (string)$user_id;
    if (!isset($data[$uid]) || !is_array($data[$uid])) {
        $data[$uid] = [];
    }
    if (!in_array($lesson_id, $data[$uid], true)) {
        $data[$uid][] = $lesson_id;
    }
    return save_completions($data);
}

function isLessonComplete(int $user_id, int $lesson_id): bool {
    $data = load_completions();
    $uid = (string)$user_id;
    return isset($data[$uid]) && is_array($data[$uid]) && in_array($lesson_id, $data[$uid], true);
}

function getUserLessonCompletions(int $user_id): array {
    $data = load_completions();
    $uid = (string)$user_id;
    if (!isset($data[$uid]) || !is_array($data[$uid])) {
        return [];
    }

    $out = [];
    foreach ($data[$uid] as $lid) {
        $out[] = ['lesson_id' => (int)$lid, 'completed_at' => null];
    }
    return $out;
}

function unmarkLessonComplete(int $user_id, int $lesson_id): bool {
    $data = load_completions();
    $uid = (string)$user_id;
    if (!isset($data[$uid]) || !is_array($data[$uid])) {
        return false;
    }

    $before = $data[$uid];
    $data[$uid] = array_values(array_filter($data[$uid], fn($x) => (int)$x !== (int)$lesson_id));
    if ($data[$uid] === $before) {
        return false;
    }
    return save_completions($data);
}

function clearUserCompletions(int $user_id): bool {
    $data = load_completions();
    $uid = (string)$user_id;
    if (isset($data[$uid])) {
        unset($data[$uid]);
    }
    return save_completions($data);
}
?>