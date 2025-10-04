@extends('admin.layout')

@section('title', 'Advanced LMS Site Builder')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1 text-primary">
                        <i class="bi bi-layout-text-window me-2"></i>
                        Advanced Site Builder: {{ $tcLm->site_title }}
                    </h1>
                    <p class="text-muted mb-0">
                        Create professional educational content with our Microsoft Word-like editor
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-success" id="save-draft">
                        <i class="bi bi-save me-2"></i>
                        Save Draft
                    </button>
                    <a href="{{ route('admin.tc-lms.preview', $tcLm) }}" class="btn btn-outline-info" target="_blank">
                        <i class="bi bi-eye me-2"></i>
                        Preview
                    </a>
                    <a href="{{ route('admin.tc-lms.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Back to Sites
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Builder Interface -->
    <div class="row">
        <div class="col-md-3">
            <!-- Components Panel -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-grid me-2"></i>
                        Content Elements
                    </h6>
                </div>
                <div class="card-body">
                    <div class="components-list">
                        <div class="component-item" draggable="true" data-component="heading">
                            <i class="bi bi-type me-2"></i>
                            Heading
                        </div>
                        <div class="component-item" draggable="true" data-component="paragraph">
                            <i class="bi bi-text-paragraph me-2"></i>
                            Paragraph
                        </div>
                        <div class="component-item" draggable="true" data-component="image">
                            <i class="bi bi-image me-2"></i>
                            Image
                        </div>
                        <div class="component-item" draggable="true" data-component="video">
                            <i class="bi bi-play-circle me-2"></i>
                            Video
                        </div>
                        <div class="component-item" draggable="true" data-component="list">
                            <i class="bi bi-list me-2"></i>
                            List
                        </div>
                        <div class="component-item" draggable="true" data-component="button">
                            <i class="bi bi-hand-index me-2"></i>
                            Button
                        </div>
                        <div class="component-item" draggable="true" data-component="card">
                            <i class="bi bi-card-text me-2"></i>
                            Card
                        </div>
                        <div class="component-item" draggable="true" data-component="spacer">
                            <i class="bi bi-distribute-vertical me-2"></i>
                            Spacer
                        </div>
                        <div class="component-item" draggable="true" data-component="divider">
                            <i class="bi bi-hr me-2"></i>
                            Divider
                        </div>
                        <div class="component-item" draggable="true" data-component="quote">
                            <i class="bi bi-quote me-2"></i>
                            Quote
                        </div>
                    </div>
                </div>
            </div>

            <!-- Media Library -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-images me-2"></i>
                        Media Library
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2 mb-3">
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#imageUploadModal">
                            <i class="bi bi-upload me-2"></i>
                            Upload Image
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#videoModal">
                            <i class="bi bi-play-circle me-2"></i>
                            Add Video
                        </button>
                    </div>
                    <div id="media-library">
                        <div class="text-center text-muted">
                            <i class="bi bi-hourglass-split"></i>
                            <p class="small">Loading media...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Canvas -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-layout-text-window me-2"></i>
                            Page Builder
                        </h6>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="mobile-view">
                                <i class="bi bi-phone"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="tablet-view">
                                <i class="bi bi-tablet"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" id="desktop-view">
                                <i class="bi bi-laptop"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="canvas-container" class="canvas-container">
                        <div id="canvas" class="canvas">
                            <!-- Canvas content will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <!-- Properties Panel -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Properties
                    </h6>
                </div>
                <div class="card-body">
                    <div id="properties-panel">
                        <p class="text-muted text-center">Select a component to edit properties</p>
                    </div>
                </div>
            </div>

            <!-- Formatting Tools -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-palette me-2"></i>
                        Formatting Tools
                    </h6>
                </div>
                <div class="card-body">
                    <div id="formatting-tools" style="display: none;">
                        <!-- Text Formatting -->
                        <div class="mb-3">
                            <label class="form-label">Font Size</label>
                            <select class="form-select form-select-sm" id="font-size">
                                <option value="12px">12px</option>
                                <option value="14px">14px</option>
                                <option value="16px" selected>16px</option>
                                <option value="18px">18px</option>
                                <option value="20px">20px</option>
                                <option value="24px">24px</option>
                                <option value="28px">28px</option>
                                <option value="32px">32px</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Text Color</label>
                            <input type="color" class="form-control form-control-sm" id="text-color" value="#000000">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Background Color</label>
                            <input type="color" class="form-control form-control-sm" id="bg-color" value="#ffffff">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Alignment</label>
                            <div class="btn-group w-100" role="group">
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-align="left">
                                    <i class="bi bi-text-left"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-align="center">
                                    <i class="bi bi-text-center"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-align="right">
                                    <i class="bi bi-text-right"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Padding</label>
                            <input type="range" class="form-range" id="padding" min="0" max="50" value="10">
                            <small class="text-muted">Padding: <span id="padding-value">10px</span></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Upload Modal -->
