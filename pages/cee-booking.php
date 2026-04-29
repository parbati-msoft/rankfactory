<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RankFactory — CEE Preparation & Class Booking in Nepal</title>

    <meta name="description" content="RankFactory by eTutorClass offers expert CEE (MBBS/BDS Entrance) preparation in Nepal. Book online classes, access live lectures, chapter-wise notes, mock tests, and performance tracking to secure top ranks in medical entrance exams.">

    <meta name="keywords" content="CEE preparation Nepal, MBBS entrance Nepal, BDS entrance exam Nepal, medical entrance coaching Nepal, CEE classes booking, online CEE coaching Nepal, physics chemistry biology entrance prep, Nepal medical entrance guide">

    <link rel="icon" type="image/png" href="assets/images/favicon.ico" sizes="32x32">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">

    <meta property="og:title" content="CEE Preparation Classes in Nepal | RankFactory">
    <meta property="og:description" content="Join RankFactory for top CEE entrance preparation. Book classes, attend live sessions, and crack MBBS/BDS entrance exams in Nepal.">
    <meta property="og:type" content="website">
    <meta property="og:image" content="assets/images/preview.jpg">

    <meta name="robots" content="index, follow">
    <!-- Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/cee-booking.css">
</head>

<body>

    <!-- HEADER -->
    <header>
        <div class="header-inner">
            <a href="./" class="logo">
                <img src="assets/images/logo.png" alt="RankFactory Logo">
            </a>
            <a href="cee-booking" class="header-cta">CEE Pre-booking →</a>
        </div>
    </header>

    <!-- HERO BANNER -->
    <section class="cee-hero">
        <div class="cee-hero-bg"></div>
        <div class="cee-hero-inner">
            <div class="cee-pill"><span class="pill-dot"></span> Limited Seats Available</div>
            <h1 class="cee-hero-title">CEE Preparation<br><span class="accent">Pre-Booking</span></h1>
            <p class="cee-hero-sub">Secure your seat now for Nepal's most comprehensive CEE preparation course. Online + Physical options available.</p>
        </div>
    </section>

    <!-- MAIN CONTENT -->
    <main class="cee-main">
        <div class="cee-container">

            <!-- ══ STEP 1: REGISTRATION ══ -->
            <div class="cee-step" id="step-registration">
                <!-- <div class="step-badge">Step 1</div>
                <h2 class="step-title">Student Registration</h2>
                <p class="step-sub">Fill in your details to begin the booking process</p> -->

                <div id="reg-error" class="cee-alert cee-alert--error" style="display:none"></div>

                <form id="reg-form" class="cee-form">
                    <h3>New Registration</h3>
                    <div class="cee-form-row">
                        <div class="cee-field">
                            <label for="reg-name">Full Name <span class="req">*</span></label>
                            <input type="text" id="reg-name" placeholder="Your full name" required>
                        </div>
                        <div class="cee-field">
                            <label for="password">Password <span class="req">*</span></label>
                            <input type="password" id="password" placeholder="Create a password" required>
                        </div>
                    </div>
                    <div class="cee-form-row">
                        <div class="cee-field">
                            <label for="reg-phone">Mobile Number <span class="req">*</span></label>
                            <input type="tel" id="reg-phone" placeholder="98XXXXXXXX" required>
                        </div>
                        <div class="cee-field">
                            <label for="reg-email">Email Address <span class="req">*</span></label>
                            <input type="email" id="reg-email" placeholder="you@email.com" required>
                        </div>
                    </div>
                    <div class="cee-field">
                        <label for="reg-address">Address <span class="req">*</span></label>
                        <input type="text" id="reg-address" placeholder="District, Province" required>
                    </div>
                    <button type="submit" class="cee-btn cee-btn--primary" id="reg-submit-btn">
                        <span>Continue to Booking</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                    </button>

                    <p>Already registered? <a href="#" id="show-login">Login here</a></p>
                </form>

                <form id="login-form" class="cee-form" style="display: none;">
                    <h3>Student Login</h3>
                    <div class="cee-field">
                        <label>Email / Phone</label>
                        <input type="text" id="login-id" required>
                    </div>
                    <div class="cee-field">
                        <label>Password</label>
                        <input type="password" id="login-password" required>
                    </div>
                    <button type="submit" class="cee-btn cee-btn--primary">Login & Continue</button>
                    <p>New student? <a href="#" id="show-reg">Register here</a></p>
                </form>
            </div>

            <!-- ══ STEP 2: COURSE SELECTION + BOOKING ══ -->
            <div class="cee-step" id="step-booking" style="display:none">
                <div class="step-badge">Step 2</div>
                <h2 class="step-title">Course Selection</h2>
                <p class="step-sub">Choose your preferred mode of study</p>

                <!-- Checkboxes -->
                <div class="mode-toggles">
                    <label class="mode-toggle" id="toggle-online">
                        <input type="checkbox" id="chk-online" checked>
                        <div class="mode-toggle-card">
                            <span class="mode-icon">💻</span>
                            <span class="mode-label">Online</span>
                            <span class="mode-check">✓</span>
                        </div>
                    </label>
                    <label class="mode-toggle" id="toggle-physical">
                        <input type="checkbox" id="chk-physical">
                        <div class="mode-toggle-card">
                            <span class="mode-icon">🏫</span>
                            <span class="mode-label">Physical</span>
                            <span class="mode-check">✓</span>
                        </div>
                    </label>
                </div>

                <!-- Loading state -->
                <div id="batch-loading" class="batch-loading">
                    <div class="loading-spinner"></div>
                    <span>Fetching course details...</span>
                </div>

                <!-- Course cards -->
                <div id="batch-cards" class="batch-cards"></div>

                <!-- Summary & Pre-booking amount -->
                <div class="booking-summary" id="booking-summary" style="display:none">
                    <div class="summary-header">
                        <span class="summary-icon">🧾</span>
                        <strong>Booking Summary</strong>
                    </div>
                    <div class="summary-rows" id="summary-rows"></div>
                    <div class="summary-divider"></div>
                    <div class="summary-total-row">
                        <span>Total Course Fee</span>
                        <span class="summary-total" id="summary-total">Rs. 0</span>
                    </div>

                    <div class="prebooking-field">
                        <label for="prebooking-amount">
                            Pre-booking Amount
                            <span class="prebooking-hint">Min. Rs. 999</span>
                        </label>
                        <div class="prebooking-input-wrap">
                            <span class="prebooking-prefix">Rs.</span>
                            <input type="number" id="prebooking-amount" value="999" min="999" step="1">
                        </div>
                        <p class="prebooking-note">💡 You can pay more to reduce your remaining balance later.</p>
                    </div>

                    <div id="booking-error" class="cee-alert cee-alert--error" style="display:none"></div>

                    <button class="cee-btn cee-btn--gold" id="booking-submit-btn">
                        <span>Continue Pre-Booking</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="cee-step" id="step-payment" style="display:none">
                <div class="step-badge">Step 3</div>
                <h2 class="step-title">Secure Payment</h2>
                <p class="step-sub">Choose your preferred payment method to finalize pre-booking</p>

                <div class="payment-methods">
                    <div class="pay-tabs">
                        <button type="button" class="pay-tab active" data-target="pay-direct">
                            <span class="pay-tab-icon">💳</span>
                            Direct Esewa
                        </button>
                        <button type="button" class="pay-tab" data-target="pay-manual">
                            <span class="pay-tab-icon">📷</span>
                            QR Scan / Upload
                        </button>
                    </div>

                    <div id="pay-direct" class="pay-content">
                        <div class="pay-info-box">
                            <img src="assets/images/esewa-logo.png" alt="eSewa" class="pay-logo">
                            <p>You will be redirected to the secure eSewa portal to pay <strong>Rs. <span class="display-amount">999</span></strong>.</p>
                            <ul class="pay-features">
                                <li>✓ Instant Verification</li>
                                <li>✓ Automatic Enrollment</li>
                            </ul>
                        </div>
                        <button type="button" class="cee-btn cee-btn--gold" id="btn-esewa-direct">
                            Pay with eSewa App
                        </button>
                    </div>

                    <div id="pay-manual" class="pay-content" style="display:none">
                        <div class="qr-container">
                            <div class="qr-image">
                                <img src="assets/images/payment-qr.png" alt="Merchant QR Code">
                            </div>
                            <div class="qr-details">
                                <p><strong>Name:</strong> RankFactory (eTutorClass)</p>
                                <p><strong>Esewa ID:</strong> 9857084809</p>
                                <p class="qr-note">Scan this QR, pay <strong>Rs. <span class="display-amount">999</span></strong>, and upload the screenshot below.</p>
                            </div>
                        </div>

                        <div class="cee-field">
                            <label>Upload Payment Screenshot <span class="req">*</span></label>
                            <div class="file-upload-wrapper">
                                <input type="file" id="payment-screenshot" accept="image/*">
                                <div class="file-dummy">
                                    <span class="file-icon">📁</span>
                                    <span class="file-text">Click to browse or drag screenshot here</span>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="cee-btn cee-btn--primary" id="btn-manual-submit">
                            Confirm Booking
                        </button>
                    </div>
                </div>
            </div>

            <div id="partial-success-notice" class="cee-alert cee-alert--info" style="display:none; background: #f0f7ff; border: 1px solid #cce3ff; border-left: 5px solid #2196f3; padding: 15px; margin-bottom: 20px; border-radius: 8px;">
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 1.4em;">✅</span>
                        <div>
                            <strong id="partial-title" style="display: block; color: #0d47a1; font-size: 1.1em;">Status</strong>
                            <p id="partial-desc" style="margin: 0; font-size: 0.95em; color: #1565c0;"></p>
                        </div>
                    </div>

                    <div style="background: white; padding: 12px; border-radius: 6px; border: 1px dashed #2196f3;">
                        <p style="margin: 0 0 10px 0; font-size: 0.85em; font-weight: bold; color: #333;">Access your account using your credentials here,</p>

                        <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
                            <a href="https://etutorclass.com/login" class="cee-btn" style="background: #2196f3; color: white; padding: 6px 15px; font-size: 0.85em; text-decoration: none; border-radius: 4px;">
                                Login to Study
                            </a>

                            <div style="display: flex; gap: 8px; align-items: center; border-left: 1px solid #ddd; padding-left: 10px;">
                                <span style="font-size: 0.75em; color: #666;">Get App:</span>
                                <a href="https://etutorclass.com/app-download" target="_blank">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Play Store" style="height: 24px;">
                                </a>
                                <a href="https://apps.apple.com/np/app/e-tutor-class/id6748941984" target="_blank">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/3/3c/Download_on_the_App_Store_Badge.svg" alt="App Store" style="height: 24px;">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══ STEP 3: SUCCESS ══ -->
            <div class="cee-step" id="step-success" style="display:none">
                <div class="booking-success">
                    <div class="booking-success-icon">🎓</div>
                    <h2 class="booking-success-title">Pre-Booking Confirmed!</h2>
                    <p class="booking-success-sub">Your seat has been reserved. You can now access your course materials, live classes, and MCQs.</p>

                    <div class="booking-success-detail" id="booking-success-detail"></div>

                    <a href="https://etutorclass.com/login" class="cee-btn cee-btn--gold" style="margin-bottom: 20px; width: 100%; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <span>💻 Login to Access Classes & MCQs</span>
                    </a>

                    <div class="app-download-section" style="margin: 25px 0; padding-top: 20px; border-top: 1px solid #eee;">
                        <p style="font-size: 0.9em; color: #666; margin-bottom: 15px;">Download our mobile app for the best experience:</p>
                        <div class="app-buttons" style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                            <a href="https://etutorclass.com/app-download" target="_blank" class="app-link">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Get it on Google Play" style="height: 40px;">
                            </a>
                            <a href="https://apps.apple.com/np/app/e-tutor-class/id6748941984" target="_blank" class="app-link">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/3/3c/Download_on_the_App_Store_Badge.svg" alt="Download on the App Store" style="height: 40px;">
                            </a>
                        </div>
                    </div>

                    <a href="cee-booking" class="cee-btn cee-btn--outline" style="width: 100%;">← Back to Home</a>
                </div>
            </div>

        </div>
    </main>

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

    <script>
        // Immediate check before page renders
        if (localStorage.getItem('rf_registered') === 'true') {
            document.documentElement.classList.add('hide-form-initially');
        }

        window.APP_CONFIG = {
            baseUrl: '<?= API_BASE_URL ?>',
            esewa_url: '<?= $_ENV['ESEWA_URL'] ?? '' ?>'
        };
    </script>

    <script src="assets/js/cee-booking.js"></script>
</body>

</html>