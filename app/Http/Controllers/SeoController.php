<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class SeoController extends Controller
{
    public function home()
    {
        if (Auth::check()) {
            return $this->redirectForRole();
        }

        return view('marketing.home');
    }

    public function freeAttendance()
    {
        return $this->solution('free');
    }

    public function locationAttendance()
    {
        return $this->solution('location');
    }

    public function organizationAttendance()
    {
        return $this->solution('organization');
    }

    public function sitemap()
    {
        $pages = [
            ['path' => '/', 'priority' => '1.0', 'frequency' => 'weekly'],
            ['path' => '/sistem-absensi-gratis', 'priority' => '0.9', 'frequency' => 'monthly'],
            ['path' => '/absensi-berbasis-lokasi', 'priority' => '0.9', 'frequency' => 'monthly'],
            ['path' => '/absensi-perusahaan-umkm-sekolah', 'priority' => '0.8', 'frequency' => 'monthly'],
        ];

        return response()
            ->view('marketing.sitemap', compact('pages'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    protected function solution(string $key)
    {
        $solutions = [
            'free' => [
                'slug' => 'sistem-absensi-gratis',
                'eyebrow' => 'Mulai tanpa biaya',
                'title' => 'Sistem Absensi Gratis untuk UMKM dan Tim',
                'seoTitle' => 'Sistem Absensi Gratis untuk UMKM & Tim | MWork',
                'description' => 'Gunakan sistem absensi gratis MWork untuk mencatat kehadiran, lokasi GPS, izin, dan laporan harian anggota dalam satu workspace.',
                'intro' => 'MWork membantu tim kecil memulai pencatatan kehadiran tanpa proses rumit. Buat workspace, undang anggota dengan kode perusahaan, lalu kelola absensi dari ponsel atau komputer.',
                'benefits' => [
                    ['title' => 'Workspace gratis', 'text' => 'Mulai mengelola anggota dan absensi tanpa biaya berlangganan di awal.'],
                    ['title' => 'Pendaftaran anggota mudah', 'text' => 'Anggota bergabung menggunakan kode perusahaan dan baru aktif setelah disetujui admin.'],
                    ['title' => 'Bisa dipasang seperti aplikasi', 'text' => 'MWork dapat dipasang sebagai PWA di perangkat yang mendukung tanpa unduhan dari toko aplikasi.'],
                ],
                'faq' => [
                    ['question' => 'Apakah MWork dapat digunakan gratis?', 'answer' => 'Ya. MWork menyediakan paket awal gratis untuk mencoba pengelolaan anggota dan absensi sebelum memilih paket lanjutan.'],
                    ['question' => 'Apakah anggota harus memasang aplikasi?', 'answer' => 'Tidak. Anggota dapat menggunakan browser, dan pada perangkat yang mendukung MWork juga dapat dipasang sebagai aplikasi web.'],
                ],
            ],
            'location' => [
                'slug' => 'absensi-berbasis-lokasi',
                'eyebrow' => 'Kehadiran dengan GPS',
                'title' => 'Sistem Absensi Berbasis Lokasi GPS',
                'seoTitle' => 'Sistem Absensi Berbasis Lokasi GPS | MWork',
                'description' => 'MWork adalah sistem absensi berdasarkan lokasi GPS untuk WFO, WFH, kerja fleksibel, dan laporan harian dengan riwayat posisi kehadiran.',
                'intro' => 'Setiap proses absensi MWork merekam koordinat aktual. Perusahaan dapat menetapkan lokasi kerja dan radius untuk membantu memastikan anggota melakukan absensi dari area yang sesuai.',
                'benefits' => [
                    ['title' => 'Koordinat tercatat', 'text' => 'Lokasi saat masuk, pulang, izin, atau laporan harian tersimpan bersama catatan kehadiran.'],
                    ['title' => 'Radius lokasi kerja', 'text' => 'Admin dapat membuat titik lokasi dan menentukan radius area absensi sesuai kebutuhan.'],
                    ['title' => 'Deteksi lokasi tanpa refresh', 'text' => 'Halaman kembali mendeteksi GPS saat lokasi perangkat diaktifkan tanpa harus dimuat ulang.'],
                ],
                'faq' => [
                    ['question' => 'Apakah semua jenis absensi merekam lokasi?', 'answer' => 'Ya. Absensi masuk, pulang, izin, dan laporan harian dirancang untuk menyimpan posisi GPS saat dikirim.'],
                    ['question' => 'Apakah MWork mendukung WFO dan WFH?', 'answer' => 'Ya. Admin dapat membuat aturan terjadwal, fleksibel, WFO, WFH, maupun laporan harian dan memberikannya kepada anggota.'],
                ],
            ],
            'organization' => [
                'slug' => 'absensi-perusahaan-umkm-sekolah',
                'eyebrow' => 'Satu platform untuk berbagai organisasi',
                'title' => 'Absensi Online untuk Perusahaan, UMKM, Kantor, dan Sekolah',
                'seoTitle' => 'Absensi Perusahaan, UMKM & Sekolah | MWork',
                'description' => 'Kelola absensi karyawan, anggota UMKM, staf kantor, dan peserta sekolah dengan workspace terpisah, tim, atasan, operator, dan audit aktivitas.',
                'intro' => 'Setiap organisasi memiliki workspace dan data terpisah. Admin mengatur anggota, tim, atasan, lokasi, izin, aturan kehadiran, serta laporan sesuai pola kerja organisasinya.',
                'benefits' => [
                    ['title' => 'Data antarorganisasi terpisah', 'text' => 'Akun, lokasi, aturan, dan audit aktivitas hanya dapat dilihat oleh organisasi yang bersangkutan.'],
                    ['title' => 'Peran kerja lengkap', 'text' => 'Tersedia akses untuk admin, operator, atasan, dan anggota dengan halaman sesuai tanggung jawabnya.'],
                    ['title' => 'Tim dan pemantauan atasan', 'text' => 'Atasan dapat memantau kehadiran anggota dari tim yang telah diberikan oleh admin.'],
                ],
                'faq' => [
                    ['question' => 'Apakah satu MWork dapat dipakai banyak perusahaan?', 'answer' => 'Ya. MWork dirancang sebagai sistem multi-workspace sehingga setiap perusahaan atau organisasi mempunyai pengaturan dan data sendiri.'],
                    ['question' => 'Apakah cocok untuk sekolah dan komunitas?', 'answer' => 'Ya. Aturan absensi dan peran dapat disesuaikan untuk perusahaan, UMKM, kantor, sekolah, instansi, maupun komunitas.'],
                ],
            ],
        ];

        abort_unless(isset($solutions[$key]), 404);

        return view('marketing.solution', ['page' => $solutions[$key]]);
    }

    protected function redirectForRole()
    {
        return redirect(match (Auth::user()->role) {
            'superadmin' => '/superadmin/dashboard',
            'admin' => '/admin/dashboard',
            'pegawai' => '/pegawai/rekap-absensi',
            'siswa' => '/siswa/absen',
            'guru' => '/guru/rekap-absensi',
            default => '/login',
        });
    }
}
