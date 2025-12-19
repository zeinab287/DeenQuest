<?php
// start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$scriptName = $_SERVER['SCRIPT_NAME'];

// ROBUST WEB ROOT CALCULATION
// Get the absolute path of the project root (parent of 'includes')
$projectRoot = str_replace('\\', '/', dirname(__DIR__));
// Get the document root of the server
$docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);

// Remove the document root from the project root to get the URL path
$webRoot = str_replace($docRoot, '', $projectRoot);

// Ensure it starts and ends with /
if (substr($webRoot, 0, 1) !== '/') {
    $webRoot = '/' . $webRoot;
}
if (substr($webRoot, -1) !== '/') {
    $webRoot .= '/';
}

// Fix for InfinityFree: if webRoot contains 'htdocs', we're in the web root
if (strpos($webRoot, 'htdocs') !== false || $webRoot === '//') {
    $webRoot = '/';
}

$currentUrl = $_SERVER['REQUEST_URI'];

// get the name of the current file (i.e: "index.php", "learner_dashboard.php")
$currentPage = basename($_SERVER['PHP_SELF']);

// define pages that should always show the simple header, even if logged in.
$publicPages = ['index.php', 'login.php', 'register.php'];

// check if the current page is in that list
$isPublicPage = in_array($currentPage, $publicPages);

// initialize empty array
$navItems = [];

// check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// navigation: role-based menu items
if (isset($_SESSION['role']) && $_SESSION['role'] === 'learner') {

    $navItems = [
        'Dashboard' => [
            'type'  => 'link',
            'url'   => $webRoot . 'learner_dashboard.php',
            'icon'  => 'fa-tachometer-alt',
            'match' => ['learner_dashboard.php']
        ],
        'Quests' => [
            'type'  => 'dropdown',
            'icon'  => 'fa-map-signs',
            'id'    => 'questDrop',
            'match' => ['pages/quests/', 'quest_view.php', 'quiz_take.php'],
            'items' => [
                'All Quests' => [
                    'url'  => $webRoot . 'pages/quests/index.php',
                    'icon' => 'fa-list'
                ],
                'My Progress Tracking' => [
                    'url'  => $webRoot . 'pages/quests/progress.php',
                    'icon' => 'fa-tasks'
                ],
            ]
        ],
        'Quizzes' => [
            'type'  => 'dropdown',
            'icon'  => 'fa-question-circle',
            'id'    => 'quizDrop',
            'match' => ['pages/quizzes/'],
            'items' => [
                'All Available Quizzes' => [
                    'url'  => $webRoot . 'pages/quizzes/index.php',
                    'icon' => 'fa-vial'
                ],
                'My Results History' => [
                    'url'  => $webRoot . 'pages/quizzes/history.php',
                    'icon' => 'fa-history'
                ],
            ]
        ],
        'Mini Games' => [
            'type'  => 'link',
            'url'   => $webRoot . 'pages/games/index.php',
            'icon'  => 'fa-gamepad',
            'match' => ['pages/games/']
        ],
        
        'Gamification' => [
            'type'  => 'link',
            'url'   => $webRoot . 'pages/gamification/index.php',
            'icon'  => 'fa-trophy',
            'match' => ['pages/gamification/']
        ],
        'Challenges' => [
            'type'  => 'dropdown',
            'icon'  => 'fa-users',
            'id'    => 'challengeDrop',
            'match' => ['pages/challenges/'],
            'items' => [
                'Challenge Home' => [
                    'url'   => $webRoot . 'pages/challenges/index.php',
                    'icon' => 'fa-home'
                ],
                'Weekly / Monthly' => [
                    'url'   => $webRoot . 'pages/challenges/index.php',
                    'icon' => 'fa-calendar-alt'
                ],
                'My Challenge Progress' => [
                    'url'   => $webRoot . 'pages/challenges/history.php',
                    'icon' => 'fa-chart-line'
                ],
            ]
        ],
        'Reflection Space' => [
            'type'  => 'dropdown',
            'icon'  => 'fa-book-open',
            'id'    => 'reflectDrop',
            'match' => ['pages/reflection/'],
            'items' => [
                'My Reflections & Goals' => [
                    'url'  => $webRoot . 'pages/reflection/index.php',
                    'icon' => 'fa-feather-alt'
                ],
                'Add New Reflection' => [
                    'url'  => $webRoot . 'pages/reflection/add.php',
                    'icon' => 'fa-plus'
                ],
            ]
        ],
    ];
}

