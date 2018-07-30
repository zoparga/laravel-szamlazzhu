<?php


namespace SzuniSoft\SzamlazzHu\Tests\Client\Fixtures;


use GuzzleHttp\Psr7\Response;

class ReceiptCancellationResponse extends Response {

    public function __construct(
        $pdf = '123',
        int $status = 200, array $headers = [], $body = null, string $version = '1.1', ?string $reason = null)
    {

        $body = $body ?: '<?xml version="1.0" encoding="UTF-8"?>
                    <xmlnyugtavalasz xmlns="http://www.szamlazz.hu/xmlnyugtavalasz" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.szamlazz.hu/xmlnyugtavalasz http://www.szamlazz.hu/docs/xsds/nyugta/xmlnyugtavalasz.xsd">
                        <sikeres>true</sikeres>
                        <hibakod></hibakod> 
                        <hibauzenet></hibauzenet>
                        <nyugtaPdf>' . base64_encode($pdf) . '</nyugtaPdf>
                        <nyugta>
                            <alap>
                            <id>123456</id>
                            <hivasAzonosito></hivasAzonosito>
                            <nyugtaszam>NYGT-2017-123</nyugtaszam> 
                            <tipus>NY</tipus>
                            <stornozott>false</stornozott> 
                            <stornozottNyugtaszam>NYGT-2017-100</stornozottNyugtaszam> 
                            <kelt>2015-12-01</kelt>
                            <fizmod>készpénz</fizmod>
                            <penznem>EUR</penznem>
                            <devizabank></devizabank>
                            <devizaarf>210</devizaarf>
                            <megjegyzes></megjegyzes> 
                            <fokonyvVevo></fokonyvVevo>
                            <teszt>false</teszt>
                            </alap>
                            <tetelek>
                                <tetel>
                                    <azonosito></azonosito>
                                    <megnevezes>Toy for dog</megnevezes>
                                    <mennyiseg>2.0</mennyiseg>
                                    <mennyisegiEgyseg>piece</mennyisegiEgyseg>
                                    <nettoEgysegar>-10000</nettoEgysegar>
                                    <netto>-20000.0</netto>
                                    <afakulcs>27</afakulcs> 
                                    <afa>-5400.0</afa>
                                    <brutto>-25400.0</brutto> 
                                    <fokonyv>
                                        <arbevetel></arbevetel>
                                        <afa></afa>
                                    </fokonyv>
                                </tetel>
                                <tetel>
                                    <megnevezes>More toy for dog</megnevezes>
                                    <mennyiseg>2.0</mennyiseg>
                                    <mennyisegiEgyseg>piece</mennyisegiEgyseg>
                                    <nettoEgysegar>-10000</nettoEgysegar>
                                    <nettoErtek>-20000.0</nettoErtek>
                                    <afakulcs>27</afakulcs>
                                    <afaErtek>-5400.0</afaErtek>
                                    <bruttoErtek>-25400.0</bruttoErtek>
                                </tetel>
                            </tetelek>
                            <kifizetesek>
                                <kifizetes>
                                    <fizetoeszkoz>utalvány</fizetoeszkoz>
                                    <osszeg>-1000.0</osszeg>
                                    <leiras>OTP SZÉP kártya</leiras>
                                </kifizetes>
                                <kifizetes>
                                    <fizetoeszkoz>bankkártya</fizetoeszkoz>
                                    <osszeg>-3000.0</osszeg>
                                </kifizetes>
                            </kifizetesek>
                            <osszegek>
                                <afakulcsossz> 
                                    <afatipus>ÁKK</afatipus>
                                    <afakulcs>0</afakulcs> 
                                    <netto>200</netto>
                                    <afa>54</afa>
                                    <brutto>254</brutto> 
                                </afakulcsossz>
                                <totalossz>
                                    <netto>200</netto>
                                    <afa>54</afa>
                                    <brutto>254</brutto>
                                </totalossz>
                            </osszegek>
                        </nyugta>
                    </xmlnyugtavalasz>
                    ';

        parent::__construct($status, $headers, $body, $version, $reason);
    }

}