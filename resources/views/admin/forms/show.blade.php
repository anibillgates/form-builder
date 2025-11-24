
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $form->form_name }}
            </h2>
            <a href="{{ route('forms.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Forms
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('forms.submit', $form) }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div id="form-container"></div>

                        <div class="mt-6">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Submit Form
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userRole = @json($userRole);
            const formFields = @json($formFields);
            const container = document.getElementById('form-container');
            
            formFields.forEach(field => {
                const permissions = field.permissions || {};
                const rolePermission = permissions[userRole] || { view: false, write: false };
                
                // Skip if user doesn't have view permission
                if (!rolePermission.view) {
                    return;
                }
                
                const fieldDiv = document.createElement('div');
                fieldDiv.className = 'mb-4';
                
                // Create label
                const label = document.createElement('label');
                label.className = 'block text-gray-700 text-sm font-bold mb-2';
                label.textContent = field.label || field.name;
                if (field.required) {
                    label.innerHTML += ' <span class="text-red-500">*</span>';
                }
                fieldDiv.appendChild(label);
                
                // Create input based on field type
                let input;
                const fieldName = field.name || field.label.toLowerCase().replace(/\s+/g, '_');
                
                switch(field.type) {
                    case 'text':
                    case 'email':
                    case 'number':
                    case 'date':
                        input = document.createElement('input');
                        input.type = field.type;
                        input.name = fieldName;
                        input.className = 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline';
                        input.placeholder = field.placeholder || '';
                        if (field.value) input.value = field.value;
                        if (field.required) input.required = true;
                        break;
                        
                    case 'textarea':
                        input = document.createElement('textarea');
                        input.name = fieldName;
                        input.className = 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline';
                        input.rows = field.rows || 4;
                        input.placeholder = field.placeholder || '';
                        if (field.value) input.value = field.value;
                        if (field.required) input.required = true;
                        break;
                        
                    case 'select':
                        input = document.createElement('select');
                        input.name = fieldName;
                        input.className = 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline';
                        
                        const defaultOption = document.createElement('option');
                        defaultOption.value = '';
                        defaultOption.textContent = '-- Select --';
                        input.appendChild(defaultOption);
                        
                        if (field.values) {
                            field.values.forEach(option => {
                                const opt = document.createElement('option');
                                opt.value = option.value;
                                opt.textContent = option.label;
                                if (option.selected) opt.selected = true;
                                input.appendChild(opt);
                            });
                        }
                        if (field.required) input.required = true;
                        break;
                        
                    case 'radio-group':
                        input = document.createElement('div');
                        if (field.values) {
                            field.values.forEach((option, index) => {
                                const radioDiv = document.createElement('div');
                                radioDiv.className = 'mb-2';
                                
                                const radio = document.createElement('input');
                                radio.type = 'radio';
                                radio.name = fieldName;
                                radio.value = option.value;
                                radio.id = `${fieldName}_${index}`;
                                radio.className = 'mr-2';
                                if (option.selected) radio.checked = true;
                                
                                const radioLabel = document.createElement('label');
                                radioLabel.htmlFor = radio.id;
                                radioLabel.textContent = option.label;
                                
                                radioDiv.appendChild(radio);
                                radioDiv.appendChild(radioLabel);
                                input.appendChild(radioDiv);
                            });
                        }
                        break;
                        
                    case 'checkbox-group':
                        input = document.createElement('div');
                        if (field.values) {
                            field.values.forEach((option, index) => {
                                const checkDiv = document.createElement('div');
                                checkDiv.className = 'mb-2';
                                
                                const checkbox = document.createElement('input');
                                checkbox.type = 'checkbox';
                                checkbox.name = `${fieldName}[]`;
                                checkbox.value = option.value;
                                checkbox.id = `${fieldName}_${index}`;
                                checkbox.className = 'mr-2';
                                if (option.selected) checkbox.checked = true;
                                
                                const checkLabel = document.createElement('label');
                                checkLabel.htmlFor = checkbox.id;
                                checkLabel.textContent = option.label;
                                
                                checkDiv.appendChild(checkbox);
                                checkDiv.appendChild(checkLabel);
                                input.appendChild(checkDiv);
                            });
                        }
                        break;
                        
                    default:
                        input = document.createElement('input');
                        input.type = 'text';
                        input.name = fieldName;
                        input.className = 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline';
                }
                
                // Apply read-only if no write permission
                if (!rolePermission.write) {
                    if (input.tagName === 'DIV') {
                        // For radio/checkbox groups
                        const inputs = input.querySelectorAll('input');
                        inputs.forEach(i => i.disabled = true);
                    } else {
                        input.disabled = true;
                    }
                    input.style.backgroundColor = '#f3f4f6';
                }
                
                // Add description if exists
                if (field.description) {
                    const desc = document.createElement('p');
                    desc.className = 'text-gray-600 text-sm mt-1';
                    desc.textContent = field.description;
                    fieldDiv.appendChild(desc);
                }
                
                fieldDiv.appendChild(input);
                container.appendChild(fieldDiv);
            });
        });
    </script>
    @endpush
</x-app-layout>