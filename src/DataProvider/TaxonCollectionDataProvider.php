<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\PaginationExtension;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use BitBag\SyliusGraphqlPlugin\Doctrine\Repository\TaxonRepositoryInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class TaxonCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private TaxonRepositoryInterface $taxonRepository;

    private PaginationExtension $paginationExtension;

    private UserContextInterface $userContext;

    /** @see QueryCollectionExtensionInterface */
    private iterable $collectionExtensions;

    private QueryNameGeneratorInterface $queryNameGenerator;

    public function __construct(
        TaxonRepositoryInterface $taxonRepository,
        PaginationExtension $paginationExtension,
        UserContextInterface $userContext,
        QueryNameGeneratorInterface $queryNameGenerator,
        iterable $collectionExtensions
    )
    {
        $this->taxonRepository = $taxonRepository;
        $this->paginationExtension = $paginationExtension;
        $this->userContext = $userContext;
        $this->queryNameGenerator = $queryNameGenerator;
        $this->collectionExtensions = $collectionExtensions;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, TaxonInterface::class, true);
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        Assert::keyExists($context, ContextKeys::CHANNEL);
        $channelMenuTaxon = $context[ContextKeys::CHANNEL]->getMenuTaxon();

        $user = $this->userContext->getUser();
        if ($user !== null && in_array('ROLE_API_ACCESS', $user->getRoles())) {
            return $this->taxonRepository->findAll();
        }

        $queryBuilder = $this->taxonRepository->createChildrenByChannelMenuTaxonQueryBuilder(
            $channelMenuTaxon
        );
        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($queryBuilder, $this->queryNameGenerator, $resourceClass, $operationName, $context);

            if ($extension instanceof QueryResultCollectionExtensionInterface && $extension->supportsResult($resourceClass, $operationName, $context)) {
                return $extension->getResult($queryBuilder, $resourceClass, $operationName, $context);
            }
        }

        return $this->paginationExtension->getResult(
            $queryBuilder,
            $resourceClass,
            $operationName,
            $context
        );
    }
}