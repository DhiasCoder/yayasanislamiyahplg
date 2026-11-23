<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use App\Models\Yayasan;
use App\Models\Sekolah;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $yayasan = Yayasan::firstOrCreate(
            ['nama_yayasan' => 'Yayasan Pendidikan Demo']
        );

        $sekolahData = [
            ['nama_sekolah' => 'SMP Demo', 'kode_sekolah' => 'SMP01', 'jenjang' => 'SMP'],
            ['nama_sekolah' => 'SMA Demo', 'kode_sekolah' => 'SMA01', 'jenjang' => 'SMA'],
            ['nama_sekolah' => 'SMK Demo', 'kode_sekolah' => 'SMK01', 'jenjang' => 'SMK'],
        ];

        $sekolahs = [];

        foreach ($sekolahData as $data) {
            $sekolahs[$data['jenjang']] = Sekolah::firstOrCreate(
                ['kode_sekolah' => $data['kode_sekolah']],
                [
                    'yayasan_id' => $yayasan->id,
                    'nama_sekolah' => $data['nama_sekolah'],
                    'jenjang' => $data['jenjang'],
                    'is_active' => true,
                ]
            );
        }

        $roleNames = [
            'super_admin',
            'admin',
            'kepala_sekolah',
            'guru',
            'siswa',
            'staff',
        ];

        $roles = [];

        foreach ($roleNames as $name) {
            $roles[$name] = Role::firstOrCreate([
                'nama_role' => $name,
            ]);
        }

        $accounts = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@demo.test',
                'password' => 'admin123',
                'role' => 'super_admin',
                'sekolah' => null,
            ],
            [
                'name' => 'Admin SMP',
                'email' => 'admin.smp@demo.test',
                'password' => 'admin123',
                'role' => 'admin',
                'sekolah' => 'SMP',
            ],
            [
                'name' => 'Kepala Sekolah SMA',
                'email' => 'kepsek.sma@demo.test',
                'password' => 'admin123',
                'role' => 'kepala_sekolah',
                'sekolah' => 'SMA',
            ],
            [
                'name' => 'Guru SMA',
                'email' => 'guru.sma@demo.test',
                'password' => 'guru123',
                'role' => 'guru',
                'sekolah' => 'SMA',
            ],
            [
                'name' => 'Siswa SMK',
                'email' => 'siswa.smk@demo.test',
                'password' => 'siswa123',
                'role' => 'siswa',
                'sekolah' => 'SMK',
            ],
            [
                'name' => 'Staff SMP',
                'email' => 'staff.smp@demo.test',
                'password' => 'staff123',
                'role' => 'staff',
                'sekolah' => 'SMP',
            ],
        ];

        foreach ($accounts as $account) {
            $user = User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make($account['password']),
                ]
            );

            UserRole::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'role_id' => $roles[$account['role']]->id,
                    'sekolah_id' => $account['sekolah']
                        ? $sekolahs[$account['sekolah']]->id
                        : null,
                ]
            );
        }
    }
}
