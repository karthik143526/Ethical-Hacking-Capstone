// js/main.js

async function checkAuth() {
    try {
        const response = await fetch('api/auth.php?action=check');
        const result = await response.json();
        if(result.status !== 'success') {
            window.location.href = 'login.html';
        }
    } catch(e) {
        window.location.href = 'login.html';
    }
}

function toggleEnterprise() {
    const secType = document.getElementById('sec-type').value;
    const entSection = document.getElementById('ent-section');
    if (secType === 'WPA3-Enterprise') {
        entSection.style.display = 'block';
    } else {
        entSection.style.display = 'none';
    }
}

function evaluatePassword() {
    const pwd = document.getElementById('wifi-password').value;
    const indicator = document.getElementById('pass-indicator');
    
    if (!pwd) {
        indicator.textContent = 'None';
        indicator.style.color = 'var(--text-secondary)';
        return;
    }

    if (pwd.length >= 12 && /[A-Z]/.test(pwd) && /[0-9]/.test(pwd) && /[^A-Za-z0-9]/.test(pwd)) {
        indicator.textContent = 'Strong';
        indicator.style.color = 'var(--accent-green)';
    } else if (pwd.length >= 8 && /[A-Za-z]/.test(pwd) && /[0-9]/.test(pwd)) {
        indicator.textContent = 'Medium';
        indicator.style.color = 'var(--accent-orange)';
    } else {
        indicator.textContent = 'Weak';
        indicator.style.color = 'var(--accent-danger)';
    }
}

async function runAssessment() {
    const ssid = document.getElementById('ssid').value;
    const err = document.getElementById('error-msg');
    
    if (!ssid) {
        err.textContent = 'Please enter a Wi-Fi Name (SSID).';
        err.style.display = 'block';
        return;
    }
    err.style.display = 'none';

    // Get current password strength from the indicator
    const passIndicator = document.getElementById('pass-indicator').textContent;
    let passStrength = passIndicator !== 'None' ? passIndicator : 'Weak';

    const payload = {
        ssid: ssid,
        sec_type: document.getElementById('sec-type').value,
        wpa3_enabled: document.getElementById('wpa3-enabled').value,
        wpa3_sae: document.getElementById('wpa3-sae').value,
        pmf: document.getElementById('pmf').value,
        pass_strength: passStrength,
        fw_updated: document.getElementById('fw-updated').value
    };

    if (payload.sec_type === 'WPA3-Enterprise') {
        payload.radius = document.getElementById('radius').value;
        payload.auth_method = document.getElementById('auth-method').value;
        payload.certificate = document.getElementById('certificate').value;
    }

    const btn = document.querySelector('.btn-primary');
    btn.disabled = true;
    btn.textContent = "Analyzing Config...";

    try {
        const response = await fetch('api/assess.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const result = await response.json();
        
        if (result.status === 'success') {
            window.location.href = 'results.html?id=' + result.assessment_id;
        } else {
            err.textContent = 'Error running assessment: ' + result.message;
            err.style.display = 'block';
        }
    } catch (error) {
        err.textContent = 'Could not connect to the backend API.';
        err.style.display = 'block';
    } finally {
        btn.disabled = false;
        btn.textContent = "Run Security Assessment";
    }
}

window.addEventListener('DOMContentLoaded', () => {
    if (window.location.pathname.endsWith('assessment.html')) {
        checkAuth();
        toggleEnterprise();
    }
});
