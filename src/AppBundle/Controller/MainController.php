<?php

namespace AppBundle\Controller;

use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MainController extends Controller
{
    protected $per_page = 3;

    /**
     * @param $page integer
     * @return Response
     *
     * @Route("/", name="homepage")
     */
    public function indexAction(int $page = 1)
    {
        /**
         * @var $repo ProductRepository
         */
        $repo = $this->getRepository('Product');

        $data['categories'] = $this->getRepository('Category')->findAll();

        $offset = ($page - 1) * $this->per_page;
        $data['products'] = $repo->findBy([],[],$this->per_page,$offset);
        $data['pages_count'] = ceil($repo->getCountOfAllProducts()/$this->per_page);
        $data['current_page'] = $page;
        $data['title'] = 'Homepage';

        return $this->render('main/index.html.twig', [
            'data' => $data,
        ]);
    }

    /**
     * @param int $page
     * @return Response
     *
     * @Route("/page/{page}", name="home_pagination")
     */
    public function homePaginationAction(int $page)
    {
        return $this->indexAction($page);
    }

    /**
     * @param $category_url string
     * @param $page integer
     * @return RedirectResponse | Response | JsonResponse
     *
     * @Route("/catalog/{category_url}/{page}", name="category_view")
     *
     */
    public function categoryViewAction(string $category_url, int $page = 1)
    {
        /**
         * @var $repo CategoryRepository
         */
        $repo = $this->getRepository('Category');
        $category = $repo->findOneBy(['urlPath' => $category_url]);

        if (null === $category) {
            return $this->redirectToRoute('not_found');
        }

        $data['categories'] = $repo->findAll();
        /**
         * @var $prod_repo ProductRepository
         */
        $prod_repo = $this->getRepository('Product');

        $offset = ($page - 1) * $this->per_page;
        $data['products'] = $prod_repo->findBy(['category' => $category],[],$this->per_page,$offset);
        $data['pages_count'] = ceil($prod_repo->getCountByCategory($category)/$this->per_page);
        $data['current_cat'] = $category;
        $data['current_page'] = $page;
        $data['title'] = $category->getName();

        return $this->render('main/index.html.twig', [
            'data' => $data
        ]);
    }

    /**
     * @param $product_id integer
     * @return RedirectResponse | Response
     *
     * @Route("/product/{product_id}", name="product_view")
     *
     */
    public function productViewAction(int $product_id)
    {
        $data['product'] = $this->getRepository('Product')->find($product_id);

        if (null === $data['product']) {
            return $this->redirectToRoute('not_found');
        }

        $data['categories'] = $this->getRepository('Category')->findAll();
        $data['title'] = $data['product']->getName();

        return $this->render('main/product.html.twig', [
            'data' => $data
        ]);
    }

    // TODO: Ajax endless pagination controller

    /**
     * @Route("/404", name="not_found")
     */
    public function notFoundAction()
    {
        return $this->render('main/404.html.twig');
    }

    /**
     * @Route("/random/category", name="random_category")
     */
    public function randomCategoryAction()
    {
        $categories = $this->getRepository('Category')->findAll();
        $lucky_num = rand(0,(count($categories) - 1));

        return $this->redirectToRoute('category_view',['category_url' => $categories[$lucky_num]->getUrlPath(), 'page' => 1]);
    }

    /**
     * @Route("/random/product", name="random_product")
     */
    public function randomProductAction()
    {
        $products = $this->getRepository('Product')->findAll();
        $lucky_num = rand(0,(count($products) - 1));

        return $this->redirectToRoute('product_view',['product_id' => $products[$lucky_num]->getId()]);
    }

    /**
     * @param $name string
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository($name)
    {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('AppBundle:'.$name);
    }
}
