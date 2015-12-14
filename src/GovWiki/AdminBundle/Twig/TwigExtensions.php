<?php

namespace GovWiki\AdminBundle\Twig;
use GovWiki\DbBundle\Entity\Environment;

/**
 * Class TwigExtensions
 * @package GovWiki\AdminBundle\Twig
 */
class TwigExtensions extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'gov_wiki.admin';
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('roles_beautify', [
                $this,
                'rolesBeautify',
            ]),

            new \Twig_SimpleFilter('name_beautify', [
                $this,
                'nameBeautify',
            ]),

            new \Twig_SimpleFilter('env_name', [
                $this,
                'environmentName'
            ]),
        ];
    }

    /**
     * @param array $roles Roles names array.
     *
     * @return array
     */
    public function rolesBeautify(array $roles = [])
    {
        $result = [];
        foreach ($roles as $role) {
            if ('ROLE_SUPER_ADMIN' !== $role) {
                // Remove ROLE_ prefix
                $result[] = strtolower(substr($role, 5));
            }
        }
        return $result;
    }

    /**
     * @param string $name Field name.
     *
     * @return string
     */
    public function nameBeautify($name)
    {
        /*
         * Split name by uppercase letters.
         */
        return ucfirst(preg_replace('/([A-Z])/', ' $1', $name));
    }

    /**
     * @param Environment|string $environment A Environment instance or name.
     *
     * @return string
     */
    public function environmentName($environment)
    {
        if ($environment instanceof Environment) {
            return $environment->getName();
        }
        return $environment;
    }
}
