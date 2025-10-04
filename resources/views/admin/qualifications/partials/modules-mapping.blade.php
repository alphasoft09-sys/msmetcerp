<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Module Mapping for:</strong> {{ $qualification->qf_name }}
            <br>
            <small>Select the modules you want to map to this qualification. Currently mapped: {{ count($mappedModuleIds) }} modules.</small>
        </div>
    </div>
</div>

<!-- Selected Modules Display -->
<div class="row mb-3" id="selectedModulesDisplay" style="display: none;">
    <div class="col-md-12">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="bi bi-check-circle me-2"></i>
                    Selected Modules (<span id="selectedModulesCount">0</span>)
                </h6>
            </div>
            <div class="card-body">
                <div id="selectedModulesList" class="d-flex flex-wrap gap-2">
                    <!-- Selected modules will be displayed here -->
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" class="form-control" id="moduleSearch" placeholder="Search modules by name or NOS code...">
            <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                <i class="bi bi-x-circle"></i>
            </button>
        </div>
    </div>
    <div class="col-md-6">
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm" id="selectAllModules">
                <i class="bi bi-check-all me-1"></i>
                Select All
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="deselectAllModules">
                <i class="bi bi-x-circle me-1"></i>
                Deselect All
            </button>
        </div>
    </div>
</div>

<form id="moduleMappingForm" data-qualification-id="{{ $qualification->id }}">
    @csrf
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-hover">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th style="width: 50px;">
                                <input type="checkbox" id="selectAllCheckbox" class="form-check-input">
                            </th>
                            <th>Module Name</th>
                            <th>NOS Code</th>
                            <th>Hours</th>
                            <th>Credits</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Sort modules: selected first, then unselected
                            $sortedModules = $allModules->sortBy(function($module) use ($mappedModuleIds) {
                                return in_array($module->id, $mappedModuleIds) ? 0 : 1;
                            });
                        @endphp
                        @foreach($sortedModules as $module)
                        <tr class="module-row {{ in_array($module->id, $mappedModuleIds) ? 'selected-module' : '' }}" 
                            data-module-name="{{ strtolower($module->module_name) }}" 
                            data-nos-code="{{ strtolower($module->nos_code) }}"
                            data-selected="{{ in_array($module->id, $mappedModuleIds) ? 'true' : 'false' }}">
                            <td>
                                <input type="checkbox" name="module_ids[]" value="{{ $module->id }}" 
                                       class="form-check-input module-checkbox"
                                       {{ in_array($module->id, $mappedModuleIds) ? 'checked' : '' }}>
                            </td>
                            <td>
                                <strong>{{ $module->module_name }}</strong>
                                @if(in_array($module->id, $mappedModuleIds))
                                    <span class="badge bg-success ms-2">Currently Mapped</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $module->nos_code }}</span>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $module->hour ?? 0 }} hours</span>
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $module->credit ?? 0 }} credits</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted">
                        <span id="selectedCount">0</span> of {{ $allModules->count() }} modules selected
                    </span>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Add CSS for better visual feedback -->
<style>
.selected-module {
    background-color: #e8f5e8 !important;
    border-left: 4px solid #28a745 !important;
}

.selected-module:hover {
    background-color: #d4edda !important;
}

.module-row {
    transition: all 0.2s ease;
}

.module-row:hover {
    background-color: #f8f9fa;
}

#moduleSearch:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.search-highlight {
    background-color: #fff3cd;
    font-weight: bold;
}

