<?php

namespace GD\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Stof\DoctrineExtensionsBundle\Entity\AbstractTranslation;

/**
 * GD\AdminBundle\Entity\CategoryTranslation
 *
 * @ORM\Entity(repositoryClass="Gedmo\Translatable\Entity\Repository\CategoryTranslationRepository")
 * @ORM\Table(
 *         name="category_translations",
 *         indexes={@ORM\index(name="translations_lookup_idx", columns={
 *             "locale", "object_class", "foreign_key"
 *         })},
 *         uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *             "locale", "object_class", "foreign_key", "field"
 *         })}
 * )
 */
class CategoryTranslation extends AbstractTranslation
{
}