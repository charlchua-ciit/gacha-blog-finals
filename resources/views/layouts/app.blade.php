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
    <script src="{{ asset('js/follow.js') }}"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="dark-theme">
    <!-- Modern Navbar -->
    <header class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <div class="navbar-logo">
                    <i class="fas fa-gamepad"></i>
                </div>
                <span class="brand-text">GachaBlog</span>
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
                        <h2 class="auth-title">Welcome to GachaBlog</h2>
                        <div class="auth-actions">
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Login</span>
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-outline">
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
                <div class="game-tags-container">
                    @foreach(App\Models\GameTag::all() as $tag)
                        <a href="{{ route('posts.byTag', $tag->tag_name) }}" class="game-tag-item">
                            <i class="fas fa-gamepad"></i>
                            <span>{{ $tag->tag_name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            
            <div class="sidebar-section">
                <div class="section-title">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                </div>
                <div class="notifications-list">
                    @auth
                        @forelse(Auth::user()->notifications()->latest()->take(5)->get() as $notification)
                            <div class="notification-item">
                                <i class="fas fa-info-circle"></i>
                                <span>{{ $notification->message }}</span>
                            </div>
                        @empty
                            <div class="notification-empty">
                                <i class="fas fa-bell-slash"></i>
                                <span>No notifications</span>
                            </div>
                        @endforelse
                    @else
                        <div class="notification-empty">
                            <i class="fas fa-bell-slash"></i>
                            <span>Login to see notifications</span>
                        </div>
                    @endauth
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            @if(session('success'))
                <div class="success fade-in">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="error fade-in">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="error fade-in">
                    <i class="fas fa-exclamation-triangle"></i>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Sidebar Overlay for Mobile -->
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <script>
        // Dark mode toggle
        const darkModeToggle = document.getElementById('darkModeToggle');
        const body = document.body;
        
        // Check for saved theme preference or default to dark
        const currentTheme = localStorage.getItem('theme') || 'dark';
        body.className = currentTheme === 'dark' ? 'dark-theme' : 'light-theme';
        
        darkModeToggle.addEventListener('click', () => {
            if (body.classList.contains('dark-theme')) {
                body.classList.remove('dark-theme');
                body.classList.add('light-theme');
                localStorage.setItem('theme', 'light');
                darkModeToggle.querySelector('i').className = 'fas fa-sun';
            } else {
                body.classList.remove('light-theme');
                body.classList.add('dark-theme');
                localStorage.setItem('theme', 'dark');
                darkModeToggle.querySelector('i').className = 'fas fa-moon';
            }
        });
        
        // Update icon on load
        if (currentTheme === 'dark') {
            darkModeToggle.querySelector('i').className = 'fas fa-sun';
        } else {
            darkModeToggle.querySelector('i').className = 'fas fa-moon';
        }

        // Sidebar toggle for mobile
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-open');
            sidebarOverlay.classList.toggle('active');
        });
        
        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('sidebar-open');
            sidebarOverlay.classList.remove('active');
        });

        // Close sidebar on window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 1024) {
                sidebar.classList.remove('sidebar-open');
                sidebarOverlay.classList.remove('active');
            }
        });

        // Add fade-in animation to content
        document.addEventListener('DOMContentLoaded', () => {
            const content = document.querySelector('.main-content');
            if (content) {
                content.classList.add('fade-in');
            }
        });
    </script>
</body>
</html>
