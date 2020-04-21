<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Book;
use App\Entity\Movie;
use App\Entity\Review;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname')
            ->add('lastname')
            ->add('email')
            ->add('username')
            ->add('address')
            ->add('address2')
            ->add('city')
            ->add('zipcode')
            ->add('phone')
            ->add('step')
            ->add('informationsEnabled')
            ->add('isBanned')
            ->add('ip')
            ->add('latitude')
            ->add('longitude')
            ->add('plainPassword');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
