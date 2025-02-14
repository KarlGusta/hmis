<!-- Navbar section -->
<header class="navbar navbar-expand-md d-none d-lg-flex d-print-none navbar-custom">
    <div class="container-xl d-flex justify-content-end">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
            <h1 class="mb-0">Welcome, <?php echo htmlspecialchars(ucfirst(strtolower($_SESSION['username']))); ?> 👋</h1>
        <?php endif; ?>
    </div>
</header>
<!-- End of Navbar section -->