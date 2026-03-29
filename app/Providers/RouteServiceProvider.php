use App\Models\User;
use Illuminate\Support\Facades\Route;

public function boot(): void
{
    parent::boot();

    Route::bind('user', function ($value) {
        return User::withTrashed()->findOrFail($value);
    });
}
