<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact | SoundsOfScholars</title>
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

        .logo { display: flex; align-items: center; gap: 10px; text-decoration: none; color: white; }

        .logo-icon {
            width: 36px; height: 36px;
            background: var(--amber-200);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }

        .logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.3px;
            color: white;
        }

        .nav-center { display: flex; align-items: center; gap: 4px; }
        .nav-item { position: relative; }

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

        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.12); color: white; }
        .nav-link svg { width: 14px; height: 14px; transition: transform 0.2s; }
        .nav-item:hover .nav-link svg { transform: rotate(180deg); }

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

        .nav-item:hover .dropdown { display: block; }

        .dropdown-header {
            padding: 10px 16px 6px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--gray-200);
        }

        .dropdown a {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 16px; font-size: 14px;
            color: var(--gray-900); text-decoration: none;
            transition: background 0.15s;
        }

        .dropdown a:hover { background: var(--blue-50); color: var(--blue-800); }

        .dropdown a span.icon {
            width: 30px; height: 30px; border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; flex-shrink: 0;
        }

        .dropdown a .link-text p { font-weight: 500; font-size: 13.5px; }
        .dropdown a .link-text small { color: var(--gray-600); font-size: 12px; }
        .dropdown-divider { height: 1px; background: var(--gray-100); margin: 4px 0; }

        .nav-right { display: flex; align-items: center; gap: 10px; }

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

        .btn-ghost:hover { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.5); color: white; }

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

        .btn-primary:hover { background: #f7b224; transform: translateY(-1px); }

        .hamburger { display: none; background: none; border: none; cursor: pointer; padding: 6px; color: white; }

        .mobile-menu {
            display: none;
            background: var(--blue-900);
            border-top: 1px solid rgba(255,255,255,0.1);
            padding: 12px 0 20px;
        }

        .mobile-menu.open { display: block; }
        .mobile-section { padding: 8px 24px 4px; font-size: 11px; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: rgba(255,255,255,0.35); }
        .mobile-menu a { display: block; color: rgba(255,255,255,0.8); text-decoration: none; padding: 10px 24px; font-size: 15px; font-weight: 500; transition: background 0.15s, color 0.15s; }
        .mobile-menu a:hover { background: rgba(255,255,255,0.08); color: white; }
        .mobile-divider { height: 1px; background: rgba(255,255,255,0.1); margin: 10px 24px; }
        .mobile-actions { padding: 12px 24px 0; display: flex; gap: 10px; }
        .mobile-actions a { flex: 1; text-align: center; padding: 10px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; }
        .mobile-login { background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2); }
        .mobile-register { background: var(--amber-200); color: var(--blue-900); }

        /* ─── PAGE HERO ─── */
        .page-hero {
            background: linear-gradient(135deg, var(--blue-900) 0%, var(--blue-800) 60%, #1a5e8c 100%);
            padding: 38px 24px 32px;
            text-align: center;
        }

        .page-hero .breadcrumb { font-size: 13px; color: rgba(255,255,255,0.5); margin-bottom: 12px; }
        .page-hero .breadcrumb a { color: rgba(255,255,255,0.6); text-decoration: none; }
        .page-hero .breadcrumb a:hover { color: white; }

        .page-hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(26px, 4vw, 38px);
            font-weight: 900;
            color: white;
            margin-bottom: 8px;
        }

        .page-hero p {
            font-size: 16px;
            color: rgba(255,255,255,0.7);
            max-width: 520px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* ─── CONTACT LAYOUT ─── */
        .contact-layout {
            max-width: 1100px;
            margin: 0 auto;
            padding: 56px 24px 80px;
            display: grid;
            grid-template-columns: 1fr 1.3fr;
            gap: 36px;
            align-items: start;
        }

        /* ─── CONTACT INFO CARDS ─── */
        .info-stack { display: flex; flex-direction: column; gap: 16px; }

        .info-card {
            background: white;
            border: 1px solid var(--gray-100);
            border-radius: 12px;
            padding: 22px 22px;
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }

        .info-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            flex-shrink: 0;
        }

        .info-card h3 {
            font-size: 15px;
            font-weight: 600;
            color: var(--blue-900);
            margin-bottom: 4px;
        }

        .info-card p {
            font-size: 13.5px;
            color: var(--gray-600);
            line-height: 1.6;
        }

        .info-card a {
            color: var(--blue-600);
            text-decoration: none;
            font-weight: 500;
        }

        .info-card a:hover { text-decoration: underline; }

        .office-hours {
            background: var(--blue-50);
            border: 1px solid var(--blue-100);
            border-radius: 12px;
            padding: 20px 22px;
        }

        .office-hours h3 {
            font-size: 14px;
            font-weight: 600;
            color: var(--blue-900);
            margin-bottom: 10px;
        }

        .office-hours .row {
            display: flex;
            justify-content: space-between;
            font-size: 13.5px;
            color: var(--gray-600);
            padding: 4px 0;
        }

        .office-hours .row span:last-child { color: var(--blue-800); font-weight: 500; }

        /* ─── CONTACT FORM ─── */
        .form-card {
            background: white;
            border: 1px solid var(--gray-100);
            border-radius: 14px;
            padding: 36px 36px;
        }

        .form-card h2 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 700;
            color: var(--blue-900);
            margin-bottom: 6px;
        }

        .form-card .subtitle {
            font-size: 14px;
            color: var(--gray-600);
            margin-bottom: 28px;
        }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }

        .form-group { margin-bottom: 16px; }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 6px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid var(--gray-100);
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            color: var(--gray-900);
            background: var(--gray-50);
            transition: border-color 0.2s, background 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--blue-400);
            background: white;
        }

        .form-group textarea { resize: vertical; min-height: 120px; }

        .submit-btn {
            width: 100%;
            background: var(--blue-800);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
        }

        .submit-btn:hover { background: var(--blue-600); transform: translateY(-1px); }

        .form-note {
            font-size: 12.5px;
            color: var(--gray-200);
            text-align: center;
            margin-top: 14px;
        }

        .success-banner {
            display: none;
            background: var(--teal-50);
            border: 1px solid var(--teal-400);
            color: var(--teal-600);
            padding: 14px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .success-banner.show { display: block; }

        /* ─── FAQ STRIP ─── */
        .faq-strip {
            background: white;
            border-top: 1px solid var(--gray-100);
            padding: 60px 24px;
        }

        .faq-inner { max-width: 800px; margin: auto; }

        .faq-inner .section-label {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--blue-600);
            text-align: center;
            margin-bottom: 10px;
        }

        .faq-inner h2 {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--blue-900);
            text-align: center;
            margin-bottom: 36px;
        }

        .faq-item {
            border-bottom: 1px solid var(--gray-100);
            padding: 18px 0;
        }

        .faq-item:last-child { border-bottom: none; }

        .faq-item h3 {
            font-size: 15px;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 6px;
        }

        .faq-item p {
            font-size: 14px;
            color: var(--gray-600);
            line-height: 1.65;
        }

        /* ─── FOOTER ─── */
        footer {
            background: var(--blue-900);
            color: rgba(255,255,255,0.6);
            text-align: center;
            padding: 24px;
            font-size: 13px;
        }

        footer a { color: rgba(255,255,255,0.7); text-decoration: none; margin: 0 10px; }
        footer a:hover { color: white; }

        @media(max-width: 900px) {
            .contact-layout { grid-template-columns: 1fr; }
        }

        @media(max-width: 768px) {
            .nav-center, .nav-right { display: none; }
            .hamburger { display: flex; }
            .form-row { grid-template-columns: 1fr; }
            .form-card { padding: 28px 22px; }
        }
    </style>
