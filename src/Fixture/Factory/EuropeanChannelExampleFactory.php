<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin\Fixture\Factory;

use Prometee\SyliusVIESClientPlugin\Entity\EuropeanChannelAwareInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EuropeanChannelExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var ChannelFactoryInterface */
    private $channelFactory;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var RepositoryInterface */
    private $countryRepository;

    /** @var RepositoryInterface */
    private $zoneRepository;

    /** @var OptionsResolver */
    private $optionsResolver;

    public function __construct(
        ChannelFactoryInterface $channelFactory,
        ChannelRepositoryInterface $channelRepository,
        RepositoryInterface $countryRepository,
        RepositoryInterface $zoneRepository
    ) {
        $this->channelFactory = $channelFactory;
        $this->channelRepository = $channelRepository;
        $this->countryRepository = $countryRepository;
        $this->zoneRepository = $zoneRepository;

        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = []): EuropeanChannelAwareInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var EuropeanChannelAwareInterface|null $channel */
        $channel = $this->channelRepository->findOneByCode($options['code']);

        if ($channel === null) {
            throw new ChannelNotFoundException(
                sprintf(
                    'Channel "%s" has not been found, please create it before adding this fixture !',
                    $options['code']
                )
            );
        }

        $channel->setBaseCountry($options['base_currency']);
        $channel->setEuropeanZone($options['european_zone']);

        return $channel;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('code', function (Options $options): string {
                return StringInflector::nameToCode($options['name']);
            })
            ->setAllowedTypes('base_country', ['string', CountryInterface::class])
            ->setNormalizer('base_country', LazyOption::findOneBy($this->countryRepository, 'code'))
            ->setAllowedTypes('european_zone', ['string', ZoneInterface::class])
            ->setNormalizer('european_zone', LazyOption::findOneBy($this->zoneRepository, 'code'))
        ;
    }
}
