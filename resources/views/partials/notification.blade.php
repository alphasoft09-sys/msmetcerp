<div class="goi-notification-container">
    @if(session('notification'))
        <x-notification 
            type="{{ session('notification.type') }}" 
            message="{{ session('notification.message') }}" 
        />
    @endif

    @if(session('status'))
        <x-notification 
            type="success" 
            message="{{ session('status') }}" 
        />
    @endif

    @if(session('error'))
        <x-notification 
            type="error" 
            message="{{ session('error') }}" 
        />
    @endif

    @if(session('success'))
        <x-notification 
            type="success" 
            message="{{ session('success') }}" 
        />
    @endif

    @if(session('info'))
        <x-notification 
            type="info" 
            message="{{ session('info') }}" 
        />
    @endif

    @if(session('warning'))
        <x-notification 
            type="warning" 
            message="{{ session('warning') }}" 
        />
    @endif

    @if ($errors->any())
        <x-notification 
            type="error" 
            message="Please correct the following errors:" 
            :dismissible="false"
            class="persistent"
        >
            <ul class="mt-2 ml-4 list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-notification>
    @endif
</div>

<script>
    // Function to show custom notification
    function showNotification(message, type = 'info', duration = 5000) {
        const container = document.querySelector('.goi-notification-container');
        if (!container) return;
        
        const notification = document.createElement('div');
        notification.className = `goi-alert goi-alert-${type}`;
        notification.setAttribute('role', 'alert');
        
        // Icon based on type
        const iconClass = {
            'success': 'fa-check-circle',
            'error': 'fa-exclamation-circle',
            'warning': 'fa-exclamation-triangle',
            'info': 'fa-info-circle'
        }[type] || 'fa-info-circle';
        
        notification.innerHTML = `
            <i class="fas ${iconClass}"></i>
            <span>${message}</span>
            <button type="button" class="notification-close" onclick="this.parentElement.remove();" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        container.appendChild(notification);
        
        // Animate in
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-10px)';
        
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateY(0)';
            notification.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        }, 10);
        
        // Auto dismiss
        if (duration > 0) {
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-10px)';
                
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, duration);
        }
        
        return notification;
    }
    
    // Make function globally available
    window.showNotification = showNotification;
</script>
