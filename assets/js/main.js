/**
 * assets/js/main.js
 * RankFactory — UI interactions & animations
 */

document.addEventListener('DOMContentLoaded', () => {

    // ── Scroll Reveal ─────────────────────────────────────────────────────────
    const revealEls = document.querySelectorAll('.reveal');

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                // Stagger each card slightly as they appear
                setTimeout(() => {
                    entry.target.classList.add('visible');
                }, i * 80);
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    revealEls.forEach(el => revealObserver.observe(el));


    // ── Feature Card Stagger ──────────────────────────────────────────────────
    document.querySelectorAll('.feature-card').forEach((card, i) => {
        card.style.transitionDelay = `${i * 0.07}s`;
    });


    // ── Form Submit Loading State ─────────────────────────────────────────────
    const form = document.querySelector('form[data-register]');
    const submitBtn = form ? form.querySelector('.submit-btn') : null;

    if (form && submitBtn) {
        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     style="animation: spin 0.8s linear infinite;">
                    <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                </svg>
                Submitting...
            `;
        });
    }


    // ── Smooth Anchor Scroll ──────────────────────────────────────────────────
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', (e) => {
            const target = document.querySelector(anchor.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

});

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('inst-modal');
    const openBtn = document.getElementById('btn-add-inst');
    const closeBtn = document.getElementById('close-modal');
    const saveBtn = document.getElementById('save-inst');
    const instSelect = document.getElementById('institution');

    // Open Modal
    openBtn.onclick = () => modal.style.display = 'flex';
    
    // Close Modal
    closeBtn.onclick = () => modal.style.display = 'none';

    // Save Institution via AJAX
    saveBtn.onclick = async function() {
        const name = document.getElementById('new-inst-name').value;
        const address = document.getElementById('new-inst-address').value;

        if(!name || !address) return alert("Please fill name and address");

        saveBtn.disabled = true;
        saveBtn.innerText = "Saving...";

        try {
            // We call a small PHP helper or the same api.php with an action
            const response = await fetch('includes/institution_proxy.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, address })
            });

            const data = await response.json();

            if(data.id) {
                // 1. Add new option to select
                const newOpt = new Option(data.name, data.id, true, true);
                instSelect.add(newOpt);
                
                // 2. Close modal & reset
                modal.style.display = 'none';
                document.getElementById('new-inst-name').value = '';
                document.getElementById('new-inst-address').value = '';
            } else {
                alert("Error: " + (data.error || "Could not save"));
            }
        } catch (err) {
            alert("Connection error");
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerText = "Save";
        }
    };
});

document.addEventListener('DOMContentLoaded', function() {
    const registerSection = document.getElementById('register');
    
    // Check if user is already registered in this browser
    if (localStorage.getItem('rf_registered') === 'true') {
        showSuccessCard();
    }

    // Function to hide form and show success UI
    function showSuccessCard() {
        // We use AJAX/Fetch to get the success HTML or simply toggle visibility
        // If your Success Card is already in the HTML but hidden:
        const formCard = document.querySelector('.form-card');
        const successCard = document.querySelector('.success-card');
        
        if (formCard && successCard) {
            formCard.style.display = 'none';
            successCard.style.display = 'block';
            // Also ensure the "Success" state variables are handled
        } else {
            // If the success card isn't in the DOM yet, we can trigger a page reload 
            // if the PHP session is already set, or redirect.
            // But usually, a simple refresh works best if PHP handles the logic:
            if (!window.location.hash.includes('success')) {
                // Optional: You can force a redirect or just hide the form via CSS
                document.body.classList.add('user-is-registered');
            }
        }
    }
});

document.addEventListener('DOMContentLoaded', function () {
    let ytDone = false;
    let waDone = false;
    const submitBtn    = document.getElementById('main-submit-btn');
    const progressBar  = document.getElementById('req-progress-bar');

    function markDone(key) {
        if (key === 'yt') ytDone = true;
        if (key === 'wa') waDone = true;

        document.getElementById('step-' + key).textContent = '✅';
        document.getElementById('step-' + key).classList.add('checked');
        document.getElementById('req-step-' + key).classList.add('is-done');
        document.getElementById('confirm-' + key).textContent = 'Done ✓';
        document.getElementById('confirm-' + key).classList.add('is-done');

        if (progressBar) progressBar.style.width = ((ytDone ? 1 : 0) + (waDone ? 1 : 0)) * 50 + '%';

        if (ytDone && waDone) {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
            submitBtn.classList.replace('submit-btn--locked', 'submit-btn--unlocked');
            submitBtn.innerHTML = '🚀 Start Free Enrollment <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
        }
    }

    // YouTube — opens new tab, marks done on tab return
    document.getElementById('confirm-yt').addEventListener('click', function () {
        if (ytDone) return;
        document.addEventListener('visibilitychange', function handler() {
            if (document.visibilityState === 'visible') {
                markDone('yt');
                document.removeEventListener('visibilitychange', handler);
            }
        });
    });

    // WhatsApp — opens new tab, marks done on tab return
    document.getElementById('confirm-wa').addEventListener('click', function () {
        if (waDone) return;
        document.addEventListener('visibilitychange', function handler() {
            if (document.visibilityState === 'visible') {
                markDone('wa');
                document.removeEventListener('visibilitychange', handler);
            }
        });
    });

    // Scroll reveal (existing)
    document.querySelectorAll('.reveal').forEach(el =>
        new IntersectionObserver((entries, obs) => {
            entries.forEach((e, i) => {
                if (e.isIntersecting) {
                    setTimeout(() => e.target.classList.add('visible'), i * 80);
                    obs.unobserve(e.target);
                }
            });
        }, { threshold: 0.12 }).observe(el)
    );

    // Feature card stagger (existing)
    document.querySelectorAll('.feature-card').forEach((card, i) => {
        card.style.transitionDelay = `${i * 0.07}s`;
    });
});

document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Initial Check: If LocalStorage says registered, but URL doesn't show success
    // Redirect them to the success view automatically
    if (localStorage.getItem('rf_registered') === 'true' && !window.location.search.includes('success')) {
        window.location.href = window.location.pathname + '?success=1#register';
    }

    // 2. Register Another Logic
    const regAnotherBtn = document.getElementById('btn-register-another');
    if (regAnotherBtn) {
        regAnotherBtn.addEventListener('click', function() {
            localStorage.removeItem('rf_registered');
            // Redirect to the reset handler we added to api.php earlier
            window.location.href = window.location.pathname + '?reset=1';
        });
    }
});