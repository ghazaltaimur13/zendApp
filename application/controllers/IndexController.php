<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    }

    public function loginAction()
    {
        $form = new Application_Form_Login();
        $form->submit->setLabel('Login');
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $username = $form->getValue('username');
                $password = $form->getValue('password');
                $authAdapter = $this->getAuthAdapter($username, $password);
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);
                if(!$result->isValid()){
                    $this->view->error = "Credentials are not valid";
                } else {
                    $this->view->success = "Successfully LoggedIn";
                }
            } else {
                $form->populate($formData);
            }
        }
            
    }

    private function getAuthAdapter($username, $password){
        $authAdapter = new Zend_Auth_Adapter_DbTable(
            Zend_Db_Table_Abstract::getDefaultAdapter(),
            'users',
            'username',
            'password'
        );
        $authAdapter->setIdentity($username);
        $authAdapter->setCredential($password);
        return $authAdapter;
    }

    function getRestXml()
    {
        $xml ='<?xml version="1.0" encoding="UTF-8"?>
            <Create>
                <ClientID>1A1</ClientID>
                <UserID>efjshashajs</UserID>
                <Password>123</Password>
                <SecurityKey>123456789</SecurityKey>
                <E1>
                    <SKU>1001</SKU>
                </E1>		
            </Create>';
    
        return $xml;
    }

    public function restserverAction(){
        $this->_helper->layout->disableLayout();

        Zend_Controller_Front::getInstance()
            ->setParam('noViewRenderer', true);
        $this->_response->setHeader('Content-Type', 'text/xml; charset=utf-8')
            ->setBody($this->getRestXml());
    }

    public function restapiAction(){
        $client = new Zend_Http_Client('http://localhost/zendTest/public/index.php/index/restserver');
        $response = $client->request('GET');

        $data = simplexml_load_string($response->getBody());
        $json  = json_encode($data);
        $jsonData = json_decode($json, true);
        $this->view->xmlData = $jsonData;
    }

}







