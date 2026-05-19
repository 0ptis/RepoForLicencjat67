<?php

/**
 * Abstract fixtures.
 */

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

/**
 * Class AbstractBaseFixtures.
 */
abstract class AbstractBaseFixtures extends Fixture
{
    protected ?Generator $faker = null;

    protected ?ObjectManager $manager = null;

    /**
     * Load.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker = Factory::create();
        $this->loadData();
    }

    /**
     * Load data.
     */
    abstract protected function loadData(): void;

    /**
     * @param int      $count
     * @param string   $referenceGroupName
     * @param callable $factory
     */
    protected function createMany(int $count, string $referenceGroupName, callable $factory): void
    {
        for ($i = 0; $i < $count; ++$i) {
            /** @var object|null $entity */
            $entity = $factory($i);

            if (null === $entity) {
                throw new \LogicException('Did you forget to return the entity object from your callback to BaseFixture::createMany()?');
            }

            $this->manager->persist($entity);

            // store for usage later than groupName_#COUNT#
            $this->addReference(sprintf('%s_%d', $referenceGroupName, $i), $entity);
        }

        $this->manager->flush();
    }

    /**
     * @param string $referenceGroupName
     * @param string $className
     *
     * @return object
     */
    protected function getRandomReference(string $referenceGroupName, string $className): object
    {
        $referenceNameList = $this->getReferenceNameListByClassName($referenceGroupName, $className);
        $randomReferenceName = (string) $this->faker->randomElement($referenceNameList);

        return $this->getReference($randomReferenceName, $className);
    }

    /**
     * @param string $referenceGroupName
     * @param string $className
     * @param int    $count
     *
     * @return object[]
     */
    protected function getRandomReferenceList(string $referenceGroupName, string $className, int $count): array
    {
        $referenceNameList = $this->getReferenceNameListByClassName($referenceGroupName, $className);
        $references = [];
        while (count($references) < $count) {
            $randomReferenceName = (string) $this->faker->randomElement($referenceNameList);
            $references[] = $this->getReference($randomReferenceName, $className);
        }

        return $references;
    }

    /**
     * @param string $referenceGroupName
     * @param string $className
     *
     * @return array
     */
    private function getReferenceNameListByClassName(string $referenceGroupName, string $className): array
    {
        if (!array_key_exists($className, $this->referenceRepository->getIdentitiesByClass())) {
            throw new \InvalidArgumentException(sprintf('Did not find any references saved with the name "%s"', $className));
        }

        $referenceNameListByClass = array_keys($this->referenceRepository->getIdentitiesByClass()[$className]);

        if ([] === $referenceNameListByClass) {
            throw new \InvalidArgumentException(sprintf('Did not find any references saved with the name "%s"', $className));
        }

        $referenceNameList = array_filter(
            $referenceNameListByClass,
            fn ($referenceName) => preg_match_all("/^{$referenceGroupName}_\\d+\$/", $referenceName)
        );

        if ([] === $referenceNameList) {
            throw new \InvalidArgumentException(sprintf('Did not find any references saved with the group name "%s" and class name "%s"', $referenceGroupName, $className));
        }

        return $referenceNameList;
    }
}
