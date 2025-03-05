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
        if (auth()->user()->hasRole('Fakultas')) {
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
            // dd($programStudi);
            $user = auth()->user();
            if ($user->hasRole('Dosen')) {
                $tahunSekarang = date('Y');

                // Siapkan array untuk menyimpan prodi yang masih available
                $availableProdi = [];

                // Cek setiap prodi yang diampu user
                foreach (auth()->user()->prodi as $userProdi) {
                    // Cek kuota di program studi
                    $programStudis = ProgramStudi::where('nama_prodi', explode(' ', $userProdi, 2)[1])->where('jenjang', explode(' ', $userProdi)[0])->where('tahun', $tahunSekarang)->first();

                    if ($programStudis) {
                        // Hitung jumlah proposal yang sudah ada
                        $existingProposals = Proposal::where('prodi', $userProdi)
                            ->whereYear('created_at', $tahunSekarang)
                            ->where(function ($query) {
                                $query->where('status', '0')->orWhere('status', '1');
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
                        ->with('errorMessage', 'Tidak ada prodi yang memiliki kuota tersedia untuk tahun ' . $tahunSekarang);
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
            return view('stisla.proposals.index', [
                'data' => $this->proposalRepository->getFilterProdi(),
                'programStudi' => $programStudi,
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

    /**
     * showing add new data form page
     *
     * @return Response
     */
    public function create()
    {
        $user = auth()->user();
        $tahunSekarang = date('Y');

        // Siapkan array untuk menyimpan prodi yang masih available
        $availableProdi = [];

        // Cek setiap prodi yang diampu user
        foreach (auth()->user()->prodi as $userProdi) {
            // Cek kuota di program studi
            $programStudis = ProgramStudi::where('nama_prodi', explode(' ', $userProdi, 2)[1])->where('jenjang', explode(' ', $userProdi)[0])->where('tahun', $tahunSekarang)->first();

            if ($programStudis) {
                // Hitung jumlah proposal yang sudah ada
                $existingProposals = Proposal::where('prodi', $userProdi)
                    ->whereYear('created_at', $tahunSekarang)
                    ->where(function ($query) {
                        $query->where('status', '0')->orWhere('status', '1');
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

        // gunakan jika ada file
        if ($request->hasFile('file_proposal')) {
            $data['file_proposal'] = $this->fileService->uploadProposal($request->file('file_proposal'));
        }
        $data['tgl_upload'] = now();
        $data['token'] = Str::random(64);
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

        // Cek apakah ada input mahasiswa
        if ($request->filled('nim_mahasiswa') && $request->filled('nama_mahasiswa')) {
            foreach ($request->nim_mahasiswa as $key => $nim) {
                // Pastikan NIM dan nama tidak kosong
                if (!empty($nim) && !empty($request->nama_mahasiswa[$key])) {
                    User::create([
                        'nip' => $nim,
                        'name' => $request->nama_mahasiswa[$key],
                        'remember_token' => $idKelompok, // atau ID yang relevan
                        'is_mahasiswa' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
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
    public function edit(Proposal $proposal)
    {
        $kelompoks = Kelompok::with('user')->get();
        $mahasiswas = User::where('remember_token', $proposal->id_kelompok)->get();
        $anggotas = $proposal->kelompoks->map(function ($kelompok) {
            return [
                // 'nip' => $kelompok->user->nip,
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
            'action' => route('proposals.update', [$proposal->id]),
            'anggota' => $this->UserRepository->getAnggotaOptions(),
            'anggotas' => $anggotas,
            'mahasiswas' => $mahasiswas,
        ]);
    }

    /**
     * update data to db
     *
     * @param ProposalRequest $request
     * @param Proposal $proposal
     * @return Response
     */
    public function update(ProposalRequest $request, Proposal $proposal)
    {
        $data = $request->only(['id_kelompok', 'judul_proposal', 'file_proposal', 'tgl_upload', 'status', 'verifikator', 'keterangan', 'tgl_verifikasi']);

        $action = $request->input('action');

        // gunakan jika ada file
        // if ($request->hasFile('file')) {
        //     $data['file'] = $this->fileService->methodName($request->file('file'));
        // }
        if (auth()->user()->hasRole('Prodi')) {
            if ($action == 'reject') {
                $data['status'] = '10';
            } else {
                $data['status'] = '1';
            }
            $data['verifikator'] = auth()->user()->name;
            $data['tgl_verifikasi'] = now();
        }
        // Simpan ketua_email ke dalam tabel kelompok
        $newData = $this->proposalRepository->update($data, $proposal->id);

        // use this if you want to create notification data
        // $title = 'Notify Title';
        // $content = 'lorem ipsum dolor sit amet';
        // $userId = 2;
        // $notificationType = 'transaksi masuk';
        // $icon = 'bell'; // font awesome
        // $bgColor = 'primary'; // primary, danger, success, warning
        // $this->NotificationRepository->createNotif($title,  $content, $userId,  $notificationType, $icon, $bgColor);

        // gunakan jika mau kirim email
        // $this->emailService->methodName($newData);

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
    public function destroy(Proposal $proposal)
    {
        // delete file from storage if exists
        // $this->fileService->methodName($proposal);

        // use this if you want to create notification data
        // $title = 'Notify Title';
        // $content = 'lorem ipsum dolor sit amet';
        // $userId = 2;
        // $notificationType = 'transaksi masuk';
        // $icon = 'bell'; // font awesome
        // $bgColor = 'primary'; // primary, danger, success, warning
        // $this->NotificationRepository->createNotif($title,  $content, $userId,  $notificationType, $icon, $bgColor);

        // gunakan jika mau kirim email
        // $this->emailService->methodName($proposal);

        $this->proposalRepository->delete($proposal->id);
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
    public function excel()
    {
        $data = $this->proposalRepository->getLatest();
        return (new ProposalExport($data))->download('proposals.xlsx', \Maatwebsite\Excel\Excel::XLSX);
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
}