<div class="modal fade" id="imageUploadModal" tabindex="-1" aria-labelledby="imageUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageUploadModalLabel">Upload Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="imageUploadForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="imageFile" class="form-label">Select Image</label>
                        <input type="file" class="form-control" id="imageFile" accept="image/*" required>
                        <div class="form-text">Supported formats: JPEG, PNG, JPG, GIF, WebP (Max: 10MB)</div>
                    </div>
                    <div class="mb-3">
                        <label for="imageAltText" class="form-label">Alt Text</label>
                        <input type="text" class="form-control" id="imageAltText" placeholder="Describe the image for accessibility">
                    </div>
                    <div class="mb-3">
                        <label for="imageDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="imageDescription" rows="2" placeholder="Optional description"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-2"></i>
                        Upload Image
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Video Modal -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="videoModalLabel">Add Video</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="videoForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="videoUrl" class="form-label">Video URL</label>
                        <input type="url" class="form-control" id="videoUrl" placeholder="https://www.youtube.com/watch?v=..." required>
                        <div class="form-text">Supported: YouTube, Vimeo, or direct video URLs</div>
                    </div>
                    <div class="mb-3">
                        <label for="videoTitle" class="form-label">Video Title</label>
                        <input type="text" class="form-control" id="videoTitle" placeholder="Enter video title">
                    </div>
                    <div class="mb-3">
                        <label for="videoDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="videoDescription" rows="2" placeholder="Optional description"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-play-circle me-2"></i>
                        Add Video
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden form for saving -->
<form id="save-form" method="POST" action="{{ route('admin.tc-lms.update', $tcLm) }}" style="display: none;">
    @csrf
    @method('PUT')
    <input type="hidden" name="site_contents" id="site-contents-input">
    <input type="hidden" name="site_title" value="{{ $tcLm->site_title }}">
    <input type="hidden" name="site_department" value="{{ $tcLm->site_department }}">
    <input type="hidden" name="site_description" value="{{ $tcLm->site_description }}">
</form>

@push('styles')
<style>
.components-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.component-item {
    padding: 12px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    cursor: move;
    background: #f8f9fa;
    transition: all 0.2s ease;
    user-select: none;
}

.component-item:hover {
    background: #e9ecef;
    border-color: #007bff;
}

.component-item:active {
    transform: scale(0.98);
}

.canvas-container {
    min-height: 600px;
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
    position: relative;
}

.canvas {
    min-height: 600px;
    padding: 20px;
    position: relative;
}

.canvas.dragover {
    border-color: #007bff;
    background: #e3f2fd;
}

.component {
    margin-bottom: 20px;
    border: 1px solid transparent;
    padding: 10px;
    border-radius: 6px;
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
}

.component:hover {
    border-color: #007bff;
    background: rgba(0, 123, 255, 0.05);
}

.component.selected {
    border-color: #007bff;
    background: rgba(0, 123, 255, 0.1);
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.2);
}

.component .delete-btn {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #dc3545;
    color: white;
    border: none;
    font-size: 12px;
    cursor: pointer;
    display: none;
    z-index: 10;
}

.component:hover .delete-btn {
    display: block;
}

