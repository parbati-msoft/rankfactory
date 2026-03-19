<?php

/**
 * index.php
 * RankFactory Landing Page — entry point.
 * Delegates all API logic to includes/api.php.
 */

require_once __DIR__ . '/includes/api.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RankFactory — Crack Every Exam</title>
    <meta name="description" content="RankFactory by eTutorClass — Nepal's leading platform for NEB Class 12 Science exam preparation. Access live classes, chapter-wise notes, mock tests, and AI-powered performance analytics to boost your board exam success.">

    <meta name="keywords" content="NEB Class 12 Science, NEB exam preparation Nepal, Class 12 Physics Chemistry Biology Maths, NEB board exam guide, +2 science preparation, Nepal education platform, mock tests NEB, online classes Nepal">

    <link rel="icon" type="image/png" href="assets/images/favicon.ico" sizes="32x32">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hide-form-initially .form-card {
            display: none !important;
        }

        .hide-form-initially .success-card {
            display: block !important;
        }
    </style>
</head>

<body>

    <!-- ══════════════════════════════════════════════════════
     HEADER
    ═══════════════════════════════════════════════════════════ -->
    <header>
        <div class="header-inner">
            <a href="/" class="logo">
                <img src="assets/images/logo.png" alt="RankFactory Logo">
            </a>
            <!-- <a href="#register" class="header-cta">Register Free →</a> -->
        </div>
    </header>


    <!-- ══════════════════════════════════════════════════════
     HERO
    ═══════════════════════════════════════════════════════════ -->
    <section class="hero">
        <div class="hero-bg"></div>

        <?php
        $showSuccess = (isset($_SESSION['registration_success']) || isset($_GET['success']));
        ?>

        <div class="hero-inner <?= $showSuccess ? 'success-mode' : '' ?>">

            <!-- Left: -->
            <?php if (!$showSuccess): ?>
                <div class="hero-left">
                    <h1>
                        Turn Your<br>
                        Hard Work Into<br>
                        <span class="accent">Top Ranks</span>
                    </h1>

                    <p class="hero-sub">
                        Master the NEB Syllabus with Nepal’s top educators. Access AI-powered mock tests,
                        chapter-wise digital notes, and live interactive sessions designed to turn
                        your preparation into top board ranks.
                    </p>
                </div>
            <?php endif; ?>

            <!-- Right: Registration Form or Success Card -->
            <div id="register">

                <?php if ($showSuccess): ?>
                    <script>
                        // Save to LocalStorage immediately so JS knows user is registered
                        localStorage.setItem('rf_registered', 'true');
                    </script>

                    <div class="success-card">

                        <div class="confetti-ring">
                            <span></span><span></span><span></span>
                            <span></span><span></span><span></span>
                        </div>

                        <div class="success-icon">🎉</div>
                        <div class="success-title">
                            <?= ($_SESSION['is_existing'] ?? false) ? "Welcome Back!" : "You're Registered!" ?>
                        </div>
                        <p class="success-sub">
                            Your enrollment in the class is confirmed.<br>
                            For further instructions, please join our WhatsApp group.
                        </p>

                        <!-- ── ACCESS YOUR CLASSES ── -->
                        <div class="action-divider"><span>📲 Access Your Classes</span></div>

                        <div class="app-buttons">
                            <a href="https://etutorclass.com/login" target="_blank" rel="noopener" class="btn-store btn-web">
                                <svg class="store-icon" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z" />
                                </svg>
                                <div class="store-text">
                                    <small>Login on</small>
                                    <strong>Website</strong>
                                </div>
                            </a>
                            <a href="https://etutorclass.com/app-download" target="_blank" rel="noopener" class="btn-store btn-android">
                                <svg class="store-icon" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.523 15.341 5.2 22.006a1.1 1.1 0 0 1-1.569-.983V2.977a1.1 1.1 0 0 1 1.569-.983l12.323 6.665-2.25 3.341zm1.356-2.013 2.25-3.341L3.63 1.993 19.74 11.46l-.861 1.868zm0 4.026-.86-1.869L3.63 22.007l16.11-9.467-2.861-4.256z" />
                                </svg>
                                <div class="store-text">
                                    <small>Get it on</small>
                                    <strong>Google Play</strong>
                                </div>
                            </a>
                            <a href="https://apps.apple.com/np/app/e-tutor-class/id6748941984" target="_blank" rel="noopener" class="btn-store btn-apple">
                                <svg class="store-icon" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z" />
                                </svg>
                                <div class="store-text">
                                    <small>Download on the</small>
                                    <strong>App Store</strong>
                                </div>
                            </a>
                        </div>

                        <!-- ── LOGIN CREDENTIALS ── -->
                        <div class="login-info-card">
                            <div class="info-header">
                                <span class="info-lock">🔑</span>
                                <strong>App Login Credentials</strong>
                            </div>
                            <div class="info-body">
                                <div class="info-row">
                                    <span class="info-label">Username</span>
                                    <span class="info-value">Your Email or Mobile Number</span>
                                </div>
                                <div class="info-divider"></div>
                                <div class="info-row">
                                    <span class="info-label">Password</span>
                                    <span class="info-value">Your Mobile Number</span>
                                </div>
                            </div>
                        </div>

                        <!-- ── CLASS SCHEDULE IMAGE ── -->
                        <div class="action-divider"><span>🗓️ Class Schedule</span></div>

                        <div class="schedule-card">
                            <img
                                src="assets/images/schedule.jpeg"
                                alt="Class Schedule"
                                class="schedule-img">

                            <div class="schedule-footer">
                                <a href="https://zoom.us/j/92024898209?pwd=Ze9jIcCMCfJoNCul3hX1YvRladvMuQ.1" target="_blank" rel="noopener" class="schedule-join-btn">
                                    🎥 Join Class
                                </a>
                                <div class="schedule-zoom-info">
                                    <div class="zoom-row">
                                        <span class="zoom-label">Meeting ID</span>
                                        <span class="zoom-value">920 2489 8209</span>
                                    </div>
                                    <div class="zoom-row">
                                        <span class="zoom-label">Password</span>
                                        <span class="zoom-value">12345</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── JOIN COMMUNITY ── -->
                        <div class="action-divider"><span>🌐 Join Our Community</span></div>
                        <!-- Full social grid -->
                        <div class="social-grid">
                            <a href="https://ig.me/j/AbbjV4Ac9zPTIJGX/" target="_blank" rel="noopener" class="social-btn s-instagram">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z" />
                                </svg>
                                <span>Instagram</span>
                            </a>
                            <div class="community-nudge">
                                <a href="https://whatsapp.com/channel/0029Vb7YxiSKLaHeXvJNiL36" target="_blank" rel="noopener" class="nudge-btn nudge-wa">
                                    <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z" />
                                    </svg>
                                    Did you join WhatsApp? →
                                </a>
                                <a href="https://www.youtube.com/@rankfactory.etutor" target="_blank" rel="noopener" class="nudge-btn nudge-yt">
                                    <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                                    </svg>
                                    Did you subscribe YouTube? →
                                </a>
                            </div>
                        </div>

                        <!-- Reminder nudge row -->


                        <div class="action-divider"><span>▶️ Latest from RankFactory</span></div>

                        <div class="yt-embed-wrap">
                            <iframe
                                src="https://www.youtube.com/embed/videoseries?list=UU-ui0-_7vSzsXhSOBmCORqg&rel=0&modestbranding=1"
                                title="RankFactory YouTube Channel"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                        </div>

                        <button type="button" id="btn-register-another" class="secondary-outline-btn">
                            Register Another Student
                        </button>


                    </div>
                <?php else: ?>

                    <!-- ── REGISTRATION FORM ── -->
                    <div class="form-card">
                        <div class="form-title">Register for Free</div>
                        <div class="form-sub">No booking required &bull; Instant access</div>

                        <?php if ($error): ?>
                            <div class="error-msg">⚠️ <?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <form method="POST" action="#register" data-register>
                            <input type="hidden" name="submit" value="1">

                            <div class="form-group">
                                <label for="name">Full Name <span class="req">*</span></label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    placeholder="Enter your full name"
                                    required
                                    value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="phone">Mobile Number <span class="req">*</span></label>
                                    <input
                                        type="tel"
                                        id="phone"
                                        name="contact"
                                        placeholder="Enter your mobile number"
                                        required
                                        value="<?= htmlspecialchars($_POST['contact'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email Address <span class="req">*</span></label>
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        placeholder="Enter your email address"
                                        required
                                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="course">Stream/Faculty</label>
                                    <select id="course" name="course">
                                        <option value="science">Class 12 | Science</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="city">District/Address</label>
                                    <input
                                        type="text"
                                        id="city"
                                        name="district"
                                        placeholder="Your district/address"
                                        value="<?= htmlspecialchars($_POST['district'] ?? '') ?>">
                                </div>
                            </div>


                            <div class="form-group">
                                <label for="institution">Institution <span class="req">*</span></label>
                                <div style="display: flex; gap: 8px;">
                                    <select id="institution" name="institution_id" required style="flex: 1;">
                                        <option value="">Select your institution</option>
                                        <?php foreach ($institutions as $inst): ?>
                                            <option value="<?= $inst['id'] ?>">
                                                <?= htmlspecialchars($inst['name']) ?> (<?= htmlspecialchars($inst['address'] ?? 'N/A') ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" id="btn-add-inst" class="small-add-btn" title="Add New Institution">+</button>
                                </div>
                            </div>


                            <div id="inst-modal" class="custom-modal" style="display:none;">
                                <div class="modal-content">
                                    <h3>Add New Institution</h3>
                                    <input type="text" id="new-inst-name" placeholder="Institution Name" class="modal-input">
                                    <input type="text" id="new-inst-address" placeholder="Address (e.g. Kathmandu)" class="modal-input">
                                    <div class="modal-actions">
                                        <button type="button" id="save-inst" class="submit-btn">Save</button>
                                        <button type="button" id="close-modal" class="cancel-btn">Cancel</button>
                                    </div>
                                </div>
                            </div>

                            <!-- ── PRE-SUBMIT STEPS ── -->
                            <div class="form-requirements">
                                <p class="req-title">✅ Complete to Unlock Registration</p>
                                <div class="req-steps">
                                    <div class="req-step" id="req-step-yt">
                                        <span class="req-check" id="step-yt">⭕</span>
                                        <span class="req-step-label">Subscribe on YouTube</span>
                                        <a href="https://www.youtube.com/@rankfactory.etutor" target="_blank" class="confirm-btn" id="confirm-yt">Subscribe ↗</a>
                                    </div>
                                    <div class="req-step" id="req-step-wa">
                                        <span class="req-check" id="step-wa">⭕</span>
                                        <span class="req-step-label">Join WhatsApp Community</span>
                                        <a href="https://whatsapp.com/channel/0029Vb7YxiSKLaHeXvJNiL36" target="_blank" class="confirm-btn confirm-btn--wa" id="confirm-wa">Join ↗</a>
                                    </div>
                                </div>
                                <div class="req-progress">
                                    <div class="req-progress-bar" id="req-progress-bar"></div>
                                </div>
                            </div>

                            <button type="submit" id="main-submit-btn" class="submit-btn submit-btn--locked" disabled>
                                🔒 Complete Steps Above to Enroll
                            </button>
                        </form>

                        <p class="form-note">🔒 Your data is 100% secure. No spam, ever.</p>
                    </div>
                <?php endif; ?>

            </div><!-- #register -->
        </div><!-- .hero-inner -->
    </section>


    <!-- ══════════════════════════════════════════════════════
     FEATURES
    ══════════════════════════════════════════════════════════ -->
    <!-- <section class="features"> 
        <div class="features-inner">
            <div class="section-label reveal">Why RankFactory</div>
            <div class="section-title reveal">Everything you need to crack the exam</div>

            <div class="features-grid">
                <div class="feature-card reveal">
                    <span class="feature-icon">🎯</span>
                    <div class="feature-name">Topic-wise Mock Tests</div>
                    <p class="feature-desc">Thousands of practice questions curated by ex-government officers and top rankers for every section.</p>
                </div>
                <div class="feature-card reveal">
                    <span class="feature-icon">📊</span>
                    <div class="feature-name">AI Performance Analytics</div>
                    <p class="feature-desc">Smart insights that pinpoint your weak areas and build personalised study plans that actually work.</p>
                </div>
                <div class="feature-card reveal">
                    <span class="feature-icon">📹</span>
                    <div class="feature-name">Live Interactive Classes</div>
                    <p class="feature-desc">Real-time sessions with top educators. Ask questions, get answers, stay accountable — together.</p>
                </div>
                <div class="feature-card reveal">
                    <span class="feature-icon">📱</span>
                    <div class="feature-name">Study Anywhere, Anytime</div>
                    <p class="feature-desc">Our mobile app lets you download lessons and practice offline — no internet needed on the go.</p>
                </div>
                <div class="feature-card reveal">
                    <span class="feature-icon">💬</span>
                    <div class="feature-name">WhatsApp Community</div>
                    <p class="feature-desc">50,000+ active students sharing tips, current affairs, and motivation every single day.</p>
                </div>
                <div class="feature-card reveal">
                    <span class="feature-icon">🏆</span>
                    <div class="feature-name">All-Nepal Rankings</div>
                    <p class="feature-desc">Compete with lakhs of aspirants in full-length mock exams and see exactly where you stand.</p>
                </div>
            </div>
        </div>
    </section> -->


    <!-- ══════════════════════════════════════════════════════
     FOOTER
    ═══════════════════════════════════════════════════════════ -->
    <footer>
        <p>© <?= date('Y') ?> RankFactory by <a href="https://etutorclass.com" target="_blank" rel="noopener">eTutorClass</a> — Empowering Aspirants Across Nepal</p>
        <p style="margin-top: 8px; font-size: 12px;">
            <a href="https://etutorclass.com/privacy">Privacy Policy</a> &nbsp;·&nbsp;
            <a href="https://etutorclass.com/terms">Terms of Service</a> &nbsp;·&nbsp;
            <a href="https://etutorclass.com/contact">Contact Us</a>
        </p>
    </footer>

    <!-- JavaScript (loaded last for performance) -->
    <script>
        // Immediate check before page renders
        if (localStorage.getItem('rf_registered') === 'true') {
            document.documentElement.classList.add('hide-form-initially');
        }
    </script>
    <script src="assets/js/main.js"></script>


</body>

</html>