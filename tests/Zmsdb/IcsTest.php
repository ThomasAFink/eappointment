<?php

namespace BO\Zmsdb\Tests;

use \BO\Zmsdb\Config as Config;
use \BO\Zmsdb\Process;
use \BO\Zmsentities\Ics as Entity;

class IcsTest extends Base
{
    public function testBasic()
    {
        setlocale(LC_ALL, 'de_DE');
        $testEntity = $this->getTestEntity();
        $testTimestamp = 1463062089; // 12.5.2016, 16:08:09 GMT+2:00 DST saved in base64 ics string below
        $process = (new Process())->readEntity(169530, 'b3b0'); //process from testDB import
        $config = (new Config())->readEntity();

        $ics = \BO\Zmsentities\Helper\Messaging::getMailIcs($process, $config, $testTimestamp);

        $this->assertEntity("\\BO\\Zmsentities\\Ics", $ics);
        $this->assertEquals($testEntity->content, \base64_encode($ics->getContent()));
    }

    protected function getTestEntity()
    {
        $input = new Entity(array(
            'content' => 'QkVHSU46VkNBTEVOREFSClgtTE9UVVMtQ0hBUlNFVDpVVEYtOApWRVJTSU9OOjIuMApQUk9ESUQ6Wk1TLUJlcmxpbgpCRUdJTjpWVElNRVpPTkUKVFpJRDpFdXJvcGUvQmVybGluClgtTElDLUxPQ0FUSU9OOkV1cm9wZS9CZXJsaW4KQkVHSU46REFZTElHSFQKVFpPRkZTRVRGUk9NOiswMTAwClRaT0ZGU0VUVE86KzAyMDAKVFpOQU1FOkNFU1QKRFRTVEFSVDoxOTcwMDMyOVQwMjAwMDAKUlJVTEU6RlJFUT1ZRUFSTFk7SU5URVJWQUw9MTtCWURBWT0tMVNVO0JZTU9OVEg9MwpFTkQ6REFZTElHSFQKQkVHSU46U1RBTkRBUkQKVFpPRkZTRVRGUk9NOiswMjAwClRaT0ZGU0VUVE86KzAxMDAKVFpOQU1FOkNFVApEVFNUQVJUOjE5NzAxMDI1VDAzMDAwMApSUlVMRTpGUkVRPVlFQVJMWTtJTlRFUlZBTD0xO0JZREFZPS0xU1U7QllNT05USD0xMApFTkQ6U1RBTkRBUkQKRU5EOlZUSU1FWk9ORQpNRVRIT0Q6UFVCTElTSApCRUdJTjpWRVZFTlQKQ0xBU1M6UFVCTElDCkRUU1RBUlQ7VFpJRD1FdXJvcGUvQmVybGluOjIwMTYwNDA4VDA5MTAwMApEVEVORDtUWklEPUV1cm9wZS9CZXJsaW46MjAxNjA0MDhUMDkyMDAwCkRUU1RBTVA6MjAxNjA1MTJUMTYwODA5WgpMT0NBVElPTjpCw7xyZ2VyYW10IFJhdGhhdXMgVGllcmdhcnRlbiBNYXRoaWxkZS1KYWNvYi1QbGF0eiwgMTA1NTEgQmVybGluClNVTU1BUlk6QmVybGluLVRlcm1pbjogMTY5NTMwCkRFU0NSSVBUSU9OOiBTZWhyIGdlZWhydGUvciBGcmF1IG9kZXIgSGVyciBaNjU2MzUgXG5cbiBoaWVybWl0IGJlc3TDpHRpZ2VuIHdpciBJaG5lbiBJaHJlbiBnZWJ1Y2h0ZW4gVGVybWluIGFtIEZyLiAwOC4gQXByaWwgMjAxNiB1bSAwOToxMCBVaHJcbiBPcnQ6IELDvHJnZXJhbXQgUmF0aGF1cyBUaWVyZ2FydGVuIE1hdGhpbGRlLUphY29iLVBsYXR6LCAxMDU1MSBCZXJsaW5cbiAoQsO8cmdlcmFtdCApIFxuXG4gSWhyZSBWb3JnYW5nc251bW1lciBpc3QgZGllICIxNjk1MzAiXG4gSWhyIENvZGUgenVyIFRlcm1pbmFic2FnZSBvZGVyIC3DpG5kZXJ1bmcgbGF1dGV0ICJiM2IwIlxuXG4gWmFobHVuZ3NoaW53ZWlzOiBBbSBTdGFuZG9ydCBrYW5uIG51ciBtaXQgZ2lyb2NhcmQgKG1pdCBQSU4pIGJlemFobHQgd2VyZGVuLlxuXG4gU2llIGhhYmVuIGZvbGdlbmRlIERpZW5zdGxlaXN0dW5nIGF1c2dld8OkaGx0OiBcbiBcbkFubWVsZHVuZyBlaW5lciBXb2hudW5nXG4gIFxuVm9yYXVzc2V0enVuZ2VuXG4gIFxuLSAgcGVyc8O2bmxpY2hlIFZvcnNwcmFjaGUgb2RlciBWZXJ0cmV0dW5nIGR1cmNoIGVpbmUgYW5kZXJlIFBlcnNvbiAgIElocmUgcGVyc8O2bmxpY2hlIFZvcnNwcmFjaGUgaXN0IGVyZm9yZGVybGljaCBvZGVyIHNpZSB3ZXJkZW4gZHVyY2ggZWluZSBhbmRlcmUgUGVyc29uIHZlcnRyZXRlbi5cbiBCZWkgZGVyIEFiZ2FiZSBkZXMgQW5tZWxkZWZvcm11bGFycyB1bmQgZGVyIMO8YnJpZ2VuIGVyZm9yZGVybGljaGVuIFVudGVybGFnZW4ga8O2bm5lbiBTaWUgc2ljaCBkdXJjaCBlaW5lIGdlZWlnbmV0ZSBQZXJzb24gdmVydHJldGVuIGxhc3Nlbi4gRGllIHZvbiBJaG5lbiBiZWF1ZnRyYWd0ZSBQZXJzb24gbXVzcyBpbiBkZXIgTGFnZSBzZWluLCBkaWUgenVyIG9yZG51bmdzZ2Vtw6TDn2VuIEbDvGhydW5nIGRlcyBNZWxkZXJlZ2lzdGVycyBlcmZvcmRlcmxpY2hlbiBBdXNrw7xuZnRlIHp1IGVydGVpbGVuLiBEYXMgQW5tZWxkZWZvcm11bGFyIG3DvHNzZW4gU2llIGVpZ2VuaMOkbmRpZyB1bnRlcnNjaHJlaWJlbi4gICAgICBcbkdlYsO8aHJlblxuIGdlYsO8aHJlbmZyZWk7IGRhcyBnaWx0IGF1Y2ggZsO8ciBkaWUgTWVsZGViZXN0w6R0aWd1bmcuIFxuIFNvbGx0ZW4gU2llIGRlbiBUZXJtaW4gbmljaHQgd2Focm5laG1lbiBrw7ZubmVuLCBzYWdlbiBTaWUgaWhuIGJpdHRlIGFiLiBcblxuIERpZXMga8O2bm5lbiBTaWUgw7xiZXIgdW5zZXJlIEludGVybmV0YnVjaHVuZ3NzZWl0ZSBodHRwczovL3NlcnZpY2UtYmVybGluL3Rlcm1pbnZlcmVpbmJhcnVuZy90ZXJtaW4vbWFuYWdlLzE2OTUzMC8gdW50ZXIgQW5nYWJlIElocmVyIFZvcmdhbmdzbnVtbWVyICIxNjk1MzAiIHVuZCBJaHJlcyBwZXJzw7ZubGljaGVuIEFic2FnZS1Db2RlcyAiYjNiMCIgZXJsZWRpZ2VuLlxuXG4gXG4gTWl0IGZyZXVuZGxpY2hlbSBHcnXDn1xuIElocmUgVGVybWludmVyd2FsdHVuZyBkZXMgTGFuZGVzIEJlcmxpbiBcblxuIGh0dHBzOi8vc2VydmljZS1iZXJsaW4vdGVybWludmVyZWluYmFydW5nLyAKQkVHSU46VkFMQVJNCkFDVElPTjpESVNQTEFZClRSSUdHRVI6LVAxRApERVNDUklQVElPTjpFcmlubmVydW5nCkVORDpWQUxBUk0KRU5EOlZFVkVOVApFTkQ6VkNBTEVOREFSCg=='));
        return $input;
    }
}
