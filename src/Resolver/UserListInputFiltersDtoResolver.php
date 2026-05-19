<?php
/**
 * UserListInputFiltersDtoResolver
 */
namespace App\Resolver;

use App\Dto\UserListInputFiltersDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * UserListInputFiltersDtoResolver class.
 */
class UserListInputFiltersDtoResolver implements ValueResolverInterface
{
    /**
     * @param Request          $request
     * @param ArgumentMetadata $argument
     *
     * @return iterable
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();
        if (!$argumentType || !is_a($argumentType, UserListInputFiltersDto::class, true)) {
            return [];
        }
        $searchQuery = $request->query->get('q');

        return [new UserListInputFiltersDto($searchQuery)];
    }
}
