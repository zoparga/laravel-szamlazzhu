<?php


namespace SzuniSoft\SzamlazzHu\Tests\Client\Fixtures;


use GuzzleHttp\Psr7\Response;

class InvoiceRetrievalResponse extends Response {


    public function __construct(int $status = 200, array $headers = [], $body = null, string $version = '1.1', ?string $reason = null)
    {

        $body = $body ?: '<?xml version="1.0" encoding="UTF-8"?>
            <szamla xmlns="http://www.szamlazz.hu/szamla" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.szamlazz.hu/szamla szamla.xsd ">
                <szallito>
                    <id>201</id>
                    <nev>Merchant</nev>
                    <cim>
                        <orszag>Magyarorsz&#225;g</orszag>
                        <irsz>1086</irsz>
                        <telepules>Budapest</telepules>
                        <cim>Some street</cim>
                    </cim>
                    <adoszam>123</adoszam>
                    <adoszameu>123</adoszameu>
                    <bank>
                        <nev>MNB</nev>
                        <bankszamla>123</bankszamla>
                    </bank>
                </szallito>
                <alap>
                    <id>529992</id>
                    <szamlaszam>E-LOLO-66</szamlaszam>
                    <rendelesszam>123</rendelesszam>
                    <tipus>D</tipus>
                    <eszamla>0</eszamla>
                    <kelt>2017-10-09</kelt>
                    <telj>2017-10-09</telj>
                    <fizh>2017-10-09</fizh>
                    <fizmod>credit_card</fizmod>
                    <fizmodunified>egy&#233;b</fizmodunified>
                    <nyelv>hu</nyelv>
                    <devizanem>HUF</devizanem>
                    <devizaarf>0</devizaarf>
                    <megjegyzes></megjegyzes>
                    <penzforg>false</penzforg>
                    <kata>true</kata>
                    <teszt>false</teszt>
                </alap>
                <vevo>
                    <id>221216</id>
                    <nev>Customer</nev>
                    <cim>
                        <orszag>Magyarorsz&#225;g</orszag>
                        <irsz>1324</irsz>
                        <telepules>Somewhere</telepules>
                        <cim>1234</cim>
                    </cim>
                    <adoszam></adoszam>
                    <fokonyv>
                        <vevo></vevo>
                        <vevoazon></vevoazon>
                    </fokonyv>
                </vevo>
                <tetelek>
                    <tetel>
                        <nev>Apple</nev>
                        <mennyiseg>1</mennyiseg>
                        <mennyisegiegyseg>kg</mennyisegiegyseg>
                        <nettoegysegar>380</nettoegysegar>
                        <afakulcs>20</afakulcs>
                        <netto>380</netto>
                        <arresafaalap>0</arresafaalap>
                        <afa>76</afa>
                        <brutto>456</brutto>
                        <megjegyzes>Healthy food</megjegyzes>
                        <fokonyv>
                        <arbevetel></arbevetel>
                        <afa></afa>
                        <gazdasagiesemeny></gazdasagiesemeny>
                        <gazdasagiesemenyafa></gazdasagiesemenyafa>
                        </fokonyv>
                    </tetel>
                </tetelek>
                <osszegek>
                    <afakulcsossz>
                    <afakulcs>20</afakulcs>
                    <netto>464</netto>
                    <afa>93</afa>
                    <brutto>557</brutto>
                    </afakulcsossz>
                    <totalossz>
                        <netto>464</netto>
                        <afa>93</afa>
                        <brutto>557</brutto>
                    </totalossz>
                </osszegek>
                <pdf>BASE64 formátumban található itt a számla PDF</pdf>
            </szamla>
        ';

        parent::__construct($status, $headers, $body, $version, $reason);
    }


}