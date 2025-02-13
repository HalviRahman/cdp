<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use App\Http\Requests\LaporanRequest;
use App\Imports\LaporanImport;
use App\Models\Laporan;
use App\Repositories\LaporanRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\UserRepository;
use App\Services\EmailService;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Barryvdh\DomPDF\Facade as PDF;

class LaporanController extends Controller
{
    /**
     * laporanRepository
     *
     * @var LaporanRepository
     */
    private LaporanRepository $laporanRepository;

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
        $this->laporanRepository      = new LaporanRepository;
        $this->fileService            = new FileService;
        $this->emailService           = new EmailService;
        $this->NotificationRepository = new NotificationRepository;
        $this->UserRepository         = new UserRepository;

        $this->middleware('can:Laporan');
        $this->middleware('can:Laporan Tambah')->only(['create', 'store']);
        $this->middleware('can:Laporan Ubah')->only(['edit', 'update']);
        $this->middleware('can:Laporan Hapus')->only(['destroy']);
        $this->middleware('can:Laporan Ekspor')->only(['json', 'excel', 'csv', 'pdf']);
        $this->middleware('can:Laporan Impor Excel')->only(['importExcel', 'importExcelExample']);
    }

    /**
     * showing data page
     *
     * @return Response
     */
    public function index()
    {
        $user = auth()->user();
        return view('stisla.laporans.index', [
            'data'             => $this->laporanRepository->getLatest(),
            'canCreate'        => $user->can('Laporan Tambah'),
            'canUpdate'        => $user->can('Laporan Ubah'),
            'canDelete'        => $user->can('Laporan Hapus'),
            'canImportExcel'   => $user->can('Order Impor Excel') && $this->importable,
            'canExport'        => $user->can('Order Ekspor') && $this->exportable,
            'title'            => __('Laporan'),
            'routeCreate'      => route('laporans.create'),
            'routePdf'         => route('laporans.pdf'),
            'routePrint'       => route('laporans.print'),
            'routeExcel'       => route('laporans.excel'),
            'routeCsv'         => route('laporans.csv'),
            'routeJson'        => route('laporans.json'),
            'routeImportExcel' => route('laporans.import-excel'),
            'excelExampleLink' => route('laporans.import-excel-example'),
        ]);
    }

    /**
     * showing add new data form page
     *
     * @return Response
     */
    public function create()
    {
        return view('stisla.laporans.form', [
            'title'         => __('Laporan'),
            'fullTitle'     => __('Tambah Laporan'),
            'routeIndex'    => route('laporans.index'),
            'action'        => route('laporans.store')
        ]);
    }

    /**
     * save new data to db
     *
     * @param LaporanRequest $request
     * @return Response
     */
    public function store(LaporanRequest $request)
    {
        $data = $request->only([
			'id_kelompok',
			'judul_proposal',
			'file_proposal',
			'tgl_upload',
			'status',
			'verifikator',
			'keterangan',
			'tgl_verifikasi',
        ]);

        // gunakan jika ada file
        // if ($request->hasFile('file')) {
        //     $data['file'] = $this->fileService->methodName($request->file('file'));
        // }

        $result = $this->laporanRepository->create($data);

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

        logCreate("Laporan", $result);

        $successMessage = successMessageCreate("Laporan");
        return redirect()->back()->with('successMessage', $successMessage);
    }

    /**
     * showing edit page
     *
     * @param Laporan $laporan
     * @return Response
     */
    public function edit(Laporan $laporan)
    {
        return view('stisla.laporans.form', [
            'd'             => $laporan,
            'title'         => __('Laporan'),
            'fullTitle'     => __('Ubah Laporan'),
            'routeIndex'    => route('laporans.index'),
            'action'        => route('laporans.update', [$laporan->id])
        ]);
    }

    /**
     * update data to db
     *
     * @param LaporanRequest $request
     * @param Laporan $laporan
     * @return Response
     */
    public function update(LaporanRequest $request, Laporan $laporan)
    {
        $data = $request->only([
			'id_kelompok',
			'judul_proposal',
			'file_proposal',
			'tgl_upload',
			'status',
			'verifikator',
			'keterangan',
			'tgl_verifikasi',
        ]);

        // gunakan jika ada file
        // if ($request->hasFile('file')) {
        //     $data['file'] = $this->fileService->methodName($request->file('file'));
        // }

        $newData = $this->laporanRepository->update($data, $laporan->id);

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

        logUpdate("Laporan", $laporan, $newData);

        $successMessage = successMessageUpdate("Laporan");
        return redirect()->back()->with('successMessage', $successMessage);
    }

    /**
     * delete user from db
     *
     * @param Laporan $laporan
     * @return Response
     */
    public function destroy(Laporan $laporan)
    {
        // delete file from storage if exists
        // $this->fileService->methodName($laporan);

        // use this if you want to create notification data
        // $title = 'Notify Title';
        // $content = 'lorem ipsum dolor sit amet';
        // $userId = 2;
        // $notificationType = 'transaksi masuk';
        // $icon = 'bell'; // font awesome
        // $bgColor = 'primary'; // primary, danger, success, warning
        // $this->NotificationRepository->createNotif($title,  $content, $userId,  $notificationType, $icon, $bgColor);

        // gunakan jika mau kirim email
        // $this->emailService->methodName($laporan);

        $this->laporanRepository->delete($laporan->id);
        logDelete("Laporan", $laporan);

        $successMessage = successMessageDelete("Laporan");
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

        $data = $this->laporanRepository->getLatest();
        return Excel::download(new LaporanExport($data), 'laporans.xlsx');
    }

    /**
     * import excel file to db
     *
     * @param \App\Http\Requests\ImportExcelRequest $request
     * @return Response
     */
    public function importExcel(\App\Http\Requests\ImportExcelRequest $request)
    {
        Excel::import(new LaporanImport, $request->file('import_file'));
        $successMessage = successMessageImportExcel("Laporan");
        return redirect()->back()->with('successMessage', $successMessage);
    }

    /**
     * download export data as json
     *
     * @return Response
     */
    public function json()
    {
        $data = $this->laporanRepository->getLatest();
        return $this->fileService->downloadJson($data, 'laporans.json');
    }

    /**
     * download export data as xlsx
     *
     * @return Response
     */
    public function excel()
    {
        $data = $this->laporanRepository->getLatest();
        return (new LaporanExport($data))->download('laporans.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
     * download export data as csv
     *
     * @return Response
     */
    public function csv()
    {
        $data = $this->laporanRepository->getLatest();
        return (new LaporanExport($data))->download('laporans.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    /**
     * download export data as pdf
     *
     * @return Response
     */
    public function pdf()
    {
        $data = $this->laporanRepository->getLatest();
        return PDF::setPaper('Letter', 'landscape')
            ->loadView('stisla.laporans.export-pdf', [
                'data'    => $data,
                'isPrint' => false
            ])
            ->download('laporans.pdf');
    }

    /**
     * export data to print html
     *
     * @return Response
     */
    public function exportPrint()
    {
        $data = $this->laporanRepository->getLatest();
        return view('stisla.laporans.export-pdf', [
            'data'    => $data,
            'isPrint' => true
        ]);
    }
}
