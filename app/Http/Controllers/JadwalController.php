<?php

namespace App\Http\Controllers;

use App\Exports\JadwalExport;
use App\Http\Requests\JadwalRequest;
use App\Imports\JadwalImport;
use App\Models\Jadwal;
use App\Repositories\JadwalRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\UserRepository;
use App\Services\EmailService;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;

class JadwalController extends Controller
{
    /**
     * jadwalRepository
     *
     * @var JadwalRepository
     */
    private JadwalRepository $jadwalRepository;

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
        $this->jadwalRepository      = new JadwalRepository;
        $this->fileService            = new FileService;
        $this->emailService           = new EmailService;
        $this->NotificationRepository = new NotificationRepository;
        $this->UserRepository         = new UserRepository;

        $this->middleware('can:Jadwal');
        $this->middleware('can:Jadwal Tambah')->only(['create', 'store']);
        $this->middleware('can:Jadwal Ubah')->only(['edit', 'update']);
        $this->middleware('can:Jadwal Hapus')->only(['destroy']);
        $this->middleware('can:Jadwal Ekspor')->only(['json', 'excel', 'csv', 'pdf']);
        $this->middleware('can:Jadwal Impor Excel')->only(['importExcel', 'importExcelExample']);
    }

    /**
     * showing data page
     *
     * @return Response
     */
    public function index()
    {
        $user = auth()->user();
        return view('stisla.jadwals.index', [
            'data'             => $this->jadwalRepository->getLatest(),
            'canCreate'        => $user->can('Jadwal Tambah'),
            'canUpdate'        => $user->can('Jadwal Ubah'),
            'canDelete'        => $user->can('Jadwal Hapus'),
            'canImportExcel'   => $user->can('Order Impor Excel') && $this->importable,
            'canExport'        => $user->can('Order Ekspor') && $this->exportable,
            'title'            => __('Jadwal'),
            'routeCreate'      => route('jadwals.create'),
            'routePdf'         => route('jadwals.pdf'),
            'routePrint'       => route('jadwals.print'),
            'routeExcel'       => route('jadwals.excel'),
            'routeCsv'         => route('jadwals.csv'),
            'routeJson'        => route('jadwals.json'),
            'routeImportExcel' => route('jadwals.import-excel'),
            'excelExampleLink' => route('jadwals.import-excel-example'),
        ]);
    }

    /**
     * showing add new data form page
     *
     * @return Response
     */
    public function create()
    {
        return view('stisla.jadwals.form', [
            'title'         => __('Jadwal'),
            'fullTitle'     => __('Tambah Jadwal'),
            'routeIndex'    => route('jadwals.index'),
            'action'        => route('jadwals.store')
        ]);
    }

    /**
     * save new data to db
     *
     * @param JadwalRequest $request
     * @return Response
     */
    public function store(JadwalRequest $request)
    {
        $data = $request->only([
			'tgl_mulai',
			'tgl_selesai',
			'keterangan',
        ]);
        // Pastikan tgl_selesai diatur ke akhir hari
        $data['tgl_selesai'] = Carbon::parse($data['tgl_selesai'])->endOfDay();

        // gunakan jika ada file
        // if ($request->hasFile('file')) {
        //     $data['file'] = $this->fileService->methodName($request->file('file'));
        // }

        $result = $this->jadwalRepository->create($data);

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

        logCreate("Jadwal", $result);

        $successMessage = successMessageCreate("Jadwal");
        return redirect()->back()->with('successMessage', $successMessage);
    }

    /**
     * showing edit page
     *
     * @param Jadwal $jadwal
     * @return Response
     */
    public function edit(Jadwal $jadwal)
    {
        return view('stisla.jadwals.form', [
            'd'             => $jadwal,
            'title'         => __('Jadwal'),
            'fullTitle'     => __('Ubah Jadwal'),
            'routeIndex'    => route('jadwals.index'),
            'action'        => route('jadwals.update', [$jadwal->id])
        ]);
    }

    /**
     * update data to db
     *
     * @param JadwalRequest $request
     * @param Jadwal $jadwal
     * @return Response
     */
    public function update(JadwalRequest $request, Jadwal $jadwal)
    {
        $data = $request->only([
			'tgl_mulai',
			'tgl_selesai',
			'keterangan',
        ]);
        // Pastikan tgl_selesai diatur ke akhir hari
        $data['tgl_selesai'] = Carbon::parse($data['tgl_selesai'])->endOfDay();
        // gunakan jika ada file
        // if ($request->hasFile('file')) {
        //     $data['file'] = $this->fileService->methodName($request->file('file'));
        // }

        $newData = $this->jadwalRepository->update($data, $jadwal->id);

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

        logUpdate("Jadwal", $jadwal, $newData);

        $successMessage = successMessageUpdate("Jadwal");
        return redirect()->back()->with('successMessage', $successMessage);
    }

    /**
     * delete user from db
     *
     * @param Jadwal $jadwal
     * @return Response
     */
    public function destroy(Jadwal $jadwal)
    {
        // delete file from storage if exists
        // $this->fileService->methodName($jadwal);

        // use this if you want to create notification data
        // $title = 'Notify Title';
        // $content = 'lorem ipsum dolor sit amet';
        // $userId = 2;
        // $notificationType = 'transaksi masuk';
        // $icon = 'bell'; // font awesome
        // $bgColor = 'primary'; // primary, danger, success, warning
        // $this->NotificationRepository->createNotif($title,  $content, $userId,  $notificationType, $icon, $bgColor);

        // gunakan jika mau kirim email
        // $this->emailService->methodName($jadwal);

        $this->jadwalRepository->delete($jadwal->id);
        logDelete("Jadwal", $jadwal);

        $successMessage = successMessageDelete("Jadwal");
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

        $data = $this->jadwalRepository->getLatest();
        return Excel::download(new JadwalExport($data), 'jadwals.xlsx');
    }

    /**
     * import excel file to db
     *
     * @param \App\Http\Requests\ImportExcelRequest $request
     * @return Response
     */
    public function importExcel(\App\Http\Requests\ImportExcelRequest $request)
    {
        Excel::import(new JadwalImport, $request->file('import_file'));
        $successMessage = successMessageImportExcel("Jadwal");
        return redirect()->back()->with('successMessage', $successMessage);
    }

    /**
     * download export data as json
     *
     * @return Response
     */
    public function json()
    {
        $data = $this->jadwalRepository->getLatest();
        return $this->fileService->downloadJson($data, 'jadwals.json');
    }

    /**
     * download export data as xlsx
     *
     * @return Response
     */
    public function excel()
    {
        $data = $this->jadwalRepository->getLatest();
        return (new JadwalExport($data))->download('jadwals.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
     * download export data as csv
     *
     * @return Response
     */
    public function csv()
    {
        $data = $this->jadwalRepository->getLatest();
        return (new JadwalExport($data))->download('jadwals.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    /**
     * download export data as pdf
     *
     * @return Response
     */
    public function pdf()
    {
        $data = $this->jadwalRepository->getLatest();
        return PDF::setPaper('Letter', 'landscape')
            ->loadView('stisla.jadwals.export-pdf', [
                'data'    => $data,
                'isPrint' => false
            ])
            ->download('jadwals.pdf');
    }

    /**
     * export data to print html
     *
     * @return Response
     */
    public function exportPrint()
    {
        $data = $this->jadwalRepository->getLatest();
        return view('stisla.jadwals.export-pdf', [
            'data'    => $data,
            'isPrint' => true
        ]);
    }
}
