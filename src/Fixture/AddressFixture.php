<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin\Fixture;

use Sylius\Bundle\CoreBundle\Fixture\AddressFixture as BaseAddressFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class AddressFixture extends BaseAddressFixture
{
    public function getName(): string
    {
        return 'address_with_vat_number';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        parent::configureResourceNode($resourceNode);

        $node = $resourceNode->children();
        $node->scalarNode('vat_number');
    }
}
