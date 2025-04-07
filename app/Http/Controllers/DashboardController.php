<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\PermissionGroup;
use App\Models\User;
use App\Repositories\SettingRepository;
use App\Services\DatabaseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Proposal;
use App\Repositories\ProposalRepository;
use App\Models\ProgramStudi;
use App\Models\Jadwal;
use Carbon\Carbon;

class DashboardController extends StislaController
{
    /**
     * constructor method
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->proposalRepository = new ProposalRepository();
        // $this->middleware('can:Log Aktivitas');
    }

    /**
     * Menampilkan halaman dashboard
     *
     * @return Response
     */
    public function index()
    {
        $widgets = [];
        $user = auth()->user();
        $userProdi = auth()->user()->prodi;
        // $userProdi = json_decode(auth()->user()->prodi, true);
        $prodiName = explode(' ', $userProdi[0], 2)[1]; // Ambil nama_prodi dari prodi pertama
        $programStudi = ProgramStudi::where('nama_prodi', $prodiName)
            ->where('tahun', request('tahun', date('Y')))
            ->first();

        if ($user->can('Pengguna')) {
            $widgets[] = (object) [
                'title' => 'Pengguna',
                'count' => User::count(),
                'bg' => 'primary',
                'icon' => 'users',
                'route' => route('user-management.users.index'),
            ];
        }
        if ($user->can('Role')) {
            $widgets[] = (object) [
                'title' => 'Role',
                'count' => Role::count(),
                'bg' => 'danger',
                'icon' => 'lock',
                'route' => route('user-management.roles.index'),
            ];
        }
        if ($user->can('Permission')) {
            $widgets[] = (object) [
                'title' => 'Permission',
                'count' => Permission::count(),
                'bg' => 'success',
                'icon' => 'key',
                'route' => route('user-management.permissions.index'),
            ];
        }
        if ($user->can('Group Permission')) {
            $widgets[] = (object) [
                'title' => 'Group Permission',
                'count' => PermissionGroup::count(),
                'bg' => 'info',
                'icon' => 'key',
                'route' => route('user-management.permission-groups.index'),
            ];
        }
        if ($user->can('Log Aktivitas')) {
            $widgets[] = (object) [
                'title' => 'Log Aktivitas',
                'count' => ActivityLog::count(),
                'bg' => 'success',
                'icon' => 'clock-rotate-left',
                'route' => route('activity-logs.index'),
            ];
        }

        if ($user->can('Notifikasi')) {
            $widgets[] = (object) [
                'title' => 'Notifikasi',
                'count' => Notification::where('user_id', $user->id)->count(),
                'bg' => 'info',
                'icon' => 'bell',
                'route' => route('notifications.index'),
            ];
        }

        if ($user->can('Backup Database')) {
            $widgets[] = (object) [
                'title' => 'Backup Database',
                'count' => count((new DatabaseService())->getAllBackupMysql()),
                'bg' => 'primary',
                'icon' => 'database',
                'route' => route('backup-databases.index'),
            ];
        }

        $logs = $this->activityLogRepository->getMineLatest();

        $userEmail = auth()->user()->email;
        // Cek jadwal pengajuan proposal
        $jadwalPengajuan = Jadwal::where('keterangan', 'Pengajuan Proposal')->where('tgl_mulai', '<=', now())->where('tgl_selesai', '>=', now())->exists();
        $hasProposal = Proposal::whereHas('kelompoks', function ($query) use ($userEmail) {
            $query->where('anggota_email', $userEmail)->whereYear('created_at', now()->year);
            // $query->where('anggota_email', $userEmail)->where('peran', 'Ketua')->whereYear('created_at', now()->year);
        })
            ->where(function ($query) {
                $query->where('status', '0')->orWhere('status', '1')->orWhere('status', '2');
            })
            ->exists();

        // $canCreateProposal = $hasProposal || $jadwalPengajuan;
        // if (!$jadwalPengajuan) {
        //     // Ambil jadwal pengajuan untuk mendapatkan tanggal
        //     $jadwalInfo = Jadwal::where('keterangan', 'Pengajuan Proposal')->first();

        //     if ($jadwalInfo) {
        //         $tglMulai = Carbon::parse($jadwalInfo->tgl_mulai)->format('d M Y');
        //         $tglSelesai = Carbon::parse($jadwalInfo->tgl_selesai)->format('d M Y');
        //         return redirect()
        //             ->back()
        //             ->with('errorMessage', "Pengajuan proposal hanya dapat dilakukan pada tanggal $tglMulai sampai $tglSelesai");
        //     } else {
        //         return redirect()->route('dashboard')->with('errorMessage', 'Jadwal pengajuan proposal belum ditentukan');
        //     }
        // }

        // if ($hasProposal) {
        //     return redirect()->back()->with('errorMessage', 'Anda sudah memiliki proposal yang aktif di tahun ini');
        // }
        $dataProposalDosen = Proposal::whereHas('kelompoks', function ($query) use ($userEmail) {
            $tahun = request('tahun', date('Y'));
            $query->where('anggota_email', $userEmail)->whereYear('tgl_upload', $tahun);
        })->get();
        // dd($dataProposalDosen);

        if (auth()->user()->hasRole('Prodi') || auth()->user()->hasRole('Koordinator Prodi')) {
            $tahunSekarang = date('Y');
            if (auth()->user()->hasRole('Koordinator Prodi')) {
                $userProdi = auth()->user()->prodi[0];
            } else {
                $userProdi = auth()->user()->kaprodi;
            }

            // Ambil data program studi
            $programStudi = ProgramStudi::where('nama_prodi', explode(' ', $userProdi, 2)[1])
                ->where('jenjang', explode(' ', $userProdi)[0])
                ->where('tahun', request('tahun', date('Y')))
                ->first();

            // Hitung jumlah proposal yang sudah ada
            $existingProposals = Proposal::where('prodi', $userProdi)
                ->whereYear('created_at', request('tahun', date('Y')))
                ->where(function ($query) {
                    $query->where('status', '0')->orWhere('status', '1')->orWhere('status', '2');
                })
                ->count();
            // dd($existingProposals);
            // dd($programStudi->kuota, );
            // Hitung sisa kuota
            $sisaKuota = ($programStudi->kuota ?? 0) - $existingProposals;
        }

        return view('stisla.dashboard.index', [
            'widgets' => $widgets,
            'logs' => $logs,
            'user' => $user,
            // 'dataProposal' => Proposal::where('prodi', json_decode($user->prodi, true))->get(),
            // 'dataProposalKeuangan' => Proposal::all(),
            'programStudi' => $programStudi,
            'dataProposal' => $this->proposalRepository->getFilterProdi(),
            'dataProposalKeuangan' => $this->proposalRepository->getFilterTahun(),
            'dataProposalDosen' => $dataProposalDosen,
            'hasProposal' => $hasProposal,
            'jadwalPengajuan' => $jadwalPengajuan,
            'sisaKuota' => $sisaKuota ?? 0,
        ]);
    }
    /**
     * home page
     *
     * @return Response
     */
    public function home()
    {
        return view('stisla.homes.index', [
            'title' => __('Selamat datang di ') . SettingRepository::applicationName(),
        ]);
    }
}
