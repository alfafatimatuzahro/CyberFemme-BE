<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Product;
use App\Models\LoginLog;
use App\Models\Transaction;
use App\Models\FraudRule;
use App\Models\SecurityNotification;
use App\Http\Controllers\User\TransactionController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

Route::get('/', fn() => view('landing'));

Route::get('/login', fn() => view('login'));

Route::get('/login-user', fn() => view('login-user'));

Route::get('/register', fn() => view('register'));

Route::post('/register', function (Request $request) {

    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'security_question' => 'required',
        'security_answer' => 'required',
    ]);

    User::create([
        'username' => $request->name,
        'nama_umkm' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),

        'role' => 'admin',

        'security_question' => $request->security_question,
        'security_answer' => $request->security_answer,

        'is_active' => true,
    ]);

    return redirect('/login')
        ->with('success', 'Registrasi berhasil');
});

Route::post('/login', function (Request $request) {

    $user = User::where('nama_umkm', $request->nama_umkm)
        ->where('role', 'admin')
        ->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return back()->withErrors([
            'login' => 'Nama UMKM atau password salah'
        ]);
    }

    Auth::login($user);

    $request->session()->regenerate();

    return redirect('/dashboard');
});

Route::post('/login-user', function (Request $request) {

    $user = User::where(
        'email',
        $request->email
    )
    ->where('role', 'kasir')
    ->first();

    if (
        !$user ||
        !Hash::check(
            $request->password,
            $user->password
        )
    ) {
        return back()->withErrors([
            'login' => 'Email atau password salah'
        ]);
    }

    Auth::login($user);

    LoginLog::create([
        'user_id' => $user->id,
        'ip_address' => $request->ip(),
        'lokasi' => 'Local',
        'user_agent' => $request->userAgent(),
        'status' => 'sukses',
        'force_logout' => false
    ]);

    return redirect('/user-dashboard');

})->name('login.user');

Route::get('/dashboard', function (Request $request) {

    $user = auth()->user();

    $jumlahKasir = User::where('admin_id', $user->id)
        ->where('role', 'kasir')
        ->count();

    $jumlahProduk = Product::where('admin_id', $user->id)
        ->count();

    $jumlahTransaksi = Transaction::where('admin_id', $user->id)
        ->count();

    $transaksiSukses = Transaction::where('admin_id', $user->id)
        ->where('status', 'sukses')
        ->count();

    $transaksiMencurigakan = Transaction::where('admin_id', $user->id)
        ->where('status', 'mencurigakan')
        ->count();

        $query = Transaction::with('kasir');

        if ($request->nama) {

            $query->where(function ($q) use ($request) {

                $q->where(
                    'invoice_id',
                    'like',
                    '%' . $request->nama . '%'
                )

                ->orWhere(
                    'nama_pelanggan',
                    'like',
                    '%' . $request->nama . '%'
                );

            });

        }

        if ($request->tanggal) {

            $query->whereDate(
                'created_at',
                $request->tanggal
            );

        }

        if ($request->status) {

            $query->where(
                'status',
                $request->status
            );

        }

        $query->where(
            'admin_id',
            $user->id
        );

        $transaksis = $query
            ->latest()
            ->get();

        $omzetHariIni = Transaction::where(
            'admin_id',
            $user->id
        )->sum('total');
    
        $notifications = SecurityNotification::latest()
        ->take(10)
        ->get();

    return view('admin-dashboard', compact(
        'user',
        'jumlahKasir',
        'jumlahProduk',
        'jumlahTransaksi',
        'transaksiSukses',
        'transaksiMencurigakan',
        'omzetHariIni',
        'transaksis',
        'notifications'
    ));

})->middleware(['auth','role:admin'])->name('dashboard');

Route::get('/user-dashboard', function () {

    $log = LoginLog::where(
        'user_id',
        auth()->id()
    )->latest()->first();

    if ($log && $log->force_logout) {

        Auth::logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();

        return redirect('/login')
            ->withErrors([
                'login' => 'Sesi Anda telah dihentikan oleh Admin.'
            ]);
    }

    $today = today();

    $totalTransaksi = Transaction::where(
        'kasir_id',
        auth()->id()
    )
    ->whereDate('created_at', $today)
    ->count();

    $totalOmzet = Transaction::where(
        'kasir_id',
        auth()->id()
    )
    ->where('status', 'sukses')
    ->whereDate('created_at', $today)
    ->sum('total');

    return view('dashboard', compact(
        'totalTransaksi',
        'totalOmzet'
    ));

})->middleware('auth')->name('user.dashboard');

Route::get('/pos', function () {

        $user = auth()->user();

        $products = Product::where(
        'admin_id',
        $user->admin_id ?? $user->id
    )
    ->where('is_active', true)
    ->orderBy('nama')
    ->get();

    return view('pos', compact('products'));

})->middleware('auth')->name('pos');

