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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <section class="about-hero">
        <div class="container">
            <h1>About NEXTGEN</h1>
            <p class="lead mb-0">Sri Lanka's most trusted and innovative mobile shop</p>
        </div>
    </section>
    <section class="about-section">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="about-card h-100">
                        <h3 class="mb-3 text-primary"><i class="fas fa-bullseye me-2"></i>Our Mission</h3>
                        <p>To empower Sri Lankans with the latest and most reliable mobile technology, providing exceptional value and service to every customer.</p>
                        <h3 class="mb-3 text-primary"><i class="fas fa-eye me-2"></i>Our Vision</h3>
                        <p>To be Sri Lanka's most trusted and innovative mobile shop, setting the standard for quality, service, and customer satisfaction.</p>
                        <h3 class="mb-3 text-primary"><i class="fas fa-heart me-2"></i>Our Values</h3>
                        <ul class="about-values">
                            <li><i class="fas fa-user-friends text-secondary me-2"></i>Customer First</li>
                            <li><i class="fas fa-shield-alt text-secondary me-2"></i>Integrity & Trust</li>
                            <li><i class="fas fa-lightbulb text-secondary me-2"></i>Innovation</li>
                            <li><i class="fas fa-certificate text-secondary me-2"></i>Quality Products</li>
                            <li><i class="fas fa-hands-helping text-secondary me-2"></i>Community Focus</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="./assets/images/im.png" class="img-fluid about-img" alt="About NEXTGEN">
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-12">
                    <div class="about-card">
                        <h3 class="mb-3 text-primary"><i class="fas fa-history me-2"></i>Our Story</h3>
                        <p>NEXTGEN was founded in 2020 with a passion for connecting people through technology. From humble beginnings, we've grown into a leading mobile shop in Sri Lanka, trusted by thousands for our wide selection, competitive prices, and friendly service. Our journey is driven by a commitment to innovation and a love for helping our customers stay connected to what matters most.</p>
                    </div>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-12">
                    <h3 class="mb-4 text-primary"><i class="fas fa-users me-2"></i>Meet Our Team</h3>
                    <div class="row g-4 justify-content-center">
                        <div class="col-6 col-md-4 col-lg-2 text-center about-team-member">
                            <img src="./assets/images/u1.jpg" class="rounded-circle mb-2" width="100" height="100" alt="Team Member">
                            <h6 class="fw-bold">Nimesh Madhuranga</h6>
                            <p class="text-muted mb-0"></p>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2 text-center about-team-member">
                            <img src="./assets/images/u2.jpg" class="rounded-circle mb-2" width="100" height="100" alt="Team Member">
                            <h6 class="fw-bold">Dinidu Thishan</h6>
                            <p class="text-muted mb-0"></p>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2 text-center about-team-member">
                            <img src="./assets/images/Hajith.jpg" class="rounded-circle mb-2" width="100" height="100" alt="Team Member">
                            <h6 class="fw-bold">Hajith Mohomed</h6>
                            <p class="text-muted mb-0"></p>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2 text-center about-team-member">
                            <img src="./assets/images/theeksana.jpg" class="rounded-circle mb-2" width="100" height="100" alt="Team Member">
                            <h6 class="fw-bold">Theekshana Sankalpa</h6>
                            <p class="text-muted mb-0"></p>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2 text-center about-team-member">
                            <img src="./assets/images/isuru.jpg" class="rounded-circle mb-2" width="100" height="100" alt="Team Member">
                            <h6 class="fw-bold">Isuru Dhananjaya</h6>
                            <p class="text-muted mb-0"></p>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2 text-center about-team-member">
                            <img src="./assets/images/vindiya.jpg" class="rounded-circle mb-2" width="100" height="100" alt="Team Member">
                            <h6 class="fw-bold">Vindya</h6>
                            <p class="text-muted mb-0"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <a href="products.php" class="about-cta btn btn-lg mt-2"><i class="fas fa-shopping-bag me-2"></i>Shop Our Products</a>
            </div>
        </div>
    </section>
    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html> 