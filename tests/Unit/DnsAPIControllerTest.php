<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\DnsAPIController;
use App\DnsRecord;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;


class DnsAPIControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_it_resolves_valid_domains()
    {
        $request = Request::create('/api/domainLookup/', 'POST',[
            'domains'     =>   ['chongkan.com', 'neustar.com'],
        ]); 

        $controller = new DnsAPIController();
        $response = $controller->domainLookup($request);  

        $this->assertEquals('chongkan.com', $response->original[0][0]->host);
    }
    public function test_it_doesnt_resolve_invalid_domains()
    {
        $request = Request::create('/api/domainLookup/', 'POST',[
            'domains'     =>   ['chongkan', 'neustar.com'],
        ]); 

        $controller = new DnsAPIController();
        $response = $controller->domainLookup($request);  
        $this->assertEquals('chongkan is not a valid domain', $response->original[0]);
    }

    public function test_it_finds_no_dns_records_for_domain()
    {
        $request = Request::create('/api/domainLookup/', 'POST',[
            'domains'     =>   ['chongkan.test'],
        ]); 

        $controller = new DnsAPIController();
        $response = $controller->domainLookup($request);  
    
        $this->assertEquals('No DNS records Found.', $response->original[0][0]->invalid);
    }

    public function test_it_gets_the_info_from()
    {
        $request = Request::create('/api/domainLookup/', 'POST',[
            'domains'     =>   ['cafedaniela.com'],
        ]); 

        $controller = new DnsAPIController();
        $response = $controller->domainLookup($request);  

        $domainData = DnsRecord::where('domain', 'chongkan.com')->get()->first();
        if ($domainData) {
            $dnsRecords = json_decode($domainData->dns_records);
        }
        var_dump($dnsRecords[0]->host);
        $this->assertEquals('cafedaniela.com', $dnsRecords[0]->host);
    }

    public function test_it_finds_dns_source_in_database(){
      
        $controller = new DnsAPIController();
        $response = $controller->resolveDNS('chongkan.com');  
        
        $this->assertEquals('SQLite Database', $response);
    }

    public function test_it_gets_dns_from_os(){
      
        $controller = new DnsAPIController();
        $response = $controller->resolveDNS('myNewReallyWeirdInexistingDomain.com');  
        
        $this->assertEquals('dns_get_record()', $response);
    }

    public function test_it_fails_when_some_domain_is_invalid()
    {
        $domainsList =   ['chongkan.com', 'neustar'];
        $controller = new DnsAPIController();
        $response = $controller->validateDomains($domainsList);
        $this->assertFalse($response);
    }

    public function test_it_passes_when_all_domains_are_valid()
    {
        $domainsList =   ['chongkan.com', 'neustar.com'];
        $controller = new DnsAPIController();
        $response = $controller->validateDomains($domainsList);
        $this->assertTrue($response);
    }
    
}