</head>
<body>

<header>
    <div class="header-inner">
        <a href="index.php" class="logo">
            <div class="logo-icon">🎓</div>
            <span class="logo-text">SoundsOfScholars</span>
        </a>

        <nav class="nav-center">
            <div class="nav-item">
                <a href="#" class="nav-link">Students
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </a>
                <div class="dropdown">
                    <div class="dropdown-header">For Students</div>
                    <a href="student/apply.php"><span class="icon" style="background:#E6F1FB"></span><div class="link-text"><p>Apply for Scholarship</p><small>Browse & submit applications</small></div></a>
                    <a href="student/dashboard.php"><span class="icon" style="background:#E1F5EE"></span><div class="link-text"><p>My Dashboard</p><small>Track your applications</small></div></a>
                    <a href="student/results.php"><span class="icon" style="background:#FAEEDA"></span><div class="link-text"><p>Application Results</p><small>View decisions & awards</small></div></a>
                    <a href="student/profile.php"><span class="icon" style="background:#F1EFE8"></span><div class="link-text"><p>My Profile</p><small>Update personal details</small></div></a>
                </div>
            </div>
            <div class="nav-item">
                <a href="#" class="nav-link">Donors
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </a>
                <div class="dropdown">
                    <div class="dropdown-header">For Donors</div>
                    <a href="donor/dashboard.php"><span class="icon" style="background:#E6F1FB"></span><div class="link-text"><p>Donor Dashboard</p><small>Overview of your impact</small></div></a>
                    <a href="donor/create-scholarship.php"><span class="icon" style="background:#E1F5EE"></span><div class="link-text"><p>Create Scholarship</p><small>Fund a new award</small></div></a>
                    <a href="donor/applicants.php"><span class="icon" style="background:#FAEEDA"></span><div class="link-text"><p>Review Applicants</p><small>Evaluate & select recipients</small></div></a>
                    <a href="donor/reports.php"><span class="icon" style="background:#F1EFE8"></span><div class="link-text"><p>Reports & Impact</p><small>See scholarship outcomes</small></div></a>
                </div>
            </div>
            <div class="nav-item">
                <a href="#" class="nav-link">Admin
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </a>
                <div class="dropdown">
                    <div class="dropdown-header">Administration</div>
                    <a href="admin/dashboard.php"><span class="icon" style="background:#E6F1FB"></span><div class="link-text"><p>Admin Dashboard</p><small>System overview</small></div></a>
                    <a href="admin/scholarships.php"><span class="icon" style="background:#E1F5EE"></span><div class="link-text"><p>Manage Scholarships</p><small>Create, edit, archive</small></div></a>
                    <a href="admin/users.php"><span class="icon" style="background:#FAEEDA"></span><div class="link-text"><p>User Management</p><small>Students, donors, roles</small></div></a>
                    <div class="dropdown-divider"></div>
                    <a href="admin/reports.php"><span class="icon" style="background:#F1EFE8"></span><div class="link-text"><p>Reports</p><small>Analytics & exports</small></div></a>
                </div>
            </div>
            <a href="about.php" class="nav-link">About</a>
        </nav>

        <div class="nav-right">
            <a href="auth/login.php" class="btn-ghost">Log in</a>
            <a href="auth/register.php" class="btn-primary">Register</a>
        </div>

        <button class="hamburger" id="hamburger" aria-label="Toggle menu">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
    </div>

    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-section">Students</div>
        <a href="student/apply.php"> &nbsp;Apply for Scholarship</a>
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


