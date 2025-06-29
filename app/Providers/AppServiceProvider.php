<?php

namespace App\Providers;

use App\Extendables\Core\Http\Enums\HttpRequestParamEnum;
use App\Features\Note\Models\ChecklistNoteContent;
use App\Features\Note\Models\Note;
use App\Features\Note\Models\TextNoteContent;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Relation::enforceMorphMap([
            Note::morphType() => Note::class,
            TextNoteContent::morphType() => TextNoteContent::class,
            ChecklistNoteContent::morphType() => ChecklistNoteContent::class,
        ]);

        Carbon::macro('toLocalizedString', function (): string {
            $timezone = request()->query(HttpRequestParamEnum::TIMEZONE->value, 'Asia/Ho_Chi_Minh');

            return $this->setTimezone($timezone)->toDateTimeString();
        });
    }
}
