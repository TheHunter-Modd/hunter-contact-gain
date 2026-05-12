/**
 * Hunter Contact Gain - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {

    // ============================================================
    // HAMBURGER MENU TOGGLE
    // ============================================================
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const mainNav = document.getElementById('mainNav');

    if (hamburgerBtn && mainNav) {
        hamburgerBtn.addEventListener('click', function() {
            this.classList.toggle('active');
            mainNav.classList.toggle('active');
            const isExpanded = this.classList.contains('active');
            this.setAttribute('aria-expanded', isExpanded);
        });

        const navLinks = mainNav.querySelectorAll('.nav__link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                hamburgerBtn.classList.remove('active');
                mainNav.classList.remove('active');
                hamburgerBtn.setAttribute('aria-expanded', 'false');
            });
        });
    }

    // ============================================================
    // FLASH MESSAGE AUTO-CLOSE
    // ============================================================
    const flashMessage = document.getElementById('flashMessage');
    if (flashMessage) {
        setTimeout(() => {
            flashMessage.style.transition = 'opacity 0.3s ease';
            flashMessage.style.opacity = '0';
            setTimeout(() => flashMessage.remove(), 300);
        }, 5000);
    }

    // ============================================================
    // PASSWORD VISIBILITY TOGGLE
    // ============================================================
    const toggleButtons = document.querySelectorAll('.form__toggle-password');
    toggleButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    this.textContent = '🙈';
                } else {
                    input.type = 'password';
                    this.textContent = '👁';
                }
            }
        });
    });

    // ============================================================
    // CONTACT MODAL LOGIC
    // ============================================================
    const submitContactBtn = document.getElementById('submitContactBtn');
    const updateContactBtn = document.getElementById('updateContactBtn');
    const contactModalOverlay = document.getElementById('contactModalOverlay');
    const closeContactModal = document.getElementById('closeContactModal');

    function openContactModal() {
        if (contactModalOverlay) {
            contactModalOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeContactModalFunc() {
        if (contactModalOverlay) {
            contactModalOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    if (submitContactBtn) {
        submitContactBtn.addEventListener('click', openContactModal);
    }
    if (updateContactBtn) {
        updateContactBtn.addEventListener('click', openContactModal);
    }
    if (closeContactModal) {
        closeContactModal.addEventListener('click', closeContactModalFunc);
    }
    
    if (contactModalOverlay) {
        contactModalOverlay.addEventListener('click', function(e) {
            if (e.target === contactModalOverlay) {
                closeContactModalFunc();
            }
        });
    }

    if (window.location.search.includes('show_contact_modal=1')) {
        openContactModal();
    }

    // ============================================================
    // PAYMENT MODAL LOGIC
    // ============================================================
    const makePaymentBtn = document.getElementById('makePaymentBtn');
    const paymentModalOverlay = document.getElementById('paymentModalOverlay');
    const closePaymentModal = document.getElementById('closePaymentModal');

    function openPaymentModal() {
        if (paymentModalOverlay) {
            paymentModalOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closePaymentModalFunc() {
        if (paymentModalOverlay) {
            paymentModalOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    if (makePaymentBtn) {
        makePaymentBtn.addEventListener('click', openPaymentModal);
    }
    if (closePaymentModal) {
        closePaymentModal.addEventListener('click', closePaymentModalFunc);
    }
    
    if (paymentModalOverlay) {
        paymentModalOverlay.addEventListener('click', function(e) {
            if (e.target === paymentModalOverlay) {
                closePaymentModalFunc();
            }
        });
    }

    // ============================================================
    // VCF DOWNLOAD MODAL LOGIC
    // ============================================================
    const downloadVcfBtn = document.getElementById('downloadVcfBtn');
    const vcfModalOverlay = document.getElementById('vcfModalOverlay');
    const closeVcfModal = document.getElementById('closeVcfModal');

    function openVcfModal() {
        if (vcfModalOverlay) {
            vcfModalOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeVcfModalFunc() {
        if (vcfModalOverlay) {
            vcfModalOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    if (downloadVcfBtn) {
        downloadVcfBtn.addEventListener('click', function(e) {
            // Prevent default if it's an anchor tag
            e.preventDefault(); 
            openVcfModal();
        });
    }
    if (closeVcfModal) {
        closeVcfModal.addEventListener('click', closeVcfModalFunc);
    }
    
    if (vcfModalOverlay) {
        vcfModalOverlay.addEventListener('click', function(e) {
            if (e.target === vcfModalOverlay) {
                closeVcfModalFunc();
            }
        });
    }

    // Auto-open VCF modal if URL has ?show_vcf_modal=1
    if (window.location.search.includes('show_vcf_modal=1')) {
        openVcfModal();
    }

});

// Global function for flash close button
function closeFlash() {
    const flash = document.getElementById('flashMessage');
    if (flash) flash.remove();
}