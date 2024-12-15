<nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" id="sidebarMenu">
    <div class="position-sticky sidebar-sticky pt-3">
        <ul class="nav flex-column">
            
            <li class="container">
                <span class="text-secondary">MENU</span>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex {{ Request::is('dashboard') ? 'active' : '' }} gap-2" href="{{ route('dashboard.index') }}" aria-current="page">
                    <i class="ti ti-home fs-5"></i>Home
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex {{ Request::is('dashboard/categories*') ? 'active' : '' }} gap-2" href="{{ route('categories.index') }}">
                    <i class="ti ti-category fs-5"></i>Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex {{ Request::is('dashboard/courses*') ? 'active' : '' }} gap-2" href="{{ route('courses.index') }}">
                    <i class="ti ti-book fs-5"></i>Courses
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex {{ Request::is('dashboard/quizzes*') ? 'active' : '' }} gap-2" href="{{ route('quizzes.index') }}">
                    <i class="ti ti-checklist fs-5"></i>Quizzez
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex {{ Request::is('dashboard/users*') ? 'active' : '' }} gap-2" href="{{ route('users.index') }}">
                    <i class="ti ti-user fs-5"></i>Users
                </a>
            </li>

            <li class="container mt-4">
                <span class="text-secondary">OTHER</span>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex {{ Request::is('dashboard/settings*') ? 'active' : '' }} gap-2" href="{{ route('dashboard.settings') }}">
                    <i class="ti ti-settings fs-5"></i>Settings
                </a>
            </li>
        </ul>
    </div>
</nav>
