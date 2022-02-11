<?php

namespace BO\Zmsclient\Tests;

use \BO\Mellon\Validator;

class TicketprinterTest extends Base
{
    const HASH_COOKIE_NAME = 'Ticketprinter';

    const HOME_URL_COOKIE_NAME = 'Ticketprinter_Homeurl';

    const HASH_TEST = '0058pfv918e8ipmbadj05sm1e7';

    const HOMEURL_TEST = 'https://service.berlin.de/terminvereinbarung/ticketprinter';

    public function testBasic()
    {
        $this->assertFalse(\BO\Zmsclient\Ticketprinter::getHash());
        \BO\Zmsclient\Ticketprinter::setHash(self::HASH_TEST);
        $this->assertEquals(self::HASH_TEST, \BO\Zmsclient\Ticketprinter::getHash());

        $this->assertFalse(\BO\Zmsclient\Ticketprinter::getHomeUrl());
        \BO\Zmsclient\Ticketprinter::setHomeUrl(self::HOMEURL_TEST);
        $this->assertEquals(self::HOMEURL_TEST, \BO\Zmsclient\Ticketprinter::getHomeUrl());
    }
}