.component .move-btn {
    position: absolute;
    top: -8px;
    left: -8px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #6c757d;
    color: white;
    border: none;
    font-size: 12px;
    cursor: move;
    display: none;
    z-index: 10;
}

.component:hover .move-btn {
    display: block;
}

/* Responsive canvas */
.canvas.mobile {
    max-width: 375px;
    margin: 0 auto;
}

.canvas.tablet {
    max-width: 768px;
    margin: 0 auto;
}

.canvas.desktop {
    max-width: 100%;
}

/* Rich text editor styles */
.rich-text {
    min-height: 50px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 8px;
    background: white;
}

.rich-text:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

/* Media library styles */
.media-item {
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 8px;
    margin-bottom: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.media-item:hover {
    border-color: #007bff;
    background: #f8f9fa;
}

.media-item img {
    width: 100%;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
}

.media-item .media-info {
    font-size: 0.8rem;
    color: #6c757d;
    margin-top: 4px;
}

/* Formatting tools */
.formatting-tools {
    display: none;
}

.formatting-tools.active {
    display: block;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the advanced builder
    initializeAdvancedBuilder();
    
    // Load existing content if any
    @if($tcLm->site_contents)
        loadExistingContent(@json($tcLm->site_contents));
    @endif
    
    // Load media library
    loadMediaLibrary();
});

function initializeAdvancedBuilder() {
    const canvas = document.getElementById('canvas');
    const components = document.querySelectorAll('.component-item');
    
    // Make components draggable
    components.forEach(component => {
        component.addEventListener('dragstart', handleDragStart);
    });
    
    // Make canvas a drop zone
    canvas.addEventListener('dragover', handleDragOver);
    canvas.addEventListener('drop', handleDrop);
    canvas.addEventListener('dragleave', handleDragLeave);
    
    // Initialize other functionality
    initializeViewButtons();
    initializeActionButtons();
    initializeFormattingTools();
    initializeModals();
}

function handleDragStart(e) {
    e.dataTransfer.setData('text/plain', e.target.dataset.component);
    e.target.style.opacity = '0.5';
}

function handleDragOver(e) {
    e.preventDefault();
    e.currentTarget.classList.add('dragover');
}

function handleDragLeave(e) {
    e.currentTarget.classList.remove('dragover');
}

function handleDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('dragover');
    
    const componentType = e.dataTransfer.getData('text/plain');
    const rect = e.currentTarget.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    
    addComponent(componentType, x, y);
}