<!-- Page Hero -->
<div class="page-hero">
    <div class="breadcrumb"><a href="index.php">Home</a> &nbsp;/&nbsp; Contact</div>
    <h1>Get in Touch</h1>
    <p>Questions about scholarships, donations, or your account? We're here to help.</p>
</div>


<!-- Contact layout -->
<div class="contact-layout">

    <!-- Info column -->
    <div class="info-stack">
        <div class="info-card">
            <div class="info-icon" style="background: var(--blue-50);"></div>
            <div>
                <h3>Email us</h3>
                <p><a href="mailto:soundsofscholars@gmail.com">soundsofscholars@gmail.com</a><br>For general inquiries and support</p>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon" style="background: var(--teal-50);"></div>
            <div>
                <h3>Call us</h3>
                <p><a href="tel:+254743694406">+254 743 694 406</a><br>Mon – Fri, 8:00 AM – 5:00 PM EAT</p>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon" style="background: #FAEEDA;"></div>
            <div>
                <h3>Visit us</h3>
                <p>Malibu Area<br>Ole Sangale Road, Madaraka<br>Nairobi, Kenya</p>
            </div>
        </div>

        <div class="office-hours">
            <h3>Support hours</h3>
            <div class="row"><span>Monday – Friday</span><span>8:00 AM – 5:00 PM</span></div>
            <div class="row"><span>Saturday</span><span>9:00 AM – 1:00 PM</span></div>
            <div class="row"><span>Sunday</span><span>Closed</span></div>
        </div>
    </div>

    <!-- Form column -->
    <div class="form-card">
        <h2>Send us a message</h2>
        <p class="subtitle">Fill out the form below and our team will respond within 1–2 business days.</p>

        <div class="success-banner" id="successBanner">✓ Your message has been sent. We'll get back to you shortly.</div>

        <form id="contactForm" action="contact_process.php" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="full_name">Full name</label>
                    <input type="text" id="full_name" name="full_name" placeholder="Jane Wanjiru" required>
                </div>
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" id="email" name="email" placeholder="jane@wanjiru.com" required>
                </div>
            </div>

            <div class="form-group">
                <label for="role">I am a</label>
                <select id="role" name="role" required>
                    <option value="" disabled selected>Select one</option>
                    <option value="student">Student</option>
                    <option value="donor">Donor</option>
                    <option value="admin">Administrator</option>
                    <option value="other">Other / Visitor</option>
                </select>
            </div>

            <div class="form-group">
                <label for="subject">Subject</label>
                <select id="subject" name="subject" required>
                    <option value="" disabled selected>Select a topic</option>
                    <option value="application">Scholarship application help</option>
                    <option value="donation">Donating / creating a scholarship</option>
                    <option value="account">Account or login issue</option>
                    <option value="privacy">Privacy inquiry</option>
                    <option value="other">Something else</option>
                </select>
            </div>

            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" placeholder="Tell us how we can help..." required></textarea>
            </div>

            <button type="submit" class="submit-btn">Send Message</button>
        </form>
    </div>
