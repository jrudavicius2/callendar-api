<?php

namespace AppBundle\Command;

use AppBundle\Entity\Category;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DevCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('dev');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new Client();
        $response = $client->request(
            'GET',
            $this->getContainer()->getParameter('base_api_url') . 'categories',
            [
                'headers' => [
                    'Content-type' => 'application/json',
                    'Accept' => '*/*',
                ]
            ]
        );

//        $curl = curl_init($this->getContainer()->getParameter('base_api_url') . '/categories');
//        curl_setopt(
//            $curl,
//            CURLOPT_HTTPHEADER,
//            [
//                'Content-type: application/json',
//                'Accept: */*',
//            ]
//        );
//
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//        $response = curl_exec($curl);
//        var_dump($response->getStatusCode());
//        var_dump(json_decode($response->getBody()->getContents()));

//        $categories = [];
//        $categoryArray = json_decode($response->getBody()->getContents());
//        var_dump($categoryArray);
//        var_dump(count($categoryArray));
//        if ($response->withStatus(200) && count($categoryArray) > 0) {
//            foreach ($categoryArray as $category) {
//                $categories[] = (new Category())
//                    ->setId($category->id)
//                    ->setName($category->name)
//                ;
//            }
//        }

        print_r($response->getHeaders());
    }
}
