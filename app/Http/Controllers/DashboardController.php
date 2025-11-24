
namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->isAdmin()) {
            return $this->adminDashboard();
        }

        return $this->userDashboard();
    }

    protected function userDashboard()
    {
        $data = [
            'availableForms' => Form::where('is_active', true)->count(),
            'mySubmissions' => auth()->user()->submissions()->count(),
        ];

        return view('dashboard', $data);
    }

    protected function adminDashboard()
    {
        $data = [
            'totalUsers' => User::count(),
            'totalForms' => Form::count(),
            'totalSubmissions' => FormSubmission::count(),
            'totalRoles' => Role::count(),
            'recentUsers' => User::with('role')->latest()->take(5)->get(),
            'recentForms' => Form::with('creator')->latest()->take(5)->get(),
            'recentSubmissions' => FormSubmission::with(['form', 'user'])->latest()->take(5)->get(),
        ];

        return view('dashboard', $data);
    }
}