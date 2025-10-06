@props([
    'title' => 'Form',
    'subtitle' => 'Enter your information',
    'formAction' => '#',
    'formMethod' => 'POST',
    'showCaptcha' => false,
    'showBackLink' => false,
    'backLinkUrl' => '#',
    'backLinkText' => 'Back to Login'
])

<div class="goi-login-container-clean">
    <!-- Form Card -->
    <div class="goi-login-card">
        <!-- Form Header -->
        <div class="goi-login-header-clean">
            <h2>{{ $title }}</h2>
            <p>{{ $subtitle }}</p>
        </div>
        
        <!-- Alert Messages -->
        @include('partials.notification')
        
        <!-- Form Content -->
        <form action="{{ $formAction }}" method="{{ $formMethod }}">
            @csrf
            {{ $slot }}
        </form>
        
        @if($showBackLink)
        <!-- Back Link -->
        <div class="goi-form-back-link">
            <a href="{{ $backLinkUrl }}" class="goi-back-link">
                <i class="fas fa-arrow-left"></i> {{ $backLinkText }}
            </a>
        </div>
        @endif
    </div>
</div>
