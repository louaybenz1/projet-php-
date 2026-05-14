// js/validation.js
// ============================================================
// TEK-UP GYM — Client-Side Form Validation & UI Enhancements
//
// STRUCTURE:
//   1. Helper functions (shared by all validators)
//   2. Auth forms   — #registerForm, #loginForm
//   3. Dashboard forms:
//        - #profileForm       (client / coach / admin)
//        - #passwordForm      (client / coach / admin)
//        - #announcementForm  (coach/messages.php)
//        - #createUserForm    (admin/users.php)
//        - #createSubForm     (admin/subscriptions.php)
//        - #createSessionForm (admin/sessions.php)
//   4. UI enhancements
//        - Active sidebar link highlight
//        - Auto-dismiss alerts after 5 seconds
//        - Confirm before deleting
//        - Character counter on textareas
//
// REMEMBER: JS validation is for UX convenience only.
// PHP always re-validates on the server. A user can
// disable JS and bypass all of this — that is expected.
// ============================================================


// ============================================================
// 1. HELPER FUNCTIONS
// ============================================================

/**
 * showError(fieldId, message)
 * Displays a red message under the given input.
 */
function showError(fieldId, message) {
    var span = document.getElementById('err-' + fieldId);
    if (span) span.textContent = message;

    var field = document.getElementById(fieldId);
    if (field) field.style.borderColor = '#ff6b6b';
}

/**
 * clearError(fieldId)
 * Removes the error state from a single field.
 */
function clearError(fieldId) {
    var span = document.getElementById('err-' + fieldId);
    if (span) span.textContent = '';

    var field = document.getElementById(fieldId);
    if (field) field.style.borderColor = '';
}

/**
 * clearAllErrors(formEl)
 * Wipes all error states inside a given form element.
 * Pass the form DOM element, not an ID string.
 */
function clearAllErrors(formEl) {
    if (!formEl) return;

    formEl.querySelectorAll('.field-error').forEach(function(span) {
        span.textContent = '';
    });
    formEl.querySelectorAll('input, textarea, select').forEach(function(el) {
        el.style.borderColor = '';
    });
}

/**
 * isValidEmail(email)
 * Basic email format check: something@something.something
 */
function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

/**
 * addLiveClearing(form, fieldIds)
 * Adds an "input" listener to each field so the red error
 * disappears as soon as the user starts correcting the field.
 */
function addLiveClearing(form, fieldIds) {
    if (!form) return;
    fieldIds.forEach(function(id) {
        var el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', function() { clearError(id); });
        }
    });
}


// ============================================================
// 2A. REGISTER FORM  (#registerForm)
// ============================================================
(function() {
    var form = document.getElementById('registerForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        clearAllErrors(form);
        var valid = true;

        // Full Name
        var name = (document.getElementById('full_name') || {}).value || '';
        name = name.trim();
        if (name === '') {
            showError('full_name', 'Please enter your full name.');
            valid = false;
        } else if (name.length < 3) {
            showError('full_name', 'Name must be at least 3 characters.');
            valid = false;
        }

        // Email
        var email = (document.getElementById('email') || {}).value || '';
        email = email.trim();
        if (email === '') {
            showError('email', 'Please enter your email address.');
            valid = false;
        } else if (!isValidEmail(email)) {
            showError('email', 'Please enter a valid email address.');
            valid = false;
        }

        // Password
        var pw = (document.getElementById('password') || {}).value || '';
        if (pw === '') {
            showError('password', 'Please enter a password.');
            valid = false;
        } else if (pw.length < 6) {
            showError('password', 'Password must be at least 6 characters.');
            valid = false;
        }

        // Confirm Password
        var cpw = (document.getElementById('confirm_password') || {}).value || '';
        if (cpw === '') {
            showError('confirm_password', 'Please confirm your password.');
            valid = false;
        } else if (pw !== cpw) {
            showError('confirm_password', 'Passwords do not match.');
            valid = false;
        }

        // Age
        var age = parseInt((document.getElementById('age') || {}).value || '0');
        if (isNaN(age) || age < 15) {
            showError('age', 'You must be at least 15 years old.');
            valid = false;
        } else if (age > 100) {
            showError('age', 'Please enter a valid age.');
            valid = false;
        }

        if (!valid) e.preventDefault();
    });

    addLiveClearing(form, ['full_name', 'email', 'password', 'confirm_password', 'age']);
})();


// ============================================================
// 2B. LOGIN FORM  (#loginForm)
// ============================================================
(function() {
    var form = document.getElementById('loginForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        clearAllErrors(form);
        var valid = true;

        var email = (document.getElementById('email') || {}).value || '';
        email = email.trim();
        if (email === '') {
            showError('email', 'Please enter your email address.');
            valid = false;
        } else if (!isValidEmail(email)) {
            showError('email', 'Please enter a valid email address.');
            valid = false;
        }

        var pw = (document.getElementById('password') || {}).value || '';
        if (pw === '') {
            showError('password', 'Please enter your password.');
            valid = false;
        }

        if (!valid) e.preventDefault();
    });

    addLiveClearing(form, ['email', 'password']);
})();


