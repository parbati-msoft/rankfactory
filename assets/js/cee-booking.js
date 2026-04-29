(function () {
    let userBookings = { online: [], physical: [] };
    let selectedBatchId = null;   // For Online
    let selectedPhysicalId = null; // For Physical
    let selectedOnlinePrice = 0;
    let selectedPhysicalPrice = 0;

    async function fetchUserBookings() {
        try {
            const res = await fetch('ajax-handler.php?action=get-user-bookings');
            const data = await res.json();
            // Data expected: { online: [...], physical: [...] }
            userBookings = data;
        } catch (e) {
            console.error("Failed to sync bookings:", e);
        }
    }

    async function checkBookingStatus(id, type) {
        if (!id) return false;
        
        let isBooked = false;
        if (type === 'online') {
            isBooked = Array.isArray(userBookings.online) && 
            userBookings.online.some(b => parseInt(b.batch_id) === parseInt(id));
        } else {
            isBooked = Array.isArray(userBookings.physical) && 
            userBookings.physical.some(b => parseInt(b.class_id) === parseInt(id));
        }

        // Update the card badge visually
        const badge = document.getElementById(`status-badge-${id}`);
        if (badge) {
            badge.innerHTML = isBooked ? '<span class="badge-success">✓ Already Enrolled</span>' : '';
        }
        return isBooked;
    }

    document.addEventListener('DOMContentLoaded', async () => {
        const rawSearch = window.location.search.replace(/\?/g, '&').replace('&', '?');
        const ceeUrlParams = new URLSearchParams(rawSearch);
        
        const ceeStatus = ceeUrlParams.get('status');
        const ceeEncodedData = ceeUrlParams.get('data');

        await fetchUserBookings();

        // ══ DOM ELEMENTS ══
        const stepReg = document.getElementById('step-registration');
        const stepBooking = document.getElementById('step-booking');
        const stepPayment = document.getElementById('step-payment');
        const stepSuccess = document.getElementById('step-success');

        const regForm = document.getElementById('reg-form');
        const loginForm = document.getElementById('login-form');
        
        const chkOnline = document.getElementById('chk-online');
        const chkPhysical = document.getElementById('chk-physical');
        const batchCards = document.getElementById('batch-cards');
        const summaryRows = document.getElementById('summary-rows');
        const summaryTotal = document.getElementById('summary-total');
        const prebookingAmountInput = document.getElementById('prebooking-amount');
        const submitBtn = document.getElementById('booking-submit-btn');

        // ══ 1. INITIAL LOAD & ESEWA RETURN ══
        if (ceeStatus === 'success' && ceeEncodedData) {
            verifyAndSaveBooking(ceeEncodedData);
        } else if (localStorage.getItem('rf_registered') === 'true') {
            const userId = await refreshUserId();
            if (userId) showStep(stepBooking);
        }

        // 2. Define the verification function
        async function verifyAndSaveBooking(data) {
            try {
                const decoded = JSON.parse(atob(data));

                const payload = {
                    transaction_id: decoded.transaction_code,
                    amount: decoded.total_amount.toString().replace(/,/g, ''),
                    // batch_id: ceeUrlParams.get('bid'),
                    method: 'direct',
                    payment_mode: 'Esewa_Direct',
                    online_batch_id: ceeUrlParams.get('bid'),
                    physical_class_id: ceeUrlParams.get('pid'),
                    online_price: ceeUrlParams.get('op'),
                    physical_price: ceeUrlParams.get('pp')
                };
                
                const response = await fetch('ajax-handler.php?action=process-cee-booking', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();
                
                if (result.success) {
                    window.history.replaceState({}, document.title, window.location.pathname);
                    showStep(stepSuccess);
                }
            } catch (err) {
                console.error("Fetch error:", err);
            }
        }

        // Toggle Login/Reg
        document.getElementById('show-login')?.addEventListener('click', (e) => {
            e.preventDefault();
            if (regForm && loginForm) {
                regForm.style.display = 'none';
                loginForm.style.display = 'flex';
            }
        });

        document.getElementById('show-reg')?.addEventListener('click', (e) => {
            e.preventDefault();
            if (regForm && loginForm) {
                loginForm.style.display = 'none';
                regForm.style.display = 'flex';
            }
        });

        // Handle Login Submit
        loginForm?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = e.target.querySelector('button');
            const payload = {
                identity: document.getElementById('login-id').value,
                password: document.getElementById('login-password').value
            };

            try {
                btn.disabled = true;
                btn.innerText = "Authenticating...";
                const res = await fetch('ajax-handler.php?action=login-cee', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await res.json();
                if (result.success) {
                    localStorage.setItem('rf_registered', 'true');
                    await refreshUserId();
                    showStep(stepBooking);
                } else { alert("Login failed."); }
            } catch (error) { alert("Login error."); }
            finally { btn.disabled = false; btn.innerText = "Login & Continue"; }
        });

        // Handle Registration Submit
        regForm?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = {
                name: document.getElementById('reg-name').value,
                password: document.getElementById('password').value,
                email: document.getElementById('reg-email').value,
                contact: document.getElementById('reg-phone').value,
                district: document.getElementById('reg-address').value
            };

            const res = await fetch('ajax-handler.php?action=register-cee', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });

            const result = await res.json();
            if (result.success) {
                localStorage.setItem('rf_registered', 'true');
                await refreshUserId();
                showStep(stepBooking);
            }
        });

        // ══ 3. BATCH & BOOKING UI ══
        if (chkOnline || chkPhysical) {
            [chkOnline, chkPhysical].forEach(input => {
                input?.addEventListener('change', updateBookingUI);
            });
        }

        async function updateBookingUI() {
            const batchLoading = document.getElementById('batch-loading');
            const bookingSummary = document.getElementById('booking-summary');
            const submitBtn = document.getElementById('booking-submit-btn');
            const prebookingField = document.querySelector('.prebooking-field');

            batchLoading.style.display = 'flex';
            batchCards.innerHTML = '';
            summaryRows.innerHTML = '';
            let totalFee = 0;
            
            // We will track if the "Current Selection" is already booked
            let isOnlineBooked = false;
            let isPhysicalBooked = false;
            selectedOnlinePrice = 0;
            selectedPhysicalPrice = 0;

            // 1. Handle Online Selection
            if (chkOnline.checked) {
                const data = await fetchBatchData('online');
                if (data) {
                    renderCard(data, 'online');
                    renderSummaryRow(data.name, data.fee);
                    totalFee += parseFloat(data.fee - data.discount);
                    selectedOnlinePrice = parseFloat(data.fee - data.discount || 0);
                    selectedBatchId = data.id; 
                    
                    // Check enrollment specifically for this online batch
                    isOnlineBooked = await checkBookingStatus(data.id, 'online');
                }
            } else { selectedBatchId = null; }

            // 2. Handle Physical Selection
            if (chkPhysical.checked) {
                const data = await fetchBatchData('physical');
                if (data) {
                    renderCard(data, 'physical');
                    renderSummaryRow(data.name, data.price);
                    totalFee += parseFloat(data.price);
                    selectedPhysicalPrice = parseFloat(data.price || 0);
                    
                    // Check enrollment specifically for this physical batch
                    selectedPhysicalId = data.id;
                    isPhysicalBooked = await checkBookingStatus(data.id, 'physical');
                }
            } else { selectedPhysicalId = null; }

            // 3. Logic: Should the button be disabled?
            // If BOTH are checked, and either is booked, or if only ONE is checked and it is booked.
            const shouldDisable = (chkOnline.checked && isOnlineBooked) || (chkPhysical.checked && isPhysicalBooked);

            if (shouldDisable) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = "<span>Selection Already Enrolled</span>";
                submitBtn.style.opacity = "0.7";
                if (prebookingField) prebookingField.style.display = 'none';
            } else {
                submitBtn.disabled = false;
                submitBtn.innerHTML = `<span>Continue Pre-Booking</span><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7" /></svg>`;
                submitBtn.style.opacity = "1";
                if (prebookingField) prebookingField.style.display = 'block';
            }

            summaryTotal.innerText = `Rs. ${totalFee.toLocaleString()}`;
            bookingSummary.style.display = totalFee > 0 ? 'block' : 'none';
            batchLoading.style.display = 'none';
        }

        // ══ 4. PAYMENT STEP ══
        document.getElementById('booking-submit-btn')?.addEventListener('click', () => {
            if (prebookingAmountInput.value < 999) {
                alert("Min. Rs. 999 required.");
                return;
            }
            document.querySelectorAll('.display-amount').forEach(el => el.innerText = prebookingAmountInput.value);
            showStep(stepPayment);
            console.log("Selected Batch ID:", prebookingAmountInput.value);
        });

        

        // Tab Switcher
        document.querySelectorAll('.pay-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.pay-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                const target = this.dataset.target;
                const direct = document.getElementById('pay-direct');
                const manual = document.getElementById('pay-manual');
                if (direct) direct.style.display = target === 'pay-direct' ? 'block' : 'none';
                if (manual) manual.style.display = target === 'pay-manual' ? 'block' : 'none';
            });
        });

        // ══ 5. ESEWA EXECUTION ══
        const esewaBtn = document.getElementById('btn-esewa-direct');

        if (esewaBtn) {
            esewaBtn.addEventListener('click', async (e) => {
                e.preventDefault(); // STOP the redirect immediately
                e.stopPropagation();

                const btn = e.currentTarget;
                const originalText = btn.innerHTML;
                
                btn.disabled = true;
                btn.innerText = "Processing...";

                try {
                    const response = await fetch('ajax-handler.php?action=prepare-esewa', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            amount: prebookingAmountInput.value,
                            batch_id: selectedBatchId,
                            online_price: selectedOnlinePrice,
                            physical_id: selectedPhysicalId,
                            physical_price: selectedPhysicalPrice
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Create a temporary hidden form to POST to eSewa
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = window.APP_CONFIG.esewa_url; // https://rc-epay.esewa.com.np/api/epay/main/v2/form

                        for (const key in data.params) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = key;
                            input.value = data.params[key];
                            form.appendChild(input);
                        }

                        document.body.appendChild(form);
                        form.submit(); 
                    } else {
                        alert("Failed to prepare eSewa: " + data.message);
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                } catch (error) {
                    console.error("eSewa Error:", error);
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    alert("Connection error. Check your console.");
                }
            });
        }

        document.getElementById('btn-manual-submit')?.addEventListener('click', async () => {
            const file = document.getElementById('payment-screenshot').files[0];
            if (!file) return alert("Upload screenshot.");

            const formData = new FormData();
                if (selectedBatchId) formData.append('online_batch_id', selectedBatchId);
                if (selectedPhysicalId) formData.append('physical_class_id', selectedPhysicalId);
            
                formData.append('amount', prebookingAmountInput.value);
                formData.append('method', 'manual');
                formData.append('payment_mode', 'Esewa_9857084809');
                formData.append('screenshot', file);
                formData.append('online_price', selectedOnlinePrice);
                formData.append('physical_price', selectedPhysicalPrice);

            const res = await fetch('ajax-handler.php?action=process-cee-booking', {
                method: 'POST',
                body: formData
            });
            const result = await res.json();
            if (result.success) showStep(stepSuccess);
        });

        // ══ HELPERS ══
        function showStep(stepElement) {
            [stepReg, stepBooking, stepPayment, stepSuccess].forEach(s => { if(s) s.style.display = 'none'; });
            if(stepElement) {
                stepElement.style.display = 'block';
                window.scrollTo({ top: 0, behavior: 'smooth' });
                if(stepElement === stepBooking) updateBookingUI();
            }
        }

        async function refreshUserId() {
            try {
                const res = await fetch('ajax-handler.php?action=mini-profile');
                const data = await res.json();
                if (data?.id) {
                    localStorage.setItem('rf_user_id', data.id);
                    return data.id;
                }
            } catch (e) { return null; }
        }

        async function fetchBatchData(type) {
            const res = await fetch(`ajax-handler.php?action=get-batch-details&type=${type}`);
            return await res.json();
        }

        function showStep(stepElement) {
            [stepReg, stepBooking, stepPayment, stepSuccess].forEach(s => { if(s) s.style.display = 'none'; });
            if(stepElement) {
                stepElement.style.display = 'block';
                window.scrollTo({ top: 0, behavior: 'smooth' });
                if(stepElement === stepBooking) updateBookingUI();
            }
        }

        function renderCard(data, type) {
            const cardHtml = `
                <div class="batch-card" id="card-${type}-${data.id}">
                    <div class="batch-card-header">
                        <h3>${data.name}</h3>
                        <span class="batch-status-badge" id="status-badge-${data.id}">
                            Checking status...
                        </span>
                    </div>
                    <div class="batch-card-body">
                        <p>Fee: Rs. ${data.fee || data.price}</p>
                        </div>
                </div>
            `;
            batchCards.insertAdjacentHTML('beforeend', cardHtml);
        }

        function renderSummaryRow(name, price) {
            summaryRows.innerHTML += `<div class="summary-row"><span>${name}</span><span>Rs. ${price}</span></div>`;
        }

        [chkOnline, chkPhysical].forEach(i => i?.addEventListener('change', updateBookingUI));
    });
    
})();