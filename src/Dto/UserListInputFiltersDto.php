<?php

/**
 * User list input filters DTO.
 */

namespace App\Dto;

/**
 * Class UserListInputFiltersDto.
 */
class UserListInputFiltersDto
{
    /**
     * Constructor.
     *
     * @param string|null $searchQuery
     */
    public function __construct(public readonly ?string $searchQuery = null)
    {
    }
}
