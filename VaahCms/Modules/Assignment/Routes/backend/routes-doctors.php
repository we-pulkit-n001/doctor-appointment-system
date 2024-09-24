<?php

use VaahCms\Modules\Assignment\Http\Controllers\Backend\DoctorsController;

Route::group(
    [
        'prefix' => 'backend/assignment/doctors',
        
        'middleware' => ['web', 'has.backend.access'],
        
],
function () {
    /**
     * Get Assets
     */
    Route::get('/assets', [DoctorsController::class, 'getAssets'])
        ->name('vh.backend.assignment.doctors.assets');
    /**
     * Get List
     */
    Route::get('/', [DoctorsController::class, 'getList'])
        ->name('vh.backend.assignment.doctors.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [DoctorsController::class, 'updateList'])
        ->name('vh.backend.assignment.doctors.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [DoctorsController::class, 'deleteList'])
        ->name('vh.backend.assignment.doctors.list.delete');


    /**
     * Fill Form Inputs
     */
    Route::any('/fill', [DoctorsController::class, 'fillItem'])
        ->name('vh.backend.assignment.doctors.fill');

    /**
     * Create Item
     */
    Route::post('/', [DoctorsController::class, 'createItem'])
        ->name('vh.backend.assignment.doctors.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [DoctorsController::class, 'getItem'])
        ->name('vh.backend.assignment.doctors.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [DoctorsController::class, 'updateItem'])
        ->name('vh.backend.assignment.doctors.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [DoctorsController::class, 'deleteItem'])
        ->name('vh.backend.assignment.doctors.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [DoctorsController::class, 'listAction'])
        ->name('vh.backend.assignment.doctors.list.actions');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [DoctorsController::class, 'itemAction'])
        ->name('vh.backend.assignment.doctors.item.action');

    //---------------------------------------------------------

});
