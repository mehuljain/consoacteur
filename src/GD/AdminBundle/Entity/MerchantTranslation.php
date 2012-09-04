<?php

namespace GD\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Stof\DoctrineExtensionsBundle\Entity\AbstractTranslation;

/**
 * GD\AdminBundle\Entity\MerchantTranslation
 *
 * @ORM\Entity(repositoryClass="Gedmo\Translatable\Entity\Repository\MerchantTranslationRepository")
 * @ORM\Table(
 *         name="merchant_translations",
 *         indexes={@ORM\index(name="translations_lookup_idx", columns={
 *             "locale", "object_class", "foreign_key"
 *         })},
 *         uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *             "locale", "object_class", "foreign_key", "field"
 *         })}
 * )
 */
class MerchantTranslation extends AbstractTranslation
{
}