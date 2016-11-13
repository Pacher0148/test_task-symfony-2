<?php

namespace ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ShopBundle\Entity\Purchase;

class MainController extends Controller
{
    public function indexAction()
    {
        $productList = $this->getDoctrine()->getRepository('ShopBundle:Product')->findAll(array(), null, 15);

        return $this->render('ShopBundle:Main:index.html.twig', array('productList' => $productList));
    }

    public function purchaseAction(Request $request)
    {
        $loggerError = $this->get('error_logger');
        $user = $this->getUser();
        $status = 'fail';
        $case = '';

        if ($user && $request->get('amount') && $request->get('productID') && $request->get('priceTotal')) {
            $em = $this->getDoctrine()->getManager();
            $product = $em->getRepository('ShopBundle:Product')->findOneBy(array('id' => $request->get('productID')));

            if ($product && $product->getStorage() > $request->get('amount') && $request->get('amount') > 0 && $request->get('priceTotal') > 0) {
                $purchase = new Purchase();
                $purchase->setProductId($product);
                $purchase->setUserId($user);

                $purchase->setAmount($request->get('amount'));
                $purchase->setTotalPrice($request->get('priceTotal'));
                $purchase->setDate(new \DateTime());
                $em->persist($purchase);
                $em->flush();

                $product->setStorage($product->getStorage() - $request->get('amount'));
                $em->persist($product);
                $em->flush();

                $loggerInfo = $this->get('info_logger');
                $loggerInfo->info('New purchase('.$purchase->getId().') created');
                $loggerInfo->info('Product('.$product->getId().') storage changed to '.$product->getStorage());

                $status = 'success';
            } else {
                if ($product)
                    $loggerError->error('Purchase fail', array('cause' => 'Product not found'));
                if ($product->getStorage() > $request->get('amount'))
                    $loggerError->error('Purchase fail', array('cause' => 'Purchase amount is bigger than storage'));
                if ($request->get('amount') <= 0)
                    $loggerError->error('Purchase fail', array('cause' => 'Incorrect amount'));
                if ($request->get('priceTotal') <= 0)
                    $loggerError->error('Purchase fail', array('cause' => 'Incorrect priceTotal'));

                $case = 'Incorrect data was been send';
            }
        } else {
            if (!$user)
                $loggerError->error('Purchase fail', array('cause' => 'Unauthorized user'));
            if (!$request->get('amount'))
                $loggerError->error('Purchase fail', array('cause' => 'Amount wasn\'t send'));
            if (!$request->get('productID'))
                $loggerError->error('Purchase fail', array('cause' => 'ProductID wasn\'t send'));
            if (!$request->get('priceTotal'))
                $loggerError->error('Purchase fail', array('cause' => 'Total price wasn\'t send'));

            $case = 'Sending process error';
        }

        return new JsonResponse(array('status' => $status, 'case' => $case));
    }
}