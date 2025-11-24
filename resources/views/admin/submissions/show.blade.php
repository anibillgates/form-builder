
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Submission Details') }}
            </h2>
            <a href="{{ route('admin.submissions.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Submissions
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Submission Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600">Form:</p>
                            <p class="font-semibold">{{ $submission->form->form_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Submitted By:</p>
                            <p class="font-semibold">{{ $submission->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">User Email:</p>
                            <p class="font-semibold">{{ $submission->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Submitted At:</p>
                            <p class="font-semibold">{{ $submission->created_at->format('M d, Y H:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Form Data</h3>
                    <div class="space-y-4">
                        @php
                            $data = json_decode($submission->submission_json, true);
                        @endphp
                        
                        @foreach($data as $key => $value)
                            <div class="border-b pb-3">
                                <p class="text-gray-600 text-sm mb-1">{{ ucwords(str_replace('_', ' ', $key)) }}</p>
                                <p class="font-medium">
                                    @if(is_array($value))
                                        {{ implode(', ', $value) }}
                                    @else
                                        {{ $value }}
                                    @endif
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>