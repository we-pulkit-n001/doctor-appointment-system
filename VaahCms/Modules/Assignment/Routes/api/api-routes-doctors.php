<?php
use VaahCms\Modules\Assignment\Http\Controllers\Backend\DoctorsController;
/*
 * API url will be: <base-url>/public/api/assignment/doctors
 */
Route::group(
    [
        'prefix' => 'assignment/doctors',
        'namespace' => 'Backend',
    ],
function () {

    /**
     * Get Assets
     */
    Route::get('/assets', [DoctorsController::class, 'getAssets'])
        ->name('vh.backend.assignment.api.doctors.assets');
    /**
     * Get List
     */
    Route::get('/', [DoctorsController::class, 'getList'])
        ->name('vh.backend.assignment.api.doctors.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [DoctorsController::class, 'updateList'])
        ->name('vh.backend.assignment.api.doctors.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [DoctorsController::class, 'deleteList'])
        ->name('vh.backend.assignment.api.doctors.list.delete');


    /**
     * Create Item
     */
    Route::post('/', [DoctorsController::class, 'createItem'])
        ->name('vh.backend.assignment.api.doctors.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [DoctorsController::class, 'getItem'])
        ->name('vh.backend.assignment.api.doctors.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [DoctorsController::class, 'updateItem'])
        ->name('vh.backend.assignment.api.doctors.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [DoctorsController::class, 'deleteItem'])
        ->name('vh.backend.assignment.api.doctors.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [DoctorsController::class, 'listAction'])
        ->name('vh.backend.assignment.api.doctors.list.action');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [DoctorsController::class, 'itemAction'])
        ->name('vh.backend.assignment.api.doctors.item.action');



});
