@extends('admin.layout')

@section('title', 'Edit LMS Site')

@section('content')
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-pencil-square me-2"></i>
                            Edit LMS Site: {{ $tcLm->site_title }}
                        </h4>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-lg px-4" id="save-draft-btn">
                                <span class="btn-text">
                                    <i class="bi bi-floppy me-2"></i>
                                    Save Draft
                                </span>
                                <span class="btn-loading d-none">
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    Saving...
                                </span>
                            </button>
                            <a href="{{ route('admin.tc-lms.preview', $tcLm) }}" class="btn btn-outline-light btn-lg px-4" target="_blank">
                                <i class="bi bi-eye me-2"></i>
                                Preview
                            </a>
                            <button type="button" class="btn btn-success btn-lg px-4" id="submit-btn">
                                <span class="btn-text">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Submit for Approval
                                </span>
                                <span class="btn-loading d-none">
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    Submitting...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <!-- Content Editor Only -->
                    <form id="site-form" method="POST" action="{{ route('admin.tc-lms.update', $tcLm) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Content Editor -->
                        <div class="editor-section">
                            <div class="editor-header bg-light p-3 border-bottom">
                                <h5 class="mb-0">
                                    <i class="bi bi-file-text me-2"></i>
                                    Content Editor
                                </h5>
                            </div>
                            
                            <div class="editor-container" style="height: calc(100vh - 200px);">
                                <!-- Isolated SunEditor in iframe -->
                                <iframe id="sun-editor-iframe" src="about:blank" style="width: 100%; height: 100%; border: 1px solid #ddd; border-radius: 4px;"></iframe>
                                <textarea id="suneditor" name="site_contents" style="display: none;">@php
                                    $content = $tcLm->site_contents ?? '';
                                    // Check if content is old JSON format and clean it
                                    if (is_string($content) && strpos($content, 'component_') !== false && strpos($content, '"type":"heading"') !== false) {
                                        $content = ''; // Clear old JSON data
                                    }
                                    echo $content;
                                @endphp</textarea>
                            </div>
                        </div>
                        
                        <!-- Hidden inputs for saving -->
                        <input type="hidden" name="site_title" value="{{ $tcLm->site_title }}">
                        <input type="hidden" name="site_department" value="{{ $tcLm->site_department }}">
                        <input type="hidden" name="site_description" value="{{ $tcLm->site_description }}">
                        <input type="hidden" name="site_contents" id="site-contents-input">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert Container -->
<div id="alert-container" class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;"></div>
@endsection

@push('styles')
<!-- SunEditor CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/css/suneditor.min.css">

<style>
/* Enhanced Button Styling */
.btn-group .btn {
    transition: all 0.3s ease;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-radius: 6px !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.btn-group .btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-group .btn:disabled {
    transform: none;
    opacity: 0.7;
    cursor: not-allowed;
}

.btn-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
}

