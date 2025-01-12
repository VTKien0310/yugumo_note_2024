<?php

namespace App\Providers;

use App\Features\Note\Models\ChecklistNoteContent;
use App\Features\Note\Models\Note;
use App\Features\Note\Models\TextNoteContent;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;
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
            Note::morphType() => Note::class,
            TextNoteContent::morphType() => TextNoteContent::class,
            ChecklistNoteContent::morphType() => ChecklistNoteContent::class,
        ]);

        Carbon::macro('toLocalizedString', function (): string {
            $timezone = request()->query('timezone', 'Asia/Ho_Chi_Minh');

            return $this->setTimezone($timezone)->toDateTimeString();
        });
    }
}
