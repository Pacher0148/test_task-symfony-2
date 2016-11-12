<?php
namespace ShopBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class PurchaseAdmin extends AbstractAdmin
{
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('user_id', null, array('route'=>array('username'=>'show')))
            ->add('product_id', null, array('route'=>array('name'=>'show')))
            ->add('date')
            ->add('total_price')
            ->add('amount')
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('show');
        $collection->remove('create');
        $collection->remove('delete');
    }
}