.btn-success {
    background: linear-gradient(135deg, #28a745, #1e7e34);
    border: none;
}

.btn-outline-light {
    border: 2px solid rgba(255,255,255,0.3);
    color: white;
}

.btn-outline-light:hover {
    background: rgba(255,255,255,0.1);
    border-color: rgba(255,255,255,0.5);
    color: white;
}

/* Loading Animation */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Alert Styling */
.alert {
    border-radius: 8px;
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    font-weight: 500;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    color: #155724;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da, #f5c6cb);
    color: #721c24;
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1, #bee5eb);
    color: #0c5460;
}
</style>
@endpush

@push('scripts')
<!-- SunEditor Scripts -->
<script src="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/suneditor.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/suneditor@latest/src/lang/en.js"></script>

<script>
// Initialize SunEditor in iframe for complete isolation
window.addEventListener('load', function() {
    setTimeout(function() {
        initializeSunEditorInIframe();
    }, 200);
});

      function getCleanContent() {
          const content = document.getElementById('suneditor').value;
          console.log('Original content:', content.substring(0, 100) + '...');
          
          // Check if content is old JSON format and clear it
          if (content && (content.includes('component_') || content.includes('"type":"heading"'))) {
              console.log('Detected old JSON format, clearing content');
              return ''; // Clear old JSON data
          }
          
          console.log('Content is clean, using as-is');
          return content || '';
      }

      function initializeSunEditorInIframe() {
    try {
        const iframe = document.getElementById('sun-editor-iframe');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        
        // Create clean HTML document in iframe
        iframeDoc.open();
        iframeDoc.write(`
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>SunEditor</title>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/css/suneditor.min.css">
                <style>
                    body {
                        margin: 0;
                        padding: 0;
                        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                        background: #fff;
                    }
                              .sun-editor {
                                  width: 100% !important;
                                  height: 100% !important;
                                  border: none !important;
                                  min-height: 100% !important;
                              }
                    .sun-editor .se-toolbar {
                        display: flex !important;
                        flex-wrap: wrap !important;
                        align-items: center !important;
                        background: #f8f9fa !important;
                        border-bottom: 1px solid #dee2e6 !important;
                        padding: 8px !important;
                    }
                    .sun-editor .se-btn-tray {
                        display: flex !important;
                        flex-wrap: wrap !important;
                        align-items: center !important;
                    }
                    .sun-editor .se-btn {
                        display: inline-flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                        width: 32px !important;
                        height: 32px !important;
                        margin: 2px !important;
                        border-radius: 4px !important;
                        background: transparent !important;
                        border: none !important;
                        cursor: pointer !important;
                    }
                    .sun-editor .se-btn:hover {
                        background-color: #e9ecef !important;
                    }
                    .sun-editor .se-btn.active {
                        background-color: #007bff !important;
                        color: white !important;
                    }
                              .sun-editor .se-wrapper {
                                  height: calc(100vh - 250px) !important;
                                  border: none !important;
                              }
                    .sun-editor .se-wrapper-inner {
                        padding: 15px !important;
                        font-family: inherit !important;
                        font-size: 16px !important;
                        line-height: 1.6 !important;
                        color: #333 !important;
                    }
                </style>
            </head>
            <body>
                      <textarea id="suneditor" name="content">${getCleanContent()}</textarea>
                
                <script src="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/suneditor.min.js"><\/script>
                <script src="https://cdn.jsdelivr.net/npm/suneditor@latest/src/lang/en.js"><\/script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Initialize SunEditor in iframe
                        const editor = SUNEDITOR.create('suneditor', {
                            height: '100%',
                            lang: SUNEDITOR_LANG['en'],
                            buttonList: [
                                ['undo', 'redo', 'font', 'fontSize', 'formatBlock'],
                                ['bold', 'underline', 'italic', 'strike', 'subscript', 'superscript'],
                                ['fontColor', 'hiliteColor'],
                                ['align', 'list', 'table'],
                                ['link', 'image', 'video'],
                                ['fullScreen', 'codeView']
                            ],
                            callBackSave: function(contents) {
                                // Send content to parent window
                                window.parent.postMessage({
                                    type: 'suneditor-content',
                                    content: contents
                                }, '*');
                            }
                        });
                        
                        // Store editor reference
                        window.sunEditor = editor;
                        
                                  // Listen for content updates from parent
                                  window.addEventListener('message', function(event) {
                                      if (event.data.type === 'get-content') {
                                          const content = editor.getContents();
                                          window.parent.postMessage({
                                              type: 'suneditor-content',
                                              content: content,
                                              status: event.data.status || 'draft'
                                          }, '*');
                                      }
                                  });
                    });
                <\/script>
            </body>
            </html>
        `);
        iframeDoc.close();
        
        // Add save functionality for both buttons
        document.getElementById('save-draft-btn').addEventListener('click', function() {
            saveContent('draft');
        });
        
        document.getElementById('submit-btn').addEventListener('click', function() {
            saveContent('submitted');
        });
        
        function saveContent(status) {
            const saveBtn = document.getElementById('save-draft-btn');
            const submitBtn = document.getElementById('submit-btn');
            
            // Show loading state
            if (status === 'draft') {
                showButtonLoading(saveBtn);
            } else {
                showButtonLoading(submitBtn);
            }
            
            // Request content from iframe
            iframe.contentWindow.postMessage({type: 'get-content', status: status}, '*');
        }
        
        function showButtonLoading(button) {
            const btnText = button.querySelector('.btn-text');
            const btnLoading = button.querySelector('.btn-loading');
            
            btnText.classList.add('d-none');
            btnLoading.classList.remove('d-none');
            button.disabled = true;
        }
        
        function hideButtonLoading(button) {
            const btnText = button.querySelector('.btn-text');
            const btnLoading = button.querySelector('.btn-loading');
            
            btnText.classList.remove('d-none');
            btnLoading.classList.add('d-none');
            button.disabled = false;
        }
        
        // Function to compress images in content
        function compressImagesInContent(content) {
            return new Promise((resolve) => {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = content;
                const images = tempDiv.querySelectorAll('img[src^="data:image"]');
                
                if (images.length === 0) {
                    resolve(content);
                    return;
                }
                
                let processedImages = 0;
                const totalImages = images.length;
                
                images.forEach((img, index) => {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    const imgElement = new Image();
                    
                    imgElement.onload = function() {
                        // Calculate new dimensions (max 1920x1080)
                        let { width, height } = imgElement;
                        const maxWidth = 1920;
                        const maxHeight = 1080;
                        
                        if (width > maxWidth || height > maxHeight) {
                            const ratio = Math.min(maxWidth / width, maxHeight / height);
                            width *= ratio;
                            height *= ratio;
                        }
                        
                        canvas.width = width;
                        canvas.height = height;
                        
                        // Draw and compress
                        ctx.drawImage(imgElement, 0, 0, width, height);
                        
                        // Convert to compressed JPEG (80% quality)
                        const compressedDataUrl = canvas.toDataURL('image/jpeg', 0.8);
                        img.src = compressedDataUrl;
                        
                        processedImages++;
                        if (processedImages === totalImages) {
                            resolve(tempDiv.innerHTML);
                        }
                    };
                    
                    imgElement.src = img.src;
                });
            });
        }

        // Listen for content from iframe
        window.addEventListener('message', function(event) {
            if (event.data.type === 'suneditor-content') {
                const status = event.data.status || 'draft';
                
                // Debug: Log the content being sent
                console.log('Content being saved:', event.data.content.substring(0, 200) + '...');
                
                // Compress images before sending
                compressImagesInContent(event.data.content).then(compressedContent => {
                    console.log('Content after compression:', compressedContent.substring(0, 200) + '...');
                    console.log('Content length after compression:', compressedContent.length);
                    
                    // Update hidden input with compressed content
                    document.getElementById('site-contents-input').value = compressedContent;
                    
                    // Show saving message
                    const message = status === 'submitted' ? 'Submitting for approval...' : 'Saving draft...';
                    showAlert(message, 'info');
                    
                    // Submit form via AJAX
                    const form = document.getElementById('site-form');
                    const formData = new FormData();
                
                // Debug: Check hidden input values first
                const siteTitleInput = document.querySelector('input[name="site_title"]');
                const siteDepartmentInput = document.querySelector('input[name="site_department"]');
                const siteDescriptionInput = document.querySelector('input[name="site_description"]');
                const siteContentsInput = document.getElementById('site-contents-input');
                
                console.log('Hidden input elements found:');
                console.log('site_title input:', siteTitleInput);
                console.log('site_title value:', siteTitleInput ? siteTitleInput.value : 'NOT FOUND');
                console.log('site_department input:', siteDepartmentInput);
                console.log('site_department value:', siteDepartmentInput ? siteDepartmentInput.value : 'NOT FOUND');
                console.log('site_description input:', siteDescriptionInput);
                console.log('site_description value:', siteDescriptionInput ? siteDescriptionInput.value : 'NOT FOUND');
                console.log('site_contents input:', siteContentsInput);
                console.log('site_contents value:', siteContentsInput ? siteContentsInput.value : 'NOT FOUND');
                
                // Manually add all required fields
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('_method', 'PUT');
                
                if (siteTitleInput) {
                    formData.append('site_title', siteTitleInput.value);
                } else {
                    console.error('site_title input not found!');
                }
                
                if (siteDepartmentInput) {
                    formData.append('site_department', siteDepartmentInput.value);
                } else {
                    console.error('site_department input not found!');
                }
                
                if (siteDescriptionInput) {
                    formData.append('site_description', siteDescriptionInput.value);
                } else {
                    console.error('site_description input not found!');
                }
                
                if (siteContentsInput) {
                    formData.append('site_contents', siteContentsInput.value);
                } else {
                    console.error('site_contents input not found!');
                }
                
                formData.append('status', status);
                
                // Debug: Log all form data
                console.log('Form data contents:');
                for (let [key, value] of formData.entries()) {
                    console.log(key + ':', value);
                }
                
                // Debug: Check if hidden inputs exist
                console.log('Hidden input elements:');
                console.log('site_title input:', document.querySelector('input[name="site_title"]'));
                console.log('site_department input:', document.querySelector('input[name="site_department"]'));
                console.log('site_description input:', document.querySelector('input[name="site_description"]'));
                
                // Debug: Check form element
                console.log('Form element:', form);
                console.log('Form elements count:', form.elements.length);
                for (let i = 0; i < form.elements.length; i++) {
                    console.log('Form element ' + i + ':', form.elements[i].name, '=', form.elements[i].value);
                }
                
                // Debug: Log request details
                console.log('Sending AJAX request with headers:');
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                console.log('X-CSRF-TOKEN:', csrfToken);
                console.log('X-Requested-With: XMLHttpRequest');
                console.log('Accept: application/json');
                console.log('Status:', status);
                
                if (!csrfToken) {
                    console.error('CSRF token not found!');
                    showAlert('Security token missing. Please refresh the page and try again.', 'danger');
                    hideButtonLoading(document.getElementById('save-draft-btn'));
                    hideButtonLoading(document.getElementById('submit-btn'));
                    return;
                }
                
                // Create a timeout promise
                const timeoutPromise = new Promise((_, reject) => {
                    setTimeout(() => reject(new Error('Request timeout')), 30000); // 30 seconds timeout
                });
                
                // Race between fetch and timeout
                Promise.race([
                      fetch(form.action, {
                          method: 'PUT',
                          headers: {
                              'X-CSRF-TOKEN': csrfToken,
                              'X-Requested-With': 'XMLHttpRequest',
                              'Accept': 'application/json'
                          },
                          body: formData
                      }),
                    timeoutPromise
                ])
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    
                    if (!response.ok) {
                        // Try to get error details from response
                        return response.text().then(text => {
                            console.error('Error response:', text);
                            throw new Error(`HTTP error! status: ${response.status}, message: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    // Hide loading states
                    hideButtonLoading(document.getElementById('save-draft-btn'));
                    hideButtonLoading(document.getElementById('submit-btn'));
                    
                    if (data.success) {
                        const successMessage = status === 'submitted' 
                            ? 'Successfully submitted for approval! Your site is now pending review.' 
                            : 'Draft saved successfully!';
                        showAlert(successMessage, 'success');
                        
                        // Update page title if submitted
                        if (status === 'submitted') {
                            document.title = document.title.replace('Edit', 'Submitted');
                        }
                    } else {
                        showAlert('Error: ' + data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Detailed error:', error);
                    console.error('Error message:', error.message);
                    console.error('Error stack:', error.stack);
                    
                    // Hide loading states
                    hideButtonLoading(document.getElementById('save-draft-btn'));
                    hideButtonLoading(document.getElementById('submit-btn'));
                    
                    let errorMessage = 'An error occurred. Please try again.';
                    
                    if (error.message === 'Request timeout') {
                        errorMessage = 'Request timed out. The server may be busy. Please try again.';
                    } else if (error.message.includes('Failed to fetch')) {
                        errorMessage = 'Network error. Please check your connection and try again.';
                    } else if (error.message.includes('HTTP error')) {
                        errorMessage = 'Server error: ' + error.message;
                    } else if (error.message.includes('JSON')) {
                        errorMessage = 'Invalid response from server. Please try again.';
                    }
                    
                    showAlert(errorMessage, 'danger');
                });
                }); // Close the compressImagesInContent Promise
            }
        });
        
        console.log('SunEditor initialized in iframe successfully');
        
    } catch (error) {
        console.error('Error initializing SunEditor in iframe:', error);
        // Fallback: show a simple textarea
        const textarea = document.getElementById('suneditor');
        if (textarea) {
            textarea.style.display = 'block';
            textarea.style.width = '100%';
            textarea.style.height = '400px';
            textarea.style.border = '1px solid #ddd';
            textarea.style.padding = '10px';
            textarea.style.fontFamily = 'Arial, sans-serif';
            textarea.style.fontSize = '14px';
        }
    }
}

// Alert function for user feedback
function showAlert(message, type) {
    const alertContainer = document.getElementById('alert-container');
    const alertId = 'alert-' + Date.now();
    
    const alertHtml = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    alertContainer.insertAdjacentHTML('beforeend', alertHtml);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        const alertElement = document.getElementById(alertId);
        if (alertElement) {
            alertElement.remove();
        }
    }, 5000);
}
</script>
@endpush
