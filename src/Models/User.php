<?php
declare(strict_types = 1);

namespace OnlineBooking\src\Models;

use OnlineBooking\src\Constants\BookingConstants;
use OnlineBooking\src\Contracts\Validate;
use OnlineBooking\src\Exceptions\InvalidDataException;
use Override;

class User implements Validate
{
   private string $newPassword;
   public function __construct(
       private(set) readonly ?int $userId,

       public string $fullName {
           get => $this->fullName;

           set {
               if (strlen(trim($value)) === 0) {
                   throw new InvalidDataException("Your full name is required.");
               }
               $this->fullName = $value;
           }
       },

       public string $username {
           get => $this->username;

           set {
               if (strlen(trim($value)) === 0) {
                   throw new InvalidDataException("Your username is required.");
               }

               $this->username = $value;
           }
       },

       private readonly string $password,
       private(set) readonly UserRole $userRole
   ) {}

    public function getHashedPassword() : string
    {
        return $this->password;
    }

    public function verifyPassword(string $plainTextPassword) : bool
    {
        return password_verify($plainTextPassword, $this->password);
    }

    public function updatePassword(string $newPassword) : void
    {
        if (!preg_match(BookingConstants::PASSWORD_FORMAT, $newPassword)) {
            throw new InvalidDataException(BookingConstants::PASSWORD_HINT);
        }
    }

    #[Override]
    public function validate(): void
    {
        $this->fullName = trim($this->fullName);
        $this->username = trim($this->username);
    }

    public static function fromArray(array $data) : static
    {
        return new static(
            (int)$data['user_id'],
            $data['full_name'],
            $data['username'],
            $data['password'],
            UserRole::from($data['user_role'])
        );
    }


}