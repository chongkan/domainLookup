<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DnsRecord;

class DnsAPIController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DNS Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for looking up the IPs DNS records, storing
    | any new IP and it records in the DB, and returning them in JSON format. 
    |
    */

    private $dnsRecords = [];
    private $errors = [];

    /**
     * Validates the Request. 
     * $domainList, inside of the Request, should be an array.
     * @return \Illuminate\Http\Response
     */
    public function domainLookup(Request $request)
    {
        // TODO, CSFR is not being validated. To restrict usage.
        // Note: Should be an Array
        $domainList = $request->get('domains');
        $response = [];
        if ($this->validateDomains($domainList)) {
            // TO not waste time resolving before validating the entire list
            foreach ($domainList as $domain) {
                $this->resolveDNS($domain);
            }
            return response()->json($this->dnsRecords); 
        }else {
            return response()->json($this->errors);    
        }
    	
    }
    /**
     * Received a valid domain address and looks for it in the DB, if found, it returns data from the DB
     * If the domain DNS records are not found, it tries to lookup the DNS records with PHP dns_get_record
     * IF records are found with dns_get_record, the are stored in the SQLite DB. 
     * 
     *  STRING $domain
     *  @return \Illuminate\Http\Response
     */
    public function resolveDNS($domain){
        $domain = trim($domain);
        $source = 'dns_get_record()';
        // Check if the we already have records in our DB. 
        $records = DnsRecord::where('domain', $domain)->get()->first();
        if (!$records) {
        // Could be from an API or a more complete resolution, the type could also be a parameter.
            $records = dns_get_record($domain, DNS_ANY);
            if ($records) {
                $this->store($records, $domain);
            }else {
                $records = [];
                $records[0] = new \stdClass();
                $records[0]->host = $domain;
                $records[0]->invalid = 'No DNS records Found.';
            } 
        }else {
            $source = 'SQLite Database';
            $records = json_decode($records->dns_records);
        }
        $this->dnsRecords[] = $records; 
        return $source;   
    } 
    /**
     * Validates the strings to allow only properly formed domains
     * @return Bool
     */
    public function validateDomains($domainList) {  
        $isValid = true;
        foreach ($domainList as $domain) {
            # validate domain
            $domain = trim($domain);
            if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/', $domain))
            {
                $this->errors[] = $domain . ' is not a valid domain';
                $isValid =  false;
            }         
        }

        return $isValid;
    }

    public function store($records, $domain){
        $dnsRecord = new DnsRecord;
        $dnsRecord->domain = $domain;
        $dnsRecord->dns_records = json_encode($records);
        $dnsRecord->save();
    }
    
}
