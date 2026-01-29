// === create account validation ===
const passwordInput = document.getElementById('password');
const verifyInput = document.getElementById('verify_password');
const createBtn = document.getElementById('createAccountBtn');

const ruleLength = document.getElementById('rule-length');
const ruleNumber = document.getElementById('rule-number');
const ruleMatch = document.getElementById('rule-match');

function updateRule(li, isValid) {
    if (!li) return;
    if (isValid) {
        li.classList.add('valid');
    } else {
        li.classList.remove('valid');
    }
}

function validatePasswordForm() {
    const pwd = passwordInput ? passwordInput.value : '';
    const v   = verifyInput ? verifyInput.value : '';

    const hasLength = pwd.length >= 8;
    const hasNumber = /[0-9]/.test(pwd);
    const matches   = pwd !== '' && pwd === v;

    updateRule(ruleLength, hasLength);
    updateRule(ruleNumber, hasNumber);
    updateRule(ruleMatch, matches);

    const allGood = hasLength && hasNumber && matches;

    if (createBtn) {
        createBtn.disabled = !allGood;
    }
}

if (passwordInput && verifyInput) {
    passwordInput.addEventListener('input', validatePasswordForm);
    verifyInput.addEventListener('input', validatePasswordForm);
    validatePasswordForm(); // run once on load
}
