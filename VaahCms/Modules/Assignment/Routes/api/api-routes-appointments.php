<?php
use VaahCms\Modules\Assignment\Http\Controllers\Backend\AppointmentsController;
/*
 * API url will be: <base-url>/public/api/assignment/appointments
 */
Route::group(
    [
        'prefix' => 'assignment/appointments',
        'namespace' => 'Backend',
    ],
function () {

    /**
     * Get Assets
     */
    Route::get('/assets', [AppointmentsController::class, 'getAssets'])
        ->name('vh.backend.assignment.api.appointments.assets');
    /**
     * Get List
     */
    Route::get('/', [AppointmentsController::class, 'getList'])
        ->name('vh.backend.assignment.api.appointments.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [AppointmentsController::class, 'updateList'])
        ->name('vh.backend.assignment.api.appointments.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [AppointmentsController::class, 'deleteList'])
        ->name('vh.backend.assignment.api.appointments.list.delete');


    /**
     * Create Item
     */
    Route::post('/', [AppointmentsController::class, 'createItem'])
        ->name('vh.backend.assignment.api.appointments.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [AppointmentsController::class, 'getItem'])
        ->name('vh.backend.assignment.api.appointments.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [AppointmentsController::class, 'updateItem'])
        ->name('vh.backend.assignment.api.appointments.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [AppointmentsController::class, 'deleteItem'])
        ->name('vh.backend.assignment.api.appointments.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [AppointmentsController::class, 'listAction'])
        ->name('vh.backend.assignment.api.appointments.list.action');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [AppointmentsController::class, 'itemAction'])
        ->name('vh.backend.assignment.api.appointments.item.action');



});
