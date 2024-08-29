<?php

namespace App\Extendables\Providers;

use App\Extendables\Core\Cache\CacheHelper;
use App\Extendables\Core\Cache\RedisCacheHelper;
use App\Extendables\Core\Http\Enums\HttpRequestParamEnum;
use App\Extendables\Core\Http\Request\States\QueryString\FilterQueryStringState;
use App\Extendables\Core\Http\Request\States\QueryString\JsonApi\JsonApiFilterQueryStringState;
use App\Extendables\Core\Http\Request\States\QueryString\JsonApi\JsonApiOnlyQueryStringState;
use App\Extendables\Core\Http\Request\States\QueryString\JsonApi\JsonApiPaginateQueryStringState;
use App\Extendables\Core\Http\Request\States\QueryString\JsonApi\JsonApiRelationQueryStringState;
use App\Extendables\Core\Http\Request\States\QueryString\JsonApi\JsonApiSortQueryStringState;
use App\Extendables\Core\Http\Request\States\QueryString\OnlyQueryStringState;
use App\Extendables\Core\Http\Request\States\QueryString\PaginateQueryStringState;
use App\Extendables\Core\Http\Request\States\QueryString\RelationQueryStringState;
use App\Extendables\Core\Http\Request\States\QueryString\SortQueryStringState;
use App\Extendables\Core\Http\Response\FluggFormatResponseBuilder;
use App\Extendables\Core\Http\Response\ResponseBuilder;
use App\Extendables\Core\Ports\File\DummyFileStoragePort;
use App\Extendables\Core\Ports\File\FileStoragePort;
use App\Extendables\Core\Ports\File\S3FileStoragePort;
use App\Extendables\Core\Ports\File\S3WithCloudFrontFileStoragePort;
use App\Extendables\Core\Ports\Mail\DummyMailPort;
use App\Extendables\Core\Ports\Mail\MailPort;
use App\Extendables\Core\Ports\Mail\NativeMailPort;
use App\Extendables\Core\Ports\Notification\DummyNotificationPort;
use App\Extendables\Core\Ports\Notification\NativeNotificationPort;
use App\Extendables\Core\Ports\Notification\NotificationPort;
use App\Extendables\Core\Ports\RemoteLog\DummyRemoteLogPort;
use App\Extendables\Core\Ports\RemoteLog\RemoteLogPort;
use App\Extendables\Core\Ports\RemoteLog\SlackLogPort;
use App\Extendables\Core\Utils\ExtendedLengthAwarePaginator;
use Aws\CloudFront\UrlSigner;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class ExtendableServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // service binding
        $this->app->bind(
            FileStoragePort::class,
            function () {
                if ($this->app->environment('testing')) {
                    return new DummyFileStoragePort();
                }

                $rootDir = config('filesystems.root_dir', '');
                $defaultTempUrlDuration = config('filesystems.temp_url_duration', 30);

                $assetUrl = config('filesystems.disks.s3.url');
                $cloudFrontKeyPairId = config('filesystems.disks.s3.cloudfront_key_pair_id');
                $cloudFrontPrivateKey = config('filesystems.disks.s3.cloudfront_private_key');

                $isCloudFrontSetup = $assetUrl && $cloudFrontKeyPairId && $cloudFrontPrivateKey;
                if ($isCloudFrontSetup) {
                    $urlSigner = new UrlSigner(
                        $cloudFrontKeyPairId,
                        base_path($cloudFrontPrivateKey)
                    );
                    return new S3WithCloudFrontFileStoragePort($urlSigner, $rootDir, $defaultTempUrlDuration);
                }

                return new S3FileStoragePort($rootDir, $defaultTempUrlDuration);
            }
        );
        $this->app->bind(MailPort::class, function () {
            if ($this->app->environment('testing')) {
                return new DummyMailPort();
            }

            return new NativeMailPort();
        });
        $this->app->bind(NotificationPort::class, function () {
            if ($this->app->environment('testing')) {
                return new DummyNotificationPort();
            }

            return new NativeNotificationPort();
        });
        $this->app->bind(RemoteLogPort::class, function () {
            if ($this->app->environment('testing')) {
                return new DummyRemoteLogPort();
            }

            return new SlackLogPort();
        });

        // state binding
        $request = request();
        $this->app->singleton(
            RelationQueryStringState::class,
            fn () => new JsonApiRelationQueryStringState($request->query(HttpRequestParamEnum::INCLUDE->value))
        );
        $this->app->singleton(
            SortQueryStringState::class,
            fn () => new JsonApiSortQueryStringState($request->query(HttpRequestParamEnum::SORT->value))
        );
        $this->app->singleton(
            FilterQueryStringState::class,
            fn () => new JsonApiFilterQueryStringState($request->query(HttpRequestParamEnum::FILTER->value))
        );
        $this->app->singleton(
            PaginateQueryStringState::class,
            fn () => new JsonApiPaginateQueryStringState($request->query(HttpRequestParamEnum::PAGINATE->value))
        );
        $this->app->singleton(
            OnlyQueryStringState::class,
            fn () => new JsonApiOnlyQueryStringState($request->query(HttpRequestParamEnum::ONLY->value))
        );

        // others binding
        $this->app->bind(
            ResponseBuilder::class,
            FluggFormatResponseBuilder::class
        );
        $this->app->bind(
            LengthAwarePaginator::class,
            ExtendedLengthAwarePaginator::class
        );
        $this->app->bind(
            CacheHelper::class,
            RedisCacheHelper::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Builder::macro('whereEmpty', function (string $field) {
            return $this->where(
                fn(Builder $query) => $query->whereNull($field)->orWhere($field, '=', '')
            );
        });
        EloquentBuilder::macro('whereEmpty', function (string $field) {
            return $this->where(
                fn(EloquentBuilder $query) => $query->whereNull($field)->orWhere($field, '=', '')
            );
        });

        Builder::macro('whereNotEmpty', function (string $field) {
            return $this->whereNotNull($field)->where($field, '<>', '');
        });
        EloquentBuilder::macro('whereNotEmpty', function (string $field) {
            return $this->whereNotNull($field)->where($field, '<>', '');
        });

        Str::macro('replaceSlash', function (string $str, string $replace = '-'): string {
            return str_replace(['/', '\\'], $replace, $str);
        });
        Str::macro('hashSha256', function (string $str): string {
            return hash('sha256', $str);
        });
    }
}
