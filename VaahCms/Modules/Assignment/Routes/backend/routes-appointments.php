<?php

use VaahCms\Modules\Assignment\Http\Controllers\Backend\AppointmentsController;

Route::group(
    [
        'prefix' => 'backend/assignment/appointments',

        'middleware' => ['web', 'has.backend.access'],

],
function () {
    /**
     * Get Assets
     */
    Route::get('/assets', [AppointmentsController::class, 'getAssets'])
        ->name('vh.backend.assignment.appointments.assets');
    /**
     * Get List
     */
    Route::get('/', [AppointmentsController::class, 'getList'])
        ->name('vh.backend.assignment.appointments.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [AppointmentsController::class, 'updateList'])
        ->name('vh.backend.assignment.appointments.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [AppointmentsController::class, 'deleteList'])
        ->name('vh.backend.assignment.appointments.list.delete');


    /**
     * Fill Form Inputs
     */
    Route::any('/fill', [AppointmentsController::class, 'fillItem'])
        ->name('vh.backend.assignment.appointments.fill');

    /**
     * Create Item
     */
    Route::post('/', [AppointmentsController::class, 'createItem'])
        ->name('vh.backend.assignment.appointments.create');

    /**
     * Get Dashboard Data
     */
    Route::get('/stats', [AppointmentsController::class, 'getDashboardData']);

    /**
     * Get I\tem
     */
    Route::get('/{id}', [AppointmentsController::class, 'getItem'])
        ->name('vh.backend.assignment.appointments.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [AppointmentsController::class, 'updateItem'])
        ->name('vh.backend.assignment.appointments.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [AppointmentsController::class, 'deleteItem'])
        ->name('vh.backend.assignment.appointments.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [AppointmentsController::class, 'listAction'])
        ->name('vh.backend.assignment.appointments.list.actions');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [AppointmentsController::class, 'itemAction'])
        ->name('vh.backend.assignment.appointments.item.action');

    /**
     * Export Appointments
     */
    Route::get('/appointments/export', [AppointmentsController::class, 'exportAppointmentsData'])
        ->name('vh.backend.assignment.appointments.export.action');

    /**
     * Import Appointment Data
     */
    Route::post('/import', [AppointmentsController::class, 'importAppointmentsData'])
        ->name('vh.backend.assignment.appointments.import.action');

    //---------------------------------------------------------

});
