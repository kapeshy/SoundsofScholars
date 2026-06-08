<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SoundsOfScholars | Scholarship Management System</title>
    <style>
       
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f4f6f8;
            color: #333;
        }

        /* Header */
        header {
            background-color: #2980b9;
            color: white;
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        nav {
            width: 80%;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;
        }

        nav a:hover {
            text-decoration: underline;
        }

        /* Hero Section */
        .hero {
            text-align: center;
            padding: 100px 20px 50px 20px;
            color: white;
            position: relative;
        }

        .hero::after {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 700px;
            margin: auto;
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 20px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .hero .buttons a {
            padding: 15px 25px;
            margin: 10px;
            background-color: #e67e22;
            border-radius: 5px;
            font-weight: bold;
            transition: 0.3s;
        }

        .hero .buttons a:hover {
            background-color: #cf711f;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 20px 0;
            background-color: #2c3e50;
            color: white;
            font-size: 14px;
        }

        @media(max-width: 768px){
            .hero h1 {
                font-size: 36px;
            }

            .hero p {
                font-size: 16px;
            }

            nav {
                flex-direction: column;
            }

            nav a {
                margin: 10px 0 0 0;
            }
        }
    </style>
</head>
<body>

<header>
    <nav>
        <div class="logo"><h2>SoundsOfScholars</h2></div>
        <div class="nav-links">
            <a href="about.php">About</a>
            <a href="donor/dashboard.php">Donors</a>
            <a href="auth/login.php">Login</a>
            <a href="auth/register.php">Register</a>
        </div>
    </nav>
</header>

<section class="hero">
    <div class="hero-content">
        <h1>Empowering Education for All</h1>
        <p>SoundsOfScholars connects students, donors, and administrators to make scholarships accessible, transparent, and efficient. Apply, donate, or manage with ease.</p>
        <div class="buttons">
            <a href="auth/login.php">Login</a>
            <a href="auth/register.php">Register</a>
        </div>
    </div>
</section>

<footer>
    <p>&copy; <?php echo date("Y"); ?> SoundsOfScholars. All rights reserved.</p>
</footer>

</body>
</html>
