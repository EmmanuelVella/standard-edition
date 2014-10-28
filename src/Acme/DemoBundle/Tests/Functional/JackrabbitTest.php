<?php

namespace Acme\DemoBundle\Tests\Functional;

use Acme\DemoBundle\Tests\WebTestCase;

class JackrabbitTest extends WebTestCase
{
    public function testConnectionsAreNotClosed()
    {
        $options = $this->getContainer()->getParameter('phpcr_backend');
        $urlParts = parse_url($options['url']);
        $port = $urlParts['port'];

        $client = $this->createClient();

        $iteration = 0;

        while (true) {
            $client->request('GET', '/');

            $this
                ->getContainer()
                ->get('doctrine_phpcr.session')
                ->getTransport()
                ->logout()
            ;

            $connectionsCount = exec('netstat -n -A inet |grep '.$port.' | wc -l');

            echo sprintf('Iteration %s - %s connections to port %s%s', $iteration++, $connectionsCount, $port, "\r");
            ob_flush();
        }
    }
}

