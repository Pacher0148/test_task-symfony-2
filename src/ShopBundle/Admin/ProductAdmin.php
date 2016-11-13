<?php
namespace ShopBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

class ProductAdmin extends AbstractAdmin
{
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('description')
            ->add('price')
            ->add('storage')
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('description')
            ->add('price')
            ->add('storage')
        ;
    }

    public function postPersist($newObject)
    {
        $loggerInfo = $this->getConfigurationPool()->getContainer()->get('info_logger');
        $loggerInfo->info('New product('.$newObject->getId().') was been created');
    }

    public function preUpdate($newObject)
    {
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $originalObject = $em->getUnitOfWork()->getOriginalEntityData($newObject);
        $loggerInfo = $this->getConfigurationPool()->getContainer()->get('info_logger');

        if ($newObject->getName() != $originalObject['name'])
            $loggerInfo->info('Product('.$originalObject['id'].') name('.$originalObject['name'].') was changed to '.$newObject->getName());
        if ($newObject->getPrice() != $originalObject['price'])
            $loggerInfo->info('Product('.$originalObject['id'].') price('.$originalObject['price'].') was changed to '.$newObject->getPrice());
        if ($newObject->getStorage() != $originalObject['storage'])
            $loggerInfo->info('Product('.$originalObject['id'].') storage('.$originalObject['storage'].') was changed to '.$newObject->getStorage());
    }

    public function postRemove($object)
    {
        $loggerInfo = $this->getConfigurationPool()->getContainer()->get('info_logger');
        $loggerInfo->info('Product('.$object->getId().') was been removed');
    }

    public function preBatchAction($actionName, ProxyQueryInterface $query, array & $idx, $allElements)
    {
        if ($actionName == 'delete') {
            $loggerInfo = $this->getConfigurationPool()->getContainer()->get('info_logger');
            foreach($idx as $id)
            {
                $loggerInfo->info('Product('.$id.') was been removed');
            }
        }
    }
}