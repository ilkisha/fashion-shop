<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class OrderFilterDto
{
    #[Assert\Choice(
        choices: ['paid', 'pending'],
        message: 'Status must be either "paid" or "pending".'
    )]
    public ?string $status = null;
}
