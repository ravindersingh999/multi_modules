<?php

namespace Multi\Admin\Controllers;

use Phalcon\Mvc\Controller;

class OrderController extends Controller
{
    public function indexAction()
    {
        $data = $this->mongo->products->find();
        $this->view->product = $data;

        if ($this->request->getPost('placeorder')) {
            $data = array(
                'product_name' => $this->request->getPost('product_name'),
                'customer_name' => $this->request->getPost('customer_name'),
                'product_quantity' => $this->request->getPost('product_quantity'),
                'order_status' => 'paid',
                'order_date' => date('Y-m-d')
            );

            $this->mongo->orders->insertOne($data);
        }
    }

    public function orderlistAction()
    {
        $data = $this->mongo->orders->find();

        if ($this->request->getPost('status')) {
            $order_id = $this->request->getPost('order_id');
            $status = $this->request->getPost('status');
            $order_status = array(
                "order_status" => $status
            );

            $this->mongo->orders->updateOne(["_id" => new MongoDB\BSON\ObjectID($order_id)], ['$set' => $order_status]);
            $this->response->redirect('order/orderlist');
        }

        if ($this->request->getPost('filter_status')) {
            $status_choose = $this->request->getPost('filter_status');
            // print_r($status);
            // die;
            $filter_status = array(
                "order_status" => $status_choose
            );
            $filter_by_status = $this->mongo->orders->find($filter_status);
            $this->view->order = $filter_by_status;
        }

        if ($this->request->getPost('filter_date')) {
            $date = $this->request->getPost('filter_date');

            if ($date == 'today') {
                $filter_date = array(
                    "order_date" => date('Y-m-d')
                );
                $filter_by_date = $this->mongo->orders->find($filter_date);
                $this->view->order = $filter_by_date;
            }
            if ($date == "this_week") {
                $start_date = date("Y-m-d", strtotime("-1 week"));
                $end_date = date("Y-m-d");
                $orders = array('order_date' => ['$gte' => $start_date, '$lte' => $end_date]);
                $orders = $this->mongo->orders->find($orders);
                $this->view->order = $orders;
            }
            if ($date == 'this_month') {
                $start_date = date('Y-m-d', strtotime('first day of this month'));
                $end_date = date('Y-m-d');
                $orders = array('order_date' => ['$gte' => $start_date, '$lte' => $end_date]);
                $orders = $this->mongo->orders->find($orders);
                $this->view->order = $orders;
            }

            if ($date == "custom") {
                $html = '<div>
                <input type="text" name="start_date" placeholder="Start Date"><br>
                <input type="text" name="end_date" placeholder="End Date">
            </div>';
                $this->view->html = $html;
            }
            if ($this->request->getPost('custom')) {
                $start_date = $this->request->getPost('start_date');
                $end_date = $this->request->getPost('end_date');
                $orders = array('order_date' => ['$gte' => $start_date, '$lte' => $end_date]);
                $orders = $this->mongo->orders->find($orders);
                $this->view->order = $orders;
            }
        } else {
            $this->view->order = $data;
        }
    }
}
