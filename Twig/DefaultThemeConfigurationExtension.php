<?php

namespace YsTools\Bundle\DefaultThemeConfigurationBundle\Twig;

use Oro\Bundle\AttachmentBundle\Entity\File;
use Oro\Bundle\AttachmentBundle\Manager\AttachmentManager;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DefaultThemeConfigurationExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('dtc_company_logo', [$this, 'getCompanyLogo']),
            new TwigFunction('dtc_company_name', [$this, 'getCompanyName']),
        ];
    }

    /**
     * @return string|null
     */
    public function getCompanyLogo()
    {
        $id = $this->container->get('oro_config.manager')->get('ystools_dtc.company_logo');
        if (!$id) {
            return null;
        }

        /** @var File $image */
        $image = $this->container->get('oro_entity.doctrine_helper')->getEntity(File::class, $id);
        if (!$image) {
            return null;
        }

        return $this->container->get('oro_attachment.manager')->getFilteredImageUrl($image, 'dtc_company_logo');
    }

    /**
     * @return string|null
     */
    public function getCompanyName()
    {
        return $this->container->get('oro_config.manager')->get('ystools_dtc.company_name');
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return [
            'oro_config.manager' => ConfigManager::class,
            'oro_entity.doctrine_helper' => DoctrineHelper::class,
            'oro_attachment.manager' => AttachmentManager::class,
        ];
    }
}
