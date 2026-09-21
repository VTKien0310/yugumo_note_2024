<?php

namespace App\Extendables\Core\Http\Enums;

enum ValidationErrorCodeEnum: string
{
    /* Built-in validation's error codes */
    case ACCEPTED = 'must_be_boolean_true';

    case ALPHA = 'must_be_alpha';

    case ALPHA_DASH = 'must_be_alpha_dash';

    case ALPHA_NUM = 'must_be_alpha_num';

    case ARRAY = 'must_be_array';

    case BETWEEN = 'must_be_between_specific_values';

    case BOOLEAN = 'must_be_boolean';

    case CONFIRMED = 'confirmation_field_not_match';

    case DATE = 'must_be_date';

    case DATE_EQUALS = 'invalid_date_value';

    case DATE_FORMAT = 'incorrect_date_format';

    case DECLINED = 'must_be_boolean_false';

    case DIFFERENT = 'must_have_a_different_value';

    case DIGITS = 'must_be_digits';

    case DIGITS_BETWEEN = 'must_be_digits_with_specific_length';

    case DIGIT_MAX = 'max_digit_reached';

    case DIGIT_MIN = 'min_digit_not_reached';

    case DIMENSIONS = 'must_be_image_with_specific_dimension';

    case DISTINCT = 'must_not_be_duplicated';

    case EMAIL = 'invalid_email_address';

    case ENDS_WITH = 'must_end_with_specific_value';

    case EXISTS = 'value_not_exists_in_database';

    case FILE = 'must_be_file';

    case FILLED = 'must_have_a_value';

    case GT = 'must_be_greater_than_specific_value';

    case GTE = 'must_be_greater_or_equal_specific_value';

    case IMAGE = 'must_be_image';

    case IN = 'invalid_value';

    case IN_ARRAY = 'must_exists_in_another_field';

    case INTEGER = 'must_be_integer';

    case IP = 'must_be_ip_address';

    case IPV4 = 'must_be_ip_v4_address';

    case IPV6 = 'must_be_ip_v6_address';

    case JSON = 'must_be_json_string';

    case LT = 'must_be_less_than_specific_value';

    case LTE = 'must_be_less_or_equal_specific_value';

    case MAC_ADDRESS = 'must_be_mac_address';

    case MAX = 'must_not_be_greater_than_specific_value';

    case MIMES = 'invalid_file_type';

    case MIN = 'must_not_be_less_than_specific_value';

    case MULTIPLE_OF = 'must_be_multiple_of_a_value';

    case NUMERIC = 'must_be_numeric';

    case PASSWORD = 'password_is_incorrect';

    case PRESENT = 'must_be_present';

    case PROHIBITED = 'is_prohibited';

    case PROHIBITS = 'prohibit_other_fields';

    case REQUIRED = 'required';

    case REQUIRED_ARRAY_KEYS = 'required_as_array';

    case SAME = 'must_be_same_as_another_field';

    case SIZE = 'must_have_specific_size';

    case STARTS_WITH = 'must_start_with_specific_value';

    case STRING = 'must_be_string';

    case TIMEZONE = 'invalid_timezone';

    case UNIQUE = 'already_exists_in_database';

    case UPLOADED = 'failed_to_upload';

    case URL = 'invalid_url';

    case UUID = 'invalid_uuid';

    case DOES_NOT_END_WITH = 'must_not_end_with_specific_value';

    case DOES_NOT_START_WITH = 'must_not_start_with_specific_value';

    case LOWERCASE = 'must_be_lowercase';

    case UPPERCASE = 'must_be_uppercase';
    /* Custom validation's error codes */
}
