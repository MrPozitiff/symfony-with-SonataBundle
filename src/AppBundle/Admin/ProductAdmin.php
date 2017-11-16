<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 016 16.11.17
 * Time: 11:53
 */

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ProductAdmin extends AbstractAdmin
{
    /**
 * @param FormMapper $form
 */
    protected function configureFormFields(FormMapper $form)
    {
        $image =
        $form->add('name', 'text',['label' => 'Enter Product Name'])
            ->add('category', 'sonata_type_model', [
                    'class' => 'AppBundle\Entity\Category',
                    'property' => 'name'
                ]
            )
            ->add('description', 'text')
            ->add('price')
            ->add('mainImage', 'sonata_type_admin', array(
                'delete' => false
            ));
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('name')
            ->add('price');
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('name')
            ->add('price')
            ->add('mainImage', null, array(
                'associated_property' => 'filename'
            ))
            ->add('category', null, [
                'associated_property' => 'name'
            ]);
    }


}