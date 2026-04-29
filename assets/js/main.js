/**
 * assets/js/main.js
 * RankFactory — UI interactions & animations
 */

document.addEventListener('DOMContentLoaded', async () => {

    // ── 1. GLOBAL UI & ANIMATIONS ──────────────────────────────────────────
    
    // Scroll Reveal Logic
    const revealEls = document.querySelectorAll('.reveal');
    if (revealEls.length > 0) {
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, i) => {
                if (entry.isIntersecting) {
                    setTimeout(() => entry.target.classList.add('visible'), i * 80);
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        revealEls.forEach(el => revealObserver.observe(el));
    }

    // Feature Card Stagger
    document.querySelectorAll('.feature-card').forEach((card, i) => {
        card.style.transitionDelay = `${i * 0.07}s`;
    });

    // Smooth Anchor Scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', (e) => {
            const href = anchor.getAttribute('href');
            if (href === '#') return;
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });


    // ── 2. FORM & REGISTRATION LOGIC ───────────────────────────────────────

    // Loading State for Registration Form
    const regForm = document.querySelector('form[data-register]');
    const submitBtn = regForm?.querySelector('.submit-btn');
    if (regForm && submitBtn) {
        regForm.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation: spin 0.8s linear infinite;"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg> Submitting...`;
        });
    }

    // Registration Persistence & Success View
    if (localStorage.getItem('rf_registered') === 'true') {
        const formCard = document.querySelector('.form-card');
        const successCard = document.querySelector('.success-card');
        
        if (formCard && successCard) {
            formCard.style.display = 'none';
            successCard.style.display = 'block';
        } else if (!window.location.search.includes('success')) {
            // Auto-redirect if registered but not on success view
            window.location.href = window.location.pathname + '?success=1#register';
        }
    }

    // Register Another User Logic
    document.getElementById('btn-register-another')?.addEventListener('click', () => {
        localStorage.removeItem('rf_registered');
        window.location.href = window.location.pathname + '?reset=1';
    });


    // ── 3. MODAL: ADD INSTITUTION ───────────────────────────────────────────
    
    const modal = document.getElementById('inst-modal');
    const openModalBtn = document.getElementById('btn-add-inst');
    const closeModalBtn = document.getElementById('close-modal');
    const saveInstBtn = document.getElementById('save-inst');
    const instSelect = document.getElementById('institution');

    if (modal && openModalBtn) {
        openModalBtn.onclick = () => modal.style.display = 'flex';
        closeModalBtn.onclick = () => modal.style.display = 'none';

        saveInstBtn.onclick = async function() {
            const name = document.getElementById('new-inst-name')?.value;
            const address = document.getElementById('new-inst-address')?.value;

            if(!name || !address) return alert("Please fill name and address");

            saveInstBtn.disabled = true;
            saveInstBtn.innerText = "Saving...";

            try {
                const response = await fetch('includes/institution_proxy.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, address })
                });
                const data = await response.json();

                if(data.id) {
                    instSelect.add(new Option(data.name, data.id, true, true));
                    modal.style.display = 'none';
                    document.getElementById('new-inst-name').value = '';
                    document.getElementById('new-inst-address').value = '';
                } else {
                    alert("Error: " + (data.error || "Could not save"));
                }
            } catch (err) { alert("Connection error"); } 
            finally {
                saveInstBtn.disabled = false;
                saveInstBtn.innerText = "Save";
            }
        };
    }


    // ── 4. SOCIAL CONFIRMATION (YouTube/WhatsApp) ───────────────────────────

    let ytDone = false;
    let waDone = false;
    const mainSubmitBtn = document.getElementById('main-submit-btn');
    const progressBar = document.getElementById('req-progress-bar');

    function markDone(key) {
        if (key === 'yt') ytDone = true;
        if (key === 'wa') waDone = true;

        const stepEl = document.getElementById('step-' + key);
        const reqStepEl = document.getElementById('req-step-' + key);
        const confirmBtn = document.getElementById('confirm-' + key);

        if (stepEl) { stepEl.textContent = '✅'; stepEl.classList.add('checked'); }
        if (reqStepEl) reqStepEl.classList.add('is-done');
        if (confirmBtn) { confirmBtn.textContent = 'Done ✓'; confirmBtn.classList.add('is-done'); }

        if (progressBar) progressBar.style.width = ((ytDone ? 1 : 0) + (waDone ? 1 : 0)) * 50 + '%';

        if (ytDone && waDone && mainSubmitBtn) {
            mainSubmitBtn.disabled = false;
            mainSubmitBtn.style.opacity = '1';
            mainSubmitBtn.style.cursor = 'pointer';
            mainSubmitBtn.classList.replace('submit-btn--locked', 'submit-btn--unlocked');
            mainSubmitBtn.innerHTML = '🚀 Start Free Enrollment <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
        }
    }

    document.getElementById('confirm-yt')?.addEventListener('click', function() {
        if (ytDone) return;
        const handler = () => { if (document.visibilityState === 'visible') { markDone('yt'); document.removeEventListener('visibilitychange', handler); }};
        document.addEventListener('visibilitychange', handler);
    });

    document.getElementById('confirm-wa')?.addEventListener('click', function() {
        if (waDone) return;
        const handler = () => { if (document.visibilityState === 'visible') { markDone('wa'); document.removeEventListener('visibilitychange', handler); }};
        document.addEventListener('visibilitychange', handler);
    });


    // ── 5. MAIN SLIDER ─────────────────────────────────────────────────────

    const slider = document.querySelector('.main-slider');
    if (slider) {
        const slides = slider.querySelectorAll('.slide');
        if (slides.length > 1) {
            let current = 0;
            let timer;

            slider.insertAdjacentHTML('beforeend', `<button class="slider-arrow prev" aria-label="Previous">&#8592;</button><button class="slider-arrow next" aria-label="Next">&#8594;</button><div class="slider-dots"></div>`);
            const dotsWrap = slider.querySelector('.slider-dots');
            
            slides.forEach((_, i) => {
                const dot = document.createElement('button');
                dot.className = 'slider-dot' + (i === 0 ? ' active' : '');
                dot.addEventListener('click', () => goTo(i));
                dotsWrap.appendChild(dot);
            });

            function goTo(index) {
                slides[current].classList.remove('active');
                dotsWrap.children[current].classList.remove('active');
                current = (index + slides.length) % slides.length;
                slides[current].classList.add('active');
                dotsWrap.children[current].classList.add('active');
                resetTimer();
            }

            function resetTimer() { clearInterval(timer); timer = setInterval(() => goTo(current + 1), 4000); }

            slides[0].classList.add('active');
            slider.querySelector('.prev').addEventListener('click', () => goTo(current - 1));
            slider.querySelector('.next').addEventListener('click', () => goTo(current + 1));
            slider.addEventListener('mouseenter', () => clearInterval(timer));
            slider.addEventListener('mouseleave', resetTimer);
            resetTimer();
        } else if (slides.length === 1) {
            slides[0].classList.add('active');
        }
    }

});