function addComponent(type, x, y) {
    const canvas = document.getElementById('canvas');
    const componentId = 'component_' + Date.now();
    
    let componentHtml = '';
    let componentData = {
        id: componentId,
        type: type,
        x: x,
        y: y,
        content: '',
        styles: {
            fontSize: '16px',
            color: '#000000',
            backgroundColor: '#ffffff',
            textAlign: 'left',
            padding: '10px'
        }
    };
    
    switch(type) {
        case 'heading':
            componentHtml = `
                <div class="component" data-id="${componentId}" onclick="selectComponent('${componentId}')">
                    <button class="delete-btn" onclick="deleteComponent('${componentId}')">&times;</button>
                    <button class="move-btn" draggable="true" onmousedown="startDrag('${componentId}')">⋮</button>
                    <h2 class="rich-text" contenteditable="true" data-placeholder="Enter heading text">Enter heading text</h2>
                </div>
            `;
            componentData.content = 'Enter heading text';
            break;
        case 'paragraph':
            componentHtml = `
                <div class="component" data-id="${componentId}" onclick="selectComponent('${componentId}')">
                    <button class="delete-btn" onclick="deleteComponent('${componentId}')">&times;</button>
                    <button class="move-btn" draggable="true" onmousedown="startDrag('${componentId}')">⋮</button>
                    <p class="rich-text" contenteditable="true" data-placeholder="Enter paragraph text">Enter paragraph text</p>
                </div>
            `;
            componentData.content = 'Enter paragraph text';
            break;
        case 'image':
            componentHtml = `
                <div class="component" data-id="${componentId}" onclick="selectComponent('${componentId}')">
                    <button class="delete-btn" onclick="deleteComponent('${componentId}')">&times;</button>
                    <button class="move-btn" draggable="true" onmousedown="startDrag('${componentId}')">⋮</button>
                    <div class="text-center p-4 border" style="min-height: 100px;">
                        <i class="bi bi-image display-4 text-muted"></i>
                        <p class="text-muted mt-2">Click to select image</p>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectImage('${componentId}')">
                            Select Image
                        </button>
                    </div>
                </div>
            `;
            break;
        case 'video':
            componentHtml = `
                <div class="component" data-id="${componentId}" onclick="selectComponent('${componentId}')">
                    <button class="delete-btn" onclick="deleteComponent('${componentId}')">&times;</button>
                    <button class="move-btn" draggable="true" onmousedown="startDrag('${componentId}')">⋮</button>
                    <div class="text-center p-4 border" style="min-height: 100px;">
                        <i class="bi bi-play-circle display-4 text-muted"></i>
                        <p class="text-muted mt-2">Click to add video</p>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="addVideo('${componentId}')">
                            Add Video
                        </button>
                    </div>
                </div>
            `;
            break;
        case 'button':
            componentHtml = `
                <div class="component" data-id="${componentId}" onclick="selectComponent('${componentId}')">
                    <button class="delete-btn" onclick="deleteComponent('${componentId}')">&times;</button>
                    <button class="move-btn" draggable="true" onmousedown="startDrag('${componentId}')">⋮</button>
                    <button class="btn btn-primary rich-text" contenteditable="true" data-placeholder="Button Text">Button Text</button>
                </div>
            `;
            componentData.content = 'Button Text';
            break;
        case 'card':
            componentHtml = `
                <div class="component" data-id="${componentId}" onclick="selectComponent('${componentId}')">
                    <button class="delete-btn" onclick="deleteComponent('${componentId}')">&times;</button>
                    <button class="move-btn" draggable="true" onmousedown="startDrag('${componentId}')">⋮</button>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title rich-text" contenteditable="true" data-placeholder="Card Title">Card Title</h5>
                            <p class="card-text rich-text" contenteditable="true" data-placeholder="Card content">Card content</p>
                        </div>
                    </div>
                </div>
            `;
            break;
        case 'spacer':
            componentHtml = `
                <div class="component" data-id="${componentId}" onclick="selectComponent('${componentId}')">
                    <button class="delete-btn" onclick="deleteComponent('${componentId}')">&times;</button>
                    <button class="move-btn" draggable="true" onmousedown="startDrag('${componentId}')">⋮</button>
                    <div style="height: 50px; background: #f8f9fa; border: 1px dashed #dee2e6;"></div>
                </div>
            `;
            break;
        case 'divider':
            componentHtml = `
                <div class="component" data-id="${componentId}" onclick="selectComponent('${componentId}')">
                    <button class="delete-btn" onclick="deleteComponent('${componentId}')">&times;</button>
                    <button class="move-btn" draggable="true" onmousedown="startDrag('${componentId}')">⋮</button>
                    <hr style="border-top: 2px solid #dee2e6;">
                </div>
            `;
            break;
        case 'quote':
            componentHtml = `
                <div class="component" data-id="${componentId}" onclick="selectComponent('${componentId}')">
                    <button class="delete-btn" onclick="deleteComponent('${componentId}')">&times;</button>
                    <button class="move-btn" draggable="true" onmousedown="startDrag('${componentId}')">⋮</button>
                    <blockquote class="blockquote">
                        <p class="rich-text" contenteditable="true" data-placeholder="Enter quote text">Enter quote text</p>
                        <footer class="blockquote-footer">
                            <cite class="rich-text" contenteditable="true" data-placeholder="Author name">Author name</cite>
                        </footer>
                    </blockquote>
                </div>
            `;
            break;
    }
    
    canvas.insertAdjacentHTML('beforeend', componentHtml);
    
    // Store component data
    window.siteComponents = window.siteComponents || {};
    window.siteComponents[componentId] = componentData;
    
    // Select the new component
    selectComponent(componentId);
}

