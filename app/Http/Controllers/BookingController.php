namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        // Mengambil data peminjaman user saat ini, mirip dengan getBookings() di Next.js
        $bookings = Booking::where('user_id', Auth::id())
                            ->latest()
                            ->get();

        // Menghitung statistik untuk kartu di bagian atas
        $stats = [
            'total' => $bookings->count(),
            'pending' => $bookings->where('status', 'pending')->count(),
            'approved' => $bookings->whereIn('status', ['approved', 'active'])->count(),
            'completed' => $bookings->where('status', 'completed')->count(),
        ];

        return view('user.bookings.index', compact('bookings', 'stats'));
    }
}