<?php
/**
 * Instafolio Installer - AJAX Handler
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$action = $_GET['action'] ?? '';

$installer_dir = __DIR__;
$root_dir = dirname($installer_dir);
$app_config = $root_dir . '/application/config/app-config.php';
$htaccess = $root_dir . '/.htaccess';

function respond($success, $message = '', $data = []) {
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message
    ], $data));
    exit;
}

function getBasePath() {
    $script_path = dirname($_SERVER['SCRIPT_NAME']);
    if ($script_path === '/' || $script_path === '\\') {
        return '/';
    }
    return rtrim($script_path, '/') . '/';
}

if ($action === 'test_db') {
    $db_host = $_POST['db_host'] ?? 'localhost';
    $db_user = $_POST['db_user'] ?? 'root';
    $db_pass = $_POST['db_pass'] ?? '';
    $db_name = $_POST['db_name'] ?? 'instafolio';
    $create_db = isset($_POST['create_db']);
    
    try {
        $conn = new mysqli($db_host, $db_user, $db_pass);
        
        if ($conn->connect_error) {
            respond(false, "Connection failed: " . $conn->connect_error);
        }
        
        if ($create_db) {
            $conn->query("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        }
        
        if (!$conn->select_db($db_name)) {
            respond(false, "Cannot select database: " . $conn->error);
        }
        
        $conn->close();
        respond(true, 'Database connection successful');
        
    } catch (Exception $e) {
        respond(false, $e->getMessage());
    }
}

if ($action === 'install') {
    $db_host = $_POST['db_host'] ?? 'localhost';
    $db_user = $_POST['db_user'] ?? 'root';
    $db_pass = $_POST['db_pass'] ?? '';
    $db_name = $_POST['db_name'] ?? 'instafolio';
    $site_url = rtrim($_POST['site_url'] ?? 'http://localhost', '/') . '/';
    $company_name = $_POST['company_name'] ?? 'My Company';
    $timezone = $_POST['timezone'] ?? 'UTC';
    $date_format = $_POST['date_format'] ?? 'd/m/Y';
    
    $base_path = getBasePath();
    
    try {
        // Connect to database
        $conn = new mysqli($db_host, $db_user, $db_pass);
        
        if ($conn->connect_error) {
            respond(false, "Database connection failed: " . $conn->connect_error);
        }
        
        // Create database
        $conn->query("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $conn->select_db($db_name);
        
        // Import database structure
        $sql_file = $installer_dir . '/database.sql';
        if (!file_exists($sql_file)) {
            respond(false, "Database SQL file not found");
        }
        
        $sql = file_get_contents($sql_file);
        
        // Execute multi-query
        if ($conn->multi_query($sql)) {
            do {
                if ($result = $conn->store_result()) {
                    $result->free();
                }
            } while ($conn->more_results() && $conn->next_result());
        }
        
        if ($conn->error) {
            respond(false, "Error importing database: " . $conn->error);
        }
        
        // Import sample data
        $sample_file = $installer_dir . '/sample_data.sql';
        if (file_exists($sample_file)) {
            $sample_sql = file_get_contents($sample_file);
            $conn->multi_query($sample_sql);
            do {
                if ($result = $conn->store_result()) {
                    $result->free();
                }
            } while ($conn->more_results() && $conn->next_result());
        }
        
        // Update company name
        $conn->query("UPDATE tbloptions SET value = '" . $conn->real_escape_string($company_name) . "' WHERE name = 'companyname'");
        
        // Update timezone
        $conn->query("UPDATE tbloptions SET value = '" . $conn->real_escape_string($timezone) . "' WHERE name = 'timezone'");
        
        // Update date format
        $conn->query("UPDATE tbloptions SET value = '" . $conn->real_escape_string($date_format) . "' WHERE name = 'date_format'");
        
        $conn->close();
        
        // Update config file
        $config_content = file_get_contents($app_config);
        
        // Replace database credentials
        $config_content = preg_replace("/define\('APP_DB_HOSTNAME','.*?'\);/", "define('APP_DB_HOSTNAME','" . addslashes($db_host) . "');", $config_content);
        $config_content = preg_replace("/define\('APP_DB_USERNAME','.*?'\);/", "define('APP_DB_USERNAME','" . addslashes($db_user) . "');", $config_content);
        $config_content = preg_replace("/define\('APP_DB_PASSWORD','.*?'\);/", "define('APP_DB_PASSWORD','" . addslashes($db_pass) . "');", $config_content);
        $config_content = preg_replace("/define\('APP_DB_NAME','.*?'\);/", "define('APP_DB_NAME','" . addslashes($db_name) . "');", $config_content);
        
        file_put_contents($app_config, $config_content);
        
        // Update .htaccess for domain root or subfolder installation
        if (file_exists($htaccess)) {
            $htaccess_content = file_get_contents($htaccess);
            
            // Replace RewriteBase dynamically
            $htaccess_content = preg_replace('/RewriteBase\s+.*?\//', 'RewriteBase ' . $base_path, $htaccess_content);
            
            file_put_contents($htaccess, $htaccess_content);
        }
        
        // Generate installer lock file
        file_put_contents($installer_dir . '/installed.lock', json_encode([
            'installed' => true,
            'date' => date('Y-m-d H:i:s'),
            'version' => '3.1.6',
            'base_path' => $base_path
        ]));
        
        // Copy custom_lang.php to application/language/english/ if not exists
        $app_lang_dir = $root_dir . '/application/language/english';
        $custom_lang_file = $app_lang_dir . '/custom_lang.php';
        if (!file_exists($custom_lang_file)) {
            $custom_lang_src = $installer_dir . '/custom_lang.php';
            if (file_exists($custom_lang_src)) {
                @mkdir($app_lang_dir, 0755, true);
                copy($custom_lang_src, $custom_lang_file);
            }
        }
        
        // Determine admin URL - use index.php/admin for proper routing
        $admin_url = rtrim($site_url, '/') . '/index.php/admin';
        
        // Also determine login URL
        $login_url = rtrim($site_url, '/') . '/index.php/login';
        
        respond(true, 'Installation completed successfully', [
            'admin_url' => $admin_url,
            'login_url' => $login_url,
            'base_path' => $base_path
        ]);
        
    } catch (Exception $e) {
        respond(false, $e->getMessage());
    }
}

respond(false, 'Invalid action');