function selectComponent(componentId) {
    // Remove previous selection
    document.querySelectorAll('.component.selected').forEach(el => {
        el.classList.remove('selected');
    });
    
    // Select current component
    const component = document.querySelector(`[data-id="${componentId}"]`);
    if (component) {
        component.classList.add('selected');
        showProperties(componentId);
        showFormattingTools();
    }
}

function deleteComponent(componentId) {
    const component = document.querySelector(`[data-id="${componentId}"]`);
    if (component) {
        component.remove();
        delete window.siteComponents[componentId];
        hideFormattingTools();
    }
}

function showProperties(componentId) {
    const component = window.siteComponents[componentId];
    if (!component) return;
    
    const propertiesPanel = document.getElementById('properties-panel');
    
    let propertiesHtml = `
        <h6>Properties</h6>
        <div class="mb-3">
            <label class="form-label">Component Type</label>
            <input type="text" class="form-control form-control-sm" value="${component.type}" readonly>
        </div>
    `;
    
    // Add specific properties based on component type
    switch(component.type) {
        case 'heading':
            propertiesHtml += `
                <div class="mb-3">
                    <label class="form-label">Heading Level</label>
                    <select class="form-select form-select-sm" id="heading-level-${componentId}" onchange="updateComponent('${componentId}')">
                        <option value="h1">H1</option>
                        <option value="h2" selected>H2</option>
                        <option value="h3">H3</option>
                        <option value="h4">H4</option>
                        <option value="h5">H5</option>
                        <option value="h6">H6</option>
                    </select>
                </div>
            `;
            break;
        case 'image':
            propertiesHtml += `
                <div class="mb-3">
                    <label class="form-label">Image Source</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="image-src-${componentId}" placeholder="Image URL">
                        <button class="btn btn-outline-secondary" type="button" onclick="selectImage('${componentId}')">
                            <i class="bi bi-image"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alt Text</label>
                    <input type="text" class="form-control form-control-sm" id="image-alt-${componentId}" placeholder="Alt text">
                </div>
            `;
            break;
        case 'video':
            propertiesHtml += `
                <div class="mb-3">
                    <label class="form-label">Video URL</label>
                    <div class="input-group input-group-sm">
                        <input type="url" class="form-control" id="video-url-${componentId}" placeholder="Video URL">
                        <button class="btn btn-outline-secondary" type="button" onclick="addVideo('${componentId}')">
                            <i class="bi bi-play-circle"></i>
                        </button>
                    </div>
                </div>
            `;
            break;
    }
    
    propertiesPanel.innerHTML = propertiesHtml;
}

function showFormattingTools() {
    document.getElementById('formatting-tools').style.display = 'block';
}

function hideFormattingTools() {
    document.getElementById('formatting-tools').style.display = 'none';
}

function initializeFormattingTools() {
    // Font size
    document.getElementById('font-size').addEventListener('change', function() {
        const selectedComponent = document.querySelector('.component.selected');
        if (selectedComponent) {
            const componentId = selectedComponent.dataset.id;
            updateComponentStyle(componentId, 'fontSize', this.value);
        }
    });
    
    // Text color
    document.getElementById('text-color').addEventListener('change', function() {
        const selectedComponent = document.querySelector('.component.selected');
        if (selectedComponent) {
            const componentId = selectedComponent.dataset.id;
            updateComponentStyle(componentId, 'color', this.value);
        }
    });
    
    // Background color
    document.getElementById('bg-color').addEventListener('change', function() {
        const selectedComponent = document.querySelector('.component.selected');
        if (selectedComponent) {
            const componentId = selectedComponent.dataset.id;
            updateComponentStyle(componentId, 'backgroundColor', this.value);
        }
    });
    
    // Text alignment
    document.querySelectorAll('[data-align]').forEach(btn => {
        btn.addEventListener('click', function() {
            const selectedComponent = document.querySelector('.component.selected');
            if (selectedComponent) {
                const componentId = selectedComponent.dataset.id;
                updateComponentStyle(componentId, 'textAlign', this.dataset.align);
            }
        });
    });
    
    // Padding
    document.getElementById('padding').addEventListener('input', function() {
        const selectedComponent = document.querySelector('.component.selected');
        if (selectedComponent) {
            const componentId = selectedComponent.dataset.id;
            updateComponentStyle(componentId, 'padding', this.value + 'px');
        }
        document.getElementById('padding-value').textContent = this.value + 'px';
    });
}