// helper function to determine if any pattern matches the current URL
function isActive(array $patterns, string $currentUrl): bool
{
    foreach ($patterns as $pattern) {
        if (strpos($currentUrl, $pattern) !== false) {
            return true;
        }
    }
    return false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DeenQuest</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= $webRoot ?>assets/css/style.css" rel="stylesheet">
    
    <?php if ($isLoggedIn && !empty($navItems) && !$isPublicPage): ?>
    <style>
        /* sidebar styles, when user is logged in */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 2px 0 15px rgba(0,0,0,0.1);
            z-index: 1040;
            overflow-y: auto;
            transition: transform 0.3s ease-in-out;
        }
        
        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-brand {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .sidebar-brand i {
            margin-right: 0.5rem;
            font-size: 1.8rem;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .sidebar-nav-item {
            margin: 0.25rem 0;
        }
        
        .sidebar-nav-link {
            display: flex;
            align-items: center;
            padding: 0.875rem 1.5rem;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            transition: all 0.2s;
            font-weight: 500;
        }
        
        .sidebar-nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            padding-left: 2rem;
        }
        
        .sidebar-nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-right: 4px solid white;
        }
        
        .sidebar-nav-link i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }
        
        .sidebar-dropdown-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.875rem 1.5rem;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            transition: all 0.2s;
            font-weight: 500;
            cursor: pointer;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
        }
        
        .sidebar-dropdown-toggle:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .sidebar-dropdown-toggle.active {
            background: rgba(255,255,255,0.15);
            color: white;
        }
        
        .sidebar-dropdown-content {
            background: rgba(0,0,0,0.1);
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .sidebar-dropdown-content.show {
            max-height: 500px;
        }
        
        .sidebar-dropdown-item {
            padding: 0.75rem 1.5rem 0.75rem 3.5rem;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.2s;
            font-size: 0.9rem;
        }
        
        .sidebar-dropdown-item:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            padding-left: 4rem;
        }
        
        .sidebar-dropdown-item i {
            margin-right: 0.5rem;
            font-size: 0.85rem;
        }
        
        .sidebar-user {
            padding: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: auto;
        }
        
        .sidebar-user-info {
            display: flex;
            align-items: center;
            color: white;
            margin-bottom: 1rem;
        }
        
        .sidebar-user-avatar {
            width: 45px;
            height: 45px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            font-size: 1.2rem;
        }
        
        .sidebar-user-name {
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .sidebar-logout {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 0.5rem;
            color: white;
            text-decoration: none;
            transition: all 0.2s;
            font-weight: 600;
        }
        
        .sidebar-logout:hover {
            background: rgba(239, 68, 68, 0.4);
            color: white;
        }
        
        .sidebar-logout i {
            margin-right: 0.5rem;
        }
        
        /* main content adjustment */
        .main-content {
            margin-left: 280px;
            padding: 2rem;
            min-height: 100vh;
        }
        
        /* mobile toggle */
        .sidebar-toggle {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1050;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: none;
        }
        
        /* mobile styles */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 5rem 1rem 2rem;
            }
            
            .sidebar-toggle {
                display: block;
            }
        }
        
        /* overlay for mobile */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1030;
            display: none;
        }
        
        .sidebar-overlay.show {
            display: block;
        }
    </style>
    <?php endif; ?>
</head>

<body class="bg-light">

