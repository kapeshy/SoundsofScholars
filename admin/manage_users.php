<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];

/* ── Which tab is active ─────────────────────────────────────── */
$tab = isset($_GET['tab']) && $_GET['tab'] === 'messages' ? 'messages' : 'users';

/* ── USERS TAB: delete a user (any role, but never yourself) ──── */
if (isset($_GET['delete_user'])) {
    $del_id = intval($_GET['delete_user']);

    if ($del_id !== intval($admin_id)) {
        mysqli_query($conn, "DELETE FROM users WHERE id='$del_id'");
    }

    header("Location: manage_users.php?tab=users");
    exit();
}

/* ── MESSAGES TAB: toggle read/unread ──────────────────────────── */
if (isset($_GET['toggle_read'])) {
    $msg_id = intval($_GET['toggle_read']);

    $current = mysqli_fetch_assoc(mysqli_query(
        $conn, "SELECT is_read FROM contact_messages WHERE id='$msg_id'"
    ));

    if ($current) {
        $new_val = $current['is_read'] ? 0 : 1;
        mysqli_query($conn, "UPDATE contact_messages SET is_read='$new_val' WHERE id='$msg_id'");
    }

    header("Location: manage_users.php?tab=messages");
    exit();
}

/* ── MESSAGES TAB: delete a message ────────────────────────────── */
if (isset($_GET['delete_msg'])) {
    $msg_id = intval($_GET['delete_msg']);
    mysqli_query($conn, "DELETE FROM contact_messages WHERE id='$msg_id'");
    header("Location: manage_users.php?tab=messages");
    exit();
}

/* ── Data for USERS tab ─────────────────────────────────────────
   "General users" = anyone with no scholarships created (donors with
   0 scholarships) and no applications submitted (students with 0
   applications) — i.e. accounts that exist but haven't engaged yet.
   Role filter still lets admin see everyone if needed.
*/
$role_filter = isset($_GET['role']) ? $_GET['role'] : 'all';

$userSql = "
    SELECT u.*,
        (SELECT COUNT(*) FROM scholarships WHERE donor_id = u.id) AS scholarship_count,
        (SELECT COUNT(*) FROM applications WHERE user_id = u.id)  AS application_count
    FROM users u
";

if ($role_filter !== 'all' && in_array($role_filter, ['admin', 'student', 'donor'], true)) {
    $userSql .= " WHERE u.role = '$role_filter'";
}

$userSql .= " ORDER BY u.created_at DESC";

$users = mysqli_query($conn, $userSql);

/* Count of "inactive"/general users for the tab badge */
$inactiveCount = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM users u
    WHERE u.role != 'admin'
    AND (SELECT COUNT(*) FROM scholarships WHERE donor_id = u.id) = 0
    AND (SELECT COUNT(*) FROM applications WHERE user_id = u.id) = 0
"))['total'];

/* ── Data for MESSAGES tab ──────────────────────────────────────── */
$messages = mysqli_query($conn, "SELECT * FROM contact_messages ORDER BY created_at DESC");

$unreadCount = mysqli_fetch_assoc(mysqli_query(
    $conn, "SELECT COUNT(*) AS total FROM contact_messages WHERE is_read = 0"
))['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Users | Admin</title>
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
    background: var(--gray-50);
    color: var(--gray-900);
}

/* ─── SIDEBAR ─── */
.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    height: 100%;
    width: 230px;
    background: var(--blue-900);
    color: white;
    box-shadow: 4px 0 20px rgba(0,0,0,0.08);
}

.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 20px 22px 22px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}

.sidebar-brand .logo-icon {
    width: 32px;
    height: 32px;
    background: var(--amber-200);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}

.sidebar-brand span {
    font-family: 'Playfair Display', serif;
    font-size: 17px;
    font-weight: 700;
    letter-spacing: -0.3px;
}

.sidebar nav { padding-top: 10px; }

.sidebar a {
    display: block;
    padding: 15px 22px;
    color: rgba(255,255,255,0.75);
    text-decoration: none;
    font-weight: 500;
    font-size: 14.5px;
    border-left: 4px solid transparent;
    transition: background 0.2s, color 0.2s, border-color 0.2s;
}

.sidebar a:hover {
    background: rgba(255,255,255,0.08);
    color: white;
    border-left: 4px solid var(--amber-200);
}

.sidebar a.active {
    background: rgba(255,255,255,0.1);
    color: white;
    border-left: 4px solid var(--amber-200);
}

.sidebar a.logout-link {
    color: rgba(255,255,255,0.55);
    margin-top: 14px;
    border-top: 1px solid rgba(255,255,255,0.08);
}

/* ─── HEADER ─── */
header {
    position: fixed;
    top: 0;
    left: 230px;
    right: 0;
    height: 64px;
    background: var(--blue-900);
    color: white;
    display: flex;
    align-items: center;
    padding: 0 28px;
    font-weight: 500;
    font-size: 14.5px;
    z-index: 1000;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}

/* ─── MAIN ─── */
.main {
    margin-left: 230px;
    margin-top: 64px;
    padding: 32px;
}

