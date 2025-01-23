<?php

use Illuminate\Http\Request;
// use Google\Cloud\Storage\Notification;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\notiController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\BackUpController;
use App\Http\Controllers\FilesOptController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\FilesExportController;
use App\Http\Controllers\InvitationController;
// use App\Http\Controllers\NotiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/sanctum/csrf-cookie', function (Request $request) {
    return response()->json(['message' => 'CSRF cookie set']);
});


Route::post('sendNotification', [notiController::class, 'send']);
//to get the FCT for each device


Route::middleware(['auth:sanctum', 'aspect'])->group(function () {
    Route::post('saveFCT', [NotiController::class, 'saveFCT']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::post('joinGroups/{group_id}', [GroupController::class, 'JoinGroups']);
    Route::post('leaveGroups/{group_id}', [GroupController::class, 'LeaveGroups']);
    Route::post('startGroups', [GroupController::class, 'StartGroups']);

    Route::get('myGroup', [GroupController::class, 'myGroups']);
    Route::get('getGroups', [GroupController::class, 'getGroups']);
    Route::get('getPoepleGroups/{group_id}', [GroupController::class, 'getPoepleGroups']);

    Route::get('getNotifications', [NotiController::class, 'getNotifications']);
    Route::get('deleteNotification/{id}', [NotiController::class, 'deleteNotification']);

    Route::post('serachPeople/{group_id}', [GroupController::class, 'serachPeople']);

    Route::middleware(['locking'])->group(function () {
        Route::post('lockFile', [FilesOptController::class, 'lockFile']);
        Route::post('unlockFile', [FilesOptController::class, 'unlockFile']);
        Route::get('DownloadFile/{file_id}', [FilesController::class, 'DownloadFile']);
    });


    Route::post('checkout/{file_id}', [FilesOptController::class, 'checkout']);



    Route::post('addFile/{group_id}', [FilesController::class, 'addFile']);
    Route::post('deleteFile/{file_id}', [FilesController::class, 'deleteFile']);
    Route::get('getfiles/{group_id}', [FilesController::class, 'getFiles']);

    //
    Route::get('getFilesForCheck/{group_id}', [FilesController::class, 'getFilesForCheck']);
    Route::post('fileRespond/{file_id}', [FilesController::class, 'fileRespond']);
    //



    Route::post('getLockedFilesByUser', [FilesOptController::class, 'getLockedFilesByUser']);

    Route::get('exportFileReportToPdf/{file_id}/{type_id}', [FilesExportController::class, 'exportFileReportToPdf']);
    Route::get('exportFileReportToCsv/{file_id}/{type_id}', [FilesExportController::class, 'exportFileReportToCsv']);

    Route::post('makeBackUpFile/{file_id}', [BackUpController::class, 'makeBackUpFile']);
    Route::get('getFileBackUp/{file_id}', [BackUpController::class, 'getFile']);



    Route::post('/invitations/send', [InvitationController::class, 'sendInvitation']);
    Route::post('/invitations/respond/{id}', [InvitationController::class, 'respondToInvitation']);
    Route::get('/groups/{id}/users-not-in', [InvitationController::class, 'getUsersNotInGroup']);
    Route::get('receivedInvitations', [InvitationController::class, 'receivedInvitations']);
    Route::get('sendInvitations', [InvitationController::class, 'sentInvitations']);
    Route::get('deleteInvitations/{invID}', [InvitationController::class, 'deleteInvitations']);



    Route::get('/backUpFile/{filename}', function ($filename) {
        $path = public_path('backUpFile/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    });

    Route::middleware(['admin'])->group(function () {
        ////
    });
});

Route::post('getdd', [FilesExportController::class, 'getdd']);
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

