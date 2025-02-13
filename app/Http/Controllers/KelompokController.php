<?php

namespace App\Http\Controllers;

use App\Exports\KelompokExport;
use App\Http\Requests\KelompokRequest;
use App\Imports\KelompokImport;
use App\Models\Kelompok;
use App\Repositories\KelompokRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\UserRepository;
use App\Services\EmailService;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Barryvdh\DomPDF\Facade as PDF;

class KelompokController extends Controller
{
    /**
     * kelompokRepository
     *
     * @var KelompokRepository
     */
    private KelompokRepository $kelompokRepository;

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
        $this->kelompokRepository      = new KelompokRepository;
        $this->fileService            = new FileService;
        $this->emailService           = new EmailService;
        $this->NotificationRepository = new NotificationRepository;
        $this->UserRepository         = new UserRepository;

        $this->middleware('can:Kelompok');
        $this->middleware('can:Kelompok Tambah')->only(['create', 'store']);
        $this->middleware('can:Kelompok Ubah')->only(['edit', 'update']);
        $this->middleware('can:Kelompok Hapus')->only(['destroy']);
        $this->middleware('can:Kelompok Ekspor')->only(['json', 'excel', 'csv', 'pdf']);
        $this->middleware('can:Kelompok Impor Excel')->only(['importExcel', 'importExcelExample']);
    }

    /**
     * showing data page
     *
     * @return Response
     */
    public function index()
    {
        $user = auth()->user();
        return view('stisla.kelompoks.index', [
            'data'             => $this->kelompokRepository->getLatest(),
            'canCreate'        => $user->can('Kelompok Tambah'),
            'canUpdate'        => $user->can('Kelompok Ubah'),
            'canDelete'        => $user->can('Kelompok Hapus'),
            'canImportExcel'   => $user->can('Order Impor Excel') && $this->importable,
            'canExport'        => $user->can('Order Ekspor') && $this->exportable,
            'title'            => __('Kelompok'),
            'routeCreate'      => route('kelompoks.create'),
            'routePdf'         => route('kelompoks.pdf'),
            'routePrint'       => route('kelompoks.print'),
            'routeExcel'       => route('kelompoks.excel'),
            'routeCsv'         => route('kelompoks.csv'),
            'routeJson'        => route('kelompoks.json'),
            'routeImportExcel' => route('kelompoks.import-excel'),
            'excelExampleLink' => route('kelompoks.import-excel-example'),
        ]);
    }

    /**
     * showing add new data form page
     *
     * @return Response
     */
    public function create()
    {
        return view('stisla.kelompoks.form', [
            'title'         => __('Kelompok'),
            'fullTitle'     => __('Tambah Kelompok'),
            'routeIndex'    => route('kelompoks.index'),
            'action'        => route('kelompoks.store')
        ]);
    }

    /**
     * save new data to db
     *
     * @param KelompokRequest $request
     * @return Response
     */
    public function store(KelompokRequest $request)
    {
        $data = $request->only([
			'ketua_email',
			'anggota_email',
        ]);

        // gunakan jika ada file
        // if ($request->hasFile('file')) {
        //     $data['file'] = $this->fileService->methodName($request->file('file'));
        // }

        $result = $this->kelompokRepository->create($data);

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

        logCreate("Kelompok", $result);

        $successMessage = successMessageCreate("Kelompok");
        return redirect()->back()->with('successMessage', $successMessage);
    }

    /**
     * showing edit page
     *
     * @param Kelompok $kelompok
     * @return Response
     */
    public function edit(Kelompok $kelompok)
    {
        return view('stisla.kelompoks.form', [
            'd'             => $kelompok,
            'title'         => __('Kelompok'),
            'fullTitle'     => __('Ubah Kelompok'),
            'routeIndex'    => route('kelompoks.index'),
            'action'        => route('kelompoks.update', [$kelompok->id])
        ]);
    }

    /**
     * update data to db
     *
     * @param KelompokRequest $request
     * @param Kelompok $kelompok
     * @return Response
     */
    public function update(KelompokRequest $request, Kelompok $kelompok)
    {
        $data = $request->only([
			'ketua_email',
			'anggota_email',
        ]);

        // gunakan jika ada file
        // if ($request->hasFile('file')) {
        //     $data['file'] = $this->fileService->methodName($request->file('file'));
        // }

        $newData = $this->kelompokRepository->update($data, $kelompok->id);

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

        logUpdate("Kelompok", $kelompok, $newData);

        $successMessage = successMessageUpdate("Kelompok");
        return redirect()->back()->with('successMessage', $successMessage);
    }

    /**
     * delete user from db
     *
     * @param Kelompok $kelompok
     * @return Response
     */
    public function destroy(Kelompok $kelompok)
    {
        // delete file from storage if exists
        // $this->fileService->methodName($kelompok);

        // use this if you want to create notification data
        // $title = 'Notify Title';
        // $content = 'lorem ipsum dolor sit amet';
        // $userId = 2;
        // $notificationType = 'transaksi masuk';
        // $icon = 'bell'; // font awesome
        // $bgColor = 'primary'; // primary, danger, success, warning
        // $this->NotificationRepository->createNotif($title,  $content, $userId,  $notificationType, $icon, $bgColor);

        // gunakan jika mau kirim email
        // $this->emailService->methodName($kelompok);

        $this->kelompokRepository->delete($kelompok->id);
        logDelete("Kelompok", $kelompok);

        $successMessage = successMessageDelete("Kelompok");
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

        $data = $this->kelompokRepository->getLatest();
        return Excel::download(new KelompokExport($data), 'kelompoks.xlsx');
    }

    /**
     * import excel file to db
     *
     * @param \App\Http\Requests\ImportExcelRequest $request
     * @return Response
     */
    public function importExcel(\App\Http\Requests\ImportExcelRequest $request)
    {
        Excel::import(new KelompokImport, $request->file('import_file'));
        $successMessage = successMessageImportExcel("Kelompok");
        return redirect()->back()->with('successMessage', $successMessage);
    }

    /**
     * download export data as json
     *
     * @return Response
     */
    public function json()
    {
        $data = $this->kelompokRepository->getLatest();
        return $this->fileService->downloadJson($data, 'kelompoks.json');
    }

    /**
     * download export data as xlsx
     *
     * @return Response
     */
    public function excel()
    {
        $data = $this->kelompokRepository->getLatest();
        return (new KelompokExport($data))->download('kelompoks.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
     * download export data as csv
     *
     * @return Response
     */
    public function csv()
    {
        $data = $this->kelompokRepository->getLatest();
        return (new KelompokExport($data))->download('kelompoks.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    /**
     * download export data as pdf
     *
     * @return Response
     */
    public function pdf()
    {
        $data = $this->kelompokRepository->getLatest();
        return PDF::setPaper('Letter', 'landscape')
            ->loadView('stisla.kelompoks.export-pdf', [
                'data'    => $data,
                'isPrint' => false
            ])
            ->download('kelompoks.pdf');
    }

    /**
     * export data to print html
     *
     * @return Response
     */
    public function exportPrint()
    {
        $data = $this->kelompokRepository->getLatest();
        return view('stisla.kelompoks.export-pdf', [
            'data'    => $data,
            'isPrint' => true
        ]);
    }
}
