<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - NEXTGEN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <section class="py-5 bg-light">
        <div class="container">
            <h1 class="text-center mb-4">About NEXTGEN</h1>
            <div class="row mb-5">
                <div class="col-md-6">
                    <h3>Our Mission</h3>
                    <p>To empower Sri Lankans with the latest and most reliable mobile technology, providing exceptional value and service to every customer.</p>
                    <h3>Our Vision</h3>
                    <p>To be Sri Lanka's most trusted and innovative mobile shop, setting the standard for quality, service, and customer satisfaction.</p>
                    <h3>Our Values</h3>
                    <ul>
                        <li>Customer First</li>
                        <li>Integrity & Trust</li>
                        <li>Innovation</li>
                        <li>Quality Products</li>
                        <li>Community Focus</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=600&q=80" class="img-fluid rounded shadow" alt="About NEXTGEN">
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-12">
                    <h3>Our Story</h3>
                    <p>NEXTGEN was founded in 2020 with a passion for connecting people through technology. From humble beginnings, we've grown into a leading mobile shop in Sri Lanka, trusted by thousands for our wide selection, competitive prices, and friendly service. Our journey is driven by a commitment to innovation and a love for helping our customers stay connected to what matters most.</p>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-12">
                    <h3 class="mb-4">Meet Our Team</h3>
                    <div class="row g-4 justify-content-center">
                        <div class="col-6 col-md-3 text-center">
                            <img src="https://randomuser.me/api/portraits/men/32.jpg" class="rounded-circle mb-2" width="100" height="100" alt="Team Member">
                            <h6>Kasun Perera</h6>
                            <p class="text-muted">Founder & CEO</p>
                        </div>
                        <div class="col-6 col-md-3 text-center">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" class="rounded-circle mb-2" width="100" height="100" alt="Team Member">
                            <h6>Nadeesha Silva</h6>
                            <p class="text-muted">Head of Sales</p>
                        </div>
                        <div class="col-6 col-md-3 text-center">
                            <img src="https://randomuser.me/api/portraits/men/65.jpg" class="rounded-circle mb-2" width="100" height="100" alt="Team Member">
                            <h6>Ruwan Jayasuriya</h6>
                            <p class="text-muted">Technical Lead</p>
                        </div>
                        <div class="col-6 col-md-3 text-center">
                            <img src="https://randomuser.me/api/portraits/women/68.jpg" class="rounded-circle mb-2" width="100" height="100" alt="Team Member">
                            <h6>Shani Fernando</h6>
                            <p class="text-muted">Customer Care</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <a href="products.php" class="btn btn-primary btn-lg">Shop Our Products</a>
            </div>
        </div>
    </section>
    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 