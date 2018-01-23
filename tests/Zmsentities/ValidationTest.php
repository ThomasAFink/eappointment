<?php

namespace BO\Zmsentities\Tests;

class ValidationTest extends Base
{
    public function testTestValid()
    {
        $entity = new \BO\Zmsentities\Useraccount();
        $entity->id= "123";
        $entity->changePassword= array('test', 'testfailed');
        try {
            $entity->testValid();
            $this->fail("Expected exception SchemaValidation not thrown");
        } catch (\BO\Zmsentities\Exception\SchemaValidation $exception) {
            $this->assertEquals(400, $exception->getCode());
        }
    }

    public function testTestValidObject()
    {
        $entity = (new \BO\Zmsentities\Scope())->getExample();
        $entity->preferences['client']['emailFrom'] = "test.de";
        try {
            $entity->testValid();
            $this->fail("Expected exception SchemaValidation not thrown");
        } catch (\BO\Zmsentities\Exception\SchemaValidation $exception) {
            foreach ($exception->data as $error) {
                $this->assertContains(
                    'Die E-Mail Adresse muss eine valide E-Mail im Format max@mustermann.de sein',
                    $error['messages']
                );
            }

            $this->assertEquals(400, $exception->getCode());
        }
    }

    public function testLocale()
    {
        $entity = new \BO\Zmsentities\Useraccount();
        $entity->id= "1234";
        $entity->changePassword= array('test', 'testfailed');
        try {
            $entity->testValid();
            $this->fail("Expected exception SchemaValidation not thrown");
        } catch (\BO\Zmsentities\Exception\SchemaValidation $exception) {
            $errorList = $exception->data;
            // merge conflict, the following two lines might fail??
            $this->assertEquals('Passwortwiederholung', key($errorList));
            $this->assertArrayHasKey('minLength', $errorList['Passwortwiederholung']['messages']);
            $this->assertArrayHasKey('format', $errorList['Passwortwiederholung']['messages']);
        }
    }
}
