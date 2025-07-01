<?php
session_start();

include "connection.php";

// Fetch FAQs from the database
$query = "SELECT * FROM faqs WHERE status = 'answered'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MMU HUB</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
            integrity="sha512-Fo3rlrZj/k7ujTnHq+HXftMMDP5QjwOlj4M97ZT8YvZT5OE+B6VryDdEbub6a1WHi9r0DxvRvW9RnWimA4Qrlw=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <script src="https://kit.fontawesome.com/23b5932f1f.js" crossorigin="anonymous"></script> 
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="css/homepage.css">
</head>

<body>
    <!-- Navbar Section -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">MMU HUB</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#faq">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="content" data-aos="fade-up"  data-aos-delay="100">
            <h1>WELCOME TO MMU HUB</h1>
            <p>Your one-stop platform for MMU community</p>
            <a href="login.php" class="btn btn-get-started">Get Started</a>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section">
        <div class="container" data-aos="fade-up">
            <h2>Our Services</h2>
            <div class="row">
                <div class="col-md-3">
                    <div class="service-card">
                        <i class="fas fa-calendar-check"></i>
                        <h3>Facility Booking</h3>
                        <p>Reserve meeting rooms, lecture halls, sports venues, and more with just a few clicks.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="service-card">
                        <i class="fas fa-bullhorn"></i>
                        <h3>Announcements</h3>
                        <p>Stay updated with the latest campus news, events, and important announcements.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="service-card">
                        <i class="fas fa-comments"></i>
                        <h3>Communication Platform</h3>
                        <p>Message and connect with other MMU users for collaboration and discussions.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="service-card">
                        <i class="fas fa-question-circle"></i>
                        <h3>Support & FAQ</h3>
                        <p>Have questions? Check our FAQ section or reach out to our support team for help.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- About MMU Section -->
    <section id="about" class="about py-5">
        <div class="container" data-aos="fade-up">
            <h2 class="text-center mb-5">About Multimedia University</h2>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="about-video">
                    <video class="video-player w-100" autoplay muted controls>
                        <source src="videos/MMUCyberjaya.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="about-text">
                        <p>Multimedia University (MMU) is Malaysia's first private university, established in 1996. With campuses in Cyberjaya and Melaka, MMU offers a wide range of programs in various fields including engineering, information technology, business, and creative multimedia.</p>
                        <p>Our state-of-the-art facilities and industry-focused curriculum ensure that students receive a high-quality education that prepares them for the challenges of the modern workplace. MMU Hub is designed to enhance the campus experience by simplifying facility management for all users.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


     <!-- FAQ Section -->
     <section id="faq" class="py-5">
        <div class="container" data-aos="fade-up">
            <h2 class="text-center mb-5">Frequently Asked Questions</h2>
            <div class="accordion" id="faqAccordion">
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= $row['id'] ?>">
                                <?= htmlspecialchars($row['question']) ?>
                            </button>
                        </h2>
                        <div id="faq<?= $row['id'] ?>" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <?= htmlspecialchars($row['answer']) ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Join MMU HUB Today!</h2>
            <p>Don't miss out. Register now to get started!</p>
            <a href="register.php">Register Now</a>
        </div>
    </section>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2024 MMU HUB. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
        crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init({
            duration: 1200,
        });
    </script>
</body>

</html>
