<?php


namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Http\Request;

class FormSubmissionController extends Controller
{
    public function index()
    {
        $forms = Form::where('is_active', true)->get();
        return view('forms.index', compact('forms'));
    }

    public function show(Form $form)
    {
        if (!$form->is_active) {
            abort(404);
        }

        $userRole = auth()->user()->role->name ?? 'guest';
        $formFields = json_decode($form->form_json, true);

        return view('forms.show', compact('form', 'userRole', 'formFields'));
    }

    public function store(Request $request, Form $form)
    {
        $userRole = auth()->user()->role->name ?? 'guest';
        $formFields = json_decode($form->form_json, true);
        
        // Filter submitted data based on write permissions
        $filteredData = [];
        
        foreach ($formFields as $field) {
            $fieldName = $field['name'] ?? $field['label'];
            $permissions = $field['permissions'] ?? [];
            
            $rolePermission = $permissions[$userRole] ?? ['view' => false, 'write' => false];
            
            // Only save data if user has write permission
            if ($rolePermission['write'] && $request->has($fieldName)) {
                $filteredData[$fieldName] = $request->input($fieldName);
            }
        }

        FormSubmission::create([
            'form_id' => $form->id,
            'user_id' => auth()->id(),
            'submission_json' => json_encode($filteredData),
        ]);

        return redirect()->route('submissions.my')
            ->with('success', 'Form submitted successfully!');
    }

    public function mySubmissions()
    {
        $submissions = FormSubmission::with('form')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('submissions.my', compact('submissions'));
    }

    public function adminIndex()
    {
        $submissions = FormSubmission::with(['form', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.submissions.index', compact('submissions'));
    }

    public function adminShow(FormSubmission $submission)
    {
        $submission->load(['form', 'user']);
        return view('admin.submissions.show', compact('submission'));
    }
}