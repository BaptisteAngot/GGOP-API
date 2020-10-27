<?php


namespace App\Admin;


use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

final class RiotServerAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper) {
        $formMapper
            ->add('name',TextType::class)
            ->add('api_route', TextType::class)
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
            ->add('name')
            ->add('api_route')
        ;
    }

    protected function configureListFields(ListMapper $listMapper){
        $listMapper
            ->addIdentifier('name')
            ->addIdentifier('api_route')
        ;
    }
}