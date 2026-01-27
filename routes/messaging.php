<?php

use App\Http\Controllers\Messaging\ConversationController;
use App\Http\Controllers\Messaging\MessageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Messaging Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('messaging')->name('messaging.')->group(function () {
    
    // Main messaging interface
    Route::get('/', [ConversationController::class, 'index'])->name('index');
    
    // Admin chat (uses admin layout)
    Route::get('/admin-chat', [ConversationController::class, 'adminChat'])->name('admin.chat')->middleware('role:admin');

    // API Routes
    Route::prefix('api')->name('api.')->group(function () {
        
        // Conversations
        Route::get('/conversations', [ConversationController::class, 'list'])->name('conversations.list');
        Route::post('/conversations', [ConversationController::class, 'store'])->name('conversations.store');
        Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
        Route::put('/conversations/{conversation}', [ConversationController::class, 'update'])->name('conversations.update');
        Route::delete('/conversations/{conversation}', [ConversationController::class, 'destroy'])->name('conversations.destroy');
        Route::post('/conversations/{conversation}/pin', [ConversationController::class, 'togglePin'])->name('conversations.pin');
        Route::post('/conversations/{conversation}/archive', [ConversationController::class, 'toggleArchive'])->name('conversations.archive');

        // Messages
        Route::get('/conversations/{conversation}/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store'])->name('messages.store');
        Route::post('/conversations/{conversation}/read', [MessageController::class, 'markAsRead'])->name('messages.read');
        Route::put('/messages/{message}', [MessageController::class, 'update'])->name('messages.update');
        Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

        // Reactions
        Route::post('/messages/{message}/reactions', [MessageController::class, 'addReaction'])->name('messages.reactions.add');
        Route::delete('/messages/{message}/reactions', [MessageController::class, 'removeReaction'])->name('messages.reactions.remove');

        // Attachments
        Route::post('/conversations/{conversation}/attachments', [App\Http\Controllers\Messaging\AttachmentController::class, 'store'])->name('attachments.store');
        Route::get('/attachments/{attachment}/download', [App\Http\Controllers\Messaging\AttachmentController::class, 'download'])->name('attachments.download');
        Route::delete('/attachments/{attachment}', [App\Http\Controllers\Messaging\AttachmentController::class, 'destroy'])->name('attachments.destroy');

        // User Status
        Route::get('/status', [App\Http\Controllers\Messaging\StatusController::class, 'show'])->name('status.show');
        Route::put('/status', [App\Http\Controllers\Messaging\StatusController::class, 'update'])->name('status.update');
        Route::post('/status/heartbeat', [App\Http\Controllers\Messaging\StatusController::class, 'heartbeat'])->name('status.heartbeat');
        Route::post('/status/offline', [App\Http\Controllers\Messaging\StatusController::class, 'offline'])->name('status.offline');
        Route::post('/status/batch', [App\Http\Controllers\Messaging\StatusController::class, 'batch'])->name('status.batch');

        // Users search (for mentions and new conversations)
        Route::get('/users/search', function(\Illuminate\Http\Request $request) {
            $query = $request->get('q', '');
            $users = \App\Models\User::where('id', '!=', auth()->id())
                ->where('name', 'like', "%{$query}%")
                ->limit(10)
                ->get(['id', 'name', 'email']);
            return response()->json($users);
        })->name('users.search');
    });
});
