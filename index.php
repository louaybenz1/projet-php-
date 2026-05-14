<?php
// index.php
// ============================================================
// Landing Page — TEK-UP GYM
// Converted from the original index.html.
// Now includes contact form handling:
//   - Validates the submitted fields server-side
//   - Inserts the message into contact_messages table
//   - Shows a success or error message to the visitor
// ============================================================

session_start();

require_once 'config/db.php';

$contactSuccess = "";
$contactError   = "";

// ============================================================
// Handle: Contact Form Submission
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {

    $name    = trim($_POST['contact_name']    ?? '');
    $email   = trim($_POST['contact_email']   ?? '');
    $message = trim($_POST['contact_message'] ?? '');

    // Server-side validation
    // (JS validation runs first, but we never trust the client alone)
    if (empty($name) || empty($email) || empty($message)) {
        $contactError = "Please fill in all fields before sending.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $contactError = "Please enter a valid email address.";

    } elseif (strlen($message) < 10) {
        $contactError = "Your message is too short. Please write at least 10 characters.";

    } else {
        // All good — insert into the database
        try {
            $db   = new Database();
            $conn = $db->getConnection();

            $stmt = $conn->prepare(
                "INSERT INTO contact_messages (name, email, message)
                 VALUES (:name, :email, :message)"
            );
            $stmt->bindParam(':name',    $name);
            $stmt->bindParam(':email',   $email);
            $stmt->bindParam(':message', $message);
            $stmt->execute();

            $contactSuccess = "Thank you, {$name}! Your message has been received. We'll get back to you soon.";

        } catch (PDOException $e) {
            // In production you would log this, not display it
            $contactError = "Something went wrong. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TEK-UP GYM</title>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Small additions for the contact form feedback messages */
        .contact-alert {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-size: 0.95rem;
            font-weight: 500;
        }
        .contact-alert-success {
            background: rgba(40, 167, 69, 0.15);
            border: 1px solid rgba(40, 167, 69, 0.4);
            color: #1a5c2a;
        }
        .contact-alert-error {
            background: rgba(220, 53, 69, 0.12);
            border: 1px solid rgba(220, 53, 69, 0.4);
            color: #7b1521;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navgym section-content">
            <a href="#" class="nav-logo">
                <h2 class="logo-text">💪gym</h2>
            </a>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="#" class="nav-link">Home</a>
                </li>
                <li class="nav-item">
                    <a href="#about-section" class="nav-link">About</a>
                </li>
                <li class="nav-item">
                    <a href="#coaches" class="nav-link">Coaches</a>
                </li>
                <li class="nav-item">
                    <a href="#Gallery" class="nav-link">Gallery</a>
                </li>
                <li class="nav-item">
                    <a href="#Testimonials" class="nav-link">Testimonials</a>
                </li>
                <li class="nav-item">
                    <a href="#contact" class="nav-link">Contact</a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                <!-- If already logged in, show a shortcut to their dashboard -->
                <li class="nav-item">
                    <a href="<?= $_SESSION['role'] ?>/dashboard.php" class="nav-link">
                        My Dashboard
                    </a>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a href="login.php" class="nav-link">Login</a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="section-content">
                <div class="hero-details">
                    <h2 class="title">TEK-UP GYM</h2>
                    <h3 class="subtitle">ENGINEER YOUR ELITE BODY</h3>
                    <p class="description">
                        <b class="emphisized-comment"><em>Don't just train, TRANSFORM!</em></b><br>
                        Modern equipment and expert<br>coaches at your service.
                    </p>
                    <div class="buttons">
                        <a href="register.php" class="button order-now">Join Us</a>
                        <a href="#contact"      class="button contact-now">Contact Us</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about-section" class="about-section">
            <div class="section-content">
                <h2 class="section-title">About Us</h2>
                <div class="about-row">
                    <div class="about-details">
                        <p class="text">
                            We are a training space dedicated to strength, discipline, and long-term health.
                            Our gym provides modern equipment and structured programmes designed to help
                            members train safely and effectively.
                        </p>
                    </div>
                    <div class="about-image">
                        <img src="assets/images/pexels-marcuschanmedia-18060023.jpg" alt="Gym training area">
                    </div>
                </div>
                <div class="about-row reverse">
                    <div class="about-details">
                        <p class="text">
                            Progress comes from consistency, not shortcuts. Every session is an opportunity
                            to improve, physically and mentally, at your own pace.
                        </p>
                    </div>
                    <div class="about-image">
                        <img src="assets/images/pexels-olly-3854576.jpg" alt="People training together">
                    </div>
                </div>
                <div class="about-row">
                    <div class="about-details">
                        <p class="text">
                            More than a gym, we are a community. Beginners and experienced athletes train
                            side by side in an environment built on respect and support.
                        </p>
                    </div>
                    <div class="about-image">
                        <img src="assets/images/pexels-victorfreitas-841130.jpg" alt="Gym community">
                    </div>
                </div>
            </div>
        </section>

        <!-- Coaches Section -->
        <section id="coaches" class="coaches-section">
            <div class="section-content">
                <h2 class="section-title">Our Coaches</h2>
                <h3 class="coaches-comment">Beginner to pro, our coaches are always there to support you.</h3>
                <div class="coaches-grid">
                    <div class="coach-card">
                        <img src="assets/images/pexels-marcuschanmedia-18060023.jpg" alt="Coach Ahmed">
                        <h3>Ahmed Musculation</h3>
                        <p>Strength &amp; Conditioning Specialist</p>
                    </div>
                    <div class="coach-card">
                        <img src="assets/images/pexels-olly-3757959.jpg" alt="Coach Lobna">
                        <h3>Lobna Pilates</h3>
                        <p>Yoga &amp; Flexibility Coach</p>
                    </div>
                    <div class="coach-card">
                        <img src="assets/images/pexels-willpicturethis-1954524.jpg" alt="Coach Djo">
                        <h3>Djo YES YOU CAN</h3>
                        <p>Cardio &amp; Endurance Trainer</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Gallery Section -->
        <section id="Gallery" class="gallery-section">
            <h2 class="section-title">Gallery</h2>
            <h3 class="gallery-comment">
                Here's a selection of our top customers.<br>
                All you need is resolve, dedication and a subscription.
            </h3>
            <div class="gallery-container">
                <ul class="gallery-list">
                    <li class="gallery-item">
                        <img src="assets/images/598072282_1828228167889079_5264217909014862266_n.jpg" alt="bdan 1" class="gallery-image">
                        <h3 class="gallery-name">Louay Benzarti</h3>
                        <p class="gallery-text">a9wa bdan fik ye Tunis.</p>
                    </li>
                    <li class="gallery-item">
                        <img src="assets/images/597554921_4198321160441397_1272258286434914994_n.jpg" alt="bdan 2" class="gallery-image">
                        <h3 class="gallery-name">Stoufa Gym Rat</h3>
                        <p class="gallery-text">healthy life style.</p>
                    </li>
                    <li class="gallery-item">
                        <img src="assets/images/590236999_1439250520961406_7048939557081141692_n.jpg" alt="bdan 3" class="gallery-image">
                        <h3 class="gallery-name">Aymen Proteine</h3>
                        <p class="gallery-text">yosken fi salla.</p>
                    </li>
                    <li class="gallery-item">
                        <img src="assets/images/pexels-thelazyartist-2247179.jpg" alt="bdan 4" class="gallery-image">
                        <h3 class="gallery-name">Asma Detox</h3>
                        <p class="gallery-text">Strong Independant Woman.</p>
                    </li>
                    <li class="gallery-item">
                        <img src="assets/images/508607956_693956870121565_8532966406027391778_n.jpg" alt="bdan 5" class="gallery-image">
                        <h3 class="gallery-name">Kamel musculation</h3>
                        <p class="gallery-text">2x World Champion.</p>
                    </li>
                </ul>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section id="Testimonials" class="testimonials-section">
            <h2 class="section-title">Testimonials</h2>
            <h3 class="testimonial-comment">
                If you still have doubts, here's what our clients say about us:
            </h3>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="client-info">
                        <img src="assets/images/ahmed_ben_salah.jpg" alt="Client 1" class="client-img">
                        <div class="client-details">
                            <h4 class="client-name">Ahmed Ben Saleh</h4>
                            <div class="star-rating">★★★★★</div>
                        </div>
                    </div>
                    <p class="client-quote">"A9wa salle fi tounes, el ambiance tayara w el coachs pro 3allekher."</p>
                </div>
                <div class="testimonial-card">
                    <div class="client-info">
                        <img src="assets/images/sami_tounsi.jpg" alt="Client 2" class="client-img">
                        <div class="client-details">
                            <h4 class="client-name">Sami Tounsi</h4>
                            <div class="star-rating">★★★★☆</div>
                        </div>
                    </div>
                    <p class="client-quote">"Service professionnel et matériel jdid. Highly recommended for bodybuilding."</p>
                </div>
                <div class="testimonial-card">
                    <div class="client-info">
                        <img src="assets/images/yasmine.jpg" alt="Client 3" class="client-img">
                        <div class="client-details">
                            <h4 class="client-name">Yasmine Dhouib</h4>
                            <div class="star-rating">★★★★★</div>
                        </div>
                    </div>
                    <p class="client-quote">"The community here is amazing. Everyone motivates each other to push harder."</p>
                </div>
                <div class="testimonial-card">
                    <div class="client-info">
                        <img src="assets/images/mourad.jpg" alt="Client 4" class="client-img">
                        <div class="client-details">
                            <h4 class="client-name">Mourad Yahiaoui</h4>
                            <div class="star-rating">★★★★★</div>
                        </div>
                    </div>
                    <p class="client-quote">"Mchalla 3likom, training programs are top notch. I lost 10kg in 2 months!"</p>
                </div>
                <div class="testimonial-card">
                    <div class="client-info">
                        <img src="assets/images/fatma.jpg" alt="Client 5" class="client-img">
                        <div class="client-details">
                            <h4 class="client-name">Fatma Ben Othmane</h4>
                            <div class="star-rating">★★★★☆</div>
                        </div>
                    </div>
                    <p class="client-quote">"Great equipment and very clean. The music choice is always motivating."</p>
                </div>
                <div class="testimonial-card">
                    <div class="client-info">
                        <img src="assets/images/mrkhaled.jpg" alt="Client 6" class="client-img">
                        <div class="client-details">
                            <h4 class="client-name">Khaled Jerbi</h4>
                            <div class="star-rating">★★★★★</div>
                        </div>
                    </div>
                    <p class="client-quote">"Best place to focus on your gains. No distractions, just pure hard work."</p>
                </div>
            </div>
        </section>

        <!-- ============================================================
             Contact Section
             Now a real PHP form that saves to the database.
             The form POSTs back to this same page (index.php).
             The hidden field "contact_submit" tells PHP which form was sent.
        ============================================================ -->
        <section id="contact" class="contact-section">
            <div class="section-content">
                <h2 class="section-title">Contact Us</h2>
                <div class="contact-container">

                    <!-- Left side: contact info -->
                    <div class="contact-info">
                        <h3>Get in Touch</h3>
                        <p>📍 TEK-UP University, Tunis</p>
                        <p>📞 +216 12 345 678</p>
                        <p>✉️ contact@tekupgym.tn</p>
                        <div class="social-links">
                            <a href="#" class="social-icon"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#" class="social-icon"><i class="fa-brands fa-instagram"></i></a>
                            <a href="#" class="social-icon"><i class="fa-brands fa-whatsapp"></i></a>
                        </div>
                    </div>

                    <!-- Right side: contact form -->
                    <form class="contact-form"
                          action="#contact"
                          method="POST"
                          id="contactForm"
                          novalidate>

                        <!-- Hidden field: tells PHP this is the contact form -->
                        <input type="hidden" name="contact_submit" value="1">

                        <!-- PHP feedback messages -->
                        <?php if ($contactSuccess): ?>
                            <div class="contact-alert contact-alert-success">
                                <i class="fa-solid fa-circle-check"></i>
                                <?= htmlspecialchars($contactSuccess) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($contactError): ?>
                            <div class="contact-alert contact-alert-error">
                                <i class="fa-solid fa-circle-xmark"></i>
                                <?= htmlspecialchars($contactError) ?>
                            </div>
                        <?php endif; ?>

                        <input
                            type="text"
                            name="contact_name"
                            id="contact_name"
                            placeholder="Your Name"
                            value="<?= htmlspecialchars($_POST['contact_name'] ?? '') ?>"
                            required
                        >
                        <span class="contact-field-error" id="err-contact_name"
                              style="color:#c0392b; font-size:0.82rem; margin-top:-10px; display:block;"></span>

                        <input
                            type="email"
                            name="contact_email"
                            id="contact_email"
                            placeholder="Your Email"
                            value="<?= htmlspecialchars($_POST['contact_email'] ?? '') ?>"
                            required
                        >
                        <span class="contact-field-error" id="err-contact_email"
                              style="color:#c0392b; font-size:0.82rem; margin-top:-10px; display:block;"></span>

                        <textarea
                            name="contact_message"
                            id="contact_message"
                            placeholder="Your Message"
                            rows="5"
                            required
                        ><?= htmlspecialchars($_POST['contact_message'] ?? '') ?></textarea>
                        <span class="contact-field-error" id="err-contact_message"
                              style="color:#c0392b; font-size:0.82rem; margin-top:-10px; display:block;"></span>

                        <button type="submit" class="submit-btn">
                            <i class="fa-solid fa-paper-plane"></i> Send Message
                        </button>

                    </form>
                </div>
            </div>
        </section>

    </main>

    <script src="js/validation.js"></script>
</body>
</html>
