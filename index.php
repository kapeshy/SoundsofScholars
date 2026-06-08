<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoundsOfScholars | Scholarship Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue-900: #042C53;
            --blue-800: #0C447C;
            --blue-600: #185FA5;
            --blue-400: #378ADD;
            --blue-200: #85B7EB;
            --blue-100: #B5D4F4;
            --blue-50:  #E6F1FB;
            --amber-400: #BA7517;
            --amber-200: #EF9F27;
            --teal-600: #0F6E56;
            --teal-400: #1D9E75;
            --teal-50:  #E1F5EE;
            --gray-900: #2C2C2A;
            --gray-600: #5F5E5A;
            --gray-200: #B4B2A9;
            --gray-100: #D3D1C7;
            --gray-50:  #F1EFE8;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #f8f9fb;
            color: var(--gray-900);
            overflow-x: hidden;
        }

        /* ─── HEADER ─── */
        header {
            background: var(--blue-900);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .header-inner {
            max-width: 1140px;
            margin: auto;
            padding: 0 24px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: white;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: var(--amber-200);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.3px;
            color: white;
        }

        /* ─── DESKTOP NAV ─── */
        .nav-center {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            padding: 8px 14px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background 0.2s, color 0.2s;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.12);
            color: white;
        }

        .nav-link svg {
            width: 14px;
            height: 14px;
            transition: transform 0.2s;
        }

        .nav-item:hover .nav-link svg {
            transform: rotate(180deg);
        }

        /* Dropdown */
        .dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            background: white;
            border: 1px solid var(--gray-100);
            border-radius: 10px;
            min-width: 220px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            overflow: hidden;
            z-index: 100;
        }

        .nav-item:hover .dropdown {
            display: block;
        }

        .dropdown-header {
            padding: 10px 16px 6px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--gray-200);
        }

        .dropdown a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            font-size: 14px;
            color: var(--gray-900);
            text-decoration: none;
            transition: background 0.15s;
        }

        .dropdown a:hover {
            background: var(--blue-50);
            color: var(--blue-800);
        }

        .dropdown a span.icon {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .dropdown a .link-text p {
            font-weight: 500;
            font-size: 13.5px;
        }

        .dropdown a .link-text small {
            color: var(--gray-600);
            font-size: 12px;
        }

        .dropdown-divider {
            height: 1px;
            background: var(--gray-100);
            margin: 4px 0;
        }

        /* ─── NAV RIGHT ─── */
        .nav-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-ghost {
            color: rgba(255,255,255,0.8);
            background: transparent;
            border: 1px solid rgba(255,255,255,0.25);
            padding: 8px 18px;
            border-radius: 7px;
            font-size: 14px;
            font-weight: 500;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s, border-color 0.2s, color 0.2s;
        }

        .btn-ghost:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.5);
            color: white;
        }

        .btn-primary {
            background: var(--amber-200);
            color: var(--blue-900);
            border: none;
            padding: 9px 20px;
            border-radius: 7px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s, transform 0.15s;
        }

        .btn-primary:hover {
            background: #f7b224;
            transform: translateY(-1px);
        }

        /* ─── MOBILE TOGGLE ─── */
        .hamburger {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            color: white;
        }

        .mobile-menu {
            display: none;
            background: var(--blue-900);
            border-top: 1px solid rgba(255,255,255,0.1);
            padding: 12px 0 20px;
        }

        .mobile-menu.open {
            display: block;
        }

        .mobile-section {
            padding: 8px 24px 4px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.35);
        }

        .mobile-menu a {
            display: block;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            padding: 10px 24px;
            font-size: 15px;
            font-weight: 500;
            transition: background 0.15s, color 0.15s;
        }

        .mobile-menu a:hover {
            background: rgba(255,255,255,0.08);
            color: white;
        }

        .mobile-divider {
            height: 1px;
            background: rgba(255,255,255,0.1);
            margin: 10px 24px;
        }

        .mobile-actions {
            padding: 12px 24px 0;
            display: flex;
            gap: 10px;
        }

        .mobile-actions a {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
        }

        .mobile-login {
            background: rgba(255,255,255,0.1);
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .mobile-register {
            background: var(--amber-200);
            color: var(--blue-900);
        }

        /* ─── HERO ─── */
        .hero {
            background: linear-gradient(135deg, var(--blue-900) 0%, var(--blue-800) 60%, #1a5e8c 100%);
            padding: 100px 24px 90px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .hero-content {
            position: relative;
            max-width: 680px;
            margin: auto;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: rgba(255,255,255,0.9);
            padding: 6px 14px;
            border-radius: 99px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 24px;
        }

        .hero-badge-dot {
            width: 7px;
            height: 7px;
            background: var(--amber-200);
            border-radius: 50%;
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(36px, 6vw, 58px);
            font-weight: 900;
            color: white;
            line-height: 1.15;
            margin-bottom: 20px;
        }

        .hero h1 em {
            font-style: italic;
            color: var(--amber-200);
        }

        .hero p {
            font-size: 17px;
            line-height: 1.7;
            color: rgba(255,255,255,0.75);
            margin-bottom: 36px;
        }

        .hero-buttons {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .hero-btn-primary {
            background: var(--amber-200);
            color: var(--blue-900);
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.2s, background 0.2s;
        }

        .hero-btn-primary:hover { background: #f7b224; transform: translateY(-2px); }

        .hero-btn-secondary {
            background: rgba(255,255,255,0.1);
            color: white;
            border: 1px solid rgba(255,255,255,0.25);
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.2s;
        }

        .hero-btn-secondary:hover { background: rgba(255,255,255,0.18); }

        /* ─── STATS BAR ─── */
        .stats-bar {
            background: white;
            border-bottom: 1px solid var(--gray-100);
        }

        .stats-inner {
            max-width: 1140px;
            margin: auto;
            padding: 24px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 0;
        }

        .stat-item {
            text-align: center;
            padding: 12px 16px;
            border-right: 1px solid var(--gray-100);
        }

        .stat-item:last-child { border-right: none; }

        .stat-item strong {
            display: block;
            font-size: 26px;
            font-weight: 700;
            color: var(--blue-800);
            font-family: 'Playfair Display', serif;
        }

        .stat-item span {
            font-size: 13px;
            color: var(--gray-600);
        }

        /* ─── ROLE CARDS ─── */
        .roles {
            max-width: 1140px;
            margin: 60px auto;
            padding: 0 24px;
        }

        .section-label {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--blue-600);
            margin-bottom: 8px;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 700;
            color: var(--blue-900);
            margin-bottom: 12px;
        }

        .section-sub {
            font-size: 16px;
            color: var(--gray-600);
            margin-bottom: 36px;
            max-width: 500px;
        }

        .role-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }

        .role-card {
            background: white;
            border: 1px solid var(--gray-100);
            border-radius: 12px;
            padding: 28px 24px;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            gap: 14px;
            transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
        }

        .role-card:hover {
            border-color: var(--blue-200);
            box-shadow: 0 4px 20px rgba(55,138,221,0.1);
            transform: translateY(-2px);
        }

        .role-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .role-card h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--blue-900);
        }

        .role-card p {
            font-size: 14px;
            color: var(--gray-600);
            line-height: 1.6;
            flex: 1;
        }

        .role-card-link {
            font-size: 13px;
            font-weight: 600;
            color: var(--blue-600);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ─── FOOTER ─── */
        footer {
            background: var(--blue-900);
            color: rgba(255,255,255,0.6);
            text-align: center;
            padding: 24px;
            font-size: 13px;
        }

        footer a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            margin: 0 10px;
        }

        footer a:hover { color: white; }

        /* ─── RESPONSIVE ─── */
        @media(max-width: 768px) {
            .nav-center, .nav-right { display: none; }
            .hamburger { display: flex; }
            .stat-item { border-right: none; border-bottom: 1px solid var(--gray-100); }
            .stat-item:last-child { border-bottom: none; }
        }
    </style>
</head>
<body>

<header>
    <div class="header-inner">

        <!-- Logo -->
        <a href="index.php" class="logo">
            <div class="logo-icon"></div>
            <span class="logo-text">SoundsOfScholars</span>
        </a>

        <!-- Center Nav -->
        <nav class="nav-center">

            <!-- Students dropdown -->
            <div class="nav-item">
                <a href="#" class="nav-link">
                    Students
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </a>
                <div class="dropdown">
                    <div class="dropdown-header">For Students</div>
                    <a href="student/apply.php">
                        <span class="icon" style="background:#E6F1FB"></span>
                        <div class="link-text"><p>Apply for Scholarship</p><small>Browse & submit applications</small></div>
                    </a>
                    <a href="student/dashboard.php">
                        <span class="icon" style="background:#E1F5EE"></span>
                        <div class="link-text"><p>My Dashboard</p><small>Track your applications</small></div>
                    </a>
                    <a href="student/results.php">
                        <span class="icon" style="background:#FAEEDA"></span>
                        <div class="link-text"><p>Application Results</p><small>View decisions & awards</small></div>
                    </a>
                    <a href="student/profile.php">
                        <span class="icon" style="background:#F1EFE8"></span>
                        <div class="link-text"><p>My Profile</p><small>Update personal details</small></div>
                    </a>
                </div>
            </div>

            <!-- Donors dropdown -->
            <div class="nav-item">
                <a href="#" class="nav-link">
                    Donors
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </a>
                <div class="dropdown">
                    <div class="dropdown-header">For Donors</div>
                    <a href="donor/dashboard.php">
                        <span class="icon" style="background:#E6F1FB"></span>
                        <div class="link-text"><p>Donor Dashboard</p><small>Overview of your impact</small></div>
                    </a>
                    <a href="donor/create-scholarship.php">
                        <span class="icon" style="background:#E1F5EE"></span>
                        <div class="link-text"><p>Create Scholarship</p><small>Fund a new award</small></div>
                    </a>
                    <a href="donor/applicants.php">
                        <span class="icon" style="background:#FAEEDA"></span>
                        <div class="link-text"><p>Review Applicants</p><small>Evaluate & select recipients</small></div>
                    </a>
                    <a href="donor/reports.php">
                        <span class="icon" style="background:#F1EFE8"></span>
                        <div class="link-text"><p>Reports & Impact</p><small>See scholarship outcomes</small></div>
                    </a>
                </div>
            </div>

            <!-- Admin dropdown -->
            <div class="nav-item">
                <a href="#" class="nav-link">
                    Admin
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </a>
                <div class="dropdown">
                    <div class="dropdown-header">Administration</div>
                    <a href="admin/dashboard.php">
                        <span class="icon" style="background:#E6F1FB"></span>
                        <div class="link-text"><p>Admin Dashboard</p><small>System overview</small></div>
                    </a>
                    <a href="admin/scholarships.php">
                        <span class="icon" style="background:#E1F5EE"></span>
                        <div class="link-text"><p>Manage Scholarships</p><small>Create, edit, archive</small></div>
                    </a>
                    <a href="admin/users.php">
                        <span class="icon" style="background:#FAEEDA"></span>
                        <div class="link-text"><p>User Management</p><small>Students, donors, roles</small></div>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="admin/reports.php">
                        <span class="icon" style="background:#F1EFE8"></span>
                        <div class="link-text"><p>Reports</p><small>Analytics & exports</small></div>
                    </a>
                </div>
            </div>

            <a href="about.php" class="nav-link">About</a>

        </nav>

        <!-- Right CTA -->
        <div class="nav-right">
            <a href="auth/login.php" class="btn-ghost">Log in</a>
            <a href="auth/register.php" class="btn-primary">Register</a>
        </div>

        <!-- Hamburger -->
        <button class="hamburger" id="hamburger" aria-label="Toggle menu">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
    </div>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-section">Students</div>
        <a href="student/apply.php">&nbsp;Apply for Scholarship</a>
        <a href="student/dashboard.php"> &nbsp;My Dashboard</a>
        <a href="student/results.php"> &nbsp;Application Results</a>

        <div class="mobile-divider"></div>

        <div class="mobile-section">Donors</div>
        <a href="donor/dashboard.php"> &nbsp;Donor Dashboard</a>
        <a href="donor/create-scholarship.php"> &nbsp;Create Scholarship</a>
        <a href="donor/applicants.php"> &nbsp;Review Applicants</a>

        <div class="mobile-divider"></div>

        <div class="mobile-section">Administration</div>
        <a href="admin/dashboard.php"> &nbsp;Admin Dashboard</a>
        <a href="admin/scholarships.php"> &nbsp;Manage Scholarships</a>
        <a href="admin/users.php"> &nbsp;User Management</a>

        <div class="mobile-divider"></div>
        <a href="about.php">ℹ &nbsp;About</a>

        <div class="mobile-actions">
            <a href="auth/login.php" class="mobile-login">Log in</a>
            <a href="auth/register.php" class="mobile-register">Register Free</a>
        </div>
    </div>
</header>


<!-- Hero -->
<section class="hero">
    <div class="hero-content">
        <div class="hero-badge">
            <div class="hero-badge-dot"></div>
            Scholarship Management, Simplified
        </div>
        <h1>Empowering <em>Education</em><br>for All</h1>
        <p>SoundsOfScholars connects students, donors, and administrators to make scholarships accessible, transparent, and efficient.</p>
    </div>
</section>





<!-- Role Cards -->
<section class="roles">
    <p class="section-label">Who we serve</p>
    <h2 class="section-title">One platform, every stakeholder</h2>
    <p class="section-sub">Whether you're applying, funding, or managing SoundsOfScholars has a tailored experience for you.</p>

    <div class="role-grid">
        <a href="auth/register.php?role=student" class="role-card">
            <div class="role-icon" style="background: var(--blue-50);"></div>
            <h3>I'm a Student</h3>
            <p>Discover scholarships matched to your profile, submit applications, and track your award status all in one place.</p>
            <span class="role-card-link">Apply now →</span>
        </a>

        <a href="auth/register.php?role=donor" class="role-card">
            <div class="role-icon" style="background: var(--teal-50);"></div>
            <h3>I'm a Donor</h3>
            <p>Create and fund scholarships, review applications, select recipients, and measure the real impact of your giving.</p>
            <span class="role-card-link">Start funding →</span>
        </a>

        <a href="admin/dashboard.php" class="role-card">
            <div class="role-icon" style="background: #FAEEDA;"></div>
            <h3>I'm an Admin</h3>
            <p>Oversee the entire system by managing users, scholarships, and reporting tools to keep everything running smoothly.</p>
            <span class="role-card-link">Go to admin →</span>
        </a>
    </div>
</section>


<footer>
    <p style="margin-bottom: 10px;">
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
    </p>
    <p>&copy; <?php echo date("Y"); ?> SoundsOfScholars. All rights reserved.</p>
</footer>

<script>
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu');
    hamburger.addEventListener('click', () => {
        mobileMenu.classList.toggle('open');
    });
</script>

</body>
</html>