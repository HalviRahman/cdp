<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProposalRequest;
use App\Models\Proposal;
use App\Repositories\ProposalRepository;
use App\Repositories\NotificationRepository;
use Illuminate\Http\JsonResponse;
use App\Services\EmailService;
use App\Services\FileService;

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
        $this->proposalRepository      = new ProposalRepository;
        $this->fileService            = new FileService;
        $this->emailService           = new EmailService;
        $this->NotificationRepository = new NotificationRepository;

        $this->middleware('can:Proposal');
        $this->middleware('can:Proposal Tambah')->only(['create', 'store']);
        $this->middleware('can:Proposal Ubah')->only(['edit', 'update']);
        $this->middleware('can:Proposal Hapus')->only(['destroy']);
    }

    /**
     * get data as pagination
     *
     * @return JsonResponse
     */
    public function index()
    {
        $data = $this->proposalRepository->getPaginate();
        $successMessage = successMessageLoadData("Proposal");
        return response200($data, $successMessage);
    }

    /**
     * get detail data
     *
     * @param Proposal $proposal
     * @return JsonResponse
     */
    public function show(Proposal $proposal)
    {
        $successMessage = successMessageLoadData("Proposal");
        return response200($proposal, $successMessage);
    }

    /**
     * save new data to db
     *
     * @param ProposalRequest $request
     * @return JsonResponse
     */
    public function store(ProposalRequest $request)
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

        $result = $this->proposalRepository->create($data);
        logCreate('Proposal', $result);

        $successMessage = successMessageCreate("Proposal");
        return response200($result, $successMessage);
    }

    /**
     * update data to db
     *
     * @param ProposalRequest $request
     * @param Proposal $proposal
     * @return JsonResponse
     */
    public function update(ProposalRequest $request, Proposal $proposal)
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

        $result = $this->proposalRepository->update($data, $proposal->id);

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

        logUpdate('Proposal', $proposal, $result);

        $successMessage = successMessageUpdate("Proposal");
        return response200($result, $successMessage);
    }

    /**
     * delete data from db
     *
     * @param Proposal $proposal
     * @return JsonResponse
     */
    public function destroy(Proposal $proposal)
    {
        $deletedRow = $this->proposalRepository->delete($proposal->id);

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

        logDelete('Proposal', $proposal);

        $successMessage = successMessageDelete("Proposal");
        return response200($deletedRow, $successMessage);
    }
}