Route::post(
    '/transactions',
    [TransactionController::class, 'store']
)->middleware('auth')
->name('transactions.store');

Route::get('/user-history', function () {

$user = auth()->user();

    $transaksis = Transaction::where(
        'kasir_id',
        $user->id
    )->latest()->get();

    $totalPenjualan = Transaction::where(
        'kasir_id',
        auth()->id()
    )->sum('total');

    $transaksiSukses = Transaction::where(
        'kasir_id',
        auth()->id()
    )
    ->where('status','sukses')
    ->count();

    $anomali = Transaction::where(
        'kasir_id',
        auth()->id()
    )
    ->whereIn('status',['mencurigakan','tertahan'])
    ->count();

    return view(
        'history',
        compact('transaksis')
    );

})->middleware('auth')->name('user-history');

Route::get('/user-profile', function () {

    $user = auth()->user();

    return view(
        'profil',
        compact('user')
    );

})->middleware('auth')->name('user-profile');



Route::get('/ringkasan-keamanan', function () {

    $kasirIds = User::where(
        'admin_id',
        auth()->id()
    )
    ->where('role', 'kasir')
    ->pluck('id');

    $totalLogin = LoginLog::whereIn(
        'user_id',
        $kasirIds
    )->count();

    $loginGagal = LoginLog::whereIn(
        'user_id',
        $kasirIds
    )
    ->where('status', 'gagal')
    ->count();

    $ancaman = LoginLog::whereIn(
        'user_id',
        $kasirIds
    )
    ->where('status', 'mencurigakan')
    ->count();

    $sesiAktif = LoginLog::whereIn(
        'user_id',
        $kasirIds
    )
    ->where('force_logout', 0)
    ->count();

    $logs = LoginLog::whereIn(
        'user_id',
        $kasirIds
    )
    ->latest()
    ->get();

    return view(
        'ringkasan-keamanan',
        compact(
            'totalLogin',
            'loginGagal',
            'ancaman',
            'sesiAktif',
            'logs'
        )
    );

})->middleware(['auth','role:admin'])->name('ringkasan-keamanan');

Route::get('/manajemen-karyawan', function () {

    $kasirs = User::where(
        'admin_id',
        auth()->id()
    )
    ->where('role', 'kasir')
    ->get();

    $totalKasir = $kasirs->count();

    $kasirAktif = $kasirs
        ->where('status', 'aktif')
        ->count();

    $login24Jam = LoginLog::whereIn(
        'user_id',
        $kasirs->pluck('id')
    )
    ->where(
        'created_at',
        '>=',
        now()->subDay()
    )
    ->count();

    return view(
        'manajemen-karyawan',
        compact(
            'kasirs',
            'totalKasir',
            'kasirAktif',
            'login24Jam'
        )
    );

})->middleware(['auth','role:admin'])
->name('manajemen-karyawan');

Route::get('/rule-based', function () {

    $rule = FraudRule::where(
        'admin_id',
        auth()->id()
    )->first();

    $jumlahTransaksi = Transaction::where(
        'admin_id',
        auth()->id()
    )->count();

    $jumlahKasus = Transaction::where(
        'admin_id',
        auth()->id()
    )
    ->where('status', 'mencurigakan')
    ->count();

    // AKURASI DETEKSI
    $total = max($jumlahTransaksi, 1);

    $akurasi = round(
        (($jumlahTransaksi - $jumlahKasus) / $total) * 100,
        1
    );

    return view(
        'rule-based',
        compact(
            'rule',
            'jumlahTransaksi',
            'jumlahKasus',
            'akurasi'
        )
    );

})->middleware(['auth','role:admin'])->name('rule-based');

Route::post('/rule-based/update', function (Request $request) {

    $rule = FraudRule::where(
        'admin_id',
        auth()->id()
    )->first();

    $rule->update([
        'batas_nominal_max' => $request->batas_nominal_max,

        'batas_qty_max' => $request->batas_qty_max,

        'jam_buka' => $request->jam_buka,
        'jam_tutup' => $request->jam_tutup,
    ]);

    return back()->with('success', 'Rule berhasil diperbarui');

})->middleware(['auth','role:admin'])->name('rule-based.update');

Route::get('/profil', function () {

    $user = auth()->user();

    return view('profil', compact('user'));

})->middleware(['auth','role:admin'])->name('profil');

Route::post('/profil/foto', function(Request $request){

    $request->validate([
        'profile_photo' => 'required|image'
    ]);

    $path = $request
        ->file('profile_photo')
        ->store('profile', 'public');

    auth()->user()->update([
        'profile_photo' => $path
    ]);

    return back();
});