// ── 6. DYNAMIC CONTENT LOADING (Exposed for HTML onclick) ──────────────────

const BASE_URL = window.APP_CONFIG?.baseUrl || '';

async function loadUnits(batchId, btn) {
    document.querySelectorAll('.batch-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    document.querySelectorAll('.unit-item.is-open').forEach(item => {
        item.classList.remove('is-open');
        item.querySelector('.notes-list').style.display = 'none';
        const toggleBtn = item.querySelector('.unit-toggle-btn');
        if (toggleBtn) toggleBtn.textContent = 'View Notes';
    });

    const container = document.getElementById('units-container');
    if (!container) return;
    container.innerHTML = '<div class="notes-state loading">⏳ Loading units...</div>';

    try {
        const response = await fetch(`${BASE_URL}/free-units/${batchId}`);
        const data = await response.json();

        if (data.status === 'success' && data.classUnits.length > 0) {
            let html = '<div class="units-list">';
            data.classUnits.forEach(unit => {
                html += `
                    <div class="unit-item" id="unit-wrap-${unit.id}">
                        <div class="unit-header">
                            <span class="unit-name">📘 ${unit.name}</span>
                            <button class="unit-toggle-btn" onclick="loadNotes(${unit.id}, this)">View Notes</button>
                        </div>
                        <div id="notes-unit-${unit.id}" class="notes-list" style="display:none;"></div>
                    </div>`;
            });
            container.innerHTML = html + '</div>';
        } else {
            container.innerHTML = '<div class="notes-state empty">📭 No subjects found.</div>';
        }
    } catch (e) { container.innerHTML = '<div class="notes-state error">⚠️ Error loading subjects.</div>'; }
}

async function loadNotes(unitId, btn) {
    const noteDiv = document.getElementById(`notes-unit-${unitId}`);
    const unitWrap = document.getElementById(`unit-wrap-${unitId}`);
    if (!noteDiv || !unitWrap) return;

    if (noteDiv.dataset.loaded === 'true') {
        const isOpen = noteDiv.style.display === 'block';
        noteDiv.style.display = isOpen ? 'none' : 'block';
        btn.textContent = isOpen ? 'View Notes' : 'Hide Notes';
        unitWrap.classList.toggle('is-open', !isOpen);
        return;
    }

    noteDiv.innerHTML = '<div class="notes-state loading">⏳ Loading notes...</div>';
    noteDiv.style.display = 'block';
    unitWrap.classList.add('is-open');
    btn.textContent = 'Hide Notes';

    try {
        const response = await fetch(`${BASE_URL}/free-notes/${unitId}`);
        const data = await response.json();

        if (data.status === 'success' && data.notes.length > 0) {
            noteDiv.innerHTML = data.notes.map(n => `
                <a href="${n.link}" target="_blank" class="note-link">
                    <span class="note-link-name">📄 ${n.name}</span>
                    <span class="note-badge">Open</span>
                </a>`).join('');
        } else {
            noteDiv.innerHTML = '<div class="notes-state empty">📭 No notes available yet.</div>';
        }
        noteDiv.dataset.loaded = 'true';
    } catch (e) { noteDiv.innerHTML = '<div class="notes-state error">⚠️ Error loading notes.</div>'; }
}