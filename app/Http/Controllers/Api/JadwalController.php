<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\JadwalRequest;
use App\Models\Jadwal;
use App\Repositories\JadwalRepository;
use App\Repositories\NotificationRepository;
use Illuminate\Http\JsonResponse;
use App\Services\EmailService;
use App\Services\FileService;

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
        $this->jadwalRepository      = new JadwalRepository;
        $this->fileService            = new FileService;
        $this->emailService           = new EmailService;
        $this->NotificationRepository = new NotificationRepository;

        $this->middleware('can:Jadwal');
        $this->middleware('can:Jadwal Tambah')->only(['create', 'store']);
        $this->middleware('can:Jadwal Ubah')->only(['edit', 'update']);
        $this->middleware('can:Jadwal Hapus')->only(['destroy']);
    }

    /**
     * get data as pagination
     *
     * @return JsonResponse
     */
    public function index()
    {
        $data = $this->jadwalRepository->getPaginate();
        $successMessage = successMessageLoadData("Jadwal");
        return response200($data, $successMessage);
    }

    /**
     * get detail data
     *
     * @param Jadwal $jadwal
     * @return JsonResponse
     */
    public function show(Jadwal $jadwal)
    {
        $successMessage = successMessageLoadData("Jadwal");
        return response200($jadwal, $successMessage);
    }

    /**
     * save new data to db
     *
     * @param JadwalRequest $request
     * @return JsonResponse
     */
    public function store(JadwalRequest $request)
    {
        $data = $request->only([
			'tgl_mulai',
			'tgl_selesai',
			'keterangan',
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

        $result = $this->jadwalRepository->create($data);
        logCreate('Jadwal', $result);

        $successMessage = successMessageCreate("Jadwal");
        return response200($result, $successMessage);
    }

    /**
     * update data to db
     *
     * @param JadwalRequest $request
     * @param Jadwal $jadwal
     * @return JsonResponse
     */
    public function update(JadwalRequest $request, Jadwal $jadwal)
    {
        $data = $request->only([
			'tgl_mulai',
			'tgl_selesai',
			'keterangan',
        ]);

        // bisa digunakan jika ada upload file dan ganti methodnya
        // if ($request->hasFile('file')) {
        //     $data['file'] = $this->fileService->uploadCrudExampleFile($request->file('file'));
        // }

        $result = $this->jadwalRepository->update($data, $jadwal->id);

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

        logUpdate('Jadwal', $jadwal, $result);

        $successMessage = successMessageUpdate("Jadwal");
        return response200($result, $successMessage);
    }

    /**
     * delete data from db
     *
     * @param Jadwal $jadwal
     * @return JsonResponse
     */
    public function destroy(Jadwal $jadwal)
    {
        $deletedRow = $this->jadwalRepository->delete($jadwal->id);

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

        logDelete('Jadwal', $jadwal);

        $successMessage = successMessageDelete("Jadwal");
        return response200($deletedRow, $successMessage);
    }
}
