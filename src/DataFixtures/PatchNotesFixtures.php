<?php

/**
 * Patch notes fixtures.
 */

namespace App\DataFixtures;

use App\Entity\PatchNotes;

/**
 * Class PatchNotesFixtures.
 */
class PatchNotesFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     *
     * @return void
     */
    protected function loadData(): void
    {
        $this->createMany(15, 'patch_notes', function () {
            $patchNotes = new PatchNotes();
            $title = ucfirst($this->faker->words(3, true));
            $patchNotes->setTitle($title);
            $patchNotes->setContent($this->faker->paragraphs(3, true));
            $date = $this->faker->dateTimeBetween('-6 months', 'now');
            $patchNotes->setCreatedAt(\DateTimeImmutable::createFromMutable($date));

            return $patchNotes;
        });
    }
}
