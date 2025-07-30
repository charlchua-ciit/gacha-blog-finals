<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gacha Blog</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="light-theme">
    <header class="navbar">
        <div class="navbar-logo">GachaBlog</div>
        <nav class="navbar-links">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('posts') }}">Posts</a>
            <a href="{{ route('users') }}">Users</a>
            <button id="darkModeToggle" class="sidebar-toggle" title="Toggle dark mode">🌙</button>
            <button id="sidebarToggle" class="sidebar-toggle">☰</button>
        </nav>
    </header>
    <div class="container">
        <aside id="sidebar" class="sidebar">
            <div class="sidebar-section">
                <strong>Profile</strong>
                @auth
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; padding: 0.75rem; background: rgba(99, 102, 241, 0.05); border-radius: 0.75rem;">
                        <div class="avatar" style="width: 40px; height: 40px;"></div>
                        <div>
                            <div style="font-weight: 600; color: var(--text-primary);">{{ Auth::user()->username }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Member</div>
                        </div>
                    </div>
                    <ul>
                        <li><a href="#">My Profile</a></li>
                        <li><a href="#">Settings</a></li>
                        <li><a href="#">Logout</a></li>
                    </ul>
                @else
                    <div style="padding: 0.75rem; background: rgba(99, 102, 241, 0.05); border-radius: 0.75rem; margin-bottom: 1rem;">
                        <a href="#" style="color: var(--primary); font-weight: 500;">Login</a>
                    </div>
                @endauth
            </div>
            <div class="sidebar-section">
                <strong>Game Tags</strong>
                <ul>
                    @isset($tags)
                        @foreach($tags as $tag)
                            <li><a href="#">{{ $tag->tag_name }}</a></li>
                        @endforeach
                    @else
                        <li><a href="#">All Tags</a></li>
                    @endisset
                </ul>
            </div>
            <div class="sidebar-section">
                <strong>Notifications</strong>
                <ul>
                    @isset($notifications)
                        @forelse($notifications as $notif)
                            <li><a href="#" style="font-size: 0.8rem;">{{ $notif->message }}</a></li>
                        @empty
                            <li style="color: var(--text-muted); font-style: italic;">No notifications</li>
                        @endforelse
                    @else
                        <li><a href="#">View All</a></li>
                    @endisset
                </ul>
            </div>
        </aside>
        <main class="main-content">
            @yield('content')
        </main>
    </div>
    <script>
        document.getElementById('sidebarToggle').onclick = function() {
            document.getElementById('sidebar').classList.toggle('sidebar-open');
        };
        // Dark mode toggle
        const darkModeToggle = document.getElementById('darkModeToggle');
        const body = document.body;
        function setTheme(theme) {
            if (theme === 'dark') {
                body.classList.add('dark-theme');
                body.classList.remove('light-theme');
                darkModeToggle.textContent = '☀️';
            } else {
                body.classList.add('light-theme');
                body.classList.remove('dark-theme');
                darkModeToggle.textContent = '🌙';
            }
            localStorage.setItem('theme', theme);
        }
        darkModeToggle.onclick = function() {
            const isDark = body.classList.contains('dark-theme');
            setTheme(isDark ? 'light' : 'dark');
        };
        // On load, set theme from localStorage
        setTheme(localStorage.getItem('theme') || 'light');
    </script>
</body>
</html>