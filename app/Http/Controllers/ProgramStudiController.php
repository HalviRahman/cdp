<?php

namespace App\Http\Controllers;

use App\Exports\ProgramStudiExport;
use App\Http\Requests\ProgramStudiRequest;
use App\Imports\ProgramStudiImport;
use App\Models\ProgramStudi;
use App\Repositories\ProgramStudiRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\UserRepository;
use App\Services\EmailService;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Barryvdh\DomPDF\Facade as PDF;

class ProgramStudiController extends Controller
{
    /**
     * programStudiRepository
     *
     * @var ProgramStudiRepository
     */
    private ProgramStudiRepository $programStudiRepository;

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
        $this->programStudiRepository = new ProgramStudiRepository();
        $this->fileService = new FileService();
        $this->emailService = new EmailService();
        $this->NotificationRepository = new NotificationRepository();
        $this->UserRepository = new UserRepository();

        $this->middleware('can:Program Studi');
        $this->middleware('can:Program Studi Tambah')->only(['create', 'store']);
        $this->middleware('can:Program Studi Ubah')->only(['edit', 'update']);
        $this->middleware('can:Program Studi Hapus')->only(['destroy']);
        $this->middleware('can:Program Studi Ekspor')->only(['json', 'excel', 'csv', 'pdf']);
        $this->middleware('can:Program Studi Impor Excel')->only(['importExcel', 'importExcelExample']);
    }

    /**
     * showing data page
     *
     * @return Response
     */
    public function index()
    {
        $user = auth()->user();
        return view('stisla.program-studis.index', [
            // 'data'             => $this->programStudiRepository->getLatest(),
            'data' => $this->programStudiRepository->getFilterTahun(),
            'canCreate' => $user->can('Program Studi Tambah'),
            'canUpdate' => $user->can('Program Studi Ubah'),
            'canDelete' => $user->can('Program Studi Hapus'),
            'canImportExcel' => $user->can('Order Impor Excel') && $this->importable,
            'canExport' => $user->can('Order Ekspor') && $this->exportable,
            'title' => __('Program Studi'),
            'routeCreate' => route('program-studis.create'),
            'routePdf' => route('program-studis.pdf'),
            'routePrint' => route('program-studis.print'),
            'routeExcel' => route('program-studis.excel'),
            'routeCsv' => route('program-studis.csv'),
            'routeJson' => route('program-studis.json'),
            'routeImportExcel' => route('program-studis.import-excel'),
            'excelExampleLink' => route('program-studis.import-excel-example'),
        ]);
    }

    /**
     * showing add new data form page
     *
     * @return Response
     */
    public function create()
    {
        return view('stisla.program-studis.form', [
            'title' => __('Program Studi'),
            'fullTitle' => __('Tambah Program Studi'),
            'routeIndex' => route('program-studis.index'),
            'action' => route('program-studis.store'),
        ]);
    }

    /**
     * save new data to db
     *
     * @param ProgramStudiRequest $request
     * @return Response
     */
    public function store(ProgramStudiRequest $request)
    {
        $data = $request->only(['nama_prodi', 'kuota', 'tahun', 'jenjang']);

        // gunakan jika ada file
        // if ($request->hasFile('file')) {
        //     $data['file'] = $this->fileService->methodName($request->file('file'));
        // }

        $result = $this->programStudiRepository->create($data);

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

        logCreate('Program Studi', $result);

        $successMessage = successMessageCreate('Program Studi');
        return redirect()->route('program-studis.index')->with('successMessage', $successMessage);
    }

    /**
     * showing edit page
     *
     * @param ProgramStudi $programStudi
     * @return Response
     */
    public function edit(ProgramStudi $programStudi)
    {
        return view('stisla.program-studis.form', [
            'd' => $programStudi,
            'title' => __('Program Studi'),
            'fullTitle' => __('Ubah Program Studi'),
            'routeIndex' => route('program-studis.index'),
            'action' => route('program-studis.update', [$programStudi->id]),
        ]);
    }

    /**
     * update data to db
     *
     * @param ProgramStudiRequest $request
     * @param ProgramStudi $programStudi
     * @return Response
     */
    public function update(ProgramStudiRequest $request, ProgramStudi $programStudi)
    {
        $data = $request->only(['nama_prodi', 'kuota', 'tahun', 'jenjang']);

        // gunakan jika ada file
        // if ($request->hasFile('file')) {
        //     $data['file'] = $this->fileService->methodName($request->file('file'));
        // }

        $newData = $this->programStudiRepository->update($data, $programStudi->id);

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

        logUpdate('Program Studi', $programStudi, $newData);

        $successMessage = successMessageUpdate('Program Studi');
        return redirect()->route('program-studis.index')->with('successMessage', $successMessage);
    }

    /**
     * delete user from db
     *
     * @param ProgramStudi $programStudi
     * @return Response
     */
    public function destroy(ProgramStudi $programStudi)
    {
        // delete file from storage if exists
        // $this->fileService->methodName($programStudi);

        // use this if you want to create notification data
        // $title = 'Notify Title';
        // $content = 'lorem ipsum dolor sit amet';
        // $userId = 2;
        // $notificationType = 'transaksi masuk';
        // $icon = 'bell'; // font awesome
        // $bgColor = 'primary'; // primary, danger, success, warning
        // $this->NotificationRepository->createNotif($title,  $content, $userId,  $notificationType, $icon, $bgColor);

        // gunakan jika mau kirim email
        // $this->emailService->methodName($programStudi);

        $this->programStudiRepository->delete($programStudi->id);
        logDelete('Program Studi', $programStudi);

        $successMessage = successMessageDelete('Program Studi');
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

        $data = $this->programStudiRepository->getLatest();
        return Excel::download(new ProgramStudiExport($data), 'program-studis.xlsx');
    }

    /**
     * import excel file to db
     *
     * @param \App\Http\Requests\ImportExcelRequest $request
     * @return Response
     */
    public function importExcel(\App\Http\Requests\ImportExcelRequest $request)
    {
        Excel::import(new ProgramStudiImport(), $request->file('import_file'));
        $successMessage = successMessageImportExcel('Program Studi');
        return redirect()->back()->with('successMessage', $successMessage);
    }

    /**
     * download export data as json
     *
     * @return Response
     */
    public function json()
    {
        $data = $this->programStudiRepository->getLatest();
        return $this->fileService->downloadJson($data, 'program-studis.json');
    }

    /**
     * download export data as xlsx
     *
     * @return Response
     */
    public function excel()
    {
        $data = $this->programStudiRepository->getLatest();
        return (new ProgramStudiExport($data))->download('program-studis.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
     * download export data as csv
     *
     * @return Response
     */
    public function csv()
    {
        $data = $this->programStudiRepository->getLatest();
        return (new ProgramStudiExport($data))->download('program-studis.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    /**
     * download export data as pdf
     *
     * @return Response
     */
    public function pdf()
    {
        $data = $this->programStudiRepository->getLatest();
        return PDF::setPaper('Letter', 'landscape')
            ->loadView('stisla.program-studis.export-pdf', [
                'data' => $data,
                'isPrint' => false,
            ])
            ->download('program-studis.pdf');
    }

    /**
     * export data to print html
     *
     * @return Response
     */
    public function exportPrint()
    {
        $data = $this->programStudiRepository->getLatest();
        return view('stisla.program-studis.export-pdf', [
            'data' => $data,
            'isPrint' => true,
        ]);
    }
}
