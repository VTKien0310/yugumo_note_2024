<?php

namespace App\Extendables\Core\Http\Enums;

enum CommonHttpErrorCodeEnum: string
{
    /**
     * Request error code
     */
    case SERVER_ERROR = 'server_error';

    case BAD_REQUEST = 'bad_request';

    case UNAUTHENTICATED = 'unauthenticated';

    case UNAUTHORIZED = 'unauthorized';

    case ROUTE_NOT_FOUND = 'route_not_found';

    case RELATION_NOT_FOUND = 'relation_not_found';

    case UNPROCESSABLE_ENTITY = 'unprocessable_entity';

    case VALIDATION_FAILED = 'validation_failed';

    case TOO_MANY_REQUESTS = 'too_many_requests';

    /**
     * Action error code
     */
    case SEND_EMAIL_FAILED = 'send_email_failed';

    case SEND_NOTIFICATION_FAILED = 'send_notification_failed';

    case NOT_ALLOWED_COUNT = 'not_allowed_count';

    case NOT_ALLOWED_SUM = 'not_allowed_sum';
}