Route::post('/profil/update', function(Request $request){

    $user = auth()->user();

    $user->update([

        'nama_umkm' => $request->nama_umkm,

        'email' => $request->email,

        'alamat_umkm' => $request->alamat_umkm

    ]);

    if ($request->hasFile('profile_photo')) {

        $path = $request
            ->file('profile_photo')
            ->store('profile', 'public');

        $user->update([
            'profile_photo' => $path
        ]);

    }

    return back()->with(
        'success',
        'Profil berhasil diperbarui'
    );

})->middleware(['auth','role:admin']) ->name('profile.update');

Route::post('/profil/password', function(Request $request){

    $user = auth()->user();

    if (
        !Hash::check(
            $request->password_lama,
            $user->password
        )
    ) {

        return back()->withErrors([
            'password_lama' => 'Password lama salah'
        ]);
    }

    if (
        $user->security_question
        !=
        $request->security_question
    ) {

        return back()->withErrors([
            'security_question' =>
            'Pertanyaan keamanan tidak sesuai'
        ]);
    }

    if (
        strtolower($user->security_answer)
        !=
        strtolower($request->security_answer)
    ) {

        return back()->withErrors([
            'security_answer' =>
            'Jawaban keamanan salah'
        ]);
    }

    $user->update([

        'password' => Hash::make(
            $request->password_baru
        )

    ]);

    return back()->with(
        'success',
        'Password berhasil diubah'
    );

})->middleware('auth');

Route::get('/history', function () {
    $user = auth()->user();
    $totalPenjualan = Transaction::where('kasir_id', $user->id)
        ->sum('total');

    $transaksiSukses = Transaction::where('kasir_id', $user->id)
        ->where('status', 'sukses')
        ->count();

    $anomali = Transaction::where('kasir_id', $user->id)
        ->whereIn('status', ['mencurigakan', 'tertahan'])
        ->count();

    $transaksis = Transaction::where(
    'kasir_id',
    auth()->id()
)->latest()->get();


    return view('history', compact(
    'transaksis',
    'totalPenjualan',
    'transaksiSukses',
    'anomali'
    ));

})->middleware('auth')->name('history');

Route::get('/profile', function () {
    return view('profile');
})->middleware('auth')->name('profile');

Route::post('/transaksi/{id}/approve', function ($id) {

    $trx = Transaction::findOrFail($id);

    $trx->update([
        'status' => 'sukses',
        'admin_reviewed' => true,
        'reviewed_by' => auth()->id(),
        'reviewed_at' => now(),
    ]);

    return back();

})->middleware(['auth','role:admin'])->name('transaksi.approve');

Route::get('/transaksi/{id}', function ($id) {

    $trx = Transaction::with([
        'kasir',
        'items'
    ])->findOrFail($id);

    return response()->json($trx);

})->middleware(['auth','role:admin']);

Route::post('/kasir/store', function(Request $request){

    User::create([

        'username' => $request->username,

        'email' => $request->email,

        'password' => Hash::make(
            $request->password
        ),

        'role' => 'kasir',

        'admin_id' => auth()->id(),

        'security_question' =>
            $request->security_question,

        'security_answer' =>
            $request->security_answer,

        'is_active' => 1,

        'temp_password' =>
            $request->password

    ]);

    return back()->with(
        'success',
        'Kasir berhasil ditambahkan'
    );

})->middleware(['auth','role:admin']);

Route::post('/kasir/update', function(Request $request){

    $kasir = User::findOrFail(
        $request->id
    );

    $data = [

        'username' => $request->username,

        'email' => $request->email

    ];

    if($request->password){

        $data['password'] = Hash::make(
            $request->password
        );

    }

    $kasir->update($data);

    return back()->with(
        'success',
        'Data kasir berhasil diperbarui'
    );


});

Route::delete('/kasir/delete/{id}', function ($id) {

    User::findOrFail($id)->delete();

    return back()->with(
        'success',
        'Kasir berhasil dihapus'
    );

})->middleware(['auth','role:admin']);


Route::post('/force-logout/{id}', function ($id) {

    $kasir = User::findOrFail($id);

    LoginLog::where(
        'user_id',
        $kasir->id
    )
    ->latest()
    ->first()
    ?->update([
        'force_logout' => true,
        'logout_at' => now()
    ]);

    return back()->with(
        'success',
        'Kasir berhasil dipaksa logout'
    );

})->middleware(['auth','role:admin']);


Route::get('/logout', function () {

    LoginLog::where(
        'user_id',
        auth()->id()
    )
    ->latest()
    ->first()
    ?->update([
        'logout_at' => now()
    ]);

    Auth::logout();

    request()->session()->invalidate();

    request()->session()->regenerateToken();

    return redirect('/');

})->name('logout');

