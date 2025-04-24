<?php

namespace App\Http\Controllers;

use App\Exports\ProposalExport;
use App\Http\Requests\ProposalRequest;
use App\Imports\ProposalImport;
use App\Models\Proposal;
use App\Models\Kelompok;
use App\Models\ProgramStudi;
use App\Repositories\ProposalRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\UserRepository;
use App\Repositories\KelompokRepository;
use App\Services\EmailService;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Barryvdh\DomPDF\Facade as PDF;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Exports\ProposalCompletedExport;
use App\Models\Jadwal;
use Carbon\Carbon;

class ProposalController extends Controller
{
    /**
     * proposalRepository
     *
     * @var ProposalRepository
     */
    private ProposalRepository $proposalRepository;

    /**
     * NotificationRepository
     *
     * @var NotificationRepository
     */
    private NotificationRepository $NotificationRepository;

    /**
     * UserRepository
     *
     * @var UserRepository
     */
    private UserRepository $UserRepository;

    /**
     * file service
     *
     * @var FileService
     */
    private FileService $fileService;

    /**
     * email service
     *
     * @var FileService
     */
    private EmailService $emailService;

    /**
     * exportable
     *
     * @var bool
     */
    private bool $exportable = false;

    /**
     * importable
     *
     * @var bool
     */
    private bool $importable = false;

    /**
     * constructor method
     *
     * @return void
     */
    public function __construct()
    {
        $this->proposalRepository = new ProposalRepository();
        $this->fileService = new FileService();
        $this->emailService = new EmailService();
        $this->NotificationRepository = new NotificationRepository();
        $this->UserRepository = new UserRepository();
        $this->kelompokRepository = new KelompokRepository();

        $this->middleware('can:Proposal');
        $this->middleware('can:Proposal Tambah')->only(['create', 'store']);
        $this->middleware('can:Proposal Ubah')->only(['edit', 'update']);
        $this->middleware('can:Proposal Hapus')->only(['destroy']);
        $this->middleware('can:Proposal Ekspor')->only(['json', 'excel', 'csv', 'pdf']);
        $this->middleware('can:Proposal Impor Excel')->only(['importExcel', 'importExcelExample']);
    }