.main h2 {
    font-family: 'Playfair Display', serif;
    font-size: 24px;
    font-weight: 700;
    color: var(--blue-900);
    margin-bottom: 20px;
}

/* ─── TABS ─── */
.tabs {
    display: flex;
    gap: 6px;
    margin-bottom: 24px;
    border-bottom: 1px solid var(--gray-100);
}

.tab-link {
    padding: 12px 20px;
    text-decoration: none;
    font-size: 14.5px;
    font-weight: 600;
    color: var(--gray-600);
    border-bottom: 3px solid transparent;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: color 0.15s, border-color 0.15s;
}

.tab-link:hover { color: var(--blue-800); }

.tab-link.active {
    color: var(--blue-900);
    border-bottom-color: var(--amber-200);
}

.tab-count {
    background: var(--blue-50);
    color: var(--blue-800);
    font-size: 11.5px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 12px;
}

.tab-link.active .tab-count {
    background: var(--amber-200);
    color: var(--blue-900);
}

/* ─── FILTER BAR ─── */
.filter-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}

.filter-bar label {
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-900);
}

.filter-bar select {
    padding: 9px 14px;
    border-radius: 8px;
    border: 1px solid var(--gray-100);
    background: white;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    color: var(--gray-900);
}

.filter-bar select:focus {
    outline: none;
    border-color: var(--blue-400);
}

/* ─── TABLE ─── */
.table-box {
    background: white;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(4,44,83,0.06);
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: var(--blue-900);
    color: white;
    padding: 14px 16px;
    text-align: left;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.02em;
    text-transform: uppercase;
}

td {
    padding: 14px 16px;
    border-bottom: 1px solid var(--gray-100);
    font-size: 13.5px;
    color: var(--gray-900);
    vertical-align: top;
}

tr:last-child td { border-bottom: none; }

tbody tr { transition: background 0.15s; }
tr:hover { background: var(--blue-50); }

/* ─── ROLE BADGES ─── */
.role-badge {
    padding: 4px 10px;
    border-radius: 14px;
    font-size: 11.5px;
    font-weight: 600;
    letter-spacing: 0.02em;
    text-transform: uppercase;
    display: inline-block;
}

