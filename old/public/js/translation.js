// Translation functionality for MSME Technology Centre
class TranslationManager {
    constructor() {
        this.currentLanguage = 'en';
        this.translations = {};
        this.translateApiUrl = '/translate';
        
        // Fallback translations (for when API is not available)
        this.fallbackTranslations = {
            'hi': {
                'Create an account': 'खाता बनाएं',
                'Sign in and get started': 'साइन इन करें और शुरू करें',
                'Email Address': 'ईमेल पता',
                'Password': 'पासवर्ड',
                'Remember me': 'मुझे याद रखें',
                'Forgot Password?': 'पासवर्ड भूल गए?',
                'Submit': 'सबमिट करें',
                'Signing In...': 'साइन इन हो रहा है...',
                'By signing in, you agree to our': 'साइन इन करके, आप हमारे',
                'Terms & Conditions': 'नियम और शर्तें',
                'and': 'और',
                'Privacy Policy': 'गोपनीयता नीति',
                'Welcome!': 'स्वागत है!',
                'Sign in to access MSME admin tools and resources.': 'MSME एडमिन टूल्स और संसाधनों तक पहुंचने के लिए साइन इन करें।',
                'Sign in to access your courses and learning resources.': 'अपने पाठ्यक्रमों और सीखने के संसाधनों तक पहुंचने के लिए साइन इन करें।',
                'Admin Login | MSME Technology Centre | Government of India': 'एडमिन लॉगिन | MSME प्रौद्योगिकी केंद्र | भारत सरकार',
                'Student Login | MSME Technology Centre | Government of India': 'छात्र लॉगिन | MSME प्रौद्योगिकी केंद्र | भारत सरकार',
                'ADMINISTRATOR LOGIN PORTAL': 'प्रशासक लॉगिन पोर्टल',
                'STUDENT LOGIN PORTAL': 'छात्र लॉगिन पोर्टल',
                'SECURE ACCESS': 'सुरक्षित पहुंच',
                'yourname@example.com': 'आपकानाम@उदाहरण.com',
                'Enter OTP': 'OTP दर्ज करें',
                'Verify OTP': 'OTP सत्यापित करें',
                'Resend OTP': 'OTP पुनः भेजें',
                'OTP sent to': 'OTP भेजा गया',
                'Enter the OTP sent to your device': 'अपने डिवाइस पर भेजे गए OTP को दर्ज करें',
                'Security Check': 'सुरक्षा जांच',
                'Enter the OTP sent to your device to verify your identity.': 'अपनी पहचान सत्यापित करने के लिए अपने डिवाइस पर भेजे गए OTP को दर्ज करें।',
                'Forgot password?': 'पासवर्ड भूल गए?',
                'Follow the steps to reset your account access.': 'अपने खाते तक पहुंच रीसेट करने के लिए चरणों का पालन करें।',
                'Follow the steps to reset your student account access.': 'अपने छात्र खाते तक पहुंच रीसेट करने के लिए चरणों का पालन करें।',
                'Password Recovery': 'पासवर्ड रिकवरी',
                'Student Password Recovery': 'छात्र पासवर्ड रिकवरी'
            }
        };
        
        this.init();
    }
    
    init() {
        this.setupLanguageSwitcher();
        this.loadSavedLanguage();
    }
    
    // Function to translate text using Laravel backend
    async translateText(text, targetLang) {
        if (targetLang === 'en') {
            return text; // No translation needed for English
        }
        
        try {
            // Call Laravel backend translation API
            const response = await fetch(this.translateApiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    text: text,
                    target_lang: targetLang,
                    source_lang: 'en'
                })
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    return data.translated_text;
                }
            }
            
            // Fallback to predefined translations
            if (this.fallbackTranslations[targetLang] && this.fallbackTranslations[targetLang][text]) {
                return this.fallbackTranslations[targetLang][text];
            }
            
            return text; // Return original text if no translation found
        } catch (error) {
            console.error('Translation error:', error);
            
            // Fallback to predefined translations
            if (this.fallbackTranslations[targetLang] && this.fallbackTranslations[targetLang][text]) {
                return this.fallbackTranslations[targetLang][text];
            }
            
            return text;
        }
    }
    
    // Function to translate all elements on the page
    async translatePage(targetLang) {
        const elements = document.querySelectorAll('[data-translate]');
        
        for (const element of elements) {
            const originalText = element.getAttribute('data-translate');
            const translatedText = await this.translateText(originalText, targetLang);
            
            if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
                element.placeholder = translatedText;
            } else {
                element.textContent = translatedText;
            }
        }
        
        // Translate placeholders
        const placeholderElements = document.querySelectorAll('[data-translate-placeholder]');
        for (const element of placeholderElements) {
            const originalPlaceholder = element.getAttribute('data-translate-placeholder');
            const translatedPlaceholder = await this.translateText(originalPlaceholder, targetLang);
            element.placeholder = translatedPlaceholder;
        }
        
        // Update page title and meta description
        const titleElement = document.querySelector('title');
        if (titleElement) {
            const originalTitle = titleElement.getAttribute('data-original-title') || titleElement.textContent;
            titleElement.setAttribute('data-original-title', originalTitle);
            const translatedTitle = await this.translateText(originalTitle, targetLang);
            titleElement.textContent = translatedTitle;
        }
    }
    
    // Setup language switcher event listeners
    setupLanguageSwitcher() {
        document.querySelectorAll('.goi-language-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                
                const lang = btn.getAttribute('data-lang');
                const action = btn.getAttribute('data-action');
                
                if (lang === 'font-size') {
                    // Handle font size adjustment
                    const currentSize = parseFloat(getComputedStyle(document.body).fontSize);
                    if (action === 'increase') {
                        document.body.style.fontSize = (currentSize + 2) + 'px';
                    } else if (action === 'decrease') {
                        document.body.style.fontSize = (currentSize - 2) + 'px';
                    }
                    
                    // Update active state for font size buttons
                    document.querySelectorAll('.goi-language-btn[data-lang="font-size"]').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                } else {
                    // Handle language translation
                    const langCode = btn.getAttribute('data-lang-code');
                    
                    // Update active state
                    document.querySelectorAll('.goi-language-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    
                    // Show loading indicator
                    const originalText = btn.innerHTML;
                    btn.innerHTML += ' <i class="fas fa-spinner fa-spin"></i>';
                    
                    try {
                        // Translate the page
                        await this.translatePage(langCode);
                        this.currentLanguage = langCode;
                        
                        // Store language preference
                        localStorage.setItem('preferredLanguage', langCode);
                        
                        // Show success notification if showNotification function exists
                        if (typeof showNotification === 'function') {
                            showNotification(`Language changed to ${originalText}`, 'success');
                        }
                    } catch (error) {
                        console.error('Translation failed:', error);
                        if (typeof showNotification === 'function') {
                            showNotification('Translation failed. Please try again.', 'error');
                        }
                    } finally {
                        // Remove loading indicator
                        btn.innerHTML = originalText;
                    }
                }
            });
        });
    }
    
    // Load saved language preference
    loadSavedLanguage() {
        const savedLanguage = localStorage.getItem('preferredLanguage');
        if (savedLanguage && savedLanguage !== 'en') {
            const langButton = document.querySelector(`[data-lang-code="${savedLanguage}"]`);
            if (langButton) {
                langButton.click();
            }
        }
    }
}

// Initialize translation manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.translationManager = new TranslationManager();
});
