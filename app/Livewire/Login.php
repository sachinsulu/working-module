<?php
namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $errorMessage = '';

    public function login()
    {
        $validated = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            // Regenerate session to prevent fixation
            // Regenerate session to prevent fixation
            session()->regenerate();
            // Redirect based on role
            if (auth()->user()->hasAnyRole(['super admin', 'dept head', 'mgmt'])) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('client.dashboard');
        }

        $this->errorMessage = 'Invalid credentials. Please try again.';
    }

    public function render()
    {
        return view('livewire.login');
    }
}
?>