    /**
     * showing data page
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('Keuangan')) {
            return redirect()->route('dashboard.index');
        }
        if ($user->hasRole('Fakultas') && $user->hasRole('Dosen') && $user->hasRole('Keuangan')) {
            // Hitung total proposal masuk
            $proposalMasuk = Proposal::whereYear('created_at', request('tahun', date('Y')))
                ->where(function ($query) {
                    $query->where('status', '0')->orWhere('status', '1')->orWhere('status', '2')->orWhere('status', '3');
                })
                ->count();

            // Hitung total kuota dari semua prodi
            $totalKuota = ProgramStudi::where('tahun', request('tahun', date('Y')))->sum('kuota');
            $tahun = $request->tahun ?? date('Y');

            // Jika parameter view=table, tampilkan tabel
            if ($request->view == 'table') {
                $data = Proposal::with(['ketuaKelompok.user'])
                    ->where('prodi', $request->prodi)
                    ->whereYear('created_at', $tahun)
                    ->get();

                return view('stisla.proposals.table', [
                    'title' => __('Proposal'),
                    'data' => $data,
                    'prodi' => $request->prodi,
                ]);
            }
            $tahun = $request->tahun ?? date('Y');
            // Jika tidak ada parameter view, tampilkan dashboard seperti biasa
            $programStudi = ProgramStudi::where('tahun', $tahun)
                ->select('program_studis.*')
                ->selectRaw(
                    '(SELECT COUNT(*) FROM proposals
                            WHERE proposals.prodi = CONCAT(program_studis.jenjang, " ", program_studis.nama_prodi)
                            AND YEAR(proposals.tgl_upload) = ?) as proposals_count',
                    [$tahun],
                )
                ->get();
        }
        if ($user->hasRole('Fakultas') || $user->hasRole('Prodi')) {
            // Hitung total proposal masuk
            $proposalMasuk = Proposal::whereYear('created_at', request('tahun', date('Y')))
                ->where(function ($query) {
                    $query->where('status', '0')->orWhere('status', '1')->orWhere('status', '2')->orWhere('status', '3');
                })
                ->count();

            // Hitung total kuota dari semua prodi
            $totalKuota = ProgramStudi::where('tahun', request('tahun', date('Y')))->sum('kuota');
            $tahun = $request->tahun ?? date('Y');

            // Jika parameter view=table, tampilkan tabel
            if ($request->view == 'table') {
                $data = Proposal::with(['ketuaKelompok.user'])
                    ->where('prodi', $request->prodi)
                    ->whereYear('created_at', $tahun)
                    ->get();

                return view('stisla.proposals.table', [
                    'title' => __('Proposal'),
                    'data' => $data,
                    'prodi' => $request->prodi,
                ]);
            }
            $tahun = $request->tahun ?? date('Y');
            // Jika tidak ada parameter view, tampilkan dashboard seperti biasa
            $programStudi = ProgramStudi::where('tahun', $tahun)
                ->select('program_studis.*')
                ->selectRaw(
                    '(SELECT COUNT(*) FROM proposals
                            WHERE proposals.prodi = CONCAT(program_studis.jenjang, " ", program_studis.nama_prodi)
                            AND YEAR(proposals.tgl_upload) = ?) as proposals_count',
                    [$tahun],
                )
                ->get();
        }

        if ($user->hasRole('Dosen')) {
            // Cek Jadwal Pengajuan Proposal
            $userEmail = auth()->user()->email;
            $jadwalPengajuan = Jadwal::where('keterangan', 'Pengajuan Proposal')->where('tgl_mulai', '<=', now())->where('tgl_selesai', '>=', now())->exists();
            $hasProposal = Proposal::whereHas('kelompoks', function ($query) use ($userEmail) {
                $query->where('anggota_email', $userEmail)->whereYear('created_at', now()->year);
                // $query->where('anggota_email', $userEmail)->where('peran', 'Ketua')->whereYear('created_at', now()->year);
            })
                ->where(function ($query) {
                    $query->where('status', '0')->orWhere('status', '1')->orWhere('status', '2');
                })
                ->exists();

            if (!$jadwalPengajuan) {
                // Ambil jadwal pengajuan untuk mendapatkan tanggal
                $jadwalInfo = Jadwal::where('keterangan', 'Pengajuan Proposal')->first();

                if ($jadwalInfo) {
                    $tglMulai = Carbon::parse($jadwalInfo->tgl_mulai)->format('d M Y');
                    $tglSelesai = Carbon::parse($jadwalInfo->tgl_selesai)->format('d M Y');
                    return redirect()
                        ->back()
                        ->with('errorMessage', "Pengajuan proposal hanya dapat dilakukan pada tanggal $tglMulai sampai $tglSelesai");
                } else {
                    return redirect()->route('dashboard')->with('errorMessage', 'Jadwal pengajuan proposal belum ditentukan');
                }
            }

            if ($hasProposal) {
                return redirect()->back()->with('errorMessage', 'Anda sudah memiliki proposal yang aktif di tahun ini');
            }
            // End of Cek Jadwal Pengajuan Proposal

            $tahunSekarang = date('Y');
            // Cek apakah user sudah terdaftar sebagai anggota di kelompok manapun
            $isAnggotaExist = Kelompok::where('anggota_email', auth()->user()->email)
                ->whereYear('created_at', $tahunSekarang)
                ->whereHas('proposal', function ($query) {
                    $query->where('status', '!=', '10'); // tidak termasuk proposal yang ditolak
                })
                ->exists();

            if ($isAnggotaExist) {
                return redirect()
                    ->route('dashboard.index')
                    ->with('errorMessage', 'Anda sudah terdaftar sebagai anggota/ketua dalam proposal di tahun ' . $tahunSekarang);
            }

            // Siapkan array untuk menyimpan prodi yang masih available
            $availableProdi = [];

            // Cek setiap prodi yang diampu user
            foreach (auth()->user()->prodi as $userProdi) {
                $namaProdi = explode(' ', $userProdi, 2)[1];
                $jenjang = explode(' ', $userProdi)[0];

                // Cek kuota di program studi
                $programStudis = ProgramStudi::where('nama_prodi', $namaProdi)->where('jenjang', $jenjang)->where('tahun', $tahunSekarang)->first();

                if ($programStudis) {
                    // Hitung jumlah proposal yang sudah ada
                    $existingProposals = Proposal::where('prodi', $userProdi)
                        ->whereYear('created_at', $tahunSekarang)
                        ->where(function ($query) {
                            $query->where('status', '0')->orWhere('status', '1')->orWhere('status', '2')->orWhere('status', '3');
                        })
                        ->count();

                    // Jika masih ada kuota, tambahkan ke array available
                    if ($existingProposals < $programStudis->kuota) {
                        $availableProdi[$userProdi] = [
                            'nama' => $userProdi,
                            'kuota_tersisa' => $programStudis->kuota - $existingProposals,
                        ];
                    }
                }
            }

            // Cek apakah dosen sudah menjadi ketua
            $isKetuaExist = Kelompok::where('anggota_email', auth()->user()->email)
                ->where('peran', 'Ketua')
                ->whereYear('created_at', $tahunSekarang)
                ->whereHas('proposal', function ($query) {
                    $query->where('status', '!=', '10');
                })
                ->exists();

            if ($isKetuaExist) {
                return redirect()->route('dashboard.index')->with('errorMessage', 'Anda sudah mengajukan proposal sebagai ketua di tahun ini.');
            }

            // Jika tidak ada prodi yang available
            if (empty($availableProdi)) {
                return redirect()
                    ->route('dashboard.index')
                    ->with('errorMessage', 'Kuota prodi sudah terpenuhi, silahkan hubungi Koordinator CDP Program Studi');
            }

            return view('stisla.proposals.form', [
                'title' => __('Proposal'),
                'fullTitle' => __('Tambah Proposal'),
                'routeIndex' => route('proposals.index'),
                'action' => route('proposals.store'),
                'anggota' => $this->UserRepository->getAnggotaOptions(),
                'availableProdi' => $availableProdi, // kirim prodi yang masih ada kuota
            ]);
        }
        // dd($totalKuota);
        return view('stisla.proposals.index', [
            'data' => $this->proposalRepository->getFilterProdi(),
            'programStudi' => $programStudi,
            'totalKuota' => $totalKuota,
            // 'proposalMasuk' => $proposalMasuk,
            'proposalMasuk' => $this->proposalRepository->getFilterProdiCount(),
            // 'data' => $this->proposalRepository->getLatest(),
            'canCreate' => $user->can('Proposal Tambah'),
            'canUpdate' => $user->can('Proposal Ubah'),
            'canDelete' => $user->can('Proposal Hapus'),
            'canImportExcel' => $user->can('Order Impor Excel') && $this->importable,
            'canExport' => $user->can('Order Ekspor') && $this->exportable,
            'title' => __('Proposal'),
            'routeCreate' => route('proposals.create'),
            'routePdf' => route('proposals.pdf'),
            'routePrint' => route('proposals.print'),
            'routeExcel' => route('proposals.excel'),
            'routeCsv' => route('proposals.csv'),
            'routeJson' => route('proposals.json'),
            'routeImportExcel' => route('proposals.import-excel'),
            'excelExampleLink' => route('proposals.import-excel-example'),
        ]);
        // } else {
        //     return redirect()->route('dashboard.index')->with('errorMessage', 'Anda tidak memiliki akses ke halaman ini.');
        // }
    }

    /**
     * showing add new data form page
     *
     * @return Response
     */
    public function create()
    {
        $userEmail = auth()->user()->email;
        $jadwalPengajuan = Jadwal::where('keterangan', 'Pengajuan Proposal')->where('tgl_mulai', '<=', now())->where('tgl_selesai', '>=', now())->exists();
        $hasProposal = Proposal::whereHas('kelompoks', function ($query) use ($userEmail) {
            $query->where('anggota_email', $userEmail)->whereYear('created_at', now()->year);
            // $query->where('anggota_email', $userEmail)->where('peran', 'Ketua')->whereYear('created_at', now()->year);
        })
            ->where(function ($query) {
                $query->where('status', '0')->orWhere('status', '1')->orWhere('status', '2');
            })
            ->exists();

        if (!$jadwalPengajuan) {
            // Ambil jadwal pengajuan untuk mendapatkan tanggal
            $jadwalInfo = Jadwal::where('keterangan', 'Pengajuan Proposal')->first();

            if ($jadwalInfo) {
                $tglMulai = Carbon::parse($jadwalInfo->tgl_mulai)->format('d M Y');
                $tglSelesai = Carbon::parse($jadwalInfo->tgl_selesai)->format('d M Y');
                return redirect()
                    ->back()
                    ->with('errorMessage', "Pengajuan proposal hanya dapat dilakukan pada tanggal $tglMulai sampai $tglSelesai");
            } else {
                return redirect()->route('dashboard')->with('errorMessage', 'Jadwal pengajuan proposal belum ditentukan');
            }
        }

        if ($hasProposal) {
            return redirect()->back()->with('errorMessage', 'Anda sudah memiliki proposal yang aktif di tahun ini');
        }
        // End of Cek Jadwal Pengajuan Proposal

        // Cek Apakah User Sudah Terdaftar Sebagai Anggota Di Kelompok Manapun
        $user = auth()->user();
        $tahunSekarang = date('Y');

        // Cek apakah user sudah terdaftar sebagai anggota di kelompok manapun
        $isAnggotaExist = Kelompok::where('anggota_email', auth()->user()->email)
            ->whereYear('created_at', $tahunSekarang)
            ->whereHas('proposal', function ($query) {
                $query->where('status', '!=', '10'); // tidak termasuk proposal yang ditolak
            })
            ->exists();

        if ($isAnggotaExist) {
            return redirect()
                ->route('dashboard.index')
                ->with('errorMessage', 'Anda sudah terdaftar sebagai anggota/ketua dalam proposal lain di tahun ' . $tahunSekarang);
        }

        // Siapkan array untuk menyimpan prodi yang masih available
        $availableProdi = [];

        foreach (auth()->user()->prodi as $userProdi) {
            // Cek kuota di program studi
            // $userProdi = trim($userProdi);
            $namaProdi = explode(' ', $userProdi, 2)[1];
            $jenjang = explode(' ', $userProdi)[0];
            $programStudis = ProgramStudi::where('nama_prodi', $namaProdi)->where('jenjang', $jenjang)->where('tahun', $tahunSekarang)->first();
            if ($programStudis) {
                // Hitung jumlah proposal yang sudah ada
                $existingProposals = Proposal::where('prodi', $userProdi)
                    ->whereYear('created_at', $tahunSekarang)
                    ->where(function ($query) {
                        $query->where('status', '0')->orWhere('status', '1')->orWhere('status', '2')->orWhere('status', '3');
                    })
                    ->count();
                // dd($existingProposals);
                // Jika masih ada kuota, tambahkan ke array available
                if ($existingProposals < $programStudis->kuota) {
                    $availableProdi[$userProdi] = [
                        'nama' => $userProdi,
                        'kuota_tersisa' => $programStudis->kuota - $existingProposals,
                    ];
                }
            }
        }
        // dd($availableProdi[auth()->user()->prodi[1]]);

        // Cek apakah dosen sudah menjadi ketua
        $isKetuaExist = Kelompok::where('anggota_email', auth()->user()->email)
            ->where('peran', 'Ketua')
            ->whereYear('created_at', date('Y'))
            ->whereHas('proposal', function ($query) {
                $query->where('status', '!=', '10');
            })
            ->exists();

        if ($isKetuaExist) {
            return redirect()->route('dashboard.index')->with('errorMessage', 'Anda sudah mengajukan proposal sebagai ketua di tahun ini.');
        }

        // Jika tidak ada prodi yang available
        if (empty($availableProdi)) {
            return redirect()
                ->route('dashboard.index')
                ->with('errorMessage', 'Tidak ada prodi yang memiliki kuota tersedia untuk tahun ' . $tahunSekarang);
        }
        // foreach ($availableProdi as $key => $value) {
        //     dd($key, $value);
        // }

        return view('stisla.proposals.form', [
            'title' => __('Proposal'),
            'fullTitle' => __('Tambah Proposal'),
            'routeIndex' => route('proposals.index'),
            'action' => route('proposals.store'),
            'anggota' => $this->UserRepository->getAnggotaOptions(),
            'availableProdi' => $availableProdi, // kirim prodi yang masih ada kuota
        ]);
    }

