<?php
use VaahCms\Modules\Assignment\Http\Controllers\Backend\PatientsController;
/*
 * API url will be: <base-url>/public/api/assignment/patients
 */
Route::group(
    [
        'prefix' => 'assignment/patients',
        'namespace' => 'Backend',
    ],
function () {

    /**
     * Get Assets
     */
    Route::get('/assets', [PatientsController::class, 'getAssets'])
        ->name('vh.backend.assignment.api.patients.assets');
    /**
     * Get List
     */
    Route::get('/', [PatientsController::class, 'getList'])
        ->name('vh.backend.assignment.api.patients.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [PatientsController::class, 'updateList'])
        ->name('vh.backend.assignment.api.patients.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [PatientsController::class, 'deleteList'])
        ->name('vh.backend.assignment.api.patients.list.delete');


    /**
     * Create Item
     */
    Route::post('/', [PatientsController::class, 'createItem'])
        ->name('vh.backend.assignment.api.patients.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [PatientsController::class, 'getItem'])
        ->name('vh.backend.assignment.api.patients.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [PatientsController::class, 'updateItem'])
        ->name('vh.backend.assignment.api.patients.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [PatientsController::class, 'deleteItem'])
        ->name('vh.backend.assignment.api.patients.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [PatientsController::class, 'listAction'])
        ->name('vh.backend.assignment.api.patients.list.action');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [PatientsController::class, 'itemAction'])
        ->name('vh.backend.assignment.api.patients.item.action');



});
