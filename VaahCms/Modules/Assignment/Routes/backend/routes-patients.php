<?php

use VaahCms\Modules\Assignment\Http\Controllers\Backend\PatientsController;

Route::group(
    [
        'prefix' => 'backend/assignment/patients',
        
        'middleware' => ['web', 'has.backend.access'],
        
],
function () {
    /**
     * Get Assets
     */
    Route::get('/assets', [PatientsController::class, 'getAssets'])
        ->name('vh.backend.assignment.patients.assets');
    /**
     * Get List
     */
    Route::get('/', [PatientsController::class, 'getList'])
        ->name('vh.backend.assignment.patients.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [PatientsController::class, 'updateList'])
        ->name('vh.backend.assignment.patients.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [PatientsController::class, 'deleteList'])
        ->name('vh.backend.assignment.patients.list.delete');


    /**
     * Fill Form Inputs
     */
    Route::any('/fill', [PatientsController::class, 'fillItem'])
        ->name('vh.backend.assignment.patients.fill');

    /**
     * Create Item
     */
    Route::post('/', [PatientsController::class, 'createItem'])
        ->name('vh.backend.assignment.patients.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [PatientsController::class, 'getItem'])
        ->name('vh.backend.assignment.patients.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [PatientsController::class, 'updateItem'])
        ->name('vh.backend.assignment.patients.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [PatientsController::class, 'deleteItem'])
        ->name('vh.backend.assignment.patients.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [PatientsController::class, 'listAction'])
        ->name('vh.backend.assignment.patients.list.actions');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [PatientsController::class, 'itemAction'])
        ->name('vh.backend.assignment.patients.item.action');

    //---------------------------------------------------------

});
