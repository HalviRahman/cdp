<?php

namespace App\Http\Controllers;

use App\Exports\ProposalExport;
use App\Http\Requests\ProposalRequest;
use App\Imports\ProposalImport;
use App\Models\Proposal;
use App\Models\Kelompok;
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
    public function index()
    {
        $user = auth()->user();
        return view('stisla.proposals.index', [
            'data' => $this->proposalRepository->getLatest(),
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

    /**
     * showing add new data form page
     *
     * @return Response
     */
    public function create()
    {
        return view('stisla.proposals.form', [
            'title' => __('Proposal'),
            'fullTitle' => __('Tambah Proposal'),
            'routeIndex' => route('proposals.index'),
            'action' => route('proposals.store'),
            'anggota' => $this->UserRepository->getAnggotaOptions(),
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
        $data = $request->only(['id_kelompok', 'judul_proposal', 'file_proposal', 'tgl_upload', 'status', 'verifikator', 'keterangan', 'tgl_verifikasi', 'anggota_email']);

        // gunakan jika ada file
        if ($request->hasFile('file_proposal')) {
            $data['file_proposal'] = $this->fileService->uploadProposal($request->file('file_proposal'));
        }
        $data['tgl_upload'] = now();
        $data['token'] = Str::random(64);

        $idKelompok = Str::uuid();

        $ketuaEmail = auth()->user()->email;
        $anggotaEmails = $request->input('anggota_email', []);

        $peranKetua = 'Ketua';
        $peranAnggota = 'Anggota';

        $data['id_kelompok'] = $idKelompok;

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

        $result = $this->proposalRepository->create($data);

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
        return redirect()->back()->with('successMessage', $successMessage);
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

        // gunakan jika ada file
        // if ($request->hasFile('file')) {
        //     $data['file'] = $this->fileService->methodName($request->file('file'));
        // }
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
        return redirect()->back()->with('successMessage', $successMessage);
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
