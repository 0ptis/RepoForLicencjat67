<?php
/**
 * ThreadListInputFiltersDtoResolver
 */
namespace App\Resolver;

use App\Dto\ThreadListInputFiltersDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * ThreadListInputFiltersDtoResolver class.
 */
class ThreadListInputFiltersDtoResolver implements ValueResolverInterface
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

        if (!$argumentType || !is_a($argumentType, ThreadListInputFiltersDto::class, true)) {
            return [];
        }
        $search = $request->query->get('search');

        return [new ThreadListInputFiltersDto($search)];
    }
}
