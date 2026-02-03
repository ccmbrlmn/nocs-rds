use Illuminate\Support\Facades\Hash;
use App\Models\User;

public function storeAdmin(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|confirmed|min:8',
        'admin_key' => 'required|string',
    ]);

    if ($request->admin_key !== config('app.admin_register_key')) {
        return back()->withErrors([
            'admin_key' => 'Invalid admin registration key.',
        ]);
    }

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'admin',
    ]);

    return redirect('/login')->with('success', 'Admin account created successfully.');
}
