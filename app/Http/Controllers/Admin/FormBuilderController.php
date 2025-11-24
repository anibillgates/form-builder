<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\Role;
use Illuminate\Http\Request;

class FormBuilderController extends Controller
{
    public function index()
    {
        $forms = Form::with('creator')->orderBy('created_at', 'desc')->get();
        return view('admin.forms.index', compact('forms'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.forms.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'form_name' => 'required|string|max:255',
            'form_json' => 'required|json',
        ]);

        Form::create([
            'form_name' => $request->form_name,
            'form_json' => $request->form_json,
            'created_by' => auth()->id(),
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Form created successfully!',
            'redirect' => route('admin.forms.index')
        ]);
    }

    public function edit(Form $form)
    {
        $roles = Role::all();
        return view('admin.forms.edit', compact('form', 'roles'));
    }

    public function update(Request $request, Form $form)
    {
        $request->validate([
            'form_name' => 'required|string|max:255',
            'form_json' => 'required|json',
        ]);

        $form->update([
            'form_name' => $request->form_name,
            'form_json' => $request->form_json,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Form updated successfully!',
            'redirect' => route('admin.forms.index')
        ]);
    }

    public function destroy(Form $form)
    {
        $form->delete();

        return redirect()->route('admin.forms.index')
            ->with('success', 'Form deleted successfully!');
    }

    public function toggleStatus(Form $form)
    {
        $form->update(['is_active' => !$form->is_active]);

        return back()->with('success', 'Form status updated successfully!');
    }
}
