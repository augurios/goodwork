<?php

namespace App\Ticket\Controllers;

use Illuminate\Support\Facades\Route;

/**********************************
    Web
**********************************/

Route::middleware('web')->group(function () {
    Route::get('tickets', [TicketController::class, 'index']);

    Route::group(['middleware' => 'auth'], function () {
        Route::post('tickets', [TicketController::class, 'store']);

        Route::post('tickets/files', [TicketFileController::class, 'store']);

        Route::get('tickets/{ticket}', [TicketController::class, 'show']);

        Route::patch('tickets/{ticket}', [TicketController::class, 'update']);

        Route::delete('tickets/{ticket}', [TicketController::class, 'delete']);

        // draft ticket

        Route::get('draft-tickets', [DraftTicketController::class, 'index']);
    });
});

/**********************************
    Api
**********************************/

Route::middleware(['api', 'auth:api'])->prefix('api')->group(function () {
    Route::get('tickets', [TicketController::class, 'index']);

    Route::post('tickets', [TicketController::class, 'store']);

    Route::get('tickets/{ticket}', [TicketController::class, 'show']);

    Route::patch('tickets/{ticket}', [TicketController::class, 'update']);

    Route::delete('tickets/{ticket}', [TicketController::class, 'delete']);

    // draft ticket

    Route::get('draft-tickets', [DraftTicketController::class, 'index']);
});

