<?php

use App\Extendables\Core\Ports\File\DummyFileStoragePort;
use App\Extendables\Core\Ports\File\FileStoragePort;
use App\Extendables\Core\Ports\Mail\DummyMailPort;
use App\Extendables\Core\Ports\Mail\MailPort;
use App\Extendables\Core\Ports\Notification\DummyNotificationPort;
use App\Extendables\Core\Ports\Notification\NotificationPort;
use App\Extendables\Core\Ports\RemoteLog\DummyRemoteLogPort;
use App\Extendables\Core\Ports\RemoteLog\RemoteLogPort;
use App\Extendables\Providers\ExtendableServiceProvider;
use App\Features\User\Models\User;
use Illuminate\Support\Str;

describe(ExtendableServiceProvider::class, function () {
    it('binds dummy ports under the testing environment', function () {
        expect(app(FileStoragePort::class))->toBeInstanceOf(DummyFileStoragePort::class)
            ->and(app(MailPort::class))->toBeInstanceOf(DummyMailPort::class)
            ->and(app(NotificationPort::class))->toBeInstanceOf(DummyNotificationPort::class)
            ->and(app(RemoteLogPort::class))->toBeInstanceOf(DummyRemoteLogPort::class);
    });

    it('replaces slashes and backslashes with designated delimiter via Str::replaceSlash', function () {
        expect(Str::replaceSlash('path/to\\something'))->toBe('path-to-something')
            ->and(Str::replaceSlash('foo/bar', '_'))->toBe('foo_bar');
    });

    it('generates sha256 hash string via Str::hashSha256', function () {
        $input = 'yugumo-note-test';
        expect(Str::hashSha256($input))->toBe(hash('sha256', $input));
    });

    it('filters query by empty and not-empty column macros', function () {
        $emptySql = User::query()->whereEmpty('remember_token')->toRawSql();
        expect($emptySql)->toContain('"remember_token" is null')
            ->and($emptySql)->toContain('"remember_token" = \'\'');

        $notEmptySql = User::query()->whereNotEmpty('remember_token')->toRawSql();
        expect($notEmptySql)->toContain('"remember_token" is not null')
            ->and($notEmptySql)->toContain('"remember_token" <> \'\'');
    });
});
