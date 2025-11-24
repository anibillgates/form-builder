
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Available Forms') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($forms as $form)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-2">{{ $form->form_name }}</h3>
                            <p class="text-gray-600 text-sm mb-4">
                                Created {{ $form->created_at->diffForHumans() }}
                            </p>
                            <a href="{{ route('forms.show', $form) }}" 
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-block">
                                Fill Form
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center text-gray-500 py-8">
                        No forms available at the moment.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
