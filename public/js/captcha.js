// CAPTCHA functionality for MSME Technology Centre
class CaptchaManager {
    constructor() {
        this.siteKey = null;
        this.isLoaded = false;
        this.isVerified = false;
        this.init();
    }
    
    async init() {
        try {
            // Get CAPTCHA configuration
            const response = await fetch('/captcha/config');
            const data = await response.json();
            
            if (data.success) {
                // Check if CAPTCHA is disabled
                if (!data.enabled) {
                    console.log('CAPTCHA: Disabled in environment');
                    this.isLoaded = true;
                    this.disabled = true;
                    return;
                }
                
                this.siteKey = data.site_key;
                this.isLocal = data.is_local || false;
                this.bypassEnabled = data.bypass_enabled || false;
                
                if (this.isLocal && this.bypassEnabled) {
                    console.log('CAPTCHA: Localhost development mode - bypass enabled');
                    this.isLoaded = true;
                } else if (this.siteKey) {
                    await this.loadRecaptcha();
                } else {
                    console.warn('CAPTCHA not configured:', data.message);
                }
            } else {
                console.warn('CAPTCHA not configured:', data.message);
            }
        } catch (error) {
            console.warn('Failed to load CAPTCHA configuration:', error);
        }
    }
    
    async loadRecaptcha() {
        if (this.isLoaded || !this.siteKey) return;
        
        return new Promise((resolve, reject) => {
            // Load reCAPTCHA Enterprise script if not already loaded
            if (!window.grecaptcha) {
                const script = document.createElement('script');
                script.src = `https://www.google.com/recaptcha/enterprise.js?render=${this.siteKey}`;
                script.async = true;
                script.defer = true;
                
                script.onload = () => {
                    this.isLoaded = true;
                    this.initializeRecaptcha();
                    resolve();
                };
                
                script.onerror = () => {
                    reject(new Error('Failed to load reCAPTCHA Enterprise'));
                };
                
                document.head.appendChild(script);
            } else {
                this.isLoaded = true;
                this.initializeRecaptcha();
                resolve();
            }
        });
    }
    
    initializeRecaptcha() {
        if (!window.grecaptcha || !this.siteKey) return;
        
        // Initialize reCAPTCHA Enterprise
        window.grecaptcha.enterprise.ready(() => {
            console.log('reCAPTCHA Enterprise initialized');
        });
    }
    
    async execute(action = 'login') {
        if (!this.isLoaded) {
            console.warn('CAPTCHA not loaded');
            return null;
        }
        
        // If CAPTCHA is disabled, return a dummy token
        if (this.disabled) {
            console.log('CAPTCHA: Disabled - returning dummy token');
            return 'disabled-bypass';
        }
        
        // For localhost development, return bypass token
        if (this.isLocal && this.bypassEnabled) {
            console.log('CAPTCHA: Using localhost bypass token');
            return 'localhost-bypass';
        }
        
        if (!this.siteKey) {
            console.warn('CAPTCHA site key not configured');
            return null;
        }
        
        try {
            const token = await window.grecaptcha.enterprise.execute(this.siteKey, { action: action });
            return token;
        } catch (error) {
            console.error('CAPTCHA execution failed:', error);
            return null;
        }
    }
    
    async verify(token) {
        if (!token) return false;
        
        try {
            const response = await fetch('/captcha/verify', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    'g-recaptcha-response': token
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.isVerified = true;
                return true;
            } else {
                console.error('CAPTCHA verification failed:', data.message);
                return false;
            }
        } catch (error) {
            console.error('CAPTCHA verification error:', error);
            return false;
        }
    }
    
    async checkVerification(token) {
        if (!token) return false;
        
        try {
            const response = await fetch('/captcha/check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    'g-recaptcha-response': token
                })
            });
            
            const data = await response.json();
            return data.success;
        } catch (error) {
            console.error('CAPTCHA check error:', error);
            return false;
        }
    }
    
    // Add CAPTCHA to form submission
    async addToForm(formElement, action = 'login') {
        if (!formElement) return;
        
        const originalSubmit = formElement.onsubmit;
        
        formElement.onsubmit = async (e) => {
            e.preventDefault();
            
            // Show loading state
            const submitBtn = formElement.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.innerHTML : '';
            
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            }
            
            try {
                // Execute CAPTCHA
                const token = await this.execute(action);
                
                if (!token) {
                    if (typeof showNotification === 'function') {
                        showNotification('CAPTCHA verification failed. Please try again.', 'error');
                    }
                    return false;
                }
                
                // Add token to form
                let tokenInput = formElement.querySelector('input[name="g-recaptcha-response"]');
                if (!tokenInput) {
                    tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = 'g-recaptcha-response';
                    formElement.appendChild(tokenInput);
                }
                tokenInput.value = token;
                
                // Verify token
                const isVerified = await this.verify(token);
                
                if (!isVerified) {
                    if (typeof showNotification === 'function') {
                        showNotification('CAPTCHA verification failed. Please try again.', 'error');
                    }
                    return false;
                }
                
                // Submit form
                if (originalSubmit) {
                    return originalSubmit.call(formElement, e);
                } else {
                    formElement.submit();
                }
                
            } catch (error) {
                console.error('CAPTCHA form submission error:', error);
                if (typeof showNotification === 'function') {
                    showNotification('CAPTCHA verification error. Please try again.', 'error');
                }
            } finally {
                // Restore button state
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            }
        };
    }
    
    // Add CAPTCHA to AJAX form submission
    async addToAjaxForm(formElement, action = 'login') {
        if (!formElement) return;
        
        const originalSubmit = formElement.onsubmit;
        
        formElement.onsubmit = async (e) => {
            e.preventDefault();
            
            // Show loading state
            const submitBtn = formElement.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.innerHTML : '';
            
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            }
            
            try {
                // Execute CAPTCHA
                const token = await this.execute(action);
                
                if (!token) {
                    if (typeof showNotification === 'function') {
                        showNotification('CAPTCHA verification failed. Please try again.', 'error');
                    }
                    return false;
                }
                
                // Add token to form data
                const formData = new FormData(formElement);
                formData.append('g-recaptcha-response', token);
                
                // Verify token
                const isVerified = await this.verify(token);
                
                if (!isVerified) {
                    if (typeof showNotification === 'function') {
                        showNotification('CAPTCHA verification failed. Please try again.', 'error');
                    }
                    return false;
                }
                
                // Continue with original AJAX submission
                if (originalSubmit) {
                    return originalSubmit.call(formElement, e);
                }
                
            } catch (error) {
                console.error('CAPTCHA AJAX form submission error:', error);
                if (typeof showNotification === 'function') {
                    showNotification('CAPTCHA verification error. Please try again.', 'error');
                }
            } finally {
                // Restore button state
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            }
        };
    }
}

// Initialize CAPTCHA manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.captchaManager = new CaptchaManager();
});