    /**
     * save new data to db
     *
     * @param ProposalRequest $request
     * @return Response
     */
    public function store(ProposalRequest $request)
    {
        $data = $request->only(['id_kelompok', 'judul_proposal', 'file_proposal', 'tgl_upload', 'status', 'verifikator', 'keterangan', 'tgl_verifikasi', 'anggota_email', 'remember_token']);
        $data['judul_proposal'] = strtoupper($data['judul_proposal']);

        // gunakan jika ada file
        if ($request->hasFile('file_proposal')) {
            $data['file_proposal'] = $this->fileService->uploadProposal($request->file('file_proposal'));
        }
        $data['tgl_upload'] = now();
        $data['token'] = Str::random(96);
        if (count(auth()->user()->prodi) > 1) {
            $data['prodi'] = $request->prodi; // ambil dari form request
        } else {
            $data['prodi'] = auth()->user()->prodi[0]; // ambil langsung dari user
        }

        // $data['prodi'] = implode('; ', auth()->user()->prodi);
        // $data['prodi'] = implode('; ', json_decode(auth()->user()->prodi, true));

        $idKelompok = Str::uuid();

        $ketuaEmail = auth()->user()->email;
        $anggotaEmails = $request->input('anggota_email', []);

        $peranKetua = 'Ketua';
        $peranAnggota = 'Anggota';

        $data['id_kelompok'] = $idKelompok;

        $result = $this->proposalRepository->create($data);
        // foreach ($request->nim_mahasiswa as $key => $nim) {
        //     User::create([
        //         'nip' => $nim,
        //         'name' => $request->nama_mahasiswa[$key],
        //         'remember_token' => $idKelompok,
        //         'is_mahasiswa' => true,
        //     ]);
        // }
        // Simpan ketua_email dengan peran 'Ketua'
        $this->kelompokRepository->create([
            'id_kelompok' => $idKelompok,
            'anggota_email' => $ketuaEmail,
            'peran' => $peranKetua,
        ]);

        // Simpan setiap anggota_email dengan peran 'Anggota'
        foreach ($anggotaEmails as $email) {
            $this->kelompokRepository->create([
                'id_kelompok' => $idKelompok,
                'anggota_email' => $email,
                'peran' => $peranAnggota,
            ]);
        }

        // Cek apakah ada input mahasiswa
        if ($request->filled('nim_mahasiswa') && $request->filled('nama_mahasiswa')) {
            foreach ($request->nim_mahasiswa as $key => $nim) {
                // Pastikan NIM dan nama tidak kosong
                if (!empty($nim) && !empty($request->nama_mahasiswa[$key])) {
                    User::create([
                        'nip' => $nim,
                        'name' => strtoupper($request->nama_mahasiswa[$key]),
                        'remember_token' => $idKelompok, // atau ID yang relevan
                        'is_mahasiswa' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
        // use this if you want to create notification data
        // $title = 'Notify Title';
        // $content = 'lorem ipsum dolor sit amet';
        // $userId = 2;
        // $notificationType = 'transaksi masuk';
        // $icon = 'bell'; // font awesome
        // $bgColor = 'primary'; // primary, danger, success, warning
        // $this->NotificationRepository->createNotif($title,  $content, $userId,  $notificationType, $icon, $bgColor);

        // gunakan jika mau kirim email
        // $this->emailService->methodName($result);

        logCreate('Proposal', $result);

        $successMessage = successMessageCreate('Proposal');
        return redirect()->route('dashboard.index')->with('successMessage', $successMessage);
    }

    /**
     * showing edit page
     *
     * @param Proposal $proposal
     * @return Response
     */
    public function edit($token)
    {
        $proposal = Proposal::where('token', $token)->first();
        $kelompoks = Kelompok::with('user')->get();
        $mahasiswas = User::where('remember_token', $proposal->id_kelompok)->get();
        $peran = $kelompoks->where('anggota_email', auth()->user()->email)->first()->peran ?? 'Anggota';
        $anggotas = $proposal->kelompoks->map(function ($kelompok) {
            return [
                'nip' => $kelompok->user->nip,
                'nama' => $kelompok->user->name,
                'prodi' => $kelompok->user->prodi,
                'peran' => $kelompok->peran,
            ];
        });

        return view('stisla.proposals.form', [
            'd' => $proposal,
            'title' => __('Proposal'),
            'fullTitle' => __('Proposal'),
            'routeIndex' => route('proposals.index'),
            'action' => route('proposals.update', [$proposal->token]),
            'anggota' => $this->UserRepository->getAnggotaOptions(),
            'anggotas' => $anggotas,
            'mahasiswas' => $mahasiswas,
            'peran' => $peran,
        ]);
    }

    /**
     * update data to db
     *
     * @param ProposalRequest $request
     * @param Proposal $proposal
     * @return Response
     */
    public function update(ProposalRequest $request, $token)
    {
        $proposal = Proposal::where('token', $token)->first();
        $data = $request->only(['id_kelompok', 'judul_proposal', 'file_proposal', 'tgl_upload', 'status', 'verifikator', 'keterangan', 'tgl_verifikasi']);
        
        if (isset($data['judul_proposal'])) {
            $data['judul_proposal'] = strtoupper($data['judul_proposal']);
        }

        // Jika sedang periode pengumpulan laporan dan status = 2 (sudah disetujui)
        if (($request->hasFile('laporan_kegiatan') || $request->hasFile('laporan_perjalanan')) && $proposal->status == 2) {
            $request->validate([
                'laporan_kegiatan' => 'required|mimes:pdf|max:5120',
                'laporan_perjalanan' => 'required|mimes:pdf|max:5120',
            ]);

            // Upload laporan kegiatan menggunakan FileService
            if ($request->hasFile('laporan_kegiatan')) {
                if ($proposal->laporan_kegiatan) {
                    // Ambil nama file dari URL lengkap
                    $oldFileName = basename($proposal->laporan_kegiatan);
                    // Bentuk path relatif untuk storage
                    $oldPath = 'proposal/' . $oldFileName;

                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
                $data['laporan_kegiatan'] = $this->fileService->uploadLaporanKegiatan($request->file('laporan_kegiatan'));
            }

            // Upload laporan perjalanan menggunakan FileService
            if ($request->hasFile('laporan_perjalanan')) {
                if ($proposal->laporan_perjalanan) {
                    // Ambil nama file dari URL lengkap
                    $oldFileName = basename($proposal->laporan_perjalanan);
                    // Bentuk path relatif untuk storage
                    $oldPath = 'proposal/' . $oldFileName;

                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
                $data['laporan_perjalanan'] = $this->fileService->uploadLaporanPerjalanan($request->file('laporan_perjalanan'));
            }

            $data['tgl_upload_laporan'] = now();
            // $data['status'] = 3;

            // Update data menggunakan repository
            $newData = $this->proposalRepository->update($data, $proposal->id);

            logUpdate('Proposal', $proposal, $newData);

            $successMessage = successMessageUpdate('Laporan');
            return redirect()->route('dashboard.index')->with('successMessage', $successMessage);
        }

        // Edit anggota kelompok
        if ($request->has('anggota_email') && $proposal->status == 0) {
            // Hapus semua anggota lama kecuali ketua
            Kelompok::where('id_kelompok', $proposal->id_kelompok)->where('peran', 'Anggota')->delete();

            // Tambah anggota baru
            foreach ($request->anggota_email as $email) {
                $this->kelompokRepository->create([
                    'id_kelompok' => $proposal->id_kelompok,
                    'anggota_email' => $email,
                    'peran' => 'Anggota',
                ]);
            }
        }

        // Edit mahasiswa
        if ($request->filled('nim_mahasiswa') && $request->filled('nama_mahasiswa') && $proposal->status == 0) {
            // Hapus data mahasiswa lama
            User::where('remember_token', $proposal->id_kelompok)->where('is_mahasiswa', 1)->delete();

            // Tambah data mahasiswa baru
            foreach ($request->nim_mahasiswa as $key => $nim) {
                if (!empty($nim) && !empty($request->nama_mahasiswa[$key])) {
                    User::create([
                        'nip' => $nim,
                        'name' => strtoupper($request->nama_mahasiswa[$key]),
                        'remember_token' => $proposal->id_kelompok,
                        'is_mahasiswa' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // gunakan jika ada file
        if ($request->hasFile('file_proposal')) {
            $data['file_proposal'] = $this->fileService->uploadProposal($request->file('file_proposal'));
        }
        // dd(auth()->user()->prodi, $proposal->prodi);
        $action = $request->input('action');
        if (auth()->user()->hasRole('Koordinator Prodi') && in_array($proposal->prodi, auth()->user()->prodi)) {
            if ($action == 'reject') {
                $data['status'] = '10';
            } else {
                $data['status'] = '1';
            }
            $data['verifikator'] = auth()->user()->name;
            $data['tgl_verifikasi'] = now();
        }
        if (auth()->user()->hasRole('Prodi') && auth()->user()->kaprodi == $proposal->prodi) {
            if ($action == 'reject') {
                $data['status'] = '10';
            } else {
                $data['status'] = '2';
            }
            $data['verifikator'] = auth()->user()->name;
            $data['tgl_verifikasi'] = now();
        }

        $newData = $this->proposalRepository->update($data, $proposal->id);

        logUpdate('Proposal', $proposal, $newData);

        $successMessage = successMessageUpdate('Proposal');
        return redirect()->route('dashboard.index')->with('successMessage', $successMessage);
    }

    /**
     * delete user from db
     *
     * @param Proposal $proposal
     * @return Response
     */
    public function destroy($token)
    {
        $proposal = $this->proposalRepository->findByToken($token);

        if (!$proposal) {
            return redirect()->back()->with('errorMessage', 'Proposal tidak ditemukan');
        }

        // Hapus file-file terkait
        if ($proposal->file_proposal) {
            Storage::delete($proposal->file_proposal);
        }
        if ($proposal->laporan_kegiatan) {
            Storage::delete($proposal->laporan_kegiatan);
        }
        if ($proposal->laporan_perjalanan) {
            Storage::delete($proposal->laporan_perjalanan);
        }

        // Hapus data proposal
        $this->proposalRepository->delete($token);
        logDelete('Proposal', $proposal);

        $successMessage = successMessageDelete('Proposal');
        return redirect()->back()->with('successMessage', $successMessage);
    }

    /**
     * download import example
     *
     * @return BinaryFileResponse
     */
    public function importExcelExample(): BinaryFileResponse
    {
        // bisa gunakan file excel langsung sebagai contoh
        // $filepath = public_path('example.xlsx');
        // return response()->download($filepath);

        $data = $this->proposalRepository->getLatest();
        return Excel::download(new ProposalExport($data), 'proposals.xlsx');
    }

    /**
     * import excel file to db
     *
     * @param \App\Http\Requests\ImportExcelRequest $request
     * @return Response
     */
    public function importExcel(\App\Http\Requests\ImportExcelRequest $request)
    {
        Excel::import(new ProposalImport(), $request->file('import_file'));
        $successMessage = successMessageImportExcel('Proposal');
        return redirect()->back()->with('successMessage', $successMessage);
    }

    /**
     * download export data as json
     *
     * @return Response
     */
    public function json()
    {
        $data = $this->proposalRepository->getLatest();
        return $this->fileService->downloadJson($data, 'proposals.json');
    }

    /**
     * download export data as xlsx
     *
     * @return Response
     */
    public function excel(Request $request)
    {
        $query = Proposal::with(['ketuaKelompok.user'])->whereYear('created_at', $request->tahun);

        // Filter by prodi if specified
        if ($request->prodi) {
            $query->where('prodi', $request->prodi);
            $prodiTitle = $request->prodi;
            $filename = 'proposal-' . str_replace(' ', '-', strtolower($request->prodi)) . '-' . $request->tahun;
        } else {
            $prodiTitle = 'Semua Program Studi';
            $filename = 'proposal-cdp-' . $request->tahun;
        }

        $data = $query->get();

        return Excel::download(new ProposalExport($data, $prodiTitle), $filename . '.xlsx');
    }

    /**
     * download export data as csv
     *
     * @return Response
     */
    public function csv()
    {
        $data = $this->proposalRepository->getLatest();
        return (new ProposalExport($data))->download('proposals.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    /**
     * download export data as pdf
     *
     * @return Response
     */
    public function pdf()
    {
        $data = $this->proposalRepository->getLatest();
        return PDF::setPaper('Letter', 'landscape')
            ->loadView('stisla.proposals.export-pdf', [
                'data' => $data,
                'isPrint' => false,
            ])
            ->download('proposals.pdf');
    }

    /**
     * export data to print html
     *
     * @return Response
     */
    public function exportPrint()
    {
        $data = $this->proposalRepository->getLatest();
        return view('stisla.proposals.export-pdf', [
            'data' => $data,
            'isPrint' => true,
        ]);
    }

    public function exportCompleted(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        return Excel::download(new ProposalCompletedExport($tahun), 'proposal-completed-' . $tahun . '.csv');
    }

    public function deleteByToken($token)
    {
        try {
            $proposal = $this->proposalRepository->findByToken($token);
            if (!$proposal) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Proposal tidak ditemukan',
                    ],
                    404,
                );
            }

            // Cek apakah user yang login memiliki akses untuk menghapus proposal ini
            if (auth()->user()->id !== $proposal->user_id && !auth()->user()->hasRole('admin')) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Anda tidak memiliki akses untuk menghapus proposal ini',
                    ],
                    403,
                );
            }

            $this->proposalRepository->deleteByToken($token);

            return response()->json([
                'status' => 'success',
                'message' => 'Proposal berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan saat menghapus proposal',
                ],
                500,
            );
        }
    }

    public function rekap_proposal(Request $request)
    {
        $user = auth()->user();
        // Hitung total proposal masuk
        $proposalMasuk = Proposal::whereYear('created_at', request('tahun', date('Y')))
            ->where(function ($query) {
                $query->where('status', '0')->orWhere('status', '1')->orWhere('status', '2')->orWhere('status', '3');
            })
            ->count();

        // Hitung total kuota dari semua prodi
        $totalKuota = ProgramStudi::where('tahun', request('tahun', date('Y')))->sum('kuota');
        $tahun = $request->tahun ?? date('Y');

        // Jika parameter view=table, tampilkan tabel
        if ($request->view == 'table') {
            $data = Proposal::with(['ketuaKelompok.user'])
                ->where('prodi', $request->prodi)
                ->whereYear('created_at', $tahun)
                ->get();

            return view('stisla.proposals.table', [
                'title' => __('Proposal'),
                'data' => $data,
                'prodi' => $request->prodi,
            ]);
        }
        $tahun = $request->tahun ?? date('Y');
        // Jika tidak ada parameter view, tampilkan dashboard seperti biasa
        $programStudi = ProgramStudi::where('tahun', $tahun)
            ->select('program_studis.*')
            ->selectRaw(
                '(SELECT COUNT(*) FROM proposals
                        WHERE proposals.prodi = CONCAT(program_studis.jenjang, " ", program_studis.nama_prodi)
                        AND YEAR(proposals.tgl_upload) = ?) as proposals_count',
                [$tahun],
            )
            ->get();

        return view('stisla.proposals.index', [
            'data' => $this->proposalRepository->getFilterProdi(),
            'programStudi' => $programStudi,
            'totalKuota' => $totalKuota,
            // 'proposalMasuk' => $proposalMasuk,
            'proposalMasuk' => $this->proposalRepository->getFilterProdiCount(),
            // 'data' => $this->proposalRepository->getLatest(),
            'canCreate' => $user->can('Proposal Tambah'),
            'canUpdate' => $user->can('Proposal Ubah'),
            'canDelete' => $user->can('Proposal Hapus'),
            'canImportExcel' => $user->can('Order Impor Excel') && $this->importable,
            'canExport' => $user->can('Order Ekspor') && $this->exportable,
            'title' => __('Proposal'),
            'routeCreate' => route('proposals.create'),
            'routePdf' => route('proposals.pdf'),
            'routePrint' => route('proposals.print'),
            'routeExcel' => route('proposals.excel'),
            'routeCsv' => route('proposals.csv'),
            'routeJson' => route('proposals.json'),
            'routeImportExcel' => route('proposals.import-excel'),
            'excelExampleLink' => route('proposals.import-excel-example'),
        ]);
    }
}