</div>


<!-- FAQ -->
<section class="faq-strip">
    <div class="faq-inner">
        <p class="section-label">Quick answers</p>
        <h2>Frequently asked questions</h2>

        <div class="faq-item">
            <h3>How long does scholarship review take?</h3>
            <p>Admins typically review new scholarships within 2–3 business days of submission by a donor.</p>
        </div>
        <div class="faq-item">
            <h3>How will I know if my application was approved?</h3>
            <p>You'll receive an in-app notification and the status will update on your student dashboard as soon as an admin makes a decision.</p>
        </div>
        <div class="faq-item">
            <h3>Can I edit my application after submitting it?</h3>
            <p>Not directly — please contact support and we'll help you update or withdraw your application before it's reviewed.</p>
        </div>
        <div class="faq-item">
            <h3>I'm a donor — how do I track my scholarship's status?</h3>
            <p>Your donor dashboard shows real-time approval status and a full history of all scholarships you've created.</p>
        </div>
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
    hamburger.addEventListener('click', () => { mobileMenu.classList.toggle('open'); });

    // Lightweight UX: show success state without requiring a live backend
    // (Remove this block once contact_process.php is wired up, or keep it
    // as a fallback success message after the form posts.)
    const form = document.getElementById('contactForm');
    const banner = document.getElementById('successBanner');
</script>

</body>
</html>