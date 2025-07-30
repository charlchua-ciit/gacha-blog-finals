<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GachaBlog - Share Your Gaming Adventures</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="light-theme">
    <!-- Modern Navbar -->
    <header class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <div class="navbar-logo">
                    <i class="fas fa-gamepad"></i>
                    <span>GachaBlog</span>
                </div>
            </div>
            
            <nav class="navbar-links">
                <a href="{{ route('home') }}" class="nav-link">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
                <a href="{{ route('posts.index') }}" class="nav-link">
                    <i class="fas fa-stream"></i>
                    <span>Posts</span>
                </a>
                <a href="{{ route('users') }}" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('posts.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        <span>Create Post</span>
                    </a>
                @endauth
            </nav>
            
            <div class="navbar-actions">
                <button id="darkModeToggle" class="btn-icon" title="Toggle dark mode">
                    <i class="fas fa-moon"></i>
                </button>
                <button id="sidebarToggle" class="btn-icon">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Modern Sidebar -->
        <aside id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <h3>Navigation</h3>
            </div>
            
            <div class="sidebar-section">
                <div class="section-title">
                    <i class="fas fa-user-circle"></i>
                    <span>Profile</span>
                </div>
                @auth
                    <div class="profile-card">
                        <div class="profile-avatar">
                            <div class="avatar">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                        <div class="profile-info">
                            <div class="profile-name">{{ Auth::user()->username }}</div>
                            <div class="profile-status">Online</div>
                        </div>
                    </div>
                    <div class="profile-actions">
                        <a href="{{ route('profile.edit') }}" class="sidebar-link">
                            <i class="fas fa-user-edit"></i>
                            <span>Edit Profile</span>
                        </a>
                        <a href="{{ route('dashboard') }}" class="sidebar-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="logout-form">
                            @csrf
                            <button type="submit" class="sidebar-link logout-btn">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="auth-card">
                        <div class="auth-title">Welcome to GachaBlog</div>
                        <div class="auth-actions">
                            <a href="{{ route('login') }}" class="btn btn-outline">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Login</span>
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i>
                                <span>Register</span>
                            </a>
                        </div>
                    </div>
                @endauth
            </div>

            <div class="sidebar-section">
                <div class="section-title">
                    <i class="fas fa-tags"></i>
                    <span>Game Tags</span>
                </div>
                <div class="tags-grid">
                    @isset($tags)
                        @foreach($tags as $tag)
                            <a href="{{ route('posts.byTag', $tag) }}" class="tag-chip">
                                <i class="fas fa-gamepad"></i>
                                <span>{{ $tag->tag_name }}</span>
                            </a>
                        @endforeach
                    @else
                        <a href="{{ route('posts.index') }}" class="tag-chip">
                            <i class="fas fa-list"></i>
                            <span>All Posts</span>
                        </a>
                    @endisset
                </div>
            </div>

            <div class="sidebar-section">
                <div class="section-title">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                </div>
                <div class="notifications-list">
                    @isset($notifications)
                        @forelse($notifications as $notif)
                            <div class="notification-item">
                                <i class="fas fa-info-circle"></i>
                                <span>{{ $notif->message }}</span>
                            </div>
                        @empty
                            <div class="notification-empty">
                                <i class="fas fa-check-circle"></i>
                                <span>All caught up!</span>
                            </div>
                        @endforelse
                    @else
                        <div class="notification-empty">
                            <i class="fas fa-bell-slash"></i>
                            <span>No notifications</span>
                        </div>
                    @endisset
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <style>
        /* Enhanced Game Tag Styling */
        .game-tag-link {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
            color: var(--primary);
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .game-tag-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .game-tag-link:hover {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.2));
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
            border-color: var(--primary);
        }
        
        .game-tag-link:hover::before {
            left: 100%;
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(99, 102, 241, 0.3);
            border-radius: 50%;
            border-top-color: var(--primary);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Focus Styles */
        .btn:focus,
        .nav-link:focus,
        .sidebar-link:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }
    </style>

    <script>
        // Enhanced Sidebar Toggle
        document.getElementById('sidebarToggle').onclick = function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('sidebar-open');
            
            // Add overlay for mobile
            if (sidebar.classList.contains('sidebar-open')) {
                const overlay = document.createElement('div');
                overlay.className = 'sidebar-overlay';
                overlay.onclick = () => {
                    sidebar.classList.remove('sidebar-open');
                    overlay.remove();
                };
                document.body.appendChild(overlay);
            } else {
                const overlay = document.querySelector('.sidebar-overlay');
                if (overlay) overlay.remove();
            }
        };

        // Enhanced Dark Mode Toggle
        const darkModeToggle = document.getElementById('darkModeToggle');
        const body = document.body;
        
        function setTheme(theme) {
            if (theme === 'dark') {
                body.classList.add('dark-theme');
                body.classList.remove('light-theme');
                darkModeToggle.innerHTML = '<i class="fas fa-sun"></i>';
            } else {
                body.classList.add('light-theme');
                body.classList.remove('dark-theme');
                darkModeToggle.innerHTML = '<i class="fas fa-moon"></i>';
            }
            localStorage.setItem('theme', theme);
        }
        
        darkModeToggle.onclick = function() {
            const isDark = body.classList.contains('dark-theme');
            setTheme(isDark ? 'light' : 'dark');
            
            // Add animation
            this.style.transform = 'rotate(360deg)';
            setTimeout(() => {
                this.style.transform = 'rotate(0deg)';
            }, 300);
        };
        
        // Set theme on load
        setTheme(localStorage.getItem('theme') || 'light');

        // Smooth page transitions
        document.addEventListener('DOMContentLoaded', function() {
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.3s ease';
            
            setTimeout(() => {
                document.body.style.opacity = '1';
            }, 100);
        });
    </script>
</body>
</html>