// ============================================================
// 3A. PROFILE FORM  (#profileForm)
// Used in client/profile.php, coach/profile.php, admin/profile.php
// ============================================================
(function() {
    var form = document.getElementById('profileForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        clearAllErrors(form);
        var valid = true;

        var name = (document.getElementById('full_name') || {}).value || '';
        name = name.trim();
        if (name === '') {
            showError('full_name', 'Full name cannot be empty.');
            valid = false;
        } else if (name.length < 3) {
            showError('full_name', 'Name must be at least 3 characters.');
            valid = false;
        }

        var age = parseInt((document.getElementById('age') || {}).value || '0');
        if (!isNaN(age) && age !== 0) { // age is optional on profile forms
            if (age < 15 || age > 100) {
                showError('age', 'Please enter a valid age (15–100).');
                valid = false;
            }
        }

        if (!valid) e.preventDefault();
    });

    addLiveClearing(form, ['full_name', 'age']);
})();


// ============================================================
// 3B. PASSWORD CHANGE FORM  (#passwordForm)
// Used in client/profile.php, coach/profile.php, admin/profile.php
// ============================================================
(function() {
    var form = document.getElementById('passwordForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        clearAllErrors(form);
        var valid = true;

        var cur = (document.getElementById('current_password') || {}).value || '';
        if (cur === '') {
            showError('current_password', 'Please enter your current password.');
            valid = false;
        }

        var np = (document.getElementById('new_password') || {}).value || '';
        if (np === '') {
            showError('new_password', 'Please enter a new password.');
            valid = false;
        } else if (np.length < 6) {
            showError('new_password', 'Password must be at least 6 characters.');
            valid = false;
        }

        var cp = (document.getElementById('confirm_password') || {}).value || '';
        if (cp === '') {
            showError('confirm_password', 'Please confirm your new password.');
            valid = false;
        } else if (np !== cp) {
            showError('confirm_password', 'Passwords do not match.');
            valid = false;
        }

        if (!valid) e.preventDefault();
    });

    addLiveClearing(form, ['current_password', 'new_password', 'confirm_password']);
})();


// ============================================================
// 3C. ANNOUNCEMENT FORM  (#announcementForm)
// coach/messages.php
// ============================================================
(function() {
    var form = document.getElementById('announcementForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        clearAllErrors(form);
        var valid = true;

        var title = (document.getElementById('title') || {}).value || '';
        title = title.trim();
        if (title === '') {
            showError('title', 'Please enter an announcement title.');
            valid = false;
        } else if (title.length > 150) {
            showError('title', 'Title must be 150 characters or fewer.');
            valid = false;
        }

        var body = (document.getElementById('body') || {}).value || '';
        body = body.trim();
        if (body === '') {
            showError('body', 'Announcement message cannot be empty.');
            valid = false;
        }

        if (!valid) e.preventDefault();
    });

    addLiveClearing(form, ['title', 'body']);
})();


// ============================================================
// 3D. CREATE USER FORM  (#createUserForm)
// admin/users.php
// ============================================================
(function() {
    var form = document.getElementById('createUserForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        clearAllErrors(form);
        var valid = true;

        var name = (document.getElementById('full_name') || {}).value || '';
        name = name.trim();
        if (name === '') {
            showError('full_name', 'Please enter a full name.');
            valid = false;
        }

        var email = (document.getElementById('email') || {}).value || '';
        email = email.trim();
        if (email === '') {
            showError('email', 'Please enter an email address.');
            valid = false;
        } else if (!isValidEmail(email)) {
            showError('email', 'Please enter a valid email address.');
            valid = false;
        }

        var pw = (document.getElementById('password') || {}).value || '';
        if (pw === '') {
            showError('password', 'Please enter a password.');
            valid = false;
        } else if (pw.length < 6) {
            showError('password', 'Password must be at least 6 characters.');
            valid = false;
        }

        var role = document.getElementById('role');
        if (role && role.value === '') {
            showError('role', 'Please select a role.');
            valid = false;
        }

        if (!valid) e.preventDefault();
    });

    addLiveClearing(form, ['full_name', 'email', 'password']);
})();


// ============================================================
// 3E. CREATE SUBSCRIPTION FORM  (#createSubForm)
// admin/subscriptions.php
// ============================================================
(function() {
    var form = document.getElementById('createSubForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        clearAllErrors(form);
        var valid = true;

        var plan = (document.getElementById('plan_name') || {}).value || '';
        plan = plan.trim();
        if (plan === '') {
            showError('plan_name', 'Please enter a plan name.');
            valid = false;
        }

        var start = (document.getElementById('start_date') || {}).value || '';
        var end   = (document.getElementById('end_date')   || {}).value || '';

        if (start === '') {
            showError('start_date', 'Please select a start date.');
            valid = false;
        }

        if (end === '') {
            showError('end_date', 'Please select an end date.');
            valid = false;
        }

        // Cross-field check: end must be after start
        if (start !== '' && end !== '' && end <= start) {
            showError('end_date', 'End date must be after the start date.');
            valid = false;
        }

        if (!valid) e.preventDefault();
    });

    addLiveClearing(form, ['plan_name', 'start_date', 'end_date']);
})();


