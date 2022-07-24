<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Critic;
use App\Entity\Review;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class ReviewAdmin extends AbstractAdmin
{
	public function toString(object $object): string
	{
		return $object instanceof Review ? $object->getName() : 'Review';
	}

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('name')
            ->add('summary')
            ->add('mpaa_rating')
            ->add('publication_date')
            ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
            ->add('name')
			->add('critic.name')
            ->add('summary')
            ->add('mpaa_rating')
            ->add('publication_date')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('name', TextType::class)
            ->add('summary', TextareaType::class)
            ->add('mpaa_rating', TextType::class)
            ->add('publication_date', DateType::class, [
				'widget' => 'single_text',
			])
            ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('name')
            ->add('summary')
            ->add('mpaa_rating')
            ->add('publication_date')
            ;
    }
}
