<?php

use App\Http\Controllers\TransportController;
use Illuminate\Support\Facades\Route;

// Routes
Route::match(['get','post'], '/transport/route', [TransportController::class, 'routes'])->name('transport.routes');
Route::match(['get','post'], '/transport/route_edit/{id}', [TransportController::class, 'routeEdit'])->name('transport.route_edit')->whereNumber('id');
Route::delete('/transport/route_delete/{id}', [TransportController::class, 'routeDelete'])->name('transport.route_delete')->whereNumber('id');
Route::post('/transport/route_delete/{id}', [TransportController::class, 'routeDelete'])->whereNumber('id');

// Vehicles
Route::match(['get','post'], '/transport/vehicle', [TransportController::class, 'vehicles'])->name('transport.vehicles');
Route::match(['get','post'], '/transport/vehicle_edit/{id}', [TransportController::class, 'vehicleEdit'])->name('transport.vehicle_edit')->whereNumber('id');
Route::delete('/transport/vehicle_delete/{id}', [TransportController::class, 'vehicleDelete'])->name('transport.vehicle_delete')->whereNumber('id');
Route::post('/transport/vehicle_delete/{id}', [TransportController::class, 'vehicleDelete'])->whereNumber('id');

// Assign
Route::match(['get','post'], '/transport/assign', [TransportController::class, 'assign'])->name('transport.assign');
Route::match(['get','post'], '/transport/assign_edit/{id}', [TransportController::class, 'assignEdit'])->name('transport.assign_edit')->whereNumber('id');
Route::delete('/transport/assign_delete/{id}', [TransportController::class, 'assignDelete'])->name('transport.assign_delete')->whereNumber('id');
Route::post('/transport/assign_delete/{id}', [TransportController::class, 'assignDelete'])->whereNumber('id');

Route::post('/transport/getVehicleByBranch', [TransportController::class, 'getVehicleByBranch'])->name('transport.getVehicleByBranch');
Route::post('/transport/getStoppageByBranch', [TransportController::class, 'getStoppageByBranch'])->name('transport.getStoppageByBranch');
