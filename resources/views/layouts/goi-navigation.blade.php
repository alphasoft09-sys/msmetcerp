<!-- Government Navigation Bar -->
<nav class="goi-navigation">
    <div class="goi-nav-container">
        <div class="goi-nav-content">
            <!-- Main Navigation Links -->
            <ul class="goi-nav-links">
                <li class="goi-nav-item">
                    <a href="{{ route('home') }}" class="goi-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        <i class="fas fa-home me-2"></i>
                        <span class="goi-nav-text">Home</span>
                    </a>
                </li>
                <li class="goi-nav-item">
                    <a href="{{ route('public.lms.index') }}" class="goi-nav-link {{ request()->routeIs('public.lms.*') ? 'active' : '' }}">
                        <i class="fas fa-book me-2"></i>
                        <span class="goi-nav-text">LMS</span>
                    </a>
                </li>
                <li class="goi-nav-item">
                    <a href="{{ route('public.exam-schedules') }}" class="goi-nav-link {{ request()->routeIs('public.exam-schedules*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt me-2"></i>
                        <span class="goi-nav-text">Exam Schedules</span>
                    </a>
                </li>
                <li class="goi-nav-item">
                    <a href="{{ route('admin.login') }}" class="goi-nav-link {{ request()->routeIs('admin.login') ? 'active' : '' }}">
                        <i class="fas fa-shield-alt me-2"></i>
                        <span class="goi-nav-text">Admin Login</span>
                    </a>
                </li>
                <li class="goi-nav-item">
                    <a href="{{ route('student.login') }}" class="goi-nav-link {{ request()->routeIs('student.login') ? 'active' : '' }}">
                        <i class="fas fa-graduation-cap me-2"></i>
                        <span class="goi-nav-text">Student Login</span>
                    </a>
                </li>
            </ul>
            
            <!-- Mobile Menu Toggle -->
            <button class="goi-mobile-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#goiNavCollapse" aria-controls="goiNavCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="goi-mobile-toggle-icon"></span>
                <span class="goi-mobile-toggle-icon"></span>
                <span class="goi-mobile-toggle-icon"></span>
            </button>
        </div>
        
        <!-- Mobile Navigation Menu -->
        <div class="collapse goi-nav-collapse" id="goiNavCollapse">
            <ul class="goi-nav-links-mobile">
                <li class="goi-nav-item-mobile">
                    <a href="{{ route('home') }}" class="goi-nav-link-mobile {{ request()->routeIs('home') ? 'active' : '' }}">
                        <i class="fas fa-home me-2"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li class="goi-nav-item-mobile">
                    <a href="{{ route('public.lms.index') }}" class="goi-nav-link-mobile {{ request()->routeIs('public.lms.*') ? 'active' : '' }}">
                        <i class="fas fa-book me-2"></i>
                        <span>LMS</span>
                    </a>
                </li>
                <li class="goi-nav-item-mobile">
                    <a href="{{ route('public.exam-schedules') }}" class="goi-nav-link-mobile {{ request()->routeIs('public.exam-schedules*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt me-2"></i>
                        <span>Exam Schedules</span>
                    </a>
                </li>
                <li class="goi-nav-item-mobile">
                    <a href="{{ route('admin.login') }}" class="goi-nav-link-mobile {{ request()->routeIs('admin.login') ? 'active' : '' }}">
                        <i class="fas fa-shield-alt me-2"></i>
                        <span>Admin Login</span>
                    </a>
                </li>
                <li class="goi-nav-item-mobile">
                    <a href="{{ route('student.login') }}" class="goi-nav-link-mobile {{ request()->routeIs('student.login') ? 'active' : '' }}">
                        <i class="fas fa-graduation-cap me-2"></i>
                        <span>Student Login</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
/* Government Navigation Bar Styles */
.goi-navigation {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    border-bottom: 3px solid #ffd700;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.goi-nav-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.goi-nav-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    min-height: 60px;
}

.goi-nav-links {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    align-items: center;
    gap: 0;
}

.goi-nav-item {
    position: relative;
}

.goi-nav-link {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    color: #ffffff;
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
    transition: all 0.3s ease;
    border-radius: 0;
    position: relative;
    white-space: nowrap;
}

.goi-nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: #ffd700;
    text-decoration: none;
}

.goi-nav-link.active {
    background-color: rgba(255, 215, 0, 0.2);
    color: #ffd700;
    border-bottom: 3px solid #ffd700;
}

.goi-nav-link i {
    font-size: 16px;
    width: 20px;
    text-align: center;
}

.goi-nav-text {
    margin-left: 8px;
}

/* Mobile Toggle Button */
.goi-mobile-toggle {
    display: none;
    background: none;
    border: none;
    cursor: pointer;
    padding: 10px;
    flex-direction: column;
    justify-content: space-around;
    width: 30px;
    height: 30px;
}

.goi-mobile-toggle-icon {
    width: 25px;
    height: 3px;
    background-color: #ffffff;
    border-radius: 2px;
    transition: all 0.3s ease;
}

.goi-mobile-toggle:hover .goi-mobile-toggle-icon {
    background-color: #ffd700;
}

/* Mobile Navigation */
.goi-nav-collapse {
    display: none;
}

.goi-nav-links-mobile {
    display: none;
    list-style: none;
    margin: 0;
    padding: 0;
    background-color: rgba(30, 60, 114, 0.95);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.goi-nav-item-mobile {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.goi-nav-link-mobile {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    color: #ffffff;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.goi-nav-link-mobile:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: #ffd700;
    text-decoration: none;
}

.goi-nav-link-mobile.active {
    background-color: rgba(255, 215, 0, 0.2);
    color: #ffd700;
}

.goi-nav-link-mobile i {
    font-size: 16px;
    width: 20px;
    text-align: center;
}

/* Responsive Design */
@media (max-width: 768px) {
    .goi-nav-links {
        display: none;
    }
    
    .goi-mobile-toggle {
        display: flex;
    }
    
    .goi-nav-collapse {
        display: block;
    }
    
    .goi-nav-links-mobile {
        display: block;
    }
    
    .goi-nav-container {
        padding: 0 15px;
    }
    
    .goi-nav-content {
        min-height: 50px;
    }
}

@media (max-width: 576px) {
    .goi-nav-link {
        padding: 12px 15px;
        font-size: 13px;
    }
    
    .goi-nav-link-mobile {
        padding: 12px 15px;
        font-size: 13px;
    }
    
    .goi-nav-container {
        padding: 0 10px;
    }
}

/* Accessibility */
.goi-nav-link:focus,
.goi-nav-link-mobile:focus,
.goi-mobile-toggle:focus {
    outline: 2px solid #ffd700;
    outline-offset: 2px;
}

/* Animation for mobile menu */
.goi-nav-collapse.collapsing {
    transition: height 0.35s ease;
}

.goi-nav-collapse.show {
    display: block;
}
</style>