function updateComponentStyle(componentId, property, value) {
    const component = document.querySelector(`[data-id="${componentId}"]`);
    if (component) {
        const editableElement = component.querySelector('.rich-text');
        if (editableElement) {
            editableElement.style[property] = value;
        }
        
        // Update component data
        if (window.siteComponents[componentId]) {
            window.siteComponents[componentId].styles[property] = value;
        }
    }
}

function initializeViewButtons() {
    document.getElementById('mobile-view').addEventListener('click', () => {
        document.getElementById('canvas').className = 'canvas mobile';
        updateActiveViewButton('mobile-view');
    });
    
    document.getElementById('tablet-view').addEventListener('click', () => {
        document.getElementById('canvas').className = 'canvas tablet';
        updateActiveViewButton('tablet-view');
    });
    
    document.getElementById('desktop-view').addEventListener('click', () => {
        document.getElementById('canvas').className = 'canvas desktop';
        updateActiveViewButton('desktop-view');
    });
}

function updateActiveViewButton(activeId) {
    document.querySelectorAll('#mobile-view, #tablet-view, #desktop-view').forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-secondary');
    });
    
    document.getElementById(activeId).classList.remove('btn-outline-secondary');
    document.getElementById(activeId).classList.add('btn-primary');
}

function initializeActionButtons() {
    document.getElementById('save-draft').addEventListener('click', saveSite);
}

function initializeModals() {
    // Image upload form
    document.getElementById('imageUploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        uploadImage();
    });
    
    // Video form
    document.getElementById('videoForm').addEventListener('submit', function(e) {
        e.preventDefault();
        addVideoFromModal();
    });
}

function uploadImage() {
    const formData = new FormData();
    const imageFile = document.getElementById('imageFile').files[0];
    const altText = document.getElementById('imageAltText').value;
    const description = document.getElementById('imageDescription').value;
    
    if (!imageFile) {
        alert('Please select an image file');
        return;
    }
    
    formData.append('image', imageFile);
    formData.append('alt_text', altText);
    formData.append('description', description);
    
    fetch('{{ route("admin.tc-lms.upload-image", $tcLm) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('imageUploadModal')).hide();
            
            // Reset form
            document.getElementById('imageUploadForm').reset();
            
            // Reload media library
            loadMediaLibrary();
            
            // Show success message
            showAlert('Image uploaded successfully!', 'success');
        } else {
            showAlert('Failed to upload image: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while uploading the image', 'danger');
    });
}

function addVideoFromModal() {
    const videoUrl = document.getElementById('videoUrl').value;
    const title = document.getElementById('videoTitle').value;
    const description = document.getElementById('videoDescription').value;
    
    if (!videoUrl) {
        alert('Please enter a video URL');
        return;
    }
    
    fetch('{{ route("admin.tc-lms.add-video", $tcLm) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            video_url: videoUrl,
            title: title,
            description: description
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('videoModal')).hide();
            
            // Reset form
            document.getElementById('videoForm').reset();
            
            // Reload media library
            loadMediaLibrary();
            
            // Show success message
            showAlert('Video added successfully!', 'success');
        } else {
            showAlert('Failed to add video: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while adding the video', 'danger');
    });
}

