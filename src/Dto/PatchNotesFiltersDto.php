<?php

/**
 * Patch notes filters DTO.
 */

namespace App\Dto;

/**
 * Class PatchNotesFiltersDto.
 */
class PatchNotesFiltersDto
{
    /**
     * Constructor.
     *
     * @param string|null $search
     * @param string      $sort
     */
    public function __construct(public readonly ?string $search = null, public readonly string $sort = 'DESC')
    {
    }
}
