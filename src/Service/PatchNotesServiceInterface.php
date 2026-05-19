<?php
/**
 * Interface PatchNotesServiceInterface
 */
namespace App\Service;

use App\Entity\PatchNotes;

/**
 * Interface PatchNotesServiceInterface
 */
interface PatchNotesServiceInterface
{
    /**
     * @param PatchNotes $patchNotes
     *
     * @return void
     */
    public function save(PatchNotes $patchNotes): void;

    /**
     * @param PatchNotes $patchNotes
     *
     * @return void
     */
    public function delete(PatchNotes $patchNotes): void;
}
