<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LaporanRequest;
use App\Models\Laporan;
use App\Repositories\LaporanRepository;
use App\Repositories\NotificationRepository;
use Illuminate\Http\JsonResponse;
use App\Services\EmailService;
use App\Services\FileService;

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
        $this->laporanRepository      = new LaporanRepository;
        $this->fileService            = new FileService;
        $this->emailService           = new EmailService;
        $this->NotificationRepository = new NotificationRepository;

        $this->middleware('can:Laporan');
        $this->middleware('can:Laporan Tambah')->only(['create', 'store']);
        $this->middleware('can:Laporan Ubah')->only(['edit', 'update']);
        $this->middleware('can:Laporan Hapus')->only(['destroy']);
    }

    /**
     * get data as pagination
     *
     * @return JsonResponse
     */
    public function index()
    {
        $data = $this->laporanRepository->getPaginate();
        $successMessage = successMessageLoadData("Laporan");
        return response200($data, $successMessage);
    }

    /**
     * get detail data
     *
     * @param Laporan $laporan
     * @return JsonResponse
     */
    public function show(Laporan $laporan)
    {
        $successMessage = successMessageLoadData("Laporan");
        return response200($laporan, $successMessage);
    }

    /**
     * save new data to db
     *
     * @param LaporanRequest $request
     * @return JsonResponse
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

        $result = $this->laporanRepository->create($data);
        logCreate('Laporan', $result);

        $successMessage = successMessageCreate("Laporan");
        return response200($result, $successMessage);
    }

    /**
     * update data to db
     *
     * @param LaporanRequest $request
     * @param Laporan $laporan
     * @return JsonResponse
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

        // bisa digunakan jika ada upload file dan ganti methodnya
        // if ($request->hasFile('file')) {
        //     $data['file'] = $this->fileService->uploadCrudExampleFile($request->file('file'));
        // }

        $result = $this->laporanRepository->update($data, $laporan->id);

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

        logUpdate('Laporan', $laporan, $result);

        $successMessage = successMessageUpdate("Laporan");
        return response200($result, $successMessage);
    }

    /**
     * delete data from db
     *
     * @param Laporan $laporan
     * @return JsonResponse
     */
    public function destroy(Laporan $laporan)
    {
        $deletedRow = $this->laporanRepository->delete($laporan->id);

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

        logDelete('Laporan', $laporan);

        $successMessage = successMessageDelete("Laporan");
        return response200($deletedRow, $successMessage);
    }
}
