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
        $status = 'fail';
        if ($request->get('amount') && $request->get('productID') && $request->get('priceTotal')) {
            $em = $this->getDoctrine()->getManager();

            $purchase = new Purchase();

            $product = $em->getRepository('ShopBundle:Product')->findOneBy(array('id' => $request->get('productID')));
            $purchase->setProductId($product);

            $purchase->setAmount($request->get('amount'));
            $purchase->setTotalPrice($request->get('priceTotal'));
            $purchase->setDate(new \DateTime());
            $em->persist($purchase);
            $em->flush();

            $status = 'success';
        }

        return new JsonResponse(array('status' => $status));
    }
}