// ============================================================
// 3F. CREATE SESSION FORM  (#createSessionForm)
// admin/sessions.php
// ============================================================
(function() {
    var form = document.getElementById('createSessionForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        clearAllErrors(form);
        var valid = true;

        var title = (document.getElementById('title') || {}).value || '';
        title = title.trim();
        if (title === '') {
            showError('title', 'Please enter a session title.');
            valid = false;
        }

        var date = (document.getElementById('session_date') || {}).value || '';
        if (date === '') {
            showError('session_date', 'Please select a session date.');
            valid = false;
        }

        var start = (document.getElementById('start_time') || {}).value || '';
        var end   = (document.getElementById('end_time')   || {}).value || '';

        if (start === '') {
            showError('start_time', 'Please select a start time.');
            valid = false;
        }

        if (end === '') {
            showError('end_time', 'Please select an end time.');
            valid = false;
        }

        // Same-day check: end time must be after start time
        if (start !== '' && end !== '' && end <= start) {
            showError('end_time', 'End time must be after the start time.');
            valid = false;
        }

        if (!valid) e.preventDefault();
    });

    addLiveClearing(form, ['title', 'session_date', 'start_time', 'end_time']);
})();


// ============================================================
// 4. UI ENHANCEMENTS
// These run on every page that loads this script.
// Each one is wrapped in a check so it only runs if the
// relevant element actually exists on the page.
// ============================================================

document.addEventListener('DOMContentLoaded', function() {

    // ----------------------------------------------------------
    // 4A. Auto-dismiss alert boxes after 5 seconds
    // Applies to .dash-alert and .alert boxes.
    // The alert fades out smoothly rather than disappearing abruptly.
    // ----------------------------------------------------------
    var alerts = document.querySelectorAll('.dash-alert-success, .alert-success');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.6s ease';
            alert.style.opacity    = '0';
            setTimeout(function() {
                // Remove from layout so it doesn't leave a gap
                alert.style.display = 'none';
            }, 650);
        }, 5000); // 5 seconds
    });


    // ----------------------------------------------------------
    // 4B. Confirm before deleting
    // Adds a "are you sure?" prompt to any button or link that
    // has a data-confirm attribute.
    //
    // Usage in HTML: <button data-confirm="Delete this user?">Delete</button>
    // This replaces inline onclick="return confirm(...)" calls,
    // which is slightly cleaner but functionally identical.
    // ----------------------------------------------------------
    document.querySelectorAll('[data-confirm]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            var message = el.getAttribute('data-confirm') || 'Are you sure?';
            if (!window.confirm(message)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });


    // ----------------------------------------------------------
    // 4C. Character counter for textareas
    // Any <textarea> with a maxlength attribute gets a live
    // counter below it showing how many characters remain.
    //
    // The counter turns red when fewer than 20 characters remain.
    // ----------------------------------------------------------
    document.querySelectorAll('textarea[maxlength]').forEach(function(textarea) {
        var max     = parseInt(textarea.getAttribute('maxlength'));
        var counter = document.createElement('span');
        counter.style.cssText = 'font-size:0.75rem; color:#5a6470; margin-top:4px; display:block;';
        counter.textContent   = max + ' characters remaining';

        // Insert counter right after the textarea
        textarea.parentNode.insertBefore(counter, textarea.nextSibling);

        textarea.addEventListener('input', function() {
            var remaining = max - textarea.value.length;
            counter.textContent = remaining + ' characters remaining';

            // Warn the user when they're running out of space
            counter.style.color = remaining < 20 ? '#ff8896' : '#5a6470';
        });
    });


    // ----------------------------------------------------------
    // 4D. Sidebar active link auto-highlight
    // In case a page forgets to add the "active" class to its
    // sidebar link, this script compares each link's href to
    // the current page URL and adds the class automatically.
    //
    // Pages that manually set "active" are unaffected since
    // adding a class twice has no effect.
    // ----------------------------------------------------------
    var currentPage = window.location.pathname.split('/').pop(); // e.g. "sessions.php"
    document.querySelectorAll('.sidebar-link').forEach(function(link) {
        var linkPage = link.getAttribute('href');
        if (linkPage && linkPage === currentPage) {
            link.classList.add('active');
        }
    });


    // ----------------------------------------------------------
    // 4E. Smooth scroll to top of form on validation error
    // When a PHP error message appears (after page reload),
    // scroll smoothly to the first alert box so the user sees it.
    // ----------------------------------------------------------
    var firstAlert = document.querySelector('.dash-alert-error, .alert-error');
    if (firstAlert) {
        firstAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

});
