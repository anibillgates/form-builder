<!-- resources/views/admin/forms/create.blade.php -->

<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <?php echo e(__('Create New Form')); ?>

            </h2>
            <a href="<?php echo e(route('admin.forms.index')); ?>" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Forms
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form id="saveFormBuilder" method="POST" action="<?php echo e(route('admin.forms.store')); ?>">
                        <?php echo csrf_field(); ?>
                        
                        <div class="mb-4">
                            <label for="form_name" class="block text-gray-700 text-sm font-bold mb-2">
                                Form Name
                            </label>
                            <input type="text" name="form_name" id="form_name" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Form Builder
                            </label>
                            <div id="fb-editor"></div>
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Save Form
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css">
    <link rel="stylesheet" href="https://formbuilder.online/assets/css/form-builder.min.css">
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://formbuilder.online/assets/js/form-builder.min.js"></script>
    
    <script>
        jQuery(function($) {
            console.log('jQuery loaded:', typeof jQuery !== 'undefined');
            console.log('FormBuilder loaded:', typeof $.fn.formBuilder !== 'undefined');
            console.log('Editor element:', document.getElementById('fb-editor'));
            
            const roles = <?php echo json_encode($roles->pluck('name')->toArray(), 15, 512) ?>;
            console.log('Roles:', roles);
            
            // Custom template for permissions
            const permissionsTemplate = function(field) {
                let html = '<div class="permissions-panel" style="margin-top: 10px; padding: 10px; background: #f5f5f5; border-radius: 4px;">';
                html += '<h4 style="margin-bottom: 10px; font-weight: bold;">Field Permissions</h4>';
                
                roles.forEach(role => {
                    html += `
                        <div style="margin-bottom: 8px; display: flex; align-items: center; gap: 20px;">
                            <span style="min-width: 100px; font-weight: 500;">${role.charAt(0).toUpperCase() + role.slice(1)}:</span>
                            <label style="display: flex; align-items: center; gap: 5px;">
                                <input type="checkbox" class="perm-view" data-role="${role}" ${field.permissions && field.permissions[role] && field.permissions[role].view ? 'checked' : ''}>
                                <span>View</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 5px;">
                                <input type="checkbox" class="perm-write" data-role="${role}" ${field.permissions && field.permissions[role] && field.permissions[role].write ? 'checked' : ''}>
                                <span>Write</span>
                            </label>
                        </div>
                    `;
                });
                
                html += '</div>';
                return html;
            };

            // Initialize FormBuilder
            const fbEditor = document.getElementById('fb-editor');
            
            if (!fbEditor) {
                console.error('FormBuilder container not found!');
                return;
            }
            
            console.log('Initializing FormBuilder...');
            
            const formBuilder = $(fbEditor).formBuilder({
                disableFields: ['autocomplete', 'button', 'file', 'header', 'paragraph'],
                onAddField: function(fieldId) {
                    const field = formBuilder.actions.getFieldData(fieldId);
                    
                    // Initialize permissions for the field
                    if (!field.permissions) {
                        field.permissions = {};
                        roles.forEach(role => {
                            field.permissions[role] = { view: true, write: role === 'admin' };
                        });
                    }
                    
                    // Add permissions panel
                    setTimeout(() => {
                        const fieldElement = $(`[data-field-id="${fieldId}"]`);
                        const editPanel = fieldElement.find('.fld-edit-panel');
                        
                        if (editPanel.length && !editPanel.find('.permissions-panel').length) {
                            editPanel.append(permissionsTemplate(field));
                            
                            // Bind permission checkbox events
                            editPanel.find('.perm-view, .perm-write').on('change', function() {
                                const role = $(this).data('role');
                                const permType = $(this).hasClass('perm-view') ? 'view' : 'write';
                                
                                if (!field.permissions) {
                                    field.permissions = {};
                                }
                                if (!field.permissions[role]) {
                                    field.permissions[role] = { view: false, write: false };
                                }
                                
                                field.permissions[role][permType] = $(this).is(':checked');
                                
                                // If write is checked, automatically check view
                                if (permType === 'write' && $(this).is(':checked')) {
                                    field.permissions[role].view = true;
                                    editPanel.find(`.perm-view[data-role="${role}"]`).prop('checked', true);
                                }
                                
                                // Update field data
                                formBuilder.actions.setFieldData(fieldId, field);
                            });
                        }
                    }, 100);
                }
            });

            // Form submission
            $('#saveFormBuilder').on('submit', function(e) {
                e.preventDefault();

                const formName = $('#form_name').val();
                const formDataJson = formBuilder.actions.getData('json');

                const formData = new FormData();
                formData.append('_token', '<?php echo e(csrf_token()); ?>');
                formData.append('form_name', formName);
                formData.append('form_json', formDataJson);

                $.ajax({
                    url: '<?php echo e(route("admin.forms.store")); ?>',
                    type: 'POST',
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            window.location.href = response.redirect;
                        }
                    },
                    error: function(xhr) {
                        alert('Error saving form. Please try again.');
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH E:\PDMR Softwares\Projects\dynamic-form-builder\resources\views/admin/forms/create.blade.php ENDPATH**/ ?>