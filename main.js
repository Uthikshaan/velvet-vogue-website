/* 
  Velvet Vogue - Interactive JavaScript Features
  HND Software Engineering Semester 02
*/

document.addEventListener('DOMContentLoaded', function () {
    console.log('Velvet Vogue JavaScript Loaded.');

    
    const buttons = document.querySelectorAll('.btn, .pill-btn');
    buttons.forEach(button => {
        button.addEventListener('click', function (e) {
            const x = e.clientX - e.target.getBoundingClientRect().left;
            const y = e.clientY - e.target.getBoundingClientRect().top;

            const ripple = document.createElement('span');
            ripple.classList.add('btn-ripple');
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Mobile Navigation Toggle
    const mobileToggle = document.getElementById('mobileToggle');
    const navLinks = document.getElementById('navLinks');

    if (mobileToggle && navLinks) {
        mobileToggle.addEventListener('click', function () {
            navLinks.classList.toggle('active');
        });
    }

    // Product Detail Size & Color Pill Selection
    const sizePills = document.querySelectorAll('.size-pill');
    const selectedSizeInput = document.getElementById('selected_size');

    sizePills.forEach(pill => {
        pill.addEventListener('click', function () {
            sizePills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            if (selectedSizeInput) {
                selectedSizeInput.value = this.getAttribute('data-value');
            }
        });
    });

    const colorPills = document.querySelectorAll('.color-pill');
    const selectedColorInput = document.getElementById('selected_color');

    colorPills.forEach(pill => {
        pill.addEventListener('click', function () {
            colorPills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            if (selectedColorInput) {
                selectedColorInput.value = this.getAttribute('data-value');
            }
        });
    });

    // Client-side Form Validation Helper
    const validateForms = document.querySelectorAll('.needs-validation');
    validateForms.forEach(form => {
        form.addEventListener('submit', function (event) {
            let valid = true;
            const inputs = form.querySelectorAll('input[required], textarea[required]');
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                    input.style.borderColor = '#dc3545';
                } else {
                    input.style.borderColor = '#e2e8f0';
                }
            });

            if (!valid) {
                event.preventDefault();
                alert('Please complete all required fields correctly before submitting.');
            }
        });
    });
});
