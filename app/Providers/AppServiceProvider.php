<?php

namespace App\Providers;

use App\Features\Note\Models\ChecklistNoteContent;
use App\Features\Note\Models\TextNoteContent;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            TextNoteContent::morphType() => TextNoteContent::class,
            ChecklistNoteContent::morphType() => ChecklistNoteContent::class,
        ]);
    }
}
