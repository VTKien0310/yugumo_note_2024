<?php

use App\Extendables\Core\Http\Route\RouteInvoker;

RouteInvoker::invokeWebRoute('authentication');

Route::get('/', fn() => view('modules.note.pages.list-note-page'))->name('note.list');
