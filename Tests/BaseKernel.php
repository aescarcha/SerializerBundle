<?php
namespace Aescarcha\SerializerBundle\Tests;

/**
 * Test the post entity
 */
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BaseKernel extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    protected $Posts, $Users;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
        $this->fixturize( $this->em );
        $this->Posts = $this->em->getRepository('AescarchaChallengeBundle:Post');
        $this->Users = $this->em->getRepository('AescarchaUserBundle:User');
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    protected function fixturize( $doctrine )
    {
        $this->truncateAllTables( $doctrine );
        $fixtures = [
            new \Aescarcha\UserBundle\DataFixtures\ORM\LoadUserData(),
            new \Aescarcha\ChallengeBundle\DataFixtures\ORM\LoadPostData(),
            new \Aescarcha\NotificationBundle\DataFixtures\ORM\LoadNotificationData(),
            new \Aescarcha\NotificationBundle\DataFixtures\ORM\LoadNominationData(),
        ];
        foreach($fixtures as $fixture){
            if($fixture instanceof \Symfony\Component\DependencyInjection\ContainerAwareInterface){
                $fixture->setContainer( static::$kernel->getContainer() );
            }
            $fixture->load($doctrine);
        }
    }

    protected function truncateAllTables( $entityManager )
    {
        $connection = $entityManager->getConnection();
        $connection->executeUpdate("SET foreign_key_checks = 0;");

        $schemaManager = $connection->getSchemaManager();
        $tables = $schemaManager->listTables();
        $query = '';
        foreach($tables as $table) {
            $name = $table->getName();
            $query .= 'TRUNCATE ' . $name . ';';
        }
        $connection->executeQuery($query, array(), array());
        $connection->executeUpdate("SET foreign_key_checks = 1;");
    }

    /**
     * Test to remove phpunit warnings
     * @return [type] [description]
     * 
     *
     */
    public function testNothing()
    {
        $this->assertEquals(1, 1);
    }
}
