@props(['type' => 'info', 'message' => null, 'dismissible' => true, 'icon' => null, 'autoDismiss' => true])

@php
    $classes = match ($type) {
        'success' => 'goi-alert goi-alert-success',
        'error' => 'goi-alert goi-alert-error',
        'warning' => 'goi-alert goi-alert-warning',
        default => 'goi-alert goi-alert-info',
    };
    
    if (!$autoDismiss) {
        $classes .= ' persistent';
    }
    
    $defaultIcons = [
        'success' => 'fa-check-circle',
        'error' => 'fa-exclamation-circle',
        'warning' => 'fa-exclamation-triangle',
        'info' => 'fa-info-circle',
    ];
    
    $iconClass = $icon ?? $defaultIcons[$type];
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} role="alert">
    <i class="fas {{ $iconClass }}"></i>
    <div class="notification-content">
        @if($message)
            <span>{{ $message }}</span>
        @endif
        {{ $slot }}
    </div>
    @if($dismissible)
        <button type="button" class="notification-close" onclick="this.parentElement.remove();" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
    @endif
</div>

<style>
    .notification-close {
        background: transparent;
        border: none;
        color: currentColor;
        opacity: 0.7;
        cursor: pointer;
        padding: 0;
        margin-left: auto;
        font-size: 0.875rem;
    }
    
    .notification-close:hover {
        opacity: 1;
    }
    
    .goi-alert {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .notification-content {
        flex: 1;
    }
    
    .notification-content ul {
        margin-top: 0.5rem;
        margin-left: 1.25rem;
        font-size: 0.9rem;
    }
    
    .notification-content li {
        margin-bottom: 0.25rem;
    }
</style>

<script>
    // Auto-hide notifications after 5 seconds with animation
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const notifications = document.querySelectorAll('.goi-alert');
            notifications.forEach(notification => {
                if (!notification.classList.contains('persistent')) {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateY(-10px)';
                    notification.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    
                    setTimeout(() => {
                        notification.style.display = 'none';
                    }, 300);
                }
            });
        }, 5000);
    });
</script>
