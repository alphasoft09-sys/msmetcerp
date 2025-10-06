<!-- Simple Navigation Bar -->
<nav class="goi-navigation">
    <div class="goi-nav-container">
        <ul class="goi-nav-links">
            <li><a href="{{ route('home') }}" class="goi-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
            <li><a href="{{ route('public.lms.index') }}" class="goi-nav-link {{ request()->routeIs('public.lms.*') ? 'active' : '' }}">LMS</a></li>
            <li><a href="{{ route('public.exam-schedules') }}" class="goi-nav-link {{ request()->routeIs('public.exam-schedules*') ? 'active' : '' }}">Exam Schedules</a></li>
            <li><a href="{{ route('admin.login') }}" class="goi-nav-link {{ request()->routeIs('admin.login') ? 'active' : '' }}">Admin Login</a></li>
            <li><a href="{{ route('student.login') }}" class="goi-nav-link {{ request()->routeIs('student.login') ? 'active' : '' }}">Student Login</a></li>
        </ul>
    </div>
</nav>

<style>
/* Simple Navigation Bar */
.goi-navigation {
    background: #1e3c72;
    border-bottom: 2px solid #ffd700;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.goi-nav-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.goi-nav-links {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    align-items: center;
    justify-content: center;
    height: 30px;
}

.goi-nav-links li {
    margin: 0;
}

.goi-nav-link {
    display: block;
    padding: 4px 12px;
    color: #ffffff;
    text-decoration: none;
    font-size: 12px;
    transition: all 0.2s ease;
}

.goi-nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: #ffd700;
    text-decoration: none;
}

.goi-nav-link.active {
    background-color: #ffd700;
    color: #1e3c72;
    font-weight: 600;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .goi-nav-links {
        flex-wrap: wrap;
        height: auto;
        padding: 8px 0;
    }
    
    .goi-nav-link {
        padding: 6px 12px;
        font-size: 13px;
    }
    
    .goi-nav-container {
        padding: 0 15px;
    }
}
</style>

