<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\KelompokRequest;
use App\Models\Kelompok;
use App\Repositories\KelompokRepository;
use App\Repositories\NotificationRepository;
use Illuminate\Http\JsonResponse;
use App\Services\EmailService;
use App\Services\FileService;

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
        $this->kelompokRepository      = new KelompokRepository;
        $this->fileService            = new FileService;
        $this->emailService           = new EmailService;
        $this->NotificationRepository = new NotificationRepository;

        $this->middleware('can:Kelompok');
        $this->middleware('can:Kelompok Tambah')->only(['create', 'store']);
        $this->middleware('can:Kelompok Ubah')->only(['edit', 'update']);
        $this->middleware('can:Kelompok Hapus')->only(['destroy']);
    }

    /**
     * get data as pagination
     *
     * @return JsonResponse
     */
    public function index()
    {
        $data = $this->kelompokRepository->getPaginate();
        $successMessage = successMessageLoadData("Kelompok");
        return response200($data, $successMessage);
    }

    /**
     * get detail data
     *
     * @param Kelompok $kelompok
     * @return JsonResponse
     */
    public function show(Kelompok $kelompok)
    {
        $successMessage = successMessageLoadData("Kelompok");
        return response200($kelompok, $successMessage);
    }

    /**
     * save new data to db
     *
     * @param KelompokRequest $request
     * @return JsonResponse
     */
    public function store(KelompokRequest $request)
    {
        $data = $request->only([
			'ketua_email',
			'anggota_email',
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

        $result = $this->kelompokRepository->create($data);
        logCreate('Kelompok', $result);

        $successMessage = successMessageCreate("Kelompok");
        return response200($result, $successMessage);
    }

    /**
     * update data to db
     *
     * @param KelompokRequest $request
     * @param Kelompok $kelompok
     * @return JsonResponse
     */
    public function update(KelompokRequest $request, Kelompok $kelompok)
    {
        $data = $request->only([
			'ketua_email',
			'anggota_email',
        ]);

        // bisa digunakan jika ada upload file dan ganti methodnya
        // if ($request->hasFile('file')) {
        //     $data['file'] = $this->fileService->uploadCrudExampleFile($request->file('file'));
        // }

        $result = $this->kelompokRepository->update($data, $kelompok->id);

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

        logUpdate('Kelompok', $kelompok, $result);

        $successMessage = successMessageUpdate("Kelompok");
        return response200($result, $successMessage);
    }

    /**
     * delete data from db
     *
     * @param Kelompok $kelompok
     * @return JsonResponse
     */
    public function destroy(Kelompok $kelompok)
    {
        $deletedRow = $this->kelompokRepository->delete($kelompok->id);

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

        logDelete('Kelompok', $kelompok);

        $successMessage = successMessageDelete("Kelompok");
        return response200($deletedRow, $successMessage);
    }
}
