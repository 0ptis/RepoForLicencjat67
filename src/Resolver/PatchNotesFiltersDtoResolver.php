<?php

/**
 * Patch notes filters DTO resolver.
 */

namespace App\Resolver;

use App\Dto\PatchNotesFiltersDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * Class PatchNotesFiltersDtoResolver.
 */
class PatchNotesFiltersDtoResolver implements ValueResolverInterface
{
    /**
     * Resolve argument.
     *
     * @param Request          $request
     * @param ArgumentMetadata $argument
     *
     * @return iterable
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();
        if (!$argumentType || !is_a($argumentType, PatchNotesFiltersDto::class, true)) {
            return [];
        }

        $search = $request->query->get('search');
        $sort = strtoupper($request->query->get('sort', 'DESC'));
        if (!in_array($sort, ['ASC', 'DESC'])) {
            $sort = 'DESC';
        }

        return [new PatchNotesFiltersDto($search, $sort)];
    }
}