function loadMediaLibrary() {
    fetch('{{ route("admin.tc-lms.get-media", $tcLm) }}')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const mediaLibrary = document.getElementById('media-library');
            if (data.media.length > 0) {
                mediaLibrary.innerHTML = data.media.map(media => `
                    <div class="media-item" onclick="insertMedia('${media.id}', '${media.type}', '${media.url}')">
                        ${media.type === 'image' ? 
                            `<img src="${media.url}" alt="${media.alt_text}">` : 
                            `<div class="text-center p-2"><i class="bi bi-play-circle display-4 text-success"></i></div>`
                        }
                        <div class="media-info">
                            <div class="fw-bold">${media.file_name}</div>
                            <div>${media.type} • ${media.file_size}</div>
                        </div>
                    </div>
                `).join('');
            } else {
                mediaLibrary.innerHTML = `
                    <div class="text-center text-muted">
                        <i class="bi bi-images"></i>
                        <p class="small">No media uploaded yet</p>
                    </div>
                `;
            }
        }
    })
    .catch(error => {
        console.error('Error loading media:', error);
        document.getElementById('media-library').innerHTML = `
            <div class="text-center text-danger">
                <i class="bi bi-exclamation-triangle"></i>
                <p class="small">Error loading media</p>
            </div>
        `;
    });
}

function insertMedia(mediaId, mediaType, mediaUrl) {
    const selectedComponent = document.querySelector('.component.selected');
    if (!selectedComponent) {
        showAlert('Please select a component first', 'warning');
        return;
    }
    
    const componentId = selectedComponent.dataset.id;
    const component = window.siteComponents[componentId];
    
    if (component && component.type === 'image') {
        const imageElement = selectedComponent.querySelector('img') || selectedComponent.querySelector('div');
        if (imageElement) {
            if (mediaType === 'image') {
                imageElement.innerHTML = `<img src="${mediaUrl}" alt="Uploaded image" style="max-width: 100%; height: auto;">`;
            } else {
                showAlert('Please select an image for this component', 'warning');
                return;
            }
        }
    } else if (component && component.type === 'video') {
        const videoElement = selectedComponent.querySelector('div');
        if (videoElement) {
            if (mediaType === 'video') {
                videoElement.innerHTML = `<iframe width="100%" height="315" src="${mediaUrl}" frameborder="0" allowfullscreen></iframe>`;
            } else {
                showAlert('Please select a video for this component', 'warning');
                return;
            }
        }
    }
    
    showAlert('Media inserted successfully!', 'success');
}

function selectImage(componentId) {
    const modal = new bootstrap.Modal(document.getElementById('imageUploadModal'));
    modal.show();
}

function addVideo(componentId) {
    const modal = new bootstrap.Modal(document.getElementById('videoModal'));
    modal.show();
}

function saveSite() {
    const content = JSON.stringify(window.siteComponents || {});
    document.getElementById('site-contents-input').value = content;
    document.getElementById('save-form').submit();
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 3000);
}

function loadExistingContent(content) {
    if (content && typeof content === 'object') {
        window.siteComponents = content;
        
        // Recreate components from saved data
        Object.values(content).forEach(componentData => {
            addComponentFromData(componentData);
        });
    }
}

function addComponentFromData(componentData) {
    const canvas = document.getElementById('canvas');
    const componentId = componentData.id;
    
    // Restore component data
    window.siteComponents[componentId] = componentData;
    
    // Recreate DOM element based on type
    let componentHtml = '';
    
    switch(componentData.type) {
        case 'heading':
            componentHtml = `
                <div class="component" data-id="${componentId}" onclick="selectComponent('${componentId}')">
                    <button class="delete-btn" onclick="deleteComponent('${componentId}')">&times;</button>
                    <button class="move-btn" draggable="true" onmousedown="startDrag('${componentId}')">⋮</button>
                    <h2 class="rich-text" contenteditable="true">${componentData.content}</h2>
                </div>
            `;
            break;
        case 'paragraph':
            componentHtml = `
                <div class="component" data-id="${componentId}" onclick="selectComponent('${componentId}')">
                    <button class="delete-btn" onclick="deleteComponent('${componentId}')">&times;</button>
                    <button class="move-btn" draggable="true" onmousedown="startDrag('${componentId}')">⋮</button>
                    <p class="rich-text" contenteditable="true">${componentData.content}</p>
                </div>
            `;
            break;
        // Add other component types as needed
    }
    
    canvas.insertAdjacentHTML('beforeend', componentHtml);
}
</script>
@endpush
@endsection