.role-badge.admin   { background: var(--blue-100); color: var(--blue-900); }
.role-badge.student { background: var(--teal-50);  color: var(--teal-600); }
.role-badge.donor   { background: #FAEEDA;         color: var(--amber-400); }

.engagement-tag {
    font-size: 12px;
    color: var(--gray-200);
}

.engagement-tag.active-tag {
    color: var(--teal-600);
    font-weight: 500;
}

/* ─── BUTTONS ─── */
.btn {
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    margin-right: 6px;
    display: inline-block;
    white-space: nowrap;
    transition: opacity 0.15s, transform 0.15s;
}

.btn:hover { opacity: 0.85; transform: translateY(-1px); }

.btn.delete { background: #D85A30; color: white; }
.btn.reply  { background: var(--blue-600); color: white; }
.btn.toggle-read   { background: var(--gray-100); color: var(--gray-900); }
.btn.toggle-unread { background: var(--amber-200); color: var(--blue-900); }

.self-tag {
    font-size: 12px;
    color: var(--gray-200);
    font-style: italic;
}

/* ─── MESSAGE ROW ─── */
.msg-row.unread { background: var(--blue-50); }
.msg-row.unread td { font-weight: 600; }

.msg-subject {
    display: inline-block;
    font-size: 11.5px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    color: var(--blue-600);
    background: var(--blue-50);
    padding: 3px 9px;
    border-radius: 6px;
    margin-bottom: 6px;
}

.msg-row.unread .msg-subject {
    background: white;
}

.msg-body {
    font-size: 13px;
    color: var(--gray-600);
    line-height: 1.6;
    max-width: 320px;
}

.read-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 6px;
}

.read-dot.unread-dot { background: var(--amber-200); }
.read-dot.read-dot   { background: var(--gray-100); }

.empty-state {
    text-align: center;
    padding: 50px 30px;
    color: var(--gray-200);
    font-size: 14.5px;
}

@media (max-width: 768px) {
    .sidebar { width: 200px; }
    header { left: 200px; }
    .main { margin-left: 200px; padding: 20px; }
    .table-box { overflow-x: auto; }
    table { min-width: 720px; }
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-brand">
        <div class="logo-icon">🎓</div>
        <span>SoundsOfScholars</span>
    </div>

    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_students.php">Manage Students Applications</a>
        <a href="manage_donors.php">Manage Donor Scholarships</a>
        <a href="manage_users.php" class="active">Manage Users</a>
        <a href="../auth/logout.php" class="logout-link">Logout</a>
    </nav>
</div>

<!-- HEADER -->
<header>
    Manage Users Messages
</header>

<!-- MAIN -->
<div class="main">

    <h2>Users & Inquiries</h2>

    <!-- TABS -->
    <div class="tabs">
        <a href="?tab=users" class="tab-link <?php echo $tab === 'users' ? 'active' : ''; ?>">
            All Users
            <span class="tab-count"><?php echo $inactiveCount; ?> inactive</span>
        </a>
        <a href="?tab=messages" class="tab-link <?php echo $tab === 'messages' ? 'active' : ''; ?>">
            Contact Messages
            <?php if ($unreadCount > 0): ?>
                <span class="tab-count"><?php echo $unreadCount; ?> unread</span>
            <?php endif; ?>
        </a>
    </div>

    <?php if ($tab === 'users'): ?>

        <!-- ═══════════ ALL USERS TAB ═══════════ -->
        <form class="filter-bar" method="GET">
            <input type="hidden" name="tab" value="users">
            <label for="role">Filter by role:</label>
            <select name="role" id="role" onchange="this.form.submit()">
                <option value="all" <?php echo $role_filter === 'all' ? 'selected' : ''; ?>>All roles</option>
                <option value="student" <?php echo $role_filter === 'student' ? 'selected' : ''; ?>>Students</option>
                <option value="donor" <?php echo $role_filter === 'donor' ? 'selected' : ''; ?>>Donors</option>
                <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Admins</option>
            </select>
        </form>

        <div class="table-box">
        <table>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Activity</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>

            <?php if (mysqli_num_rows($users) > 0): ?>

                <?php while ($u = mysqli_fetch_assoc($users)): ?>
                    <?php
                    $isInactive = ($u['scholarship_count'] == 0 && $u['application_count'] == 0 && $u['role'] !== 'admin');
                    ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td>
                            <span class="role-badge <?php echo $u['role']; ?>">
                                <?php echo htmlspecialchars($u['role']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($u['role'] === 'donor'): ?>
                                <span class="engagement-tag <?php echo $u['scholarship_count'] > 0 ? 'active-tag' : ''; ?>">
                                    <?php echo $u['scholarship_count']; ?> scholarship<?php echo $u['scholarship_count'] == 1 ? '' : 's'; ?>
                                </span>
                            <?php elseif ($u['role'] === 'student'): ?>
                                <span class="engagement-tag <?php echo $u['application_count'] > 0 ? 'active-tag' : ''; ?>">
                                    <?php echo $u['application_count']; ?> application<?php echo $u['application_count'] == 1 ? '' : 's'; ?>
                                </span>
                            <?php else: ?>
                                <span class="engagement-tag">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date("d M Y", strtotime($u['created_at'])); ?></td>
                        <td>
                            <?php if (intval($u['id']) === intval($admin_id)): ?>
                                <span class="self-tag">You</span>
                            <?php else: ?>
                                <a href="?tab=users&delete_user=<?php echo $u['id']; ?>"
                                   class="btn delete"
                                   onclick="return confirm('Delete this user? This cannot be undone.');">
                                   Delete
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>

            <?php else: ?>
                <tr><td colspan="7" class="empty-state">No users found for this filter.</td></tr>
            <?php endif; ?>

        </table>
        </div>

    <?php else: ?>

        <!-- ═══════════ CONTACT MESSAGES TAB ═══════════ -->
        <div class="table-box">
        <table>
            <tr>
                <th></th>
                <th>From</th>
                <th>Role</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Received</th>
                <th>Actions</th>
            </tr>

            <?php if (mysqli_num_rows($messages) > 0): ?>

                <?php while ($m = mysqli_fetch_assoc($messages)): ?>
                    <tr class="msg-row <?php echo $m['is_read'] ? '' : 'unread'; ?>">
                        <td>
                            <span class="read-dot <?php echo $m['is_read'] ? 'read-dot' : 'unread-dot'; ?>"></span>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($m['full_name']); ?><br>
                            <span style="color: var(--gray-600); font-weight: 400; font-size: 12.5px;">
                                <?php echo htmlspecialchars($m['email']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="role-badge <?php echo $m['role']; ?>">
                                <?php echo htmlspecialchars($m['role']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="msg-subject"><?php echo htmlspecialchars($m['subject']); ?></span>
                        </td>
                        <td>
                            <div class="msg-body"><?php echo nl2br(htmlspecialchars($m['message'])); ?></div>
                        </td>
                        <td><?php echo date("d M Y, g:i a", strtotime($m['created_at'])); ?></td>
                        <td>
                            <a href="mailto:<?php echo htmlspecialchars($m['email']); ?>?subject=Re: Your message to SoundsOfScholars"
                               class="btn reply">
                               Reply
                            </a>

                            <a href="?tab=messages&toggle_read=<?php echo $m['id']; ?>"
                               class="btn <?php echo $m['is_read'] ? 'toggle-unread' : 'toggle-read'; ?>">
                               Mark <?php echo $m['is_read'] ? 'unread' : 'read'; ?>
                            </a>

                            <a href="?tab=messages&delete_msg=<?php echo $m['id']; ?>"
                               class="btn delete"
                               onclick="return confirm('Delete this message?');">
                               Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>

            <?php else: ?>
                <tr><td colspan="7" class="empty-state">No contact messages yet.</td></tr>
            <?php endif; ?>

        </table>
        </div>

    <?php endif; ?>

</div>

</body>
</html>