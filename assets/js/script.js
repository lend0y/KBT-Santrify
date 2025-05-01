// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle (if needed)
    const mobileMenuBtn = document.createElement('button');
    mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
    mobileMenuBtn.classList.add('mobile-menu-btn');
    
    const header = document.querySelector('header .container');
    if (header) {
        header.prepend(mobileMenuBtn);
        
        const nav = document.querySelector('nav');
        mobileMenuBtn.addEventListener('click', function() {
            nav.classList.toggle('show');
        });
    }
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let valid = true;
            const inputs = form.querySelectorAll('input[required]');
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                    input.classList.add('error');
                } else {
                    input.classList.remove('error');
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('Harap isi semua field yang wajib diisi.');
            }
        });
    });
    
    // Password confirmation validation
    const passwordConfirmForms = document.querySelectorAll('form[data-validate-password]');
    passwordConfirmForms.forEach(form => {
        const password = form.querySelector('input[name="password"]');
        const confirmPassword = form.querySelector('input[name="confirm_password"]');
        
        if (password && confirmPassword) {
            form.addEventListener('submit', function(e) {
                if (password.value !== confirmPassword.value) {
                    e.preventDefault();
                    confirmPassword.classList.add('error');
                    alert('Password dan konfirmasi password tidak sama.');
                }
            });
            
            confirmPassword.addEventListener('input', function() {
                if (password.value !== confirmPassword.value) {
                    confirmPassword.classList.add('error');
                } else {
                    confirmPassword.classList.remove('error');
                }
            });
        }
    });
});