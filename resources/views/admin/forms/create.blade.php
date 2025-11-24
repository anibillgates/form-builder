<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Form - {{ config('app.name') }}</title>
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- jQuery UI -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    
    <!-- FormBuilder CSS and JS -->
    <link rel="stylesheet" href="https://formbuilder.online/assets/css/form-builder.min.css">
    <script src="https://formbuilder.online/assets/js/form-builder.min.js"></script>
    
    <style>
        .form-builder {
            min-height: 500px;
        }
        .permissions-panel {
            margin-top: 15px;
            padding: 15px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
        }
        .permission-row {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 10px;
            padding: 8px;
            background: white;
            border-radius: 4px;
        }
        .role-label {
            min-width: 100px;
            font-weight: 600;
            color: #374151;
        }
        .permission-checkbox {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .permission-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .fld-label, .fld-name {
            margin-bottom: 10px;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Create New Form
                </h2>
                <a href="{{ route('admin.forms.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Forms
                </a>
            </div>
        </header>

        <!-- Page Content -->
        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form id="saveFormBuilder" method="POST">
                            @csrf
                            
                            <div class="mb-6">
                                <label for="form_name" class="block text-gray-700 text-sm font-bold mb-2">
                                    Form Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="form_name" 
                                       id="form_name" 
                                       required
                                       placeholder="Enter form name"
                                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>

                            <div class="mb-6">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Form Builder
                                </label>
                                <div id="fb-editor" class="form-builder"></div>
                            </div>

                            <div class="flex items-center justify-between">
                                <button type="submit" 
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Save Form
                                </button>
                                <span id="save-status" class="text-sm text-gray-600"></span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        jQuery(function($) {
            console.log('Initializing Form Builder...');
            
            const roles = @json($roles->pluck('name')->toArray());
            console.log('Available roles:', roles);
            
            // Initialize FormBuilder
            const fbOptions = {
                disableFields: ['autocomplete', 'button', 'header', 'paragraph'],
                disabledActionButtons: ['data', 'save'],
                controlOrder: [
                    'text',
                    'email', 
                    'number',
                    'date',
                    'textarea',
                    'select',
                    'radio-group',
                    'checkbox-group'
                ],
                typeUserAttrs: {
                    text: {
                        permissions: {
                            label: 'Permissions',
                            value: '',
                            type: 'hidden'
                        }
                    }
                }
            };

            const formBuilder = $('#fb-editor').formBuilder(fbOptions);
            
            // Wait for form builder to be ready
            setTimeout(function() {
                console.log('Form Builder initialized');
                
                // Add permission controls when field is added or edited
                $(document).on('click', '.field-edit', function() {
                    setTimeout(function() {
                        addPermissionControls();
                    }, 100);
                });
                
            }, 500);

            // Function to add permission controls
            function addPermissionControls() {
                $('.fld-label').each(function() {
                    const $fieldEditPanel = $(this).closest('.field-edit-panel');
                    
                    // Check if permissions panel already exists
                    if ($fieldEditPanel.find('.permissions-panel').length === 0) {
                        const fieldData = getFieldData($(this));
                        const permissionsHtml = createPermissionsPanel(fieldData);
                        $fieldEditPanel.append(permissionsHtml);
                        
                        // Bind change events
                        bindPermissionEvents($fieldEditPanel);
                    }
                });
            }

            // Get field data from the edit panel
            function getFieldData($element) {
                const $panel = $element.closest('.field-edit-panel');
                const fieldName = $panel.find('.fld-name input').val() || '';
                
                return {
                    name: fieldName,
                    permissions: {}
                };
            }

            // Create permissions panel HTML
            function createPermissionsPanel(fieldData) {
                let html = '<div class="permissions-panel">';
                html += '<h4 style="margin-bottom: 12px; font-weight: bold; color: #1f2937;">Field Permissions</h4>';
                
                roles.forEach(role => {
                    const isAdmin = role === 'admin';
                    const defaultView = true;
                    const defaultWrite = isAdmin;
                    
                    html += `
                        <div class="permission-row">
                            <span class="role-label">${role.charAt(0).toUpperCase() + role.slice(1)}:</span>
                            <label class="permission-checkbox">
                                <input type="checkbox" 
                                       class="perm-view" 
                                       data-role="${role}" 
                                       ${defaultView ? 'checked' : ''}>
                                <span>View</span>
                            </label>
                            <label class="permission-checkbox">
                                <input type="checkbox" 
                                       class="perm-write" 
                                       data-role="${role}" 
                                       ${defaultWrite ? 'checked' : ''}>
                                <span>Write</span>
                            </label>
                        </div>
                    `;
                });
                
                html += '</div>';
                return html;
            }

            // Bind permission checkbox events
            function bindPermissionEvents($panel) {
                $panel.find('.perm-view, .perm-write').off('change').on('change', function() {
                    const $checkbox = $(this);
                    const role = $checkbox.data('role');
                    const $row = $checkbox.closest('.permission-row');
                    
                    // If write is checked, automatically check view
                    if ($checkbox.hasClass('perm-write') && $checkbox.is(':checked')) {
                        $row.find('.perm-view').prop('checked', true);
                    }
                    
                    console.log(`Permission changed for role ${role}`);
                });
            }

            // Form submission
            $('#saveFormBuilder').on('submit', function(e) {
                e.preventDefault();
                
                const formName = $('#form_name').val().trim();
                
                if (!formName) {
                    alert('Please enter a form name');
                    return;
                }
                
                $('#save-status').text('Saving...');
                
                // Get form data from builder
                const formData = formBuilder.actions.getData();
                console.log('Form data from builder:', formData);
                
                // Parse and enhance with permissions
                let formFields = [];
                try {
                    formFields = typeof formData === 'string' ? JSON.parse(formData) : formData;
                } catch(e) {
                    console.error('Error parsing form data:', e);
                    alert('Error preparing form data');
                    return;
                }
                
                // Add permissions to each field
                $('.permissions-panel').each(function(index) {
                    if (formFields[index]) {
                        const $panel = $(this);
                        const permissions = {};
                        
                        roles.forEach(role => {
                            const $row = $panel.find(`.perm-view[data-role="${role}"]`).closest('.permission-row');
                            permissions[role] = {
                                view: $row.find('.perm-view').is(':checked'),
                                write: $row.find('.perm-write').is(':checked')
                            };
                        });
                        
                        formFields[index].permissions = permissions;
                    }
                });
                
                console.log('Final form data with permissions:', formFields);
                
                // Submit to server
                $.ajax({
                    url: '{{ route("admin.forms.store") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        form_name: formName,
                        form_json: JSON.stringify(formFields)
                    },
                    success: function(response) {
                        console.log('Success response:', response);
                        $('#save-status').text('Saved successfully!').addClass('text-green-600');
                        
                        if (response.success && response.redirect) {
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 1000);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error response:', xhr);
                        $('#save-status').text('Error saving form').addClass('text-red-600');
                        
                        let errorMessage = 'Error saving form. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        alert(errorMessage);
                    }
                });
            });
        });
    </script>
</body>
</html>
