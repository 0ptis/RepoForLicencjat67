<?php

/**
 * Thread list input filters DTO.
 */

namespace App\Dto;

/**
 * Class ThreadListInputFiltersDto.
 */
class ThreadListInputFiltersDto
{
    /**
     * Constructor.
     *
     * @param string|null $search
     */
    public function __construct(public readonly ?string $search = null)
    {
    }
}