/* Animation for search results */
.module-row {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<!-- Initialize module mapping functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Note: Modal footer buttons are added by the main page JavaScript
    
    // Module search functionality
    const moduleSearchInput = document.getElementById('moduleSearch');
    if (moduleSearchInput) {
        moduleSearchInput.addEventListener('input', function() {
            console.log('Search input changed:', this.value);
            const searchTerm = this.value.toLowerCase().trim();
            const moduleRows = document.querySelectorAll('.module-row');
            let visibleCount = 0;
            
            moduleRows.forEach(function(row) {
                const moduleName = row.dataset.moduleName || '';
                const nosCode = row.dataset.nosCode || '';
                
                if (searchTerm === '' || moduleName.includes(searchTerm) || nosCode.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                    
                    // Highlight search terms if there's a search term
                    if (searchTerm !== '') {
                        highlightSearchTerms(row, searchTerm);
                    } else {
                        removeSearchHighlights(row);
                    }
                } else {
                    row.style.display = 'none';
                }
            });
            
            console.log(`Search results: ${visibleCount} modules visible out of ${moduleRows.length} total`);
            
            // Update select all checkbox after search
            updateSelectAllCheckbox();
        });
        
        // Also add keyup event for better responsiveness
        moduleSearchInput.addEventListener('keyup', function() {
            console.log('Search keyup:', this.value);
            const searchTerm = this.value.toLowerCase().trim();
            const moduleRows = document.querySelectorAll('.module-row');
            let visibleCount = 0;
            
            moduleRows.forEach(function(row) {
                const moduleName = row.dataset.moduleName || '';
                const nosCode = row.dataset.nosCode || '';
                
                if (searchTerm === '' || moduleName.includes(searchTerm) || nosCode.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                    
                    // Highlight search terms if there's a search term
                    if (searchTerm !== '') {
                        highlightSearchTerms(row, searchTerm);
                    } else {
                        removeSearchHighlights(row);
                    }
                } else {
                    row.style.display = 'none';
                }
            });
            
            console.log(`Keyup search results: ${visibleCount} modules visible`);
            
            // Update select all checkbox after search
            updateSelectAllCheckbox();
        });
    } else {
        console.error('Module search input not found!');
    }
    
    // Clear search functionality
    const clearSearchBtn = document.getElementById('clearSearch');
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            moduleSearchInput.value = '';
            const moduleRows = document.querySelectorAll('.module-row');
            
            moduleRows.forEach(function(row) {
                row.style.display = '';
                removeSearchHighlights(row);
            });
            
            updateSelectAllCheckbox();
            console.log('Search cleared');
        });
    }
    
    // Search highlighting functions
    function highlightSearchTerms(row, searchTerm) {
        const moduleNameCell = row.querySelector('td:nth-child(2) strong');
        const nosCodeCell = row.querySelector('td:nth-child(3) .badge');
        
        if (moduleNameCell && searchTerm) {
            const originalText = moduleNameCell.textContent;
            const highlightedText = originalText.replace(
                new RegExp(searchTerm, 'gi'),
                match => `<span class="search-highlight">${match}</span>`
            );
            moduleNameCell.innerHTML = highlightedText;
        }
        
        if (nosCodeCell && searchTerm) {
            const originalText = nosCodeCell.textContent;
            const highlightedText = originalText.replace(
                new RegExp(searchTerm, 'gi'),
                match => `<span class="search-highlight">${match}</span>`
            );
            nosCodeCell.innerHTML = highlightedText;
        }
    }
    
    function removeSearchHighlights(row) {
        const moduleNameCell = row.querySelector('td:nth-child(2) strong');
        const nosCodeCell = row.querySelector('td:nth-child(3) .badge');
        
        if (moduleNameCell) {
            const originalText = moduleNameCell.textContent;
            moduleNameCell.innerHTML = originalText;
        }
        
        if (nosCodeCell) {
            const originalText = nosCodeCell.textContent;
            nosCodeCell.innerHTML = originalText;
        }
    }
    
    // Select all functionality
    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        const visibleCheckboxes = document.querySelectorAll('.module-checkbox:not([style*="display: none"])');
        visibleCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
        updateSelectedModulesDisplay();
    });
    
    document.getElementById('selectAllModules').addEventListener('click', function() {
        const visibleCheckboxes = document.querySelectorAll('.module-checkbox:not([style*="display: none"])');
        visibleCheckboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        document.getElementById('selectAllCheckbox').checked = true;
        updateSelectedCount();
        updateSelectedModulesDisplay();
    });
    
    document.getElementById('deselectAllModules').addEventListener('click', function() {
        const visibleCheckboxes = document.querySelectorAll('.module-checkbox:not([style*="display: none"])');
        visibleCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        document.getElementById('selectAllCheckbox').checked = false;
        updateSelectedCount();
        updateSelectedModulesDisplay();
    });
    
    // Individual checkbox change
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('module-checkbox')) {
            updateSelectedCount();
            updateSelectAllCheckbox();
            updateSelectedModulesDisplay();
        }
    });
    
    // Update selected count
    function updateSelectedCount() {
        const selectedCount = document.querySelectorAll('.module-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = selectedCount;
        document.getElementById('selectedModulesCount').textContent = selectedCount;
    }
    
    // Update selected modules display
    function updateSelectedModulesDisplay() {
        const selectedModules = [];
        
        document.querySelectorAll('.module-checkbox:checked').forEach(function(checkbox) {
            const moduleId = checkbox.value;
            const moduleRow = checkbox.closest('tr');
            const moduleName = moduleRow.querySelector('td:nth-child(2) strong').textContent;
            const nosCode = moduleRow.querySelector('td:nth-child(3) .badge').textContent;
            const hours = moduleRow.querySelector('td:nth-child(4) .badge').textContent;
            const credits = moduleRow.querySelector('td:nth-child(5) .badge').textContent;
            
            selectedModules.push({
                id: moduleId,
                name: moduleName,
                nosCode: nosCode,
                hours: hours,
                credits: credits
            });
        });
        
        if (selectedModules.length > 0) {
            document.getElementById('selectedModulesDisplay').style.display = 'block';
            
            const modulesHtml = selectedModules.map(module => `
                <div class="badge bg-primary p-2">
                    <i class="bi bi-check-circle me-1"></i>
                    <strong>${module.name}</strong>
                    <span class="badge bg-light text-dark ms-1">${module.nosCode}</span>
                    <span class="badge bg-info ms-1">${module.hours}</span>
                    <span class="badge bg-warning ms-1">${module.credits}</span>
                </div>
            `).join('');
            
            document.getElementById('selectedModulesList').innerHTML = modulesHtml;
        } else {
            document.getElementById('selectedModulesDisplay').style.display = 'none';
            document.getElementById('selectedModulesList').innerHTML = '';
        }
    }
    
    // Update select all checkbox
    function updateSelectAllCheckbox() {
        const visibleCheckboxes = document.querySelectorAll('.module-checkbox:not([style*="display: none"])');
        const checkedVisibleCheckboxes = document.querySelectorAll('.module-checkbox:not([style*="display: none"]):checked');
        
        if (checkedVisibleCheckboxes.length === 0) {
            document.getElementById('selectAllCheckbox').indeterminate = false;
            document.getElementById('selectAllCheckbox').checked = false;
        } else if (checkedVisibleCheckboxes.length === visibleCheckboxes.length) {
            document.getElementById('selectAllCheckbox').indeterminate = false;
            document.getElementById('selectAllCheckbox').checked = true;
        } else {
            document.getElementById('selectAllCheckbox').indeterminate = true;
        }
    }
    
    // Form submission
    document.getElementById('moduleMappingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Form submission triggered');
        
        const selectedModules = Array.from(document.querySelectorAll('.module-checkbox:checked')).map(checkbox => checkbox.value);
        
        console.log('Selected modules:', selectedModules);
        
        // Show loading state
        const submitBtn = document.querySelector('button[type="submit"][form="moduleMappingForm"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Saving...';
        submitBtn.disabled = true;
        
        // Show immediate feedback
        console.log('Sending AJAX request...');
        
        fetch('/admin/qualifications/{{ $qualification->id }}/modules', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                module_ids: selectedModules
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('AJAX Success - Full response:', data);
            
            if (data.success) {
                console.log('Showing success message...');
                
                // Show success message
                if (typeof window.showAlert === 'function') {
                    window.showAlert('success', 'Module mappings updated successfully');
                } else {
                    alert('Module mappings updated successfully');
                }
                
                console.log('Closing modal and reloading page...');
                
                // Close modal and reload page after a short delay
                setTimeout(() => {
                    const modalInstance = bootstrap.Modal.getInstance(document.getElementById('mapModulesModal'));
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                    console.log('Reloading page...');
                    window.location.reload();
                }, 2000);
            } else {
                console.log('Showing error message...');
                const errorMessage = data.message || 'Failed to update module mappings';
                
                if (typeof window.showAlert === 'function') {
                    window.showAlert('danger', errorMessage);
                } else {
                    alert(errorMessage);
                }
            }
        })
        .catch(error => {
            console.error('AJAX Error:', error);
            const errorMessage = 'An error occurred while updating module mappings';
            
            if (typeof window.showAlert === 'function') {
                window.showAlert('danger', errorMessage);
            } else {
                alert(errorMessage);
            }
        })
        .finally(() => {
            console.log('AJAX request completed');
            // Restore button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
    
    // Initialize displays
    updateSelectedCount();
    updateSelectAllCheckbox();
    updateSelectedModulesDisplay();
});
</script> 