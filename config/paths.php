<?php
// config/paths.php
// To make it easy to manage my URLs when I upload to production, that is cpanel
class PathConfig
{
    private static $instance = null;
    private $config;

    private function __construct()
    {
        // Base path configuration
        $this->config = [
            'development' => [
                'base_path' => '/hmis',
                'paths' => [
                    'home' => '/', 
                    'views' => [
                        'appointments' => '/views/appointments/',
                        'patients' => '/views/patients/',
                        'triage' => '/views/triage/',
                        'outpatient' => '/views/outpatient/',
                        'consultations' => '/views/consultations/',
                        'prescriptions' => '/views/prescriptions/',
                        'billing' => '/views/billing/',
                        'room' => '/views/room/',
                        'items' => '/views/items/',
                        'queue' => '/views/queue/',
                        'auth' => '/views/auth/',
                        'staff' => '/views/staff/',
                        'departments' => '/views/departments/',
                        'doctors' => '/views/doctors/',
                        'medications' => '/views/medications/',
                        'pharmacy' => '/views/pharmacy/',
                        'lab' => '/views/lab/',
                        'nutrition' => '/views/nutrition/',
                        'mch' => '/views/mch/',
                        'dental' => '/views/dental/',
                        'physio' => '/views/physio/',
                        'theater' => '/views/theater/',
                        'inpatient' => '/views/inpatient/',
                        'stores' => '/views/stores/',
                        'insurance' => '/views/insurance/',
                        'accounting' => '/views/accounting/',
                        'moh' => '/views/moh/',
                        'activities' => '/views/activities/'
                    ],
                    'handlers' => '/handlers/',
                    'auth' => [
                        'login' => '/views/auth/login.php',
                        'logout' => '/views/auth/logout.php'
                    ],
                    'classes' => [
                        'department' => '/classes/Department.php',
                        'leave' => '/classes/Leave.php',
                        'user' => '/classes/User.php',
                        'PatientQueue' => '/classes/PatientQueue.php',
                        'Triage' => '/classes/Triage.php',
                        'VitalSigns' => '/classes/VitalSigns.php'
                    ],
                    'config' => [
                        'config' => '/config/config.php',
                        'database' => '/config/database.php',
                        'paths' => '/config/paths.php'
                    ],
                    'assets' => [
                        'dist' => '/dist/',
                        'css' => '/assets/css/'
                    ],
                    'includes' => [
                        'auth' => '/includes/auth.php',
                        'functions' => '/includes/functions.php'
                    ],
                    'public' => [
                        'css' => '/public/css/',
                        'images' => '/public/images/',
                        'js' => '/public/js/',
                        'sponsor_images' => '/src/img/logo/'
                    ],
                    'templates' => [
                        'footer_scripts' => '/templates/footer_scripts.php',
                        'header' => '/templates/header.php',
                        'main_footer' => '/templates/main_footer.php',
                        'navbar' => '/templates/navbar.php',
                        'sidebar' => '/templates/sidebar.php'
                    ]
                ]
            ],
            'production' => [
                'base_path' => 'https://all-lands-digital-collins.trycloudflare.com/hmis', // Update this with your production base path
                'paths' => [
                    // Development paths go here. Copy and paste from the development paths above.
                    'home' => '/', 
                    'views' => [
                        'appointments' => '/views/appointments/',
                        'patients' => '/views/patients/',
                        'triage' => '/views/triage/',
                        'outpatient' => '/views/outpatient/',
                        'consultations' => '/views/consultations/',
                        'prescriptions' => '/views/prescriptions/',
                        'billing' => '/views/billing/',
                        'room' => '/views/room/',
                        'items' => '/views/items/',
                        'queue' => '/views/queue/',
                        'auth' => '/views/auth/',
                        'staff' => '/views/staff/',
                        'departments' => '/views/departments/',
                        'doctors' => '/views/doctors/',
                        'medications' => '/views/medications/',
                        'pharmacy' => '/views/pharmacy/',
                        'lab' => '/views/lab/',
                        'nutrition' => '/views/nutrition/',
                        'mch' => '/views/mch/',
                        'dental' => '/views/dental/',
                        'physio' => '/views/physio/',
                        'theater' => '/views/theater/',
                        'inpatient' => '/views/inpatient/',
                        'stores' => '/views/stores/',
                        'insurance' => '/views/insurance/',
                        'accounting' => '/views/accounting/',
                        'moh' => '/views/moh/',
                        'activities' => '/views/activities/'
                    ],
                    'handlers' => '/handlers/',
                    'auth' => [
                        'login' => '/views/auth/login.php',
                        'logout' => '/views/auth/logout.php'
                    ],
                    'classes' => [
                        'department' => '/classes/Department.php',
                        'leave' => '/classes/Leave.php',
                        'user' => '/classes/User.php',
                        'PatientQueue' => '/classes/PatientQueue.php',
                        'Triage' => '/classes/Triage.php',
                        'VitalSigns' => '/classes/VitalSigns.php'
                    ],
                    'config' => [
                        'config' => '/config/config.php',
                        'database' => '/config/database.php',
                        'paths' => '/config/paths.php'
                    ],
                    'assets' => [
                        'dist' => '/dist/',
                        'css' => '/assets/css/'
                    ],
                    'includes' => [
                        'auth' => '/includes/auth.php',
                        'functions' => '/includes/functions.php'
                    ],
                    'public' => [
                        'css' => '/public/css/',
                        'images' => '/public/images/',
                        'js' => '/public/js/',
                        'sponsor_images' => '/src/img/logo/'
                    ],
                    'templates' => [
                        'footer_scripts' => '/templates/footer_scripts.php',
                        'header' => '/templates/header.php',
                        'main_footer' => '/templates/main_footer.php',
                        'navbar' => '/templates/navbar.php',
                        'sidebar' => '/templates/sidebar.php'
                    ]
            ]
            ]
        ];
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new PathConfig();
        }
        return self::$instance;
    }

    public function getPath($key, $subkey = null, $subsubkey = null)
    {
        $environment = $this->getCurrentEnvironment();
        $base = $this->config[$environment]['base_path'];
        $paths = $this->config[$environment]['paths'];

        if ($subkey && $subsubkey) {
            return $base . $paths[$key][$subkey][$subsubkey];
        } elseif ($subkey) {
            return $base . $paths[$key][$subkey];
        }

        return $base . $paths[$key];
    }

    private function getCurrentEnvironment()
    {
        // You can modify this logic based on your needs
        if ($_SERVER['SERVER_NAME'] === 'localhost') {
            return 'development';
        }
        return 'production';
    }
}

// Helper function for easy access
function path($key, $subkey = null)
{
    return PathConfig::getInstance()->getPath($key, $subkey);
}