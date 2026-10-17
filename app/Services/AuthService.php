<?php

namespace App\Services;

use App\Enums\StatusAkun;
use App\Models\Admin;
use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function registerUser(array $data): User
    {
        $data['email'] = strtolower($data['email']);
        $data['status_akun'] = StatusAkun::AKTIF;
        $data['tanggal_daftar'] = now();

        return User::create($data);
    }

    /**
     * @return array{user: User, token: string}
     */
    public function loginUser(string $email, string $password): array
    {
        $user = User::where('email', strtolower($email))->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            abort(401, 'Email atau kata sandi salah.');
        }

        if ($user->status_akun !== StatusAkun::AKTIF) {
            abort(403, 'Akun tidak aktif.');
        }

        $token = $user->createToken('user-token', ['user'])->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function logoutUser(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * @return array{admin: Admin, token: string}
     */
    public function loginAdmin(string $email, string $password): array
    {
        $admin = Admin::where('email', strtolower($email))->first();

        if (! $admin || ! Hash::check($password, $admin->password)) {
            LogAktivitas::create([
                'aktor_tipe' => 'admin',
                'aktor_id' => $admin?->id_admin,
                'aksi' => 'login_gagal',
                'deskripsi' => 'Percobaan login admin gagal untuk email: ' . $email,
            ]);

            abort(401, 'Email atau kata sandi salah.');
        }

        $token = $admin->createToken('admin-token', ['admin'])->plainTextToken;

        LogAktivitas::create([
            'aktor_tipe' => 'admin',
            'aktor_id' => $admin->id_admin,
            'aksi' => 'login_berhasil',
            'deskripsi' => 'Admin berhasil login.',
        ]);

        return ['admin' => $admin, 'token' => $token];
    }

    public function logoutAdmin(Admin $admin): void
    {
        $admin->currentAccessToken()->delete();
    }

    public function updateProfile(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh();
    }

    public function changePassword(User $user, string $oldPassword, string $newPassword): void
    {
        if (! Hash::check($oldPassword, $user->password)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'password_lama' => ['Kata sandi lama tidak sesuai.'],
            ]);
        }

        $user->update(['password' => $newPassword]);

        // Cabut semua token lain
        $currentTokenId = $user->currentAccessToken()->id;
        $user->tokens()->where('id', '!=', $currentTokenId)->delete();

        LogAktivitas::create([
            'aktor_tipe' => 'user',
            'aktor_id' => $user->id_user,
            'aksi' => 'ubah_kata_sandi',
            'deskripsi' => 'User mengubah kata sandi.',
        ]);
    }
}
