<?php
declare(strict_types=1);
namespace OnlineBooking\src\Constants;

final class BookingConstants
{
    public const string CONTACT_NUMBER_FORMAT = '/^(\+63|0)9\d{9}$/';

    public const string CONTACT_NUMBER_HINT = "SAMPLE FORMAT: 09XXXXXXXXX";

    public const string PASSWORD_FORMAT = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';

    public const string PASSWORD_HINT = "Your password must contain 8 or more characters and 1 uppercase, lowercase, a number
            and a special character.";

    public const string NUMBER_PLATE_FORMAT = '/^[A-Z]{3}\s\d{3,4}$/';

    public const string NUMBER_PLATE_HINT = "SAMPLE PLATE NUMBER FORMAT: ABC 123";

}