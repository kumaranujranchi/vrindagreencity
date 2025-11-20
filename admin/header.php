<header class="header">
    <div class="header-content">
        <div class="logo">
            <h1>🏡 Vrinda Green City</h1>
        </div>
        <nav class="nav">
            <a href="index.php" class="nav-link">Dashboard</a>
            <a href="contact-leads.php" class="nav-link">Contact Leads</a>
            <a href="property-inquiries.php" class="nav-link">Property Inquiries</a>
            <a href="subscribers.php" class="nav-link">Subscribers</a>
            <a href="test-chatbot-leads.php" class="nav-link">Test Chatbot</a>
            <a href="logout.php" class="nav-link logout">Logout</a>
        </nav>
        <div class="user-info">
            <span>👤 <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
        </div>
    </div>
</header>
