<?php
declare(strict_types=1);

namespace OnlineBooking\src\Models;

use OnlineBooking\src\Contracts\Validate;
use Override;

class Admin extends User implements Validate
{
    public function __construct(
      public private(set) readonly ?int $adminId,
      ?int $userId,
      string $fullName,
      string $username,
      string $password
    )
    {
        parent::__construct($userId, $fullName, $username, $password, UserRole::ADMIN);
    }

    #[Override]
    public static function fromArray(array $data): static
    {
       return new static(
           (int)$data['admin_id'],
           (int)$data['user_id'],
           $data['full_name'],
           $data['username'],
           $data['password']
       );
    }
}