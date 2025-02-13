<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProgramStudiRequest;
use App\Models\ProgramStudi;
use App\Repositories\ProgramStudiRepository;
use App\Repositories\NotificationRepository;
use Illuminate\Http\JsonResponse;
use App\Services\EmailService;
use App\Services\FileService;

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
     * constructor method
     *
     * @return void
     */
    public function __construct()
    {
        $this->programStudiRepository      = new ProgramStudiRepository;
        $this->fileService            = new FileService;
        $this->emailService           = new EmailService;
        $this->NotificationRepository = new NotificationRepository;

        $this->middleware('can:Program Studi');
        $this->middleware('can:Program Studi Tambah')->only(['create', 'store']);
        $this->middleware('can:Program Studi Ubah')->only(['edit', 'update']);
        $this->middleware('can:Program Studi Hapus')->only(['destroy']);
    }

    /**
     * get data as pagination
     *
     * @return JsonResponse
     */
    public function index()
    {
        $data = $this->programStudiRepository->getPaginate();
        $successMessage = successMessageLoadData("Program Studi");
        return response200($data, $successMessage);
    }

    /**
     * get detail data
     *
     * @param ProgramStudi $programStudi
     * @return JsonResponse
     */
    public function show(ProgramStudi $programStudi)
    {
        $successMessage = successMessageLoadData("Program Studi");
        return response200($programStudi, $successMessage);
    }

    /**
     * save new data to db
     *
     * @param ProgramStudiRequest $request
     * @return JsonResponse
     */
    public function store(ProgramStudiRequest $request)
    {
        $data = $request->only([
			'nama_prodi',
			'kuota',
        ]);

        // bisa digunakan jika ada upload file dan ganti methodnya
        // if ($request->hasFile('file')) {
        //     $data['file'] = $this->fileService->uploadCrudExampleFile($request->file('file'));
        // }

        // use this if you want to create notification data
        // $title = 'Notify Title';
        // $content = 'lorem ipsum dolor sit amet';
        // $userId = 2;
        // $notificationType = 'transaksi masuk';
        // $icon = 'bell'; // font awesome
        // $bgColor = 'primary'; // primary, danger, success, warning
        // $this->NotificationRepository->createNotif($title,  $content, $userId,  $notificationType, $icon, $bgColor);

        // bisa digunakan jika ingim kirim email dan ganti methodnya
        // $this->emailService->methodName($params);

        $result = $this->programStudiRepository->create($data);
        logCreate('Program Studi', $result);

        $successMessage = successMessageCreate("Program Studi");
        return response200($result, $successMessage);
    }

    /**
     * update data to db
     *
     * @param ProgramStudiRequest $request
     * @param ProgramStudi $programStudi
     * @return JsonResponse
     */
    public function update(ProgramStudiRequest $request, ProgramStudi $programStudi)
    {
        $data = $request->only([
			'nama_prodi',
			'kuota',
        ]);

        // bisa digunakan jika ada upload file dan ganti methodnya
        // if ($request->hasFile('file')) {
        //     $data['file'] = $this->fileService->uploadCrudExampleFile($request->file('file'));
        // }

        $result = $this->programStudiRepository->update($data, $programStudi->id);

        // use this if you want to create notification data
        // $title = 'Notify Title';
        // $content = 'lorem ipsum dolor sit amet';
        // $userId = 2;
        // $notificationType = 'transaksi masuk';
        // $icon = 'bell'; // font awesome
        // $bgColor = 'primary'; // primary, danger, success, warning
        // $this->NotificationRepository->createNotif($title,  $content, $userId,  $notificationType, $icon, $bgColor);

        // bisa digunakan jika ingim kirim email dan ganti methodnya
        // $this->emailService->methodName($params);

        logUpdate('Program Studi', $programStudi, $result);

        $successMessage = successMessageUpdate("Program Studi");
        return response200($result, $successMessage);
    }

    /**
     * delete data from db
     *
     * @param ProgramStudi $programStudi
     * @return JsonResponse
     */
    public function destroy(ProgramStudi $programStudi)
    {
        $deletedRow = $this->programStudiRepository->delete($programStudi->id);

        // use this if you want to create notification data
        // $title = 'Notify Title';
        // $content = 'lorem ipsum dolor sit amet';
        // $userId = 2;
        // $notificationType = 'transaksi masuk';
        // $icon = 'bell'; // font awesome
        // $bgColor = 'primary'; // primary, danger, success, warning
        // $this->NotificationRepository->createNotif($title,  $content, $userId,  $notificationType, $icon, $bgColor);

        // bisa digunakan jika ingim kirim email dan ganti methodnya
        // $this->emailService->methodName($params);

        logDelete('Program Studi', $programStudi);

        $successMessage = successMessageDelete("Program Studi");
        return response200($deletedRow, $successMessage);
    }
}
