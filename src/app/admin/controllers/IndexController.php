<?php

namespace Multi\Admin\Controllers;

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
    }

    public function addproductAction()
    {
        if ($this->request->getPost('addproduct')) {
            if (empty($this->request->getPost('product_name')) || empty($this->request->getPost('product_category')) || empty($this->request->getPost('product_price')) || empty($this->request->getPost('product_stock'))) {
                $this->view->productaddmsg = "<span class='text-danger'>**All fields required</span>";
            } else {
                // $empty = array();
                // for ($i = 0; $i < count($data['field']); $i++) {
                //     $empty[$data['field'][$i]] = $data['value'][$i];
                // }

                $field = $this->request->getPost('field');
                $value = $this->request->getPost('value');
                $additional = array_combine($field, $value);

                $variation1 = $this->request->getPost('variation1');
                $variation2 = $this->request->getPost('variation2');
                $variation3 = $this->request->getPost('variation3');
                $variation = array(
                    "variation1" => $variation1,
                    "variation2" => $variation2,
                    "variation3" => $variation3,
                );
                $this->mongo->products->insertOne([
                    "product_name" => $this->request->getPost('product_name'),
                    "product_category" => $this->request->getPost('product_category'),
                    "product_price" => $this->request->getPost('product_price'),
                    "product_stock" => $this->request->getPost('product_stock'),
                    "additional" => $additional,
                    "varitions" => $variation
                ]);
                $this->view->productaddmsg = "<span class='text-success'>Product Added Successfully!!</span>";
            }
        }
    }

    public function productAction()
    {
        $data = $this->mongo->products->find();
        $this->view->list = $data;

        if ($this->request->getPost('search')) {
            $productsearch = $this->request->getPost('name');
            $product = array();
            foreach ($data as $k => $v) {
                if (strtolower($v->product_name) == strtolower($productsearch)) {
                    array_push($product, $v);
                }
            }
            $this->view->product =  $product;
        }
    }

    public function productactionAction()
    {
        if ($this->request->getPost('delete')) {
            $id = $this->request->getPost('delval');
            $data = $this->mongo->products->deleteOne([
                "_id" => new \MongoDB\BSON\ObjectID($id)
            ]);

            $this->response->redirect('/index/product');
        }

        if ($this->request->getPost('edit')) {

            $id = $this->request->getPost('delval');
            $data = $this->mongo->products->find([
                '_id' => new \MongoDB\BSON\ObjectID($id)
            ]);
            foreach ($data as $k => $v) {
                $this->view->data = $v;
            }
        }
    }

    public function updateproductAction()
    {

        $id = $this->request->getPost('id');
        // $product_name = $this->request->getPost('product_name');
        // $product_category = $this->request->getPost('product_category');
        // $product_price = $this->request->getPost('product_price');
        // $product_stock = $this->request->getPost('product_stock');

        $field = $this->request->getPost('field');
        $value = $this->request->getPost('value');
        $additional = array_combine($field, $value);
        $data = array(
            "product_name" => $this->request->getPost('product_name'),
            "product_category" => $this->request->getPost('product_category'),
            "product_price" => $this->request->getPost('product_price'),
            "product_stock" => $this->request->getPost('product_stock'),
            "additional" => $additional
        );

        $this->mongo->products->updateOne(["_id" => new MongoDB\BSON\ObjectID($id)], ['$set' => $data]);
        $this->response->redirect('/index/product');
    }
}