<?php if ($isLoggedIn && !empty($navItems) && !$isPublicPage): ?>
<button class="sidebar-toggle" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="<?= $webRoot ?>learner_dashboard.php" class="sidebar-brand">
            <i class="fas fa-star-and-crescent"></i>
            DeenQuest
        </a>
    </div>
    
    <nav class="sidebar-nav">
        <?php foreach ($navItems as $label => $data): ?>
            <?php $active = isActive($data['match'], $currentUrl) ? 'active' : ''; ?>
            
            <?php if ($data['type'] === 'link'): ?>
                <div class="sidebar-nav-item">
                    <a href="<?= $data['url'] ?>" class="sidebar-nav-link <?= $active ?>">
                        <i class="fas <?= $data['icon'] ?>"></i>
                        <span><?= $label ?></span>
                    </a>
                </div>
            <?php else: ?>
                <div class="sidebar-nav-item">
                    <button class="sidebar-dropdown-toggle <?= $active ?>" onclick="toggleDropdown('<?= $data['id'] ?>')">
                        <span>
                            <i class="fas <?= $data['icon'] ?>"></i>
                            <?= $label ?>
                        </span>
                        <i class="fas fa-chevron-down" style="font-size: 0.8rem;"></i>
                    </button>
                    <div class="sidebar-dropdown-content" id="<?= $data['id'] ?>">
                        <?php foreach ($data['items'] as $itemLabel => $itemData): ?>
                            <a href="<?= $itemData['url'] ?>" class="sidebar-dropdown-item">
                                <i class="fas <?= $itemData['icon'] ?>"></i>
                                <?= $itemLabel ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
    
    <div class="sidebar-user">
        <div class="sidebar-user-info">
            <div class="sidebar-user-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <div class="sidebar-user-name">
                    <?= htmlspecialchars($_SESSION['name'] ?? 'User') ?>
                </div>
                <small style="color: rgba(255,255,255,0.7); font-size: 0.8rem;">
                    <?= ucfirst($_SESSION['role'] ?? 'learner') ?>
                </small>
            </div>
        </div>
        <a href="<?= $webRoot ?>actions/logout.php" class="sidebar-logout">
            <i class="fas fa-sign-out-alt"></i>
            Log Out
        </a>
    </div>
</div>

<div class="main-content">

<script>
    // dropdown toggle function
    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        const allDropdowns = document.querySelectorAll('.sidebar-dropdown-content');
        
        // close all other dropdowns
        allDropdowns.forEach(d => {
            if (d.id !== id) {
                d.classList.remove('show');
            }
        });
        
        // toggle current dropdown
        dropdown.classList.toggle('show');
    }
    
    // mobile sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    sidebarToggle?.addEventListener('click', () => {
        sidebar.classList.toggle('show');
        sidebarOverlay.classList.toggle('show');
    });
    
    sidebarOverlay?.addEventListener('click', () => {
        sidebar.classList.remove('show');
        sidebarOverlay.classList.remove('show');
    });
</script>

<?php else: ?>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand text-success fw-bold" href="<?= $webRoot ?>index.php">
            <i class="fas fa-star-and-crescent me-2"></i>DeenQuest
        </a>


        <div class="ms-auto">
            <?php if ($isLoggedIn): ?>
                <?php
                    // determine the correct dashboard link based on role
                    $dashboardLink = ($_SESSION['role'] === 'admin') ? $webRoot . 'admin_dashboard.php' : $webRoot . 'learner_dashboard.php';
                 ?>
                <div class="d-flex align-items-center">
                    <span class="badge bg-secondary me-3 d-none d-md-inline-block opacity-75">
                        <small><?= ucfirst($_SESSION['role'] ?? 'User') ?></small>
                    </span>
            
                    <a href="<?= $dashboardLink ?>" class="btn btn-success fw-bold me-2 shadow-sm">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>

                    <a href="<?= $webRoot ?>actions/logout.php" class="btn btn-outline-danger fw-bold shadow-sm">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            <?php else: ?>
                <a href="<?= $webRoot ?>login.php" class="btn btn-outline-success me-2">Login</a>
                <a href="<?= $webRoot ?>register.php" class="btn btn-success shadow-sm">Sign Up</a>
            <?php endif; ?>
        </div>


    </div>
</nav>

<div class="container">
<?php endif; ?>