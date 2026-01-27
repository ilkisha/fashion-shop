<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class ProductFilterDto
{
    public const ALLOWED_GENDERS = ['men', 'women', 'unisex'];

    #[Assert\Choice(choices: self::ALLOWED_GENDERS, message: 'Invalid gender.')]
    public ?string $gender = null;

    #[Assert\Length(max: 255)]
    public ?string $category = null;

    #[Assert\Length(max: 255)]
    public ?string $q = null;
}
