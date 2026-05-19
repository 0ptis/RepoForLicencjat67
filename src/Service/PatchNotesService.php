<?php
/**
 * PatchNotesService
 */
namespace App\Service;

use App\Entity\PatchNotes;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class PatchNotesService
 */
class PatchNotesService implements PatchNotesServiceInterface
{
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param PatchNotes $patchNotes
     *
     * @return void
     */
    public function save(PatchNotes $patchNotes): void
    {
        if (null === $patchNotes->getCreatedAt()) {
            $patchNotes->setCreatedAt(new \DateTimeImmutable());
        }

        $this->entityManager->persist($patchNotes);
        $this->entityManager->flush();
    }

    /**
     * @param PatchNotes $patchNotes
     *
     * @return void
     */
    public function delete(PatchNotes $patchNotes): void
    {
        $this->entityManager->remove($patchNotes);
        $this->entityManager->flush();
    }
}
