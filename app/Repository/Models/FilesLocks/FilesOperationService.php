<?php

namespace App\Repository\Models\FilesLocks;

use App\Models\Files;
use App\Models\Groups;
use App\Repository\Repo;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Models\file_reservation_logs;
use App\Classes\FireBaseServices\FirebaseService;
use App\Repository\Models\BackUpFile\BackUpService;
use App\Repository\Models\Interface\Files\LockUnLockFile;
use App\Repository\Models\Notification\NotificationService;
use Illuminate\Support\Facades\Notification;
use App\Notifications\UnlockFileNotification;

class FilesOperationService extends Repo implements LockUnLockFile
{
    use ResponseTrait;
    protected $notificationService;
    protected $backupService;
    public function __construct()
    {
        $this->notificationService = new FirebaseService();
        $this->backupService = new BackUpService();
        parent::__construct(file_reservation_logs::class);
    }

    public function LockFiles(array $file_ids)
    {
        $lockedFiles = [];
        $errors = [];

        try {
            DB::transaction(function () use ($file_ids, &$lockedFiles, &$errors) {
                foreach ($file_ids as $file_id) {
                    $file = Files::where('id', $file_id)->lockForUpdate()->first();

                    if (!$file) {
                        $errors[] = "File with ID {$file_id} does not exist.";
                        return;
                    }

                    if (!is_null($file->locked_by)) {
                        $errors[] = "File with ID {$file_id} is already locked by another user.";
                        return;
                    }
                }

                foreach ($file_ids as $file_id) {
                    $file = Files::where('id', $file_id)->lockForUpdate()->first();

                    $file->locked_by = auth()->user()->id;
                    $file->locked_at = now();
                    $file->save();

                    parent::create([
                        'file_id' => $file->id,
                        'user_id' => auth()->user()->id,
                        'action' => 'lock',
                    ]);

                    $lockedFiles[] = $file_id;
                }
            });

            if (empty($errors)) {
                $data = $this->notificationService->getUserToNotifi($file_ids[0]);
                $this->notificationService->sendNotifiToUser($data, 'some file are lock');
                // Notify all users in the group
                $groupUsers = $this->getUsersInGroupByFile($file_ids[0]);
                $notificationData = [
                    'file_id' => $file_ids[0],
                    'message' => 'A file in your group has been locked successfully',
                ];
                Notification::send($groupUsers, new UnlockFileNotification($notificationData));
                return $this->apiResponse('All files locked successfully', ['locked_files' => $lockedFiles], 200);
            } else {
                return $this->apiResponse('Could not lock files because some are already locked or do not exist.', [
                    'errors' => $errors
                ], 400);
            }
        } catch (\Exception $e) {
            return $this->apiResponse($e->getMessage(), null, 400);
        }
    }

    public function UnlockFiles(array $file_ids)
    {
        $unlockedFiles = [];
        $errors = [];

        try {
            DB::transaction(function () use ($file_ids, &$unlockedFiles, &$errors) {
                foreach ($file_ids as $file_id) {
                    $file = Files::where('id', $file_id)->lockForUpdate()->first();

                    if (!$file) {
                        $errors[] = "File with ID {$file_id} does not exist.";
                        return;
                    }

                    if ($file->locked_by !== auth()->user()->id) {
                        $errors[] = "You are not authorized to unlock file with ID {$file_id}.";
                        return;
                    }
                }

                foreach ($file_ids as $file_id) {
                    $file = Files::where('id', $file_id)->lockForUpdate()->first();

                    $file->locked_by = null;
                    $file->locked_at = null;
                    $file->save();

                    file_reservation_logs::create([
                        'file_id' => $file->id,
                        'user_id' => auth()->user()->id,
                        'action' => 'unlock',
                    ]);

                    $unlockedFiles[] = $file_id;
                }
            });

            if (empty($errors)) {

                $data = $this->notificationService->getUserToNotifi($file_ids[0]);
                $this->notificationService->sendNotifiToUser($data, 'some file are unlock');
                return $this->apiResponse('All files unlocked successfully', ['unlocked_files' => $unlockedFiles], 200);
            } else {
                return $this->apiResponse('Could not unlock files due to authorization errors or missing files.', [
                    'errors' => $errors
                ], 400);
            }
        } catch (\Exception $e) {
            return $this->apiResponse($e->getMessage(), null, 400);
        }
    }


    public function getLockedFilesByUser()
    {
        $lockedFiles = Files::where('locked_by', auth()->id())->get();

        return $this->apiResponse('Locked files retrieved successfully', $lockedFiles, 200);
    }


    public function checkout($request, int $file_id)
    {
        $lockedFiles = [];
        $errors = [];

        try {
            DB::transaction(function () use ($file_id, &$lockedFiles, &$errors) {
                $file = Files::where('id', $file_id)->lockForUpdate()->first();
                if (!$file) {
                    $errors[] = "File with ID {$file_id} does not exist.";
                    return;
                }
                if ($file->locked_by !== auth()->user()->id) {
                    $errors[] = "You are not authorized to unlock file with ID {$file_id}.";
                    return;
                }

                $file = Files::where('id', $file_id)->lockForUpdate()->first();
                $file->locked_by = null;
                $file->locked_at = null;
                $file->save();

                parent::create([
                    'file_id' => $file->id,
                    'user_id' => auth()->user()->id,
                    'action' => 'unlock',
                ]);

                $lockedFiles[] = $file_id;
            });

            if (empty($errors)) {
                $data = $this->notificationService->getUserToNotifi($file_id);
                $this->notificationService->sendNotifiToUser($data, 'some file are unlock');
                
                // Notify all users in the group
                $groupUsers = $this->getUsersInGroupByFile($file_id);
                $notificationData = [
                    'file_id' => $file_id,
                    'message' => 'A file in your group has been unlocked successfully',
                ];
                Notification::send($groupUsers, new UnlockFileNotification($notificationData));

                $res = $this->backupService->makeBackUpFile($request, $file_id);
                return $this->apiResponse('All files unlocked successfully', $res, 200);
            } else {
                return $this->apiResponse('Could not unlock files because some are already unlock or do not exist.', [
                    'errors' => $errors
                ], 400);
            }
        } catch (\Exception $e) {
            return $this->apiResponse($e->getMessage(), null, 400);
        }
    }

    private function getUsersInGroupByFile(int $file_id)
    {
        $file = Files::findOrFail($file_id);
        $group = $file->group;
        return $group->users; 
    }
}
